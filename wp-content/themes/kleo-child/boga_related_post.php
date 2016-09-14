<?php
class boga_related_post

{
    var $related_post = array();
    var $retrieved_post = array();
    var $query_done = null;

    function __construct()
    {
        add_shortcode( 'RelatedPostSidebar', array($this, 'sidebar_related_post') );

        add_filter( 'the_content', array($this, 'inside_content_related_post'));
    }

    function query_related_post(){
        if ($this->query_done){return;}

        global $post;
        /* Query args */
        $args = array(
            'post__not_in' => array($post->ID),
            'showposts' => 16,
            'order' => 'DESC',
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'post_date_gmt',
            'fields' => 'ids'
        );
        $categories = get_the_category($post->ID);
        $tags = wp_get_post_tags($post->ID);
        $cat_name = $categories[0]->name;

        if (!empty($tags) && $cat_name != "Streetstyle") {
            if (!has_term( 'BogadiaTV', 'post_tag', $this->retrieved_post[0] )){
                $tag_ids = array();
                foreach ($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
            }else{
                $tag_ids = 2546;
            }
            $args['tag__in'] = $tag_ids;
        }

        if (!empty($categories) && !has_term( 'BogadiaTV', 'post_tag', $this->retrieved_post[0] )) {
            $category_ids = array();
            foreach ($categories as $rcat) {
                $category_ids[] = $rcat->term_id;
            }
            $args['category__in'] = $category_ids;
        }
        $this->related_post = get_posts($args);
        $this->query_done = true;
    }

    function get_related_post($num_post){
        $posts_to_retrieve = array();

        for ($i=0; $i < $num_post; $i++){
            $post_to_retrieve = array_pop($this->related_post);
            if ($post_to_retrieve){
                $posts_to_retrieve[$i] = $post_to_retrieve;
            }else{
                if ($this->retrieved_post){
                    $this->related_post = $this->retrieved_post;
                    $this->retrieved_post = null;
                }else{
                    break;

                }
            }
        }
        $this->retrieved_post = $posts_to_retrieve;
        return $posts_to_retrieve;
    }

    function cut_title($title){
        if ( mb_strlen( $title, 'utf8' ) > 50 ) {
            $last_space = strrpos( substr( $title, 0, 50 ), ' ' ); // find the last space within 35 characters
            return substr( $title, 0, $last_space ) . ' ...';
        }
        return $title;
    }

    function inside_content_related_post( $content ) {

        if ( is_single() && ! is_admin()  && (in_category('Belleza') || in_category('Lifestyle') || in_category('Moda') || in_category('Humor')) ) {
            function prefix_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
                $closing_p = '</p>';
                $paragraphs = explode( $closing_p, $content );
                foreach ($paragraphs as $index => $paragraph) {

                    if ( trim( $paragraph ) ) {
                        $paragraphs[$index] .= $closing_p;
                    }

                    if ( $paragraph_id == $index + 1 ) {
                        $paragraphs[$index] .= $insertion;
                    }
                }

                return implode( '', $paragraphs );
            }
            self::query_related_post();
            $likes_posts = self::get_related_post(3);
            $c=0;
            $post_re1 = '';
            $post_re2 = '';
            $post_re3 = '';
            foreach( $likes_posts as $likes_post ) {
                $c=$c+1;
                $link = get_permalink($likes_post);
                $title = self::cut_title(get_the_title($likes_post));
                if ($c==1) {
                    $post_re1 = '<h6 style="float:left;line-height: 17px; margin: 5px 0px;"><a href="'.$link.'"  title="'.$title.'" class="re_post_in_post">'.$title.'</a></h6>';
                }
                if ($c==2) {
                    $post_re2 = '<hr style="margin:0px auto; width:100%;clear: left;" /><h6 style="float:left;line-height: 17px; margin: 5px 0px;"><a href="'.$link.'" title="'.$title.'" class="re_post_in_post">'.$title.'</a></h6>';
                }
                if ($c==3) {
                    $post_re3 = '<hr style="margin:0px auto; width:100%;clear: left;" /><h6 style="float:left;line-height: 17px; margin: 5px 0px;"><a href="'.$link.'" title="'.$title.'" class="re_post_in_post">'.$title.'</a></h6>';
                }
            }
            $post_re = $post_re1.$post_re2.$post_re3;

            $ad_code = '<div style="max-width: 180px; float: left; margin:0px 20px 20px 0px;"><h5 style="margin:0px auto; font-size: 14px; text-align: center;"><strong>ARTÍCULOS DE INTERÉS</strong></h5><hr style="margin: 0px 0px 5px 0px;" />'.$post_re.'</div>';

            return prefix_insert_after_paragraph( $ad_code, 2, $content );
        }
        return $content;
    }

    function sidebar_related_post(){
        self::query_related_post();
        $related_posts = self::get_related_post(4);
        if (!empty( $related_posts )){
            echo '<h4 class="widget-title">También te gustará</h4>';
            foreach( $related_posts as $related_post ) {
                $link = get_permalink($related_post);
                $title = get_the_title($related_post);
                echo '<div class="post-sidebar">';
                echo '<a class="sidebar-img" href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $related_post, 'thumbnail' ).'</a>'.'<h5 class="title-post-sidebar"><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h5>';
                echo '</div>';
            }
        }
    }

    function main_related_post(){
        if ( is_single() && ! is_admin()  && (in_category('Belleza') || in_category('Lifestyle') || in_category('Moda') || in_category('Humor')) ){
            self::query_related_post();
            $related_posts = self::get_related_post(9);

            echo '<section class="container-wrap">'
                . '<div class="container">'
                . '<div class="related-wrap">'
                . '<div class="hr-title hr-long"><abbr>ARTÍCULOS RELACIONADOS</abbr></div>'
                . '<ul style="list-style-type: none; padding-left: 0px;">';

            foreach( $related_posts as $likes_post ) {
                $post_id = $likes_post;
                $link = get_permalink($post_id);
                $title = self::cut_title(get_the_title($post_id));
                //$img_url = wp_get_attachment_url(get_post_thumbnail_id($post_id));
    /*            $img_url = get_the_post_thumbnail( $post_id, 'thumbnail' );*/
                echo '<li id="post-'. $post_id .'" class="post-item col-sm-4 boga-related">'
                    . '<article>'
                    . '<div class="post-image">'
                    . '<a title="'. $title .'" href="'. $link .'" class="element-wrap">'
                    //. '<img src="'. $img_url .'" alt="'. $title .'" class="attachment-thumbnail wp-post-image">'
                    . get_the_post_thumbnail( $post_id, 'medium' )
                    . '</a>'
                    . '</div><!--end post-image-->'
                    . '<div class="entry-content">'
                    . '<h5 class="post-title entry-title"><a title="'. $title .'" href="'. $link .'">'. $title .'</a></h5>'
                    . '</div>'
                    . '</article>'
                    . '</li>';
            }

            echo '</ul>'
                . '</div>'
                . '</div>'
                . '</section>';

        }
    }
}