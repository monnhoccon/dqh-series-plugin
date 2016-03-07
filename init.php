<?php
/**
 * Đăng ký CSS
 */
function DQH_register_css()
{
    wp_register_style('DQH-style', DQH_URI . 'assets/css/style.css');
    wp_enqueue_style('DQH-style');
}
add_action('init', 'DQH_register_css');
