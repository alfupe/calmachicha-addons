<?php
namespace Extension\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Extension\Utils\Language;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PostTerms extends Widget_Base {
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('cca-post-terms', plugins_url('../assets/css/post-terms.css', __DIR__));
    }

    public function get_name() {
        return 'post-terms';
    }

    public function get_title() {
        return __('Post Terms', Language::TEXT_DOMAIN);
    }

    public function get_icon() {
        return 'eicon-tags';
    }

    public function get_categories() {
        return ['calmachicha_addons'];
    }

    public function get_style_depends() {
        return ['cca-post-terms'];
    }

    protected function get_post_taxonomies() {
        $taxonomies = [];

        foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy) {
            $taxonomies[$taxonomy->name] = $taxonomy->label;
        }

        return $taxonomies;
    }

    protected function _register_controls() {
        // Content tab
        $this->grid_options_section();
    }

    protected function grid_options_section() {
        $this->start_controls_section(
            'section_grid',
            [
                'label' => __('Grid Options', Language::TEXT_DOMAIN),
            ]
        );

        $this->add_control(
            'taxonomy',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Taxonomy', Language::TEXT_DOMAIN),
                'options' => $this->get_post_taxonomies(),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $terms = get_the_terms(get_the_ID(), $settings['taxonomy']);
        $this->add_render_attribute(
            'wrapper',
            [
                'class' => ['cca-post-terms', $settings['_css_classes']]
            ]
        );
    ?>
        <section <?= $this->get_render_attribute_string('wrapper'); ?>>
            <?php foreach ($terms as $term): ?>
            <div>
                <a href="<?= get_term_link($term->term_id) ?>"><?= $term->name ?></a>
            </div>
            <?php endforeach; ?>
        </section>
    <?php
    }
}
