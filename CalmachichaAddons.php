<?php
/**
 * Plugin Name: Calmachicha Addons
 * Description: A collection of widgets for Elementor.
 * Plugin URI:  https://calmachichapublicidad.com/
 * Version:     1.0.0
 * Author:      Calmachicha
 * Author URI:  https://calmachichapublicidad.com/
 * Text Domain: calmachicha-addons
 */
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use Extension\Utils\Language;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class CalmachichaAddons {
    const NAME = 'calmachicha-addons';
    const VERSION = '1.0.0';
    const MINIMUM_ELEMENTOR_VERSION = '2.6.0';
    const MINIMUM_PHP_VERSION = '7.0';

    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        add_action('init', [$this, 'i18n']);
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function i18n() {
        load_plugin_textdomain(Language::TEXT_DOMAIN);
    }

    public function init() {
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);

            return;
        }

        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);

            return;
        }

        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);

            return;
        }

        // Once we get here, We have passed all validation checks so we can safely include our plugin
        require_once('Extension/Extension.php');
    }

    /**
     * Warning when the site doesn't have Elementor installed or activated.
     */
    public function admin_notice_missing_main_plugin() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', Language::TEXT_DOMAIN),
            '<strong>' . esc_html__('Elementor Test Extension', Language::TEXT_DOMAIN) . '</strong>',
            '<strong>' . esc_html__('Elementor', Language::TEXT_DOMAIN) . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', Language::TEXT_DOMAIN),
            '<strong>' . esc_html__('Elementor Test Extension', Language::TEXT_DOMAIN) . '</strong>',
            '<strong>' . esc_html__('Elementor', Language::TEXT_DOMAIN) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', Language::TEXT_DOMAIN),
            '<strong>' . esc_html__('Elementor Test Extension', Language::TEXT_DOMAIN) . '</strong>',
            '<strong>' . esc_html__('PHP', Language::TEXT_DOMAIN) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

CalmachichaAddons::instance();
