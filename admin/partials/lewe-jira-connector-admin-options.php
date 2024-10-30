<?php

/**
 * Plugin Options Page
 *
 * @link       https://www.lewe.com
 * @since      1.0.0
 *
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/admin/partials
 */

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
  die;
}

/**
 * Load classes and initialize variables.
 */
$P = new Lewe_Jira_Connector_Plugin();
$options = $P->get_options();
$show_alert = false;

$BS = new Lewe_Jira_Connector_Bootstrap('jco');
$FA = new Lewe_Jira_Connector_Fontawesome('jco');
$J = new Lewe_Jira_Connector_Jira();
$L = new Lewe_Jira_Connector_License();

/**
 * Get license information
 */
if ($lkey = get_option('lewe-jira-connector_license_key_text')) {

  $L->setKey($lkey);

  switch ($lstat = $L->status()) {

    case 'blocked';
      $lmsg = '<div class="jco-alert jco-alert-danger jco-mt-4">' . esc_html(__('Your Lewe Jira Connector license is blocked. Please renew your license to enable additional features.', 'lewe-jira-connector')) . '</div>';
      break;

    case 'expired';
      $lmsg = '<div class="jco-alert jco-alert-danger jco-mt-4">' . esc_html(__('Your Lewe Jira Connector license has expired more than 30 days ago. Please renew your license to enable additional features.', 'lewe-jira-connector')) . '</div>';
      break;

    case 'invalid';
      $lmsg = '<div class="jco-alert jco-alert-danger jco-mt-4">' . esc_html(__('Your Lewe Jira Connector license is invalid. Please get a valid license to enable additional features.', 'lewe-jira-connector')) . '</div>';
      break;

    case 'pending';
      $lmsg = '<div class="jco-alert jco-alert-danger jco-mt-4">' . esc_html(__('Your Lewe Jira Connector license is pending. Please activate your license to enable additional features.', 'lewe-jira-connector')) . '</div>';
      break;

    case 'unregistered';
      $lmsg = '<div class="jco-alert jco-alert-warning jco-mt-4">' . esc_html(__('Your Lewe Jira Connector license is not registered for this domain. Please activate your license to re-enable additional features.', 'lewe-jira-connector')) . '</div>';
      break;
  }
} else {

  $lmsg = '<div class="jco-alert jco-alert-info">' . esc_html(__('Please consider getting a Lewe Jira Connector license to enable additional feature.', 'lewe-jira-connector')) . '</div>';
}

/**
 * Save options
 */
if (isset($_REQUEST['submit'])) {

  $value_changed = false;
  $show_alert = false;

  foreach ($options as $option) {

    if (isset($_REQUEST[$option])) {

      $request_option = sanitize_text_field($_REQUEST[$option]);

      if (get_option($option) != $request_option) {

        $value_changed = true;
        $show_alert = true;

        if (update_option($option, $request_option)) {

          $alert_style = 'success';
        } else {

          $alert_style = 'danger';
        }
      } else {

        $show_alert = true;
      }
    } else {

      /**
       * Unchecked checkboxes will not be in the $_REQUEST array.
       * Make sure we set them to 0.
       */
      if (strpos($option, 'checkbox')) {

        update_option($option, 0);
      }
    }
  }

  /**
   * Build response message
   */
  if ($show_alert) {

    if ($value_changed) {

      if ($alert_style == 'success') {

        $alert_title = esc_html(__('Success', 'lewe-jira-connector'));
        $alert_text = esc_html(__('The changes were saved successfully.', 'lewe-jira-connector'));
      } else {

        $alert_title = esc_html(__('Error', 'lewe-jira-connector'));
        $alert_text = esc_html(__('An error occurred while trying to save your settings.', 'lewe-jira-connector'));
      }
    } else {

      $alert_style = 'info';
      $alert_title = esc_html(__('Information', 'lewe-jira-connector'));
      $alert_text = esc_html(__('Nothing was saved because no value was changed.', 'lewe-jira-connector'));
    }
  }
}

?>

