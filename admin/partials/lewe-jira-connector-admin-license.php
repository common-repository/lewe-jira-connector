<?php

/**
 * Plugin License Page
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

$BS = new Lewe_Jira_Connector_Bootstrap('jco');
$FA = new Lewe_Jira_Connector_Fontawesome('jco');
$L = new Lewe_Jira_Connector_License();

?>

<div class="jco-options">

  <div class="jco-options-header">
    <img src="<?php echo esc_html(LEWE_JIRA_CONNECTOR_PLUGIN_URL) . 'global/images/icon-80x80.png'; ?>" alt="" class="jco-options-header-icon">
    <div class="jco-options-header-title"><?php echo esc_html(LEWE_JIRA_CONNECTOR_NAME . " " . LEWE_JIRA_CONNECTOR_VERSION); ?></div>
    <div class="jco-options-header-subtitle">
      <?php echo esc_html(__('Display Jira issue information on your WordPress website', 'lewe-jira-connector')); ?>
      <div class="jco-options-header-buttons">
        <a href="<?php echo esc_html(LEWE_JIRA_CONNECTOR_DOC_URI); ?>" target="_blank" class="jco-btn jco-btn-info jco-btn-xs"><?php echo esc_html(__('Documentation', 'lewe-jira-connector')); ?></a>
        <?php if (!LEWE_JIRA_CONNECTOR_LICENSE_REQUIRED) { ?><a href="<?php echo esc_html(LEWE_JIRA_CONNECTOR_DONATION_URI); ?>" target="_blank" class="jco-btn jco-btn-primary jco-btn-xs"><?php echo esc_html(__('Donate', 'lewe-jira-connector')); ?></a><?php } ?>
      </div>
    </div>
  </div>

  <div class="wrap">

    <div style="clear:both;">
      <h1><?php echo esc_html(__('Lewe Jira Connector License', 'lewe-jira-connector')); ?></h1>
    </div>

    <div style="clear:both;">

      <form name="form" action="<?php echo esc_html(admin_url('admin.php?page=lewe-jira-connector%2Fadmin%2Fpartials%2Flewe-jira-connector-admin-license.php')); ?>" method="post">

        <?php settings_fields('lewe-jira-connector_options_group'); ?>

        <table class="form-table" style="margin-left:16px;">
          <tbody>
          <tr>
            <td scope="row" colspan="2">
              <?php
              //
              // Activate License
              //
              if (isset($_REQUEST['activate_license'])) {
                $L->setKey($_REQUEST['lewe-jira-connector_license_key_text']);
                $response = $L->activate();

                // Uncomment next line to look at the data
                $allowed_html = array(
                  'div' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'span' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'hr' => array(),
                  'p' => array(),
                  'strong' => array(),
                  'table' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'thead' => array(),
                  'tbody' => array(),
                  'th' => array(),
                  'tr' => array(),
                  'td' => array(),
                );
                echo wp_kses($L->showResult($response), $allowed_html);

                if ($response->result == 'success') {
                  update_option('lewe-jira-connector_license_key_text', $L->getKey()); ?>
                <?php }
              }
              //
              // Deactivate License
              //
              if (isset($_REQUEST['deactivate_license'])) {
                $L->setKey($_REQUEST['lewe-jira-connector_license_key_text']);
                $response = $L->deactivate();

                // Uncomment it if you want to look at the full response
                $allowed_html = array(
                  'div' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'span' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'hr' => array(),
                  'p' => array(),
                  'strong' => array(),
                  'table' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'thead' => array(),
                  'tbody' => array(),
                  'th' => array(),
                  'tr' => array(),
                  'td' => array(),
                );
                echo wp_kses($L->showResult($response), $allowed_html);
              }

              //
              // Remove License
              //
              if (isset($_REQUEST['remove_license'])) {
                $L->setKey($_REQUEST['lewe-jira-connector_license_key_text']);
                $response = $L->deactivate();
                update_option('lewe-jira-connector_license_key_text', '');
              }

              if (get_option('lewe-jira-connector_license_key_text')) {
                $L->setKey(get_option('lewe-jira-connector_license_key_text'));
                $lstat = $L->status();

                $allowed_html = array(
                  'div' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'span' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'hr' => array(),
                  'strong' => array(),
                  'table' => array(
                    'class' => array(),
                    'style' => array(),
                  ),
                  'thead' => array(),
                  'tbody' => array(),
                  'th' => array(),
                  'tr' => array(),
                  'td' => array(),
                );
                echo wp_kses($L->showStatus($L->details, true), $allowed_html);

                if ($L->isExpired() && $L->isInGracePeriod()) {
                  $renewInDays = (30 + intval($L->daysToExpiry()));
                  ?>
                  <div class="jco-alert jco-alert-warning"><?php echo esc_html(__('Your Connector Jira Server license has expired', 'lewe-jira-connector')) . ' ' . str_replace('-', '', $L->daysToExpiry()) . ' ' . esc_html(__('days ago. Please renew your license within', 'lewe-jira-connector')) . ' ' . esc_html($renewInDays) . ' ' . esc_html(__('days', 'lewe-jira-connector')); ?>.</div>
                  <?php

                }

                $buttons = '';
                switch ($lstat) {
                  case 'active':
                    if (intval($L->daysToExpiry()) <= 30) {
                      $buttons .= '<input type="submit" id="deactivate_license" name="deactivate_license" value="' . esc_html(__('Deactivate', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-warning jco-btn-sm" /> <a href="' . LEWE_JIRA_CONNECTOR_DOC_LIC_URI . '" target="_blank" class="jco-btn jco-btn-info jco-btn-sm">' . esc_html(__('Renew', 'lewe-jira-connector')) . '</a>';
                    } else {
                      $buttons .= '<input type="submit" id="deactivate_license" name="deactivate_license" value="' . esc_html(__('Deactivate', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-warning jco-btn-sm" />';
                    }
                    break;
                  case 'blocked':
                    $buttons .= '<input type="submit" id="deactivate_license" name="deactivate_license" value="' . esc_html(__('Deactivate', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-warning jco-btn-sm" /> <a href="' . LEWE_JIRA_CONNECTOR_DOC_LIC_URI . '" target="_blank" class="jco-btn jco-btn-info jco-btn-sm">' . esc_html(__('Unblock', 'lewe-jira-connector')) . '</a>';
                    break;
                  case 'expired':
                    $buttons .= '<a href="' . LEWE_JIRA_CONNECTOR_DOC_LIC_URI . '" target="_blank" class="jco-btn jco-btn-warning jco-btn-sm">' . esc_html(__('Renew', 'lewe-jira-connector')) . '</a>';
                    break;
                  case 'pending':
                    $buttons .= '<input type="submit" id="activate_license" name="activate_license" value="' . esc_html(__('Activate', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-success jco-btn-sm" /> <input type="submit" id="deactivate_license" name="deactivate_license" value="' . esc_html(__('Deactivate', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-warning jco-btn-sm" />';
                    break;
                  case 'unregistered':
                    $buttons .= '<input type="submit" id="activate_license" name="activate_license" value="' . esc_html(__('Register', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-success jco-btn-sm" />';
                    break;
                }
                $buttons .= ' <input type="submit" id="remove_license" name="remove_license" value="' . esc_html(__('Remove License', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-danger jco-btn-sm" />';
              } else { ?>
              <div class="jco-callout jco-callout-danger">
                <span class="jco-text-danger"><strong><?php echo esc_html(__('Missing License', 'lewe-jira-connector')); ?></strong></span>
                <hr>
                <?php echo esc_html(__('Please enter the license key for this plugin. You were given a license key when you purchased this item.', 'lewe-jira-connector')); ?>
              </div>
    </div>
    <?php
    $buttons = '<input type="submit" id="activate_license" name="activate_license" value="' . esc_html(__('Activate', 'lewe-jira-connector')) . '" class="jco-btn jco-btn-success jco-btn-sm" /> <a href="' . LEWE_JIRA_CONNECTOR_DOC_LIC_URI . '" target="_blank" class="jco-btn jco-btn-info jco-btn-sm">' . esc_html(__('Get a license', 'lewe-jira-connector')) . '</a>';
    } ?>
    </td>
    </tr>
    <tr>
      <th scope="row"><label for="lewe-jira-connector_license_key_text"><?php echo esc_html(__('License Key', 'lewe-jira-connector')); ?></label></th>
      <td>
        <input type="text" id="lewe-jira-connector_license_key_text" name="lewe-jira-connector_license_key_text" value="<?php echo esc_html(get_option('lewe-jira-connector_license_key_text')); ?>">
      </td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td sope="row">
        <?php
        $allowed_html = array(
          'input' => array(
            'class' => array(),
            'id' => array(),
            'name' => array(),
            'type' => array(),
            'value' => array(),
          ),
          'a' => array(
            'class' => array(),
            'href' => array(),
            'target' => array(),
          ),
        );
        echo wp_kses($buttons, $allowed_html);
        ?>
      </td>
    </tr>
    </tbody>
    </table>

    </form>

  </div>

</div>

</div>
<hr>
