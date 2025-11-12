<?php
defined('ABSPATH') or die('You do not have permissions to this file!');

/**
 * Feed Scheduler
 * 
 * @author tomeckiStudio
 * @version 1.0.0
 */
final class ACP_Feed_Scheduler {
    const ACTION_BUILD_CRON         = 'acp_feed_build_cron';
    const SCHEDULE_HOOK_AS          = 'acp_feed_build_as';
    const SCHEDULE_HOOK_WP_EVENT    = 'acp_feed_build_event';
    const HOOK_PROCESS_BATCH        = 'acp_feed_process_batch';
    const SCHEDULE_GROUP            = 'acp-feed';

    /**
     * Initialize hooks for Scheduler
     * 
     * @since 1.0.0
     */
    public static function init(){
        // Initial scheduling
        add_action(self::SCHEDULE_HOOK_WP_EVENT, [__CLASS__, 'acp_run_build']);
        add_action(self::SCHEDULE_HOOK_AS, [__CLASS__, 'acp_run_build']);

        // For server cron
        add_action('admin_post_nopriv_' . self::ACTION_BUILD_CRON, [__CLASS__, 'acp_handle_server_cron']);
        add_action('admin_post_' . self::ACTION_BUILD_CRON, [__CLASS__, 'acp_handle_server_cron']);
    }

    /**
     * Schedule batch processing
     * 
     * @param string $build_id - Build identifier
     * @param int $page - Page number
     * @param int $delay_seconds - Delay in seconds
     * @return string - Scheduling method used
     * @since 1.0.0
     */
    public static function schedule_batch($build_id, $page, $delay_seconds = 0){
        $args = ['build_id' => $build_id, 'page' => $page];

        if(function_exists('as_enqueue_async_action') && $delay_seconds === 0){
            as_enqueue_async_action(self::HOOK_PROCESS_BATCH, $args, self::SCHEDULE_GROUP);
            return 'as_enqueue_async_action';
        }

        if(function_exists('as_schedule_single_action')){
            as_schedule_single_action(time() + max(0, $delay_seconds), self::HOOK_PROCESS_BATCH, $args, self::SCHEDULE_GROUP);
            return 'as_schedule_single_action';
        }

        if(function_exists('wp_schedule_single_event')){
            wp_schedule_single_event(time() + max(0, $delay_seconds), self::HOOK_PROCESS_BATCH, [$build_id, $page]);
            return 'wp_schedule_single_event';
        }

        do_action(self::HOOK_PROCESS_BATCH, $build_id, $page);
        return 'do_action_sync';
    }

    /**
     * Clear scheduled events
     * 
     * @since 1.0.0
     */
    public static function acp_clear(){
        // WP-Cron
        $timestamp = wp_next_scheduled(self::SCHEDULE_HOOK_WP_EVENT);
        while($timestamp){
            wp_unschedule_event($timestamp, self::SCHEDULE_HOOK_WP_EVENT);
            $timestamp = wp_next_scheduled(self::SCHEDULE_HOOK_WP_EVENT);
        }

        // WooCommerce Action Scheduler
        if(function_exists('as_unschedule_all_actions')){
            as_unschedule_all_actions(self::SCHEDULE_HOOK_AS);
        }
    }

    /**
     * Reschedule the feed builds based on current settings
     * 
     * @since 1.0.0
     */
    public static function acp_reschedule(){
        self::acp_clear();

        global $acp_class_settings;

        $cron_method = $acp_class_settings->acp_get_setting('cron_method', 'server');
        $cron_minutes = max(1, (int)$acp_class_settings->acp_get_setting('cron_interval_minutes', 30));

        acp_log("Scheduler -> acp_reschedule() -> Rescheduling with method: {$cron_method}, interval: {$cron_minutes} minutes");

        if($cron_method === 'server'){
            acp_log('Scheduler -> acp_reschedule() -> Cron driver set to server; no internal scheduling');
            return;
        }

        if($cron_method === 'woo' && function_exists('as_schedule_recurring_action')){
            as_schedule_recurring_action(time() + 60, $cron_minutes * 60, self::SCHEDULE_HOOK_AS, [], 'acp-feed');
            acp_log("Scheduler -> acp_reschedule() -> Scheduled build via Woo Action Scheduler every {$cron_minutes} minutes");
            return;
        }

        // Fallback to WP-Cron

        if(!wp_next_scheduled(self::SCHEDULE_HOOK_WP_EVENT)){
            acp_log('Scheduler -> acp_reschedule() -> No existing WP-Cron event found; scheduling new event');
            if(wp_schedule_event(time() + 60, 'acp_feed_cron', self::SCHEDULE_HOOK_WP_EVENT))
                acp_log("Scheduler -> acp_reschedule() -> Scheduled build via WP-Cron every {$cron_minutes} minutes");
        }
    }

    /**
     * Run feed build
     * 
     * @since 1.0.0
     */
    public static function acp_run_build(){
        $state = get_option(ACP_Feed::STATE_OPTION);

        if(is_array($state) && !$state['finished']){
            acp_log('Scheduler -> acp_run_build() -> Build already in progress; skipping trigger');
            return;
        }

        $ok = ACP_Feed::initialize_build();
        acp_log('Scheduler -> acp_run_build() -> Triggered initialize_build(): ' . ($ok ? 'OK' : 'FAILED'));
    }

    /**
     * Handle server cron request
     * 
     * @since 1.0.0
     */
    public static function acp_handle_server_cron(){
        global $acp_class_settings;

        $key = isset($_GET['key']) ? sanitize_text_field(wp_unslash($_GET['key'])) : '';

        $server_cron_key = $acp_class_settings->acp_get_setting('server_cron_key');

        if(empty($server_cron_key) || ($key !== $server_cron_key)){
            status_header(403);
            echo 'Forbidden';
            exit;
        }

        self::acp_run_build();
        status_header(200);
        echo 'OK';

        exit;
    }

}