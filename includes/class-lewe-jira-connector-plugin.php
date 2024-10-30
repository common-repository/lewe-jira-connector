<?php

/**
 * Plugin core class serving both, public and admin functionalities.
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/includes
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_Plugin {
  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Lewe_Jira_Connector_Loader $loader Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * Array of option names.
   *
   * @since    2.0.0
   * @access   protected
   * @var      array $options Array of option names.
   */
  protected $options = array();

  /**
   * The title of of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_title The title of this plugin.
   */
  protected $plugin_title;

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_slug The string used to uniquely identify this plugin.
   */
  protected $plugin_slug;

  /**
   * The unique prefix of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_prefix The prefix to uniquely identify this plugin.
   */
  protected $plugin_prefix;

  /**
   * The URI of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_uri The prefix to uniquely identify this plugin.
   */
  protected $plugin_uri;

  /**
   * The current version of the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $version The current version of the plugin.
   */
  protected $version;

  // ------------------------------------------------------------------------
  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function __construct() {
    $this->version = LEWE_JIRA_CONNECTOR_VERSION;
    $this->plugin_title = LEWE_JIRA_CONNECTOR_NAME;
    $this->plugin_slug = LEWE_JIRA_CONNECTOR_SLUG;
    $this->plugin_prefix = LEWE_JIRA_CONNECTOR_PREFIX;
    $this->plugin_uri = LEWE_JIRA_CONNECTOR_DOC_URI;
    $this->plugin_donation_uri = LEWE_JIRA_CONNECTOR_DONATION_URI;

    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
    $this->define_public_hooks();
    $this->load_tinymce_buttons();

    $this->options = array(

      $this->plugin_slug . '_host_title_text',
      $this->plugin_slug . '_host_url_text',
      $this->plugin_slug . '_host_username_text',
      $this->plugin_slug . '_host_password_password',

      $this->plugin_slug . '_issuebutton_color_select',
      $this->plugin_slug . '_issuebutton_outline_checkbox',
      $this->plugin_slug . '_issuebutton_summary_chars_number',
      $this->plugin_slug . '_issuebutton_linktojira_checkbox',

      $this->plugin_slug . '_issuefilter_color_rows_checkbox',
      $this->plugin_slug . '_issuefilter_show_issuetype_checkbox',
      $this->plugin_slug . '_issuefilter_show_priority_checkbox',
      $this->plugin_slug . '_issuefilter_show_key_checkbox',
      $this->plugin_slug . '_issuefilter_show_summary_checkbox',
      $this->plugin_slug . '_issuefilter_show_description_checkbox',
      $this->plugin_slug . '_issuefilter_show_reporter_checkbox',
      $this->plugin_slug . '_issuefilter_show_assignee_checkbox',
      $this->plugin_slug . '_issuefilter_show_created_checkbox',
      $this->plugin_slug . '_issuefilter_show_duedate_checkbox',
      $this->plugin_slug . '_issuefilter_show_versions_checkbox',
      $this->plugin_slug . '_issuefilter_show_fixversions_checkbox',
      $this->plugin_slug . '_issuefilter_show_resolution_checkbox',
      $this->plugin_slug . '_issuefilter_show_status_checkbox',
      $this->plugin_slug . '_issuefilter_startat_number',
      $this->plugin_slug . '_issuefilter_maxresults_number',
      $this->plugin_slug . '_issuefilter_showcount_checkbox',
      $this->plugin_slug . '_issuefilter_linktojira_checkbox',

      $this->plugin_slug . '_issueinline_color_select',
      $this->plugin_slug . '_issueinline_summary_chars_number',
      $this->plugin_slug . '_issueinline_linktojira_checkbox',

      $this->plugin_slug . '_issuepanel_color_select',
      $this->plugin_slug . '_issuepanel_show_summary_checkbox',
      $this->plugin_slug . '_issuepanel_show_description_checkbox',
      $this->plugin_slug . '_issuepanel_show_lastcomment_checkbox',
      $this->plugin_slug . '_issuepanel_description_chars_number',
      $this->plugin_slug . '_issuepanel_linktojira_checkbox',

      $this->plugin_slug . '_license_key_text',
      $this->plugin_slug . '_license_status_text',

      $this->plugin_slug . '_statuscolor_todo_select',
      $this->plugin_slug . '_statuscolor_inprogress_select',
      $this->plugin_slug . '_statuscolor_done_select',

      $this->plugin_slug . '_delete_on_uninstall_checkbox',

    );
  }

  // ------------------------------------------------------------------------
  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following classes that make up the plugin:
   *
   * - Lewe_Jira_Connector_Loader. Orchestrates the hooks of the plugin.
   * - Lewe_Jira_Connector_i18n. Defines internationalization functionality.
   * - Lewe_Jira_Connector_Admin. Defines all hooks for the admin area.
   * - Lewe_Jira_Connector_Public. Defines all hooks for the public side of the site.
   *
   * Create an instance of the loader which will be used to register the hooks with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies() {
    /**
     * Load global classes.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lewe-jira-connector-bootstrap.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lewe-jira-connector-fontawesome.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lewe-jira-connector-i18n.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lewe-jira-connector-jira.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lewe-jira-connector-loader.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-lewe-jira-connector-license.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-lewe-jira-connector-admin.php';
    require plugin_dir_path(dirname(__FILE__)) . 'admin/partials/lewe-jira-connector-jira-host-meta-box.php';


    /**
     * The class responsible for defining all actions that occur in the public-facing side of the site.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-lewe-jira-connector-public.php';

    $this->loader = new Lewe_Jira_Connector_Loader();
  }

  // ------------------------------------------------------------------------
  /**
   * Load TinyMCE buttons.
   *
   * Registers and adds TinyMCE buttons for your plugin. Leave empty if you don't need any.
   * If you add buttons, make sure you configure them in admin/js/tinymce_buttons.js
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_tinymce_buttons() {
    if (!function_exists('jco_theme_setup')) {
      function jco_theme_setup() {
        add_action('init', 'jco_buttons');
      }
    }
    add_action('after_setup_theme', 'jco_theme_setup');

    if (!function_exists('jco_buttons')) {
      function jco_buttons() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) return;
        if (get_user_option('rich_editing') !== 'true') return;

        add_filter('mce_external_plugins', 'jco_add_buttons');
        add_filter('mce_buttons', 'jco_register_buttons');
      }
    }

    if (!function_exists('jco_add_buttons')) {
      function jco_add_buttons($plugin_array) {
        $plugin_array['jcobtn'] = plugins_url('../admin/js/tinymce_buttons.js', __FILE__);
        return $plugin_array;
      }
    }

    if (!function_exists('jco_register_buttons')) {
      function jco_register_buttons($buttons) {
        array_push($buttons, '|');
        array_push($buttons, 'jcobtn');
        return $buttons;
      }
    }
  }

  // ------------------------------------------------------------------------
  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Plugin_i18n class in order to set the domain and to register the hook with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale() {
    $plugin_i18n = new Lewe_Jira_Connector_i18n();
    $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
  }

  // ------------------------------------------------------------------------
  /**
   * Register admin hooks.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_admin_hooks() {
    $plugin_admin = new Lewe_Jira_Connector_Admin($this->get_plugin_title(), $this->get_plugin_slug(), $this->get_plugin_prefix(), $this->get_options(), $this->get_version());

    $this->loader->add_filter('plugin_action_links', $plugin_admin, 'action_links', 10, 5);
    $this->loader->add_filter('plugin_row_meta', $plugin_admin, 'meta_links', 10, 2);
    $this->loader->add_filter('manage_jira_host_posts_columns', $plugin_admin, 'set_jira_host_columns');

    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    $this->loader->add_action('admin_menu', $plugin_admin, 'create_menu');
    $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    $this->loader->add_action('init', $plugin_admin, 'register_post_types');
    $this->loader->add_action('save_post_jira_host', $plugin_admin, 'save_jira_host');
    $this->loader->add_action('manage_jira_host_posts_custom_column', $plugin_admin, 'show_jira_host_column', 10, 2);
    $this->loader->add_action('in_plugin_update_message-lewe-jira-connector/lewe-jira-connector.php', $plugin_admin, 'show_plugin_update_message', 10, 2);
  }

  // ------------------------------------------------------------------------
  /**
   * Register public hooks.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_public_hooks() {
    $plugin_public = new Lewe_Jira_Connector_Public($this->get_plugin_title(), $this->get_plugin_slug(), $this->get_plugin_prefix(), $this->get_version());

    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    $this->loader->add_shortcode('jco-issue', $plugin_public, 'shortcode_jco_issue');
    $this->loader->add_shortcode('jco-filter', $plugin_public, 'shortcode_jco_filter');
  }

  // ------------------------------------------------------------------------
  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    1.0.0
   */
  public function run() {
    $this->loader->run();
  }

  //-------------------------------------------------------------------------
  /**
   * Get the option names of the plugin.
   *
   * @return    array|string    Get the option names of the plugin.
   * @since     2.0.0
   */
  public function get_options() {
    return $this->options;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the title of the plugin.
   *
   * @return    string    The title of the plugin.
   * @since     1.0.0
   */
  public function get_plugin_title() {
    return $this->plugin_title;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the name of the plugin.
   * It is used to uniquely identify it within the context of WordPress and to define internationalization functionality.
   *
   * @return    string    The name of the plugin.
   * @since     1.0.0
   */
  public function get_plugin_slug() {
    return $this->plugin_slug;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the prefix of the plugin.
   *
   * @return    string    The prefix of the plugin.
   * @since     1.0.0
   */
  public function get_plugin_prefix() {
    return $this->plugin_prefix;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the URI of the plugin.
   *
   * @return    string    The URI of the plugin.
   * @since     1.0.0
   */
  public function get_plugin_uri() {
    return $this->plugin_uri;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the donation URI of the plugin.
   *
   * @return    string    The donation URI of the plugin.
   * @since     1.0.0
   */
  public function get_plugin_donation_uri() {
    return $this->plugin_donation_uri;
  }

  // ------------------------------------------------------------------------
  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @return    Lewe_Jira_Connector_Loader    Orchestrates the hooks of the plugin.
   * @since     1.0.0
   */
  public function get_loader() {
    return $this->loader;
  }

  // ------------------------------------------------------------------------
  /**
   * Get the version number of the plugin.
   *
   * @return    string    The version number of the plugin.
   * @since     1.0.0
   */
  public function get_version() {
    return $this->version;
  }
}
