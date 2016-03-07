<?php
/*
Author: Dinh Quoc Han <dinhquochan96@gmail.com>
Author URI: https://www.dinhquochan.com
*/

class DQH_Series_Module {
    public $args = array();

    public function config( $args )
    {
        $this->args = $args;
    }

    public function run()
    {
        add_action( 'init', array($this, 'createPostType'));
        add_action( 'edit_form_after_title', array($this, 'moduleFieldsAfterTitle') );
        add_action('save_post', array($this, 'savePost'));
        add_action( 'save_post', array($this, 'saveCustomSidebar' ));
        add_action( 'admin_init', array($this, 'addCustomColunm') );
        add_action('add_meta_boxes', array($this, 'addSidebarMetabox') );
        add_filter( 'the_content', array($this, 'addContentPost'));
    }
    /**
     * Create Post Type
     * @return void
     */
    public function createPostType()
    {
        register_post_type(  $this->args['name_type'], $this->args['setting_type']);
    }

    /**
     * The fields after title post
     * @param  boolean $postData Data Post
     * @return void
     */
    public function moduleFieldsAfterTitle( $postData = false ){
        $scr    = get_current_screen();
        $value  = '';
        $title = '';
        $id = 0;

        $postDataNew = (isset($_GET['parent_id'])) ? $_GET['parent_id'] : 0;
        if ( $postData ) {
            $t = get_post($postData);
            $a = get_post($t->post_parent);
            $title = $a->post_title;
            $id     = $a->ID;
        }

        if(empty($title)) {
            $t = get_post($postDataNew);
            $title = $t->post_title;
            $id     = $t->ID;
        }

        if ($scr->id == $this->args['name_type']){
            if(!empty($title)){
                echo '<input type="hidden" name="parent_id" value="'.$id.'">';
                echo '<p>'.$this->args['lang_parent_post'].'<strong>'.$title.'</strong> (ID: '.$id.')</p>';
            } else {
                echo '<label>'.$this->args['lang_parent_post'].'</label> <input type="number" name="parent_id" value="0"> (<em>This is ID Format</em>)';
            }
        }
    }
    /**
     * Save Data Post
     * @param  integer $post_id ID Post
     * @return void
     */
    public function savePost( $post_id ) {
        $chapterID = isset( $_POST['parent_id'] ) ? $_POST['parent_id'] : false ;
        if ( ! wp_is_post_revision( $post_id ) && $chapterID ){
            remove_action('save_post', array($this, 'savePost'));
            $postdata = array(
                'ID' => $post_id,
                'post_parent' => $chapterID
            );
            wp_update_post( $postdata );
            add_action('save_post', array($this, 'savePost'));
        }
    }

    /**
     * Custom Manager
     * @return void
     */
    public function addCustomColunm(){
        $_GET['post_type'] = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
        if($_GET['post_type'] != $this->args['name_type']):
            add_action( 'manage_posts_custom_column' , array($this, 'colunmManagerPost'), 10, 2 );
            add_filter( 'manage_posts_columns' , array($this, 'colunmManagerPostTitle'));
        endif;
    }

    public function colunmManagerPostTitle( $columns ) {
        return array_merge( $columns, array( 'dqhAddSeries' => __( $this->args['name_type']) ) );
    }

    public function colunmManagerPost( $column, $post_id ) {
        if($column == "dqhAddSeries" && get_post_meta( $post_id, '_mts_post_type', true ) == '1'){
            $html = '<a href="'. admin_url('post-new.php?post_type='. $this->args['name_type'] .'&parent_id='. $post_id).'" class="button button-primary">'. $this->args['lang_add_new'] .'</a>';
            echo $html;
        }
    }

    /** list post */
    public function getListPosts( $id = 0) {
        global $post, $wpdb;
        $id = ($id <= 0) ? get_the_ID() : $id;
        $current_post_id = get_the_ID();
        if($this->args['paging_chapter'] == 1){
            $per_page     = $this->args['chapers_per_page'];
            ## lấy số trang hiện tại
            $current_page =  isset($_GET['trang']) ? absint($_GET['trang']) : 1;
            ## đếm tổng dữ liệu có
            $total_item   = $wpdb->get_var(sprintf('select count(*) from %s where post_type = \'%s\' and post_parent = %d and post_status = \'%s\'', $wpdb->posts, $this->args['name_type'], $id, 'publish'));
            ## lấy tổng số trang sẽ là số chia hết của dữ liệu chia cho số bài trên 1 trang.
            $total_page   = ceil($total_item/$per_page);
            ## lấy start,limit để query SQL
            $start        = ($current_page-1)*$per_page;
            $limit        = ($current_page*$per_page) - 1;
            $limit        = ($limit == 0) ? 1 : $limit;
            ## xử lý dữ liệu lấy theo trang
            $query = $wpdb->get_results(sprintf('select ID, post_title from %s where post_type = \'%s\' and post_parent = %d and post_status = \'%s\' order by ID LIMIT %d, %d', $wpdb->posts, $this->args['name_type'], $id, 'publish', $start, $limit));
        }
        else
        {
            $query = $wpdb->get_results(sprintf('select * from %s where post_type = \'%s\' and post_parent = %d and post_status = \'%s\' order by ID', $wpdb->posts, $this->args['name_type'], $id, 'publish'));
        }
        $out   = '';

        if ($query) {
            $out .= '<'.$this->args['html_list'].'>';
            foreach ( $query as $k ) {
                $uri = get_permalink($k->ID);
                $out .=  ($k->ID == $current_post_id) ? '<li><strong>'.$k->post_title.'</strong></li>' : '<li><a href="'.$uri.'" title="'.$k->post_title.'">'.$k->post_title .'</a></li>';
            }
            $out .=  '</'.$this->args['html_list'].'>';
        }
        if($this->args['paging_chapter'] == 1)
            $out .= paginate_links( array(
                'base' => get_permalink() . '%_%',
                'format' => '?trang=%#%',
                'current' => max( 1, $current_page),
                'total' => $total_page
            ));

        return $out;
    }

