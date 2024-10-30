<?php

/**
 * Admin functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/admin
 * @author     George Lewe <george@lewe.com>
 */
class Lewe_Jira_Connector_Admin {
  /**
   * The Bootstrap agent of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      Lewe_Jira_Connector_Bootstrap $bootstrap The Bootstrap agent of this plugin.
   */
  private $bootstrap;

  /**
   * The license agent of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      Lewe_Jira_Connector_License $license The license agent of this plugin.
   */
  private $license;

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
   * The title of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_title The title of this plugin.
   */
  private $plugin_title;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $version The current version of this plugin.
   */
  private $version;

  // -------------------------------------------------------------------------
  /**
   * Initialize the class and set its properties.
   *
   * @param string $plugin_slug The slug of this plugin.
   * @param string $version The version of this plugin.
   * @since    1.0.0
   */
  public function __construct($plugin_title, $plugin_slug, $plugin_prefix, $plugin_options, $version) {
    $this->license = new Lewe_Jira_Connector_License();
    $this->plugin_options = $plugin_options;
    $this->plugin_prefix = $plugin_prefix;
    $this->plugin_slug = $plugin_slug;
    $this->plugin_title = $plugin_title;
    $this->version = $version;
  }

  // -------------------------------------------------------------------------
  /**
   * Register the admin stylesheets.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {
    wp_enqueue_style($this->plugin_slug, plugins_url($this->plugin_slug) . '/global/css/' . $this->plugin_slug . '.css', array(), $this->version, 'all');
    wp_enqueue_style($this->plugin_slug . '-fa-all', plugins_url($this->plugin_slug) . '/global/css/' . $this->plugin_slug . '-fa-all.css', array(), $this->version, 'all');
    wp_enqueue_style($this->plugin_slug . '-admin', plugin_dir_url(__FILE__) . 'css/' . $this->plugin_slug . '-admin.css', array(), $this->version, 'all');
  }

  // -------------------------------------------------------------------------
  /**
   * Register the admin javascripts.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {
    wp_enqueue_script($this->plugin_slug . '-admin', plugin_dir_url(__FILE__) . 'js/' . $this->plugin_slug . '-admin.js', array( 'jquery' ), $this->version, false);
  }

  /**
   * Register Post Types
   *
   * @since    2.0.0
   */
  public function register_post_types() {
    $labels = array(
      'name' => _x('Jira Hosts', 'Post Type General Name', 'lewe-jira-connector'),
      'singular_name' => _x('Jira Host', 'Post Type Singular Name', 'lewe-jira-connector'),
      'menu_name' => __('Jira Hosts', 'lewe-jira-connector'),
      'name_admin_bar' => __('Jira Host', 'lewe-jira-connector'),
      'archives' => __('Jira Host Archives', 'lewe-jira-connector'),
      'attributes' => __('Jira Host Attributes', 'lewe-jira-connector'),
      'parent_item_colon' => __('Parent Host:', 'lewe-jira-connector'),
      'all_items' => __('All Hosts', 'lewe-jira-connector'),
      'add_new_item' => __('Add New Host', 'lewe-jira-connector'),
      'add_new' => __('Add New', 'lewe-jira-connector'),
      'new_item' => __('New Host', 'lewe-jira-connector'),
      'edit_item' => __('Edit Host', 'lewe-jira-connector'),
      'update_item' => __('Update Host', 'lewe-jira-connector'),
      'view_item' => __('View Host', 'lewe-jira-connector'),
      'view_items' => __('View Hosts', 'lewe-jira-connector'),
      'search_items' => __('Search Host', 'lewe-jira-connector'),
      'not_found' => __('Not found', 'lewe-jira-connector'),
      'not_found_in_trash' => __('Not found in Trash', 'lewe-jira-connector'),
      'featured_image' => __('Featured Image', 'lewe-jira-connector'),
      'set_featured_image' => __('Set featured image', 'lewe-jira-connector'),
      'remove_featured_image' => __('Remove featured image', 'lewe-jira-connector'),
      'use_featured_image' => __('Use as featured image', 'lewe-jira-connector'),
      'insert_into_item' => __('Insert into host', 'lewe-jira-connector'),
      'uploaded_to_this_item' => __('Uploaded to this host', 'lewe-jira-connector'),
      'items_list' => __('Hosts list', 'lewe-jira-connector'),
      'items_list_navigation' => __('Hosts list navigation', 'lewe-jira-connector'),
      'filter_items_list' => __('Filter hosts list', 'lewe-jira-connector'),
    );

    $args = array(
      'label' => __('Jira Host', 'lewe-jira-connector'),
      'description' => __('Jira host details', 'lewe-jira-connector'),
      'labels' => $labels,
      'supports' => array( 'title' ),
      'taxonomies' => array( 'category', 'post_tag' ),
      'hierarchical' => false,
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 5,
      'menu_icon' => plugins_url($this->plugin_slug) . '/global/images/icon-20x20.png',
      'show_in_admin_bar' => true,
      'show_in_nav_menus' => true,
      'can_export' => true,
      'has_archive' => true,
      'exclude_from_search' => true,
      'publicly_queryable' => true,
      'capability_type' => 'page',
    );

    register_post_type('jira_host', $args);
  }

