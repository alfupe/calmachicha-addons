<?php
namespace Extension\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Extension\Utils\Language;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class PostGrid extends Widget_Base {
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        wp_register_style('cca-post-grid', plugins_url('../assets/css/post-grid.css', __DIR__));
    }

    public function get_name() {
        return 'post_grid';
    }

    public function get_title() {
        return __('Post Grid', Language::TEXT_DOMAIN);
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['calmachicha_addons'];
    }

    public function get_style_depends() {
        return ['cca-post-grid'];
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

        // Post type
        $this->add_control(
            'post_type',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Post Type', Language::TEXT_DOMAIN),
                'default' => 'post',
                'options' => $this->get_post_types(),
            ]
        );

        $this->add_responsive_control(
            'grid_columns',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Columns', Language::TEXT_DOMAIN),
                'desktop_default' => 3,
                'tablet_default' => 2,
                'mobile_default' => 1,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    5 => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .cca-post-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ]
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label' => __('Image size', Language::TEXT_DOMAIN),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_image_sizes(),
                'default' => 'post-thumbnail',
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

        // Columns margin.
        $this->add_responsive_control(
            'grid_style_columns_gap',
            [
                'label' => __('Columns gap', Language::TEXT_DOMAIN),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .cca-post-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Row margin.
        $this->add_responsive_control(
            'grid_style_rows_gap',
            [
                'label' => __('Rows gap', Language::TEXT_DOMAIN),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 30,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .cca-post-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
                ]
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
                'class' => ['cca-post-grid', $settings['_css_classes']]
            ]
        );
    ?>
        <section <?= $this->get_render_attribute_string('wrapper'); ?>>
            <?php foreach ($items as $item): ?>
            <article>
                <figure>
                    <img src="<?= get_the_post_thumbnail_url($item->ID, $settings['image_size']) ?>" alt="<?= $item->post_title ?>">
                </figure>
                <div>
                    <?= $item->post_title ?>
                </div>
                <div>
                    <?= $item->post_content ?>
                </div>
            </article>
            <?php endforeach; ?>
        </section>
    <?php
    }
}
