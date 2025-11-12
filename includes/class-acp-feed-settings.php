<?php
defined('ABSPATH') or die('Suspicious activities detected!');

/**
 * Feed Settings
 * 
 * @author tomeckiStudio
 * @version 1.0.0
 */
class ACP_Feed_Settings {
    const ACP_OPTIONS_KEY = 'acp_feed_settings';

    public $acp_settings = [];

    function __construct(){
        $this->acp_settings = wp_parse_args(get_option(self::ACP_OPTIONS_KEY, []), $this->acp_default_settings());
    }

    /**
     * Default settings
     * 
     * @since 1.0.0
     */
    private function acp_default_settings(){
        return [
            // key                                          => value, // options
            // if option is attr or meta, then custom_key is used
            // Logger
            'log_enable'                                    => '',
            'log_method'                                    => 'custom', // nolog|custom|woocommerce
            // Cron
            'cron_method'                                   => 'server', // woo|wp|server
            'cron_interval_minutes'                         => 30,
            'server_cron_key'                               => '',
            // Other
            'batch_size'                                    => 300,
            // Product settings
            'product_types'                                 => ['simple'],
            'product_too_long_value'                        => 'truncate', // truncate|omit
            // Product Mapping
            'product_id'                                    => 'sku', // id|sku|both|attr|meta
            'custom_key_product_id'                         => 'acp_id',
            'product_gtin'                                  => 'woo', // woo|attr|meta
            'custom_key_product_gtin'                       => 'acp_gtin',
            'product_mpn'                                   => 'attr', // attr|meta
            'custom_key_product_mpn'                        => 'acp_mpn',
            'product_title'                                 => 'woo', // woo|attr|meta
            'custom_key_product_title'                      => 'acp_title',
            'product_description'                           => 'long', // long|short|attr|meta
            'custom_key_product_description'                => 'acp_description',
            'product_link'                                  => '%product_link%',
            'product_condition'                             => 'attr', // new|used|refurbished
            'custom_key_product_condition'                  => 'acp_condition',
            'product_category'                              => 'woo', // woo|attr|meta
            'custom_key_product_category'                   => 'acp_category',
            'product_brand'                                 => 'woo', // woo|attr|meta
            'custom_key_product_brand'                      => 'acp_brand',
            'product_material'                              => 'attr', // attr|meta
            'custom_key_product_material'                   => 'acp_material',
            'product_dimensions'                            => 'woo', // woo|attr|meta
            'custom_key_product_dimensions'                 => 'acp_dimensions',
            'product_length'                                => 'woo', // woo|attr|meta
            'custom_key_product_length'                     => 'acp_length',
            'product_width'                                 => 'woo', // woo|attr|meta
            'custom_key_product_width'                      => 'acp_width',
            'product_height'                                => 'woo', // woo|attr|meta
            'custom_key_product_height'                     => 'acp_height',
            'product_weight'                                => 'woo', // woo|attr|meta
            'custom_key_product_weight'                     => 'acp_weight',
            'product_age_group'                             => 'attr', // attr|meta
            'custom_key_product_age_group'                  => 'acp_age_group',
            'product_image_link'                            => 'woo', // woo|attr|meta
            'custom_key_product_image_link'                 => 'acp_image_link',
            'product_additional_image_link'                 => 'woo', // woo|attr|meta
            'custom_key_product_additional_image_link'      => 'acp_additional_image_link',
            'product_video_link'                            => 'attr', // attr|meta
            'custom_key_product_video_link'                 => 'acp_video_link',
            'product_model_3d_link'                         => 'attr', // attr|meta
            'custom_key_product_model_3d_link'              => 'acp_model_3d_link',
            'product_price'                                 => 'woo', // woo|attr|meta
            'custom_key_product_price'                      => 'acp_price',
            'product_applicable_taxes_fees'                 => 'attr', // attr|meta
            'custom_key_product_applicable_taxes_fees'      => 'acp_applicable_taxes_fees',
            'product_sale_price'                            => 'woo', // woo|attr|meta
            'custom_key_product_sale_price'                 => 'acp_sale_price',
            'product_sale_price_effective_date'             => 'woo', // woo|attr|meta
            'custom_key_product_sale_price_effective_date'  => 'acp_sale_price_effective_date',
            'product_unit_pricing_measure'                  => 'attr', // attr|meta
            'custom_key_product_unit_pricing_measure'       => 'acp_unit_pricing_measure',
            'product_base_measure'                          => 'attr', // attr|meta
            'custom_key_product_base_measure'               => 'acp_base_measure',
            'product_pricing_trend'                         => 'attr', // attr|meta
            'custom_key_product_pricing_trend'              => 'acp_pricing_trend',
            'product_availability'                          => 'woo', // woo|attr|meta
            'custom_key_product_availability'               => 'acp_availability',
            'product_availability_date'                     => 'attr', // attr|meta
            'custom_key_product_availability_date'          => 'acp_availability_date',
            'product_inventory_quantity'                    => 'woo', // woo|attr|meta
            'custom_key_product_inventory_quantity'         => 'acp_inventory_quantity',
            'product_expiration_date'                       => 'attr', // attr|meta
            'custom_key_product_expiration_date'            => 'acp_expiration_date',
            'product_pickup_method'                         => 'attr', // attr|meta
            'custom_key_product_pickup_method'              => 'acp_pickup_method',
            'product_pickup_sla'                            => 'attr', // attr|meta
            'custom_key_product_pickup_sla'                 => 'acp_pickup_sla',
            'product_shipping'                              => 'woo', // woo|attr|meta
            'custom_key_product_shipping'                   => 'acp_shipping',
            'product_delivery_estimate'                     => 'attr', // attr|meta
            'custom_key_product_delivery_estimate'          => 'acp_delivery_estimate',
            'product_seller_name'                           => 'static', // static|attr|meta
            'custom_key_product_seller_name'                => get_bloginfo('name'),
            'product_seller_url'                            => 'static', // static|attr|meta
            'custom_key_product_seller_url'                 => home_url('/'),
            'product_seller_privacy_policy'                 => 'static', // static|attr|meta
            'custom_key_product_seller_privacy_policy'      => get_privacy_policy_url(),
            'product_seller_tos'                            => 'static', // static|attr|meta
            'custom_key_product_seller_tos'                 => 'acp_seller_tos',
            'product_return_policy'                         => 'static', // static|attr|meta
            'custom_key_product_return_policy'              => 'acp_return_policy',
            'product_return_window'                         => 'static', // static|attr|meta
            'custom_key_product_return_window'              => 30,
            'product_popularity_score'                      => 'attr', // attr|meta
            'custom_key_product_popularity_score'           => 'acp_popularity_score',
            'product_return_rate'                           => 'attr', // attr|meta
            'custom_key_product_return_rate'                => 'acp_return_rate',
            'product_warning'                               => 'attr', // attr|meta
            'custom_key_product_warning'                    => 'acp_warning',
            'product_warning_url'                           => 'attr', // attr|meta
            'custom_key_product_warning_url'                => 'acp_warning_url',
            'product_age_restriction'                       => 'static', // static|attr|meta
            'custom_key_product_age_restriction'            => '',
            'product_review_count'                          => 'woo', // woo|attr|meta
            'custom_key_product_review_count'               => 'acp_review_count',
            'product_review_rating'                         => 'woo', // woo|attr|meta
            'custom_key_product_review_rating'              => 'acp_review_rating',
            'product_store_review_count'                    => 'static', // static|attr|meta
            'custom_key_product_store_review_count'         => 'acp_store_review_count',
            'product_store_review_rating'                   => 'static', // static|attr|meta
            'custom_key_product_store_review_rating'        => 'acp_store_review_rating',
            'product_q_and_a'                               => 'attr', // attr|meta
            'custom_key_product_q_and_a'                    => 'acp_q_and_a',
            'product_raw_review_data'                       => 'woo', // woo|attr|meta
            'custom_key_product_raw_review_data'            => 'acp_raw_review_data',
            'product_related_product_id'                    => 'crosssell', // none|crosssell|upsell|both
            'product_relationship_type'                     => 'attr', // attr|meta
            'custom_key_product_relationship_type'          => 'acp_relationship_type',
            'product_geo_price'                             => 'attr', // attr|meta
            'custom_key_product_geo_price'                  => 'acp_geo_price',
            'product_geo_availability'                      => 'attr', // attr|meta
            'custom_key_product_geo_availability'           => 'acp_geo_availability',
        ];
    }

    /**
     * Get all settings
     *
     * @since 1.0.0
     */
    public function acp_get_settings(){
        return $this->acp_settings;
    }

    /**
     * Get single setting
     * 
     * @param string $key - Setting key
     * @param mixed $default - Default value if not set
     * @return mixed - Setting value
     * @since 1.0.0
     */
    public function acp_get_setting($key, $default = null){
        return $this->acp_settings[$key] ?? $default;
    }

    /**
     * Save single setting
     * 
     * @param string $key - Setting key
     * @param mixed $value - Setting value
     * @return bool - True on success, false on failure
     * @since 1.0.0
     */
    public function acp_save_setting($key, $value){
        $this->acp_settings[$key] = $value;
        return update_option(self::ACP_OPTIONS_KEY, $this->acp_settings);
    }

    /**
     * Initialize admin settings
     * 
     * @since 1.0.0
     */
    public function acp_init(){
        add_action('update_option_' . ACP_Feed_Settings::ACP_OPTIONS_KEY, [$this, 'acp_on_settings_update'], 10, 3);

        add_action('admin_menu', [$this, 'acp_menu']);
        add_action('admin_init', [$this, 'acp_register']);
        add_action('admin_enqueue_scripts', [$this, 'acp_admin_assets']);
    }

    /**
     * Called when settings are updated
     * 
     * @since 1.0.0
     */
    public function acp_on_settings_update($old, $new, $option){
        $this->acp_settings = wp_parse_args($new, $this->acp_default_settings());

        acp_log("Settings updated, rescheduling cron as needed.");

        ACP_Feed_Scheduler::acp_reschedule();
    }

