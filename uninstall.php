<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * @link       https://www.lewe.com
 * @since      1.0.0
 *
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector\Uninstall
 */

/**
 * If uninstall not called from WordPress, then exit.
 */
if (!defined('LEWE_JIRA_CONNECTOR_NAME')) exit;

/**
 * The unique name (slug) of this plugin.
 *
 * @var string  $pname  The ID of this plugin.
 */
$plugin_slug = 'lewe-jira-connector';

if (!get_option($plugin_slug . '_delete_on_uninstall_checkbox')) return false;

//
// Delete plugin options
//
$options = array(

    $plugin_slug . '_host_title_text',
    $plugin_slug . '_host_url_text',
    $plugin_slug . '_host_username_text',
    $plugin_slug . '_host_password_password',

    $plugin_slug . '_issuebutton_color_select',
    $plugin_slug . '_issuebutton_outline_checkbox',
    $plugin_slug . '_issuebutton_summary_chars_number',

    $plugin_slug . '_issuefilter_color_rows_checkbox',
    $plugin_slug . '_issuefilter_show_issuetype_checkbox',
    $plugin_slug . '_issuefilter_show_priority_checkbox',
    $plugin_slug . '_issuefilter_show_key_checkbox',
    $plugin_slug . '_issuefilter_show_summary_checkbox',
    $plugin_slug . '_issuefilter_show_reporter_checkbox',
    $plugin_slug . '_issuefilter_show_assignee_checkbox',
    $plugin_slug . '_issuefilter_show_resolution_checkbox',
    $plugin_slug . '_issuefilter_show_status_checkbox',
    $plugin_slug . '_issuefilter_startat_number',
    $plugin_slug . '_issuefilter_maxresults_number',
    $plugin_slug . '_issuefilter_showcount_checkbox',

    $plugin_slug . '_issueinline_color_select',
    $plugin_slug . '_issueinline_summary_chars_number',

    $plugin_slug . '_issuepanel_color_select',
    $plugin_slug . '_issuepanel_show_summary_checkbox',
    $plugin_slug . '_issuepanel_show_description_checkbox',
    $plugin_slug . '_issuepanel_show_lastcomment_checkbox',
    $plugin_slug . '_issuepanel_description_chars_number',

    $plugin_slug . '_license_key_text',
    $plugin_slug . '_license_status_text',

    $plugin_slug . '_statuscolor_todo_select',
    $plugin_slug . '_statuscolor_inprogress_select',
    $plugin_slug . '_statuscolor_done_select',

    $plugin_slug . '_delete_on_uninstall_checkbox',

);

foreach ($options as $option) {

    delete_option($option);
}

//
// Delete all posts of type 'jira_host'
//
$allposts = get_posts(array('post_type' => 'jira_host', 'numberposts' => -1));

foreach ($allposts as $eachpost) {

    wp_delete_post($eachpost->ID, true);
}