    public function theParentPost(){
        global $post;
        $out = '<a href="'.get_permalink($post->post_parent).'" title='.get_the_title($post->post_parent).'"><i class="fa fa-home"></i> '.get_the_title($post->post_parent).'</a>';
        return $out;
    }

    public function theNextPost($id = 0){
        global $post, $wpdb;
        $id = ($id <= 0) ? get_the_ID() : $id;
        $k = $wpdb->get_row(sprintf('select * from %s where post_type = \'%s\' and post_parent = %d and post_status = \'%s\' and ID > %s', $wpdb->posts, $this->args['name_type'], $post->post_parent, 'publish', $id));
        if ($k) {
            $uri = get_permalink($k->ID);
            $out = '<a href="'.$uri.'" rel="next" title="'.$k->post_title.'" class="pull-right">'.$this->args['next_post'].' <i class="fa fa-long-arrow-right"></i></a>';
            return $out;
        }
    }

    public function thePreviousPost($id = 0){
        global $post, $wpdb;
        $id = ($id <= 0) ? get_the_ID() : $id;
        $k = $wpdb->get_row(sprintf('select ID, post_title from %s where post_type = \'%s\' and post_parent = %d and post_status = \'%s\' and ID < %s ORDER BY `ID` DESC', $wpdb->posts, $this->args['name_type'], $post->post_parent, 'publish', $id));
        if ($k) {
            $uri = get_permalink($k->ID);
            $out = '<a href="'.$uri.'" rel="previous" title="'.$k->post_title.'" class="pull-left"><i class="fa fa-long-arrow-left"></i> '.$this->args['previous_post'].'</a>';
            return $out;
        }
    }

    public function addSidebarMetabox() {
        $screens = array('post');
        foreach ($screens as $screen) {
            add_meta_box(
                'dqhbox_sidebar_metabox',                  // id
                __($this->args['lang_type']),    // title
                array($this, 'innerSidebarMetabox'),            // callback
                $screen,                                // post_type
                'side',                                 // context (normal, advanced, side)
                'high'                               // priority (high, core, default, low)
            // callback args ($post passed by default)
            );
        }
    }

    /**
     * Print the box content.
     *
     * @param WP_Post $post The object for the current post/page.
     */
    public function innerSidebarMetabox($post) {
        global $wp_registered_sidebars;

        // Add an nonce field so we can check for it later.
        wp_nonce_field('dqhbox_inner_sidebar_metabox', 'dqhbox_inner_sidebar_metabox_nonce');

        /*
        * Use get_post_meta() to retrieve an existing value
        * from the database and use the value for the form.
        */

        $custom_type_post = get_post_meta( $post->ID, '_mts_post_type', true );

        echo '<div class="mts_sidebar_location_fields"><br/><input type="checkbox" name="mts_post_type" value="1" '.($custom_type_post != 1 ? '' : 'checked').'> '. $this->args['lang_select'] .'.</div>';

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                function mts_toggle_sidebar_location_fields() {
                    $('.mts_sidebar_location_fields').toggle(($('#mts_custom_sidebar').val() != 'mts_nosidebar'));
                }
                mts_toggle_sidebar_location_fields();
                $('#mts_custom_sidebar').change(function() {
                    mts_toggle_sidebar_location_fields();
                });
            });
        </script>
        <?php
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function saveCustomSidebar( $post_id ) {

        /*
        * We need to verify this came from our screen and with proper authorization,
        * because save_post can be triggered at other times.
        */

        // Check if our nonce is set.
        if ( ! isset( $_POST['dqhbox_inner_sidebar_metabox_nonce'] ) )
            return $post_id;

        $nonce = $_POST['dqhbox_inner_sidebar_metabox_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'dqhbox_inner_sidebar_metabox' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize user input.
        $custom_type_post = sanitize_text_field( $_POST['mts_post_type'] );

        update_post_meta( $post_id, '_mts_post_type', $custom_type_post );
    }

    public function addContentPost( $content ){
        global $post;
        $story = get_post_meta( $post->ID, '_mts_post_type', true );

        $new_addion = '';

        $story[0] = isset($story[0]) ? $story[0] : 0;

        if ($story[0] == '1'):
            $new_addion .= '<h3>'.$this->args['title_in_post'].'</h3><p>'.  $this->getListPosts() . '</p>';

        elseif(get_post_type() == $this->args['name_type']):
            $new_addion .= '<div class="nav-signle">'.$this->thePreviousPost().$this->theNextPost().'</div><div class="clearfix"></div><h4>'.$this->theParentPost() .'</h4>';
        endif;

        $content .= $new_addion;

        return $content;
    }

}