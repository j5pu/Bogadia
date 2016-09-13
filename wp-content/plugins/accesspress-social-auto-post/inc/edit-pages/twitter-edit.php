<div class="asap-network-wrap">
    <h4 class="asap-network-title"><?php _e('Twitter Account Details', ASAP_TD); ?></h4>
    <?php if (isset($_SESSION['asap_message'])) { ?><p class="asap-authorize_note"><?php
        echo $_SESSION['asap_message'];
        unset($_SESSION['asap_message']);
        ?></p><?php } ?>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="asap_form_action"/>
        <input type="hidden" name="network_name" value="twitter"/>
        <input type="hidden" name="account_id" value="<?php echo $account_id; ?>"/>
        <?php wp_nonce_field('asap_form_action', 'asap_form_nonce') ?>
        <div class="asap-network-inner-wrap">
            <div class="asap-network-field-wrap">
                <label><?php _e('Auto Publish', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="checkbox" value="1" name="auto_publish" <?php checked($account_row['account_status'], true); ?>/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Account Title', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_title" value="<?php echo $account_row['account_title']; ?>"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('API Key', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_details[api_key]" value="<?php echo $account_details['api_key']; ?>"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('API Secret', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_details[api_secret]" value="<?php echo $account_details['api_secret']; ?>"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Access Token', ASAP_TD); ?></label>
                <div class="asap-network-field"><input type="text" name="account_details[access_token]" value="<?php echo $account_details['access_token']; ?>"/></div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Access Token Secret', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[access_token_secret]" value="<?php echo $account_details['access_token_secret']; ?>"/>
                    <div class="asap-field-note">
                        <?php
                        $site_url = site_url();
                        _e("Please visit <a href='https://apps.twitter.com/' target='_blank'>here</a> and create new app to get API Key, API Secret, Access Token and Access Token Secret keys.<br/><br/> Also please make sure you keep $site_url in the website field while creating the app.", ASAP_TD);
                        ?>
                    </div>
                </div>
            </div>
            <div class="asap-network-field-wrap">
                <label><?php _e('Post Message Format', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <textarea name="account_details[message_format]"><?php echo $account_details['message_format']; ?></textarea>
                    <div class="asap-field-note">
                        <?php _e('Please use #post_title,#post_content,#post_excerpt,#post_link,#author_name for the corresponding post title, post content, post excerpt, post link, post author name respectively.', ASAP_TD); ?>
                    </div>
                </div>
            </div>
            <?php /* <div class="asap-network-field-wrap">
              <label><?php _e('Include Image', ASAP_TD); ?></label>
              <div class="asap-network-field">
              <label class="asap-full-width"><input type="checkbox" name="account_details[include_image]" value="1" <?php checked($account_details['include_image'], true); ?>/><?php _e('Check if you want to include the image in the post', ASAP_TD); ?></label>
              </div>
              </div>
             */ ?>

            <div class="asap-network-field-wrap">
                <label><?php _e('Use Short URLS', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="checkbox" name="account_details[short_url]" value="1" <?php echo (isset($account_details['short_url'])) ? 'checked="checked"' : ''; ?>/>
                </div>
            </div>
            <div class="asap-network-field-wrap asap-bitly-ref">
                <label><?php _e('Bitly Username', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[bitly_username]" value="<?php echo $account_details['bitly_username']; ?>"/>
                </div>
            </div>
            <div class="asap-network-field-wrap asap-bitly-ref">
                <label><?php _e('Bitly API Key', ASAP_TD); ?></label>
                <div class="asap-network-field">
                    <input type="text" name="account_details[bitly_api_key]" value="<?php echo $account_details['bitly_api_key']; ?>"/>
                    <div class="asap-field-note">
                        <?php _e("Please visit <a href='https://bitly.com/a/your_api_key' target='_blank'>here</a> to get your bitly username and api key", ASAP_TD); ?>
                    </div>
                </div>
            </div>


        </div>
        <!--Post Settings Section -->
        <?php include('post-settings.php');?>
        <!--Post Settings Section -->
    </form>
</div>

