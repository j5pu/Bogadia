<table class="form-table">

    <!-- ============ Users BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Admin', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Notify me, when new photo uploaded', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Notify to email, when users upload photo', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-upload-notify" <?php echo ( get_option('fotov-upload-notify', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Email to notify me', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Email', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fotov-upload-notify-email" value="<?php echo get_option('fotov-upload-notify-email', ''); ?>"/> <?php _e('If not set, uses email from site options.', 'fv') ?>
        </td>
    </tr>

    <!-- ============ Users BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Users', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Notify users, when photo approved or deleted', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Send notification to user, when it photo approved or deleted', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-users-notify" <?php echo ( get_option('fotov-users-notify', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?> &nbsp;(<?php _e('on plublishing or deleting photo', 'fv') ?>)
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Notify users, when photo uploaded', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Send notification to user, when it photo uploaded', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-users-notify-upload" <?php echo ( get_option('fotov-users-notify-upload', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?> &nbsp;
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Notify users from email', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If not set, wordpress set`s from email<br/> like wordpress@domain.com', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fotov-users-notify-from-mail" value="<?php echo get_option('fotov-users-notify-from-mail', ''); ?>"/>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Notify users from name', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If not set using `Wordpress`', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fotov-users-notify-from-name" value="<?php echo get_option('fotov-users-notify-from-name', ''); ?>"/>
        </td>
    </tr>

    <!-- ============ Users BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Log emails', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Log all emails', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Log emails for debug', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fv[log-emails]" <?php echo ( isset($settings['log-emails']) && $settings['log-emails'] ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>


</table>