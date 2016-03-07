<?php
/*
Plugin Name: DQH Series System
Plugin URI: https://github.com/dinhquochan/dqh-series-plugin
Description: Create the series system for WordPress.
Version: 1.0
Author: Dinh Quoc Han
Author URI: https://www.dinhquochan.com
License: GPLv2 or later
*/
global $wpdb;

define("DQH_PATH", plugin_dir_path(__FILE__));
define("DQH_URI", plugin_dir_url(__FILE__));
define("DQH_VERSION", 1.0);

require (DQH_PATH . 'libs/register_menu.php');
require (DQH_PATH . 'libs/admin_pages.php');
require (DQH_PATH . 'libs/helpers.php');
require (DQH_PATH . 'libs/series_module.php');
require (DQH_PATH . 'init.php');

$cfg = array(
    'name_type'      => get_option('DQH_name_type', 'story'),
    'setting_type'   => array(
        'labels' => array(
            'name' => get_option('DQH_name', 'Truyện Dài'),
            'singular_name' => get_option('DQH_name', 'Truyện Dài')
        ),
        'show_ui' => true,
        'public' => true,
        'has_archive' => true,
        'taxonomies'    => array('post_tag'),
        'rewrite' => array('slug' => get_option('DQH_rewite', 'chapter'), 'with_front' => false),
        'supports' => array( 'title', 'editor', 'author', 'excerpt', 'comments' )
    ),
    'lang_parent_post' => get_option('DQH_lang_parent_post', 'Bài Viết Gốc'),
    'lang_add_new'     => 'Add New Post',
    'lang_type'        => get_option('DQH_name', 'Truyện Dài'),
    'lang_select'      => get_option('DQH_name', 'Truyện Dài'),
    'title_in_post'    => get_option('DQH_title_in_post', 'Danh sách các phần'),
    'next_post'        => get_option('DQH_next_post', 'Phần Sau'),
    'previous_post'    => get_option('DQH_previous_post', 'Phần Trước'),
    'paging_chapter'   => get_option('DQH_paging_chapter', 1),
    'chapers_per_page' => get_option('DQH_chapers_per_page', 50),
    'html_list'        => get_option('DQH_html_list', 'ul'),
);

$app = new DQH_Series_Module;
$app->config($cfg);
$app->run();