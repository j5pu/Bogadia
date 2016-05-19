<table class="form-table">

    <!-- ============ Upload BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Uploading', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Only logged in users can upload photos:', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Allow upload photos only authorized users', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-autorize" <?php echo ( get_option('fotov-upload-autorize', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Show default login form, if user not logged in:', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e('You may need add some styles for it looks better', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fv[upload-show-login-form]" <?php checked( FvFunctions::ss('upload-show-login-form') ); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Limit photo upload by email', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('By one email user can`t upload more<br/> then set in contest options <br/>(The maximum number of one user upload photos)', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-limit-email" <?php echo ( get_option('fotov-upload-limit-email', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?> <?php _e('(need check "show in upload form email field")', 'fv') ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Limit photo upload by ip', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('By one ip user can`t upload more<br/> then set in contest options <br/>(The maximum number of one user upload photos)', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-limit-ip" <?php echo ( get_option('fotov-upload-limit-ip', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Limit photo upload by cookie', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('In one browser user can`t upload more<br/> then set in contest options <br/>(The maximum number of one user upload photos)', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-limit-cookie" <?php echo ( get_option('fotov-upload-limit-cookie', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?> <?php _e('(Not works in ie)', 'fv') ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Limit photo upload by user id', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('In one browser user can`t upload more<br/> then set in contest options <br/>(The maximum number of one user upload photos)', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-limit-userid" <?php echo ( get_option('fotov-upload-limit-userid', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?> <?php _e('(works, if upload enabled only for autorized users AND user are autorized)', 'fv') ?>
        </td>

    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Limit uploaded photo size', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If your did`t have a lot of hosting disc space,<br/> you may limit uploading photo size.<br/> If image will be bigger,<br/> he receive error message like `Photo size more than 2 megabytes!`.','fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="number" name="fotov-upload-photo-limit-size" value="<?php echo get_option('fotov-upload-photo-limit-size', 0); ?>" min="0" max="15000" step="10"/> Kb. <br/>
            <small><?php _e("Enter a valid max size in kilobytes, 1024 Kb = 1 Mb.", "fv") ?></small>
        </td>
    </tr>
    <!-- ============ Resize BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td><h3><?php _e('Resize images', 'fv') ?></h3></td>
        <td colspan="2">
            <em><?php _e('Automatically resize (i.e. scale down) images after uploading. Save disk space and preserve your layout. Thanks to Daniel Mores (http://mores.cc), A. Huizinga, Jacob Wyke', 'fv') ?></em>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Resize images at upload', 'fv') ?>?</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('You can set the max width, and images
                        <br/>(JPEG, PNG or GIF) will be resized while they are uploaded.
                        <br/>There will not be a copy or backup with the original size.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-photo-resize" <?php echo ( get_option('fotov-upload-photo-resize', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Max width (about, may vary within this range)', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Recommended use only one param - maxwidth or maxheight
                        <br/>, then image not be disproportionate', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="number" name="fotov-upload-photo-maxwidth" size="4" maxlength="4" value="<?php echo get_option('fotov-upload-photo-maxwidth', 0); ?>" min="0" max="3500" step="10"/> px. <br/>
            <small>Enter a valid max width in pixels (e.g. 500). Enter 0 if you only wish to resize to a max height only.</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Max height (about, may vary within this range)', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Recommended use only one param - maxwidth or maxheight
                        <br/>, then image not be disproportionate', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="number" name="fotov-upload-photo-maxheight" value="<?php echo get_option('fotov-upload-photo-maxheight', 0); ?>" min="0" max="3500" step="10"/> px. <br/>
            <small>Enter a valid max height in pixels (e.g. 500). Enter 0 if you only wish to resize to a max width only.</small>
        </td>
    </tr>

</table>