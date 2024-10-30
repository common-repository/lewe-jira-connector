<?php

/**
 * Chord content type meta box
 *
 * @link       https://www.lewe.com
 * @since      1.0.0
 *
 * @package    ChordPress
 * @subpackage ChordPress/admin/partials
 */

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
  die;
}

if (!function_exists('display_jira_host_meta_box')) {
  function display_jira_host_meta_box($jira_host) {
    /**
     * Load classes and initialize variables.
     */
    $P = new Lewe_Jira_Connector_Plugin();
    $BS = new Lewe_Jira_Connector_Bootstrap('jco');
    $FA = new Lewe_Jira_Connector_Fontawesome('jco');

    $leftwidth = "40%";
    $options = $P->get_options();
    $pslug = $P->get_plugin_slug();
    $ptitle = $P->get_plugin_title();

    /**
     * Retrieve current values
     */
    $jira_host_description = sanitize_text_field(get_post_meta($jira_host->ID, 'jira_host_description', true));
    $jira_host_url = sanitize_text_field(get_post_meta($jira_host->ID, 'jira_host_url', true));
    $jira_host_username = sanitize_text_field(get_post_meta($jira_host->ID, 'jira_host_username', true));
    $jira_host_password = sanitize_text_field(get_post_meta($jira_host->ID, 'jira_host_password', true));
    $jira_host_token = sanitize_text_field(get_post_meta($jira_host->ID, 'jira_host_token', true));

    ?>

    <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
      <tbody>
      <tr>
        <th scope="row"><label for="jira_host_description"><?php echo esc_html(__('Description', 'lewe-jira-connector')); ?></label></th>
        <td>
          <input type="text" id="jira_host_description" name="jira_host_description" value="<?php echo esc_html($jira_host_description); ?>"/>
          <p class="description"><?php echo esc_html(__('Enter a description for this Jira host.', 'lewe-jira-connector')); ?></p>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="jira_host_url"><?php echo esc_html(__('URL', 'lewe-jira-connector')); ?></label></th>
        <td>
          <input type="text" name="jira_host_url" value="<?php echo esc_html($jira_host_url); ?>"/>
          <p class="description"><?php echo esc_html(__('Enter the base URL for this Jira host.', 'lewe-jira-connector')); ?></p>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="jira_host_username"><?php echo esc_html(__('Username', 'lewe-jira-connector')); ?></label></th>
        <td>
          <input type="text" name="jira_host_username" value="<?php echo esc_html($jira_host_username); ?>"/>
          <p class="description"><?php echo esc_html(__('Enter the username for this Jira host.', 'lewe-jira-connector')); ?></p>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="jira_host_password"><?php echo esc_html(__('Password', 'lewe-jira-connector')); ?></label></th>
        <td>
          <input type="password" name="jira_host_password" value="<?php echo esc_html($jira_host_password); ?>"/>
          <p class="description"><?php echo esc_html(__('Enter the password (Jira Server, Jira Data Center) or the', 'lewe-jira-connector')); ?>
            <a href="https://support.atlassian.com/atlassian-account/docs/manage-api-tokens-for-your-atlassian-account/" target="_blank"><?php echo esc_html(__('API token', 'lewe-jira-connector')); ?></a>
            <?php echo esc_html(__('(Jira Cloud) for the username you specified.', 'lewe-jira-connector')); ?>
          </p>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="jira_host_token"><?php echo esc_html(__('Personal Access Token (PAT)', 'lewe-jira-connector')); ?></label></th>
        <td>
          <input type="password" name="jira_host_token" value="<?php echo esc_html($jira_host_token); ?>"/>
          <p class="description"><?php echo esc_html(__('If this host requires a personal access token (PAT) instead of basic authentication for API calls, enter yours here. Leave this field empty for basic authentication with your username and password. Read more about ', 'lewe-jira-connector')); ?>
            <a href="https://confluence.atlassian.com/enterprise/using-personal-access-tokens-1026032365.html" target="_blank"><?php echo esc_html(__('Personal Access Tokens', 'lewe-jira-connector')); ?></a>.
          </p>
        </td>
      </tr>

      </tbody>
    </table>
    <?php
  }
}
