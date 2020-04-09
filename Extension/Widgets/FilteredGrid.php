<?php
namespace Extension\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Extension\Utils\Language;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class FilteredGrid extends Widget_Base {
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('cca-filtered-grid', plugins_url('../assets/css/filtered-grid.css', __DIR__));
        wp_register_script('images-loaded', plugins_url('../assets/js/imagesloaded.pkgd.min.js', __DIR__), ['elementor-frontend'], false, true);
        wp_register_script('isotope', plugins_url('../assets/js/isotope.pkgd.min.js', __DIR__), ['elementor-frontend', 'images-loaded'], false, true);
        wp_register_script('cca-filtered-grid', plugins_url('../assets/js/filtered-grid.js', __DIR__), ['elementor-frontend', 'isotope'], false, true);
    }

    public function get_name() {
        return 'cca-filtered-grid';
    }

    public function get_title() {
        return __('Filtered Grid', Language::TEXT_DOMAIN);
    }

    public function get_icon() {
        return 'eicon-posts-masonry';
    }

    public function get_categories() {
        return ['calmachicha_addons'];
    }

    public function get_style_depends() {
        return ['cca-filtered-grid'];
    }

    public function get_script_depends() {
        return ['images-loaded', 'isotope', 'cca-filtered-grid'];
    }

    private function get_post_types() {
        $options = [];
        $exclude = ['attachment', 'elementor_library'];

        $args = [
            'public' => true,
        ];

        foreach (get_post_types($args, 'objects') as $post_type) {
            if (!isset($post_type->name)) {
                continue;
            }

            if (!isset($post_type->label)) {
                continue;
            }

            if (in_array($post_type->name, $exclude)) {
                continue;
            }

            $options[$post_type->name] = $post_type->label;
        }

        return $options;
    }

    private function get_image_sizes() {
        $image_sizes = get_intermediate_image_sizes();

        return array_combine($image_sizes, $image_sizes);
    }

    private function get_taxonomies() {
        $post_type = $this->get_controls_settings()['post_type'];
        $taxonomies_objects = get_object_taxonomies($post_type, 'object');
        $taxonomies = [];

        foreach($taxonomies_objects as $taxonomy) {
            $taxonomies[$taxonomy->name] = $taxonomy->label;
        }

        return $taxonomies;
    }

    protected function _register_controls() {
        // Content tab
        $this->grid_options_section();
        // Style tab
        $this->grid_style_section();
    }

    protected function grid_options_section() {
        $this->start_controls_section(
            'section_grid',
            [
                'label' => __('Grid Options', Language::TEXT_DOMAIN),
            ]
        );

        $this->add_control(
            'post_type',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Post Type', Language::TEXT_DOMAIN),
                'default' => 'post',
                'options' => $this->get_post_types(),
            ]
        );

        $this->add_control(
            'taxonomy_offset',
            [
                'type' => Controls_Manager::NUMBER,
                'label' => __('Taxonomy offset', Language::TEXT_DOMAIN),
                'default' => 0
            ]
        );

        $this->add_control(
            'image_size',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Image size', Language::TEXT_DOMAIN),
                'options' => $this->get_image_sizes(),
                'default' => 'thumbnail',
            ]
        );

        $this->end_controls_section();
    }

    protected function grid_style_section() {
        // Tab
        $this->start_controls_section(
            'section_grid_style',
            [
                'label' => __('Grid Options', Language::TEXT_DOMAIN),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'gutter',
            [
                'label' => __('Gutter', Language::TEXT_DOMAIN),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'frontend_available' => true, // this makes available in JS https://github.com/elementor/elementor/issues/8258#issuecomment-499550103
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $items = get_posts([
            'post_type' => $settings['post_type']
        ]);
        $this->add_render_attribute(
            'wrapper',
            [
                'class' => ['cca-filtered-grid', $settings['_css_classes']]
            ]
        );
        //$taxonomy_offset = isset($settings['taxonomy']) ? array_search($settings['taxonomy'], array_keys($this->get_taxonomies())) : 0;
        $taxonomy = array_keys($this->get_taxonomies())[$settings['taxonomy_offset']];
        //var_dump($settings);
        ?>
        <section class="cca-filtered-grid-filters">
            <div class="cca-filtered-grid-filters__filter cca-filtered-grid-filters__filter--is-active"
                 data-filter="*">
                <?= __('All', Language::TEXT_DOMAIN) ?>
            </div>
            <?php
                if (isset($taxonomy)):
                    $filters = get_terms([
                        'taxonomy' => $taxonomy,
                        'hide_empty' => false
                    ]);
                    foreach ($filters as $filter): ?>
                    <div class="cca-filtered-grid-filters__filter"
                         data-filter=".<?= $filter->slug ?>">
                        <?= $filter->name ?>
                    </div>
                <?php
                    endforeach;
                endif;
            ?>
        </section>
        <section <?= $this->get_render_attribute_string('wrapper'); ?>>
            <?php foreach ($items as $item):
                //$fields = get_fields($item->ID);
                $terms = get_the_terms($item->ID, $taxonomy);
                $caterories = implode(' ', array_column($terms, 'slug'));
                $item_image = get_the_post_thumbnail_url($item->ID, $settings['image_size']);
                ?>
            <article class="cca-filtered-grid__item <?= $caterories ?>">
                <a href="<?= get_permalink($item->ID) ?>">
                    <figure class="cca-filtered-grid__image">
                        <?php if ($item_image): ?>
                        <img src="<?= $item_image ?>" alt="<?= $item->post_title ?>" loading="lazy">
                        <?php endif; ?>
                    </figure>
                </a>
                <div class="cca-filtered-grid__contents">
                    <h2 class="cca-filtered-grid__title">
                        <?= wp_trim_words($item->post_title, 5) ?>
                    </h2>
                    <div class="cca-filtered-grid__description">
                        <?= wp_trim_words(get_the_excerpt($item->ID), 15) ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
            <?php if (!count($items)): ?>
            <div class="cca-filtered-grid__no-results">
                <?= __('There are no results', Language::TEXT_DOMAIN) ?>
            </div>
            <?php endif; ?>
        </section>
        <?php
    }
}