  // -------------------------------------------------------------------------
  /**
   * Register Settings
   *
   * @since    1.0.0
   */
  public function register_settings() {
    foreach ($this->plugin_options as $option) {
      add_option($option, '');
      register_setting($this->plugin_slug . '_options_group', $option, $this->plugin_slug . '_callback');
    }

    /**
     * Meta box object that will be displayed on the Jira Host post edit page.
     */
    add_meta_box(
      'jira_host_meta_box',          // (string) (Required) Meta box ID (used in the 'id' attribute for the meta box).
      'Jira Host Details',           // (string) (Required) Title of the meta box.
      'display_jira_host_meta_box',  // (callable) (Required) Function that fills the box with the desired content. The function should echo its output.
      'jira_host',                   // (string|array|WP_Screen) (Optional) The screen or screens on which to show the box (such as a post type, 'link', or 'comment').
      'normal',                      // (string) (Optional) The context within the screen where the box should display. Post edit screen contexts include 'normal', 'side', and 'advanced'.
      'high'                         // (string) (Optional) The priority within the context where the box should show. Accepts 'high', 'core', 'default', or 'low'.
    );
  }

  // -------------------------------------------------------------------------
  /**
   * Create Admin Menu
   *
   * @since    1.0.0
   */
  public function create_menu() {
    // Parameters for add_menu_page:
    // - The title to be displayed in the browser window for this page,
    // - The text to be displayed for this menu item,
    // - Permission needed to view this menu item,
    // - The unique ID for this menu item or file reference,
    // - The name of the function to call when rendering this menu's page (null if file ref above),
    // - The icon of the menu entry ('dashicons-...' for WP dashicon)
    add_menu_page(
      $this->plugin_title . ' Admin',
      $this->plugin_title,
      'manage_options',
      $this->plugin_slug . '_admin_menu',
      null,
      plugins_url($this->plugin_slug) . '/global/images/icon-20x20.png'
    );

    // Parameters for add_submenu_page:
    // - TThe parent menu slug,
    // - The title to be displayed in the browser window for this page,
    // - The text to be displayed for this menu item,
    // - Permission needed to view this menu item,
    // - The unique ID for this menu item or file reference,
    // - The name of the function to call when rendering this menu's page (null if file ref above)
    add_submenu_page(
      $this->plugin_slug . '_admin_menu',
      $this->plugin_title . ' Options',
      'Options',
      'manage_options',
      plugin_dir_path(__FILE__) . 'partials/lewe-jira-connector-admin-options.php',
      null,
    );

    add_submenu_page(
      $this->plugin_slug . '_admin_menu',
      'Jira Hosts',
      'Jira Hosts',
      'manage_options',
      'edit.php?post_type=jira_host',
      null
    );

    add_submenu_page(
      $this->plugin_slug . '_admin_menu',
      $this->plugin_title . ' License',
      'License',
      'manage_options',
      plugin_dir_path(__FILE__) . 'partials/lewe-jira-connector-admin-license.php',
      null,
    );

    remove_submenu_page($this->plugin_slug . '_admin_menu', $this->plugin_slug . '_admin_menu');
  }

  // -------------------------------------------------------------------------
  /**
   * Action Links (shown underneath the plugin name on the admin plugin page)
   *
   * @since    1.0.0
   */
  public function action_links($actions, $file) {
    if ($file == $this->plugin_slug . '/' . $this->plugin_slug . '.php') {
      $settings = array( 'settings' => '<a href="admin.php?page=' . $this->plugin_slug . '%2Fadmin%2Fpartials%2Flewe-jira-connector-admin-options.php">' . esc_html(__('Settings', 'lewe-jira-connector')) . '</a>' );
      $actions = array_merge($settings, $actions);
    }

    return $actions;
  }

