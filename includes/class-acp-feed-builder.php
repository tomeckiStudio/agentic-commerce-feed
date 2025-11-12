<?php
defined('ABSPATH') or die('Suspicious activities detected!');

/**
 * Feed Builder
 * 
 * @author tomeckiStudio
 * @version 1.0.0
 */
final class ACP_Feed_Builder {
    
    /**
     * Emit product data into XML
     * 
     * @param XMLWriter $xml_writer - XML Writer instance
     * @param string $build_id - Build identifier
     * @param WC_Product $product - Product instance
     * @return bool True on success, false on failure
     * @since 1.0.0
     */
    public static function emit_product($xml_writer, $build_id, $product){
        global $acp_class_settings;

        try{
            // ACP: enable_search
            $acp_enable_search = self::acp_get_enable_search($product);

            if(gettype($acp_enable_search) === 'boolean'){
                $acp_enable_search = $acp_enable_search ? 'true' : 'false';
            }else{
                if(!in_array($acp_enable_search, ['true', 'false'])){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s value.", "acp-feed-woocommerce"), "ACP: enable_search") . " "
                            . sprintf(__("Expected 'true' or 'false', got: '%s'", "acp-feed-woocommerce"), json_encode($acp_enable_search))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: enable_search: "
                        . "expected 'true' or 'false', got: '" . json_encode($acp_enable_search) . "'"
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );
                    
                    return false;
                }
            }

            // ACP: enable_checkout
            $acp_enable_checkout = self::acp_get_enable_checkout($product);

            if(gettype($acp_enable_checkout) === 'boolean'){
                $acp_enable_checkout = $acp_enable_checkout ? 'true' : 'false';
            }else{
                if(!in_array($acp_enable_checkout, ['true', 'false'])){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s value.", "acp-feed-woocommerce"), "ACP: enable_checkout") . " "
                            . sprintf(__("Expected 'true' or 'false', got: '%s'", "acp-feed-woocommerce"), json_encode($acp_enable_checkout))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: enable_checkout: "
                        . "expected 'true' or 'false', got: '" . json_encode($acp_enable_checkout) . "' "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            if($acp_enable_checkout === 'true'){
                if($acp_enable_search === 'false'){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("ACP: enable_checkout can't be true when ACP: enable_search is false", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: enable_checkout: "
                        . "enable_checkout can't be true when enable_search is false "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }
            
            // ACP: id
            $acp_id = self::acp_get_id($product);

            if(empty($acp_id)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("ACP: id can't be empty", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: id " . json_encode($acp_id) . " "
                    . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_id = strval($acp_id); 

            // id can't be trimmed - must be exactly as is
            if(strlen($acp_id) > 100){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("ACP: id can't be longer than 100 characters, can't be trimmed", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with too long ACP: id: " . json_encode($acp_id) . " "
                    . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            if(!preg_match('/^[a-zA-Z0-9]+$/', $acp_id)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Invalid ACP: id (not alphanumeric)", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: id (not alphanumeric): " . json_encode($acp_id) . " "
                    . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }
            

            // ACP: gtin
            $acp_gtin = self::acp_get_gtin($product);
            
            if(!empty($acp_gtin)){
                $acp_gtin = strval($acp_gtin);

                // gtin can't be trimmed - must be exactly as is
                if((strlen($acp_gtin) < 8 || strlen($acp_gtin) > 14)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: gtin (length not between 8 and 14)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: gtin (length not between 8 and 14): " . json_encode($acp_gtin) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );
                    
                    return false;
                }

                if(!preg_match('/^\d+$/', $acp_gtin)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: gtin (not numeric)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: gtin (not numeric): " . json_encode($acp_gtin) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning', 
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: gtin")
                );
            }
            
            // ACP: mpn
            $acp_mpn = self::acp_get_mpn($product);

            if(empty($acp_mpn)){
                if(empty($acp_gtin)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("ACP: mpn is required if ACP: gtin is empty.", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product: ACP: mpn is required if ACP: gtin is empty. "
                        . "ACP: mpn: " . json_encode($acp_mpn) . ", ACP: gtin: " . json_encode($acp_gtin) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                $acp_mpn = strval($acp_mpn);

                // mpn can't be trimmed - must be exactly as is
                if(strlen($acp_mpn) > 70){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: mpn", 70)
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with too long ACP: mpn (>70): " . json_encode($acp_mpn) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                if(!preg_match('/^[a-zA-Z0-9]+$/', $acp_mpn)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: mpn (not alphanumeric)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: mpn (not alphanumeric): " . json_encode($acp_mpn) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: title
            $acp_title = self::acp_get_title($product);

            if(empty($acp_title)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: title")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: title: " . json_encode($acp_title) . " "
                    . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_title = strval($acp_title);

            if(strlen($acp_title) > 150){
                if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                    $acp_title = substr($acp_title, 0, 147) . '...';
                    
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning', 
                        sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: title", 150)
                    );
                    acp_log('ACP_Feed_Builder -> emit_product() -> '
                        . 'Product with too long Title (>150), trimmed to the correct length: ' . json_encode($acp_title) . ' '
                        . 'for product with ID: ' . $product->get_id() . ' (' . $product->get_name() . ')');
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: title", 150)
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with too long ACP: title (>150): " . json_encode($acp_title) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: description
            $acp_description = self::acp_get_description($product);

            if(empty($acp_description)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: description")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: description: " . json_encode($acp_description) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_description = strval($acp_description);

            if(strlen($acp_description) > 5000){
                if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                    $acp_description = substr((string)$acp_description, 0, 4997) . '...';

                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning', 
                        sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: description", 5000)
                    );
                    acp_log('ACP_Feed_Builder -> emit_product() -> '
                        .'Product with too long Description (>5000), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                        . "for product with ID: " . $product->get_id() . ' (' . $product->get_name() . ')');
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP description", 5000)
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with too long ACP: description (>5000): " . json_encode($acp_description) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: link
            $acp_link = self::acp_get_link($product);

            if(empty($acp_link)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: link")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: link: " . json_encode($acp_link) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_link = strval($acp_link);

            // ACP: condition
            $acp_condition = self::acp_get_condition($product);

            if(empty($acp_condition)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: condition")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: condition: " . json_encode($acp_condition) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_condition = strtolower(strval($acp_condition));

            if(!in_array($acp_condition, ['new', 'refurbished', 'used'])){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Invalid ACP: condition (not one of: 'new', 'refurbished', 'used')", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: condition (not one of: 'new', 'refurbished', 'used'): " . json_encode($acp_condition) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: product_category
            $acp_product_category = self::acp_get_category_path($product);

            if(empty($acp_product_category)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: product_category")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: product_category: " . json_encode($acp_product_category) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }
            
            $acp_product_category = strval($acp_product_category);

            // ACP: brand
            $acp_brand = self::acp_get_brand($product);

            if(empty($acp_brand)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: brand")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: brand: " . json_encode($acp_brand) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_brand = strval($acp_brand);

            if(strlen($acp_brand) > 70){
                if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                    $acp_brand = substr((string)$acp_brand, 0, 67) . '...';

                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning', 
                        sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: brand", 70)
                    );
                    acp_log('ACP_Feed_Builder -> emit_product() -> '
                        .'Product with too long Brand (>70), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                        . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: brand", 70)
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with too long ACP: brand (>70): " . json_encode($acp_brand) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: material
            $acp_material = self::acp_get_material($product);

            if(empty($acp_material)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: material")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: material: " . json_encode($acp_material) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_material = strval($acp_material);

            if(strlen($acp_material) > 100){
                if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                    $acp_material = substr((string)$acp_material, 0, 97) . '...';

                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning', 
                        sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: material", 100)
                    );
                    acp_log('ACP_Feed_Builder -> emit_product() -> '
                        .'Product with too long ACP material (>100), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                        . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: material", 100)
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with too long ACP: material (>100): " . json_encode($acp_material) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: dimensions
            $acp_dimensions = self::acp_get_dimensions($product);

            if(!empty($acp_dimensions)){
                $acp_dimensions = strval($acp_dimensions);

                if(!self::acp_is_dimensions_valid($acp_dimensions)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: dimensions") . " "
                            . sprintf(__("Expected 'LxWxH unit', e.g. 10x20x30 cm, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_dimensions))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: dimensions (not in format LxWxH unit, e.g. 10x20x30 cm): " . json_encode($acp_dimensions) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: length
            $acp_length = self::acp_get_length($product);

            if(!empty($acp_length)){
                $acp_length = strval($acp_length);

                if(!self::acp_is_number_unit_valid($acp_length)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: length") . " "
                            . sprintf(__("Expected 'number unit', e.g. 10 cm, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_length))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: length (not in format number unit, e.g. 10 cm): " . json_encode($acp_length) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: width
            $acp_width = self::acp_get_width($product);

            if(!empty($acp_width)){
                $acp_width = strval($acp_width);

                if(!self::acp_is_number_unit_valid($acp_width)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: width") . " "
                            . sprintf(__("Expected 'number unit', e.g. 10 cm, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_width))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: width (not in format number unit, e.g. 10 cm): " . json_encode($acp_width) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: height
            $acp_height = self::acp_get_height($product);

            if(!empty($acp_height)){
                $acp_height = strval($acp_height);

                if(!self::acp_is_number_unit_valid($acp_height)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: height") . " "
                            . sprintf(__("Expected 'number unit', e.g. 10 cm, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_height))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: height (not in format number unit, e.g. 10 cm): " . json_encode($acp_height) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // All three dimensions (length, width, height) must be set or none
            if((!empty($acp_length) || !empty($acp_width) || !empty($acp_height)) && (empty($acp_length) || empty($acp_width) || empty($acp_height))){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Incomplete ACP: dimensions (if any of length, width, height is set, then all must be set)", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with incomplete ACP: dimensions (if any of length, width, height is set, then all must be set): "
                    . "length: " . json_encode($acp_length) . ", width: " . json_encode($acp_width) . ", height: " . json_encode($acp_height) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: weight
            $acp_weight = self::acp_get_weight($product);

            if(empty($acp_weight)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: weight")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: weight: " . json_encode($acp_weight) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_weight = strval($acp_weight);

            if(!self::acp_is_number_unit_valid($acp_weight)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: weight") . " "
                        . sprintf(__("Expected 'number unit', e.g. 10 kg, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_weight))
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: weight (not in format number unit, e.g. 10 kg): " . json_encode($acp_weight) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: age_group
            $acp_age_group = self::acp_get_age_group($product);

            if(!empty($acp_age_group)){
                $acp_age_group = strtolower(strval($acp_age_group));

                if(!in_array($acp_age_group, ['newborn', 'infant', 'toddler', 'kids', 'adult'])){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect value", "acp-feed-woocommerce"), "ACP: age_group") . " "
                            . sprintf(__("Expected one of 'newborn', 'infant', 'toddler', 'kids', 'adult', got: '%s'", "acp-feed-woocommerce"), json_encode($acp_age_group))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: age_group (not one of: 'newborn', 'infant', 'toddler', 'kids', 'adult'): " . json_encode($acp_age_group) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: image_link
            $acp_image_link = self::acp_get_main_image($product);

            if(empty($acp_image_link)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: image_link")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: image_link: " . json_encode($acp_image_link) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_image_link = strval($acp_image_link);

            if(!preg_match('/\.(jpg|jpeg|png)$/i', $acp_image_link)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Invalid ACP: image_link (not a valid image: jpeg/png)", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: image_link (not a valid image: jpeg/png): " . json_encode($acp_image_link) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: additional_image_link
            $acp_additional_images = self::acp_get_additional_images($product);

            if(!empty($acp_additional_images)){
                $acp_additional_images_list = array_filter(
                    array_map('trim', explode(',', $acp_additional_images)),
                    static function ($value){
                        return $value !== '';
                    }
                );
                $valid_additional_images = [];

                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Found " . count($acp_additional_images_list) . " ACP: additional_image_link entries "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                foreach($acp_additional_images_list as $acp_additional_image){
                    $acp_additional_image = strval($acp_additional_image);

                    if(preg_match('/\.(jpg|jpeg|png)$/i', $acp_additional_image)){
                        $valid_additional_images[] = $acp_additional_image;
                        continue;
                    }

                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(),
                        'warning',
                        __("Invalid ACP: additional_image_link (not a valid image: jpeg/png)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping invalid ACP: additional_image_link (not a valid image: jpeg/png): "
                            . json_encode($acp_additional_image) . " for product with ID: "
                            . $product->get_id() . " (" . $product->get_name() . ")"
                    );
                }

                if(!empty($valid_additional_images)){
                    $acp_additional_images = implode(',', $valid_additional_images);
                }else if(!empty($acp_additional_images_list)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(),
                        'warning',
                        __("No valid ACP: additional_image_links (not a valid image: jpeg/png)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "No valid ACP: additional_image_links (not a valid image: jpeg/png) for product with ID: "
                            . $product->get_id() . " (" . $product->get_name() . ")"
                    );
                    $acp_additional_images = "";
                }else{
                    $acp_additional_images = "";
                }
            }
            
            // ACP: video_link
            $acp_video_link = self::acp_get_video_link($product);

            if(!empty($acp_video_link)){
                $acp_video_link = strval($acp_video_link);

                if(!filter_var($acp_video_link, FILTER_VALIDATE_URL)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: video_link (not a valid URL)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: video_link (not a valid URL): " . json_encode($acp_video_link) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: model_3d_link
            $acp_model_3d_link = self::acp_get_model_3d_link($product);

            if(!empty($acp_model_3d_link)){
                $acp_model_3d_link = strval($acp_model_3d_link);

                if(!filter_var($acp_model_3d_link, FILTER_VALIDATE_URL)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: model_3d_link (not a valid URL)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: model_3d_link (not a valid URL): " . json_encode($acp_model_3d_link) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
                
                $model_path = parse_url($acp_model_3d_link, PHP_URL_PATH);
                $model_extension = strtolower(pathinfo($model_path ?? '', PATHINFO_EXTENSION));

                if($model_extension !== 'glb' && $model_extension !== 'gltf'){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning', 
                    __("ACP: model_3d_link should be in preferred format (GLB/GLTF)", "acp-feed-woocommerce")
                );
                }
            }

            // ACP: price
            $acp_price = self::acp_get_price($product);

            if(empty($acp_price)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: price")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: price: " . json_encode($acp_price) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_price = strval($acp_price);

            if(!preg_match('/^\d+(\.\d+)?\s*[A-Z]{3}$/', $acp_price)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: price") . " "
                        . sprintf(__("Expected 'number currency', e.g. 10.99 USD, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_price))
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: price (not in format number currency, e.g. 10.99 USD): " . json_encode($acp_price) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: applicable_taxes_fees
            $acp_applicable_taxes_fees = self::acp_get_applicable_taxes_fees($product);

            if(!empty($acp_applicable_taxes_fees)){
                $acp_applicable_taxes_fees = strval($acp_applicable_taxes_fees);

                if(!preg_match('/^\d+(\.\d+)?\s*[A-Z]{3}$/', $acp_applicable_taxes_fees)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: applicable_taxes_fees") . " "
                            . sprintf(__("Expected 'number currency', e.g. 10.99 USD, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_applicable_taxes_fees))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: applicable_taxes_fees (not in format number currency, e.g. 10.99 USD): " . json_encode($acp_applicable_taxes_fees) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            } 

            // ACP: sale_price
            $acp_sale_price = self::acp_get_sale_price($product);

            if(!empty($acp_sale_price)){
                $acp_sale_price = strval($acp_sale_price);

                if(!preg_match('/^\d+(\.\d+)?\s*[A-Z]{3}$/', $acp_sale_price)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed',
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: sale_price") . " "
                            . sprintf(__("Expected 'number currency', e.g. 10.99 USD, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_sale_price))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: sale_price (not in format number currency, e.g. 10.99 USD): " . json_encode($acp_sale_price) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                // Sale price must be less than regular price
                $regular_price_value = (float)explode(' ', $acp_price)[0];
                $sale_price_value = (float)explode(' ', $acp_sale_price)[0];

                if($sale_price_value >= $regular_price_value){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: sale_price (not less than regular price)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: sale_price (not less than regular price): " . json_encode($acp_sale_price) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: sale_price_effective_date
            $acp_sale_price_effective_date = self::acp_get_sale_price_effective_date($product);

            if(!empty($acp_sale_price_effective_date)){
                $acp_sale_price_effective_date = strval($acp_sale_price_effective_date);

                if(!preg_match('/^\d{4}-\d{2}-\d{2}\s*\/\s*\d{4}-\d{2}-\d{2}$/', $acp_sale_price_effective_date)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: sale_price_effective_date") . " "
                            . sprintf(__("Expected 'YYYY-MM-DD / YYYY-MM-DD', e.g. 2055-05-04 / 2056-07-06, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_sale_price_effective_date))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: sale_price_effective_date (not in format YYYY-MM-DD / YYYY-MM-DD): " . json_encode($acp_sale_price_effective_date) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            if(!empty($acp_sale_price)){
                if(empty($acp_sale_price_effective_date)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: sale_price_effective_date while ACP: sale_price is set")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with empty ACP: sale_price_effective_date while ACP: sale_price is set: " . json_encode($acp_sale_price_effective_date) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: unit_pricing_measure
            $acp_unit_pricing_measure = self::acp_get_unit_pricing_measure($product);

            if(!empty($acp_unit_pricing_measure)){
                $acp_unit_pricing_measure = strval($acp_unit_pricing_measure);

                if(!self::acp_is_number_unit_valid($acp_unit_pricing_measure)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: unit_pricing_measure") . " "
                            . sprintf(__("Expected 'number unit', e.g. 10 kg, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_unit_pricing_measure))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: unit_pricing_measure (not in format number unit, e.g. 10 kg): " . json_encode($acp_unit_pricing_measure) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: base_measure
            $acp_base_measure = self::acp_get_base_measure($product);

            if(!empty($acp_base_measure)){
                $acp_base_measure = strval($acp_base_measure);

                if(!self::acp_is_number_unit_valid($acp_base_measure)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: base_measure") . " "
                            . sprintf(__("Expected 'number unit', e.g. 1 kg, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_base_measure))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: base_measure (not in format number unit, e.g. 1 kg): " . json_encode($acp_base_measure) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // Both togher or none
            if((!empty($acp_unit_pricing_measure) || !empty($acp_base_measure)) && (empty($acp_unit_pricing_measure) || empty($acp_base_measure))){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Incomplete Measure (if any of ACP: unit_pricing_measure, ACP: base_measure is set, then both must be set)", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with incomplete Measure (if any of ACP: unit_pricing_measure, ACP: base_measure is set, then both must be set): "
                    . "unit_pricing_measure: " . json_encode($acp_unit_pricing_measure) . ", base_measure: " . json_encode($acp_base_measure) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: pricing_trend
            $acp_pricing_trend = self::acp_get_pricing_trend($product);

            if(!empty($acp_pricing_trend)){
                $acp_pricing_trend = strval($acp_pricing_trend);

                if(strlen($acp_pricing_trend) > 80){
                    if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                        $acp_pricing_trend = substr((string)$acp_pricing_trend, 0, 147) . '...';

                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'warning', 
                            sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: pricing_trend", 150)
                        );
                        acp_log('ACP_Feed_Builder -> emit_product() -> '
                            .'Product with too long ACP pricing_trend (>150), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                            . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                    }else{
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: pricing_trend", 150)
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with too long ACP: pricing_trend (>150): " . json_encode($acp_pricing_trend) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }

            // ACP: availability
            $acp_availability = self::acp_get_availability($product);

            $acp_availability = strtolower(strval($acp_availability));

            if(!in_array($acp_availability, ['in_stock', 'out_of_stock', 'preorder'])){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Invalid %s, incorrect value", "acp-feed-woocommerce"), "ACP: availability") . " "
                        . sprintf(__("Expected 'in_stock', 'out_of_stock' or 'preorder', got: '%s'", "acp-feed-woocommerce"), json_encode($acp_availability))
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: availability (not one of: 'in_stock', 'out_of_stock' or 'preorder'): " . json_encode($acp_availability) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: availability_date
            $acp_availability_date = self::acp_get_availability_date($product);

            if($acp_availability === 'preorder' && empty($acp_availability_date)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: availability_date while ACP: availability is preorder")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: availability_date while ACP: availability is preorder: " . json_encode($acp_availability_date) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            if(!empty($acp_availability_date)){
                $acp_availability_date = strval($acp_availability_date);

                if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $acp_availability_date)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: availability_date") . " "
                            . sprintf(__("Expected 'YYYY-MM-DD', e.g. 2055-05-06, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_availability_date))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: availability_date (not in format YYYY-MM-DD): " . json_encode($acp_availability_date) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                // Check if future date
                $date_now = new DateTime();
                $date_availability = DateTime::createFromFormat('Y-m-d', $acp_availability_date);
                if($date_availability <= $date_now){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a future date)", "acp-feed-woocommerce"), "ACP: availability_date")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: availability_date (not a future date): " . json_encode($acp_availability_date) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: inventory_quantity
            $acp_inventory_quantity = self::acp_get_inventory_quantity($product);
            
            if(is_int($acp_inventory_quantity) || is_numeric($acp_inventory_quantity)){
                $acp_inventory_quantity_qty = intval($acp_inventory_quantity);
                
                if(intval($acp_inventory_quantity_qty) < 0){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: inventory_quantity")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: inventory_quantity (not a non-negative integer): " . json_encode($acp_inventory_quantity) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: inventory_quantity")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: inventory_quantity (not a non-negative integer): " . json_encode($acp_inventory_quantity) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_inventory_quantity = strval($acp_inventory_quantity);

            // ACP: expiration_date
            $acp_expiration_date = self::acp_get_expiration_date($product);

            if(!empty($acp_expiration_date)){
                $acp_expiration_date = strval($acp_expiration_date);

                if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $acp_expiration_date)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed',
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: expiration_date") . " "
                            . sprintf(__("Expected 'YYYY-MM-DD', e.g. 2055-05-06, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_expiration_date))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: expiration_date (not in format YYYY-MM-DD): " . json_encode($acp_expiration_date) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                // Check if future date
                $date_now = new DateTime();
                $date_expiration = DateTime::createFromFormat('Y-m-d', $acp_expiration_date);
                if($date_expiration <= $date_now){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a future date)", "acp-feed-woocommerce"), "ACP: expiration_date")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: expiration_date (not a future date): " . json_encode($acp_expiration_date) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: pickup_method
            $acp_pickup_method = self::acp_get_pickup_method($product);

            if(!empty($acp_pickup_method)){
                $acp_pickup_method = strtolower(strval($acp_pickup_method));

                if(!in_array($acp_pickup_method, ['in_store', 'reserve', 'not_supported'])){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect value", "acp-feed-woocommerce"), "ACP: pickup_method") . " "
                            . sprintf(__("Expected 'in_store', 'reserve' or 'not_supported', got: '%s'", "acp-feed-woocommerce"), json_encode($acp_pickup_method))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: pickup_method (not one of: 'in_store', 'reserve' or 'not_supported'): " . json_encode($acp_pickup_method) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }   
            }

            // ACP: pickup_sla
            $acp_pickup_sla = self::acp_get_pickup_sla($product);

            if(!empty($acp_pickup_sla)){
                $acp_pickup_sla = strval($acp_pickup_sla);

                if(!self::acp_is_number_unit_valid($acp_pickup_sla)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: pickup_sla") . " "
                            . sprintf(__("Expected 'number unit', e.g. 10 days, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_pickup_sla))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: pickup_sla (not in format number unit, e.g. 10 days): " . json_encode($acp_pickup_sla) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                if(empty($acp_pickup_method)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("ACP: pickup_sla set while ACP: pickup_method is empty", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with ACP: pickup_sla set while ACP: pickup_method is empty: "
                        . "ACP: pickup_sla: " . json_encode($acp_pickup_sla) . ", ACP: pickup_method: " . json_encode($acp_pickup_method) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: shipping
            $acp_shipping = self::acp_get_shipping($product);

            if($acp_enable_checkout){
                if(empty($acp_shipping)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: shipping")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with empty ACP: shipping: " . json_encode($acp_shipping) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                $acp_shipping = strval($acp_shipping);

                // Check if shipping is in format: country:region:service:price;country:region:service:price;...
                $shipping_rules = array_filter(
                    array_map('trim', explode(';', $acp_shipping)),
                    static function ($value){
                        return $value !== '';
                    }
                );
                foreach($shipping_rules as $shipping_rule){
                    $shipping_parts = array_map('trim', explode(':', $shipping_rule));

                    if(count($shipping_parts) !== 4){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: shipping (not in format country:region:service:price)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: shipping (not in format country:region:service:price): " . json_encode($acp_shipping) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }

                    $price = $shipping_parts[3];

                    if(!preg_match('/^\d+(\.\d+)?\s*[A-Z]{3}$/', $price)){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid Shipping Price in ACP: shipping, incorrect format", "acp-feed-woocommerce") . " "
                                . sprintf(__("Expected 'number currency', e.g. 10.99 USD, got: '%s'", "acp-feed-woocommerce"), json_encode($price))
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid Shipping Price in ACP: shipping (not in format number currency, e.g. 10.99 USD): " . json_encode($acp_shipping) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }

            // ACP: delivery_estimate
            $acp_delivery_estimate = self::acp_get_delivery_estimate($product);

            if(!empty($acp_delivery_estimate)){
                $acp_delivery_estimate = strval($acp_delivery_estimate);

                 if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $acp_delivery_estimate)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: delivery_estimate") . " "
                            . sprintf(__("Expected 'YYYY-MM-DD', e.g. 2055-05-06, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_delivery_estimate))
                     );
                     acp_log("ACP_Feed_Builder -> emit_product() -> "
                         . "Skipping product with invalid ACP: delivery_estimate date (not in format YYYY-MM-DD): " . json_encode($acp_delivery_estimate) . " "
                             . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                     );

                     return false;
                }

                // Check if future date
                $date_now = new DateTime();
                $date_delivery = DateTime::createFromFormat('Y-m-d', $acp_delivery_estimate);
                if($date_delivery <= $date_now){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a future date)", "acp-feed-woocommerce"), "ACP: delivery_estimate date")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: delivery_estimate date (not a future date): " . json_encode($acp_delivery_estimate) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: seller_name
            $acp_seller_name = self::acp_get_seller_name($product);

            if(empty($acp_seller_name)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: seller_name")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: seller_name: " . json_encode($acp_seller_name) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_seller_name = strval($acp_seller_name);

            if(strlen($acp_seller_name) > 70){
                if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                    $acp_seller_name = substr((string)$acp_seller_name, 0, 67) . '...';

                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning', 
                        sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: seller_name", 70)
                    );
                    acp_log('ACP_Feed_Builder -> emit_product() -> '
                        .'Product with too long ACP seller_name (>70), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                        . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: seller_name", 70)
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with too long ACP: seller_name (>70): " . json_encode($acp_seller_name) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: seller_url
            $acp_seller_url = self::acp_get_seller_url($product);

            if(empty($acp_seller_url)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: seller_url")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: seller_url: " . json_encode($acp_seller_url) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_seller_url = strval($acp_seller_url);

            if(!filter_var($acp_seller_url, FILTER_VALIDATE_URL)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Invalid ACP: seller_url (not a valid URL)", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: seller_url (not a valid URL): " . json_encode($acp_seller_url) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: seller_privacy_policy
            $acp_seller_privacy_policy = self::acp_get_seller_privacy_policy($product);

            if(!empty($acp_seller_privacy_policy)){
                $acp_seller_privacy_policy = strval($acp_seller_privacy_policy);

                if(!filter_var($acp_seller_privacy_policy, FILTER_VALIDATE_URL)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: seller_privacy_policy URL (not a valid URL)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: seller_privacy_policy URL (not a valid URL): " . json_encode($acp_seller_privacy_policy) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            if($acp_enable_checkout === true){
                if(empty($acp_seller_privacy_policy)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: seller_privacy_policy URL while ACP: enable_checkout is true")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with empty ACP: seller_privacy_policy URL while ACP: enable_checkout is true: " . json_encode($acp_seller_privacy_policy) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: seller_tos
            $acp_seller_tos = self::acp_get_seller_tos($product);

            if(!empty($acp_seller_tos)){
                $acp_seller_tos = strval($acp_seller_tos);

                if(!filter_var($acp_seller_tos, FILTER_VALIDATE_URL)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: seller_tos URL (not a valid URL)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: seller_tos URL (not a valid URL): " . json_encode($acp_seller_tos) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            if($acp_enable_checkout === true){
                if(empty($acp_seller_tos)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: seller_tos URL while ACP: enable_checkout is true")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with empty ACP: seller_tos URL while ACP: enable_checkout is true: " . json_encode($acp_seller_tos) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: return_policy
            $acp_return_policy = self::acp_get_return_policy($product);
            
            if(empty($acp_return_policy)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Empty %s.", "acp-feed-woocommerce"), "ACP: return_policy URL")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with empty ACP: return_policy URL: " . json_encode($acp_return_policy) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            $acp_return_policy = strval($acp_return_policy);

            if(!filter_var($acp_return_policy, FILTER_VALIDATE_URL)){
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    __("Invalid ACP: return_policy URL (not a valid URL)", "acp-feed-woocommerce")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: return_policy URL (not a valid URL): " . json_encode($acp_return_policy) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );

                return false;
            }

            // ACP: return_window
            $acp_return_window = self::acp_get_return_window($product);

            if(is_int($acp_return_window) || is_numeric($acp_return_window)){
                $acp_return_window_int = intval($acp_return_window);

                if(intval($acp_return_window_int) < 0){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: return_window")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: return_window (not a non-negative integer): " . json_encode($acp_return_window) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'failed', 
                    sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: return_window")
                );
                acp_log("ACP_Feed_Builder -> emit_product() -> "
                    . "Skipping product with invalid ACP: return_window (not a non-negative integer): " . json_encode($acp_return_window) . " "
                        . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                );  

                return false;
            }

            // ACP: popularity_score
            $acp_popularity_score = self::acp_get_popularity_score($product);
            
            if($acp_popularity_score !== '' && $acp_popularity_score !== null){
                if(is_int($acp_popularity_score) || is_float($acp_popularity_score) || is_numeric($acp_popularity_score)){
                    $acp_popularity_score_int = floatval($acp_popularity_score);

                    if($acp_popularity_score_int < 0 || $acp_popularity_score_int > 5){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: popularity_score (not a number between 0 and 5)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: popularity_score (not a number between 0 and 5): " . json_encode($acp_popularity_score) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: popularity_score")
                );
            }

            // ACP: return_rate
            $acp_return_rate = self::acp_get_return_rate($product);

            if(!empty($acp_return_rate)){
                $acp_return_rate = strval($acp_return_rate);

                if(!preg_match('/^\d+(\.\d+)?\s*%$/', $acp_return_rate)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect format.", "acp-feed-woocommerce"), "ACP: return_rate") . " "
                            . sprintf(__("Expected 'number%', e.g. 5%, got: '%s'", "acp-feed-woocommerce"), json_encode($acp_return_rate))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: return_rate (not in format number%, e.g. 5%): " . json_encode($acp_return_rate) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                $return_rate_value = (float)str_replace('%', '', $acp_return_rate);
                if($return_rate_value < 0 || $return_rate_value > 100){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: return_rate (not between 0% and 100%)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: return_rate (not between 0% and 100%): " . json_encode($acp_return_rate) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: return_rate")
                );
            }

            // ACP: warning
            $acp_warning = self::acp_get_warning($product);

            if(!empty($acp_warning)){
                $acp_warning = strval($acp_warning);

                if(strlen($acp_warning) > 100){
                    if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                        $acp_warning = substr($acp_warning, 0, 97) . '...';

                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'warning', 
                            sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: warning", 100)
                        );
                        acp_log('ACP_Feed_Builder -> emit_product() -> '
                            .'Product with too long ACP warning (>100), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                            . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                    }else{
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: warning", 100)
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with too long ACP: warning (>100): " . json_encode($acp_warning) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }else{
                if($acp_enable_checkout){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning',
                        __("ACP: warning is recommended to be set while ACP: enable_checkout is true", "acp-feed-woocommerce")
                    );
                }
            }

            // ACP: warning_url
            $acp_warning_url = self::acp_get_warning_url($product);

            if(!empty($acp_warning_url)){
                $acp_Warning_url = strval($acp_warning_url);

                if(!filter_var($acp_warning_url, FILTER_VALIDATE_URL)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: warning_url (not a valid URL)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: warning_url (not a valid URL): " . json_encode($acp_warning_url) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                if($acp_enable_checkout){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'warning',
                        __("ACP: warning_url is recommended to be set while ACP: enable_checkout is true", "acp-feed-woocommerce")
                    );
                }
            }

            // ACP: age_restriction
            $acp_age_restriction = self::acp_get_age_restriction($product);

            if($acp_age_restriction !== '' && $acp_age_restriction !== null){
                if(is_int($acp_age_restriction) || is_numeric($acp_age_restriction)){
                    $acp_age_restriction_int = intval($acp_age_restriction);

                    if($acp_age_restriction_int < 0){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: age_restriction")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: age_restriction (not a non-negative integer): " . json_encode($acp_age_restriction) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: age_restriction")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: age_restriction (not a non-negative integer): " . json_encode($acp_age_restriction) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );  
                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: age_restriction")
                );
            }

            // ACP: product_review_count
            $acp_product_review_count = self::acp_get_product_review_count($product);

            if($acp_product_review_count !== '' && $acp_product_review_count !== null){
                if(is_int($acp_product_review_count) || is_numeric($acp_product_review_count)){
                    $acp_product_review_count_int = intval($acp_product_review_count);

                    if($acp_product_review_count_int < 0){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: product_review_count")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: product_review_count (not a non-negative integer): " . json_encode($acp_product_review_count) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: product_review_count")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: product_review_count (not a non-negative integer): " . json_encode($acp_product_review_count) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: product_review_count")
                );
            }
            
            // ACP: product_review_rating
            $acp_product_review_rating = self::acp_get_product_review_rating($product);

            if($acp_product_review_rating !== '' && $acp_product_review_rating !== null){
                if(is_int($acp_product_review_rating) || is_float($acp_product_review_rating) || is_numeric($acp_product_review_rating)){
                    $acp_product_review_rating_float = floatval($acp_product_review_rating);

                    if($acp_product_review_rating_float < 0 || $acp_product_review_rating_float > 5){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: product_review_rating (not a number between 0 and 5)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: product_review_rating (not a number between 0 and 5): " . json_encode($acp_product_review_rating) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: product_review_rating (not a number between 0 and 5)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: product_review_rating (not a number between 0 and 5): " . json_encode($acp_product_review_rating) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: product_review_rating")
                );
            }

            // ACP: store_review_count
            $acp_store_review_count = self::acp_get_store_review_count($product);

            if($acp_store_review_count !== '' && $acp_store_review_count !== null){
                if(is_int($acp_store_review_count) || is_numeric($acp_store_review_count)){
                    $acp_store_review_count_int = intval($acp_store_review_count);

                    if($acp_store_review_count_int < 0){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: store_review_count")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: store_review_count (not a non-negative integer): " . json_encode($acp_store_review_count) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s (not a non-negative integer)", "acp-feed-woocommerce"), "ACP: store_review_count")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: store_review_count (not a non-negative integer): " . json_encode($acp_store_review_count) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: store_review_rating
            $acp_store_review_rating = self::acp_get_store_review_rating($product);

            if($acp_store_review_rating !== '' && $acp_store_review_rating !== null){
                if(is_int($acp_store_review_rating) || is_float($acp_store_review_rating) || is_numeric($acp_store_review_rating)){
                    $acp_store_review_rating_float = floatval($acp_store_review_rating);

                    if($acp_store_review_rating_float < 0 || $acp_store_review_rating_float > 5){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: store_review_rating (not a number between 0 and 5)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: store_review_rating (not a number between 0 and 5): " . json_encode($acp_store_review_rating) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }else{
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: store_review_rating (not a number between 0 and 5)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: store_review_rating (not a number between 0 and 5): " . json_encode($acp_store_review_rating) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }

            // ACP: q_and_a
            $acp_q_and_a = self::acp_get_q_and_a($product);

            if(!empty($acp_q_and_a)){
                $acp_q_and_a = strval($acp_q_and_a);

                if(strlen($acp_q_and_a) > 5000){
                    if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                        $acp_q_and_a = substr((string)$acp_q_and_a, 0, 4997) . '...';

                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'warning', 
                            sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: q_and_a", 5000)
                        );
                        acp_log('ACP_Feed_Builder -> emit_product() -> '
                            .'Product with too long ACP q_and_a (>5000), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                            . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                    }else{
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: q_and_a", 5000)
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with too long ACP: q_and_a (>5000): " . json_encode($acp_q_and_a) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: q_and_a")
                );
            }

            // ACP: raw_review_data
            $acp_raw_review_data = self::acp_get_raw_review_data($product);

            if(!empty($acp_raw_review_data)){
                $acp_raw_review_data = strval($acp_raw_review_data);

                try{
                    json_decode($acp_raw_review_data);
                    if(json_last_error() !== JSON_ERROR_NONE){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: raw_review_data (not a valid JSON)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: raw_review_data (not a valid JSON): " . json_encode($acp_raw_review_data) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }catch(Throwable $e){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: raw_review_data (not a valid JSON)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: raw_review_data (not a valid JSON): " . json_encode($acp_raw_review_data) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                if(strlen($acp_raw_review_data) > 10000){
                    if($acp_class_settings->acp_get_setting('product_too_long_value', 'truncate') === 'truncate'){
                        $acp_raw_review_data = substr((string)$acp_raw_review_data, 0, 9997) . '...';

                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'warning', 
                            sprintf(__("%s was too long (>%s), truncated to the correct length", "acp-feed-woocommerce"), "ACP: ra_review_data", 10000)
                        );
                        acp_log('ACP_Feed_Builder -> emit_product() -> '
                            .'Product with too long ACP raw_review_data (>10000), trimmed to the correct length: ' . json_encode($acp_description) . ' '
                            . "for product with ID: " . $product->get_id() . ' (' . $product->name() . ')');
                    }else{
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Too long %s (>%s)", "acp-feed-woocommerce"), "ACP: raw_review_data", 10000)
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with too long ACP: raw_review_data (>10000): " . json_encode($acp_raw_review_data) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: raw_review_data")
                );
            }

            // ACP: related_product_id
            $acp_related_product_id = self::acp_get_related_ids($product);

            if(!empty($acp_related_product_id)){
                $acp_related_product_id = strval($acp_related_product_id);
                $related_ids = explode(',', $acp_related_product_id);

                foreach($related_ids as $related_id){
                    if(!preg_match('/^[a-zA-Z0-9]+$/', $related_id)){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: related_product_id (not alphanumeric IDs separated by commas)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: related_product_id (not alphanumeric IDs separated by commas): " . json_encode($acp_related_product_id) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: related_product_id")
                );
            }

            // ACP: relationship_type
            $acp_relationship_type = self::acp_get_relationship_type($product);
            
            if(!empty($acp_relationship_type)){
                $acp_relationship_type = strtolower(strval($acp_relationship_type));

                if(!in_array($acp_relationship_type, ['part_of_set', 'required_part', 'often_bought_with', 'substitute', 'different_brand', 'accessory'])){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        sprintf(__("Invalid %s, incorrect value", "acp-feed-woocommerce"), "ACP: relationship_type") . " "
                            . sprintf(__("Expected 'part_of_set', 'required_part', 'often_bought_with', 'substitute', 'different_brand' or 'accessory', got: '%s'", "acp-feed-woocommerce"), json_encode($acp_relationship_type))
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: relationship_type (not one of: 'part_of_set', 'required_part', 'often_bought_with', 'substitute', 'different_brand' or 'accessory'): " . json_encode($acp_relationship_type) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: relationship_type")
                );
            }

            // ACP: geo_price
            $acp_geo_price = self::acp_get_geo_price($product);

            // TODO - awaiting confirmation from OpenAI on validation rules
            if(!empty($acp_geo_price)){
                $acp_geo_price = trim(strval($acp_geo_price));

                $geo_price_rules = array_filter(
                    array_map('trim', explode(';', $acp_geo_price)),
                    static function ($value){
                        return $value !== '';
                    }
                );

                if(empty($geo_price_rules)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: geo_price (not in format country:price currency)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: geo_price (not in format country:price currency): " . json_encode($acp_geo_price) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                foreach($geo_price_rules as $geo_price_rule){
                    $geo_price_parts = array_map('trim', explode(':', $geo_price_rule, 2));

                    if(count($geo_price_parts) !== 2 || $geo_price_parts[0] === '' || $geo_price_parts[1] === ''){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: geo_price (not in format country:price currency)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: geo_price (not in format country:price currency): " . json_encode($acp_geo_price) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }

                    list($country_code, $geo_price_value) = $geo_price_parts;

                    if(!preg_match('/^[A-Z]{2}$/', $country_code)){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid country code in ACP: geo_price (expected ISO 3166-1 alpha-2)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid country code in ACP: geo_price: " . json_encode($geo_price_rule) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }

                    if(!preg_match('/^\d+(\.\d+)?\s*[A-Z]{3}$/', $geo_price_value)){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid price in ACP: geo_price, incorrect format", "acp-feed-woocommerce") . " "
                                . sprintf(__("Expected 'number currency', e.g. 10.99 USD, got: '%s'", "acp-feed-woocommerce"), json_encode($geo_price_value))
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: geo_price value (not in format number currency, e.g. 10.99 USD): " . json_encode($geo_price_rule) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }
                }
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: geo_price")
                );
            }

            // ACP: geo_availability
            $acp_geo_availability = self::acp_get_geo_availability($product);

            // TODO - awaiting confirmation from OpenAI on validation rules
            if(!empty($acp_geo_availability)){
                $acp_geo_availability = trim(strval($acp_geo_availability));

                $geo_availability_rules = array_filter(
                    array_map('trim', explode(';', $acp_geo_availability)),
                    static function ($value){
                        return $value !== '';
                    }
                );

                if(empty($geo_availability_rules)){
                    ACP_Feed_DBHelper::acp_report(
                        $build_id,
                        $product->get_id(), 
                        'failed', 
                        __("Invalid ACP: geo_availability (not in format country:availability)", "acp-feed-woocommerce")
                    );
                    acp_log("ACP_Feed_Builder -> emit_product() -> "
                        . "Skipping product with invalid ACP: geo_availability (not in format country:availability): " . json_encode($acp_geo_availability) . " "
                            . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                    );

                    return false;
                }

                $normalized_geo_availability_rules = [];

                foreach($geo_availability_rules as $geo_availability_rule){
                    $geo_availability_parts = array_map('trim', explode(':', $geo_availability_rule, 2));

                    if(count($geo_availability_parts) !== 2 || $geo_availability_parts[0] === '' || $geo_availability_parts[1] === ''){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid ACP: geo_availability (not in format country:availability)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: geo_availability (not in format country:availability): " . json_encode($geo_availability_rule) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }

                    list($country_code, $availability_value) = $geo_availability_parts;

                    if(!preg_match('/^[A-Z]{2}$/', $country_code)){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            __("Invalid country code in ACP: geo_availability (expected ISO 3166-1 alpha-2)", "acp-feed-woocommerce")
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid country code in ACP: geo_availability: " . json_encode($geo_availability_rule) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }

                    $availability_value_normalized = strtolower($availability_value);

                    if(!in_array($availability_value_normalized, ['in_stock', 'out_of_stock', 'preorder'], true)){
                        ACP_Feed_DBHelper::acp_report(
                            $build_id,
                            $product->get_id(), 
                            'failed', 
                            sprintf(__("Invalid %s, incorrect value", "acp-feed-woocommerce"), "ACP: geo_availability") . " "
                                . sprintf(__("Expected 'in_stock', 'out_of_stock' or 'preorder', got: '%s'", "acp-feed-woocommerce"), json_encode($availability_value))
                        );
                        acp_log("ACP_Feed_Builder -> emit_product() -> "
                            . "Skipping product with invalid ACP: geo_availability value (not one of: 'in_stock', 'out_of_stock' or 'preorder'): " . json_encode($geo_availability_rule) . " "
                                . "for product with ID: " . $product->get_id() . " (" . $product->get_name() . ")"
                        );

                        return false;
                    }

                    $normalized_geo_availability_rules[] = $country_code . ':' . $availability_value_normalized;
                }

                $acp_geo_availability = implode(';', $normalized_geo_availability_rules);
            }else{
                ACP_Feed_DBHelper::acp_report(
                    $build_id,
                    $product->get_id(), 
                    'warning',
                    sprintf(__("%s is optional but recommended.", "acp-feed-woocommerce"), "ACP: geo_availability")
                );
            }


            // All good, emit the product

            $xml_writer->startElement('product'); // <product>

            // Flags
            self::acp_element($xml_writer, 'enable_search',  $acp_enable_search ? 'true' : 'false');
            self::acp_element($xml_writer, 'enable_checkout', $acp_enable_checkout ? 'true' : 'false');
    
            // Basic
            self::acp_element($xml_writer, 'id', $acp_id);
            self::acp_element($xml_writer, 'gtin', $acp_gtin);
            self::acp_element($xml_writer, 'mpn', $acp_mpn);
            self::acp_element($xml_writer, 'title', $acp_title);
            self::acp_element($xml_writer, 'description', $acp_description);
            self::acp_element($xml_writer, 'link', $acp_link);
    
            // Item information
            self::acp_element($xml_writer, 'condition', $acp_condition);
            self::acp_element($xml_writer, 'product_category', $acp_product_category);
            self::acp_element($xml_writer, 'brand', $acp_brand);
            self::acp_element($xml_writer, 'material', $acp_material);
            self::acp_element($xml_writer, 'dimensions', $acp_dimensions);
            self::acp_element($xml_writer, 'length', $acp_length);
            self::acp_element($xml_writer, 'width', $acp_width);
            self::acp_element($xml_writer, 'height', $acp_height);
            self::acp_element($xml_writer, 'weight', $acp_weight);
            self::acp_element($xml_writer, 'age_group', $acp_age_group);

            // Media
            self::acp_element($xml_writer, 'image_link', $acp_image_link);
            self::acp_element($xml_writer, 'additional_image_link', $acp_additional_images);
            self::acp_element($xml_writer, 'video_link', $acp_video_link);
            self::acp_element($xml_writer, 'model_3d_link', $acp_model_3d_link);

            // Prices
            self::acp_element($xml_writer, 'price', $acp_price);
            self::acp_element($xml_writer, 'applicable_taxes_fees', $acp_applicable_taxes_fees);
            self::acp_element($xml_writer, 'sale_price', $acp_sale_price);
            self::acp_element($xml_writer, 'sale_price_effective_date', $acp_sale_price_effective_date);
            self::acp_element($xml_writer, 'unit_pricing_measure', $acp_unit_pricing_measure);
            self::acp_element($xml_writer, 'base_measure', $acp_base_measure);
            self::acp_element($xml_writer, 'pricing_trend', $acp_pricing_trend);

            // Availability & Inventory
            self::acp_element($xml_writer, 'availability', $acp_availability);
            self::acp_element($xml_writer, 'availability_date', $acp_availability_date);
            self::acp_element($xml_writer, 'inventory_quantity', (string)$acp_inventory_quantity);
            self::acp_element($xml_writer, 'expiration_date', $acp_expiration_date);
            self::acp_element($xml_writer, 'pickup_method', $acp_pickup_method);
            self::acp_element($xml_writer, 'pickup_sla', $acp_pickup_sla);

            // Variant

            // TODO - awaiting confirmation from OpenAI on validation rules

            // Fulfillment
            self::acp_element($xml_writer, 'shipping', $acp_shipping);
            self::acp_element($xml_writer, 'delivery_estimate', $acp_delivery_estimate);

            // Merchant info
            self::acp_element($xml_writer, 'seller_name', $acp_seller_name);
            self::acp_element($xml_writer, 'seller_url', $acp_seller_url);
            self::acp_element($xml_writer, 'seller_privacy_policy', $acp_seller_privacy_policy);
            self::acp_element($xml_writer, 'seller_tos', $acp_seller_tos);

            // Returns
            self::acp_element($xml_writer, 'return_policy', $acp_return_policy);
            self::acp_element($xml_writer, 'return_window', $acp_return_window);

            // Performance
            self::acp_element($xml_writer, 'popularity_score', $acp_popularity_score);
            self::acp_element($xml_writer, 'return_rate', $acp_return_rate);

            // Compliance
            self::acp_element($xml_writer, 'warning', $acp_warning);
            self::acp_element($xml_writer, 'warning_url', $acp_warning_url);
            self::acp_element($xml_writer, 'age_restriction', $acp_age_restriction);

            // Reviews and Q&A
            self::acp_element($xml_writer, 'product_review_count', $acp_product_review_count);
            self::acp_element($xml_writer, 'product_review_rating', $acp_product_review_rating);
            self::acp_element($xml_writer, 'store_review_count', $acp_store_review_count);
            self::acp_element($xml_writer, 'store_review_rating', $acp_store_review_rating);
            self::acp_element($xml_writer, 'q_and_a', $acp_q_and_a);
            self::acp_element($xml_writer, 'raw_review_data', $acp_raw_review_data);

            // Related products
            self::acp_element($xml_writer, 'related_product_id', $acp_related_product_id);
            self::acp_element($xml_writer, 'relationship_type', $acp_relationship_type);

            // Geo tagging
            self::acp_element($xml_writer, 'geo_price', $acp_geo_price);
            self::acp_element($xml_writer, 'geo_availability', $acp_geo_availability);
            
            /**
             * Action hook before ending product element in the feed
             * Use case: add custom elements to the product
             * 
             * @param XMLWriter $xml_writer - XMLWriter object to write XML elements
             * @param WC_Product $product - WooCommerce product object being processed
             * @since 1.0.0
             */
            do_action('acp_feed_before_product_end', $xml_writer, $product);

            $xml_writer->endElement(); // </product>

            return true;
        }catch(Throwable $e){
            acp_log('Error in emit_product: '.$e);
        }

        return false;
    }

    /*--------------------------------------------------*/
    /*                                                  */
    /*                      Helpers                     */
    /*                                                  */
    /*--------------------------------------------------*/

    /**
     * Get attribute value
     *
     * @param WC_Product $product - WooCommerce product object to get attribute from
     * @param string $key - Attribute key
     * @return string|null - Cleaned attribute value or null if not found
     * @since 1.0.0
     */
    public static function acp_get_attribute($product, $key){
        if(empty($key))
            return null;

        $value = $product->get_attribute($key);
        
        if(!empty($value))
            return wc_clean(wp_strip_all_tags($value));
        
        return null;
    }

    /**
     * Get meta value
     *
     * @param WC_Product $product - WooCommerce product object to get meta from
     * @param string $key - Meta key
     * @return string|null - Cleaned meta value or null if not found
     * @since 1.0.0
     */
    public static function acp_get_meta($product, $key){
        if(empty($key))
            return null;

        $value = $product->get_meta($key);
        
        if(!empty($value))
            return wc_clean(wp_strip_all_tags($value));
        
        return null;
    }

    /**
     * Get ACP: enable_search
     *
     * @param WC_Product $product - WooCommerce product object to check
     * @return bool - True if product is enabled for search, false otherwise
     * @since 1.0.0
     */
    public static function acp_get_enable_search($product){
        $enable_search = ($product->get_status() === 'publish');

        if($product->get_catalog_visibility() !== 'visible')
            $enable_search = false;

        /**
         * Filter to modify ACP: enable_search value
         *
         * @param bool $enable_search - Current enable_search value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return bool - the filter has to return bool
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_enable_search', $enable_search, $product);
    }

    /**
     * Get ACP: enable_checkout
     *
     * @param WC_Product $product - WooCommerce product object to check
     * @return bool - True if product is enabled for checkout, false otherwise
     * @since 1.0.0
     */
    public static function acp_get_enable_checkout($product){
        $enable_checkout = false;

        if(self::acp_get_enable_search($product)){
            if($product->managing_stock()){
                $acp_inventory_quantity = $product->get_stock_quantity();

                if($acp_inventory_quantity > 0)
                    $enable_checkout = true;

                if($product->backorders_allowed())
                    $enable_checkout = true;
            }else{
                $stock_status = $product->get_stock_status();

                if($stock_status === 'instock')
                    $enable_checkout = true;
                else if($stock_status === 'onbackorder')
                    $enable_checkout = true;
            }
        }

        /**
         * Filter to modify ACP: enable_checkout value
         *
         * @param bool $enable_checkout - Current enable_checkout value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return bool - the filter has to return bool
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_enable_checkout', $enable_checkout, $product);
    }

    /**
     * Get ACP: id
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - id for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_id($product){
        global $acp_class_settings;

        $acp_id = null;
        $acp_id_get_method = $acp_class_settings->acp_get_setting('product_id', 'sku');

        if($acp_id_get_method === 'id')
            $acp_id = $product->get_id();
        else if($acp_id_get_method === 'sku')
            $acp_id = $product->get_sku();
        else if($acp_id_get_method === 'both')
            $acp_id = $product->get_sku() ?: $product->get_id();
        else if($acp_id_get_method === 'attr')
            $acp_id = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_id', ''));
        else if($acp_id_get_method === 'meta')
            $acp_id = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_id', ''));
        
        /**
         * Filter to modify ACP: id value
         * 
         * @param mixed $acp_id - Current id value
         * @param string $acp_id_get_method - Method used to get the id
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - the filter has to return alphanumeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_id', $acp_id, $acp_id_get_method, $product);
    }

    /**
     * Get ACP: gtin
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - gtin for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_gtin($product){
        global $acp_class_settings;

        $acp_gtin = null;
        $acp_gtin_get_method = $acp_class_settings->acp_get_setting('product_gtin', 'woo');

        if($acp_gtin_get_method === 'woo')
            $acp_gtin = $product->get_global_unique_id();
        else if($acp_gtin_get_method === 'attr')
            $acp_gtin = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_gtin', ''));
        else if($acp_gtin_get_method === 'meta')
            $acp_gtin = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_gtin', ''));

        /**
         * Filter to modify ACP: gtin value
         *
         * @param mixed $acp_gtin - Current gtin value
         * @param string $acp_gtin_get_method - Method used to get the gtin
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - the filter has to return numeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_gtin', $acp_gtin, $acp_gtin_get_method, $product);
    }

    /**
     * Get ACP: mpn
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - mpn for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_mpn($product){
        global $acp_class_settings;

        $acp_mpn = null;
        $acp_mpn_get_method = $acp_class_settings->acp_get_setting('product_mpn', 'attr');

        if($acp_mpn_get_method === 'attr')
            $acp_mpn = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_mpn', ''));
        else if($acp_mpn_get_method === 'meta')
            $acp_mpn = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_mpn', ''));

        /**
         * Filter to modify ACP: mpn value
         * 
         * @param mixed $acp_mpn - Current mpn value
         * @param string $acp_mpn_get_method - Method used to get the mpn
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - the filter has to return alphanumeric string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_mpn', $acp_mpn, $acp_mpn_get_method, $product);
    }

    /**
     * Get ACP: title
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - title for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_title($product){
        global $acp_class_settings;

        $acp_title = null;
        $acp_title_get_method = $acp_class_settings->acp_get_setting('product_title', 'woo');

        if($acp_title_get_method === 'woo')
            $acp_title = $product->get_name();
        else if($acp_title_get_method === 'attr')
            $acp_title = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_title', ''));
        else if($acp_title_get_method === 'meta')
            $acp_title = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_title', ''));

        /**
         * Filter to modify ACP: title value
         *
         * @param mixed $acp_title - Current title value
         * @param string $acp_title_get_method - Method used to get the title value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the UTF-8 encoding
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_title', $acp_title, $acp_title_get_method, $product);
    }

    /**
     * Get ACP: description
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - description for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_description($product){
        global $acp_class_settings;

        $acp_desc = null;
        $acp_desc_get_method = $acp_class_settings->acp_get_setting('product_description', 'long');

        if($acp_desc_get_method === 'long')
            $acp_desc = self::acp_clean_description($product->get_description());
        else if($acp_desc_get_method === 'short')
            $acp_desc = self::acp_clean_description($product->get_short_description());
        else if($acp_desc_get_method === 'attr')
            $acp_desc = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_description', ''));
        else if($acp_desc_get_method === 'meta')
            $acp_desc = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_description', ''));

        /**
         * Filter to modify ACP: description value
         *
         * @param mixed $acp_desc - Current description value
         * @param string $acp_desc_get_method - Method used to get the description value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the UTF-8 encoding, plain text only
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_description', $acp_desc, $acp_desc_get_method, $product);
    }
    
    /**
     * Clean description by stripping HTML tags and trimming whitespace
     *
     * @param string $html - HTML description to clean
     * @return string - Cleaned description
     * @since 1.0.0
     */
    public static function acp_clean_description(string $html){
        $text = strip_tags($html);
        return trim($text);
    }

    /**
     * Get ACP: link
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - link for the product item
     * @since 1.0.0
     */
    public static function acp_get_link($product){
        global $acp_class_settings;
        
        $product_default_link = get_permalink($product->get_id());
        $acp_link = $acp_class_settings->acp_get_setting('product_link', '%product_link%');
        $acp_link = str_replace('%product_link%', $product_default_link, $acp_link);

        /**
         * Filter to modify ACP: link value
         *
         * @param mixed $acp_link - Current link value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_link', $acp_link, $product);
    }

    /**
     * Get ACP: condition
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - condition for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_condition($product){
        global $acp_class_settings;

        $acp_condition = null;
        $acp_condition_get_method = $acp_class_settings->acp_get_setting('product_condition', 'attr');

        if($acp_condition_get_method === 'attr')
            $acp_condition = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_condition', ''));
        else if($acp_condition_get_method === 'meta')
            $acp_condition = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_condition', ''));

        /**
         * Filter to modify ACP: condition value
         *
         * @param mixed $acp_condition - Current condition value
         * @param string $acp_condition_get_method - Method used to get the condition value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return lowercase string, allowed: 'new', 'used' or 'refurbished'
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_condition', $acp_condition, $acp_condition_get_method, $product);
    }

    /**
     * Get ACP: product_category
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - category path for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_category_path($product){
        global $acp_class_settings;

        $acp_category = null;
        $acp_category_get_method = $acp_class_settings->acp_get_setting('product_category', 'woo');

        if($acp_category_get_method === 'woo'){
            $product_id = $product->get_id();
            $terms = get_the_terms($product_id, 'product_cat');
            
            if(empty($terms) || is_wp_error($terms))
                return null;

            usort($terms, function($a, $b){
                return self::acp_term_depth($a) <=> self::acp_term_depth($b);
                });

            $term = end($terms);
            $chain = [];
            
            while($term && !is_wp_error($term)){
                $chain[] = $term->name;
                $term = $term->parent ? get_term($term->parent, 'product_cat') : null;
            }

            $acp_category = implode(' > ', array_reverse($chain));
        }else if($acp_category_get_method === 'attr'){
            $acp_category = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_category', ''));
        }else if($acp_category_get_method === 'meta'){
            $acp_category = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_category', ''));
        }

        /**
         * Filter to modify ACP: product_category value
         *
         * @param mixed $acp_category - Current category value
         * @param string $acp_category_get_method - Method used to get the category value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_category', $acp_category, $acp_category_get_method, $product);
    }

    /**
     * Get term depth in the category hierarchy
     *
     * @param WP_Term $term - Term object to calculate depth for
     * @return int - Depth of the term in the hierarchy
     * @since 1.0.0
     */
    public static function acp_term_depth($term){
        $depth = 0;
        
        while($term && $term->parent){
            $depth++; 
            $term = get_term($term->parent, 'product_cat');
        } 
        
        return $depth;
    }

    /**
     * Get ACP: brand
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - brand for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_brand($product){
        global $acp_class_settings;

        $acp_brand = null;
        $acp_brand_get_method = $acp_class_settings->acp_get_setting('product_brand', 'attr');

        if($acp_brand_get_method === 'woo'){
            $terms = get_the_terms($product->get_id(), 'product_brand');

            foreach($terms as $term){
                if($term->parent == 0){
                    $acp_brand = $term->name;
                    break;
                }
            }
        }else if($acp_brand_get_method === 'attr'){
            $acp_brand = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_brand', ''));
        }else if($acp_brand_get_method === 'meta'){
            $acp_brand = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_brand', ''));
        }

        /**
         * Filter to modify ACP: brand value
         *
         * @param mixed $acp_brand - Current brand value
         * @param string $acp_brand_get_method - Method used to get the brand value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_brand', $acp_brand, $acp_brand_get_method, $product);
    }

    /**
     * Get ACP: material
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - material for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_material($product){
        global $acp_class_settings;

        $acp_material = null;
        $acp_material_get_method = $acp_class_settings->acp_get_setting('product_material', 'attr');

        if($acp_material_get_method === 'attr')
            $acp_material = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_material', ''));
        else if($acp_material_get_method === 'meta')
            $acp_material = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_material', ''));

        /**
         * Filter to modify ACP: material value
         *
         * @param mixed $acp_material - Current material value
         * @param string $acp_material_get_method - Method used to get the material value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_material', $acp_material, $acp_material_get_method, $product);
    }

    /**
     * Get ACP: dimensions
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - dimensions for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_dimensions($product){
        global $acp_class_settings;

        $acp_dimensions = null;
        $acp_dimensions_get_method = $acp_class_settings->acp_get_setting('product_dimensions', 'woo');

        if($acp_dimensions_get_method === 'woo'){
            $length = $product->get_length();
            $width = $product->get_width();
            $height = $product->get_height();
            $unit = get_option('woocommerce_dimension_unit');

            if($length && $width && $height){
                $acp_dimensions = $length . 'x' . $width . 'x' . $height . ' ' . $unit;
            }
        }else if($acp_dimensions_get_method === 'attr'){
            $acp_dimensions = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_dimensions', ''));
        }else if($acp_dimensions_get_method === 'meta'){
            $acp_dimensions = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_dimensions', ''));
        }

        /**
         * Filter to modify ACP: dimensions value
         *
         * @param mixed $acp_dimensions - Current dimensions value
         * @param string $acp_dimensions_get_method - Method used to get the dimensions value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "LxWxH unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_dimensions', $acp_dimensions, $acp_dimensions_get_method, $product);
    }

    /**
     * Validate ACP: dimensions format
     *
     * @param string $text - Dimensions text to validate
     * @return bool - True if dimensions format is valid, false otherwise
     * @since 1.0.0
     */
    public static function acp_is_dimensions_valid(string $text){
        $pattern = '/^'                // start
                . '(\d+(?:[.,]\d+)?)' // L
                . 'x'
                . '(\d+(?:[.,]\d+)?)' // W
                . 'x'
                . '(\d+(?:[.,]\d+)?)' // H
                . ' '                 // exactly one ASCII space
                . '([^\s].*)'         // unit: non-empty, may contain spaces
                . '$/u';              // end, UTF-8

        if(!preg_match($pattern, $text, $matcher))
            return false;

        $L = (float) str_replace(',', '.', $matcher[1]);
        $W = (float) str_replace(',', '.', $matcher[2]);
        $H = (float) str_replace(',', '.', $matcher[3]);
        $unit = $matcher[4];

        if($L <= 0 || $W <= 0 || $H <= 0)
            return false;
        
        return true;
    }

    /**
     * Validate ACP: number with unit format
     *
     * @param string $text - Number with unit text to validate
     * @return bool - True if number with unit format is valid, false otherwise
     * @since 1.0.0
     */
    public static function acp_is_number_unit_valid(string $text){
        if(!preg_match('/^(\d+(?:\.\d+)?) (\S(?:.*\S)?)$/u', $text, $matcher))
            return false;
            
        $amount = (int) $matcher[1];
        $unit   = trim($matcher[2]);

        if($amount < 0 || $unit === '')
            return false;

        return true;
    }

    /**
     * Get ACP: length
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - length for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_length($product){
        global $acp_class_settings;

        $acp_length = null;
        $acp_length_get_method = $acp_class_settings->acp_get_setting('product_length', 'woo');

        if($acp_length_get_method === 'woo'){
            $length = $product->get_length();
            $unit = get_option('woocommerce_dimension_unit');

            if($length){
                $acp_length = $length . ' ' . $unit;
            }
        }else if($acp_length_get_method === 'attr'){
            $acp_length = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_length', ''));
        }else if($acp_length_get_method === 'meta'){
            $acp_length = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_length', ''));
        }

        /**
         * Filter to modify ACP: length value
         *
         * @param mixed $acp_length - Current length value
         * @param string $acp_length_get_method - Method used to get the length value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_length', $acp_length, $acp_length_get_method, $product);
    }

    /**
     * Get ACP: width
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - width for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_width($product){
        global $acp_class_settings;

        $acp_width = null;
        $acp_width_get_method = $acp_class_settings->acp_get_setting('product_width', 'woo');

        if($acp_width_get_method === 'woo'){
            $width = $product->get_width();
            $unit = get_option('woocommerce_dimension_unit');

            if($width){
                $acp_width = $width . ' ' . $unit;
            }
        }else if($acp_width_get_method === 'attr'){
            $acp_width = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_width', ''));
        }else if($acp_width_get_method === 'meta'){
            $acp_width = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_width', ''));
        }

        /**
         * Filter to modify ACP: width value
         *
         * @param mixed $acp_width - Current width value
         * @param string $acp_width_get_method - Method used to get the width value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_width', $acp_width, $acp_width_get_method, $product);
    }

    /**
     * Get ACP: height
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - height for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_height($product){
        global $acp_class_settings;

        $acp_height = null;
        $acp_height_get_method = $acp_class_settings->acp_get_setting('product_height', 'woo');

        if($acp_height_get_method === 'woo'){
            $height = $product->get_height();
            $unit = get_option('woocommerce_dimension_unit');

            if($height){
                $acp_height = $height . ' ' . $unit;
            }
        }else if($acp_height_get_method === 'attr'){
            $acp_height = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_height', ''));
        }else if($acp_height_get_method === 'meta'){
            $acp_height = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_height', ''));
        }

        /**
         * Filter to modify ACP: height value
         *
         * @param mixed $acp_height - Current height value
         * @param string $acp_height_get_method - Method used to get the height value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_height', $acp_height, $acp_height_get_method, $product);
    }

    /**
     * Get ACP: weight
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - weight for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_weight($product){
        global $acp_class_settings;

        $acp_weight = null;
        $acp_weight_get_method = $acp_class_settings->acp_get_setting('product_weight', 'woo');

        if($acp_weight_get_method === 'woo'){
            $weight = $product->get_weight();
            $unit = get_option('woocommerce_weight_unit');

            if($weight){
                $acp_weight = $weight . ' ' . $unit;
            }
        }else if($acp_weight_get_method === 'attr'){
            $acp_weight = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_weight', ''));
        }else if($acp_weight_get_method === 'meta'){
            $acp_weight = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_weight', ''));
        }

        /**
         * Filter to modify ACP: weight value
         *
         * @param mixed $acp_weight - Current weight value
         * @param string $acp_weight_get_method - Method used to get the weight value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_weight', $acp_weight, $acp_weight_get_method, $product);
    }

    /**
     * Get ACP: age_group
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - age_group for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_age_group($product){
        global $acp_class_settings;

        $acp_age_group = null;
        $acp_age_group_get_method = $acp_class_settings->acp_get_setting('product_age_group', 'attr');

        if($acp_age_group_get_method === 'attr')
            $acp_age_group = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_age_group', ''));
        else if($acp_age_group_get_method === 'meta')
            $acp_age_group = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_age_group', ''));

        /**
         * Filter to modify ACP: age_group value
         *
         * @param mixed $acp_age_group - Current age_group value
         * @param string $acp_age_group_get_method - Method used to get the age_group value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return lowercase string, allowed: 'newborn', 'infant', 'toddler', 'kids', 'adult'
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_age_group', $acp_age_group, $acp_age_group_get_method, $product);
    }

    /**
     * Get ACP: main_image
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - main_image link for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_main_image($product){
        global $acp_class_settings;

        $image_link = null;
        $acp_image_get_method = $acp_class_settings->acp_get_setting('product_image', 'woo');

        if($acp_image_get_method === 'woo'){
            $id = $product->get_image_id();
            
            if($id){
                $src = wp_get_attachment_image_src($id, 'full');
                
                if($src && !empty($src[0]))
                    $image_link = $src[0];
            }
        }else if($acp_image_get_method === 'attr'){
            $image_link = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_image', ''));
        }else if($acp_image_get_method === 'meta'){
            $image_link = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_image', ''));
        }
        
        /**
         * Filter to modify ACP: image_link value
         *
         * @param mixed $image_link - Current image_link value
         * @param string $acp_image_get_method - Method used to get the image_link value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_image_link', $image_link, $acp_image_get_method, $product);
    }

    /**
     * Get ACP: additional_images
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return string - Comma-separated additional image links for the product item
     * @since 1.0.0
     */
    public static function acp_get_additional_images($product){ 
        global $acp_class_settings;

        $additional_images = ""; 
        $acp_additional_image_link_get_method = $acp_class_settings->acp_get_setting('product_additional_image_link', 'woo');

        if($acp_additional_image_link_get_method === 'woo'){
            $first = true;

            foreach (($product->get_gallery_image_ids() ?: []) as $id){
                $src = wp_get_attachment_image_src($id, 'full'); 

                if($src && !empty($src[0])){ 
                    if(!$first) $additional_images .= ",";
                
                    $additional_images .= $src[0];
                }

                $first = false;
            } 
        }else if($acp_additional_image_link_get_method === 'attr'){
            $additional_images = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_additional_image_link', ''));
        }else if($acp_additional_image_link_get_method === 'meta'){
            $additional_images = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_additional_image_link', ''));
        }

        /**
         * Filter to modify ACP: additional_image_link value
         *
         * @param mixed $additional_images - Current additional_image_link value
         * @param string $acp_additional_image_link_get_method - Method used to get the additional_image_link value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_additional_image_link', $additional_images, $acp_additional_image_link_get_method, $product);
    }

    /**
     * Get ACP: video_link
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - video_link for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_video_link($product){
        global $acp_class_settings;

        $video_link = null;
        $acp_video_get_method = $acp_class_settings->acp_get_setting('product_video_link', 'attr');

        if($acp_video_get_method === 'attr')
            $video_link = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_video_link', ''));
        else if($acp_video_get_method === 'meta')
            $video_link = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_video_link', ''));

        /**
         * Filter to modify ACP: video_link value
         *
         * @param mixed $video_link - Current video_link value
         * @param string $acp_video_get_method - Method used to get the video_link value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_video_link', $video_link, $acp_video_get_method, $product);
    }

    /**
     * Get ACP: model_3d_link
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - model_3d_link for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_model_3d_link($product){
        global $acp_class_settings;

        $model_3d_link = null;
        $acp_model_3d_get_method = $acp_class_settings->acp_get_setting('product_model_3d_link', 'attr');

        if($acp_model_3d_get_method === 'attr')
            $model_3d_link = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_model_3d_link', ''));
        else if($acp_model_3d_get_method === 'meta')
            $model_3d_link = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_model_3d_link', ''));

        /**
         * Filter to modify ACP: model_3d_link value
         *
         * @param mixed $model_3d_link - Current model_3d_link value
         * @param string $acp_model_3d_get_method - Method used to get the model_3d_link value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_model_3d_link', $model_3d_link, $acp_model_3d_get_method, $product);
    }

    /**
     * Get ACP: price
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - price for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_price($product){
        global $acp_class_settings;

        $acp_price = null;
        $acp_price_get_method = $acp_class_settings->acp_get_setting('product_price', 'woo');

        if($acp_price_get_method === 'woo'){
            $price = $product->get_regular_price();

            if(!empty($price))
                $acp_price = self::acp_display_price(wc_get_price_to_display($product, ['price' => (float)$price]));
        }else if($acp_price_get_method === 'attr'){
            $acp_price = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_price', ''));
        }else if($acp_price_get_method === 'meta'){
            $acp_price = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_price', ''));
        }

        /**
         * Filter to modify ACP: price value
         *
         * @param mixed $acp_price - Current price value
         * @param string $acp_price_get_method - Method used to get the price value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number currency"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_price', $acp_price, $acp_price_get_method, $product);
    }

    /**
     * Get ACP: applicable_taxes_fees
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - applicable_taxes_fees for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_applicable_taxes_fees($product){
        global $acp_class_settings;

        $acp_taxes_fees = null;
        $acp_taxes_fees_get_method = $acp_class_settings->acp_get_setting('product_applicable_taxes_fees', 'attr');

        if($acp_taxes_fees_get_method === 'attr')
            $acp_taxes_fees = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_applicable_taxes_fees', ''));
        else if($acp_taxes_fees_get_method === 'meta')
            $acp_taxes_fees = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_applicable_taxes_fees', ''));

        /**
         * Filter to modify ACP: applicable_taxes_fees value
         *
         * @param mixed $acp_taxes_fees - Current applicable_taxes_fees value
         * @param string $acp_taxes_fees_get_method - Method used to get the applicable_taxes_fees value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number currency"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_applicable_taxes_fees', $acp_taxes_fees, $acp_taxes_fees_get_method, $product);
    }

    /**
     * Get ACP: sale_price
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - sale_price for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_sale_price($product){
        global $acp_class_settings;

        $acp_sale_price = null;
        $acp_sale_price_get_method = $acp_class_settings->acp_get_setting('product_sale_price', 'woo');

        if($acp_sale_price_get_method === 'woo'){
            $price = $product->get_sale_price();

            if(!empty($price))
                $acp_sale_price = self::acp_display_price(wc_get_price_to_display($product, ['price' => (float)$price]));
        }else if($acp_sale_price_get_method === 'attr'){
            $acp_sale_price = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_sale_price', ''));
        }else if($acp_sale_price_get_method === 'meta'){
            $acp_sale_price = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_sale_price', ''));
        }
        
        /**
         * Filter to modify ACP: sale_price value
         *
         * @param mixed $acp_sale_price - Current sale_price value
         * @param string $acp_sale_price_get_method - Method used to get the sale_price value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number currency"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_sale_price', $acp_sale_price, $acp_sale_price_get_method, $product);
    }

    /**
     * Format price with currency
     *
     * @param float $price - Price value to format
     * @return string - Formatted price with currency
     * @since 1.0.0
     */
    public static function acp_display_price(float $price){
        return $price . ' ' . get_woocommerce_currency();
    }

    /**
     * Get ACP: sale_price_effective_date
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - sale_price_effective_date for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_sale_price_effective_date($product){
        global $acp_class_settings;

        $acp_sale_price_effective_date = null;
        $acp_sale_price_effective_date_get_method = $acp_class_settings->acp_get_setting('product_sale_price_effective_date', 'woo');

        if($acp_sale_price_effective_date_get_method === 'woo'){
            $date_from = $product->get_date_on_sale_from() ? $product->get_date_on_sale_from()->date('Y-m-d') : '';
            $date_to = $product->get_date_on_sale_to() ? $product->get_date_on_sale_to()->date('Y-m-d') : '';

            if(!empty($date_from) && !empty($date_to))
                $acp_sale_price_effective_date = $date_from . ' / ' . $date_to;
        }else if($acp_sale_price_effective_date_get_method === 'attr'){
            $acp_sale_price_effective_date = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_sale_price_effective_date', ''));
        }else if($acp_sale_price_effective_date_get_method === 'meta'){
            $acp_sale_price_effective_date = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_sale_price_effective_date', ''));
        }

        /**
         * Filter to modify ACP: sale_price_effective_date value
         *
         * @param mixed $acp_sale_price_effective_date - Current sale_price_effective_date value
         * @param string $acp_sale_price_effective_date_get_method - Method used to get the sale_price_effective_date value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "YYYY-MM-DD / YYYY-MM-DD"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_sale_price_effective_date', $acp_sale_price_effective_date, $acp_sale_price_effective_date_get_method, $product);
    }

    /**
     * Get ACP: unit_pricing_measure
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - unit_pricing_measure for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_unit_pricing_measure($product){
        global $acp_class_settings;

        $acp_pricing_measure = null;
        $acp_pricing_measure_get_method = $acp_class_settings->acp_get_setting('product_unit_pricing_measure', 'attr');

        if($acp_pricing_measure_get_method === 'attr')
            $acp_pricing_measure = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_unit_pricing_measure', ''));
        else if($acp_pricing_measure_get_method === 'meta')
            $acp_pricing_measure = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_unit_pricing_measure', ''));

        /**
         * Filter to modify ACP: unit_pricing_measure value
         *
         * @param mixed $acp_pricing_measure - Current unit_pricing_measure value
         * @param string $acp_pricing_measure_get_method - Method used to get the unit_pricing_measure value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_unit_pricing_measure', $acp_pricing_measure, $acp_pricing_measure_get_method, $product);
    }

    /**
     * Get ACP: base_measure
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - base_measure for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_base_measure($product){
        global $acp_class_settings;

        $acp_base_measure = null;
        $acp_base_measure_get_method = $acp_class_settings->acp_get_setting('product_base_measure', 'attr');

        if($acp_base_measure_get_method === 'attr')
            $acp_base_measure = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_base_measure', ''));
        else if($acp_base_measure_get_method === 'meta')
            $acp_base_measure = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_base_measure', ''));

        /**
         * Filter to modify ACP: base_measure value
         *
         * @param mixed $acp_base_measure - Current base_measure value
         * @param string $acp_base_measure_get_method - Method used to get the base_measure value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_base_measure', $acp_base_measure, $acp_base_measure_get_method, $product);
    }

    /**
     * Get ACP: pricing_trend
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - pricing_trend for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_pricing_trend($product){
        global $acp_class_settings;

        $acp_pricing_trend = null;
        $acp_pricing_trend_get_method = $acp_class_settings->acp_get_setting('product_pricing_trend', 'attr');

        if($acp_pricing_trend_get_method === 'attr')
            $acp_pricing_trend = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_pricing_trend', ''));
        else if($acp_pricing_trend_get_method === 'meta')
            $acp_pricing_trend = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_pricing_trend', ''));

        /**
         * Filter to modify ACP: pricing_trend value
         *
         * @param mixed $acp_pricing_trend - Current pricing_trend value
         * @param string $acp_pricing_trend_get_method - Method used to get the pricing_trend value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_pricing_trend', $acp_pricing_trend, $acp_pricing_trend_get_method, $product);
    }

    /**
     * Get ACP: availability
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - availability for the product item
     * @since 1.0.0
     */
    public static function acp_get_availability($product){ 
        global $acp_class_settings;

        $acp_availability = 'out_of_stock';
        $acp_availability_get_method = $acp_class_settings->acp_get_setting('product_availability', 'woo');

        if($acp_availability_get_method === 'woo'){
            if($product->managing_stock()){
                $acp_inventory_quantity = $product->get_stock_quantity();

                if($acp_inventory_quantity > 0)
                    $acp_availability = 'in_stock';

                if($product->backorders_allowed())
                    $acp_availability = 'in_stock';
            }else{
                $stock_status = $product->get_stock_status();

                if($stock_status === 'instock')
                    $acp_availability = 'in_stock';
                else if($stock_status === 'onbackorder')
                    $acp_availability = 'in_stock';
            }
        }else if($acp_availability_get_method === 'attr'){
            $acp_availability = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_availability', ''));
        }else if($acp_availability_get_method === 'meta'){
            $acp_availability = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_availability', ''));
        }

        /**
         * Filter to modify ACP: availability value
         *
         * @param mixed $acp_availability - Current availability value
         * @param string $acp_availability_get_method - Method used to get the availability value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return lowercase string, allowed: 'in_stock', 'out_of_stock', 'preorder'
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_availability', $acp_availability, $acp_availability_get_method, $product);
    }

    /**
     * Get ACP: availability_date
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - availability_date for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_availability_date($product){
        global $acp_class_settings;

        $acp_availability_date = null;
        $acp_availability_date_get_method = $acp_class_settings->acp_get_setting('product_availability_date', 'attr');

        if($acp_availability_date_get_method === 'attr')
            $acp_availability_date = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_availability_date', ''));
        else if($acp_availability_date_get_method === 'meta')
            $acp_availability_date = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_availability_date', ''));

        /**
         * Filter to modify ACP: availability_date value
         *
         * @param mixed $acp_availability_date - Current availability_date value
         * @param string $acp_availability_date_get_method - Method used to get the availability_date value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "YYYY-MM-DD"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_availability_date', $acp_availability_date, $acp_availability_date_get_method, $product);
    }

    /**
     * Get ACP: inventory_quantity
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - inventory_quantity for the product item
     * @since 1.0.0
     */
    public static function acp_get_inventory_quantity($product){
        global $acp_class_settings;

        $acp_inventory_quantity = 0;
        $acp_inventory_quantity_get_method = $acp_class_settings->acp_get_setting('product_inventory_quantity', 'attr');

        if($acp_inventory_quantity_get_method === 'woo'){
            if($product->managing_stock()){
                $acp_product_quantity = $product->get_stock_quantity();

                if($acp_product_quantity > 0)
                    $acp_inventory_quantity = $acp_product_quantity;

                if($product->backorders_allowed())
                    $acp_inventory_quantity = 1;
            }else{
                $stock_status = $product->get_stock_status();

                if($stock_status === 'instock')
                    $acp_inventory_quantity = 1;
                else if($stock_status === 'onbackorder')
                    $acp_inventory_quantity = 1;
            }
        }else if($acp_inventory_quantity_get_method === 'attr')
            $acp_inventory_quantity = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_inventory_quantity', ''));
        else if($acp_inventory_quantity_get_method === 'meta')
            $acp_inventory_quantity = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_inventory_quantity', ''));

        /**
         * Filter to modify ACP: inventory_quantity value
         *
         * @param mixed $acp_inventory_quantity - Current inventory_quantity value
         * @param string $acp_inventory_quantity_get_method - Method used to get the inventory_quantity value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return non-negative numeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_inventory_quantity', $acp_inventory_quantity, $acp_inventory_quantity_get_method, $product);
    }

    /**
     * Get ACP: expiration_date
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - expiration_date for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_expiration_date($product){
        global $acp_class_settings;

        $acp_expiration_date = null;
        $acp_expiration_date_get_method = $acp_class_settings->acp_get_setting('product_expiration_date', 'attr');

        if($acp_expiration_date_get_method === 'attr')
            $acp_expiration_date = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_expiration_date', ''));
        else if($acp_expiration_date_get_method === 'meta')
            $acp_expiration_date = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_expiration_date', ''));

        /**
         * Filter to modify ACP: expiration_date value
         *
         * @param mixed $acp_expiration_date - Current expiration_date value
         * @param string $acp_expiration_date_get_method - Method used to get the expiration_date value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "YYYY-MM-DD"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_expiration_date', $acp_expiration_date, $acp_expiration_date_get_method, $product);
    }

    /**
     * Get ACP: pickup_method
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - pickup_method for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_pickup_method($product){
        global $acp_class_settings;

        $acp_pickup_method = null;
        $acp_pickup_method_get_method = $acp_class_settings->acp_get_setting('product_pickup_method', 'attr');

        if($acp_pickup_method_get_method === 'attr')
            $acp_pickup_method = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_pickup_method', ''));
        else if($acp_pickup_method_get_method === 'meta')
            $acp_pickup_method = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_pickup_method', ''));

        /**
         * Filter to modify ACP: pickup_method value
         *
         * @param mixed $acp_pickup_method - Current pickup_method value
         * @param string $acp_pickup_method_get_method - Method used to get the pickup_method value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return lowercase string, allowed: 'in_stock', 'reserve', 'not_supported'
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_pickup_method', $acp_pickup_method, $acp_pickup_method_get_method, $product);
    }

    /**
     * Get ACP: pickup_sla
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - pickup_sla for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_pickup_sla($product){
        global $acp_class_settings;

        $acp_pickup_sla = null;
        $acp_pickup_sla_get_method = $acp_class_settings->acp_get_setting('product_pickup_sla', 'attr');

        if($acp_pickup_sla_get_method === 'attr')
            $acp_pickup_sla = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_pickup_sla', ''));
        else if($acp_pickup_sla_get_method === 'meta')
            $acp_pickup_sla = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_pickup_sla', ''));

        /**
         * Filter to modify ACP: pickup_sla value
         *
         * @param mixed $acp_pickup_sla - Current pickup_sla value
         * @param string $acp_pickup_sla_get_method - Method used to get the pickup_sla value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number unit"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_pickup_sla', $acp_pickup_sla, $acp_pickup_sla_get_method, $product);
    }

    /**
     * Get ACP: shipping
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return string - shipping info for the product item
     * @since 1.0.0
     */
    public static function acp_get_shipping($product){
        global $acp_class_settings;

        $acp_shipping = null;
        $acp_shipping_get_method = $acp_class_settings->acp_get_setting('product_shipping', 'woo');

        if($acp_shipping_get_method === 'woo'){
            $currency = get_woocommerce_currency();
            $base_location = wc_get_base_location();
            $base_country = $base_location['country'] ?? '';

            $zones_data = WC_Shipping_Zones::get_zones();
            $zones = array_map(static function($z){ return new WC_Shipping_Zone($z['zone_id']); }, $zones_data);
            $zones[] = new WC_Shipping_Zone(0);

            $continents = function_exists('wc') && wc()->countries ? wc()->countries->get_continents() : [];

            $segments = [];

            foreach($zones as $zone){
                $locations = $zone->get_zone_locations();
                $methods   = $zone->get_shipping_methods(true);

                $pairs = [];

                if(empty($locations) && $base_country){
                    $pairs[] = [$base_country, $base_country];
                }else{
                    foreach($locations as $loc){
                        switch($loc->type){
                            case 'country':
                                $loc_country = $loc->code;
                                if($loc_country) $pairs[] = [$loc_country, $loc_country];
                                break;

                            case 'state':
                                $parts = explode(':', $loc->code, 2);
                                $loc_country = $parts[0] ?? '';
                                $loc_state = $parts[1] ?? '';
                                if($loc_country) $pairs[] = [$loc_country, $loc_state ?: $loc_country];
                                break;

                            case 'continent':
                                $continent = $continents[$loc->code] ?? null;
                                if($continent && !empty($continent['countries'])){
                                    foreach($continent['countries'] as $continent_country){
                                        $pairs[] = [$continent_country, $continent_country];
                                    }
                                }
                                break;

                            case 'postcode':
                            default:
                                break;
                        }
                    }
                }

                // Remove deduplicates
                $pairs = array_values(array_unique(array_map(static function($p){ return implode('|', $p); }, $pairs)));
                $pairs = array_map(static function($s){ return explode('|', $s, 2); }, $pairs);

                foreach($methods as $method){
                    if(empty($method) || (isset($method->enabled) && $method->enabled !== 'yes'))
                        continue;

                    $label = method_exists($method, 'get_title') ? (string) $method->get_title() : '';
                    if($label === ''){
                        $label = $method->title ?? $method->method_title ?? __('Shipping', 'advanced-crm-for-woocommerce');
                    }

                    // Try to get shipping cost from WooCommerce and other plugins
                    $raw_cost = null;
                    if(method_exists($method, 'get_option'))
                        $raw_cost = $method->get_option('cost', null);
                    elseif(isset($method->cost))
                        $raw_cost = $method->cost;

                    if($raw_cost === null || $raw_cost === ''){
                        if(isset($method->id) && $method->id === 'free_shipping'){
                            $raw_cost = '0';
                        }else{
                            foreach(['price','amount','rate','min_cost','rule_cost'] as $k){
                                $v = method_exists($method, 'get_option') ? $method->get_option($k, null) : null;
                                if($v !== null && $v !== '' ){ $raw_cost = $v; break; }
                            }
                        }
                    }
                    
                    if(is_numeric($raw_cost)){
                        $cost = (float) $raw_cost;
                    }else{
                        $num  = preg_replace('/[^0-9.\-]/', '', (string) $raw_cost);
                        $cost = ($num === '' ? 0.0 : (float) $num);
                    }
                    $price = number_format($cost, 2, '.', '') . ' ' . $currency;

                    foreach($pairs as [$loc_country, $loc_state]){
                        $segments[] = sprintf('%s:%s:%s:%s', $loc_country, $loc_state, $label, $price);
                    }
                }
            }

            $acp_shipping = implode(';', $segments);
        }else if($acp_shipping_get_method === 'attr'){
            $acp_shipping = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_shipping', ''));
        }else if($acp_shipping_get_method === 'meta'){
            $acp_shipping = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_shipping', ''));
        }

        /**
         * Filter to modify ACP: shipping value
         *
         * @param mixed $acp_shipping - Current shipping value
         * @param string $acp_shipping_get_method - Method used to get the shipping value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "country:region:label:price;...", price in "number currency" format
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_shipping', $acp_shipping, $acp_shipping_get_method, $product);
    }

    /**
     * Get ACP: delivery_estimate
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - delivery_estimate for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_delivery_estimate($product){
        global $acp_class_settings;

        $acp_delivery_estimate = null;
        $acp_delivery_estimate_get_method = $acp_class_settings->acp_get_setting('product_delivery_estimate', 'attr');

        if($acp_delivery_estimate_get_method === 'attr')
            $acp_delivery_estimate = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_delivery_estimate', ''));
        else if($acp_delivery_estimate_get_method === 'meta')
            $acp_delivery_estimate = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_delivery_estimate', ''));

        /**
         * Filter to modify ACP: delivery_estimate value
         *
         * @param mixed $acp_delivery_estimate - Current delivery_estimate value
         * @param string $acp_delivery_estimate_get_method - Method used to get the delivery_estimate value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "YYYY-MM-DD"
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_delivery_estimate', $acp_delivery_estimate, $acp_delivery_estimate_get_method, $product);
    }

    /**
     * Get ACP: seller_name
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - seller_name for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_seller_name($product){
        global $acp_class_settings;

        $acp_seller_name = null;
        $acp_seller_name_get_method = $acp_class_settings->acp_get_setting('product_seller_name', 'static');

        if($acp_seller_name_get_method === 'static')
            $acp_seller_name = $acp_class_settings->acp_get_setting('custom_key_product_seller_name', get_bloginfo('name'));
        else if($acp_seller_name_get_method === 'attr')
            $acp_seller_name = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_name', ''));
        else if($acp_seller_name_get_method === 'meta')
            $acp_seller_name = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_name', ''));

        /**
         * Filter to modify ACP: seller_name value
         *
         * @param mixed $acp_seller_name - Current seller_name value
         * @param string $acp_seller_name_get_method - Method used to get the seller_name value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_seller_name', $acp_seller_name, $acp_seller_name_get_method, $product);
    }

    /**
     * Get ACP: seller_url
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - seller_url for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_seller_url($product){
        global $acp_class_settings;

        $acp_seller_url = null;
        $acp_seller_url_get_method = $acp_class_settings->acp_get_setting('product_seller_url', 'static');

        if($acp_seller_url_get_method === 'static')
            $acp_seller_url = $acp_class_settings->acp_get_setting('custom_key_product_seller_url', get_home_url());
        else if($acp_seller_url_get_method === 'attr')
            $acp_seller_url = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_url', ''));
        else if($acp_seller_url_get_method === 'meta')
            $acp_seller_url = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_url', ''));

        /**
         * Filter to modify ACP: seller_url value
         *
         * @param mixed $acp_seller_url - Current seller_url value
         * @param string $acp_seller_url_get_method - Method used to get the seller_url value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_seller_url', $acp_seller_url, $acp_seller_url_get_method, $product);
    }

    /**
     * Get ACP: seller_privacy_policy
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - seller_privacy_policy for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_seller_privacy_policy($product){
        global $acp_class_settings;

        $acp_seller_privacy_policy = null;
        $acp_seller_privacy_policy_get_method = $acp_class_settings->acp_get_setting('product_seller_privacy_policy', 'static');

        if($acp_seller_privacy_policy_get_method === 'static')
            $acp_seller_privacy_policy = $acp_class_settings->acp_get_setting('custom_key_product_seller_privacy_policy', '');
        else if($acp_seller_privacy_policy_get_method === 'attr')
            $acp_seller_privacy_policy = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_privacy_policy', ''));
        else if($acp_seller_privacy_policy_get_method === 'meta')
            $acp_seller_privacy_policy = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_privacy_policy', ''));

        /**
         * Filter to modify ACP: seller_privacy_policy value
         *
         * @param mixed $acp_seller_privacy_policy - Current seller_privacy_policy value
         * @param string $acp_seller_privacy_policy_get_method - Method used to get the seller_privacy_policy value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_seller_privacy_policy', $acp_seller_privacy_policy, $acp_seller_privacy_policy_get_method, $product);
    }

    /**
     * Get ACP: seller_tos
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - seller_tos for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_seller_tos($product){
        global $acp_class_settings;

        $acp_seller_tos = null;
        $acp_seller_tos_get_method = $acp_class_settings->acp_get_setting('product_seller_tos', 'static');

        if($acp_seller_tos_get_method === 'static')
            $acp_seller_tos = $acp_class_settings->acp_get_setting('custom_key_product_seller_tos', '');
        else if($acp_seller_tos_get_method === 'attr')
            $acp_seller_tos = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_tos', ''));
        else if($acp_seller_tos_get_method === 'meta')
            $acp_seller_tos = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_seller_tos', ''));

        /**
         * Filter to modify ACP: seller_tos value
         *
         * @param mixed $acp_seller_tos - Current seller_tos value
         * @param string $acp_seller_tos_get_method - Method used to get the seller_tos value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_seller_tos', $acp_seller_tos, $acp_seller_tos_get_method, $product);
    }

    /**
     * Get ACP: return_policy
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - return_policy for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_return_policy($product){
        global $acp_class_settings;

        $acp_return_policy = null;
        $acp_return_policy_get_method = $acp_class_settings->acp_get_setting('product_return_policy', 'attr');

        if($acp_return_policy_get_method === 'static')
            $acp_return_policy = $acp_class_settings->acp_get_setting('custom_key_product_return_policy', '');
        else if($acp_return_policy_get_method === 'attr')
            $acp_return_policy = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_return_policy', ''));
        else if($acp_return_policy_get_method === 'meta')
            $acp_return_policy = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_return_policy', ''));

        /**
         * Filter to modify ACP: return_policy value
         *
         * @param mixed $acp_return_policy - Current return_policy value
         * @param string $acp_return_policy_get_method - Method used to get the return_policy value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_return_policy', $acp_return_policy, $acp_return_policy_get_method, $product);
    }

    /**
     * Get ACP: return_window
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - return_window for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_return_window($product){
        global $acp_class_settings;

        $acp_return_window = null;
        $acp_return_window_get_method = $acp_class_settings->acp_get_setting('product_return_window', 'attr');

        if($acp_return_window_get_method === 'static')
            $acp_return_window = $acp_class_settings->acp_get_setting('custom_key_product_return_window', '');
        else if($acp_return_window_get_method === 'attr')
            $acp_return_window = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_return_window', ''));
        else if($acp_return_window_get_method === 'meta')
            $acp_return_window = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_return_window', ''));

        /**
         * Filter to modify ACP: return_window value
         *
         * @param mixed $acp_return_window - Current return_window value
         * @param string $acp_return_window_get_method - Method used to get the return_window value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return non-negative numeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_return_window', $acp_return_window, $acp_return_window_get_method, $product);
    }

    /**
     * Get ACP: popularity_score
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - popularity_score for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_popularity_score($product){
        global $acp_class_settings;

        $acp_popularity_score = null;
        $acp_popularity_score_get_method = $acp_class_settings->acp_get_setting('product_popularity_score', 'attr');

        if($acp_popularity_score_get_method === 'attr')
            $acp_popularity_score = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_popularity_score', ''));
        else if($acp_popularity_score_get_method === 'meta')
            $acp_popularity_score = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_popularity_score', ''));

        /**
         * Filter to modify ACP: popularity_score value
         *
         * @param mixed $acp_popularity_score - Current popularity_score value
         * @param string $acp_popularity_score_get_method - Method used to get the popularity_score value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return numeric string, integer or float in the range 0-5
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_popularity_score', $acp_popularity_score, $acp_popularity_score_get_method, $product);
    }

    /**
     * Get ACP: return_rate
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - return_rate for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_return_rate($product){
        global $acp_class_settings;

        $acp_return_rate = null;
        $acp_return_rate_get_method = $acp_class_settings->acp_get_setting('product_return_rate', 'attr');

        if($acp_return_rate_get_method === 'attr')
            $acp_return_rate = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_return_rate', ''));
        else if($acp_return_rate_get_method === 'meta')
            $acp_return_rate = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_return_rate', ''));

        /**
         * Filter to modify ACP: return_rate value
         *
         * @param mixed $acp_return_rate - Current return_rate value
         * @param string $acp_return_rate_get_method - Method used to get the return_rate value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string in the format "number%" in the range 0%-100%
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_return_rate', $acp_return_rate, $acp_return_rate_get_method, $product);
    }

    /**
     * Get ACP: warning
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - warning for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_warning($product){
        global $acp_class_settings;

        $acp_warning = null;
        $acp_warning_get_method = $acp_class_settings->acp_get_setting('product_warning', 'attr');

        if($acp_warning_get_method === 'attr')
            $acp_warning = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_warning', ''));
        else if($acp_warning_get_method === 'meta')
            $acp_warning = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_warning', ''));

        /**
         * Filter to modify ACP: warning value
         *
         * @param mixed $acp_warning - Current warning value
         * @param string $acp_warning_get_method - Method used to get the warning value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_warning', $acp_warning, $acp_warning_get_method, $product);
    }

    /**
     * Get ACP: warning_url
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - warning_url for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_warning_url($product){
        global $acp_class_settings;

        $acp_warning_url = null;
        $acp_warning_url_get_method = $acp_class_settings->acp_get_setting('product_warning_url', 'attr');

        if($acp_warning_url_get_method === 'attr')
            $acp_warning_url = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_warning_url', ''));
        else if($acp_warning_url_get_method === 'meta')
            $acp_warning_url = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_warning_url', ''));

        /**
         * Filter to modify ACP: warning_url value
         *
         * @param mixed $acp_warning_url - Current warning_url value
         * @param string $acp_warning_url_get_method - Method used to get the warning_url value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_warning_url', $acp_warning_url, $acp_warning_url_get_method, $product);
    }

    /**
     * Get ACP: age_restriction
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - age_restriction for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_age_restriction($product){
        global $acp_class_settings;

        $acp_age_restriction = null;
        $acp_age_restriction_get_method = $acp_class_settings->acp_get_setting('product_age_restriction', 'attr');

        if($acp_age_restriction_get_method === 'static')
            $acp_age_restriction = $acp_class_settings->acp_get_setting('custom_key_product_age_restriction', '');
        else if($acp_age_restriction_get_method === 'attr')
            $acp_age_restriction = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_age_restriction', ''));
        else if($acp_age_restriction_get_method === 'meta')
            $acp_age_restriction = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_age_restriction', ''));

        /**
         * Filter to modify ACP: age_restriction value
         *
         * @param mixed $acp_age_restriction - Current age_restriction value
         * @param string $acp_age_restriction_get_method - Method used to get the age_restriction value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return non-negative numeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_age_restriction', $acp_age_restriction, $acp_age_restriction_get_method, $product);
    }

    /**
     * Get ACP: product_review_count
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - product_review_count for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_product_review_count($product){
        global $acp_class_settings;

        $acp_review_count = null;
        $acp_review_count_get_method = $acp_class_settings->acp_get_setting('product_review_count', 'woo');

        if($acp_review_count_get_method === 'woo'){
            $acp_review_count = $product->get_review_count();

            if($acp_review_count == 0)
                $acp_review_count = "";
        }else if($acp_review_count_get_method === 'attr'){
            $acp_review_count = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_review_count', ''));
        }else if($acp_review_count_get_method === 'meta'){
            $acp_review_count = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_review_count', ''));
        }

        /**
         * Filter to modify ACP: product_review_count value
         *
         * @param mixed $acp_review_count - Current product_review_count value
         * @param string $acp_review_count_get_method - Method used to get the product_review_count value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return non-negative numeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_product_review_count', $acp_review_count, $acp_review_count_get_method, $product);
    }

    /**
     * Get ACP: product_review_rating
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - product_review_rating for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_product_review_rating($product){
        global $acp_class_settings;

        $acp_review_rating = null;
        $acp_review_rating_get_method = $acp_class_settings->acp_get_setting('product_review_rating', 'woo');

        if($acp_review_rating_get_method === 'woo'){
            $acp_review_rating = $product->get_average_rating();

            if($acp_review_rating == 0)
                $acp_review_rating = "";
        }else if($acp_review_rating_get_method === 'attr'){
            $acp_review_rating = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_review_rating', ''));
        }else if($acp_review_rating_get_method === 'meta'){
            $acp_review_rating = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_review_rating', ''));
        }

        /**
         * Filter to modify ACP: product_review_rating value
         *
         * @param mixed $acp_review_rating - Current product_review_rating value
         * @param string $acp_review_rating_get_method - Method used to get the product_review_rating value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return numeric string, integer or float in the range 0-5
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_product_review_rating', $acp_review_rating, $acp_review_rating_get_method, $product);
    }

    /**
     * Get ACP: store_review_count
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - store_review_count for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_store_review_count($product){
        global $acp_class_settings;

        $acp_store_review_count = null;
        $acp_store_review_count_get_method = $acp_class_settings->acp_get_setting('product_store_review_count', 'attr');

        if($acp_store_review_count_get_method === 'static'){
            $acp_store_review_count = $acp_class_settings->acp_get_setting('custom_key_product_store_review_count', '');
        }else if($acp_store_review_count_get_method === 'attr'){
            $acp_store_review_count = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_store_review_count', ''));
        }else if($acp_store_review_count_get_method === 'meta'){
            $acp_store_review_count = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_store_review_count', ''));
        }

        /**
         * Filter to modify ACP: store_review_count value
         *
         * @param mixed $acp_store_review_count - Current store_review_count value
         * @param string $acp_store_review_count_get_method - Method used to get the store_review_count value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return non-negative numeric string or integer
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_store_review_count', $acp_store_review_count, $acp_store_review_count_get_method, $product);
    }

    /**
     * Get ACP: store_review_rating
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - store_review_rating for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_store_review_rating($product){
        global $acp_class_settings;

        $acp_store_review_rating = null;
        $acp_store_review_rating_get_method = $acp_class_settings->acp_get_setting('product_store_review_rating', 'attr');

        if($acp_store_review_rating_get_method === 'static'){
            $acp_store_review_rating = $acp_class_settings->acp_get_setting('custom_key_product_store_review_rating', '');
        }else if($acp_store_review_rating_get_method === 'attr'){
            $acp_store_review_rating = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_store_review_rating', ''));
        }else if($acp_store_review_rating_get_method === 'meta'){
            $acp_store_review_rating = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_store_review_rating', ''));
        }

        /**
         * Filter to modify ACP: store_review_rating value
         *
         * @param mixed $acp_store_review_rating - Current store_review_rating value
         * @param string $acp_store_review_rating_get_method - Method used to get the store_review_rating value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return numeric string, integer or float in the range 0-5
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_store_review_rating', $acp_store_review_rating, $acp_store_review_rating_get_method, $product);
    }

    /**
     * Get ACP: q_and_a
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - q_and_a for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_q_and_a($product){
        global $acp_class_settings;

        $acp_q_and_a = null;
        $acp_q_and_a_get_method = $acp_class_settings->acp_get_setting('product_q_and_a', 'attr');

        if($acp_q_and_a_get_method === 'attr')
            $acp_q_and_a = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_q_and_a', ''));
        else if($acp_q_and_a_get_method === 'meta')
            $acp_q_and_a = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_q_and_a', ''));

        /**
         * Filter to modify ACP: q_and_a value
         *
         * @param mixed $acp_q_and_a - Current q_and_a value
         * @param string $acp_q_and_a_get_method - Method used to get the q_and_a value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string, plain text in the format "Q: question? A: answer."
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_q_and_a', $acp_q_and_a, $acp_q_and_a_get_method, $product);
    }

    /**
     * Get ACP: raw_review_data
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - raw_review_data for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_raw_review_data($product){
        global $acp_class_settings;

        $acp_raw_review_data = null;
        $acp_raw_review_data_get_method = $acp_class_settings->acp_get_setting('product_raw_review_data', 'attr');

        if($acp_raw_review_data_get_method === 'woo'){
            $reviews = get_comments([
                'post_id' => $product->get_id(),
                'status' => 'approve',
                'type' => 'review',
            ]);

            $raw_data = [];

            foreach($reviews as $review){
                $raw_data[] = [
                    'author' => $review->comment_author,
                    'date' => $review->comment_date,
                    'rating' => get_comment_meta($review->comment_ID, 'rating', true),
                    'content' => $review->comment_content,
                ];
            }

            if(!empty($raw_data))
                $acp_raw_review_data = json_encode($raw_data);
        }else if($acp_raw_review_data_get_method === 'attr')
            $acp_raw_review_data = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_raw_review_data', ''));
        else if($acp_raw_review_data_get_method === 'meta')
            $acp_raw_review_data = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_raw_review_data', ''));

        /**
         * Filter to modify ACP: raw_review_data value
         *
         * @param mixed $acp_raw_review_data - Current raw_review_data value
         * @param string $acp_raw_review_data_get_method - Method used to get the raw_review_data value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string, may be JSON encoded
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_raw_review_data', $acp_raw_review_data, $acp_raw_review_data_get_method, $product);
    }

    /** 
     * Get ACP: related_ids
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - related_ids for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_related_ids($product){
        global $acp_class_settings;

        $related_ids = [];
        $acp_related_ids_get_method = $acp_class_settings->acp_get_setting('product_related_product_id', 'crosssell');
        $related_ids_tmp = [];

        if($acp_related_ids_get_method === 'crosssell'){
            $related_ids_tmp = $product->get_cross_sell_ids();
        }else if($acp_related_ids_get_method === 'upsell'){
            $related_ids_tmp = $product->get_upsell_ids();
        }else if($acp_related_ids_get_method === 'both'){
            $related_ids_tmp = array_merge($product->get_cross_sell_ids(), $product->get_upsell_ids());
            $related_ids_tmp = array_unique($related_ids_tmp);
        }

        if(!empty($related_ids_tmp)){
            if(count($related_ids_tmp) > 20)
                $related_ids_tmp = array_slice($related_ids_tmp, 0, 20);
        
            foreach($related_ids_tmp as $key => $related_id){
                $related_product = wc_get_product($related_id);

                if($related_product){
                    if(self::acp_get_enable_search($related_product)){
                        $related_ids[] = self::acp_get_id($related_product);
                    }
                }
            }

            if(!empty($related_ids)){
                $related_ids = implode(',', $related_ids);
            }
        }

        /**
         * Filter to modify ACP: related_ids value
         *
         * @param mixed $related_ids - Current related_ids value
         * @param string $acp_related_ids_get_method - Method used to get the related_ids value
         * @param mixed $related_ids - Additional context for the filter
         * @return mixed - The filter has to return string of related IDs separated by comma
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_related_ids', $related_ids, $acp_related_ids_get_method, $related_ids);
    }

    /**
     * Get ACP: relationship_type
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - relationship_type for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_relationship_type($product){
        global $acp_class_settings;

        $relationship_type = null;
        $acp_relationship_type_get_method = $acp_class_settings->acp_get_setting('product_relationship_type', 'attr');

        if($acp_relationship_type_get_method === 'attr')
            $relationship_type = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_relationship_type', ''));
        else if($acp_relationship_type_get_method === 'meta')
            $relationship_type = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_relationship_type', ''));

        /**
         * Filter to modify ACP: relationship_type value
         *
         * @param mixed $relationship_type - Current relationship_type value
         * @param string $acp_relationship_type_get_method - Method used to get the relationship_type value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return lowercase string, allowed: 'part_of_set', 'required_part', 'ofter_bought_with', 'substitute', 'different_brand', 'accessory'
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_relationship_type', $relationship_type, $acp_relationship_type_get_method, $product);
    }

    /**
     * Get ACP: geo_price
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - geo_price for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_geo_price($product){
        global $acp_class_settings;

        $geo_price = null;
        $acp_geo_price_get_method = $acp_class_settings->acp_get_setting('product_geo_price', 'attr');

        if($acp_geo_price_get_method === 'attr')
            $geo_price = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_geo_price', ''));
        else if($acp_geo_price_get_method === 'meta')
            $geo_price = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_geo_price', ''));

        /**
         * Filter to modify ACP: geo_price value
         *
         * @param mixed $geo_price - Current geo_price value
         * @param string $acp_geo_price_get_method - Method used to get the geo_price value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_geo_price', $geo_price, $acp_geo_price_get_method, $product);
    
    }
    
    /**
     * Get ACP: geo_availability
     *
     * @param WC_Product $product - WooCommerce product object to get it from
     * @return mixed - geo_availability for the product item or null if not found
     * @since 1.0.0
     */
    public static function acp_get_geo_availability($product){
        global $acp_class_settings;

        $geo_availability = null;
        $acp_geo_availability_get_method = $acp_class_settings->acp_get_setting('product_geo_availability', 'attr');

        if($acp_geo_availability_get_method === 'attr')
            $geo_availability = self::acp_get_attribute($product, $acp_class_settings->acp_get_setting('custom_key_product_geo_availability', ''));
        else if($acp_geo_availability_get_method === 'meta')
            $geo_availability = self::acp_get_meta($product, $acp_class_settings->acp_get_setting('custom_key_product_geo_availability', ''));

        /**
         * Filter to modify ACP: geo_availability value
         *
         * @param mixed $geo_availability - Current geo_availability value
         * @param string $acp_geo_availability_get_method - Method used to get the geo_availability value
         * @param WC_Product $product - WooCommerce product object being processed
         * @return mixed - The filter has to return string
         * @since 1.0.0
         */
        return apply_filters('acp_feed_attribute_geo_availability', $geo_availability, $acp_geo_availability_get_method, $product);
    }

    /**
     * Write ACP element to XML
     *
     * @param XMLWriter $xml_writer - XMLWriter object to write to
     * @param string $name - Element name
     * @param mixed $val - Element value
     * @since 1.0.0
     */
    public static function acp_element($xml_writer, $name, $val){
        if ($val === null || $val === '')
            return;
        
        if(gettype($val) !== 'string')
            return;

        $xml_writer->startElement($name);
        $xml_writer->text($val);
        $xml_writer->endElement();
    }

}
