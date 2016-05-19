<div class="meta-box-sortables col-lg-12">
    <div id="fv_votes_workplace" class="postbox ">
        <div id="box-upload-settings" class="handlediv" title="Click"><br></div>
        <h3 class="hndle"><span><?php echo __('Upload settings', 'fv') ?></span></h3>
        <div class="inside">
            <div id="sv_wrapper" class="b-wrap">

                <!-- ============= Upload ============= -->
                <div class="form-group">
                    <label><i class="fvicon fvicon-download"></i>
                        <?php echo __('Photo upload enabled', 'fv'); ?>
                        <input type="checkbox" id="fv_upload_enable" name="fv_upload_enable" <?php echo ($action == 'add' || !$contest->upload_enable) ? '' : 'checked' ?>>
                    </label>
                    <small><?php _e('(first plugin check this option, after checks upload dates (this option and upload dates not affects to `Upload shortcode`, it works always)', 'fv'); ?></small>
                </div>

                <div class="form-group">
                        <label for="max_uploads_per_user">
                            <?php echo __('The maximum number of one user upload photos', 'fv') ?> <?php fv_get_tooltip_code(__('Maximum number of pictures uploaded by each user in this competition.', 'fv')) ?>
                        </label>
                        <input type="number" id="max_uploads_per_user" name="max_uploads_per_user" value="<?php echo ($action == 'add') ? '5' : $contest->max_uploads_per_user ?>" style="width: 60px;">
                </div>
                <div class="form-group">
                    <label for="moderation_type"><i class="fvicon fvicon-signup"></i>
                        <?php echo __('Moderation type', 'fv') ?> <?php fv_get_tooltip_code(__('Moderate photos before publishing or after', 'fv')) ?>
                    </label>
                    <select id="moderation_type" name="moderation_type">
                        <option value="pre" <?php ( isset($contest->id) )? selected('pre', $contest->moderation_type) : ''?>> <?php _e('Pre-moderation', 'fv') ?></option>
                        <option value="after" <?php ( isset($contest->id) )? selected('after', $contest->moderation_type) : ''?>> <?php _e('After moderation', 'fv') ?></option>
                    </select>
                    <small><?php _e('also you will need setup by what params limit uploads [ip, email, user id] in Settings, because this will not works', 'fv'); ?></small>
                </div>

                <div class="row">
                        <div class="form-group col-sm-12">
                            <span id="timestamp">
                                <label><?php echo __('Upload date start', 'fv'); ?></label>
                                <input type="text" class="datetime form-control" id="upload_date_start" name="upload_date_start" value="<?php echo ($action == 'add') ? date("Y-m-d H:i:s", current_time('timestamp') - 7200) : $contest->upload_date_start ?>">
                                <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>
                            </span>
                        </div>

                        <div class="form-group col-sm-12">
                            <span id="timestamp">
                                <label><?php echo __('Upload date finish', 'fv'); ?></label>
                                <?php fv_get_tooltip_code(__('When time end, upload from will be hidden', 'fv')) ?>
                                <input type="text" class="datetime form-control" id="upload_date_finish" name="upload_date_finish" value="<?php echo ($action == 'add') ? date("Y-m-d H:i:s", current_time('timestamp') + 1209600) : $contest->upload_date_finish ?>">
                                <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>
                            </span>
                        </div>
                </div>

                <div class="form-group">
                    <label for="upload_theme"><i class="fvicon fvicon-box-add"></i>
                        <?php echo __('Upload form theme', 'fv'); ?>
                    </label>
                    <select id="upload_theme" name="upload_theme">
                        <option value="default" <?php ( isset($contest->id) )? selected('default', $contest->upload_theme) : ''?>>default</option>
                        <?php do_action('fv/admin/contest_settings/upload_theme', $contest); ?>
                    </select>
                    <small>(Can be overridden in upload shortcode via attribute 'upload_theme="**"')</small>
                </div>
                <div class="clearfix"></div>

            </div>
        </div>
    </div>
</div>