  // -------------------------------------------------------------------------
  /**
   * Meta Links (shown underneath the plugin description on the admin plugin page)
   *
   * @since    1.0.0
   */
  public function meta_links($links, $file) {
    if ($file == $this->plugin_slug . '/' . $this->plugin_slug . '.php') {
      $support_link = LEWE_JIRA_CONNECTOR_SUPPORT_URI;
      $doc_link = LEWE_JIRA_CONNECTOR_DOC_URI;
      $review_link = "https://wordpress.org/support/plugin/lewe-jira-connector/reviews/?filter=5#new-post";
      $links[] = '<a href="' . esc_url($support_link) . '" target="_blank" title="' . esc_html(__('Support', 'lewe-jira-connector')) . '">' . esc_html(__('Support', 'lewe-jira-connector')) . '</a>';
      $links[] = '<a href="' . esc_url($doc_link) . '" target="_blank" title="' . esc_html(__('Documentation', 'lewe-jira-connector')) . '">' . esc_html(__('Documentation', 'lewe-jira-connector')) . '</a>';
      $links[] = '<a href="' . esc_url($review_link) . '" target="_blank" title="' . esc_html(__('Rate and Review!', 'lewe-jira-connector')) . '">' . esc_html(__('Rate and Review!', 'lewe-jira-connector')) . '</a>';
    }

    return $links;
  }

  /**
   * Save Jira Host post type
   *
   * @param int $post_id
   * @return void
   * @since 2.0.0
   *
   */
  public function save_jira_host($post_id) {
    if (!current_user_can('edit_post', $post_id)) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $post = get_post($post_id);

    if (isset($_POST['post_type']) && $_POST['post_type'] == 'jira_host' && $post->post_type == 'jira_host') {
      $meta_options = array(
        'jira_host_description',
        'jira_host_url',
        'jira_host_username',
        'jira_host_password',
        'jira_host_token',
      );

      foreach ($meta_options as $meta_option) {
        update_post_meta($post_id, $meta_option, sanitize_text_field($_POST[$meta_option]));
      }
    }
  }

  /**
   * Set Jira host columns
   *
   * @param int $post_id
   * @return array    Post columns
   * @since 2.2.0
   *
   */
  public function set_jira_host_columns($columns) {
    $mycolumns = array(
      'jira_host_description' => __('Description', 'lewe-jira-connector'),
      'jira_host_url' => __('URL', 'lewe-jira-connector'),
      'jira_host_username' => __('Username', 'lewe-jira-connector'),
      'jira_host_id' => __('Host ID', 'lewe-jira-connector'),
    );

    $columns = $this->array_insert_after($columns, 'title', $mycolumns);
    return $columns;
  }

  /**
   * Show Jira host column
   *
   * @param string $column Column ID
   * @param int $post_id
   * @since 2.0.0
   *
   */
  public function show_jira_host_column($column, $post_id) {
    switch ($column) {
      case 'jira_host_description':
        echo esc_html(get_post_meta($post_id, 'jira_host_description', true));
        break;
      case 'jira_host_url':
        echo esc_html(get_post_meta($post_id, 'jira_host_url', true));
        break;
      case 'jira_host_username':
        echo esc_html(get_post_meta($post_id, 'jira_host_username', true));
        break;
      case 'jira_host_id':
        echo esc_html($post_id);
        break;
    }
  }

  /**
   * Upgrade message (if Upgrade Notice section exists in readme.txt).
   *
   * @param string $data
   * @param string $response
   * @since 2.0.0
   *
   */
  public function show_plugin_update_message($data, $response) {
    if (isset($data['upgrade_notice'])) {
      printf('<div style="padding:8px; background-color:#fefefe; border:1px solid #a0a0a0; border-radius:4px;">%s</div>', wpautop($data['upgrade_notice']));
    }
  }

  /**
   * Insert a value or key/value pair after a specific key in an array.
   * If key doesn't exist, value is appended to the end of the array.
   *
   * @param array $array
   * @param string $key
   * @param array $new
   *
   * @return array
   */
  private function array_insert_after(array $array, $key, array $new) {
    $keys = array_keys($array);
    $index = array_search($key, $keys);
    $pos = false === $index ? count($array) : $index + 1;

    return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
  }
}
