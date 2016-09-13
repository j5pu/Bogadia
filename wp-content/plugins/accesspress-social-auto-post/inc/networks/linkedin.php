<div class="asap-network-wrap">
    <h4 class="asap-network-title"><?php _e('LinkedIn Account Details', ASAP_TD); ?></h4>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asap_form_action"/>
        <input type="hidden" name="network_name" value="linkedin"/>
        <?php wp_nonce_field('asap_form_action', 'asap_form_nonce') ?>
        <div class="asap-network-inner-wrap">
            <div class="asap-network-field-wrap">
                <label><?php _e('Auto Publish', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="checkbox" value="1" name="auto_publish"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Account Title', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_title"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Client ID', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_details[client_id]"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Client Secret', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[client_secret]"/>
                    <div class="asap-field-note">
                         <?php
                        $callback_url = admin_url('admin-post.php?action=asap_linkedin_callback_authorize&account_id=' . $account_id);
                        _e("Please visit <a href='https://www.linkedin.com/developer/apps' target='_blank'>here</a> and create new LinkedIn Application to get Client ID and Client Secret.<br/><br/> Please keep below url in the  <strong>Authorized Redirect URL</strong>,<strong>Default \"Accept\" Redirect URL</strong> fields while creating the app. <br/><br/>  $callback_url", ASAP_TD);
                        ?>
                    </div>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Post Message Format', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <textarea name="account_details[message_format]"></textarea>
                    <div class="asap-field-note">
                        <?php _e('Note: Please use #post_title,#post_content,#post_excerpt,#post_link,#author_name for the corresponding post title, post content, post excerpt, post link, post author name respectively.', ASAP_TD); ?>
                    </div>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Post Format', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <select name="account_details[post_format]">
                        <option value="simple"><?php _e('Simple Text Message', ASAP_TD); ?></option>
                        <option value="link"><?php _e('Attach Blog Post', ASAP_TD); ?></option>
                    </select>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Include Image', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <label class="asap-full-width"><input type="checkbox" name="account_details[include_image]" value="1"/><?php _e('Check if you want to include the image in the post', ASAP_TD); ?></label>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Post Image', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <select name="account_details[post_image]">
                        <option value="featured_image"><?php _e('Featured Image', ASAP_TD); ?></option>
                        <option value="custom_image"><?php _e('Custom Image', ASAP_TD); ?></option>
                    </select>

                </div>
            </div>
            <div class="asap-network-field-wrap asap-custom-image">
                <label><?php _e('Custom Image URL', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[custom_image_url]" placeholder="<?php _e('Enter URL of the image here', ASAP_TD); ?>"/>
                </div>
            </div>

        </div>
        <!--Post Settings Section-->
        <?php include('post-settings.php'); ?>
        <!--Post Settings Section-->

    </form>
</div>