<?php
$account_extra_details = unserialize($account_row['account_extra_details']);
$authorize_status = $account_extra_details['authorize_status'];
?>
<div class="asap-network-wrap">
    <h4 class="asap-network-title"><?php _e('Tumblr Account Details', ASAP_TD); ?></h4>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asap_tumblr_authorize_action"/>
        <input type="hidden" name="account_id" value="<?php echo $account_id ?>"/>
        <?php wp_nonce_field('asap_tumblr_authorize_action', 'asap_tumblr_authorize_nonce'); ?>
        <input type="submit" name="asap_tumblr_authorize" value="<?php echo ($authorize_status == 0) ? __('Authorize', ASAP_TD) : __('Reauthorize', ASAP_TD); ?>" style="display: none;"/>
    </form>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asap_form_action"/>
        <input type="hidden" name="network_name" value="tumblr"/>
        <input type="hidden" name="account_id" value="<?php echo $account_id; ?>"/>
        <?php wp_nonce_field('asap_form_action', 'asap_form_nonce') ?>
        <div class="asap-network-inner-wrap">
            <?php
            if ($authorize_status == 0) {
                ?>
                <p class="asap-authorize-note"><?php _e('It seems that you haven\'t authorized your account yet.The auto publish for this account won\'t work until you will authorize.Please authorize using below button', ASAP_TD); ?></p>
                <?php
            }
            ?>
            <input type="button" class="asap-authorize-btn" id="asap-tumblr-authorize-ref" value="<?php echo ($authorize_status == 0) ? __('Authorize', ASAP_TD) : __('Reauthorize', ASAP_TD); ?>"/>
            <div class="asap-network-field-wrap">
                <label><?php _e('Auto Publish', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="checkbox" value="1" name="auto_publish" <?php checked($account_row['account_status'], true); ?>/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Account Title', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_title" value="<?php echo $account_row['account_title']; ?>"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Tumblr API Consumer Key', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_details[consumer_key]" value="<?php echo $account_details['consumer_key']; ?>"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('OAuth Consumer Secret', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[consumer_secret]"  value="<?php echo $account_details['consumer_secret']; ?>"/>
                    <div class="asap-field-note">
                        <?php
                        $site_url = site_url();
                        $registration_url = $this->get_tumblr_reg_url();
                        _e("Please visit <a href='$registration_url' target='_blank'>here</a> and create new Tumblr Application to get Tumblr API Consumer Key and Consumer Secret.", ASAP_TD);
                        ?>
                    </div>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Tumblr Username', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[username]" value="<?php echo $account_details['username']; ?>"/>
                    <div class="asap-field-note">
                        <?php _e('Note:Please enter your tumblr username.For example devteam2070', ASAP_TD); ?>
                    </div>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Post Message Format', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <textarea name="account_details[message_format]"><?php echo stripslashes($account_details['message_format']); ?></textarea>
                    <div class="asap-field-note">
                        <?php _e('Note: Please use #post_title,#post_content,#post_excerpt,#post_link,#author_name for the corresponding post title, post content, post excerpt, post link, post author name respectively.You can use basic <strong>html tags</strong> such as &lt;a>, &lt;p> etc too.', ASAP_TD); ?>
                    </div>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Tags', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[tags]" value="<?php echo $account_details['tags']; ?>"/>
                    <div class="asap-field-note">
                        <?php _e('Note: Please enter the tags that you want to inlcude in each post by separating with comma.For example tag1, tag2, tag3.Leave blank if you don\'t want to use any.', ASAP_TD); ?>
                    </div>
                </div>
            </div>


        </div>
        <!--Post Settings Section -->
        <?php include('post-settings.php'); ?>
        <!--Post Settings Section -->


    </form>
</div>