    /**
     * Enqueue admin assets
     * 
     * @since 1.0.0
     */
    public function acp_admin_assets($hook_suffix){
        $page = isset($_GET['page']) ? sanitize_key((string) $_GET['page']) : '';
        if ($page !== 'acp-feed-settings') {
            return;
        }

        wp_enqueue_style('acp_admin_styles', ACP_PATH . '/assets/admin-styles.css', array(), ACP_VERSION, 'all');
		wp_enqueue_script('acp_admin_script', ACP_PATH . '/assets/admin-script.js', array(), ACP_VERSION, true);

        wp_localize_script('acp_admin_script', 'ACPAdmin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('acp-admin'),
        ]);
    }

    /**
     * Add admin menu
     * 
     * @since 1.0.0
     */
    public function acp_menu(){
        add_submenu_page(
            'woocommerce',
            __("Agentic Commerce Feed", "acp-feed-woocommerce"),
            __("Agentic Commerce Feed", "acp-feed-woocommerce"),
            'manage_woocommerce',
            'acp-feed-settings',
            [$this, 'acp_render']
        );
    }

    /**
     * Register settings
     * 
     * @since 1.0.0
     */
    public function acp_register(){
        register_setting(self::ACP_OPTIONS_KEY, self::ACP_OPTIONS_KEY, [
            'type' => 'array',
            'sanitize_callback' => [$this, 'acp_sanitize'],
            'default' => $this->acp_default_settings(),
        ]);

        
        /*--------------------------------------------------*/
        /*                  Feed Settings                   */
        /*--------------------------------------------------*/
        add_settings_section('acp_main', __("Feed Settings", "acp-feed-woocommerce"), function () {
            echo '<p>' . esc_html__("Configure logging, scheduling and defaults for feed generation.", "acp-feed-woocommerce") . '</p>';
        }, 'acp-feed-settings');

        add_settings_field(
            'log_enable', 
            __("Logger", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_checkbox'], 
            'acp-feed-settings', 
            'acp_main', 
            array(
                'key' => "log_enable", 
                'desc' => __("Enable logging.", "acp-feed-woocommerce")
            )
        );
        add_settings_field(
            'log_method', 
            __("Logger method", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_main', 
            array(
                'key' => "log_method", 
                'options' => [
                    'nolog' => ['label' => __("No log", "acp-feed-woocommerce"), 'desc' => __("Do not log any messages", "acp-feed-woocommerce")], 
                    'custom' => ['label' => __("Custom log", "acp-feed-woocommerce"), 'desc' => __("Log messages to a separate file in the main directory of the site.", "acp-feed-woocommerce")], 
                    'woocommerce' => ['label' => __("WooCommerce log", "acp-feed-woocommerce"), 'desc' => __("Use the WooCommerce log system.", "acp-feed-woocommerce")]
                ], 
                'desc' => __("Choose logging method.", "acp-feed-woocommerce")
            )
        );

        add_settings_field(
            'cron_method', 
            __("Cron method", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_main', 
            array(
                'key' => "cron_method", 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Uses the WooCommerce cron system", "acp-feed-woocommerce")], 
                    'wp' => ['label' => __("WordPress", "acp-feed-woocommerce"), 'desc' => __("Uses the WordPress cron system", "acp-feed-woocommerce")], 
                    'server' => ['label' => __("Server", "acp-feed-woocommerce"), 'desc' => __("Set up cron yourself on the server.", "acp-feed-woocommerce")]
                ], 
                'desc' => __("Choose cron method.", "acp-feed-woocommerce")
            )
        );
        add_settings_field(
            'cron_interval_minutes', 
            __("Interval (minutes)", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_number'], 
            'acp-feed-settings', 
            'acp_main', 
            array(
                'key' => "cron_interval_minutes",
                1, 
                1, 
                'desc' => __("How often should cron run?", "acp-feed-woocommerce")
            )
        );
        add_settings_field(
            'server_cron_url', 
            __("Server cron URL", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_readonly'], 
            'acp-feed-settings', 
            'acp_main', 
            array(
                'key' => "server_cron_key", 
                'desc' => __("Use this for your server cron.", "acp-feed-woocommerce")
            )
        );

        add_settings_field(
            'batch_size', 
            __("Batch size", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_number'], 
            'acp-feed-settings', 
            'acp_main', 
            array(
                'key' => "batch_size", 
                1, 
                1, 
                'desc' => __("How many products are to be processed in a single batch?", "acp-feed-woocommerce")
            )
        );

        /*--------------------------------------------------*/
        /*                Product Settings                  */
        /*--------------------------------------------------*/
        add_settings_section('acp_product_settings', __("Product Settings", "acp-feed-woocommerce"), function () {
            echo '<p>' . esc_html__("Configure product options for feed.", "acp-feed-woocommerce") . '</p>';
        }, 'acp-feed-settings');       

        add_settings_field(
            'product_too_long_value', 
            __("Too long value", "acp-feed-woocommerce"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product_settings', 
            array(
                'key' => "product_too_long_value", 
                'options' => [
                    'truncate' => ['label' => __("Truncate", "acp-feed-woocommerce"), 'desc' => __("Truncate the value to fit the required length.", "acp-feed-woocommerce")],
                    'omit' => ['label' => __("Omit product", "acp-feed-woocommerce"), 'desc' => __("Omit the product entirely if any of the values exceeds the required length.", "acp-feed-woocommerce")]
                ], 
                'desc' => __("Select what should happen if the value of the attribute to be exported is too long.", "acp-feed-woocommerce")
            )
        );

        /*--------------------------------------------------*/
        /*                Product Mapping                   */
        /*--------------------------------------------------*/
        add_settings_section('acp_product', __("Product Mapping", "acp-feed-woocommerce"), function () {
            echo '<p>' . esc_html__("Configure product options for feed.", "acp-feed-woocommerce") . '</p>';
        }, 'acp-feed-settings');

        // ACP: enable_search
        add_settings_field(
            'enable_search', 
            sprintf(__("%s Product search active (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>enable_search</code>"), 
            [$this, 'acp_settings_field_description'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "enable_search", 
                'desc' => __("AI searches can only be performed on products that have been published and the rest of the required attributes are specified correctly.", "acp-feed-woocommerce")
            )
        );

        // ACP: enable_checkout
        add_settings_field(
            'enable_checkout', 
            sprintf(__("%s Product checkout active (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>enable_checkout</code>"), 
            [$this, 'acp_settings_field_description'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "enable_checkout", 
                'desc' => __("AI will only allow purchase of the product if:<br>1) enable_search is true;<br>2) the product is in stock or on back order;<br>3) the rest of the required attributes are specified correctly.", "acp-feed-woocommerce")
            )
        );

        // ACP: id
        add_settings_field(
            'product_id', 
            sprintf(__("%s Product ID (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>id</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_id", 
                'custom_key' => 'custom_key_product_id', 
                'options' => [
                    'id' => ['label' => __("Product ID", "acp-feed-woocommerce"), 'desc' => __("Use Product ID set assigned by WooCommerce", "acp-feed-woocommerce")], 
                    'sku' => ['label' => __("Product SKU", "acp-feed-woocommerce"), 'desc' => __("Use Product SKU that you can assign in product settings.", "acp-feed-woocommerce")], 
                    'both' => ['label' => __("SKU or ID", "acp-feed-woocommerce"), 'desc' => __("Use the product SKU first; if it is not set, use the product ID.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Select the source of the product identifier: SKU, ID, product attribute, or metadata.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Alphanumeric; max 100 chars; must remain stable over time; must be unique for each product in the feed.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: gtin
        add_settings_field(
            'product_gtin', 
            sprintf(__("Product GTIN (ACP: %s)", "acp-feed-woocommerce"), "<code>gtin</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_gtin", 
                'custom_key' => 'custom_key_product_gtin',
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the 'GTIN, UPC, EAN, ISBN' field in the product settings in the 'Inventory' tab.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Select the source of the GTIN value.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Provide 8-14 digits with no dashes or spaces.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: mpn
        add_settings_field(
            'product_mpn', 
            sprintf(__("Product MPN (ACP: %s)", "acp-feed-woocommerce"), "<code>mpn</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_mpn", 
                'custom_key' => 'custom_key_product_mpn',
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Select the source of the MPN value.", "acp-feed-woocommerce"),
                    __("Required if GTIN is missing", "acp-feed-woocommerce"),
                    __("Alphanumeric; max 70 chars.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: title
        add_settings_field(
            'product_title', 
            sprintf(__("%s Product title (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>title</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_title", 
                'custom_key' => 'custom_key_product_title',
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the Product Title.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Select the source of the product title.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Max 150 chars; avoid all caps for best rendering.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: description
        add_settings_field(
            'product_description', 
            sprintf(__("%s Product description (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>description</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_description", 
                'custom_key' => 'custom_key_product_description', 
                'options' => [
                    'long' => ['label' => __("Long description", "acp-feed-woocommerce"), 'desc' => __("Long description of the product which you normally add to the product.", "acp-feed-woocommerce")], 
                    'short' => ['label' => __("Short description", "acp-feed-woocommerce"), 'desc' => __("Short description of the product which you normally add to the product.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Which description should be sent?", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Max 5,000 chars; plain text only.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: link
        add_settings_field(
            'product_link', 
            sprintf(__("%s Product link (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>link</code>"), 
            [$this, 'acp_settings_field_text'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_link", 
                'desc' => $this->acp_format_desc(
                    __("Link to product. Variable: %product_link% - link to product", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("URL must return HTTP 200; HTTPS is preferred.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: condition
        add_settings_field(
            'product_condition', 
            sprintf(__("%s Product condition (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>condition</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_condition", 
                'custom_key' => 'custom_key_product_condition', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Condition of the product.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Use lower-case strings, allowed: 'new', 'refurbished', or 'used'.", "acp-feed-woocommerce")
                )
            )
        );
        
        // ACP: product_category
        add_settings_field(
            'product_category', 
            sprintf(__("%s Product category (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>product_category</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_category", 
                'custom_key' => 'custom_key_product_category', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product categories assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Category of the product.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Use the '>' separator between taxonomy levels, for example: Apparel > Shoes.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: brand
        add_settings_field(
            'product_brand', 
            sprintf(__("%s Product brand (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>brand</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_brand", 
                'custom_key' => 'custom_key_product_brand', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product brands assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Brand of the product.", "acp-feed-woocommerce"),
                    __("Required for all products except movies, books, and musical recording brands", "acp-feed-woocommerce"),
                    __("Max 70 chars.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: material
        add_settings_field(
            'product_material', 
            sprintf(__("%s Product material (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>material</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_material", 
                'custom_key' => 'custom_key_product_material', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Material of the product.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Max 100 chars.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: dimensions
        add_settings_field(
            'product_dimensions', 
            sprintf(__("Product dimensions (ACP: %s)", "acp-feed-woocommerce"), "<code>dimensions</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_dimensions", 
                'custom_key' => 'custom_key_product_dimensions', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product length, width and height assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Dimensions of the product in LxWxH format with unit (e.g. 12x8x5 cm).", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Units required if provided. Unit in abbreviations such as in, cm, or mm.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: length
        add_settings_field(
            'product_length', 
            sprintf(__("Product length (ACP: %s)", "acp-feed-woocommerce"), "<code>length</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_length", 
                'custom_key' => 'custom_key_product_length', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product length assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Length of the product with unit (e.g. 10 mm).", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Include a unit such as mm, cm, in, or ft.", "acp-feed-woocommerce"),
                    __("Provide length, width, and height when using individual fields.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: width
        add_settings_field(
            'product_width', 
            sprintf(__("Product width (ACP: %s)", "acp-feed-woocommerce"), "<code>width</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_width", 
                'custom_key' => 'custom_key_product_width', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product width assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Width of the product with unit (e.g. 10 mm).", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Include a unit such as mm, cm, in, or ft.", "acp-feed-woocommerce"),
                    __("Provide length, width, and height when using individual fields.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: height
        add_settings_field(
            'product_height', 
            sprintf(__("Product height (ACP: %s)", "acp-feed-woocommerce"), "<code>height</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_height", 
                'custom_key' => 'custom_key_product_height', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product height assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Height of the product with unit (e.g. 10 mm).", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Include a unit such as mm, cm, in, or ft.", "acp-feed-woocommerce"),
                    __("Provide length, width, and height when using individual fields.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: weight
        add_settings_field(
            'product_weight', 
            sprintf(__("%s Product weight (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>weight</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_weight", 
                'custom_key' => 'custom_key_product_weight', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product weight assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Weight of the product with unit (e.g. 1.5 lb).", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Provide a positive number and unit such as kg, g, lb, or oz.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: age_group
        add_settings_field(
            'product_age_group', 
            sprintf(__("Product age group (ACP: %s)", "acp-feed-woocommerce"), "<code>age_group</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_age_group", 
                'custom_key' => 'custom_key_product_age_group', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Age group of the product. Allowed values: 'newborn', 'infant', 'toddler', 'kids', 'adult'.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Use lower-case values exactly as listed.", "acp-feed-woocommerce")
                )
            )
        );


        // ACP: image_link
        add_settings_field(
            'product_image_link', 
            sprintf(__("%s Product image link (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>image_link</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_image_link", 
                'custom_key' => 'custom_key_product_image_link', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product image assigned in WooCommerce (main product image).", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Link to the main product image.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Use a direct JPEG or PNG URL; HTTPS preferred.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: additional_image_link
        add_settings_field(
            'product_additional_image_link', 
            sprintf(__("Product additional image links (ACP: %s)", "acp-feed-woocommerce"), "<code>additional_image_link</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_additional_image_link", 
                'custom_key' => 'custom_key_product_additional_image_link', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product gallery images assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Links to additional product images.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide a comma-separated list or array of valid image URLs.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: video_link
        add_settings_field(
            'product_video_link', 
            sprintf(__("Product video links (ACP: %s)", "acp-feed-woocommerce"), "<code>video_link</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_video_link", 
                'custom_key' => 'custom_key_product_video_link', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Links to product videos.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("URLs must be publicly accessible without authentication.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: model_3d_link
        add_settings_field(
            'product_model_3d_link', 
            sprintf(__("Product 3D model links (ACP: %s)", "acp-feed-woocommerce"), "<code>model_3d_link</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_model_3d_link", 
                'custom_key' => 'custom_key_product_model_3d_link', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Links to product 3D models.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Use publicly accessible URLs; GLB or GLTF formats are preferred.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: price
        add_settings_field(
            'product_price', 
            sprintf(__("%s Product price (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>price</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_price", 
                'custom_key' => 'custom_key_product_price', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product price assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Price of the product. Price with currency, e.g. 29.99 USD", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Include an ISO 4217 currency code in the value.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: applicable_taxes_fees
        add_settings_field(
            'product_applicable_taxes_fees', 
            sprintf(__("Product applicable taxes and fees (ACP: %s)", "acp-feed-woocommerce"), "<code>applicable_taxes_fees</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_applicable_taxes_fees", 
                'custom_key' => 'custom_key_product_applicable_taxes_fees', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Applicable taxes and fees for the product. Price with currency, e.g. 29.99 USD", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Include an ISO 4217 currency code in the value.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: sale_price
        add_settings_field(
            'product_sale_price', 
            sprintf(__("Product sale price (ACP: %s)", "acp-feed-woocommerce"), "<code>sale_price</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_sale_price", 
                'custom_key' => 'custom_key_product_sale_price', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product sale price assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Sale price of the product. Price with currency, e.g. 19.99 USD", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Sale price must be less than or equal to the regular price and include currency.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: sale_price_effective_date	
        add_settings_field(
            'product_sale_price_effective_date', 
            sprintf(__("Product sale price effective date (ACP: %s)", "acp-feed-woocommerce"), "<code>sale_price_effective_date</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_sale_price_effective_date", 
                'custom_key' => 'custom_key_product_sale_price_effective_date', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product sale price schedule assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Effective date range for the sale price of the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide ISO 8601 start and end dates where the start precedes the end.", "acp-feed-woocommerce"),
                    __("Required when a sale price is provided.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: unit_pricing_measure 
        add_settings_field(
            'product_unit_pricing_measure', 
            sprintf(__("Product unit pricing measure (ACP: %s)", "acp-feed-woocommerce"), "<code>unit_pricing_measure</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_unit_pricing_measure", 
                'custom_key' => 'custom_key_product_unit_pricing_measure', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The unit pricing measure for the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide a numeric value with unit, such as 16 oz.", "acp-feed-woocommerce"),
                    __("Submit together with the base measure.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: base_measure
        add_settings_field(
            'product_base_measure', 
            sprintf(__("Product base measure (ACP: %s)", "acp-feed-woocommerce"), "<code>base_measure</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_base_measure", 
                'custom_key' => 'custom_key_product_base_measure', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The base measure for the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide the unit reference, such as 1 oz.", "acp-feed-woocommerce"),
                    __("Submit together with the unit pricing measure.", "acp-feed-woocommerce")
                )
            )
        );  

        // ACP: pricing_trend
        add_settings_field(
            'product_pricing_trend', 
            sprintf(__("Product pricing trend (ACP: %s)", "acp-feed-woocommerce"), "<code>pricing_trend</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_pricing_trend", 
                'custom_key' => 'custom_key_product_pricing_trend', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The pricing trend for the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Limit to 80 characters, for example 'Lowest price in 6 months'.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: availability
        add_settings_field(
            'product_availability', 
            sprintf(__("%s Product availability (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>availability</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_availability", 
                'custom_key' => 'custom_key_product_availability', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product stock management system assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Availability of the product.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Use lower-case values, allowed: 'in_stock', 'out_of_stock', or 'preorder'.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: availability_date
        add_settings_field(
            'product_availability_date', 
            sprintf(__("Product availability date (ACP: %s)", "acp-feed-woocommerce"), "<code>availability_date</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_availability_date", 
                'custom_key' => 'custom_key_product_availability_date', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The availability date for the product.", "acp-feed-woocommerce"),
                    __("Required when availability is preorder", "acp-feed-woocommerce"),
                    __("Provide a future ISO 8601 date.", "acp-feed-woocommerce"),
                    __("Only needed when availability is set to preorder.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: inventory_quantity
        add_settings_field(
            'product_inventory_quantity', 
            sprintf(__("%s Product inventory quantity (ACP: %s)", "acp-feed-woocommerce"), "<span class='acp-required'></span>", "<code>inventory_quantity</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_inventory_quantity", 
                'custom_key' => 'custom_key_product_inventory_quantity', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product inventory quantity assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Inventory quantity of the product.", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Provide a non-negative integer.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: expiration_date
        add_settings_field(
            'product_expiration_date', 
            sprintf(__("Product expiration date (ACP: %s)", "acp-feed-woocommerce"), "<code>expiration_date</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_expiration_date", 
                'custom_key' => 'custom_key_product_expiration_date', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The expiration date of the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide a future ISO 8601 date.", "acp-feed-woocommerce")
                )
            )
        ); 

        // ACP: pickup_method
        add_settings_field(
            'product_pickup_method', 
            sprintf(__("Product pickup method (ACP: %s)", "acp-feed-woocommerce"), "<code>pickup_method</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_pickup_method", 
                'custom_key' => 'custom_key_product_pickup_method', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The pickup method for the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Use lower-case values, allowed: 'in_store', 'reserve', or 'not_supported'.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: pickup_sla
        add_settings_field(
            'product_pickup_sla', 
            sprintf(__("Product pickup SLA (ACP: %s)", "acp-feed-woocommerce"), "<code>pickup_sla</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_pickup_sla", 
                'custom_key' => 'custom_key_product_pickup_sla', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The pickup SLA for the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Use a positive integer and time unit, for example 1 day.", "acp-feed-woocommerce"),
                    __("Pickup method must be provided.", "acp-feed-woocommerce")
                )
            )
        );



        // TODO: variants - awaiting clarification from OpenAI


        // ACP: shipping
        add_settings_field(
            'product_shipping', 
            sprintf(__("Product shipping (ACP: %s)", "acp-feed-woocommerce"), "<code>shipping</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_shipping", 
                'custom_key' => 'custom_key_product_shipping', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the shipping methods assigned in WooCommerce.", "acp-feed-woocommerce")], 
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Shipping details for the product.", "acp-feed-woocommerce"),
                    __("Required where applicable", "acp-feed-woocommerce"),
                    __("Use colon-delimited entries such as country:region:service:price.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: delivery_estimate
        add_settings_field(
            'product_delivery_estimate', 
            sprintf(__("Product delivery estimate (ACP: %s)", "acp-feed-woocommerce"), "<code>delivery_estimate</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_delivery_estimate", 
                'custom_key' => 'custom_key_product_delivery_estimate', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The delivery estimate for the product.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide a future ISO 8601 date.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: seller_name
        add_settings_field(
            'product_seller_name', 
            sprintf(__("Product seller name (ACP: %s)", "acp-feed-woocommerce"), "<code>seller_name</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_seller_name", 
                'custom_key' => 'custom_key_product_seller_name', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Name of the seller, e.g. name of your shop", "acp-feed-woocommerce"),
                    __("Required for display", "acp-feed-woocommerce"),
                    __("Max 70 chars.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: seller_url
        add_settings_field(
            'product_seller_url', 
            sprintf(__("Product seller url (ACP: %s)", "acp-feed-woocommerce"), "<code>seller_url</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_seller_url", 
                'custom_key' => 'custom_key_product_seller_url', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("URL of the seller, e.g. url of your shop", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("HTTPS URL is preferred and should resolve successfully.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: seller_privacy_policy
        add_settings_field(
            'product_seller_privacy_policy', 
            sprintf(__("Product seller privacy policy (ACP: %s)", "acp-feed-woocommerce"), "<code>seller_privacy_policy</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_seller_privacy_policy", 
                'custom_key' => 'custom_key_product_seller_privacy_policy', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("URL of your privacy policy", "acp-feed-woocommerce"),
                    __("Required when checkout is enabled", "acp-feed-woocommerce"),
                    __("Use an HTTPS URL that resolves successfully.", "acp-feed-woocommerce"),
                    __("Depends on enable_checkout being true.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: seller_tos
        add_settings_field(
            'product_seller_tos', 
            sprintf(__("Product seller terms of service (ACP: %s)", "acp-feed-woocommerce"), "<code>seller_tos</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_seller_tos", 
                'custom_key' => 'custom_key_product_seller_tos', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("URL of your terms of service", "acp-feed-woocommerce"),
                    __("Required when checkout is enabled", "acp-feed-woocommerce"),
                    __("Use an HTTPS URL that resolves successfully.", "acp-feed-woocommerce"),
                    __("Depends on enable_checkout being true.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: return_policy
        add_settings_field(
            'product_return_policy', 
            sprintf(__("Product return policy (ACP: %s)", "acp-feed-woocommerce"), "<code>return_policy</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_return_policy", 
                'custom_key' => 'custom_key_product_return_policy', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("URL of your return policy", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Use an HTTPS URL that resolves successfully.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: return_window
        add_settings_field(
            'product_return_window', 
            sprintf(__("Product return window (ACP: %s)", "acp-feed-woocommerce"), "<code>return_window</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product', 
            array(
                'key' => "product_return_window", 
                'custom_key' => 'custom_key_product_return_window', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("Number of days within which returns are accepted", "acp-feed-woocommerce"),
                    __("Required", "acp-feed-woocommerce"),
                    __("Provide a positive integer representing days.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: popularity_score
        add_settings_field(
            'product_popularity_score', 
            sprintf(__("Product popularity score (ACP: %s)", "acp-feed-woocommerce"), "<code>popularity_score</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_popularity_score", 
                'custom_key' => 'custom_key_product_popularity_score', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The popularity score for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Use a 0-5 scale or another merchant-defined scoring system.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: return_rate
        add_settings_field(
            'product_return_rate', 
            sprintf(__("Product return rate (ACP: %s)", "acp-feed-woocommerce"), "<code>return_rate</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_return_rate", 
                'custom_key' => 'custom_key_product_return_rate', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The return rate for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Express as a percentage between 0 and 100.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: warning
        add_settings_field(
            'product_warning', 
            sprintf(__("Product warning (ACP: %s)", "acp-feed-woocommerce"), "<code>warning</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_warning", 
                'custom_key' => 'custom_key_product_warning', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The warning for the product.", "acp-feed-woocommerce"),
                    __("Recommended for checkout-enabled products", "acp-feed-woocommerce"),
                    __("Provide plain text disclaimers such as safety or regulatory notices.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: warning_url
        add_settings_field(
            'product_warning_url', 
            sprintf(__("Product warning url (ACP: %s)", "acp-feed-woocommerce"), "<code>warning_url</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_warning_url", 
                'custom_key' => 'custom_key_product_warning_url', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The warning URL for the product.", "acp-feed-woocommerce"),
                    __("Recommended for checkout-enabled products", "acp-feed-woocommerce"),
                    __("URL must resolve with HTTP 200.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: age_restriction
        add_settings_field(
            'product_age_restriction', 
            sprintf(__("Product age restriction (ACP: %s)", "acp-feed-woocommerce"), "<code>age_restriction</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_age_restriction", 
                'custom_key' => 'custom_key_product_age_restriction', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The age restriction for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Provide a positive integer representing the minimum age.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: product_review_count
        add_settings_field(
            'product_review_count', 
            sprintf(__("Product review count (ACP: %s)", "acp-feed-woocommerce"), "<code>product_review_count</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_review_count", 
                'custom_key' => 'custom_key_product_review_count', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product review count of your real reviews for the product.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The review count for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Provide a non-negative integer based on real reviews.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: product_review_rating
        add_settings_field(
            'product_review_rating', 
            sprintf(__("Product review rating (ACP: %s)", "acp-feed-woocommerce"), "<code>product_review_rating</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_review_rating", 
                'custom_key' => 'custom_key_product_review_rating', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product average review rating of your real reviews for the product.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The review rating for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Use a 0-5 scale averaged from authentic reviews.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: store_review_count
        add_settings_field(
            'product_store_review_count', 
            sprintf(__("Store review count (ACP: %s)", "acp-feed-woocommerce"), "<code>store_review_count</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_store_review_count", 
                'custom_key' => 'custom_key_product_store_review_count', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The review count for the store or brand.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Provide a non-negative integer.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: store_review_rating
        add_settings_field(
            'product_store_review_rating', 
            sprintf(__("Store review rating (ACP: %s)", "acp-feed-woocommerce"), "<code>store_review_rating</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_store_review_rating", 
                'custom_key' => 'custom_key_product_store_review_rating', 
                'options' => [
                    'static' => ['label' => __("Static value", "acp-feed-woocommerce"), 'desc' => __("Enter a static value for all products.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The review rating for the store or brand.", "acp-feed-woocommerce"),
                    __("Optional", "acp-feed-woocommerce"),
                    __("Use a 0-5 scale.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: q_and_a
        add_settings_field(
            'product_q_and_a', 
            sprintf(__("Product Q&A count (ACP: %s)", "acp-feed-woocommerce"), "<code>q_and_a</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_q_and_a", 
                'custom_key' => 'custom_key_product_q_and_a', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The Q&A content for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Use plain text entries summarizing common questions and answers.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: raw_review_data
        add_settings_field(
            'product_raw_review_data', 
            sprintf(__("Product raw review data (ACP: %s)", "acp-feed-woocommerce"), "<code>raw_review_data</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_raw_review_data", 
                'custom_key' => 'custom_key_product_raw_review_data', 
                'options' => [
                    'woo' => ['label' => __("WooCommerce", "acp-feed-woocommerce"), 'desc' => __("Use the product reviews data of your real reviews for the product.", "acp-feed-woocommerce")],
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The raw review data for the product in JSON format.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("May include JSON or other structured payloads with review details.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: related_product_id
        add_settings_field(
            'product_related_product_id', 
            sprintf(__("Related product IDs (ACP: %s)", "acp-feed-woocommerce"), "<code>related_product_id</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_related_product_id", 
                'options' => [
                    'none' => ['label' => __("None", "acp-feed-woocommerce"), 'desc' => __("Don't include related product IDs.", "acp-feed-woocommerce")],
                    'crosssell' => ['label' => __("Cross-sell products", "acp-feed-woocommerce"), 'desc' => __("Use the cross-sell products assigned in WooCommerce.", "acp-feed-woocommerce")],
                    'upsell' => ['label' => __("Up-sell products", "acp-feed-woocommerce"), 'desc' => __("Use the up-sell products assigned in WooCommerce.", "acp-feed-woocommerce")],
                    'both' => ['label' => __("Both", "acp-feed-woocommerce"), 'desc' => __("Use both cross-sell and up-sell products assigned in WooCommerce.", "acp-feed-woocommerce")],
                ], 
                'desc' => $this->acp_format_desc(
                    __("The related product IDs for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Provide a comma-separated list of associated product IDs.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: relationship_type
        add_settings_field(
            'product_relationship_type', 
            sprintf(__("Relationship type (ACP: %s)", "acp-feed-woocommerce"), "<code>relationship_type</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_relationship_type", 
                'custom_key' => 'custom_key_product_relationship_type', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The relationship type for the related products.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Use lower-case values such as part_of_set, accessory, or substitute.", "acp-feed-woocommerce")
                )
            )
        );

        add_settings_field(
            'acp_warning_box',
            __("Important notice", "acp-feed-woocommerce"),
            [$this, 'acp_settings_field_warning'],
            'acp-feed-settings',
            'acp_product',
            array(
                'label' => __("Be careful with the following ACP fields", "acp-feed-woocommerce"),
                'desc' => __("The following options: geo_price and geo_availability can be set, but are not validated. Waiting for OpenAI", "acp-feed-woocommerce")
            )
        );

        // ACP: geo_price
        add_settings_field(
            'product_geo_price', 
            sprintf(__("Product geo price (ACP: %s)", "acp-feed-woocommerce"), "<code>geo_price</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_geo_price", 
                'custom_key' => 'custom_key_product_geo_price', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The geo price for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("Include regional price details with ISO 4217 currency codes.", "acp-feed-woocommerce")
                )
            )
        );

        // ACP: geo_availability
        add_settings_field(
            'product_geo_availability', 
            sprintf(__("Product geo availability (ACP: %s)", "acp-feed-woocommerce"), "<code>geo_availability</code>"), 
            [$this, 'acp_settings_field_select'], 
            'acp-feed-settings', 
            'acp_product',
            array(
                'key' => "product_geo_availability", 
                'custom_key' => 'custom_key_product_geo_availability', 
                'options' => [
                    'attr' => ['label' => __("Attribute", "acp-feed-woocommerce"), 'desc' => __("Use the product attribute; enter the attribute key, e.g. 'pa_attribute_name' (global attribute), 'diffrent_attribute_name' (from the product level) or something else.", "acp-feed-woocommerce")], 
                    'meta' => ['label' => __("Metadata", "acp-feed-woocommerce"), 'desc' => __("Use product metadata; enter the meta key, or ACF field name.", "acp-feed-woocommerce")]
                ], 
                'desc' => $this->acp_format_desc(
                    __("The geo availability for the product.", "acp-feed-woocommerce"),
                    __("Recommended", "acp-feed-woocommerce"),
                    __("List availability overrides per region using valid ISO 3166 codes.", "acp-feed-woocommerce")
                )
            )
        );
    }

    /**
     * Sanitize ACP feed settings
     *
     * @param array $new_settings_unsanitized - The unsanitized new settings
     * @return array - The sanitized new settings
     * @since 1.0.0
     */
    public function acp_sanitize($new_settings_unsanitized){
        $new_settings = array();
        $new_settings['log_enable'] = 
            $new_settings_unsanitized['log_enable'] == 'on' 
            ? 'on' 
            : '';
        $new_settings['log_method'] = 
            in_array($new_settings_unsanitized['log_method'] ?? 'nolog', ['nolog', 'custom', 'woocommerce'], true) 
            ? $new_settings_unsanitized['log_method'] 
            : $this->acp_settings['log_method'];

        $new_settings['cron_method'] = 
            in_array($new_settings_unsanitized['cron_method'] ?? 'server', ['woo','wp','server'], true) 
            ? $new_settings_unsanitized['cron_method'] 
            : $this->acp_settings['cron_method'];
        $new_settings['cron_interval_minutes'] = max(1, (int)($new_settings_unsanitized['cron_interval_minutes'] ?? $this->acp_settings['cron_interval_minutes']));
        $new_settings['server_cron_key'] = $this->acp_settings['server_cron_key'];

        $new_settings['batch_size'] = max(1, (int)($new_settings_unsanitized['batch_size'] ?? $this->acp_settings['batch_size']));

        $new_settings['product_types'] = array_values(array_intersect((array)($new_settings_unsanitized['product_types'] ?? $this->acp_settings['product_types']), ['simple', 'variable', 'grouped', 'external']));
        $new_settings['product_too_long_value'] = 
            in_array($new_settings_unsanitized['product_too_long_value'] ?? 'truncate', ['truncate', 'omit'], true) 
            ? $new_settings_unsanitized['product_too_long_value'] 
            : $this->acp_settings['product_too_long_value'];

        // ACP: product_id   
        $new_settings['product_id'] = 
            in_array($new_settings_unsanitized['product_id'] ?? 'id', ['id', 'sku', 'both', 'attr', 'meta'], true) 
            ? $new_settings_unsanitized['product_id'] 
            : $this->acp_settings['product_id'];

        // Custom key for ACP: product_id (text field)
        $new_settings['custom_key_product_id'] =
            isset($new_settings_unsanitized['custom_key_product_id'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_id'])
                : $this->acp_settings['custom_key_product_id'];

        // ACP: product_gtin
        $new_settings['product_gtin'] =
            in_array($new_settings_unsanitized['product_gtin'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_gtin']
                : $this->acp_settings['product_gtin'];

        // Custom key for ACP: product_gtin (text field)
        $new_settings['custom_key_product_gtin'] =
            isset($new_settings_unsanitized['custom_key_product_gtin'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_gtin'])
                : $this->acp_settings['custom_key_product_gtin'];

        // ACP: product_mpn
        $new_settings['product_mpn'] =
            in_array($new_settings_unsanitized['product_mpn'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_mpn']
                : $this->acp_settings['product_mpn'];
                
        // Custom key for ACP: product_mpn (text field)
        $new_settings['custom_key_product_mpn'] =
            isset($new_settings_unsanitized['custom_key_product_mpn'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_mpn'])
                : $this->acp_settings['custom_key_product_mpn'];

        // ACP: product_title
        $new_settings['product_title'] =
            in_array($new_settings_unsanitized['product_title'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_title']
                : $this->acp_settings['product_title'];
                
        // Custom key for ACP: product_title (text field)
        $new_settings['custom_key_product_title'] =
            isset($new_settings_unsanitized['custom_key_product_title'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_title'])
                : $this->acp_settings['custom_key_product_title'];

        // ACP: product_description
        $new_settings['product_description'] =
            in_array($new_settings_unsanitized['product_description'] ?? 'long', ['long','short','attr','meta'], true)
                ? $new_settings_unsanitized['product_description']
                : $this->acp_settings['product_description'];

        // Custom key for ACP: product_description (text field)
        $new_settings['custom_key_product_description'] =
            isset($new_settings_unsanitized['custom_key_product_description'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_description'])
                : $this->acp_settings['custom_key_product_description'];

        // ACP: product_link (text field)
        // No options, just a text field
        // Sanitize as text field, but validate URL when generating the feed
        $new_settings['product_link'] = isset($new_settings_unsanitized['product_link'])
            ? sanitize_text_field($new_settings_unsanitized['product_link'])
            : $this->acp_settings['product_link'];

        // ACP: product_condition
        $new_settings['product_condition'] =
            in_array($new_settings_unsanitized['product_condition'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_condition']
                : $this->acp_settings['product_condition'];

        // Custom key for ACP: product_condition (text field)
        $new_settings['custom_key_product_condition'] =
            isset($new_settings_unsanitized['custom_key_product_condition'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_condition'])
                : $this->acp_settings['custom_key_product_condition'];

        // ACP: product_category
        $new_settings['product_category'] =
            in_array($new_settings_unsanitized['product_category'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_category']
                : $this->acp_settings['product_category'];

        // Custom key for ACP: product_category (text field)
        $new_settings['custom_key_product_category'] =
            isset($new_settings_unsanitized['custom_key_product_category'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_category'])
                : $this->acp_settings['custom_key_product_category'];

        // ACP: product_brand
        $new_settings['product_brand'] =
            in_array($new_settings_unsanitized['product_brand'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_brand']
                : $this->acp_settings['product_brand'];

        // Custom key for ACP: product_brand (text field)
        $new_settings['custom_key_product_brand'] =
            isset($new_settings_unsanitized['custom_key_product_brand'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_brand'])
                : $this->acp_settings['custom_key_product_brand'];

        // ACP: product_material
        $new_settings['product_material'] =
            in_array($new_settings_unsanitized['product_material'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_material']
                : $this->acp_settings['product_material'];

        // Custom key for ACP: product_material (text field)
        $new_settings['custom_key_product_material'] =
            isset($new_settings_unsanitized['custom_key_product_material'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_material'])
                : $this->acp_settings['custom_key_product_material'];

        // ACP: product_dimensions
        $new_settings['product_dimensions'] =
            in_array($new_settings_unsanitized['product_dimensions'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_dimensions']
                : $this->acp_settings['product_dimensions'];

        // Custom key for ACP: product_dimensions (text field)
        $new_settings['custom_key_product_dimensions'] =
            isset($new_settings_unsanitized['custom_key_product_dimensions'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_dimensions'])
                : $this->acp_settings['custom_key_product_dimensions'];

        // ACP: product_length
        $new_settings['product_length'] =
            in_array($new_settings_unsanitized['product_length'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_length']
                : $this->acp_settings['product_length'];

        // Custom key for ACP: product_length (text field)
        $new_settings['custom_key_product_length'] =
            isset($new_settings_unsanitized['custom_key_product_length'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_length'])
                : $this->acp_settings['custom_key_product_length'];

        // ACP: product_width
        $new_settings['product_width'] =
            in_array($new_settings_unsanitized['product_width'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_width']
                : $this->acp_settings['product_width'];

        // Custom key for ACP: product_width (text field)
        $new_settings['custom_key_product_width'] =
            isset($new_settings_unsanitized['custom_key_product_width'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_width'])
                : $this->acp_settings['custom_key_product_width'];

        // ACP: product_height
        $new_settings['product_height'] =
            in_array($new_settings_unsanitized['product_height'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_height']
                : $this->acp_settings['product_height'];

        // Custom key for ACP: product_height (text field)
        $new_settings['custom_key_product_height'] =
            isset($new_settings_unsanitized['custom_key_product_height'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_height'])
                : $this->acp_settings['custom_key_product_height'];

        // ACP: product_weight
        $new_settings['product_weight'] =
            in_array($new_settings_unsanitized['product_weight'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_weight']
                : $this->acp_settings['product_weight'];

        // Custom key for ACP: product_weight (text field)
        $new_settings['custom_key_product_weight'] =
            isset($new_settings_unsanitized['custom_key_product_weight'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_weight'])
                : $this->acp_settings['custom_key_product_weight'];

        // ACP: product_age_group
        $new_settings['product_age_group'] =
            in_array($new_settings_unsanitized['product_age_group'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_age_group']
                : $this->acp_settings['product_age_group'];

        // Custom key for ACP: product_age_group (text field)
        $new_settings['custom_key_product_age_group'] =
            isset($new_settings_unsanitized['custom_key_product_age_group'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_age_group'])
                : $this->acp_settings['custom_key_product_age_group'];

        // ACP: product_image_link
        $new_settings['product_image_link'] =
            in_array($new_settings_unsanitized['product_image_link'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_image_link']
                : $this->acp_settings['product_image_link'];

        // Custom key for ACP: product_image_link (text field)
        $new_settings['custom_key_product_image_link'] =
            isset($new_settings_unsanitized['custom_key_product_image_link'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_image_link'])
                : $this->acp_settings['custom_key_product_image_link'];

        // ACP: product_additional_image_link
        $new_settings['product_additional_image_link'] =
            in_array($new_settings_unsanitized['product_additional_image_link'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_additional_image_link']
                : $this->acp_settings['product_additional_image_link'];

        // Custom key for ACP: product_additional_image_link (text field)
        $new_settings['custom_key_product_additional_image_link'] =
            isset($new_settings_unsanitized['custom_key_product_additional_image_link'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_additional_image_link'])
                : $this->acp_settings['custom_key_product_additional_image_link'];

        // ACP: product_video_link
        $new_settings['product_video_link'] =
            in_array($new_settings_unsanitized['product_video_link'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_video_link']
                : $this->acp_settings['product_video_link'];

        // Custom key for ACP: product_video_link (text field)
        $new_settings['custom_key_product_video_link'] =
            isset($new_settings_unsanitized['custom_key_product_video_link'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_video_link'])
                : $this->acp_settings['custom_key_product_video_link'];

        // ACP: product_model_3d_link
        $new_settings['product_model_3d_link'] =
            in_array($new_settings_unsanitized['product_model_3d_link'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_model_3d_link']
                : $this->acp_settings['product_model_3d_link'];

        // Custom key for ACP: product_model_3d_link (text field)
        $new_settings['custom_key_product_model_3d_link'] =
            isset($new_settings_unsanitized['custom_key_product_model_3d_link'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_model_3d_link'])
                : $this->acp_settings['custom_key_product_model_3d_link'];

        // product_price
        $new_settings['product_price'] =
            in_array($new_settings_unsanitized['product_price'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_price']
                : $this->acp_settings['product_price'];

        // Custom key for ACP: product_price (text field)
        $new_settings['custom_key_product_price'] =
            isset($new_settings_unsanitized['custom_key_product_price'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_price'])
                : $this->acp_settings['custom_key_product_price'];

        // product_applicable_taxes_fees
        $new_settings['product_applicable_taxes_fees'] =
            in_array($new_settings_unsanitized['product_applicable_taxes_fees'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_applicable_taxes_fees']
                : $this->acp_settings['product_applicable_taxes_fees'];

        // Custom key for ACP: product_applicable_taxes_fees (text field)
        $new_settings['custom_key_product_applicable_taxes_fees'] =
            isset($new_settings_unsanitized['custom_key_product_applicable_taxes_fees'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_applicable_taxes_fees'])
                : $this->acp_settings['custom_key_product_applicable_taxes_fees'];

        // product_sale_price
        $new_settings['product_sale_price'] =
            in_array($new_settings_unsanitized['product_sale_price'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_sale_price']
                : $this->acp_settings['product_sale_price'];

        // Custom key for ACP: product_sale_price (text field)
        $new_settings['custom_key_product_sale_price'] =
            isset($new_settings_unsanitized['custom_key_product_sale_price'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_sale_price'])
                : $this->acp_settings['custom_key_product_sale_price'];

        // product_sale_price_effective_date
        $new_settings['product_sale_price_effective_date'] =
            in_array($new_settings_unsanitized['product_sale_price_effective_date'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_sale_price_effective_date']
                : $this->acp_settings['product_sale_price_effective_date'];

        // Custom key for ACP: product_sale_price_effective_date (text field)
        $new_settings['custom_key_product_sale_price_effective_date'] =
            isset($new_settings_unsanitized['custom_key_product_sale_price_effective_date'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_sale_price_effective_date'])
                : $this->acp_settings['custom_key_product_sale_price_effective_date'];

        // product_unit_pricing_measure
        $new_settings['product_unit_pricing_measure'] =
            in_array($new_settings_unsanitized['product_unit_pricing_measure'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_unit_pricing_measure']
                : $this->acp_settings['product_unit_pricing_measure'];

        // Custom key for ACP: product_unit_pricing_measure (text field)
        $new_settings['custom_key_product_unit_pricing_measure'] =
            isset($new_settings_unsanitized['custom_key_product_unit_pricing_measure'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_unit_pricing_measure'])
                : $this->acp_settings['custom_key_product_unit_pricing_measure'];

        // product_base_measure
        $new_settings['product_base_measure'] =
            in_array($new_settings_unsanitized['product_base_measure'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_base_measure']
                : $this->acp_settings['product_base_measure'];

        // Custom key for ACP: product_base_measure (text field)
        $new_settings['custom_key_product_base_measure'] =
            isset($new_settings_unsanitized['custom_key_product_base_measure'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_base_measure'])
                : $this->acp_settings['custom_key_product_base_measure'];

        // product_pricing_trend
        $new_settings['product_pricing_trend'] =
            in_array($new_settings_unsanitized['product_pricing_trend'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_pricing_trend']
                : $this->acp_settings['product_pricing_trend'];

        // Custom key for ACP: product_pricing_trend (text field)
        $new_settings['custom_key_product_pricing_trend'] =
            isset($new_settings_unsanitized['custom_key_product_pricing_trend'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_pricing_trend'])
                : $this->acp_settings['custom_key_product_pricing_trend'];

        // product_availability
        $new_settings['product_availability'] =
            in_array($new_settings_unsanitized['product_availability'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_availability']
                : $this->acp_settings['product_availability'];

        // Custom key for ACP: product_availability (text field)
        $new_settings['custom_key_product_availability'] =
            isset($new_settings_unsanitized['custom_key_product_availability'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_availability'])
                : $this->acp_settings['custom_key_product_availability'];

        // product_availability_date
        $new_settings['product_availability_date'] =
            in_array($new_settings_unsanitized['product_availability_date'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_availability_date']
                : $this->acp_settings['product_availability_date'];

        // Custom key for ACP: product_availability_date (text field)
        $new_settings['custom_key_product_availability_date'] =
            isset($new_settings_unsanitized['custom_key_product_availability_date'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_availability_date'])
                : $this->acp_settings['custom_key_product_availability_date'];

        // product_inventory_quantity
        $new_settings['product_inventory_quantity'] =
            in_array($new_settings_unsanitized['product_inventory_quantity'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_inventory_quantity']
                : $this->acp_settings['product_inventory_quantity'];

        // Custom key for ACP: product_inventory_quantity (text field)
        $new_settings['custom_key_product_inventory_quantity'] =
            isset($new_settings_unsanitized['custom_key_product_inventory_quantity'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_inventory_quantity'])
                : $this->acp_settings['custom_key_product_inventory_quantity'];

        // product_expiration_date
        $new_settings['product_expiration_date'] =
            in_array($new_settings_unsanitized['product_expiration_date'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_expiration_date']
                : $this->acp_settings['product_expiration_date'];

        // Custom key for ACP: product_expiration_date (text field)
        $new_settings['custom_key_product_expiration_date'] =
            isset($new_settings_unsanitized['custom_key_product_expiration_date'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_expiration_date'])
                : $this->acp_settings['custom_key_product_expiration_date'];

        // product_pickup_method
        $new_settings['product_pickup_method'] =
            in_array($new_settings_unsanitized['product_pickup_method'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_pickup_method']
                : $this->acp_settings['product_pickup_method'];

        // Custom key for ACP: product_pickup_method (text field)
        $new_settings['custom_key_product_pickup_method'] =
            isset($new_settings_unsanitized['custom_key_product_pickup_method'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_pickup_method'])
                : $this->acp_settings['custom_key_product_pickup_method'];

        // product_pickup_sla
        $new_settings['product_pickup_sla'] =
            in_array($new_settings_unsanitized['product_pickup_sla'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_pickup_sla']
                : $this->acp_settings['product_pickup_sla'];

        // Custom key for ACP: product_pickup_sla (text field)
        $new_settings['custom_key_product_pickup_sla'] =
            isset($new_settings_unsanitized['custom_key_product_pickup_sla'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_pickup_sla'])
                : $this->acp_settings['custom_key_product_pickup_sla'];


        // product_shipping
        $new_settings['product_shipping'] =
            in_array($new_settings_unsanitized['product_shipping'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_shipping']
                : $this->acp_settings['product_shipping'];

        // Custom key for ACP: product_shipping (text field)
        $new_settings['custom_key_product_shipping'] =
            isset($new_settings_unsanitized['custom_key_product_shipping'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_shipping'])
                : $this->acp_settings['custom_key_product_shipping'];

        // product_delivery_estimate
        $new_settings['product_delivery_estimate'] =
            in_array($new_settings_unsanitized['product_delivery_estimate'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_delivery_estimate']
                : $this->acp_settings['product_delivery_estimate'];

        // Custom key for ACP: product_delivery_estimate (text field)
        $new_settings['custom_key_product_delivery_estimate'] =
            isset($new_settings_unsanitized['custom_key_product_delivery_estimate'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_delivery_estimate'])
                : $this->acp_settings['custom_key_product_delivery_estimate'];

        // product_seller_name
        $new_settings['product_seller_name'] =
            in_array($new_settings_unsanitized['product_seller_name'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_seller_name']
                : $this->acp_settings['product_seller_name'];

        // Custom key for ACP: product_seller_name (text field)
        $new_settings['custom_key_product_seller_name'] =
            isset($new_settings_unsanitized['custom_key_product_seller_name'])
                ? ( $new_settings['product_seller_name'] === 'static'
                    ? sanitize_text_field($new_settings_unsanitized['custom_key_product_seller_name'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_seller_name']) )
                : $this->acp_settings['custom_key_product_seller_name'];

        // product_seller_url
        $new_settings['product_seller_url'] =
            in_array($new_settings_unsanitized['product_seller_url'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_seller_url']
                : $this->acp_settings['product_seller_url'];

        // Custom key for ACP: product_seller_url (text field)
        $new_settings['custom_key_product_seller_url'] =
            isset($new_settings_unsanitized['custom_key_product_seller_url'])
                ? ( $new_settings['product_seller_url'] === 'static'
                    ? esc_url_raw($new_settings_unsanitized['custom_key_product_seller_url'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_seller_url']) )
                : $this->acp_settings['custom_key_product_seller_url'];

        // product_seller_privacy_policy
        $new_settings['product_seller_privacy_policy'] =
            in_array($new_settings_unsanitized['product_seller_privacy_policy'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_seller_privacy_policy']
                : $this->acp_settings['product_seller_privacy_policy'];

        // Custom key for ACP: product_seller_privacy_policy (text field)
        $new_settings['custom_key_product_seller_privacy_policy'] =
            isset($new_settings_unsanitized['custom_key_product_seller_privacy_policy'])
                ? ( $new_settings['product_seller_privacy_policy'] === 'static'
                    ? esc_url_raw($new_settings_unsanitized['custom_key_product_seller_privacy_policy'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_seller_privacy_policy']) )
                : $this->acp_settings['custom_key_product_seller_privacy_policy'];

        // product_seller_tos
        $new_settings['product_seller_tos'] =
            in_array($new_settings_unsanitized['product_seller_tos'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_seller_tos']
                : $this->acp_settings['product_seller_tos'];

        // Custom key for ACP: product_seller_tos (text field)
        $new_settings['custom_key_product_seller_tos'] =
            isset($new_settings_unsanitized['custom_key_product_seller_tos'])
                ? ( $new_settings['product_seller_tos'] === 'static'
                    ? esc_url_raw($new_settings_unsanitized['custom_key_product_seller_tos'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_seller_tos']) )
                : $this->acp_settings['custom_key_product_seller_tos'];

        // product_return_policy
        $new_settings['product_return_policy'] =
            in_array($new_settings_unsanitized['product_return_policy'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_return_policy']
                : $this->acp_settings['product_return_policy'];

        // Custom key for ACP: product_return_policy (text field)
        $new_settings['custom_key_product_return_policy'] =
            isset($new_settings_unsanitized['custom_key_product_return_policy'])
                ? ( $new_settings['product_return_policy'] === 'static'
                    ? esc_url_raw($new_settings_unsanitized['custom_key_product_return_policy'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_return_policy']) )
                : $this->acp_settings['custom_key_product_return_policy'];

        // product_return_window
        $new_settings['product_return_window'] =
            in_array($new_settings_unsanitized['product_return_window'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_return_window']
                : $this->acp_settings['product_return_window'];

        // Custom key for ACP: product_return_window (text field)
        $new_settings['custom_key_product_return_window'] =
            isset($new_settings_unsanitized['custom_key_product_return_window'])
                ? ( $new_settings['product_return_window'] === 'static'
                    ? (string) absint($new_settings_unsanitized['custom_key_product_return_window'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_return_window']) )
                : $this->acp_settings['custom_key_product_return_window'];

        // product_popularity_score
        $new_settings['product_popularity_score'] =
            in_array($new_settings_unsanitized['product_popularity_score'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_popularity_score']
                : $this->acp_settings['product_popularity_score'];

        // Custom key for ACP: product_popularity_score (text field)
        $new_settings['custom_key_product_popularity_score'] =
            isset($new_settings_unsanitized['custom_key_product_popularity_score'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_popularity_score'])
                : $this->acp_settings['custom_key_product_popularity_score'];

        // product_return_rate
        $new_settings['product_return_rate'] =
            in_array($new_settings_unsanitized['product_return_rate'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_return_rate']
                : $this->acp_settings['product_return_rate'];

        // Custom key for ACP: product_return_rate (text field)
        $new_settings['custom_key_product_return_rate'] =
            isset($new_settings_unsanitized['custom_key_product_return_rate'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_return_rate'])
                : $this->acp_settings['custom_key_product_return_rate'];

        // product_warning
        $new_settings['product_warning'] =
            in_array($new_settings_unsanitized['product_warning'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_warning']
                : $this->acp_settings['product_warning'];

        // Custom key for ACP: product_warning (text field)
        $new_settings['custom_key_product_warning'] =
            isset($new_settings_unsanitized['custom_key_product_warning'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_warning'])
                : $this->acp_settings['custom_key_product_warning'];

        // product_warning_url
        $new_settings['product_warning_url'] =
            in_array($new_settings_unsanitized['product_warning_url'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_warning_url']
                : $this->acp_settings['product_warning_url'];

        // Custom key for ACP: product_warning_url (text field)
        $new_settings['custom_key_product_warning_url'] =
            isset($new_settings_unsanitized['custom_key_product_warning_url'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_warning_url'])
                : $this->acp_settings['custom_key_product_warning_url'];

        // product_age_restriction
        $new_settings['product_age_restriction'] =
            in_array($new_settings_unsanitized['product_age_restriction'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_age_restriction']
                : $this->acp_settings['product_age_restriction'];

        // Custom key for ACP: product_age_restriction (text field)
        $new_settings['custom_key_product_age_restriction'] =
            isset($new_settings_unsanitized['custom_key_product_age_restriction'])
                ? ( $new_settings['product_age_restriction'] === 'static'
                    ? sanitize_text_field($new_settings_unsanitized['custom_key_product_age_restriction'])
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_age_restriction']) )
                : $this->acp_settings['custom_key_product_age_restriction'];

        // product_review_count
        $new_settings['product_review_count'] =
            in_array($new_settings_unsanitized['product_review_count'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_review_count']
                : $this->acp_settings['product_review_count'];

        // Custom key for ACP: product_review_count (text field)
        $new_settings['custom_key_product_review_count'] =
            isset($new_settings_unsanitized['custom_key_product_review_count'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_review_count'])
                : $this->acp_settings['custom_key_product_review_count'];

        // product_review_rating
        $new_settings['product_review_rating'] =
            in_array($new_settings_unsanitized['product_review_rating'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_review_rating']
                : $this->acp_settings['product_review_rating'];

        // Custom key for ACP: product_review_rating (text field)
        $new_settings['custom_key_product_review_rating'] =
            isset($new_settings_unsanitized['custom_key_product_review_rating'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_review_rating'])
                : $this->acp_settings['custom_key_product_review_rating'];

        // product_store_review_count
        $new_settings['product_store_review_count'] =
            in_array($new_settings_unsanitized['product_store_review_count'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_store_review_count']
                : $this->acp_settings['product_store_review_count'];

        // Custom key for ACP: product_store_review_count (text field)
        $new_settings['custom_key_product_store_review_count'] =
            isset($new_settings_unsanitized['custom_key_product_store_review_count'])
                ? ( $new_settings['product_store_review_count'] === 'static'
                    ? ($new_settings_unsanitized['custom_key_product_store_review_count'] != '' ? (string) absint($new_settings_unsanitized['custom_key_product_store_review_count']) : '')
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_store_review_count']) )
                : $this->acp_settings['custom_key_product_store_review_count'];

        // product_store_review_rating
        $new_settings['product_store_review_rating'] =
            in_array($new_settings_unsanitized['product_store_review_rating'] ?? 'static', ['static','attr','meta'], true)
                ? $new_settings_unsanitized['product_store_review_rating']
                : $this->acp_settings['product_store_review_rating'];

        // Custom key for ACP: product_store_review_rating (text field)
        $new_settings['custom_key_product_store_review_rating'] =
            isset($new_settings_unsanitized['custom_key_product_store_review_rating'])
                ? ( $new_settings['product_store_review_rating'] === 'static'
                    ? ($new_settings_unsanitized['custom_key_product_store_review_rating'] != '' ? (string) floatval($new_settings_unsanitized['custom_key_product_store_review_rating']) : '')
                    : sanitize_text_field($new_settings_unsanitized['custom_key_product_store_review_rating']) )
                : $this->acp_settings['custom_key_product_store_review_rating'];

        // product_q_and_a
        $new_settings['product_q_and_a'] =
            in_array($new_settings_unsanitized['product_q_and_a'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_q_and_a']
                : $this->acp_settings['product_q_and_a'];

        // Custom key for ACP: product_q_and_a (text field)
        $new_settings['custom_key_product_q_and_a'] =
            isset($new_settings_unsanitized['custom_key_product_q_and_a'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_q_and_a'])
                : $this->acp_settings['custom_key_product_q_and_a'];

        // product_raw_review_data
        $new_settings['product_raw_review_data'] =
            in_array($new_settings_unsanitized['product_raw_review_data'] ?? 'woo', ['woo','attr','meta'], true)
                ? $new_settings_unsanitized['product_raw_review_data']
                : $this->acp_settings['product_raw_review_data'];

        // Custom key for ACP: product_raw_review_data (text field)
        $new_settings['custom_key_product_raw_review_data'] =
            isset($new_settings_unsanitized['custom_key_product_raw_review_data'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_raw_review_data'])
                : $this->acp_settings['custom_key_product_raw_review_data'];

        // product_related_product_id
        $new_settings['product_related_product_id'] =
            in_array($new_settings_unsanitized['product_related_product_id'] ?? 'crosssell', ['none', 'crosssell', 'upsell', 'both'], true)
                ? $new_settings_unsanitized['product_related_product_id']
                : $this->acp_settings['product_related_product_id'];

        // product_relationship_type
        $new_settings['product_relationship_type'] =
            in_array($new_settings_unsanitized['product_relationship_type'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_relationship_type']
                : $this->acp_settings['product_relationship_type'];

        // Custom key for ACP: product_relationship_type (text field)
        $new_settings['custom_key_product_relationship_type'] =
            isset($new_settings_unsanitized['custom_key_product_relationship_type'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_relationship_type'])
                : $this->acp_settings['custom_key_product_relationship_type'];

        // product_geo_price
        $new_settings['product_geo_price'] =
            in_array($new_settings_unsanitized['product_geo_price'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_geo_price']
                : $this->acp_settings['product_geo_price'];

        // Custom key for ACP: product_geo_price (text field)
        $new_settings['custom_key_product_geo_price'] =
            isset($new_settings_unsanitized['custom_key_product_geo_price'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_geo_price'])
                : $this->acp_settings['custom_key_product_geo_price'];

        // product_geo_availability
        $new_settings['product_geo_availability'] =
            in_array($new_settings_unsanitized['product_geo_availability'] ?? 'attr', ['attr','meta'], true)
                ? $new_settings_unsanitized['product_geo_availability']
                : $this->acp_settings['product_geo_availability'];

        // Custom key for ACP: product_geo_availability (text field)
        $new_settings['custom_key_product_geo_availability'] =
            isset($new_settings_unsanitized['custom_key_product_geo_availability'])
                ? sanitize_text_field($new_settings_unsanitized['custom_key_product_geo_availability'])
                : $this->acp_settings['custom_key_product_geo_availability'];

        return $new_settings;
    }

    /**
     * Render the ACP Feed settings page in the WordPress admin.
     *
     * @since 1.0.0
     */
    public function acp_render(){
        if(!current_user_can('manage_woocommerce'))
            wp_die(__("You do not have permission.", "acp-feed-woocommerce"));
        ?>

        <div class="wrap">
            <h1><?php esc_html_e("Agentic Commerce Protocole Feed", "acp-feed-woocommerce"); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields(self::ACP_OPTIONS_KEY); do_settings_sections('acp-feed-settings'); submit_button(); ?>
            </form>

            <?php
            $current_state = get_option(ACP_Feed::STATE_OPTION);
            $is_build_running = !empty($current_state);
            ?>
            <hr/>
            <h2><?php esc_html_e("Tools", "acp-feed-woocommerce"); ?></h2>
            <p>
                <a class="button button-primary" href="<?php echo $is_build_running ? '' : esc_url(wp_nonce_url(admin_url('admin-post.php?action=' . ACP_Feed_Tools::ACTION_RUN_NOW), 'nonce_' . ACP_Feed_Tools::ACTION_RUN_NOW)); ?>" <?php echo $is_build_running ? 'disabled' : ''; ?>>
                    <?php esc_html_e("Run build now", "acp-feed-woocommerce"); ?>
                </a>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=' . ACP_Feed_Tools::ACTION_RESET_STATE), 'nonce_' . ACP_Feed_Tools::ACTION_RESET_STATE)); ?>">
                    <?php esc_html_e("Reset build state", "acp-feed-woocommerce"); ?>
                </a>
            </p>
            <?php $this->acp_render_status_box(); ?>

            <h3><?php esc_html_e("Logs", "acp-feed-woocommerce"); ?></h3>
            <a class="button button-primary" href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=acp-feed-settings&view-logs'), 'nonce_acp_feed_view_logs')); ?>">
                <?php esc_html_e("View logs", "acp-feed-woocommerce"); ?>
            </a>
            <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=' . ACP_Feed_Tools::ACTION_DOWNLOAD_LOGS), 'nonce_' . ACP_Feed_Tools::ACTION_DOWNLOAD_LOGS)); ?>">
                <?php esc_html_e("Download logs", "acp-feed-woocommerce"); ?>
            </a>
            <div>
                <?php
                if(isset($_GET['view-logs']) && check_admin_referer('nonce_acp_feed_view_logs')){
                    $log_method  = $this->acp_get_setting('log_method', 'custom');

                    $read_tail = static function(string $path, int $max_bytes = 102400): string {
                        if(!file_exists($path) || !is_readable($path)){
                            return '';
                        }
                        $size = filesize($path);
                        if($size === false){
                            $size = 0;
                        }
                        if($size > $max_bytes){
                            $handle = fopen($path, 'rb');
                            if($handle === false){
                                return '';
                            }
                            fseek($handle, -$max_bytes, SEEK_END);
                            $data = fread($handle, $max_bytes);
                            fclose($handle);
                            if($data === false){
                                return '';
                            }
                            return "... truncated ...\n" . ltrim((string)$data);
                        }
                        $contents = file_get_contents($path);
                        return $contents === false ? '' : (string)$contents;
                    };

                    $log_contents = '';
                    if($log_method === 'woocommerce' && function_exists('wc_get_logger')){
                        $handle = "acp-feed-woocommerce";
                        $log_file_path = '';

                        if(class_exists('WC_Log_Handler_File')){
                            $log_file_path = WC_Log_Handler_File::get_log_file_path($handle);
                            if(!file_exists($log_file_path)){
                                $uploads = wp_get_upload_dir();
                                $log_dir = trailingslashit($uploads['basedir']) . 'wc-logs/';
                                if(is_dir($log_dir)){
                                    $candidates = glob($log_dir . $handle . '-*.log');
                                    if(!empty($candidates)){
                                        usort($candidates, static function($a, $b){
                                            return filemtime($b) <=> filemtime($a);
                                        });
                                        $log_file_path = $candidates[0];
                                    }
                                }
                            }
                        }

                        if($log_file_path && file_exists($log_file_path)){
                            $log_contents = $read_tail($log_file_path);
                        }
                    }elseif($log_method === 'custom'){
                        $log_path = trailingslashit(ABSPATH) . 'acp_feed.log';
                        $log_contents = $read_tail($log_path);
                    }else{
                        echo '<p>' . esc_html__("The current logging method does not store log files to display.", "acp-feed-woocommerce") . '</p>';
                        $log_contents = '';
                    }

                    if(empty($log_contents) && $log_method !== 'nolog'){
                        echo '<p>' . esc_html__("No log entries found for the current configuration.", "acp-feed-woocommerce") . '</p>';
                    }elseif(!empty($log_contents)){
                        echo '<p>' . esc_html__("Showing the most recent log entries:", "acp-feed-woocommerce") . '</p>';
                        echo '<textarea readonly rows="15" style="width:100%;font-family:monospace;">' . esc_textarea($log_contents) . '</textarea>';
                    }
                }
                ?>

            </div>
        </div>

        <?php
    }

    /**
     * Render the status box showing current and last successful feed build info.
     *
     * @since 1.0.0
     */
    private function acp_render_status_box(){
        $current_state  = get_option(ACP_Feed::STATE_OPTION);
        $last_successful_state  = get_option(ACP_Feed::LAST_SUCCESSFUL_OPTION);
        $upload_dir = wp_get_upload_dir();
        $file_path  = trailingslashit($upload_dir['basedir']) . ACP_Feed::FEED_FILE;
        $file_size   = file_exists($file_path) ? size_format(filesize($file_path)) : __("file is missing", "acp-feed-woocommerce");
        $file_url    = trailingslashit($upload_dir['baseurl']) . ACP_Feed::FEED_FILE;
        ?>

        <table class="widefat acp-main-table">
            <thead>
                <tr>
                    <th><?php esc_html_e("Feed info", "acp-feed-woocommerce"); ?></th>
                    <th><?php esc_html_e("Value", "acp-feed-woocommerce"); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2"><strong><?php esc_html_e("Current build state", "acp-feed-woocommerce"); ?></strong></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Build id", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($current_state) ? esc_html($current_state['build_id']) : "No build is being processed"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Created at", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($current_state) ? esc_html(date('Y-m-d H:i:s', $current_state['created_at'])) : "No build is being processed"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Current page", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($current_state) ? esc_html(($current_state['page'])) : "No build is being processed"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Products to process", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($current_state) ? esc_html($current_state['page_size']) : "No build is being processed"; ?></code></td>
                </tr>
                <tr>
                    <td colspan="2"><strong><?php esc_html_e("Last successful build", "acp-feed-woocommerce"); ?></strong></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Build id", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($last_successful_state) ? esc_html($last_successful_state['build_id']) : "No successful build yet"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Created at", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($last_successful_state) ? esc_html(date('Y-m-d H:i:s', $last_successful_state['created_at'])) : "No successful build yet"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Completed at", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($last_successful_state) ? esc_html(date('Y-m-d H:i:s', $last_successful_state['finished_at'])) : "No successful build yet"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("File path", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($last_successful_state) ? esc_html($file_path) : "No successful build yet"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Public URL", "acp-feed-woocommerce"); ?></td>
                    <td>
                        <code>
                            <?php if(!empty($last_successful_state)){ ?>
                                <a href="<?php echo esc_url($file_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($file_url); ?></a>
                            <?php } else {
                                echo esc_html("No successful build yet");
                            } ?>
                        </code>
                    </td>
                </tr>
                <tr>
                    <td><?php esc_html_e("File size", "acp-feed-woocommerce"); ?></td>
                    <td><code><?php echo !empty($last_successful_state) ? esc_html($file_size) : "No successful build yet"; ?></code></td>
                </tr>
                <tr>
                    <td><?php esc_html_e("Report", "acp-feed-woocommerce"); ?></td>
                    <td>
                        <?php 
                        if(!empty($last_successful_state['build_id'])){
                            $all_reports_for_build = ACP_Feed_DBHelper::acp_get_reports($last_successful_state['build_id']);

                            if(!empty($all_reports_for_build)){
                                $statuses = [
                                    'emitted' => [
                                        'label' => __("Emitted", "acp-feed-woocommerce"),
                                        'reports' => [],
                                    ],
                                    'warning' => [
                                        'label' => __("Warnings", "acp-feed-woocommerce"),
                                        'reports' => [],
                                    ],
                                    'failed' => [
                                        'label' => __("Failed", "acp-feed-woocommerce"),
                                        'reports' => [],
                                    ],
                                ];

                                foreach($all_reports_for_build as $report){
                                    $status_key = strtolower($report['status'] ?? '');
                                    if(!isset($statuses[$status_key])){
                                        $status_key = 'warning';
                                    }
                                    $statuses[$status_key]['reports'][] = $report;
                                }

                                $default_tab = 'failed';

                                echo '<div class="acp-report-tabs">';

                                echo '<div class="acp-report-tabs__nav">';
                                foreach($statuses as $status => $status_meta){
                                    $classes = 'acp-report-tabs__nav-btn acp-report-tabs__nav-btn--' . $status;
                                    if($status === $default_tab){
                                        $classes .= ' is-active';
                                    }
                                    echo '<button type="button" class="' . esc_attr($classes) . '" data-tab="' . esc_attr($status) . '">';
                                    echo esc_html($status_meta['label']);
                                    echo ' <span class="acp-report-tabs__count">' . esc_html(count($status_meta['reports'])) . '</span>';
                                    echo '</button>';
                                }
                                echo '</div>';

                                foreach($statuses as $status => $status_meta){
                                    $panel_classes = 'acp-report-tabs__panel acp-report-tabs__panel--' . $status;
                                    if($status === $default_tab){
                                        $panel_classes .= ' is-active';
                                    }
                                    echo '<div class="' . esc_attr($panel_classes) . '" data-tab="' . esc_attr($status) . '">';

                                    if(!empty($status_meta['reports'])){
                                        echo '<table class="widefat fixed striped acp-report-tabs__table">';
                                        echo '<thead><th>' . esc_html__("Product ID", "acp-feed-woocommerce") . '</th><th class="acp-report-tabs__status">' . esc_html__("Status", "acp-feed-woocommerce") . '</th><th>' . esc_html__("Info", "acp-feed-woocommerce") . '</th></tr></thead>';
                                        echo '<tbody>';

                                        foreach($status_meta['reports'] as $report){
                                            $row_class = 'acp-report-tabs__row acp-report-tabs__row--' . $status;
                                            $product = wc_get_product($report['product_id']);
                                            $product_name = "";

                                            if($product instanceof WC_Product)
                                                $product_name = $product->get_name();

                                            echo '<tr class="' . esc_attr($row_class) . '">';
                                            echo '<td>' . (!empty($report['product_id']) ? esc_html($report['product_id']) . ' (' . $product_name . ')' : '-') . '</td>';
                                            echo '<td class="acp-report-tabs__status acp-report-tabs__status--' . esc_attr($status) . '">' . esc_html($status_meta['label']) . '</td>';
                                            echo '<td>' . esc_html($report['info']) . '</td>';
                                            echo '</tr>';
                                        }

                                        echo '</tbody>';
                                        echo '</table>';
                                    }else{
                                        echo '<p class="acp-report-tabs__empty">' . esc_html__("No entries for this status.", "acp-feed-woocommerce") . '</p>';
                                    }

                                    echo '</div>';
                                }

                                echo '</div>';
                            }else{
                                echo "<code>" . esc_html__("No report entries for the last successful build.", "acp-feed-woocommerce") . "</code>";
                            }
                        }else{
                            echo "<code>" . esc_html__("No successful build yet", "acp-feed-woocommerce") . "</code>";
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php
    }

    /**
     * Format a description for a settings field.
     *
     * @param string $summary - A brief summary of the field.
     * @param string $requirement - The requirement information.
     * @param string $validation - The validation rules.
     * @param string $dependencies - The dependencies information.
     * @return string - The formatted description.
     * @since 1.0.0
     */
    private function acp_format_desc($summary, $requirement, $validation = '', $dependencies = ''){
        $parts = array();

        if($summary !== ''){
            $parts[] = $summary;
        }

        if($requirement !== ''){
            $parts[] = sprintf(
                "<b>%s:</b> %s",
                __("Requirement", "acp-feed-woocommerce"),
                $requirement
            );
        }

        if($dependencies !== ''){
            $parts[] = sprintf(
                "<b>%s:</b> %s",
                __("Dependencies", "acp-feed-woocommerce"),
                $dependencies
            );
        }

        if($validation !== ''){
            $parts[] = sprintf(
                "<b>%s:</b> %s",
                __("Validation rules", "acp-feed-woocommerce"),
                $validation
            );
        }

        return implode('<br>', $parts);
    }

    /**
     * Get allowed HTML tags for descriptions.
     *
     * @return array - The allowed HTML tags.
     * @since 1.0.0
     */
    private function acp_get_allowed_desc_tags(){
        return array(
            'br' => array(),
            'b' => array(),
        );
    }

    /*--------------------------------------------------*/
    /*                                                  */
    /*                      Fields                      */
    /*                                                  */
    /*--------------------------------------------------*/

    /**
     * Generate custom settings field: Checkbox
     *
     * @param array $args - The field arguments in associative array [key, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_checkbox($args){
        $key = $args['key'];
        $desc = $args['desc'] ?? '';
        ?>
        <input type="checkbox" name="<?php echo esc_attr(self::ACP_OPTIONS_KEY); ?>[<?php echo esc_attr($key); ?>]" <?php checked($this->acp_settings[$key], 'on'); ?>>
        <?php if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Generate custom settings field: Checkbox Select
     *
     * @param array $args - The field arguments in associative array [key, options, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_checkbox_select($args){
        $key = $args['key'];
        $options = $args['options'] ?? [];
        $desc = $args['desc'] ?? '';
        $selected = (array)$this->acp_settings[$key];
        
        foreach((array)$options as $val){
            $label = ucwords(str_replace(['-', '_'], ' ', $val));
            ?>
            <label style="display: block; margin-bottom: 4px;">
                <input type="checkbox" name="<?php echo esc_attr(self::ACP_OPTIONS_KEY); ?>[<?php echo esc_attr($key); ?>][]" value="<?php echo esc_attr($val); ?>" <?php checked(in_array($val, $selected, true)); ?>>
                <?php echo esc_html($label); ?>
            </label>
            <?php
        }
        if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif;

    }

    /**
     * Generate custom settings field: Select
     *
     * @param array $args - The field arguments in associative array [key, custom_key, options, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_select($args){
        $key = $args['key'];
        $custom_key = $args['custom_key'] ?? '';
        $options = $args['options'] ?? [];
        $desc = $args['desc'] ?? '';

        $name = self::ACP_OPTIONS_KEY . '[' . $key . ']';
        $current = $this->acp_settings[$key] ?? array_key_first($options);
        $custom_value = $this->acp_settings[$custom_key] ?? '';

        $id_base = 'acp-csel-' . sanitize_key($key) . '-' . wp_generate_password(6, false, false);
        ?>

        <input type="hidden"
            id="<?php echo esc_attr($id_base); ?>-hidden"
            name="<?php echo esc_attr($name); ?>"
            value="<?php echo esc_attr((string)$current); ?>">

        <div id="<?php echo esc_attr($id_base); ?>" class="acp-csel">
            <?php foreach ($options as $val => $data): 
                $label = $data['label'] ?? (string)$val;
                $description  = $data['desc']  ?? '';
                $selected = ((string)$val === (string)$current);
                $opt_id = $id_base . '-option-' . sanitize_key((string)$val);
            ?>
                <div class="acp-csel-option <?php echo $selected ? 'is-selected' : ''; ?>" id="<?php echo esc_attr($opt_id); ?>" data-value="<?php echo esc_attr((string)$val); ?>">
                    <span class="acp-csel-label"><?php echo esc_html($label); ?></span>
                    <?php if ($description !== ''): ?>
                        <span class="acp-help" acp-title="<?php echo esc_attr(wp_strip_all_tags($description)); ?>">?</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(!empty($custom_key)): ?>
            <input type="text" class="acp-csel-custom acp-hidden" id="<?php echo esc_attr($id_base); ?>-custom" name="<?php echo esc_attr(self::ACP_OPTIONS_KEY); ?>[<?php echo esc_attr($custom_key); ?>]" value="<?php echo esc_attr($custom_value); ?>" placeholder="<?php echo esc_attr__("Enter custom key", "acp-feed-woocommerce"); ?>">
        <?php endif; ?>

        <?php if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif; ?>

        <?php

    }

    /**
     * Generate custom settings field: Number
     *
     * @param array $args - The field arguments in associative array [key, min, step, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_number($args){
        $key = $args['key'];
        $min = $args['min'] ?? 1;
        $step = $args['step'] ?? 1;
        $desc = $args['desc'] ?? '';
        ?>
        <input type="number" min="<?php echo esc_attr($min); ?>" step="<?php echo esc_attr($step); ?>" name="<?php echo esc_attr(self::ACP_OPTIONS_KEY); ?>[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr((int)$this->acp_settings[$key]); ?>">
        <?php if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Generate custom settings field: Text
     *
     * @param array $args - The field arguments in associative array [key, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_text($args){
        $key = $args['key'];
        $desc = $args['desc'] ?? '';
        ?>
        <input type="text" class="regular-text" name="<?php echo esc_attr(self::ACP_OPTIONS_KEY); ?>[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($this->acp_settings[$key]); ?>">
        <?php if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Generate custom settings field: Warning
     *
     * @param array $args - The field arguments in associative array [label, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_warning($args){
        $label = $args['label'];
        $desc = $args['desc'] ?? '';
        ?>
        <strong style="color: #d63638;"><?php echo esc_html($label); ?></strong>
        <?php if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Generate custom settings field: Readonly
     *
     * @param array $args - The field arguments in associative array [key, desc].
     * @since 1.0.0
     */
    public function acp_settings_field_readonly($args){
        $key = $args['key'];
        $desc = $args['desc'] ?? '';
        $value = $this->acp_settings[$key];

        if($key === 'server_cron_key'){
            $value = add_query_arg([
                'action' => ACP_Feed_Scheduler::ACTION_BUILD_CRON,
                'key'    => rawurlencode($this->acp_settings[$key]),
            ], admin_url('admin-post.php'));
        }
        ?>
        
        <input type="text" readonly class="regular-text" value="<?php echo esc_attr($value); ?>">
        <?php if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif; ?>
        <?php
    }

    /**
     * Generate custom settings field: Description
     *
     * @param array $args - The field arguments in associative array [desc].
     * @since 1.0.0
     */
    public function acp_settings_field_description($args){
        $desc = $args['desc'] ?? '';
        if (!empty($desc)): ?>
            <p class="description"><?php echo wp_kses($desc, $this->acp_get_allowed_desc_tags()); ?></p>
        <?php endif;
    }

}
