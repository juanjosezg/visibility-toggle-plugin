<?php
/**
 * Plugin Name: Custom Container Year with Elementor Element
 * Description: Adds a "Select Year" dropdown to Layout tabs of Elementor elements and appends the selected year as a direct class name.
 * Version: 2.1
 * Author: CDeparment
 * Text Domain: custom-container-year
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add control to Elementor container's Layout tab (new version)
add_action('elementor/element/container/section_layout/after_section_start', function ($element) {
    add_year_control($element);
}, 10, 1);

// Add control to Elementor section's Layout tab (old version)
add_action('elementor/element/section/section_layout/after_section_start', function ($element) {
    add_year_control($element);
}, 10, 1);

// Add control to Elementor column's Layout tab (old version)
add_action('elementor/element/column/layout/after_section_start', function ($element) {
    add_year_control($element);
}, 10, 1);

// Function to add year control
function add_year_control($element) {
    $element->add_control(
        'year_selection',
        [
            'label' => __('Select Year', 'custom-container-year'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '' => __('Select a Year', 'custom-container-year'),
                '2023' => __('2023', 'custom-container-year'),
                '2024' => __('2024', 'custom-container-year'),
                '2025' => __('2025', 'custom-container-year'),
                '2026' => __('2026', 'custom-container-year'),
                '2027' => __('2027', 'custom-container-year'),
            ],
            'default' => '',
            'prefix_class' => 'year-',
        ]
    );
}

// Ensure the class is added in the frontend for containers
add_filter('elementor/frontend/container/class_names', function ($classes, $container) {
    return add_year_class($classes, $container);
}, 10, 2);

// Ensure the class is added in the frontend for sections
add_filter('elementor/frontend/section/class_names', function ($classes, $section) {
    return add_year_class($classes, $section);
}, 10, 2);

// Ensure the class is added in the frontend for columns
add_filter('elementor/frontend/column/class_names', function ($classes, $column) {
    return add_year_class($classes, $column);
}, 10, 2);

// Function to add year class
function add_year_class($classes, $element) {
    $year = $element->get_settings('year_selection');
    
    if (!empty($year)) {
        $classes[] = 'year-' . sanitize_html_class($year);
    }
    
    return $classes;
}

// Ensure the class is added when rendering for all element types
add_filter('elementor/widget/render_content', function ($content, $widget) {
    $year = $widget->get_settings('year_selection');
    
    if (!empty($year) && is_string($content)) {
        // Check if there's already a class attribute
        if (strpos($content, 'class="') !== false) {
            // Use a regex to insert the year class
            $content = preg_replace(
                '/class="([^"]*)"/', 
                'class="$1 year-' . sanitize_html_class($year) . '"', 
                $content, 
                1
            );
        } else {
            // If no class attribute exists, add one
            $content = preg_replace(
                '/^(<[^ >]+)/', 
                '$1 class="year-' . sanitize_html_class($year) . '"', 
                $content, 
                1
            );
        }
    }
    
    return $content;
}, 10, 2);