<?php

/**
 * License management API.
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/includes
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_License {
  /**
   * The JSON response from the license server.
   *
   * @since    1.0.0
   * @access   public
   * @var      array $details The JSON response from the license server.
   */
  public $details;

  /**
   * The grace period in days after expiry during which a warning message is displayed.
   *
   * @since    1.0.0
   * @access   private
   * @var      integer $gracePeriod The grace period in days.
   */
  private $gracePeriod = 30;

  /**
   * The host REST URI.
   *
   * @since    1.0.0
   * @access   private
   * @var      $string $host    The host REST URI
   */
  private $host;

  /**
   * The license server item reference.
   *
   * @since    2.1.0
   * @access   private
   * @var      string $item_reference
   */
  private $item_reference;

  /**
   * The license key of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $key The license key of this plugin.
   */
  private $key = '';

  /**
   * The language array with license response messages.
   *
   * @since    1.0.0
   * @access   private
   * @var      array $lang The language array with license respponse messages.
   */
  private $lang = array();

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_slug The string used to uniquely identify this plugin.
   */
  protected $plugin_slug;

  /**
   * The name of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_slug The string used to uniquely identify this plugin.
   */
  protected $plugin_title;

  /**
   * The license server secret key.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $secret_key
   */
  private $secret_key;


  // ------------------------------------------------------------------------
  /**
   * Constructor.
   *
   * Fill the license messages array.
   *
   * @since    1.0.0
   */
  public function __construct() {
    $this->host = 'https://lic.lewe.com/';
    $this->secret_key = '5e091d9b9cbf36.90197318';
    $this->item_reference = 'Lewe Jira Connector';

    $this->lang['lic_active'] = esc_html(__('Active License', 'lewe-jira-connector'));
    $this->lang['lic_active_subject'] = esc_html(__('This is an active license for this domain. Awesome!', 'lewe-jira-connector'));
    $this->lang['lic_active_unregistered_subject'] = esc_html(__('This is an active license but not registered for this domain.', 'lewe-jira-connector'));
    $this->lang['lic_alert_activation_fail'] = esc_html(__('The following error occurred while trying to activate your license:', 'lewe-jira-connector'));
    $this->lang['lic_alert_activation_success'] = esc_html(__('Your license was successfully activated for this domain.', 'lewe-jira-connector'));
    $this->lang['lic_alert_registration_fail'] = esc_html(__('The following error occurred while trying to register your domain to your license:', 'lewe-jira-connector'));
    $this->lang['lic_alert_registration_success'] = esc_html(__('Your domain was successfully registered to your license.', 'lewe-jira-connector'));
    $this->lang['lic_alert_deregistration_fail'] = esc_html(__('The following error occurred while trying to deregister your domain from your license:', 'lewe-jira-connector'));
    $this->lang['lic_alert_deregistration_success'] = esc_html(__('Your domain was successfully deregistered from your license.', 'lewe-jira-connector'));
    $this->lang['lic_blocked'] = esc_html(__('Blocked License', 'lewe-jira-connector'));
    $this->lang['lic_blocked_subject'] = esc_html(__('This license key is blocked.', 'lewe-jira-connector'));
    $this->lang['lic_blocked_help'] = esc_html(__('Please contact your administrator to unblock this license.', 'lewe-jira-connector'));
    $this->lang['close_this_message'] = esc_html(__('Close this message', 'lewe-jira-connector'));
    $this->lang['lic_company'] = esc_html(__('Company', 'lewe-jira-connector'));
    $this->lang['lic_date_created'] = esc_html(__('Date Created', 'lewe-jira-connector'));
    $this->lang['lic_date_expiry'] = esc_html(__('Date Expiry', 'lewe-jira-connector'));
    $this->lang['lic_date_renewed'] = esc_html(__('Date Renewed', 'lewe-jira-connector'));
    $this->lang['lic_daysleft'] = esc_html(__('days left', 'lewe-jira-connector'));
    $this->lang['lic_details'] = esc_html(__('License Details', 'lewe-jira-connector'));
    $this->lang['lic_email'] = esc_html(__('E-mail', 'lewe-jira-connector'));
    $this->lang['lic_expired'] = esc_html(__('Expired License', 'lewe-jira-connector'));
    $this->lang['lic_expired_subject'] = esc_html(__('This license key has expired.', 'lewe-jira-connector'));
    $this->lang['lic_expired_help'] = esc_html(__('Please contact your administrator to renew this license.', 'lewe-jira-connector'));
    $this->lang['lic_expiringsoon'] = esc_html(__('License Expiry Warning', 'lewe-jira-connector'));
    /* translators: %d: Number of days untile license expiry */
    $this->lang['lic_expiringsoon_subject'] = esc_html(__('Your license will expire in %d days.', 'lewe-jira-connector'));
    $this->lang['lic_expiringsoon_help'] = esc_html(__('Please contact your administrator to renew this license in time.', 'lewe-jira-connector'));
    $this->lang['lic_invalid'] = esc_html(__('Invalid License', 'lewe-jira-connector'));
    $this->lang['lic_invalid_subject'] = esc_html(__('This license key is invalid.', 'lewe-jira-connector'));
    $this->lang['lic_invalid_text'] = esc_html(__('This instance is unregistered or a ucfirst license key was not entered and activated yet.', 'lewe-jira-connector'));
    $this->lang['lic_invalid_help'] = esc_html(__('Please contact the administrator to obtain a valid license.', 'lewe-jira-connector'));
    $this->lang['lic_key'] = esc_html(__('License Key', 'lewe-jira-connector'));
    $this->lang['lic_name'] = esc_html(__('Licensee', 'lewe-jira-connector'));
    $this->lang['lic_max_allowed_domains'] = esc_html(__('Maximum Allowed Domains', 'lewe-jira-connector'));
    $this->lang['lic_pending'] = esc_html(__('Pending License', 'lewe-jira-connector'));
    $this->lang['lic_pending_subject'] = esc_html(__('This license is registered but not activated yet.', 'lewe-jira-connector'));
    $this->lang['lic_pending_help'] = esc_html(__('Please contact your administrator to activate this license.', 'lewe-jira-connector'));
    $this->lang['lic_registered_domains'] = esc_html(__('Registered Domains', 'lewe-jira-connector'));
    $this->lang['lic_status'] = esc_html(__('Status', 'lewe-jira-connector'));
    $this->lang['lic_product'] = esc_html(__('Product', 'lewe-jira-connector'));
    $this->lang['lic_unregistered'] = esc_html(__('Unregistered License', 'lewe-jira-connector'));
    $this->lang['lic_unregistered_subject'] = esc_html(__('The license key of this instance is not registered for this domain.', 'lewe-jira-connector'));
    $this->lang['lic_unregistered_help'] = esc_html(__('Please contact the administrator to register this domain or obtain a valid license.', 'lewe-jira-connector'));
  }

  // ------------------------------------------------------------------------
  /**
   * Activate a license and register the domain the request is coming from.
   *
   * @return   JSON    The JSON reponse from the license server.
   * @since    1.0.0
   * @access   public
   *
   */
  public function activate() {
    $request = $this->build_request('activate');
    $response = wp_remote_get($request);
    // $http_code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body($response);
    $body = json_decode($body);
    $this->details = $body;
    return $body;
  }

  // ------------------------------------------------------------------------
  /**
   * Build the REST API request URI.
   *
   * @param array $data Array with request parameters
   *
   * @return   string    The REST API request URI
   * @since    2.1.0
   * @access   private
   *
   */
  private function build_request($action = 'check') {
    $request = $this->host . '?';

    switch ($action) {
      case 'activate':
        $request .= 'slm_action=slm_activate&secret_key=' . $this->secret_key . '&license_key=' . $this->key . '&registered_domain=' . $_SERVER['SERVER_NAME'] . '&item_reference=' . urlencode($this->item_reference);
        break;
      case 'deactivate':
        $request .= 'slm_action=slm_deactivate&secret_key=' . $this->secret_key . '&license_key=' . $this->key . '&registered_domain=' . $_SERVER['SERVER_NAME'] . '&item_reference=' . urlencode($this->item_reference);
        break;
      default:
        $request .= 'slm_action=slm_check&secret_key=' . $this->secret_key . '&license_key=' . $this->key;
        break;
    }

    return $request;
  }

  // ------------------------------------------------------------------------
  /**
   * Deactivate a license and deregister the domain the request is coming from.
   *
   * @return   JSON    The JSON reponse from the license server.
   * @since    1.0.0
   * @access   public
   *
   */
  public function deactivate() {
    $request = $this->build_request('deactivate');
    $response = wp_remote_get($request);
    // $http_code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body($response);
    $body = json_decode($body);
    $this->details = $body;
    return $body;
  }

  // ------------------------------------------------------------------------
  /**
   * Check whether the current domain is registered.
   *
   * @return   boolean    True, if domain is registered, false, if not.
   * @since    1.0.0
   * @access   public
   *
   */
  public function domainRegistered() {
    // if (!$this->readKey()) return false; // Enable if using the readKey() method

    if (count($this->details->registered_domains)) {
      foreach ($this->details->registered_domains as $domain) {
        if ($domain->registered_domain == $_SERVER['SERVER_NAME']) {
          return true;
        }
      }
      return false;
    } else {
      return false;
    }
  }

  // ------------------------------------------------------------------------
  /**
   * Return trhe number of days until license expiry.
   *
   * @return   integer    The number of days until license expiry.
   * @since    1.0.0
   * @access   public
   *
   */
  public function daysToExpiry() {
    $todayDate = new DateTime('now');
    $expiryDate = new DateTime($this->details->date_expiry);
    $daysToExpiry = $todayDate->diff($expiryDate);
    return intval($daysToExpiry->format('%R%a'));
  }

  //--------------------------------------------------------------------------
  /**
   * Check for a successful http return code.
   *
   * @return boolean True if successful, false if not
   * @since    2.1.0
   * @access   public
   *
   */
  public function http_success($code) {
    return $code >= 200 && $code < 300;
  }

  // ------------------------------------------------------------------------
  /**
   * Load the license information from license server and save to $details.
   *
   * @since    1.0.0
   * @access   public
   */
  public function load() {
    // $BS = new Lewe_Jira_Connector_Bootstrap('jco');
    $request = $this->build_request('check');
    $response = wp_remote_get($request);
    // $http_code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body($response);
    $body = json_decode($body);
    $this->details = $body;
    // $BS->dnd($body);
    return $body;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the license key.
   *
   * @return   string    The license key.
   * @since    1.0.0
   * @access   public
   *
   */
  public function getKey() {
    return $this->key;
  }

  // ------------------------------------------------------------------------
  /**
   * Read the license key from database.
   *
   * @return   string    The license key.
   * @since    1.0.0
   * @access   public
   *
   */
  public function readKey() {
    //
    // You may want to use this method to read the license key from elsewhere
    // e.g. from a database with this pseudo code
    // $this->key = read_key_from_db();
    //
  }

  // ------------------------------------------------------------------------
  /**
   * Save the license key to the database.
   *
   * @param string $key The license key.
   * @since    1.0.0
   * @access   public
   *
   */
  public function saveKey($key) {
    //
    // You may want to use this method to save the license key elsewhere
    // e.g. to a database with this pseudo code
    // save_key_to_db($this->key);
    //
  }

  // ------------------------------------------------------------------------
  /**
   * Set the license key.
   *
   * @param string $key The license key.
   * @since    1.0.0
   * @access   public
   *
   */
  public function setKey($key) {
    $this->key = $key;
  }

  // ------------------------------------------------------------------------
  /**
   * Create a table with license details and displays it inside a Bootstrap
   * alert box. This method assumes that your application uses Bootstrap 4.
   *
   * @param array $data License information array.
   * @param boolean $showDetails Whether or not to show details.
   *
   * @return   string    HTML Bootstrap 4 alert box with license information.
   * @since    1.0.0
   * @access   public
   *
   */
  public function showStatus($data, $showDetails = false) {
    if (isset($data->result) && $data->result == "error") {
      $alert['type'] = 'danger';
      $alert['title'] = $this->lang['lic_invalid'];
      $alert['subject'] = $this->lang['lic_invalid_subject'];
      $alert['text'] = $this->lang['lic_invalid_text'];
      $alert['help'] = $this->lang['lic_invalid_help'];
      $details = "";
    } else {
      $domains = "";
      if (isset($data->registered_domains) && count($data->registered_domains)) {
        foreach ($data->registered_domains as $domain) {
          $domains .= $domain->registered_domain . ', ';
        }
        $domains = substr($domains, 0, -2); // Remove last comma and blank
      }

      $daysleft = "";
      if ($daysToExpiry = $this->daysToExpiry()) {
        if ($daysToExpiry <= 30)
          $daysleft = " (<span class=\"jco-text-danger jco-font-weight-bold\">" . $daysToExpiry . " " . $this->lang['lic_daysleft'] . "</span>)";
        else
          $daysleft = " (" . $daysToExpiry . " " . $this->lang['lic_daysleft'] . ")";
      }

      $details = "<div style=\"height:20px;\"></div>";
      $details .= "<table class=\"lic-table\">
                <tr><th>" . $this->lang['lic_product'] . ":</th><td>" . $data->product_ref . "</td></tr>
                <tr><th>" . $this->lang['lic_key'] . ":</th><td>" . $data->license_key . "</td></tr>
                <tr><th>" . $this->lang['lic_name'] . ":</th><td>" . $data->first_name . " " . $data->last_name . "</td></tr>
                <tr><th>" . $this->lang['lic_email'] . ":</th><td>" . $data->email . "</td></tr>
                <tr><th>" . $this->lang['lic_company'] . ":</th><td>" . $data->company_name . "</td></tr>
                <tr><th>" . $this->lang['lic_date_created'] . ":</th><td>" . $data->date_created . "</td></tr>
                <tr><th>" . $this->lang['lic_date_renewed'] . ":</th><td>" . $data->date_renewed . "</td></tr>
                <tr><th>" . $this->lang['lic_date_expiry'] . ":</th><td>" . $data->date_expiry . $daysleft . "</td></tr>
                <tr><th>" . $this->lang['lic_registered_domains'] . ":</th><td>" . $domains . "</td></tr>
            </table>";

      switch ($this->status()) {

        case "active":
          if (intval($this->daysToExpiry()) <= 30) {
            $title = $this->lang['lic_expiringsoon'];
            $alert['type'] = 'warning';
            $alert['title'] = $title;
            $alert['subject'] = sprintf($LANG['lic_expiringsoon_subject'], $this->daysToExpiry());
            $alert['text'] = '';
            $alert['help'] = '';
          } else {
            $title = $this->lang['lic_active'];
            $alert['type'] = 'success';
            $alert['title'] = $title;
            $alert['subject'] = $this->lang['lic_active_subject'];
            $alert['text'] = '';
            $alert['help'] = '';
          }
          break;

        case "expired":
          $title = $this->lang['lic_expired'];
          $alert['type'] = 'warning';
          $alert['title'] = $title;
          $alert['subject'] = $this->lang['lic_expired_subject'];
          $alert['help'] = $this->lang['lic_expired_help'];
          break;

        case "blocked":
          $alert['type'] = 'danger';
          $title = $this->lang['lic_blocked'];
          $alert['title'] = $title;
          $alert['subject'] = $this->lang['lic_blocked_subject'];
          $alert['text'] = '';
          $alert['help'] = $this->lang['lic_blocked_help'];
          break;

        case "pending":
          $alert['type'] = 'warning';
          $title = $this->lang['lic_pending'];
          $alert['title'] = $title;
          $alert['subject'] = $this->lang['lic_pending_subject'];
          $alert['text'] = '';
          $alert['help'] = $this->lang['lic_pending_help'];
          break;

        case "unregistered":
          $title = $this->lang['lic_unregistered'];
          $alert['type'] = 'warning';
          $alert['title'] = $title;
          $alert['subject'] = $this->lang['lic_active_unregistered_subject'];
          $alert['text'] = '';
          $alert['help'] = '';
          break;
      }
    }

    $alertBox = '
        <div class="jco-callout jco-callout-' . $alert['type'] . '">
            <span class="jco-text-' . $alert['type'] . '"><strong>' . $alert['title'] . '</strong></span>
            <hr>
            ' . $alert['subject'] . '
            ' . (($showDetails) ? $details : '') . '
        </div>';

    return $alertBox;
  }

  // ------------------------------------------------------------------------
  /**
   * Create and return a Bootstrap 4 alert box with the API call result and message.
   *
   * @param JSON $response JSON response from API call.
   * @return   string    HTML Bootstrap 4 alert box with license information.
   * @since    1.0.0
   * @access   public
   *
   */
  public function showResult($response) {
    if (isset($response->result) && $response->result == "error") {
      $alert['title'] = 'License Error';
      $alert['type'] = 'danger';
    } else {
      $alert['title'] = 'License Success';
      $alert['type'] = 'success';
    }
    $alert['text'] = $response->message;

    $alertBox = '
         <div class="jco-alert jco-alert-' . $alert['type'] . '">
            <p><strong>' . $alert['title'] . '</strong></p>
            <hr>
            <p>' . $alert['text'] . '</p>
         </div>';

    return $alertBox;
  }

  // ------------------------------------------------------------------------
  /**
   * Get license status.
   *
   * @return   string    active/blocked/invalid/expired/pending/unregistered.
   * @since    1.0.0
   * @access   public
   *
   */
  public function status() {
    $this->load();

    if ($this->details->result == 'error') {
      return "invalid";
    }

    switch ($this->details->status) {
      case "active":
        if (!$this->domainRegistered()) {
          return 'unregistered';
        }
        return 'active';
        break;
      case "expired":
        return 'expired';
        break;
      case "blocked":
        return 'blocked';
        break;
      case "pending":
        return 'pending';
        break;
    }
  }

  // ------------------------------------------------------------------------
  /**
   * Get license valid or not.
   *
   * @return   boolean    True if valid, false if not.
   * @since    1.0.0
   * @access   public
   *
   */
  public function isInvalid() {
    $this->load();
    return $this->details->result == 'error';
  }

  // ------------------------------------------------------------------------
  /**
   * Get license expired or not.
   *
   * @return   boolean    True if expired, false if not.
   * @since    1.0.0
   * @access   public
   *
   */
  public function isExpired() {
    $this->load();
    return $this->details->result != 'error' && $this->details->status == 'expired';
  }

  // ------------------------------------------------------------------------
  /**
   * Get license in grace period or not.
   *
   * @return   boolean    True if expired, false if not.
   * @since    1.0.0
   * @access   public
   *
   */
  public function isInGracePeriod() {
    $todayDate = new DateTime('now');
    $expiryDate = new DateTime($this->details->date_expiry);
    $daysToExpiry = $todayDate->diff($expiryDate);
    return $daysToExpiry < 0 && abs($daysToExpiry) <= $this->gracePeriod;
  }

  // ------------------------------------------------------------------------
  /**
   * Get license blocked or not.
   *
   * @return   boolean    True if blocked, false if not.
   * @since    1.0.0
   * @access   public
   *
   */
  public function isBlocked() {
    $this->load();
    return $this->details->result != 'error' && $this->details->status == 'blocked';
  }
}
