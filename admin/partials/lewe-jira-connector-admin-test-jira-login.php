<?php

/**
 * WP Jira Connector Login Test
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/admin/partials
 * @author     George Lewe <george@lewe.com>
 */

define('WPINC', 1);
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$reqServer = trim(sanitize_text_field($_REQUEST['server']));
$reqUser = trim(sanitize_text_field($_REQUEST['user']));
$reqPass = trim(sanitize_text_field($_REQUEST['pass']));

if (strlen($reqServer) and strlen($reqUser) and strlen($reqPass)) {
  include '../../includes/class-lewe-jira-connector-restapi.php';
  include '../../includes/class-lewe-jira-connector-jira.php';

  $jArray = array(
    'host' => $reqServer,
    'username' => $reqUser,
    'password' => $reqPass,
  );
  $J = new Lewe_Jira_Connector_Jira($jArray);

  if ($J->testLogin()) {
    $jiraUser = $J->getUser($reqUser);
    $jiraServerInfo = $J->getServerInfo();
    ?>
    <div class="jco-alert jco-alert-success">
      <p style="font-size:120%;"><strong><?php echo esc_html(__('Jira Login Test', 'lewe-jira-connector')); ?></strong></p>
      <p><?php echo esc_html(__('Connection and login to the JIRA server successful.', 'lewe-jira-connector')); ?></p>
      <p><strong><?php echo esc_html(__('Jira Server Title', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraServerInfo->serverTitle); ?><br>
        <strong><?php echo esc_html(__('Jira BaseURL', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraServerInfo->baseUrl); ?><br>
        <strong><?php echo esc_html(__('Jira Version', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraServerInfo->version); ?><br>
        <strong><?php echo esc_html(__('Jira Build', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraServerInfo->buildNumber); ?><br><br>
        <strong><?php echo esc_html(__('Username', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraUser->name); ?><br>
        <strong><?php echo esc_html(__('Display Name', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraUser->displayName); ?><br>
        <strong><?php echo esc_html(__('E-mail', 'lewe-jira-connector')); ?>:</strong><?php echo esc_html($jiraUser->emailAddress); ?><br>
      </p>
    </div>
    <?php
  } else {
    ?>
    <div class='jco-alert jco-alert-danger'>
      <p style="font-size:120%;"><strong><?php echo esc_html(__('Jira Login Test', 'lewe-jira-connector')); ?></strong></p>
      <p><?php echo esc_html(__('The connection or authentication for this host failed. Please check the parameters and also whether a login captcha is required for this account', 'lewe-jira-connector')); ?>.</p>
    </div>
    <?php

  }
} else {
  ?>
  <div class='jco-alert jco-alert-warning'>
    <p style="font-size:120%;"><strong><?php echo esc_html(__('Jira Login Test', 'lewe-jira-connector')); ?></strong></p>
    <p><?php echo esc_html(__('The server and/or login parameters are incomplete', 'lewe-jira-connector')); ?>.</p>
  </div>
  <?php
}
?>
