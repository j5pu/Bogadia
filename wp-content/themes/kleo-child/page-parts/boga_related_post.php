<section class="container-wrap">
    <div class="container">
        <div class="related-wrap">
            <div class="hr-title hr-long"><abbr>ARTÍCULOS RELACIONADOS</abbr></div>
                <ul style="list-style-type: none;">
                    <?php
                    $related_posts = boga_get_related_post(9);
                    foreach( $related_posts as $likes_post ) {
                        $post_id = $likes_post->ID;
                        $link = get_permalink($post_id);
                        $title = get_the_title($post_id);
                        if ( mb_strlen( $title, 'utf8' ) > 60 ) {
                            $last_space = strrpos( substr( $title, 0, 60 ), ' ' ); // find the last space within 35 characters
                            $title = substr( $title, 0, $last_space ) . ' ...';
                        }
                    /*    echo '<a class="sidebar-img" href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $img, 'thumbnail' ).'</a>'.'<h5 class="title-post-sidebar"><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h5>';
                        echo '</div>';
                        wp_reset_query();*/
                        ?>
                        <li id="post-<?php echo $post_id; ?>" <?php post_class(array("post-item col-sm-4")); ?>>
                            <article>
                                <?php
                                    echo '<div class="post-image">';
                                    $img_url = get_the_post_thumbnail( $post_id, 'thumbnail' );
                                    //$image = aq_resize( $img_url, 197);
                                    //$image = aq_resize( $img_url, $kleo_config['post_gallery_img_width'], $kleo_config['post_gallery_img_height'], true, true, true );
                                    echo '<a title="'. $title .'" href="'. $link .'" class="element-wrap">'
                                        . '<img src="'. $img_url .'" alt="'. $title .'">'
                                        . '</a>';
                                    echo '</div><!--end post-image-->';
                                ?>

                                <div class="entry-content">
                                    <h4 class="post-title entry-title"><a title="<?php echo $title; ?>" href="<?php echo $link; ?>"><?php echo $title; ?></a></h4>
                                </div>

                            </article>
                        </li>
                    <?php
                    }
                    ?>
                </ul>

</section>
