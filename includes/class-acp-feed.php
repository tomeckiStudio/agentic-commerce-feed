<?php
defined('ABSPATH') or die('Suspicious activities detected!');

/**
 * Manage Feed Generation
 * 
 * @author tomeckiStudio
 * @version 1.0.0
 */
final class ACP_Feed{
    const FEED_FILE                 = 'acp-product-feed.xml';
    const STATE_OPTION              = 'acp_feed_state';
    const LAST_SUCCESSFUL_OPTION    = 'acp_feed_last_successful_build';
    const LOCK_PREFIX               = 'acp_feed_lock_';

    /**
     * Start a new feed build process
     *
     * @return bool True on success, false on failure
     * @since 1.0.0
     */
    public static function initialize_build(){
		try{
			acp_log("initialize_build() -> Initiating feed generation...");

            $state = get_option(self::STATE_OPTION);

            if(!empty($state)){
                if(is_array($state)){
                    if($state['finished_at'] === null){
                        acp_log('initialize_build() -> Build already in progress; cannot initialize new build.');
                        return false;
                    }
                }
            }

			$upload_dir = wp_get_upload_dir();
			
			if(empty($upload_dir['basedir'])){
				acp_log("initialize_build() -> Could not determine WordPress uploads base directory.");
				return false;
			}
			
			$acp_feed_xml = trailingslashit($upload_dir['basedir']) . self::FEED_FILE;
			$acp_feed_tmp  = $acp_feed_xml . '.tmp';

			$build_id = uniqid('build_', true);
			@unlink($acp_feed_tmp);
			acp_log("initialize_build() -> Starting new build: {$build_id}; cleared {$acp_feed_tmp}");

			$acp_feed_file_writer = fopen($acp_feed_tmp, 'ab');
			
			if(!$acp_feed_file_writer){ 
				acp_log("initialize_build() -> Cannot open {$acp_feed_tmp} for header");
				return false; 
			}

			fwrite($acp_feed_file_writer, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>");

            /**
             * Action hook before starting products element in the feed
             * Use case: add custom elements to the feed right after XML declaration
             *
             * @param resource $acp_feed_file_writer - File writer resource for the feed file
             * @since 1.0.0
             */
            do_action('acp_feed_before_products_start', $acp_feed_file_writer);

			fwrite($acp_feed_file_writer, "<products>\n");

            /**
             * Action hook after starting products element in the feed
             * Use case: add custom elements to the feed after opening products tag
             *
             * @param resource $acp_feed_file_writer - File writer resource for the feed file
             * @since 1.0.0
             */
            do_action('acp_feed_after_products_start', $acp_feed_file_writer);

			fclose($acp_feed_file_writer);
			
			acp_log("initialize_build() -> Wrote XML header to {$acp_feed_tmp}");

            /**
             * Filter to modify the batch size for feed generation
             * Use case: adjust the number of products processed per batch based on server capabilities
             * 
             * @param int $acp_batch_size - The number of products per batch (default is 300)
             * @since 1.0.0
             */
            $acp_batch_size = intval(apply_filters('acp_batch_size', 300));

            if($acp_batch_size <= 0){
                acp_log("initialize_build() -> Invalid batch size configured: {$acp_batch_size}, resetting to 300");
                $acp_batch_size = 300;
            }

			$state = [
				'build_id'      => $build_id,
				'page'          => 1,
				'page_size'     => $acp_batch_size,
				'statuses'      => ['publish'],
				'created_at'    => time(),
                'finished_at'   => null,
                'debug_msg'     => "",
			];
			update_option(self::STATE_OPTION, $state, false);
			acp_log("initialize_build() -> Saved build state: " . json_encode($state));

			$how_scheduled = ACP_Feed_Scheduler::schedule_batch($build_id, 1, 1);
			acp_log("initialize_build() -> Scheduled page 1 via {$how_scheduled}");

            return true;
		}catch(Throwable $e){
			acp_log("initialize_build() -> Exception: {$e->getMessage()}");
		}
		
        return false;
    }

    /**
     * Process a single batch (page) of products
     *
     * @param string $build_id - The build identifier
     * @param int $page - The page number
     * @since 1.0.0
     */
    public static function process_batch($build_id, $page){
        try{
            acp_log("process_batch() -> ENTER process_batch build={$build_id} page={$page}");
    
            $state = get_option(self::STATE_OPTION);

            if(!is_array($state)){
                acp_log("process_batch() -> No state to process or state is incorrect.");
                return;
            }

            if($state['build_id'] !== $build_id){
                $state_build_id = $state['build_id'];
                acp_log("process_batch() -> State mismatch. build_id of state: {$state_build_id}, passed build_id: {$build_id}.");
                return;
            }

            if($state['finished_at'] !== null){
                acp_log('process_batch() -> Already finished.');
                return;
            }

            if(empty($state['page_size']) || !is_int($state['page_size']) || $state['page_size'] <= 0){
                acp_log('process_batch() -> Invalid page_size in state.');
                return;
            }

            $page_size = (int) $state['page_size'];

            if(empty($state['statuses']) || !is_array($state['statuses'])){
                acp_log("process_batch() -> Invalid statuses in state.");
                return;
            }

            $statuses = (array) $state['statuses'];
    
            if(!class_exists('WooCommerce')){
                acp_log('process_batch() -> WooCommerce not loaded; rescheduling...');
                ACP_Feed_Scheduler::schedule_batch($build_id, (int)$page, 10);
                return;
            }
    
            if(!self::acquire_lock($build_id)){
                acp_log('process_batch() -> Lock busy; rescheduling...'); 
                ACP_Feed_Scheduler::schedule_batch($build_id, (int)$page, 10);
                return; 
            }
    
            $upload_dir = wp_get_upload_dir();

            if(empty($upload_dir['basedir'])){
                acp_log('process_batch() -> Could not determine WordPress uploads base directory.');
                self::release_lock($build_id);
                return;
            }

            $acp_feed_xml = trailingslashit($upload_dir['basedir']) . self::FEED_FILE;
            $acp_feed_tmp  = $acp_feed_xml . '.tmp';
    
            $fetch_products_start_time = microtime(true);
            $ids = wc_get_products([
                'status'  => $statuses,
                'limit'   => $page_size,
                'page'    => (int) $page,
                'return'  => 'ids',
                'orderby' => 'ID',
                'order'   => 'ASC',
                'type'    => ['simple'],
            ]);

            /**
             * Action hook allowing modification of fetched product IDs for the batch
             * Use case: filter or modify the list of product IDs based on custom logic, e.g., exclude certain categories
             * Important: Ensure that the returned value is an array of product IDs
             * Important: If returning an empty array, the batch will be treated as finished
             *
             * @param array $ids - Array of product IDs fetched for the current batch
             * @param int $page - The current page number being processed
             * @since 1.0.0
             */
            do_action('acp_fetch_products', $ids, $page);
            
            if(!is_array($ids)){
                acp_log('process_batch() -> wc_get_products returned non-array; coercing to empty array.');
                $ids = [];
            }        
            
            acp_log(sprintf('process_batch() -> Fetched IDs: count=%d in %.3fs', count($ids), microtime(true) - $fetch_products_start_time));
    
            if (empty($ids)){
                acp_log('process_batch() -> No more IDs; finalizing...');

                $acp_feed_file_writer = fopen($acp_feed_tmp, 'ab');

                if(!$acp_feed_file_writer){ 
                    acp_log("process_batch() -> Cannot open {$acp_feed_tmp} to close root.");
                    self::release_lock($build_id);
                    return;
                }

                /**
                 * Action hook before ending products element in the feed
                 * Use case: add custom elements to the feed before closing products tag
                 *
                 * @param resource $acp_feed_file_writer - File writer resource for the feed file
                 * @since 1.0.0
                 */
                do_action('acp_feed_before_products_end', $acp_feed_file_writer);

                fwrite($acp_feed_file_writer, "</products>");
                
                /**
                 * Action hook after ending products element in the feed
                 * Use case: add custom elements to the feed right after closing products tag
                 *
                 * @param resource $acp_feed_file_writer - File writer resource for the feed file
                 * @since 1.0.0
                 */
                do_action('acp_feed_after_products_end', $acp_feed_file_writer);

                fclose($acp_feed_file_writer);

                @chmod($acp_feed_tmp, 0644);
                $feed_file_rename_result = @rename($acp_feed_tmp, $acp_feed_xml);

                if(!$feed_file_rename_result){
                    acp_log("process_batch() -> Rename failed from {$acp_feed_tmp} to {$acp_feed_xml}");
                    self::release_lock($build_id);
                    return;
                }

                acp_log("process_batch() -> Renamed feed file to {$acp_feed_xml}");

                $state['finished_at'] = time();
                update_option(self::STATE_OPTION, "", false);
                update_option(self::LAST_SUCCESSFUL_OPTION, $state, false);
                acp_log("process_batch() -> Feed file build ({$build_id}) finished.");
                self::release_lock($build_id);

                ACP_Feed_DBHelper::acp_clear_reports_except_build($build_id);

                return;
            }

            $acp_feed_file_writer = fopen($acp_feed_tmp, 'ab');

            if(!$acp_feed_file_writer){
                acp_log("process_batch() -> Cannot open {$acp_feed_tmp} for append.");
                self::release_lock($build_id);
                return;
            }
    
            $products_written = 0; 
            $products_write_start = microtime(true);

            foreach($ids as $product_id){
                $product = wc_get_product($product_id);

                if(!$product){
                    acp_log("process_batch() -> wc_get_product failed for ID={$product_id}");
                    continue;
                }
    
                $xml_writer = new XMLWriter();
                $xml_writer->openMemory();
                $xml_writer->setIndent(true);

                acp_log("process_batch() -> emit product, ID={$product_id}");

                if(ACP_Feed_Builder::emit_product($xml_writer, $build_id, $product)){
                    ACP_Feed_DBHelper::acp_report($build_id, $product_id, 'emitted');
                    acp_log("process_batch() -> product emitted , ID={$product_id}");
                }else{
                    acp_log("process_batch() -> product skipped , ID={$product_id}");
                }

                $xml_writer_buffer = $xml_writer->flush(true);

                if($xml_writer_buffer !== ''){
                    fwrite($acp_feed_file_writer, $xml_writer_buffer);
                    $products_written++;
                }
    
                unset($product);
            }

            fclose($acp_feed_file_writer);

            acp_log(sprintf('process_batch() -> Appended page=%d: products_written=%d in %.3fs', $page, $products_written, microtime(true) - $products_write_start));
    
            $next_page = (int)$page + 1;
            $state['page'] = $next_page;
            update_option(self::STATE_OPTION, $state, false);
            acp_log("process_batch() -> Updated state to page={$next_page}");
    
            self::release_lock($build_id);
    
            $schedule_method = ACP_Feed_Scheduler::schedule_batch($build_id, $next_page, 1);
            acp_log("process_batch() -> Scheduled next page {$next_page} via {$schedule_method}");
        }catch(Throwable $e){
            acp_log('process_batch() -> EXCEPTION: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

            if (is_string($build_id ?? null)) {
                self::release_lock($build_id);
            }
        }
    }    

    /**
     * Acquire a lock for the build
     * 
     * @param string $build_id - The build identifier
     * @return bool True if lock acquired, false if busy
     * @since 1.0.0
     */
    public static function acquire_lock($build_id){
        $key = self::LOCK_PREFIX . $build_id;

        if(get_transient($key))
            return false;

        set_transient($key, 1, 5 * 60);
        return true;
    }

    /**
     * Release the lock for the build
     * 
     * @param string $build_id - The build identifier
     * @since 1.0.0
     */
    public static function release_lock($build_id){
        delete_transient(self::LOCK_PREFIX . $build_id);
    }
}
