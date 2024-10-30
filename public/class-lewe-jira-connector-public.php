<?php

/**
 * Public functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/public
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_Public {
  /**
   * The Bootstrap agent of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      Lewe_Jira_Connector_Bootstrap $BS The Bootstrap agent of this plugin.
   */
  private $BS;

  /**
   * The Font Awesome agent of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      Lewe_Jira_Connector_Fontawesome $FA The license agent of this plugin.
   */
  private $FA;

  /**
   * The License agent of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      Lewe_Jira_Connector_License $L The license agent of this plugin.
   */
  private $L;

  /**
   * The title of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_title The title of this plugin.
   */
  private $plugin_title;

  /**
   * The unique name (slug) of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_slug The ID of this plugin.
   */
  private $plugin_slug;

  /**
   * The unique prefix of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_prefix The prefix of this plugin.
   */
  private $plugin_prefix;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $version The current version of this plugin.
   */
  private $version;

  //--------------------------------------------------------------------------
  /**
   * Constructor.
   *
   * @param string $plugin_slug The name of the plugin.
   * @param string $version The version of this plugin.
   * @since    1.0.0
   */
  public function __construct($plugin_title, $plugin_slug, $plugin_prefix, $version) {
    $this->BS = new Lewe_Jira_Connector_Bootstrap($plugin_prefix);
    $this->FA = new Lewe_Jira_Connector_Fontawesome('jco');
    $this->L = new Lewe_Jira_Connector_License();
    $this->plugin_title = $plugin_title;
    $this->plugin_slug = $plugin_slug;
    $this->plugin_prefix = $plugin_prefix;
    $this->version = $version;
  }

  //--------------------------------------------------------------------------
  /**
   * Register the public stylesheets.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {
    wp_enqueue_style($this->plugin_slug, plugins_url($this->plugin_slug) . '/global/css/' . $this->plugin_slug . '.css', array(), $this->version, 'all');
    wp_enqueue_style($this->plugin_slug . '-fa-all', plugins_url($this->plugin_slug) . '/global/css/' . $this->plugin_slug . '-fa-all.css', array(), $this->version, 'all');
    wp_enqueue_style($this->plugin_slug . '-public', plugin_dir_url(__FILE__) . 'css/' . $this->plugin_slug . '-public.css', array(), $this->version, 'all');
  }

  //--------------------------------------------------------------------------
  /**
   * Register the public javascripts.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {
    wp_enqueue_script($this->plugin_slug . '-public', plugin_dir_url(__FILE__) . 'js/' . $this->plugin_slug . '-public.js', array( 'jquery' ), $this->version, false);
  }

  //--------------------------------------------------------------------------
  /**
   * Shortcode: jco-filter
   *
   * Arguments
   *   host=      Jira host post ID
   *   jql=       Jira Issue Key
   *   columns=   Comma separated list of columns to show
   *
   * @param array $atts Array of settings from shortcode
   * @param string $content Content wrapped in shortcode
   * @return string            Formatted HTML to display on page
   */
  function shortcode_jco_filter($atts, $content = null, $tag = '', $license_required = true) {
    //
    // Check license if this shortcode requires one
    //
    if ($license_required && ($license_message = $this->license_error())) {
      return $license_message;
    }

    $debug = false;
    $returnHtml = '';

    $jiraFieldHeaders = array(
      'issuetype' => 'T',
      'priority' => 'P',
      'key' => __('Key', 'lewe-jira-connector'),
      'summary' => __('Summary', 'lewe-jira-connector'),
      'description' => __('Description', 'lewe-jira-connector'),
      'reporter' => __('Reporter', 'lewe-jira-connector'),
      'assignee' => __('Assignee', 'lewe-jira-connector'),
      'created' => __('Created', 'lewe-jira-connector'),
      'duedate' => __('Due Date', 'lewe-jira-connector'),
      'versions' => __('Affects Version/s', 'lewe-jira-connector'),
      'fixVersions' => __('Fix Version/s', 'lewe-jira-connector'),
      'resolution' => __('Resolution', 'lewe-jira-connector'),
      'status' => __('Status', 'lewe-jira-connector'),
    );

    //
    // Get shortcode parameters
    //
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $args = shortcode_atts(
      [
        'host' => '',
        'jql' => '',
        'fields' => '',
        'startat' => '',
        'maxresults' => '',
        'showcount' => '',
        'linktojira' => '',
        'headers' => '',
        'debug' => '',
      ],
      $atts,
      $tag
    );

    if (strlen($args['host'])) $argsHost = sanitize_text_field($args['host']);
    else $argsHost = '';

    if (strlen($argsHost) && $post = get_post($argsHost)) {
      //
      // Retrieve meta data from Jira host post
      //
      $host_description = sanitize_text_field(get_post_meta($args['host'], 'jira_host_description', 1));
      $host_url = sanitize_text_field(get_post_meta($args['host'], 'jira_host_url', 1));
      $host_username = sanitize_text_field(get_post_meta($args['host'], 'jira_host_username', 1));
      $host_password = sanitize_text_field(get_post_meta($args['host'], 'jira_host_password', 1));
      $host_token = sanitize_text_field(get_post_meta($args['host'], 'jira_host_token', 1));

      $jArray = array(
        'host' => $host_url,
        'username' => $host_username,
        'password' => $host_password,
        'token' => $host_token
      );
      $J = new Lewe_Jira_Connector_Jira($jArray);

      if ($J->testConnection()) {
        //
        // Jira login successful. Get plugin defaults.
        //
        $jcStatusColorTodo = get_option($this->plugin_slug . '_statuscolor_todo_select');
        if (!strlen($jcStatusColorTodo)) $jcStatusColorTodo = 'dark';

        $jcStatusColorInprogress = get_option($this->plugin_slug . '_statuscolor_inprogress_select');
        if (!strlen($jcStatusColorInprogress)) $jcStatusColorInprogress = 'primary';

        $jcStatusColorDone = get_option($this->plugin_slug . '_statuscolor_done_select');
        if (!strlen($jcStatusColorDone)) $jcStatusColorDone = 'success';

        //
        // If we have a JQL, proceed...
        //
        if (strlen($args['jql'])) {
          $argsJql = sanitize_text_field($args['jql']);
          //
          // Check for cf{{ }} parameter. We need to convert it into valid JQL: cf[1234]
          //
          if ($textPosStart = strpos($argsJql, 'cf{{')) {
            $argsJql = str_replace('cf{{', 'cf[', $argsJql);
            $argsJql = str_replace('}}', ']', $argsJql);
          }
          // $this->BS->dnd($argsJql);
        } else {
          $argsJql = '';
        }

        if (strlen($argsJql)) {
          //
          // Get the Jira fields to retrieve
          //
          if (strlen($args['fields'])) $argsFields = sanitize_text_field($args['fields']);
          else $argsFields = '';
          $jqlFields = '';
          if (strlen($argsFields)) {
            //
            // Shortcode fields are set
            //
            $jqlFields = str_replace(' ', '', $argsFields);
          } else {
            //
            // Use global fields as set in options
            //
            if (get_option($this->plugin_slug . '_issuefilter_show_issuetype_checkbox')) $jqlFields .= 'issuetype,';
            if (get_option($this->plugin_slug . '_issuefilter_show_priority_checkbox')) $jqlFields .= 'priority,';
            if (get_option($this->plugin_slug . '_issuefilter_show_key_checkbox')) $jqlFields .= 'key,';
            if (get_option($this->plugin_slug . '_issuefilter_show_summary_checkbox')) $jqlFields .= 'summary,';
            if (get_option($this->plugin_slug . '_issuefilter_show_description_checkbox')) $jqlFields .= 'description,';
            if (get_option($this->plugin_slug . '_issuefilter_show_reporter_checkbox')) $jqlFields .= 'reporter,';
            if (get_option($this->plugin_slug . '_issuefilter_show_assignee_checkbox')) $jqlFields .= 'assignee,';
            if (get_option($this->plugin_slug . '_issuefilter_show_created_checkbox')) $jqlFields .= 'created,';
            if (get_option($this->plugin_slug . '_issuefilter_show_duedate_checkbox')) $jqlFields .= 'duedate,';
            if (get_option($this->plugin_slug . '_issuefilter_show_versions_checkbox')) $jqlFields .= 'versions,';
            if (get_option($this->plugin_slug . '_issuefilter_show_fixversions_checkbox')) $jqlFields .= 'fixVersions,';
            if (get_option($this->plugin_slug . '_issuefilter_show_resolution_checkbox')) $jqlFields .= 'resolution,';
            if (get_option($this->plugin_slug . '_issuefilter_show_status_checkbox')) $jqlFields .= 'status,';
          }

          if (!strlen($jqlFields)) {
            $jData = '
                        <strong>' . __('Host', 'lewe-jira-connector') . ':</strong> ' . $jArray['host'] . '<br>
                        <strong>' . __('Username', 'lewe-jira-connector') . ':</strong> ' . $jArray['username'] . '<br>
                        <strong>' . __('JQL', 'lewe-jira-connector') . ':</strong> ' . $args['jql'];
            $alert_style = 'warning';
            $alert_title = $this->plugin_title . ' ' . __('Warning', 'lewe-jira-connector');
            $alert_text = '<strong>' . __('Jira Issue Filter: No fields specified', 'lewe-jira-connector') . '</strong>';
            $alert_help = __('Check the Jira Issue Filter tab in the Lewe Jira Connector options and make sure that at least one field is checked on.', 'lewe-jira-connector') . '<br>' . __('Alternatively, you can specify fields in the shortcode.', 'lewe-jira-connector');
            $alert_dismissable = true;
            $alert_width = '100%';
            $returnHtml .= $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
            return $returnHtml;
          }

          //
          // Get startat
          //
          if (strlen($args['startat'])) $argsStartat = sanitize_text_field($args['startat']);
          else $argsStartat = '';
          $jqlStartat = 0;
          if (strlen($argsStartat) and is_numeric($argsStartat)) {
            //
            // Use shortcode value
            //
            $jqlStartat = intval($argsStartat);
          } else {
            //
            // Use global value
            //
            if ($globalOption = get_option($this->plugin_slug . '_issuefilter_startat_number')) $jqlStartat = intval($globalOption);
          }

          //
          // Get maxresult
          //
          if (strlen($args['maxresults'])) $argsMaxresults = sanitize_text_field($args['maxresults']);
          else $argsMaxresults = '';
          $jqlMaxresults = 50;
          if (strlen($argsMaxresults) and is_numeric($argsMaxresults)) {
            //
            // Use shortcode value
            //
            $jqlMaxresults = intval($argsMaxresults);
          } else {
            //
            // Use global value
            //
            if ($globalOption = get_option($this->plugin_slug . '_issuefilter_maxresults_number')) $jqlMaxresults = intval($globalOption);
          }

          //
          // Get show count
          //
          if (strlen($args['showcount'])) $argsShowcount = sanitize_text_field($args['showcount']);
          else $argsShowcount = '';
          $jqlShowcount = false;
          if (strlen($argsShowcount)) {
            //
            // Use shortcode value
            //
            if ($argsShowcount == "1" or $argsShowcount == "true") {
              $jqlShowcount = true;
            } else {
              $jqlShowcount = false;
            }
          } else {
            //
            // Use global value
            //
            if (get_option($this->plugin_slug . '_issuefilter_showcount_checkbox')) {
              $jqlShowcount = true;
            } else {
              $jqlShowcount = false;
            }
          }

          //
          // Get "Link to Jira" option
          //
          if (strlen($args['linktojira'])) $argsLinktoJira = sanitize_text_field($args['linktojira']);
          else $argsLinktoJira = '';
          $linktoJira = true;
          if (strlen($argsLinktoJira)) {
            //
            // Use shortcode value
            //
            if ($argsLinktoJira == "1" or $argsLinktoJira == "true") {
              $linktoJira = true;
            } else {
              $linktoJira = false;
            }
          } else {
            //
            // Use global value
            //
            if (get_option($this->plugin_slug . '_issuefilter_linktojira_checkbox')) {
              $linktoJira = true;
            } else {
              $linktoJira = false;
            }
          }

          //
          // Get 'debug'
          // Hidden parameter that will display the JSON response on the page
          //
          if (strlen($args['debug'])) $argsDebug = sanitize_text_field($args['debug']);
          else $argsDebug = '';
          $jqlDebug = false;
          if (strlen($argsDebug) && in_array(strtolower($argsDebug), array( "1", "yes", "true" ))) {
            $jqlDebug = true;
          }

          //
          // Search JIRA issues by JQL
          //
          $jResponse = $J->searchIssues($argsJql, $jqlStartat, $jqlMaxresults, true, $jqlFields);
          // $this->BS->dnd($argsJql);
          // $this->BS->dnd($jResponse);

          if (!$jResponse or isset($jResponse->errorMessages)) {
            $jData = '
                        <strong>' . __('Host', 'lewe-jira-connector') . ':</strong> ' . $jArray['host'] . '<br>
                        <strong>' . __('Username', 'lewe-jira-connector') . ':</strong> ' . $jArray['username'] . '<br>
                        <strong>' . __('JQL', 'lewe-jira-connector') . ':</strong> ' . $args['jql'];
            $alert_style = 'warning';
            $alert_title = $this->plugin_title . ' ' . __('Warning', 'lewe-jira-connector');
            $alert_text = "";
            if (!$jResponse) {
              $alert_text = "<p><strong>" . __('No response from host. Are your Jira host settings ok?', 'lewe-jira-connector') . "</strong></p>";
            }
            if (isset($jResponse->errorMessages)) {
              $alert_text = "<p><strong>" . __('Errors received from host:', 'lewe-jira-connector') . "</strong></p>";
              foreach ($jResponse->errorMessages as $errMsg) $alert_text .= "<p>" . $errMsg . "</p>";
            }
            $alert_text .= "<p>" . $jData . "</p>";
            $alert_help = '';
            $alert_dismissable = true;
            $alert_width = '100%';
            $returnHtml .= $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
          } else {
            $returnHtml = "\n\n<!-- Lewe Jira Connector Filter Table -->\n";
            $csv = '';
            $csvHeaderRow = '';
            $csvValueRow = '';

            //
            // Show the count if requested
            //
            if ($jqlShowcount) {
              /* translators: %1$d: Number of currently displayed issues */
              /* translators: %2$d: Number of max issues */
              /* translators: %3$d: Sequential number of first issue on this page */
              /* translators: %4$d: Sequential number of last issue on this page */
              $showCountString = sprintf(__('Showing %1$d of %2$d results (match %3$d to %4$d)', 'lewe-jira-connector'), $jqlMaxresults, $jResponse->total, ($jqlStartat + 1), ($jqlStartat + $jqlMaxresults));
              $returnHtml .= '<p>' . $showCountString . '</p>';
            }

            //
            // Start the table and add the header row
            //
            $returnHtml .= '
                        <table id="jco-filter-result" class="jco-filter-table">
                            <thead>
                                <tr>';

            if (isset($args['headers']) && strlen($args['headers'])) {
              $jqlHeaders = $args['headers'];
              $jqlHeaders = rtrim($jqlHeaders, ',');
              $headers = explode(',', $jqlHeaders);
              $i = 0;
              foreach ($headers as $header) {
                $returnHtml .= '<th onclick="jcoSortTable(\'jco-filter-result\', ' . $i . ')">' . $header . '</th>';
                $csvHeaderRow .= $header . ',';
                $i++;
              }
            } else {
              $jqlFields = rtrim($jqlFields, ',');
              $headers = explode(',', $jqlFields);
              $i = 0;
              foreach ($headers as $header) {
                if (isset($jiraFieldHeaders[$header])) {
                  $returnHtml .= '<th onclick="jcoSortTable(\'jco-filter-result\', ' . $i . ')">' . $jiraFieldHeaders[$header] . '</th>';
                  $csvHeaderRow .= $jiraFieldHeaders[$header] . ',';
                } else {
                  $returnHtml .= '<th onclick="jcoSortTable(\'jco-filter-result\', ' . $i . ')">' . $header . '</th>';
                  $csvHeaderRow .= $header . ',';
                }
                $i++;
              }
            }

            $returnHtml .= '
                                </tr>
                            </thead>
                            <tbody>
                        ';

            $csvHeaderRow = rtrim($csvHeaderRow, ',') . PHP_EOL;
            $csv .= $csvHeaderRow;

            //
            // Now loop through each issue and create its row
            //
            if (isset($jResponse->issues)) {
              foreach ($jResponse->issues as $issue) {
                //
                // Get the status category color for this issue
                //
                $issueStatusCategory = $issue->fields->status->statusCategory->name;

                switch ($issueStatusCategory) {
                  case 'To Do':
                    $statusColor = get_option($this->plugin_slug . '_statuscolor_todo_select');
                    if (!strlen($statusColor)) $statusColor = 'dark';
                    $rowCSS = 'jco-filter-row-' . $statusColor;
                    break;
                  case 'In Progress':
                    $statusColor = get_option($this->plugin_slug . '_statuscolor_inprogress_select');
                    if (!strlen($statusColor)) $statusColor = 'primary';
                    $rowCSS = 'jco-filter-row-' . $statusColor;
                    break;
                  case 'Done':
                    $statusColor = get_option($this->plugin_slug . '_statuscolor_done_select');
                    if (!strlen($statusColor)) $statusColor = 'success';
                    $rowCSS = 'jco-filter-row-' . $statusColor;
                    break;
                }

                //
                // If the user wants to color the rows, get the info next
                //
                $rowCSS = '';
                if (get_option($this->plugin_slug . '_issuefilter_color_rows_checkbox')) $rowCSS = 'jco-filter-row-' . $statusColor;

                //
                // Open the row
                //
                $returnHtml .= '<tr class="' . $rowCSS . '">';

                //
                // Add the requested Jira field cells
                //
                $fields = explode(',', $jqlFields);

                foreach ($fields as $field) {
                  switch ($field) {
                    case 'issuetype':
                      $issueType = $issue->fields->issuetype->name;
                      $issueTypeIcon = $issue->fields->issuetype->iconUrl;
                      $returnHtml .= '<td><img src="' . $issueTypeIcon . '" title="' . $issueType . '" style="max-height:20px;"></td>';
                      $csvValueRow .= $issueType . ',';
                      break;
                    case 'priority':
                      if (isset($issue->fields->priority)) {
                        $issuePriority = $issue->fields->priority->name;
                        $issuePriorityIcon = $issue->fields->priority->iconUrl;
                      } else {
                        $issuePriority = '';
                        $issuePriorityIcon = '';
                      }
                      $returnHtml .= '<td><img src="' . $issuePriorityIcon . '" title="' . $issuePriority . '" style="max-height:20px;"></td>';
                      $csvValueRow .= $issuePriority . ',';
                      break;
                    case 'key':
                      if ($linktoJira) {
                        $returnHtml .= '<td><a href="' . $jArray['host'] . '/browse/' . $issue->key . '" target="_blank">' . $issue->key . '</a></td>';
                      } else {
                        $returnHtml .= '<td>' . $issue->key . '</td>';
                      }
                      $csvValueRow .= $issue->key . ',';
                      break;
                    case 'summary':
                      $returnHtml .= '<td>' . $issue->fields->summary . '</td>';
                      $csvValueRow .= $issue->fields->summary . ',';
                      break;
                    case 'description':
                      $returnHtml .= '<td>' . $issue->fields->description . '</td>';
                      $csvValueRow .= $issue->fields->description . ',';
                      break;
                    case 'reporter':
                      $returnHtml .= '<td>' . $issue->fields->reporter->displayName . '</td>';
                      $csvValueRow .= $issue->fields->reporter->displayName . ',';
                      break;
                    case 'assignee':
                      $returnHtml .= '<td>' . $issue->fields->assignee->displayName . '</td>';
                      $csvValueRow .= $issue->fields->assignee->displayName . ',';
                      break;
                    case 'resolution':
                      if (isset($issue->fields->resolution->name)) {
                        $returnHtml .= '<td>' . $issue->fields->resolution->name . '</td>';
                        $csvValueRow .= $issue->fields->resolution->name . ',';
                      } else {
                        $returnHtml .= '<td><i>' . __('Unresolved', 'lewe-jira-connector') . '</i></td>';
                        $csvValueRow .= __('Unresolved', 'lewe-jira-connector') . ',';
                      }
                      break;
                    case 'status':
                      $returnHtml .= '<td><span class="jco-badge jco-badge-' . $statusColor . '">' . $issue->fields->status->name . '</span></td>';
                      $csvValueRow .= $issue->fields->status->name . ',';
                      break;
                    case 'created':
                      $returnHtml .= '<td>' . substr($issue->fields->created, 0, 10) . '</td>';
                      $csvValueRow .= substr($issue->fields->created, 0, 10) . ',';
                      break;
                    case 'duedate':
                      if (isset($issue->fields->duedate)) {
                        $returnHtml .= '<td>' . $issue->fields->duedate . '</td>';
                        $csvValueRow .= $issue->fields->duedate . ',';
                      } else {
                        $returnHtml .= '<td></td>';
                        $csvValueRow .= ',';
                      }
                      break;
                    case 'versions':
                      $issueVersions = '';
                      $issueVersionsCsv = '';
                      if (isset($issue->fields->versions)) {
                        foreach ($issue->fields->versions as $version) {
                          $issueVersions .= $version->name . '<br>';
                          $issueVersionsCsv .= $version->name . '|';
                        }
                        $issueVersions = rtrim($issueVersions, "<br>");
                        $issueVersionsCsv = rtrim($issueVersionsCsv, "|");
                      }
                      $returnHtml .= '<td>' . $issueVersions . '</td>';
                      $csvValueRow .= $issueVersionsCsv . ',';
                      break;
                    case 'fixVersions':
                      $issueFixVersions = '';
                      $issueFixVersionsCsv = '';
                      if (isset($issue->fields->fixVersions)) {
                        foreach ($issue->fields->fixVersions as $fixVersion) {
                          $issueFixVersions .= $fixVersion->name . '<br>';
                          $issueFixVersionsCsv .= $fixVersion->name . '|';
                        }
                        $issueFixVersions = rtrim($issueFixVersions, "<br>");
                        $issueFixVersionsCsv = rtrim($issueFixVersionsCsv, "|");
                      }
                      $returnHtml .= '<td>' . $issueFixVersions . '</td>';
                      $csvValueRow .= $issueFixVersionsCsv . ',';
                      break;
                    default:
                      // $this->BS->dnd($issue);
                      if (strpos($field, 'customfield_') !== false) {
                        $cf = $issue->fields->{$field};
                        if (is_array($cf)) {
                          $returnHtml .= '<td>' . $cf[0]->value . '</td>';
                        } else {
                          $returnHtml .= '<td>' . $cf . '</td>';
                        }
                        $csvValueRow .= $cf[0]->value . ',';
                      }
                      break;
                  }
                }
                //
                // Close the row
                //
                $returnHtml .= '</tr>';
                $csvValueRow = rtrim($csvValueRow, ',') . PHP_EOL;
                $csv .= $csvValueRow;
              }
            }
            //
            // Close the table
            //
            $returnHtml .= "
                            </tbody>
                        </table>\n\n";

            if ($jqlDebug) {
//                            $this->BS->dnd($jResponse, false);
              $debugContent = "<pre style=\"overflow-wrap: break-word; word-break: break-all;\">" . var_export($jResponse, true) . "</pre>";
              $returnHtml .= $this->BS->alert('info', 'JCO Debug', $debugContent, '', true);
//                            $returnHtml .= $debugContent;
            }

            // $returnHtml .= "
            // <textarea rows='12' cols='111'>".$csv."</textarea>\n\n";
          }
        }

        //
        // Debug info (enable at the top of function)
        //
        if ($debug) {
          $jData = '
                    <strong>Host:</strong> ' . $jArray['host'] . '<br>
                    <strong>JQL:</strong> ' . $args['jql'] . '<br>
                    <strong>Fields:</strong> ' . $args['fields'];

          $alert_style = 'info';
          $alert_title = $this->plugin_title . ' Debug Info';
          $alert_text = $jData;
          $alert_help = '';
          $alert_dismissable = false;
          $alert_width = '100%';
          $returnHtml .= $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
          $returnHtml .= $this->BS->dnd(var_export($jResponse, false));
        }

        return $returnHtml;
      } else {
        //
        // JIRA login failed
        //
        $jData = '
                <strong>' . __('Host', 'lewe-jira-connector') . ':</strong> ' . $jArray['host'] . '<br>
                <strong>' . __('Username', 'lewe-jira-connector') . ':</strong> ' . $jArray['username'];

        $alert_style = 'danger';
        $alert_title = $this->plugin_title . ' Error';
        $alert_text = "<p><strong>" . __('Jira Connection Failed', 'lewe-jira-connector') . "</strong></p><p>" . $jData . "</p>";
        $alert_help = __('Please check the JIRA host settings.', 'lewe-jira-connector');
        $alert_dismissable = false;
        $alert_width = '100%';
        return $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
      }
    } else {
      return '<div class="jco-alert jco-alert-danger">' . $this->FA->icon('solid', 'exclamation-triangle', false, '2') . __('Invalid Jira host ID', 'lewe-jira-connector') . '</div>';
    }
  }

  //--------------------------------------------------------------------------
  /**
   * Shortcode: jco-issue
   *
   * Arguments
   *   host=      Jira host post ID
   *   key=       Jira Issue Key
   *   format=    Display format (button or panel)
   *
   * @param array $atts Array of settings from shortcode
   * @param string $content Content wrapped in shortcode
   * @return string            Formatted HTML to display on page
   */
  function shortcode_jco_issue($atts, $content = null, $tag = '', $license_required = true) {
    $debug = false;
    $returnHtml = '';

    //
    // Get shortcode parameters
    //
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $args = shortcode_atts(
      [
        'color' => '',
        'description' => '',
        'format' => 'button',
        'host' => '',
        'key' => '',
        'outline' => '',
        'summary' => '',
        'linktojira' => ''
      ],
      $atts,
      $tag
    );

    if (strlen($args['host'])) $argsHost = sanitize_text_field($args['host']);
    else $argsHost = '';
    if (strlen($argsHost) && $post = get_post($argsHost)) {
      //
      // Retrieve meta data from Jira host post
      //
      $host_description = sanitize_text_field(get_post_meta($argsHost, 'jira_host_description', 1));
      $host_url = sanitize_text_field(get_post_meta($argsHost, 'jira_host_url', 1));
      $host_username = sanitize_text_field(get_post_meta($argsHost, 'jira_host_username', 1));
      $host_password = sanitize_text_field(get_post_meta($argsHost, 'jira_host_password', 1));
      $host_token = sanitize_text_field(get_post_meta($argsHost, 'jira_host_token', 1));

      $jArray = array(
        'host' => $host_url,
        'username' => $host_username,
        'password' => $host_password,
        'token' => $host_token
      );
      $J = new Lewe_Jira_Connector_Jira($jArray);

      if ($J->testConnection()) {
        //
        // Jira login successful. Get plugin defaults.
        //
        $jcStatusColorTodo = get_option($this->plugin_slug . '_statuscolor_todo_select');
        if (!strlen($jcStatusColorTodo)) $jcStatusColorTodo = 'dark';

        $jcStatusColorInprogress = get_option($this->plugin_slug . '_statuscolor_inprogress_select');
        if (!strlen($jcStatusColorInprogress)) $jcStatusColorInprogress = 'primary';

        $jcStatusColorDone = get_option($this->plugin_slug . '_statuscolor_done_select');
        if (!strlen($jcStatusColorDone)) $jcStatusColorDone = 'success';

        //
        // If we have a key, proceed
        //
        if (strlen($args['key'])) $argsKey = sanitize_text_field($args['key']);
        else $argsKey = '';
        if (strlen($argsKey)) {
          //
          // Get JIRA issue
          //
          $jResponse = $J->getIssue($argsKey, "summary,description,comment,status,issuetype,priority");
          // $this->BS->dnd($jResponse, false);

          if (!$jResponse or isset($jResponse->errorMessages)) {
            $jData = '
                        <strong>' . __('Host', 'lewe-jira-connector') . ':</strong> ' . $jArray['host'] . '<br>
                        <strong>' . __('Username', 'lewe-jira-connector') . ':</strong> ' . $jArray['username'] . '<br>
                        <strong>' . __('Issue key', 'lewe-jira-connector') . ':</strong> ' . $args['key'];
            $alert_style = 'warning';
            $alert_title = $this->plugin_title . ' ' . __('Warning', 'lewe-jira-connector');
            $alert_text = "";
            if (!$jResponse) {
              $alert_text = "<p><strong>" . __('No response from host. Are your Jira host settings ok?', 'lewe-jira-connector') . "</strong></p>";
            }
            if (isset($jResponse->errorMessages)) {
              $alert_text = "<p><strong>" . __('Errors received from host:', 'lewe-jira-connector') . "</strong></p>";
              foreach ($jResponse->errorMessages as $errMsg) $alert_text .= "<p>" . $errMsg . "</p>";
            }
            $alert_text .= "<p>" . $jData . "</p>";
            $alert_help = '';
            $alert_dismissable = true;
            $alert_width = '100%';
            $returnHtml .= $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
          } else {
            //
            // Translate status category color to Bootstrap color
            //
            switch ($jResponse->fields->status->statusCategory->name) {
              case "To Do":
                $statusColor = $jcStatusColorTodo;
                break;
              case "In Progress":
                $statusColor = $jcStatusColorInprogress;
                break;
              case "Done":
                $statusColor = $jcStatusColorDone;
                break;
              default:
                $statusColor = $jcStatusColorTodo;
                break;
            }

            //-----------------------------------------------------------
            // Panel
            //
            if (strlen($args['format'])) $argsFormat = sanitize_text_field($args['format']);
            else $argsFormat = '';
            if (strlen($argsFormat) and $argsFormat == 'panel') {
              //
              // This format requires a license
              //
              if ($license_required && ($license_message = $this->license_error())) {
                return $license_message;
              }

              //
              // Get panel default options
              //
              $jcIssuePanelColor = get_option($this->plugin_slug . '_issuepanel_color_select');
              if (!strlen($jcIssuePanelColor) || $jcIssuePanelColor == 'status') $jcIssuePanelColor = $statusColor;

              $jcIssuePanelDescriptionChars = get_option($this->plugin_slug . '_issuepanel_description_chars_number');
              if (!strlen($jcIssuePanelDescriptionChars)) $jcIssuePanelDescriptionChars = 240;
              else $jcIssuePanelDescriptionChars = intval($jcIssuePanelDescriptionChars);

              //
              // Get Link to Jira
              //
              if (strlen($args['linktojira'])) $argsLinktoJira = sanitize_text_field($args['linktojira']);
              else $argsLinktoJira = '';
              $linktoJira = true;
              if (strlen($argsLinktoJira)) {
                //
                // Use shortcode value
                //
                if ($argsLinktoJira == "1" or $argsLinktoJira == "true") {
                  $linktoJira = true;
                } else {
                  $linktoJira = false;
                }
              } else {
                //
                // Use global value
                //
                if (get_option($this->plugin_slug . '_issuepanel_linktojira_checkbox')) {
                  $linktoJira = true;
                } else {
                  $linktoJira = false;
                }
              }

              //
              // Get shortcode arguments
              //
              if (strlen($args['color']) && in_array($args['color'], $this->BS->get_styles())) $jcIssuePanelColor = $args['color'];
              if (strlen($args['description']) && is_numeric($args['description'])) $jcIssuePanelDescriptionChars = intval($args['description']);

              //
              // Get and truncate description
              //
              $issueDescription = $jResponse->fields->description;
              if (strlen($issueDescription) > $jcIssuePanelDescriptionChars) $issueDescription = substr($issueDescription, 0, $jcIssuePanelDescriptionChars) . '...';

              //
              // Get last comment
              //
              if (isset($jResponse->fields->comment->comments))
                $last = count($jResponse->fields->comment->comments) - 1;

              $panelBody = '';
              if (get_option($this->plugin_slug . '_issuepanel_show_summary_checkbox')) $panelBody .= '<strong>' . $jResponse->fields->summary . '</strong><br>';
              if (get_option($this->plugin_slug . '_issuepanel_show_description_checkbox')) $panelBody .= '<p><strong>' . esc_html(__('Description', 'lewe-jira-connector')) . ':</strong><br> ' . $issueDescription . '</p>';
              if (get_option($this->plugin_slug . '_issuepanel_show_lastcomment_checkbox')) $panelBody .= '<p><strong>' . esc_html(__('Last Comment', 'lewe-jira-connector')) . ':</strong><br><i>' . $jResponse->fields->comment->comments[$last]->body . '</i></p>';

              $alert_style = $jcIssuePanelColor;
              $alert_title = '<img src="' . $jResponse->fields->issuetype->iconUrl . '" class="jco-issuetype-icon">';
              $alert_title .= '<img src="' . $jResponse->fields->priority->iconUrl . '" class="jco-priority-icon" title="' . $jResponse->fields->priority->name . '">';
              if ($linktoJira) {
                $alert_title .= '<a href="' . $jArray['host'] . '/browse/' . $jResponse->key . '" target="_blank">' . $jResponse->key . ' <span class="jco-badge jco-badge-' . $statusColor . ' float-right" style="margin-top:8px;">' . $jResponse->fields->status->name . '</span></a>';
              } else {
                $alert_title .= $jResponse->key . '<span class="jco-badge jco-badge-' . $statusColor . ' float-right" style="margin-top:8px;">' . $jResponse->fields->status->name . '</span>';
              }
              $alert_text = $panelBody;
              $alert_help = '';
              $alert_dismissable = false;
              $alert_width = '100%';
              $returnHtml .= $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);

              //-----------------------------------------------------------
              // Inline
              //
            } else if (strlen($argsFormat) and $argsFormat == 'inline') {
              //
              // This format requires a license
              //
              if ($license_required && ($license_message = $this->license_error())) {
                return $license_message;
              }

              //
              // Get default options
              //
              $jcIssueInlineColor = get_option($this->plugin_slug . '_issuebutton_color_select');
              if (!strlen($jcIssueInlineColor)) $jcIssueInlineColor = 'light';
              if ($jcIssueInlineColor == 'status') $jcIssueInlineColor = $statusColor;

              $jcIssueInlineSummaryChars = get_option($this->plugin_slug . '_issuebutton_summary_chars_number');
              if (!strlen($jcIssueInlineSummaryChars)) $jcIssueInlineSummaryChars = 40;
              else $jcIssueInlineSummaryChars = intval($jcIssueInlineSummaryChars);

              //
              // Get Link to Jira
              //
              if (strlen($args['linktojira'])) $argsLinktoJira = sanitize_text_field($args['linktojira']);
              else $argsLinktoJira = '';
              $linktoJira = true;
              if (strlen($argsLinktoJira)) {
                //
                // Use shortcode value
                //
                if ($argsLinktoJira == "1" or $argsLinktoJira == "true") {
                  $linktoJira = true;
                } else {
                  $linktoJira = false;
                }
              } else {
                //
                // Use global value
                //
                if (get_option($this->plugin_slug . '_issueinline_linktojira_checkbox')) {
                  $linktoJira = true;
                } else {
                  $linktoJira = false;
                }
              }

              //
              // Get shortcode arguments
              //
              if (strlen($args['color']) && in_array($args['color'], $this->BS->get_styles())) $jcIssueInlineColor = $args['color'];
              if (strlen($args['summary']) && is_numeric($args['summary'])) $jcIssueInlineSummaryChars = intval($args['summary']);

              //
              // Build the summary and truncate if needed
              //
              $issueSummary = $jResponse->fields->summary;
              if ($jcIssueInlineSummaryChars && strlen($issueSummary) > $jcIssueInlineSummaryChars)
                $issueSummary = ' : ' . substr($issueSummary, 0, $jcIssueInlineSummaryChars) . '...';
              elseif ($jcIssueInlineSummaryChars && strlen($issueSummary) <= $jcIssueInlineSummaryChars)
                $issueSummary = ' : ' . substr($issueSummary, 0, $jcIssueInlineSummaryChars);
              else
                $issueSummary = '';

              $returnHtml .= '<span class="jco-lozenge jco-lozenge-' . $jcIssueInlineColor . '" style="margin:0 4px 0 4px;">';
              if ($linktoJira) {
                $returnHtml .= '<a href="' . $jArray['host'] . '/browse/' . $jResponse->key . '" target="_blank" title="' . __('Go to issue...', 'lewe-jira-connector') . '">' . $jResponse->key . '</a>';
              } else {
                $returnHtml .= $jResponse->key;
              }
              $returnHtml .= $issueSummary . '
                                <span class="jco-badge jco-badge-' . $statusColor . '" style="margin:0 4px 0 4px;">' . $jResponse->fields->status->name . '</span>
                            </span>';
            } else {
              //--------------------------------------------------------
              // Button display (default)
              //

              //
              // Get default options
              //
              $jcIssueButtonColor = get_option($this->plugin_slug . '_issuebutton_color_select');
              if (!strlen($jcIssueButtonColor) || $jcIssueButtonColor == 'status') $jcIssueButtonColor = $statusColor;

              $jcIssueButtonOutline = '';
              if (get_option($this->plugin_slug . '_issuebutton_outline_checkbox')) $jcIssueButtonOutline = ' jco-btn-outline-' . $jcIssueButtonColor;

              $jcIssueButtonSummaryChars = get_option($this->plugin_slug . '_issuebutton_summary_chars_number');
              if (!strlen($jcIssueButtonSummaryChars)) $jcIssueButtonSummaryChars = 40;
              else $jcIssueButtonSummaryChars = intval($jcIssueButtonSummaryChars);

              //
              // Get Link to Jira
              //
              if (strlen($args['linktojira'])) $argsLinktoJira = sanitize_text_field($args['linktojira']);
              else $argsLinktoJira = '';
              $linktoJira = true;
              if (strlen($argsLinktoJira)) {
                //
                // Use shortcode value
                //
                if ($argsLinktoJira == "1" or $argsLinktoJira == "true") {
                  $linktoJira = true;
                } else {
                  $linktoJira = false;
                }
              } else {
                //
                // Use global value
                //
                if (get_option($this->plugin_slug . '_issuebutton_linktojira_checkbox')) {
                  $linktoJira = true;
                } else {
                  $linktoJira = false;
                }
              }

              //
              // Get shortcode arguments (first sanitize then evaluate)
              //
              if (strlen($args['color'])) $argsColor = sanitize_text_field($args['color']);
              else $argsColor = '';
              if (strlen($args['outline'])) $argsOutline = sanitize_text_field($args['outline']);
              else $argsOutline = '';
              if (strlen($args['summary'])) $argsSummary = sanitize_text_field($args['summary']);
              else $argsSummary = '';

              if (strlen($argsColor) && in_array($argsColor, $this->BS->get_styles())) $jcIssueButtonColor = $argsColor;
              if (strlen($argsOutline) && ($argsOutline == 'true' || $argsOutline == '1')) $jcIssueButtonOutline = ' jco-btn-outline-' . $jcIssueButtonColor;
              if (strlen($argsOutline) && ($argsOutline == 'false' || $argsOutline == '0')) $jcIssueButtonOutline = '';
              if (strlen($argsSummary) && is_numeric($argsSummary)) $jcIssueButtonSummaryChars = intval($argsSummary);

              //
              // Build the summary and truncate if needed
              //
              $issueSummary = $jResponse->fields->summary;
              if ($jcIssueButtonSummaryChars && strlen($issueSummary) > $jcIssueButtonSummaryChars)
                $issueSummary = ' : ' . substr($issueSummary, 0, $jcIssueButtonSummaryChars) . '...';
              elseif ($jcIssueButtonSummaryChars && strlen($issueSummary) <= $jcIssueButtonSummaryChars)
                $issueSummary = ' : ' . substr($issueSummary, 0, $jcIssueButtonSummaryChars);
              else
                $issueSummary = 'n/a';

              $returnHtml .= '
                            <div class="jco-btn' . $jcIssueButtonOutline . ' jco-alert-' . $jcIssueButtonColor . '" style="width:100%;">
                                <img src="' . $jResponse->fields->issuetype->iconUrl . '" class="jco-issuetype-icon" title="' . $jResponse->fields->issuetype->name . '">
                                <img src="' . $jResponse->fields->priority->iconUrl . '" class="jco-priority-icon" title="' . $jResponse->fields->priority->name . '">';
              if ($linktoJira) {
                $returnHtml .= '<a href="' . $jArray['host'] . '/browse/' . $jResponse->key . '" target="_blank" style="float:left;font-weight:bold;text-decoration:none;">' . $jResponse->key . '</a>';
              } else {
                $returnHtml .= '<span style="float:left;font-weight:bold;text-decoration:none;">' . $jResponse->key . '</span>';
              }
              $returnHtml .= '<span style="float:left;margin:0 8px 0 4px;">' . $issueSummary . '</span>
                                <span class="jco-badge jco-badge-' . $statusColor . '" style="float:right;margin-top:2px;">' . $jResponse->fields->status->name . '</span>
                            </div>
                            <div style="clear:both;margin-bottom:0.5rem;"></div>';
            }
          }
        }

        //
        // Debug info (enable at the top of function)
        //
        if ($debug) {
          $jData = '
                    <strong>Host:</strong> ' . $jArray['host'] . '<br>
                    <strong>Username:</strong> ' . $jArray['username'] . '<br>
                    <strong>Password:</strong> ' . $jArray['password'] . '<br>
                    <strong>Format:</strong> ' . $args['format'] . '<br>
                    <strong>Key:</strong> ' . $args['key'];

          $alert_style = 'info';
          $alert_title = $this->plugin_title . ' Debug Info';
          $alert_text = $jData;
          $alert_help = '';
          $alert_dismissable = false;
          $alert_width = '100%';
          $returnHtml .= $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
          $returnHtml .= $this->BS->dnd(var_export($jResponse, false));
        }

        return $returnHtml;
      } else {
        //
        // JIRA login failed
        //
        $jData = '
                <strong>' . __('Host', 'lewe-jira-connector') . ':</strong> ' . $jArray['host'] . '<br>
                <strong>' . __('Username', 'lewe-jira-connector') . ':</strong> ' . $jArray['username'];

        $alert_style = 'danger';
        $alert_title = $this->plugin_title . ' ' . __('Error', 'lewe-jira-connector');
        $alert_text = "<p><strong>" . __('Jira Connection Failed', 'lewe-jira-connector') . "</strong></p><p>" . $jData . "</p>";
        $alert_help = __('Please check the JIRA host settings.', 'lewe-jira-connector');
        $alert_dismissable = false;
        $alert_width = '100%';
        return $this->BS->alert($alert_style, $alert_title, $alert_text, $alert_help, $alert_dismissable, $alert_width);
      }
    } else {
      return '<div class="jco-alert jco-alert-danger">' . $this->FA->icon('solid', 'exclamation-triangle', false, '2') . __('Invalid Jira host ID', 'lewe-jira-connector') . '</div>';
    }
  }

  //--------------------------------------------------------------------------
  /**
   * Check license error.
   *
   * @return    string or boolean
   * @since     1.0.0
   */
  function license_error() {
    if (get_option($this->plugin_slug . '_license_key_text')) {
      $this->L->setKey(get_option($this->plugin_slug . '_license_key_text'));
      $this->L->load();

      if ($this->L->isExpired() && !$this->L->isInGracePeriod()) {
        return '<div class="jco-alert jco-alert-danger">' . esc_html(__('Your ' . $this->plugin_title . ' license has expired more than 30 days ago. Please renew your license.', 'lewe-jira-connector')) . '</div>';
      }

      if ($this->L->isBlocked()) {
        return '<div class="jco-alert jco-alert-danger">' . esc_html(__('Your ' . $this->plugin_title . ' license is blocked. Please renew your license.', 'lewe-jira-connector')) . '</div>';
      }

      if (!$this->L->domainRegistered()) {
        return '<div class="jco-alert jco-alert-warning">' . esc_html(__('Your ' . $this->plugin_title . ' license is not registered for this domain. Please activate your license.', 'lewe-jira-connector')) . '</div>';
      }

      return false;
    } else {
      return '<div class="jco-alert jco-alert-danger">' . esc_html(__('Your ' . $this->plugin_title . ' license is missing', 'lewe-jira-connector')) . '</div>';
    }
  }
}
