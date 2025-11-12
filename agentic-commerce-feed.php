<?php
/*
 * Plugin Name:       Agentic Commerce Feed For WooCommerce
 * Description:       Export your products to a feed compatible with the Agentic Commerce Protocol by OpenAI. Show your products in ChatGPT's Instant Checkout.
 * Version:           1.0.0
 * Text Domain:       acp-feed-woocommerce
 * Domain Path:       /languages
 * Requires at least: 6.8
 * Requires PHP: 	  8.1
 * Author:            tomeckiStudio
 * Author URI:        https://tomecki.studio
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires Plugins:  woocommerce
 *
 *
 * ==================== LICENSE ====================
 * Copyright (C) 2025  tomeckiStudio (https://tomecki.studio)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ADDITIONAL TERMS per GNU GPL Section 7
 * The origin of the Program MUST NOT be misrepresented; you MUST NOT claim that you wrote the original Program. 
 * Altered source versions MUST be plainly marked as such, and MUST NOT be misrepresented as being the original Program.
 * 
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE 
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/gpl-3.0.html>.
 * 
 */

defined('ABSPATH') or die('Suspicious activities detected!');

define('ACP_DIR', __DIR__);
define('ACP_PATH', plugin_dir_url(__FILE__));
define('ACP_VERSION', "1.0.0");
//define('ACP_FEED_ALWAYS_LOG', true); // uncomment to force logging

require_once ACP_DIR . '/includes/class-acp-feed-builder.php';
require_once ACP_DIR . '/includes/class-acp-feed.php';
require_once ACP_DIR . '/includes/class-acp-feed-tools.php';
require_once ACP_DIR . '/includes/class-acp-feed-settings.php';
require_once ACP_DIR . '/includes/class-acp-feed-scheduler.php';
require_once ACP_DIR . '/includes/class-acp-feed-dbhelper.php';

$GLOBALS['acp_class_settings'] = new ACP_Feed_Settings();
ACP_Feed_Scheduler::init();

if(is_admin()){
    global $acp_class_settings;
    $acp_class_settings->acp_init();

    ACP_Feed_Tools::init();
}

// Hook to process batch
add_action(ACP_Feed_Scheduler::HOOK_PROCESS_BATCH, ['ACP_Feed', 'process_batch'], 10, 2);

add_action('init', function(){
    global $acp_class_settings;
    
    load_plugin_textdomain('acp-feed-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    
    $cron_minutes = max(1, (int)$acp_class_settings->acp_get_setting('cron_interval_minutes', 30));
    add_filter('cron_schedules', function ($schedules) use ($cron_minutes){
        $schedules['acp_feed_cron'] = [
            'interval' => $cron_minutes * 60,
            'display'  => "ACP Feed every {$cron_minutes} minutes",
        ];
        return $schedules;
    });

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links){
        $url = admin_url('admin.php?page=acp-feed-settings'); 

        $action_links = array();
        $action_links[] = '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'acp-feed-woocommerce') . '</a>';
        $action_links[] = '<a href="https://github.com/tomeckiStudio/agentic-commerce-feed">' . esc_html__('GitHub', 'acp-feed-woocommerce') . '</a>';

		return array_merge($action_links, $links);
    });
});

register_activation_hook(__FILE__, function(){
    global $acp_class_settings;

    if(empty($acp_class_settings->acp_get_setting('server_cron_key'))){
        $acp_class_settings->acp_save_setting('server_cron_key', wp_generate_password(24, false, false));
    }

    ACP_Feed_DBHelper::acp_create_tables();
    ACP_Feed_Scheduler::acp_reschedule();
});

register_deactivation_hook(__FILE__, function(){
    ACP_Feed_DBHelper::acp_drop_tables();
    ACP_Feed_Scheduler::acp_clear();
});

function acp_log($msg){
    global $acp_class_settings;

    if(gettype($msg) != "string")
        $msg = json_encode($msg);

    try{
        $log_method_enable = $acp_class_settings->acp_get_setting('log_enable', false);
        $log_method = $acp_class_settings->acp_get_setting('log_method', 'custom');

        if($log_method_enable || (defined('WP_DEBUG') && WP_DEBUG) || (defined('ACP_FEED_ALWAYS_LOG') && ACP_FEED_ALWAYS_LOG)){
            if((defined('ACP_FEED_ALWAYS_LOG') && ACP_FEED_ALWAYS_LOG) && $log_method === 'nolog')
                $log_method = 'custom';

            if($log_method === 'woocommerce'){
                if(function_exists('wc_get_logger')){
                    $logger = wc_get_logger();
                    $context = ['source' => 'acp-feed-woocommerce'];
                    $logger->error($msg, $context);
                }else{
                    error_log('[ACP Feed] ' . $msg);
                }
            }else if($log_method === 'custom'){
                $msg = "[(ACP Feed) " . date("d/m/Y, H:i") . "] ". $msg . PHP_EOL;

                file_put_contents(trailingslashit(ABSPATH) . "/acp_feed.log", $msg, FILE_APPEND);
            }else{
                error_log('[ACP Feed] ' . $msg);
            }
        }
    } catch (Throwable $e) {
        error_log('[ACP Feed] ' . $msg);
    }
}

