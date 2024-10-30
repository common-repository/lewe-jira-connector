<?php

/**
 * This is used to display visual objects of the Bootstrap framework.
 *
 * @since      2.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/includes
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_Fontawesome {
  /**
   * The unique prefix of this plugin. Use this as the CSS style prefix.
   *
   * @since    2.0.0
   * @access   protected
   * @var      string $prefix The prefix to uniquely identify this plugin.
   */
  protected $prefix;

  /**
   * Bootstrap alert box styles.
   *
   * @since    2.0.0
   * @access   protected
   * @var      array $styles Array of Bootstrap 4 styles.
   */
  protected $types = array(
    'brands' => 'b',
    'duotone' => 'd',
    'light' => 'l',
    'regular' => 'r',
    'solid' => 's'
  );

  // -------------------------------------------------------------------------
  /**
   * Constructs the Fontawesome class.
   *
   * @since    2.0.0
   */
  public function __construct($prefix) {
    $this->prefix = $prefix;
  }

  // -------------------------------------------------------------------------
  /**
   * Returns a Fontawesome icon tag based on the given parameters.
   *
   * @param string $type Fontawesome icon type
   * @param string $icon Fontawesome icon name
   * @param string $size Fontawesome icon size
   * @param string $margin_right Bootstrap mr- class suffix
   * @param string $margin_left Bootstrap ml- class suffix
   * @param string $rotate Fontawesome rotate class suffix
   * @param string $flip Fontawesome flip class suffix
   * @param string $animate Fontawesome animation class suffix
   *
   * @return string    HTML code of the alert box
   * @since 2.0.0
   *
   */
  public function icon($type = 'solid', $icon = 'font-awesome', $size = false, $margin_right = false, $margin_left = false, $rotate = false, $flip = false, $animate = false) {
    /**
     * Prepare the HTML.
     */
    $icontag = '<i class="%type% %icon% %size% %rotate% %flip% %animate% %marginleft% %marginright%"></i>';

    /**
     * Type class
     */
    $icontag = str_replace('%type%', $this->prefix . '-fa' . $this->types[$type], $icontag);

    /**
     * Icon
     */
    $icontag = str_replace('%icon%', $this->prefix . '-fa-' . $icon, $icontag);

    /**
     * Size
     */
    if ($size) {
      $icontag = str_replace('%size%', $this->prefix . '-fa-' . $size, $icontag);
    } else {
      $icontag = str_replace('%size%', '', $icontag);
    }

    /**
     * Margin Left
     */
    if ($margin_left) {
      $icontag = str_replace('%marginleft%', $this->prefix . '-ml-' . $margin_left, $icontag);
    } else {
      $icontag = str_replace('%marginleft%', '', $icontag);
    }

    /**
     * Margin Right
     */
    if ($margin_right) {
      $icontag = str_replace('%marginright%', $this->prefix . '-mr-' . $margin_right, $icontag);
    } else {
      $icontag = str_replace('%marginright%', '', $icontag);
    }

    /**
     * Rotation
     */
    if ($rotate) {
      $icontag = str_replace('%rotate%', $this->prefix . '-fa-rotate-' . $rotate, $icontag);
    } else {
      $icontag = str_replace('%rotate%', '', $icontag);
    }

    /**
     * Flip
     */
    if ($flip) {
      $icontag = str_replace('%flip%', $this->prefix . '-fa-flip-' . $flip, $icontag);
    } else {
      $icontag = str_replace('%flip%', '', $icontag);
    }

    /**
     * Animation
     */
    if ($animate) {
      $icontag = str_replace('%animte%', $this->prefix . '-fa-' . $animate, $icontag);
    } else {
      $icontag = str_replace('%animate%', '', $icontag);
    }

    return $icontag;
  }
}
