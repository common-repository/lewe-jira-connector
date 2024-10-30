<?php

/**
 * This is used to display visual objects of the Bootstrap framework.
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/includes
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_Bootstrap {
  /**
   * The unique prefix of this plugin. Use this as the CSS style prefix.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $prefix The prefix to uniquely identify this plugin.
   */
  protected $prefix;

  /**
   * Bootstrap alert box styles.
   *
   * @since    1.0.0
   * @access   protected
   * @var      array $styles Array of Bootstrap 4 styles.
   */
  protected $styles = array( 'danger', 'dark', 'info', 'light', 'primary', 'secondary', 'success', 'warning' );

  /**
   * HTML heading levels.
   *
   * @since    1.0.0
   * @access   protected
   * @var      array $heading_levels Array of HTML heading levels.
   */
  private $heading_levels = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );

  // -------------------------------------------------------------------------
  /**
   * Constructs the Bootstrap class.
   *
   * @since    1.0.0
   */
  public function __construct($prefix) {
    $this->prefix = $prefix;
  }

  // -------------------------------------------------------------------------
  /**
   * Returns a Bootstrap alert box based on the given parameters.
   *
   * @param string $style Bootstrap style
   * @param string $heading Heading of the alert box
   * @param string $content Content of the alert box
   * @param string $dismissible Whether the alert box shall be dismissible or not. Default: false
   * @param string $width Width of the alert box in pixel or percent. Default: 100%
   *
   * @return string    HTML code of the alert box
   * @since 1.0.0
   *
   */
  public function alert($style, $heading, $content, $help = '', $dismissible = false, $width = '100%') {
    /**
     * Prepare the HTML frame.
     */
    $identifier = "\n\n<!-- Lewe Jira Connector Alert Box -->\n";
    $startTag = "<div role=\"alert\" class=\"" . $this->prefix . "-alert " . $this->prefix . "-alert-%style%\" style=\"width:%width%;\">\n";
    $dismissButton = "   <span aria-hidden=\"true\" onclick=\"dismissParent(this);\" class=\"dashicons dashicons-hidden float-right action-icon\"></span>\n";
    $headingTag = "   <div class=\"" . $this->prefix . "-alert-heading\">%headingText%</div>\n";
    $contentTag = "   " . $content . "\n";
    $helpTag = "   <p><span style=\"font-style:italic;\">%helpText%</span></p>\n";
    $endTag = "\n</div>\n";

    /**
     * Add the requested styles.
     */
    if (in_array($style, $this->styles)) {
      $startTag = str_replace('%style%', $style, $startTag);
      $dismissButton = str_replace('%style%', $style, $dismissButton);
    } else {
      $startTag = str_replace('%style%', 'primary', $startTag);
      $dismissButton = str_replace('%style%', 'primary', $dismissButton);
    }

    /**
     * Add the requested heading.
     */
    if (strlen($heading)) {
      $headingTag = str_replace('%headingText%', $heading, $headingTag);
    } else {
      $headingTag = '';
    }

    /**
     * Add the requested help text.
     */
    if (strlen($help)) {
      $helpTag = str_replace('%helpText%', $help, $helpTag);
    } else {
      $helpTag = '';
    }

    /**
     * Remove dismissible button if so requested.
     */
    if (!$dismissible) {
      $dismissButton = '';
    }

    /**
     * Add the requested width.
     */
    if (strpos($width, 'px') || strpos($width, '%')) {
      $startTag = str_replace('%width%', $width, $startTag);
    } else {
      $startTag = str_replace('%width%', '100%', $startTag);
    }

    /**
     * Return the alert box.
     */
    return $identifier . $startTag . $dismissButton . $headingTag . $contentTag . $helpTag . $endTag;
  }

  // ---------------------------------------------------------------------------
  /**
   * Returns an array of Bootstrap style names.
   *
   * @return array
   * @since 2.0.0
   */
  function get_styles() {
    return $this->styles;
  }

  //--------------------------------------------------------------------------
  /**
   * Dump and Die
   *
   * @param array $a Array to print out pretty
   * @since     1.0.0
   */
  function dnd($a, $die = true) {
    echo esc_html(highlight_string("<?php\n\$data =\n" . var_export($a, true) . ";\n?>"));
    if ($die) {
      die();
    }
  }
}
