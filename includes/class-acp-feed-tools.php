<?php
defined('ABSPATH') or die('You do not have permissions to this file!');

/**
 * Tools
 * 
 * @author tomeckiStudio
 * @version 1.0.0
 */
final class ACP_Feed_Tools {
    const ACTION_RUN_NOW         = 'acp_feed_run_now';
    const ACTION_RESET_STATE     = 'acp_feed_reset_state';
    const ACTION_DOWNLOAD_LOGS   = 'acp_feed_download_logs';

   /**
    * Initialize hooks for Tools
    * 
    * @since 1.0.0
    */ 
    public static function init(){
        add_action('admin_post_' . self::ACTION_RUN_NOW, [__CLASS__, 'acp_handle_run_now']);
        add_action('admin_post_' . self::ACTION_RESET_STATE, [__CLASS__, 'acp_handle_reset_state']);
        add_action('admin_post_' . self::ACTION_DOWNLOAD_LOGS, [__CLASS__, 'acp_handle_download_logs']);
    }

    /**
     * Try to run build
     *
     * @since 1.0.0
     */
    public static function acp_handle_run_now(){
        if(!current_user_can('manage_woocommerce') || !check_admin_referer('nonce_' . self::ACTION_RUN_NOW))
            wp_die(__('Forbidden', 'acp-feed-woocommerce'));

        ACP_Feed_Scheduler::acp_run_build();
        wp_safe_redirect(admin_url('admin.php?page=acp-feed-settings'));

        exit;
    }

    /**
     * Reset build state
     *
     * @since 1.0.0
     */
    public static function acp_handle_reset_state(){
        if(!current_user_can('manage_woocommerce') || !check_admin_referer('nonce_' . self::ACTION_RESET_STATE))
            wp_die(__('Forbidden', 'acp-feed-woocommerce'));

        delete_option(ACP_Feed::STATE_OPTION);
        ACP_Feed_Scheduler::acp_clear();
        acp_log('Scheduler -> acp_handle_reset_state() -> Reset build state');
        wp_safe_redirect(admin_url('admin.php?page=acp-feed-settings'));

        exit;
    }

    /**
     * Download log file
     *
     * @since 1.0.0
     */
    public static function acp_handle_download_logs(){
        if(!current_user_can('manage_woocommerce') || !check_admin_referer('nonce_' . self::ACTION_DOWNLOAD_LOGS))
            wp_die(__('Forbidden', 'acp-feed-woocommerce'));

        $settings      = get_option(ACP_Feed_Settings::ACP_OPTIONS_KEY, []);
        $log_method    = isset($settings['log_method']) ? $settings['log_method'] : 'custom';
        $log_method    = in_array($log_method, ['woocommerce', 'custom'], true) ? $log_method : 'custom';
        $download_path = '';

        if($log_method === 'woocommerce' && function_exists('wc_get_logger') && class_exists('WC_Log_Handler_File')){
            $handle        = 'acp-feed-woocommerce';
            $download_path = WC_Log_Handler_File::get_log_file_path($handle);

            if(!file_exists($download_path)){
                $uploads = wp_get_upload_dir();
                $log_dir = trailingslashit($uploads['basedir']) . 'wc-logs/';

                if(is_dir($log_dir)){
                    $candidates = glob($log_dir . $handle . '-*.log');

                    if(!empty($candidates)){
                        usort($candidates, static function($a, $b){
                            return filemtime($b) <=> filemtime($a);
                        });
                        $download_path = $candidates[0];
                    }
                }
            }
        }elseif($log_method === 'custom'){
            $download_path = trailingslashit(ABSPATH) . 'acp_feed.log';
        }

        if(empty($download_path) || !file_exists($download_path) || !is_readable($download_path)){
            wp_die(__('Log file not found or inaccessible for download.', 'acp-feed-woocommerce'));
        }

        $file_size = filesize($download_path);
        $file_name = basename($download_path);

        if($file_size === false){
            wp_die(__('Unable to read log file size.', 'acp-feed-woocommerce'));
        }

        nocache_headers();
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . $file_size);
        readfile($download_path);
        exit;
    }
}