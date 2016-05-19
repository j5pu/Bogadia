<table class="form-table">

    <!-- ============ Not compiled BLOCK ============ -->
    <tr valign="top">
        <th scope="row"><?php _e('Remove all "new-line" codes in html?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you contest layout looks broken - try this option. Or can be used for little decrease page size.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[remove-newline]" <?php echo checked(FvFunctions::ss('remove-newline', false)); ?>/> <?php _e('Yes', 'fv') ?>
            <br/><small>Example, when you need enable it: <a target="_blank" href="https://yadi.sk/i/6Ycoqwugk7nDy">https://yadi.sk/i/6Ycoqwugk7nDy</a></small>
        </td>
    </tr>

    <!-- ============ Not compiled BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Debug Scripts & styles', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Load not minimized scripts and styles?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Can be used Developers for debug, not recommended for most users.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[not-compiled-assets]" <?php echo checked(FvFunctions::ss('not-compiled-assets', false)); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <!-- ============ Updating BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Photos', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('On deleting contest photo delete image from hosting?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you not need photos after contest, check this.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv-image-delete-from-hosting" <?php echo checked(get_option('fv-image-delete-from-hosting', false), 'on'); ?>/> <?php _e('Enable', 'fv') ?>
        </td>
    </tr>

    </tr>

    <!-- ============ Updating BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Updating key', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Key for automatically updating plugin', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Key, taken you at plugin purchase', 'fv') ?>">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <?php
            $defaults = array('key' => '', 'valid' => 0, 'expiration' => 'Key not entered!');
            $key_arr = get_option('fotov-update-key', $defaults);
            if (!$key_arr) {
                $key_arr = $defaults;
            }
            ?>
            <input name="fotov-update-key" class="all-options" value="<?php echo $key_arr['key']; ?>"/>
            <a href="<?php echo admin_url( 'admin.php?page=fv-settings&action=refresh_key#additional' ) ?>" class="button button-secondary button-large">Refresh data for saved key</a> <small>(Edit => Save => Refresh)</small>
            <br/>
            <small><?php
                echo ($key_arr['valid']) ?
                    __('Key expiration date: ', 'fv') . $key_arr['expiration'] : __('Error: ', 'fv') . __($key_arr['expiration'], 'fv');
            ?></small>
        </td>
    </tr>


    <!-- ============ FB API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Facebook api key', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row" style="vertical-align: middle;"><?php _e('Facebook api key', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If you want, that Facebook use true image<br/> on share, you need register as developer.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fotov-fb-apikey"  class="all-options" value="<?php echo get_option('fotov-fb-apikey', ''); ?>"/>
            <small><?php _e('How to get FB key - ', 'fv') ?> <a href="http://wp-vote.net/create-facebook-app-id/" target="_blank">http://wp-vote.net/create-facebook-app-id/</a></small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Load Facebook assets and init in header or footer?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Some social applications may break FB sharing,<br/> when loads higher as this plugin.', 'fv') ); ?>
        <td>
            <select name="fv-fb-assets-position">
                <option value="footer" <?php selected('footer', get_option('fv-fb-assets-position', 'footer') ); ?>>in footer</option>
                <option value="head" <?php selected('head', get_option('fv-fb-assets-position', 'footer') ); ?>>in head</option>
            </select>
            &nbsp; <small><?php _e('By default sets in Footer, but if you have problems with FB sharing after configure FV App Id, change to Head.', 'fv') ?></small>
        </td>
    </tr>

    <!-- ============ ReCaptcha API ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('ReCaptcha api keys', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('ReCaptcha api key', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If you want use reCAPTCHA.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fv[recaptcha-key]"  class="all-options" value="<?php echo ( isset($settings['recaptcha-key']) ) ? $settings['recaptcha-key'] : ''; ?>"/>
            <small><?php _e('How to get ReCaptcha key - ', 'fv') ?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank">https://www.google.com/recaptcha/admin#list</a>, https://developers.google.com/recaptcha/docs/start</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('ReCaptcha secret key', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Needs for verify response.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fv[recaptcha-secret-key]"  class="all-options" value="<?php echo ( isset($settings['recaptcha-secret-key']) ) ? $settings['recaptcha-secret-key'] : ''; ?>"/>
            <small><?php _e('How to get ReCaptcha secret key - ', 'fv') ?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank">https://www.google.com/recaptcha/admin#list</a>, https://developers.google.com/recaptcha/docs/start</small>
        </td>
    </tr>

    <!-- ============ EXPORT ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Export to CSV', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Export delimiter', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('String delimiter in CSV file', 'fv') ); ?>

        <td>
            <select name="fv-export-delimiter">
                <option value=";" <?php selected(';', get_option('fv-export-delimiter', ';') ); ?>>; (recommended for Excel)</option>
                <option value="\t" <?php selected('\t', get_option('fv-export-delimiter', ';') ); ?>>tab</option>
                <option value="," <?php selected(',', get_option('fv-export-delimiter', ';') ); ?>>,</option>
                <option value="," <?php selected(':', get_option('fv-export-delimiter', ';') ); ?>>:</option>
            </select>
            &nbsp; <small><?php _e('How is this - ', 'fv') ?> <a href="http://en.wikipedia.org/wiki/Delimiter-separated_values" target="_blank">Delimiter-separated values - WIKI</a></small>
        </td>
    </tr>


    <!-- ============ CAPABILITY  ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Capability', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Needed capability to manage contests/contests settings', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select capability, what user need<br/> to change photos in contest, translations, settings.<br/> Need if you want grant access to manager or other user.', 'fv') ); ?>

        <td>
            <select name="fv-needed-capability">
                <option value="edit_pages" <?php selected('edit_pages', get_option('fv-needed-capability', 'edit_pages') ); ?>>edit_pages (administrator+, editor+) = default</option>
                <option value="manage_options" <?php selected('manage_options', get_option('fv-needed-capability', 'edit_pages') ); ?>>manage_options (administrator+)</option>
                <option value="edit_posts" <?php selected('edit_posts', get_option('fv-needed-capability', 'edit_pages') ); ?>>edit_posts (administrator+, editor+, author+)</option>
                <option value="install_plugins" <?php selected('install_plugins', get_option('fv-needed-capability', 'edit_pages') ); ?>>install_plugins (administrator+, editor+, contributor+)</option>
                <option value="moderate_comments" <?php selected('moderate_comments', get_option('fv-needed-capability', 'edit_pages') ); ?>>moderate_comments (administrator+, editor+)</option>
            </select>
            <br/><small><?php _e('More about roles and capabilities - ', 'fv') ?> <a href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank"><?php _e('Capability vs. Role Table', 'fv') ?></a></small>
        </td>
    </tr>



    <!-- ============ Addons support ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Addons support', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Disable addons support?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Can be used for little decrease server loading. But all addons will stop works (like circled countdown).', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[disable-addons-support]" <?php echo checked(FvFunctions::ss('disable-addons-support', false)); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top" style="display: none;">
        <th scope="row"><?php _e('Enable SQL debug?', 'fv') ?>:</th>
        <?php echo fv_get_td_tooltip_code( __('Will save all plugin SQL queries.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[debug-sql]" <?php checked( FvFunctions::ss('debug-sql', false) ); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

</table>