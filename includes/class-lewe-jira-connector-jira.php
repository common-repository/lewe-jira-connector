<?php

/**
 * Jira Interface calls.
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/includes
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_Jira {
  /**
   * The host REST URI.
   *
   * @since    1.0.0
   * @access   private
   * @var      $string $host    The host REST URI
   */
  private $host;

  /**
   * The Jira REST API uri.
   *
   * @since    2.0.0
   * @access   private
   * @var      string
   */
  private $api_uri = '/rest/api/2/';

  /**
   * Array for wp_remote_get arguments.
   *
   * @since    2.0.0
   * @access   private
   * @var      array
   */
  private $args = array();

  /**
   * The password of the instance.
   *
   * @since    2.1.0
   * @access   private
   * @var      string
   */
  private $password;

  /**
   * The username of the instance.
   *
   * @since    2.1.0
   * @access   private
   * @var      string
   */
  private $username;

  /**
   * The personal access token (PAT) of the instance.
   *
   * @since    2.3.0
   * @access   private
   * @var      string
   */
  private $token;

  //-------------------------------------------------------------------------
  /**
   * Constructor.
   *
   * @param array $config
   */
  public function __construct(array $config = array()) {
    $host = (isset($config['host'])) ? $config['host'] : null;
    $this->host = $host . $this->api_uri;

    $this->username = (isset($config['username'])) ? $config['username'] : null;
    $this->password = (isset($config['password'])) ? $config['password'] : null;
    $this->token = (isset($config['token'])) ? $config['token'] : null;

    //
    // Default is basic authentication
    //
    if ($this->username && $this->password) {
      $this->args = array(
        'headers' => array(
          'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
        )
      );
    }

    //
    // If a token was given, we change from Baisc to Bearer authentication
    //
    if ($this->token) {
      $this->args = array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $this->token
        )
      );
    }
  }

  //-------------------------------------------------------------------------
  /**
   * Get Issue Details.
   *
   * @param string $issueKey Issue key to fetch
   *
   * @return object | boolean
   */
  public function getIssue($issueKey, $fields = "*all") {
    $request = $this->host . 'issue/' . $issueKey . '?fields=' . $fields;
    $response = wp_remote_get($request, $this->args);
    // $this->dnd($response);
    $http_code = wp_remote_retrieve_response_code($response);

    if ($this->http_success($http_code)) {
      $body = wp_remote_retrieve_body($response);
      $body = json_decode($body);
      return $body;
    } else {
      return false;
    }
  }

  //--------------------------------------------------------------------------
  /**
   * Get Server Info of current class instance.
   *
   * @return object | boolean
   *
   * stdClass Object
   * (
   *    [baseUrl] => <host>
   *    [version] => 6.3.11
   *    [versionNumbers] => Array
   *    (
   *       [0] => 6
   *       [1] => 3
   *       [2] => 11
   *    )
   *
   *    [buildNumber] => 6341
   *    [buildDate] => 2014-11-19T00:00:00.000+0000
   *    [serverTime] => 2016-05-15T21:23:16.322+0000
   *    [scmInfo] => 83c4d29b62e84cdfe80aa51b4d63b8d49a6ccb35
   *    [serverTitle] => trackSpace
   * )
   *
   */
  public function getServerInfo() {
    $request = $this->host . 'serverInfo';
    $response = wp_remote_get($request, $this->args);
    $http_code = wp_remote_retrieve_response_code($response);

    if ($this->http_success($http_code)) {
      $body = wp_remote_retrieve_body($response);
      $body = json_decode($body);
      return $body;
    } else {
      return false;
    }
  }

  //--------------------------------------------------------------------------
  /**
   * Get User (single).
   *
   * @param string $username String to search for in username, name or email
   *
   * @return object | boolean
   *
   * Array
   * (
   *    [0] => stdClass Object
   *    (
   *       [self] => <host>/rest/api/2/user?username=<username>
   *       [key] => <username>
   *       [name] => <username>
   *       [emailAddress] => george.lewe@lhsystems.com
   *       [avatarUrls] => stdClass Object
   *       (
   *          [48x48] => <host>/secure/useravatar?ownerId=<username>&avatarId=14270
   *          [24x24] => <host>/secure/useravatar?size=small&ownerId=<username>&avatarId=14270
   *          [16x16] => <host>/secure/useravatar?size=xsmall&ownerId=<username>&avatarId=14270
   *          [32x32] => <host>/secure/useravatar?size=medium&ownerId=<username>&avatarId=14270
   *       )
   *       [displayName] => Lewe, George [Admin]
   *       [active] => 1
   *       [timeZone] => Europe/Berlin
   *    )
   *    ...
   * )
   */
  public function getUser($username) {
    $request = $this->host . 'user/?username=' . $username;
    $response = wp_remote_get($request, $this->args);
    $http_code = wp_remote_retrieve_response_code($response);

    if ($this->http_success($http_code)) {
      $body = wp_remote_retrieve_body($response);
      $body = json_decode($body);
      return $body;
    } else {
      return false;
    }
  }

  //--------------------------------------------------------------------------
  /**
   * Check for a successful http return code.
   *
   * @return boolean True if successful, false if not
   */
  public function http_success($code) {
    return $code >= 200 && $code < 300;
  }

  //--------------------------------------------------------------------------
  /**
   * Search Issues.
   *
   * @param string $jql JQL query
   * @param string $startAt The index of the first user to return
   * @param string $maxResults The maximum number of users to return (defaults to 50).
   * @param boolean $validateQuery Whether to validate the JQL query (default true)
   * @param string $fields The list of fields to return for each issue. By default, all navigable fields are returned.
   * @param string $expand A comma-separated list of the parameters to expand.
   *
   * @return object | boolean
   */
  public function searchIssues($jql = '', $startAt = '0', $maxResults = '50', $validateQuery = 'true', $fields = '', $expand = '') {
    $jql = urlencode($jql);
    $request = $this->host . 'search/?jql=' . $jql . '&startAt=' . $startAt . '&maxResults=' . $maxResults . '&validateQuery=' . $validateQuery . '&fields=' . $fields . '&expand=' . $expand;
    $response = wp_remote_get($request, $this->args);
    $http_code = wp_remote_retrieve_response_code($response);

    if ($this->http_success($http_code)) {
      $body = wp_remote_retrieve_body($response);
      $body = json_decode($body);
      return $body;
    } else {
      return false;
    }
  }

  //--------------------------------------------------------------------------
  /**
   * Test remote Jira connection.
   *
   * @return boolean True if successful, false if not
   */
  public function testConnection() {
    $request = $this->host . 'serverInfo';
    $response = wp_remote_get($request);
    $http_code = wp_remote_retrieve_response_code($response);
    return $this->http_success($http_code);
  }

  //--------------------------------------------------------------------------
  /**
   * Test Login.
   *
   * @return boolean True if successful, false if not
   */
  public function testLogin() {
    $request = $this->host . 'issue/createmeta';
    $response = wp_remote_get($request, $this->args);
    $http_code = wp_remote_retrieve_response_code($response);
    return $this->http_success($http_code);
  }

  //--------------------------------------------------------------------------
  /**
   * Dump and Die
   *
   * @param array $a Array to print out pretty
   */
  function dnd($a, $die = true) {
    echo esc_html(highlight_string("<?php\n\$data =\n" . var_export($a, true) . ";\n?>"));
    if ($die) {
      die();
    }
  }
}
