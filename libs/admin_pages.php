<?php
/*-----------------------------------------------------
 *                      SETTINGS
 *-----------------------------------------------------*/
function dqh_settings_page()
{
    echo '<h1>Series System</h1>';
    ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'dqh-plugin' ); ?>
        <?php do_settings_sections( 'dqh-plugin' ); ?>
        <p>Don't change it if you don't know what is it !</p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Name Type Post</th>
                <td><input type="text" name="DQH_name_type" value="<?php echo esc_attr( get_option('DQH_name_type', 'story') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Name Type</th>
                <td><input type="text" name="DQH_name" value="<?php echo esc_attr( get_option('DQH_name', 'Truyện Dài') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Rewrite</th>
                <td><input type="text" name="DQH_rewrite" value="<?php echo esc_attr( get_option('DQH_rewrite', 'chapter') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Name Parrent Link Post</th>
                <td><input type="text" name="DQH_lang_parent_post" value="<?php echo esc_attr( get_option('DQH_lang_parent_post', 'Bài viết gốc') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Name Title List Chapter</th>
                <td><input type="text" name="DQH_title_in_post" value="<?php echo esc_attr( get_option('DQH_title_in_post', 'Danh sách các bài viết') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Next</th>
                <td><input type="text" name="DQH_next_post" value="<?php echo esc_attr( get_option('DQH_next_post', 'Bài Viết Sau') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Previous</th>
                <td><input type="text" name="DQH_previous_post" value="<?php echo esc_attr( get_option('DQH_previous_post','Bài Viết Trước') ); ?>" /></td>
            </tr>


            <tr valign="top">
                <th scope="row">Allow Paging Chapters List</th>
                <td><input type="checkbox" name="DQH_paging_chapter" value="1" <?php echo (get_option('DQH_paging_chapter', 1) == 1) ? 'checked' : ''; ?>/></td>
            </tr>


            <tr valign="top">
                <th scope="row">Chapters Per Page</th>
                <td><input type="number" name="DQH_chapers_per_page" value="<?php echo esc_attr( get_option('DQH_chapers_per_page', 50)); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">HTML List</th>
                <td><input type="text" name="DQH_html_list" value="<?php echo esc_attr( get_option('DQH_html_list', 'ol')); ?>" /></td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <p>Author: <a href="https://www.dinhquochan.com/">Đinh Quốc Hân</a> - Version: <?php echo DQH_VERSION; ?></p>
    <?php
}