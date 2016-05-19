<div class="meta-box-sortables col-lg-12">
    <div id="fv_votes_workplace" class="postbox ">
        <div id="box-other-settings" class="handlediv" title="Click"><br></div>
        <h3 class="hndle"><span><?php echo __('Other settings', 'fv') ?></span></h3>
        <div class="inside">
            <div id="sv_wrapper" class="b-wrap">

                <div><strong><legend><?php _e('Contest list settings', 'fv') ?></legend></strong></div>

                <div class="pure-control-group">
                    <div class="pure-g">
                        <div class="pure-u-1">
                            <label><?php _e('Page, where contest are placed', 'fv') ?>
                            <select name="fv_page_id">
                                <option value="">
                                    <?php echo esc_attr( __( 'Select page' ) ); ?></option>
                                <?php
                                $pages = get_pages();
                                foreach ( $pages as $page ) {
                                    $option = '<option value="' . $page->ID . '"' . selected( $page->ID, $contest->page_id ) . '>';
                                    $option .= 'Page: ' . $page->post_title;
                                    $option .= '</option>';
                                    echo $option;
                                }
                                $posts = get_posts();
                                foreach ( $posts as $post ) {
                                    $option = '<option value="' . $post->ID . '"' . selected( $post->ID, $contest->page_id ) . '>';
                                    $option .= 'Post: ' . $post->post_title;
                                    $option .= '</option>';
                                    echo $option;
                                }

                                ?>
                            </select></label>
                            <br/><small><?php _e('(need, only if you uses contest_list shortcode)', 'fv') ?></small>
                        </div>
                        <div class="pure-u-1">
                            <label for="fv_cover_image"><?php echo __('Cover image ID for contest list', 'fv') ?> <?php fv_get_tooltip_code(__('Did`t shows in photos list, only as cover image.', 'fv')) ?></label>
                            <input type="number" id="fv_cover_image" name="fv_cover_image" value="<?php echo $contest->cover_image ?>" min="0" max="99999" size="5">
                            <input type="hidden" id="fv_cover_image_url">
                            <input type="button" class="button" value="Upload Image" onclick="fv_wp_media_upload('#fv_cover_image_url', '#fv_cover_image', '#cover-image-thumb')"/>
                            <img src="<?php echo ($contest->cover_image > 0) ? @reset (wp_get_attachment_image_src($contest->cover_image)) : ''; ?>" alt="" id="cover-image-thumb" height="28">
                        </div>
                    </div>
                </div>

                <?php do_action('fv/admin/contest_settings_form', $contest); ?>

            </div>
        </div>
    </div>
</div>