<?php
/**
 * Plugin Name: MNDEV Plugin - Code Notes
 * Plugin URI: https://dominhnhut.com/
 * Description: Plugin to manage internal code notes and feature documentation for MNDEV website.
 * Version: 1.0.0
 * Author: MNDEV dominhnhut.com
 * Author URI: https://dominhnhut.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mndev-plugin
 * Domain Path: /languages
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MNDEV_PLUGIN_VERSION', '1.0.0');
define('MNDEV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MNDEV_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class MNDEVPlugin
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Load text domain
        load_plugin_textdomain('mndev-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Handle AJAX requests
        add_action('wp_ajax_mndev_add_note', array($this, 'ajax_add_note'));
        add_action('wp_ajax_mndev_get_notes', array($this, 'ajax_get_notes'));
        add_action('wp_ajax_mndev_update_note', array($this, 'ajax_update_note'));
        add_action('wp_ajax_mndev_delete_note', array($this, 'ajax_delete_note'));

        // Add plugin action links
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
        add_filter('plugin_row_meta', array($this, 'add_plugin_meta_links'), 10, 2);
    }

    /**
     * Activate plugin
     */
    public function activate()
    {
        // Create notes table
        $this->create_notes_table();
    }

    /**
     * Deactivate plugin
     */
    public function deactivate()
    {
        // Flush rewrite rules on deactivation
        flush_rewrite_rules();
    }

    /**
     * Create notes table
     */
    private function create_notes_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'mndev_notes';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            content text NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_menu_page(
            __('MNDEV Notes', 'mndev-plugin'),
            __('MNDEV Notes', 'mndev-plugin'),
            'manage_options',
            'mndev-notes',
            array($this, 'admin_page'),
            'dashicons-sticky',
            25
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook)
    {
        if ('toplevel_page_mndev-notes' !== $hook) {
            return;
        }

        wp_enqueue_style('mndev-plugin-style', MNDEV_PLUGIN_URL . 'assets/css/style.css', array(), MNDEV_PLUGIN_VERSION);
        wp_enqueue_script('mndev-plugin-script', MNDEV_PLUGIN_URL . 'assets/js/script.js', array('jquery'), MNDEV_PLUGIN_VERSION, true);

        wp_localize_script('mndev-plugin-script', 'mndev_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mndev_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this note?', 'mndev-plugin'),
                'note_added' => __('Note added successfully!', 'mndev-plugin'),
                'note_updated' => __('Note updated successfully!', 'mndev-plugin'),
                'note_deleted' => __('Note deleted successfully!', 'mndev-plugin'),
                'error' => __('An error occurred. Please try again.', 'mndev-plugin')
            )
        ));
    }

    /**
     * Admin page
     */
    public function admin_page()
    {
        include_once MNDEV_PLUGIN_DIR . 'includes/admin-page.php';
    }

    /**
     * AJAX: Add note
     */
    public function ajax_add_note()
    {
        check_ajax_referer('mndev_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'mndev-plugin'));
        }

        $title = sanitize_text_field($_POST['title']);
        $content = wp_kses_post($_POST['content']);

        if (empty($title) || empty($content)) {
            wp_send_json_error(array('message' => __('Title and content are required.', 'mndev-plugin')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'mndev_notes';

        $result = $wpdb->insert(
            $table_name,
            array(
                'title' => $title,
                'content' => $content,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s')
        );

        if ($result) {
            wp_send_json_success(array(
                'id' => $wpdb->insert_id,
                'message' => __('Note added successfully!', 'mndev-plugin')
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to add note.', 'mndev-plugin')));
        }
    }

    /**
     * AJAX: Get notes
     */
    public function ajax_get_notes()
    {
        check_ajax_referer('mndev_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'mndev-plugin'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'mndev_notes';

        $notes = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY updated_at DESC"
        );

        wp_send_json_success($notes);
    }

    /**
     * AJAX: Update note
     */
    public function ajax_update_note()
    {
        check_ajax_referer('mndev_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'mndev-plugin'));
        }

        $id = intval($_POST['id']);
        $title = sanitize_text_field($_POST['title']);
        $content = wp_kses_post($_POST['content']);

        if (empty($id) || empty($title) || empty($content)) {
            wp_send_json_error(array('message' => __('ID, title, and content are required.', 'mndev-plugin')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'mndev_notes';

        $result = $wpdb->update(
            $table_name,
            array(
                'title' => $title,
                'content' => $content,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s', '%s'),
            array('%d')
        );

        if ($result !== false) {
            wp_send_json_success(array('message' => __('Note updated successfully!', 'mndev-plugin')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update note.', 'mndev-plugin')));
        }
    }

    /**
     * AJAX: Delete note
     */
    public function ajax_delete_note()
    {
        check_ajax_referer('mndev_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'mndev-plugin'));
        }

        $id = intval($_POST['id']);

        if (empty($id)) {
            wp_send_json_error(array('message' => __('Note ID is required.', 'mndev-plugin')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'mndev_notes';

        $result = $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );

        if ($result) {
            wp_send_json_success(array('message' => __('Note deleted successfully!', 'mndev-plugin')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete note.', 'mndev-plugin')));
        }
    }

    /**
     * Add action links to plugin
     */
    public function add_action_links($links)
    {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            admin_url('admin.php?page=mndev-notes'),
            __('Settings', 'mndev-plugin')
        );

        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add plugin meta links
     */
    public function add_plugin_meta_links($links, $file)
    {
        if (plugin_basename(__FILE__) === $file) {
            $links[] = '<a href="https://dominhnhut.com/" target="_blank">' . __('Author Website', 'mndev-plugin') . '</a>';
            $links[] = '<a href="https://dominhnhut.com/lien-he/" target="_blank">' . __('Contact', 'mndev-plugin') . '</a>';
        }
        return $links;
    }
}

// Initialize the plugin
new MNDEVPlugin();
