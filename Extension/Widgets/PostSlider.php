<?php
namespace Extension\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Extension\Utils\Language;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PostSlider extends Widget_Base {
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('swiper', plugins_url('../assets/css/swiper.min.css', __DIR__));
        wp_register_style('post-slider', plugins_url('../assets/css/post-slider.css', __DIR__));
        wp_register_script('swiper', plugins_url('../assets/js/swiper.min.js', __DIR__), ['elementor-frontend'], false, true);
        wp_register_script('cca-post-slider', plugins_url('../assets/js/post-slider.js', __DIR__), ['elementor-frontend', 'swiper'], false, true);
    }

    public function get_name() {
        return 'cca-post-slider';
    }

    public function get_title() {
        return __('Post slider', Language::TEXT_DOMAIN);
    }

    public function get_icon() {
        return 'eicon-post-slider';
    }

    public function get_categories() {
        return ['calmachicha_addons'];
    }

    public function get_style_depends() {
        return ['swiper', 'post-slider'];
    }

    public function get_script_depends() {
        return ['swiper', 'cca-post-slider'];
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
                'class' => ['cca-post-slider', $settings['_css_classes']]
            ]
        );
        ?>
        <section <?= $this->get_render_attribute_string('wrapper'); ?>>
            <section class="post-slider">
                <div class="post-slider--swiper swiper-container">
                    <div class="swiper-wrapper">
                        <?php foreach ($items as $item): ?>
                            <div class="swiper-slide slide-post-wrapper">
                                <article class="slide-post">
                                    <a href="<?= get_permalink($item->ID) ?>">
                                        <figure class="slide-post">
                                            <?= get_the_post_thumbnail($item->ID, 'medium_large') ?>
                                            <figcaption class="slide-post">
                                                <div class="slide-post"><?= $item->post_title ?></div>
                                                <div class="slide-post"><?= wp_trim_words(get_the_excerpt($item->ID), 15) ?></div>
                                            </figcaption>
                                        </figure>
                                    </a>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Add Arrows -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <?php if (!count($items)): ?>
                    <div class="post-slider__no-results">
                        <?= __('There are no results', Language::TEXT_DOMAIN) ?>
                    </div>
                <?php endif; ?>
            </section>
        </section>
        <?php
    }
}
