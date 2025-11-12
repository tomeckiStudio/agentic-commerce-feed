<?php
defined('ABSPATH') or die('You do not have permissions to this file!');

/**
 * Database Helper
 * 
 * @author tomeckiStudio
 * @version 1.0.0
 */
final class ACP_Feed_DBHelper{
    const ACP_DB_TABLE_REPORT = 'acp_feed_report';

    /**
     * Create necessary database table
     * 
     * @since 1.0.0
     */
    static public function acp_create_tables(){
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_name = $wpdb->prefix . self::ACP_DB_TABLE_REPORT;

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `build_id` VARCHAR(100) NOT NULL,
            `product_id` BIGINT(20) UNSIGNED NULL,
            `status` VARCHAR(20) NOT NULL,
            `info` TEXT NOT NULL,
            PRIMARY KEY (`id`)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drop database table
     * 
     * @since 1.0.0
     */
    static public function acp_drop_tables(){
        global $wpdb;

        $table_name = $wpdb->prefix . self::ACP_DB_TABLE_REPORT;

        $sql = "DROP TABLE IF EXISTS {$table_name};";

        return $wpdb->query($sql);
    }

    /**
     * Report product processing result
     * 
     * @since 1.0.0
     */
    static public function acp_report($build_id, $product_id, $status, $info = ""){
        global $wpdb;

        $table_name = $wpdb->prefix . self::ACP_DB_TABLE_REPORT;

        $sql = "INSERT INTO {$table_name} (`build_id`, `product_id`, `status`, `info`) VALUES (%s, %d, %s, %s)";
        $sql = $wpdb->prepare($sql, $build_id, $product_id, $status, $info);

        return $wpdb->query($sql);
    }

    /**
     * Get reports for given build
     * 
     * @since 1.0.0
     */
    static public function acp_get_reports($build_id){
        global $wpdb;

        $table_name = $wpdb->prefix . self::ACP_DB_TABLE_REPORT;

        $sql = "SELECT * FROM {$table_name} WHERE `build_id` = %s ORDER BY `product_id` DESC";
        $sql = $wpdb->prepare($sql, $build_id);

        return $wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * Clear reports except given build
     * 
     * @since 1.0.0
     */
    static public function acp_clear_reports_except_build($build_id){
        global $wpdb;

        $table_name = $wpdb->prefix . self::ACP_DB_TABLE_REPORT;

        $sql = "DELETE FROM {$table_name} WHERE `build_id` != %s";
        $sql = $wpdb->prepare($sql, $build_id);

        return $wpdb->query($sql);
    }

    /**
     * Clear all reports
     * 
     * @since 1.0.0
     */
    static public function acp_clear_reports(){
        global $wpdb;

        $table_name = $wpdb->prefix . self::ACP_DB_TABLE_REPORT;

        $sql = "TRUNCATE TABLE {$table_name}";

        return $wpdb->query($sql);
    }

}