<div class="jco-options">

  <div class="jco-options-header">
    <img src="<?php echo esc_html(LEWE_JIRA_CONNECTOR_PLUGIN_URL) . 'global/images/icon-80x80.png'; ?>" alt="" class="jco-options-header-icon">
    <div class="jco-options-header-title"><?php echo LEWE_JIRA_CONNECTOR_NAME . " " . LEWE_JIRA_CONNECTOR_VERSION; ?></div>
    <div class="jco-options-header-subtitle">
      <?php echo esc_html(__('Display Jira issue information on your WordPress website', 'lewe-jira-connector')); ?>
      <div class="jco-options-header-buttons">
        <a href="<?php echo esc_html(LEWE_JIRA_CONNECTOR_DOC_URI); ?>" target="_blank" class="jco-btn jco-btn-info jco-btn-xs"><?php echo esc_html(__('Documentation', 'lewe-jira-connector')); ?></a>
        <?php if (!LEWE_JIRA_CONNECTOR_LICENSE_REQUIRED) { ?><a href="<?php echo esc_html(LEWE_JIRA_CONNECTOR_DONATION_URI); ?>" target="_blank" class="jco-btn jco-btn-primary jco-btn-xs"><?php echo esc_html(__('Donate', 'lewe-jira-connector')); ?></a><?php } ?>
      </div>
    </div>
  </div>

  <div class="wrap">

    <?php
    if ($show_alert) {
      $allowed_html = array(
        'div' => array(
          'class' => array(),
          'role' => array(),
          'style' => array(),
        ),
        'span' => array(
          'aria-hidden' => array(),
          'class' => array(),
          'onclick' => array(),
          'style' => array(),
        ),
        'p' => array(),
      );
      echo wp_kses($BS->alert($alert_style, $alert_title, $alert_text, '', true, '98%'), $allowed_html);
    }
    settings_errors();
    if (isset($_GET['tab'])) $get_tab = sanitize_text_field($_GET['tab']);
    $active_tab = isset($get_tab) ? $get_tab : 'issue_button';
    ?>

    <div style="clear:both;">

      <!-- Tabs -->
      <h2 class="nav-tab-wrapper">
        <a href="?page=<?php echo esc_html(__FILE__); ?>&tab=issue_button" class="nav-tab <?php echo esc_html($active_tab == 'issue_button' ? 'nav-tab-active jco-tab-active' : 'jco-tab-inactive'); ?>"><?php echo esc_html(__('Jira Issue Button', 'lewe-jira-connector')); ?></a>
        <a href="?page=<?php echo esc_html(__FILE__); ?>&tab=issue_inline" class="nav-tab <?php echo esc_html($active_tab == 'issue_inline' ? 'nav-tab-active jco-tab-active' : 'jco-tab-inactive'); ?>"><?php echo esc_html(__('Jira Issue Inline', 'lewe-jira-connector')); ?></a>
        <a href="?page=<?php echo esc_html(__FILE__); ?>&tab=issue_panel" class="nav-tab <?php echo esc_html($active_tab == 'issue_panel' ? 'nav-tab-active jco-tab-active' : 'jco-tab-inactive'); ?>"><?php echo esc_html(__('Jira Issue Panel', 'lewe-jira-connector')); ?></a>
        <a href="?page=<?php echo esc_html(__FILE__); ?>&tab=issue_filter" class="nav-tab <?php echo esc_html($active_tab == 'issue_filter' ? 'nav-tab-active jco-tab-active' : 'jco-tab-inactive'); ?>"><?php echo esc_html(__('Jira Filter', 'lewe-jira-connector')); ?></a>
        <a href="?page=<?php echo esc_html(__FILE__); ?>&tab=colors" class="nav-tab <?php echo esc_html($active_tab == 'colors' ? 'nav-tab-active jco-tab-active' : 'jco-tab-inactive'); ?>"><?php echo esc_html(__('Colors', 'lewe-jira-connector')); ?></a>
        <a href="?page=<?php echo esc_html(__FILE__); ?>&tab=options" class="nav-tab <?php echo esc_html($active_tab == 'tab_options' ? 'nav-tab-active jco-tab-active' : 'jco-tab-inactive'); ?>"><?php echo esc_html(__('Options', 'lewe-jira-connector')); ?></a>
      </h2>

      <form name="form" action="<?php echo esc_html(admin_url('admin.php?page=lewe-jira-connector%2Fadmin%2Fpartials%2Flewe-jira-connector-admin-options.php&tab=' . $active_tab)); ?>" method="post">

        <?php settings_fields('lewe-jira-connector_options_group'); ?>

        <?php
        //  ,------------------------,
        // _| Tab: Jira Issue Button |_
        //
        if ($active_tab == 'issue_button') $display = "display:block;";
        else $display = "display:none;";
        ?>
        <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
          <tbody>
          <tr>
            <td scope="row" colspan="2" class="jco-pl-0">
              <div class="jco-callout jco-callout-info">
                <p><code>[jco-issue host="8670" key="MYKEY-123" format="<span style="color:#990000;">button</span>"]</code></p>
                <p><?php echo esc_html(__('The following default options apply when you chose the button format for the jco-issue shortcode. You can overwrite these values with custom shortcode parameters. See the documentation for more details.', 'lewe-jira-connector')); ?></p>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issuebutton_color_select"><?php echo esc_html(__('Default Button Color', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 'status';
              $selection = get_option('lewe-jira-connector_issuebutton_color_select');
              if (!strlen($selection)) $selection = $defaultValue;
              ?>
              <select id="lewe-jira-connector_issuebutton_color_select" name="lewe-jira-connector_issuebutton_color_select" class="jco-badge-<?php echo esc_html($selection); ?>" onchange="changeColor('lewe-jira-connector_issuebutton_color_select')">
                <option value="status" <?php echo esc_html(($selection == 'status') ? "selected" : ""); ?>><?php echo esc_html(__('Jira Status Category', 'lewe-jira-connector')); ?></option>
                <option class="jco-badge-danger" value="danger" <?php echo esc_html(($selection == 'danger') ? "selected" : ""); ?>>Danger</option>
                <option class="jco-badge-dark" value="dark" <?php echo esc_html(($selection == 'dark') ? "selected" : ""); ?>>Dark</option>
                <option class="jco-badge-info" value="info" <?php echo esc_html(($selection == 'info') ? "selected" : ""); ?>>Info</option>
                <option class="jco-badge-light" value="light" <?php echo esc_html(($selection == 'light') ? "selected" : ""); ?>>Light</option>
                <option class="jco-badge-primary" value="primary" <?php echo esc_html(($selection == 'primary') ? "selected" : ""); ?>>Primary</option>
                <option class="jco-badge-secondary" value="secondary" <?php echo esc_html(($selection == 'secondary') ? "selected" : ""); ?>>Secondary</option>
                <option class="jco-badge-success" value="success" <?php echo esc_html(($selection == 'success') ? "selected" : ""); ?>>Success</option>
                <option class="jco-badge-warning" value="warning" <?php echo esc_html(($selection == 'warning') ? "selected" : ""); ?>>Warning</option>
              </select>
              <p class="description"><?php echo esc_html(__('Select the color type of the button. The default is "Jira Status Category". You can overwrite the status category style on the Style tab of this page.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Default Button Style', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuebutton_outline_checkbox' name='lewe-jira-connector_issuebutton_outline_checkbox' <?php checked(get_option('lewe-jira-connector_issuebutton_outline_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Outlined Button', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Check to use an outlined button style (no fill color)', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issuebutton_summary_chars_number"><?php echo esc_html(__('Default Summary Length', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 40;
              $fieldValue = get_option('lewe-jira-connector_issuebutton_summary_chars_number');
              if (!strlen($fieldValue)) $fieldValue = $defaultValue;
              ?>
              <input type="number" id="lewe-jira-connector_issuebutton_summary_chars_number" name="lewe-jira-connector_issuebutton_summary_chars_number" value="<?php echo esc_html($fieldValue); ?>"/>
              <p class="description"><?php echo esc_html(__('Enter the number of characters of the Jira issue summary to display. (Default = ', 'lewe-jira-connector')) . $defaultValue; ?>)</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Link to Jira', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuebutton_linktojira_checkbox' name='lewe-jira-connector_issuebutton_linktojira_checkbox' <?php checked(get_option('lewe-jira-connector_issuebutton_linktojira_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Link issue key to Jira', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select this option to link the issue key to the actual issue in Jira.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          </tbody>
        </table>

        <?php
        //  ,------------------------,
        // _| Tab: Jira Issue Inline |_
        //
        $display = "display:none;";
        if ($active_tab == 'issue_inline') {
          if ($lstat == 'active') {
            $display = "display:block;";
          } else {
            $allowed_html = array(
              'div' => array(
                'class' => array(),
              ),
              'br' => array(),
              'em' => array(),
              'strong' => array(),
            );
            echo wp_kses($lmsg, $allowed_html);
          }
        }
        ?>
        <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
          <tbody>
          <tr>
            <td scope="row" colspan="2" class="jco-pl-0">
              <div class="jco-callout jco-callout-info">
                <p><code>[jco-issue host="8670" key="MYKEY-123" format="<span style="color:#990000;">inline</span>"]</code></p>
                <p><?php echo esc_html(__('The following default options apply when you chose the inline format for the jco-issue shortcode. You can overwrite these values with custom shortcode parameters. See the documentation for more details.', 'lewe-jira-connector')); ?></p>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issueinline_color_select"><?php echo esc_html(__('Default Lozenge Color', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 'light';
              $selection = get_option('lewe-jira-connector_issueinline_color_select');
              if (!strlen($selection)) $selection = $defaultValue;
              ?>
              <select id="lewe-jira-connector_issueinline_color_select" name="lewe-jira-connector_issueinline_color_select" class="jco-badge-<?php echo esc_html($selection); ?>" onchange="changeColor('lewe-jira-connector_issu_issueinline_color_select')">
                <option value="status" <?php echo esc_html(($selection == 'status') ? "selected" : ""); ?>><?php echo esc_html(__('Jira Status Category', 'lewe-jira-connector')); ?></option>
                <option class="jco-badge-danger" value="danger" <?php echo esc_html(($selection == 'danger') ? "selected" : ""); ?>>Danger</option>
                <option class="jco-badge-dark" value="dark" <?php echo esc_html(($selection == 'dark') ? "selected" : ""); ?>>Dark</option>
                <option class="jco-badge-info" value="info" <?php echo esc_html(($selection == 'info') ? "selected" : ""); ?>>Info</option>
                <option class="jco-badge-light" value="light" <?php echo esc_html(($selection == 'light') ? "selected" : ""); ?>>Light</option>
                <option class="jco-badge-primary" value="primary" <?php echo esc_html(($selection == 'primary') ? "selected" : ""); ?>>Primary</option>
                <option class="jco-badge-secondary" value="secondary" <?php echo esc_html(($selection == 'secondary') ? "selected" : ""); ?>>Secondary</option>
                <option class="jco-badge-success" value="success" <?php echo esc_html(($selection == 'success') ? "selected" : ""); ?>>Success</option>
                <option class="jco-badge-warning" value="warning" <?php echo esc_html(($selection == 'warning') ? "selected" : ""); ?>>Warning</option>
              </select>
              <p class="description"><?php echo esc_html(__('Select the background color of the inline lozenge. The default is "light". You can overwrite the status category style on the Colors tab of this page.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issueinline_summary_chars_number"><?php echo esc_html(__('Default Summary Length', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 0;
              $fieldValue = get_option('lewe-jira-connector_issueinline_summary_chars_number');
              if (!strlen($fieldValue)) $fieldValue = $defaultValue;
              ?>
              <input type="number" id="lewe-jira-connector_issueinline_summary_chars_number" name="lewe-jira-connector_issueinline_summary_chars_number" value="<?php echo esc_html($fieldValue); ?>"/>
              <p class="description"><?php echo esc_html(__('Enter the number of characters of the Jira issue summary to display in the lozenge. Set to 0 for none. (Default = ', 'lewe-jira-connector')) . esc_html($defaultValue); ?>)</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Link to Jira', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issueinline_linktojira_checkbox' name='lewe-jira-connector_issueinline_linktojira_checkbox' <?php checked(get_option('lewe-jira-connector_issueinline_linktojira_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Link issue key to Jira', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select this option to link the issue key to the actual issue in Jira.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          </tbody>
        </table>

        <?php
        //  ,-----------------------,
        // _| Tab: Jira Issue Panel |_
        //
        $display = "display:none;";
        if ($active_tab == 'issue_panel') {
          if ($lstat == 'active') {
            $display = "display:block;";
          } else {
            $allowed_html = array(
              'div' => array(
                'class' => array(),
              ),
              'br' => array(),
              'em' => array(),
              'strong' => array(),
            );
            echo wp_kses($lmsg, $allowed_html);
          }
        }
        ?>
        <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
          <tbody>
          <tr>
            <td scope="row" colspan="2" class="jco-pl-0">
              <div class="jco-callout jco-callout-info">
                <p><code>[jco-issue host="8670" key="MYKEY-123" format="<span style="color:#990000;">panel</span>"]</code></p>
                <p><?php echo esc_html(__('The following default options apply when you chose the panel format for the jco-issue shortcode. You can overwrite these values with custom shortcode parameters. See the documentation for more details.', 'lewe-jira-connector')); ?></p>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issuepanel_color_select"><?php echo esc_html(__('Panel Color', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 'status';
              $selection = get_option('lewe-jira-connector_issuepanel_color_select');
              if (!strlen($selection)) $selection = $defaultValue;
              ?>
              <select id="lewe-jira-connector_issuepanel_color_select" name="lewe-jira-connector_issuepanel_color_select" class="jco-badge-<?php echo esc_html($selection); ?>" onchange="changeColor('lewe-jira-connector_issuepanel_color_select')">
                <option value="status" <?php echo esc_html(($selection == 'status') ? "selected" : ""); ?>><?php echo esc_html(__('Jira Status Category', 'lewe-jira-connector')); ?></option>
                <option class="jco-badge-danger" value="danger" <?php echo esc_html(($selection == 'danger') ? "selected" : ""); ?>>Danger</option>
                <option class="jco-badge-dark" value="dark" <?php echo esc_html(($selection == 'dark') ? "selected" : ""); ?>>Dark</option>
                <option class="jco-badge-info" value="info" <?php echo esc_html(($selection == 'info') ? "selected" : ""); ?>>Info</option>
                <option class="jco-badge-light" value="light" <?php echo esc_html(($selection == 'light') ? "selected" : ""); ?>>Light</option>
                <option class="jco-badge-primary" value="primary" <?php echo esc_html(($selection == 'primary') ? "selected" : ""); ?>>Primary</option>
                <option class="jco-badge-secondary" value="secondary" <?php echo esc_html(($selection == 'secondary') ? "selected" : ""); ?>>Secondary</option>
                <option class="jco-badge-success" value="success" <?php echo esc_html(($selection == 'success') ? "selected" : ""); ?>>Success</option>
                <option class="jco-badge-warning" value="warning" <?php echo esc_html(($selection == 'warning') ? "selected" : ""); ?>>Warning</option>
              </select>
              <p class="description"><?php echo esc_html(__('Select the color type of the panel. The default is "Jira Status Category". You can overwrite the status category style on the Style tab of this page.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Panel Body', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuepanel_show_summary_checkbox' name='lewe-jira-connector_issuepanel_show_summary_checkbox' <?php checked(get_option('lewe-jira-connector_issuepanel_show_summary_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Summary', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuepanel_show_description_checkbox' name='lewe-jira-connector_issuepanel_show_description_checkbox' <?php checked(get_option('lewe-jira-connector_issuepanel_show_description_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Description', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuepanel_show_lastcomment_checkbox' name='lewe-jira-connector_issuepanel_show_lastcomment_checkbox' <?php checked(get_option('lewe-jira-connector_issuepanel_show_lastcomment_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Last Comment', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select the issue fields to show in the panel body. The panel header will always show the issue key and the status.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issuepanel_description_chars_number"><?php echo esc_html(__('Description characters', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 240;
              $fieldValue = get_option('lewe-jira-connector_issuepanel_description_chars_number');
              if (!strlen($fieldValue)) $fieldValue = $defaultValue;
              ?>
              <input type="number" id="lewe-jira-connector_issuepanel_description_chars_number" name="lewe-jira-connector_issuepanel_description_chars_number" value="<?php echo esc_html($fieldValue); ?>"/>
              <p class="description"><?php echo esc_html(__('Enter the nmuber of characters of the Jira issue description to display. (Default = ', 'lewe-jira-connector')) . esc_html($defaultValue); ?>)</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Link to Jira', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuepanel_linktojira_checkbox' name='lewe-jira-connector_issuepanel_linktojira_checkbox' <?php checked(get_option('lewe-jira-connector_issuepanel_linktojira_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Link issue key to Jira', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select this option to link the issue key to the actual issue in Jira.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          </tbody>
        </table>

        <?php
        //  ,------------------,
        // _| Tab: Jira Filter |_
        //
        $display = "display:none;";
        if ($active_tab == 'issue_filter') {
          if ($lstat == 'active') {
            $display = "display:block;";
          } else {
            $allowed_html = array(
              'div' => array(
                'class' => array(),
              ),
              'br' => array(),
              'em' => array(),
              'strong' => array(),
            );
            echo wp_kses($lmsg, $allowed_html);
          }
        }
        ?>
        <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
          <tbody>
          <tr>
            <td scope="row" colspan="2" class="jco-pl-0">
              <div class="jco-callout jco-callout-info">
                <p><code>[jco-filter host="8670" jql="project=MYKEY AND resolution=unresolved"]</code></p>
                <p><?php echo esc_html(__('The following default options apply when you use the jco-filter shortcode. You can overwrite these values with custom shortcode parameters. See the documentation for more details.', 'lewe-jira-connector')); ?></p>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Jira Fields', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_issuetype_checkbox' name='lewe-jira-connector_issuefilter_show_issuetype_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_issuetype_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Issue Type (as icon)', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_priority_checkbox' name='lewe-jira-connector_issuefilter_show_priority_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_priority_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Priority (as icon)', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_key_checkbox' name='lewe-jira-connector_issuefilter_show_key_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_key_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Issue Key', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_summary_checkbox' name='lewe-jira-connector_issuefilter_show_summary_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_summary_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Summary', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_description_checkbox' name='lewe-jira-connector_issuefilter_show_description_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_description_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Description', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_reporter_checkbox' name='lewe-jira-connector_issuefilter_show_reporter_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_reporter_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Reporter', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_assignee_checkbox' name='lewe-jira-connector_issuefilter_show_assignee_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_assignee_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Assignee', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_duedate_checkbox' name='lewe-jira-connector_issuefilter_show_created_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_created_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Created Date', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_duedate_checkbox' name='lewe-jira-connector_issuefilter_show_duedate_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_duedate_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Due Date', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_versions_checkbox' name='lewe-jira-connector_issuefilter_show_versions_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_versions_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Affects Version/s', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_fixversions_checkbox' name='lewe-jira-connector_issuefilter_show_fixversions_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_fixversions_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Fix Version/s', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_resolution_checkbox' name='lewe-jira-connector_issuefilter_show_resolution_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_resolution_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Resolution', 'lewe-jira-connector')); ?></p>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_show_status_checkbox' name='lewe-jira-connector_issuefilter_show_status_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_show_status_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show Status', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select the Jira fields to show in the filter result table.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Color Rows', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_color_rows_checkbox' name='lewe-jira-connector_issuefilter_color_rows_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_color_rows_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Color rows based on status category', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select this option to color each row based on the status category of the issue. The color will have a transparency set.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issuefilter_startat_number"><?php echo esc_html(__('Start At', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 0;
              $fieldValue = get_option('lewe-jira-connector_issuefilter_startat_number');
              if (!strlen($fieldValue)) $fieldValue = $defaultValue;
              ?>
              <input type="number" id="lewe-jira-connector_issuefilter_startat_number" name="lewe-jira-connector_issuefilter_startat_number" value="<?php echo esc_html($fieldValue); ?>"/>
              <p class="description"><?php echo esc_html(__('Enter the start number of the results to show. This is usually 0 to start with the first match. To start at the 51st match you would need to enter 50. (Default = ', 'lewe-jira-connector')) . esc_html($defaultValue); ?>)</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_issuefilter_maxresults_number"><?php echo esc_html(__('Maximum Results', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 50;
              $fieldValue = get_option('lewe-jira-connector_issuefilter_maxresults_number');
              if (!strlen($fieldValue)) $fieldValue = $defaultValue;
              ?>
              <input type="number" id="lewe-jira-connector_issuefilter_maxresults_number" name="lewe-jira-connector_issuefilter_maxresults_number" value="<?php echo esc_html($fieldValue); ?>"/>
              <p class="description"><?php echo esc_html(__('Enter the maximum number of the results to show. (Default = ', 'lewe-jira-connector')) . esc_html($defaultValue); ?>)</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Show Count', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_showcount_checkbox' name='lewe-jira-connector_issuefilter_showcount_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_showcount_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Show issue count above the result table', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select this option to show the issue count above the result table.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><?php echo esc_html(__('Link to Jira', 'lewe-jira-connector')); ?></th>
            <td>
              <p><input type='checkbox' id='lewe-jira-connector_issuefilter_linktojira_checkbox' name='lewe-jira-connector_issuefilter_linktojira_checkbox' <?php checked(get_option('lewe-jira-connector_issuefilter_linktojira_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Link issue key to Jira', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('Select this option to link the issue keys to the actual issue in Jira.', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          </tbody>
        </table>

        <?php
        //  ,-------------,
        // _| Tab: Colors |_
        //
        if ($active_tab == 'colors') $display = "display:block;";
        else $display = "display:none;";
        ?>
        <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
          <tbody>
          <tr>
            <td scope="row" colspan="2" class="jco-pl-0">
              <div class="jco-callout jco-callout-info">
                <?php echo esc_html(__('You can select a custom color for the Jira status categories here.', 'lewe-jira-connector')); ?>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_statuscolor_todo_select"><?php echo esc_html(__('To Do Color', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 'danger';
              $selection = get_option('lewe-jira-connector_statuscolor_todo_select');
              if (!strlen($selection)) $selection = $defaultValue;
              ?>
              <select id="lewe-jira-connector_statuscolor_todo_select" name="lewe-jira-connector_statuscolor_todo_select" class="jco-badge-<?php echo esc_html($selection); ?>" onchange="changeColor('lewe-jira-connector_statuscolor_todo_select')">
                <option class="jco-badge-danger" value="danger" <?php echo esc_html(($selection == 'danger') ? "selected" : ""); ?>>Danger</option>
                <option class="jco-badge-dark" value="dark" <?php echo esc_html(($selection == 'dark') ? "selected" : ""); ?>>Dark</option>
                <option class="jco-badge-info" value="info" <?php echo esc_html(($selection == 'info') ? "selected" : ""); ?>>Info</option>
                <option class="jco-badge-light" value="light" <?php echo esc_html(($selection == 'light') ? "selected" : ""); ?>>Light</option>
                <option class="jco-badge-primary" value="primary" <?php echo esc_html(($selection == 'primary') ? "selected" : ""); ?>>Primary</option>
                <option class="jco-badge-secondary" value="secondary" <?php echo esc_html(($selection == 'secondary') ? "selected" : ""); ?>>Secondary</option>
                <option class="jco-badge-success" value="success" <?php echo esc_html(($selection == 'success') ? "selected" : ""); ?>>Success</option>
                <option class="jco-badge-warning" value="warning" <?php echo esc_html(($selection == 'warning') ? "selected" : ""); ?>>Warning</option>
              </select>
              <p class="description"><?php echo esc_html(__('Select the color style of the "To Do" status category (Default = Danger)', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_statuscolor_inprogress_select"><?php echo esc_html(__('In Progress Color', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 'primary';
              $selection = get_option('lewe-jira-connector_statuscolor_inprogress_select');
              if (!strlen($selection)) $selection = $defaultValue;
              ?>
              <select id="lewe-jira-connector_statuscolor_inprogress_select" name="lewe-jira-connector_statuscolor_inprogress_select" class="jco-badge-<?php echo esc_html($selection); ?>" onchange="changeColor('lewe-jira-connector_statuscolor_inprogress_select')">
                <option class="jco-badge-danger" value="danger" <?php echo esc_html(($selection == 'danger') ? "selected" : ""); ?>>Danger</option>
                <option class="jco-badge-dark" value="dark" <?php echo esc_html(($selection == 'dark') ? "selected" : ""); ?>>Dark</option>
                <option class="jco-badge-info" value="info" <?php echo esc_html(($selection == 'info') ? "selected" : ""); ?>>Info</option>
                <option class="jco-badge-light" value="light" <?php echo esc_html(($selection == 'light') ? "selected" : ""); ?>>Light</option>
                <option class="jco-badge-primary" value="primary" <?php echo esc_html(($selection == 'primary') ? "selected" : ""); ?>>Primary</option>
                <option class="jco-badge-secondary" value="secondary" <?php echo esc_html(($selection == 'secondary') ? "selected" : ""); ?>>Secondary</option>
                <option class="jco-badge-success" value="success" <?php echo esc_html(($selection == 'success') ? "selected" : ""); ?>>Success</option>
                <option class="jco-badge-warning" value="warning" <?php echo esc_html(($selection == 'warning') ? "selected" : ""); ?>>Warning</option>
              </select>
              <p class="description"><?php echo esc_html(__('Select the color style of the "In Progress" status category (Default = Primary)', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_statuscolor_done_select"><?php echo esc_html(__('Done Color', 'lewe-jira-connector')); ?></label></th>
            <td>
              <?php
              $defaultValue = 'success';
              $selection = get_option('lewe-jira-connector_statuscolor_done_select');
              if (!strlen($selection)) $selection = $defaultValue;
              ?>
              <select id="lewe-jira-connector_statuscolor_done_select" name="lewe-jira-connector_statuscolor_done_select" class="jco-badge-<?php echo esc_html($selection); ?>" onchange="changeColor('lewe-jira-connector_statuscolor_done_select')">
                <option class="jco-badge-danger" value="danger" <?php echo esc_html(($selection == 'danger') ? "selected" : ""); ?>>Danger</option>
                <option class="jco-badge-dark" value="dark" <?php echo esc_html(($selection == 'dark') ? "selected" : ""); ?>>Dark</option>
                <option class="jco-badge-info" value="info" <?php echo esc_html(($selection == 'info') ? "selected" : ""); ?>>Info</option>
                <option class="jco-badge-light" value="light" <?php echo esc_html(($selection == 'light') ? "selected" : ""); ?>>Light</option>
                <option class="jco-badge-primary" value="primary" <?php echo esc_html(($selection == 'primary') ? "selected" : ""); ?>>Primary</option>
                <option class="jco-badge-secondary" value="secondary" <?php echo esc_html(($selection == 'secondary') ? "selected" : ""); ?>>Secondary</option>
                <option class="jco-badge-success" value="success" <?php echo esc_html(($selection == 'success') ? "selected" : ""); ?>>Success</option>
                <option class="jco-badge-warning" value="warning" <?php echo esc_html(($selection == 'warning') ? "selected" : ""); ?>>Warning</option>
              </select>
              <p class="description"><?php echo esc_html(__('Select the color style of the "Done" status category (Default = Success)', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          </tbody>
        </table>

        <script>
          function changeColor(elid) {
            var ele = document.getElementById(elid);
            var color = document.getElementById(elid).value;
            ele.className = "jco-badge-" + color;
          }
        </script>

        <?php
        //  ,--------------,
        // _| Tab: Options |_
        //
        if ($active_tab == 'options') $display = "display:block;";
        else $display = "display:none;";
        ?>
        <table class="form-table" style="margin-left:16px;<?php echo esc_html($display); ?>">
          <tbody>
          <tr>
            <td scope="row" colspan="2" class="jco-pl-0">
              <div class="jco-callout jco-callout-info">
                <?php echo esc_html(__('Set general options for this plugin.', 'lewe-jira-connector')); ?><br>
              </div>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="lewe-jira-connector_delete_on_uninstall_checkbox"><?php echo esc_html(__('Remove data on uninstall', 'lewe-jira-connector')); ?></label></th>
            <td>
              <p><input type="checkbox" id="lewe-jira-connector_delete_on_uninstall_checkbox" name="lewe-jira-connector_delete_on_uninstall_checkbox" <?php checked(get_option('lewe-jira-connector_delete_on_uninstall_checkbox'), 1) ?> value='1'>&nbsp;<?php echo esc_html(__('Yes', 'lewe-jira-connector')); ?></p>
              <p class="description"><?php echo esc_html(__('If checked, all plugin options and all Jira host posts will be deleted from the database when the plugin is deleted (This does not apply when the plugin is just deactivated).', 'lewe-jira-connector')); ?></p>
            </td>
          </tr>
          </tbody>
        </table>

        <p class="submit"><input type="submit" name="submit" id="submit" class="jco-btn jco-btn-primary jco-btn-sm" value="<?php echo esc_html(__('Save Changes', 'lewe-jira-connector')); ?>"></p>

      </form>
    </div>
  </div>

</div>
