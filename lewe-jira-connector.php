<?php

/**
 * Lewe Jira Connector
 *
 * @package           Lewe Jira Connector
 * @author            George Lewe
 * @copyright         2021-2023 George Lewe
 * @link              https://www.lewe.com
 * @license           GPLv3
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Lewe Jira Connector
 * Plugin URI:        https://lewe.gitbook.io/lewe-jira-connector-for-wordpress/
 * Description:       The Lewe Jira Connector plugin connects to a Jira host (Server, Data Center or Cloud) and displays information from there on your WordPress site.
 * Version:           2.7.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            George Lewe
 * Author URI:        https://www.lewe.com
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.txt
 * Text Domain:       lewe-jira-connector
 * Domain Path:       /languages
 */

// ----------------------------------------------------------------------------
/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
  die;
}

// ----------------------------------------------------------------------------
/**
 * Global static variables
 */
define('LEWE_JIRA_CONNECTOR_NAME', 'Lewe Jira Connector');
define('LEWE_JIRA_CONNECTOR_SLUG', 'lewe-jira-connector');
define('LEWE_JIRA_CONNECTOR_PREFIX', 'jco');
define('LEWE_JIRA_CONNECTOR_VERSION', '2.7.3');
define('LEWE_JIRA_CONNECTOR_AUTHOR', 'George Lewe');
define('LEWE_JIRA_CONNECTOR_AUTHOR_URI', 'https://www.lewe.com');
define('LEWE_JIRA_CONNECTOR_DOC_URI', 'https://lewe.gitbook.io/lewe-jira-connector-for-wordpress/');
define('LEWE_JIRA_CONNECTOR_DOC_LIC_URI', 'https://lewe.gitbook.io/lewe-jira-connector-for-wordpress/license');
define('LEWE_JIRA_CONNECTOR_SUPPORT_URI', 'https://georgelewe.atlassian.net/servicedesk/customer/portal/5');
define('LEWE_JIRA_CONNECTOR_DONATION_URI', 'https://www.paypal.me/GeorgeLewe');
define('LEWE_JIRA_CONNECTOR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('LEWE_JIRA_CONNECTOR_PLUGIN_URL', plugin_dir_url(__FILE__));

// ----------------------------------------------------------------------------
/**
 * Plugin requires licensing.
 * This will enable/disable licensing features.
 */
define('LEWE_JIRA_CONNECTOR_LICENSE_REQUIRED', true);

// ----------------------------------------------------------------------------
/**
 * The code that runs during plugin activation.
 */
function lewe_jira_connector_activate() {
  require_once plugin_dir_path(__FILE__) . 'includes/class-lewe-jira-connector-activator.php';
  Lewe_Jira_Connector_Activator::activate();
}

register_activation_hook(__FILE__, 'lewe_jira_connector_activate');

// ----------------------------------------------------------------------------
/**
 * The code that runs during plugin deactivation.
 */
function lewe_jira_connector_deactivate() {
  require_once plugin_dir_path(__FILE__) . 'includes/class-lewe-jira-connector-deactivator.php';
  Lewe_Jira_Connector_Deactivator::deactivate();
}

register_deactivation_hook(__FILE__, 'lewe_jira_connector_deactivate');

// ----------------------------------------------------------------------------
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-lewe-jira-connector-plugin.php';

// ----------------------------------------------------------------------------
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off
 * the plugin from this point in the file does not affect the page life cycle.
 *
 * @since    1.0.0
 */
function lewe_jira_connector_run() {
  $plugin = new Lewe_Jira_Connector_Plugin();
  $plugin->run();
}

lewe_jira_connector_run();
