<?php
namespace Extension;

use Elementor\Elements_Manager;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Extension {
    private static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct() {
        // Register widget scripts
        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);

        // Register widgets
        add_action( 'elementor/widgets/widgets_registered', [$this, 'register_widgets'] );
    }

    public function add_elementor_widget_categories(Elements_Manager $elements_manager) {
        $elements_manager->add_category(
            'calmachicha_addons',
            [
                'title' => 'Calmachicha Addons',
                'icon' => 'fa fa-plug',
            ]
        );
    }

    public function register_widgets() {
        Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostGrid());
        Plugin::instance()->widgets_manager->register_widget_type(new Widgets\FilteredGrid());
        Plugin::instance()->widgets_manager->register_widget_type(new Widgets\PostTerms());
    }
}

Extension::instance();
