<?php
/**
 * Trang admin chính
 */
function DQH_register_menu() {
    add_options_page(
        __( 'Series System'),
        'Series System',
        'manage_options',
        'dqh-series-option-page',
        'dqh_settings_page',
        'dashicons-admin-users',
        21
    );
    add_action('admin_init', 'DQH_plugin_settings');
}
add_action( 'admin_menu', 'DQH_register_menu' );

function DQH_plugin_settings() {
    //register our settings
    register_setting( 'dqh-plugin', 'DQH_name_type' );
    register_setting( 'dqh-plugin', 'DQH_name' );
    register_setting( 'dqh-plugin', 'DQH_rewrite' );
    register_setting( 'dqh-plugin', 'DQH_lang_parent_post' );
    register_setting( 'dqh-plugin', 'DQH_title_in_post' );
    register_setting( 'dqh-plugin', 'DQH_next_post' );
    register_setting( 'dqh-plugin', 'DQH_previous_post' );
    register_setting( 'dqh-plugin', 'DQH_paging_chapter' );
    register_setting( 'dqh-plugin', 'DQH_chapers_per_page' );
    register_setting( 'dqh-plugin', 'DQH_html_list' );
}