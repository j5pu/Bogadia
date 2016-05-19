<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="fv_popup_label">
        <?php echo ($unit->id)? __('Editing contestant:', 'fv') : __('Adding contestant:', 'fv')  ?>
        <small><?php echo __('Please don\'t use " (double quotes) in fields', 'fv') ?></small>
    </h4>
</div>

<div class="modal-body">
    <div class="sv_unit status<?php echo $unit->status ?>" >
        <form>
            <div class="row">
                <div class="form-group col-sm-18">
                        <label><?php echo __('Name', 'fv') ?></label> <small>max length - 255 chars, no html allowed</small>
                        <input class="form-control" name="form[name]" type="text" value="<?php echo stripslashes($unit->name) ?>" />
                </div>

                <div class="form-group col-sm-6">
                        <label><?php echo __('Number of votes', 'fv') ?></label>
                        <input class="form-control w07" name="form[votes]" type="number" size="4" width="40" value="<?php echo $unit->votes_count ?>" />
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="form-group">
                <label> <?php echo __('Short description', 'fv') ?> </label> <small>max length - 500 chars,
                    html allowed like
                    <a href="https://core.trac.wordpress.org/browser/trunk/src/wp-includes/kses.php#L60" target="_blank">in post</a>
                    <?php echo __('(shows in photos list)', 'fv') ?></small>
                <input name="form[description]" class="form-control" type="text" value="<?php echo esc_attr( stripslashes($unit->description) ) ?>" onkeyup="fv_count_chars(this);"/>
                <span class="need-count-chars"><?php echo mb_strlen(stripslashes($unit->description)); ?></span>
            </div>
            <div class="form-group">
                <label> <?php echo __('Full description', 'fv') ?> </label> <small>max length - 1255 chars,
                    html allowed like
                    <a href="https://core.trac.wordpress.org/browser/trunk/src/wp-includes/kses.php#L60" target="_blank">in post</a>
                    <?php echo __('(shows in single contest photo page)', 'fv') ?></small>
                <textarea name="form[full_description]" class="form-control" rows="3" onkeyup="fv_count_chars(this);"><?php echo  esc_attr( stripslashes($unit->full_description) ) ?></textarea>
                <span class="need-count-chars"><?php echo mb_strlen(stripslashes($unit->full_description)); ?></span>
            </div>

            <div class="form-group">
                <label> <?php echo __('Social description', 'fv') ?> </label> <small>max length - 150 chars, no html allowed
                    <?php echo __('(uses on sharing image into Social networks)', 'fv') ?></small>
                <input name="form[social_description]" class="form-control" type="text" value="<?php echo stripslashes($unit->social_description) ?>" />
            </div>

            <!--<div class="form-group">
                    <label><?php echo __('Additional filed', 'fv') ?></label> <small>max length - 500 chars </small>
                    <input name="form[additional]" class="form-control" type="text" value="<?php //echo $unit->additional ?>" />
            </div>-->

            <div class="form-group">
                    <strong>Upload info:</strong><br/>
                    User email: <?php echo ( !empty($unit->user_email) ) ? $unit->user_email : ''; ?> / User id: <?php echo ( !empty($unit->user_id) ) ? $unit->user_id : ''; ?>
                    <br/>Upload form data: <?php echo FvFunctions::showUploadInfo($unit->upload_info); ?>
            </div>

            <div style="clear:both;"> </div>

            <div class="form-group">
                <label><?php echo __('Contest image', 'fv') ?></label>
                <small><?php echo __('(Use full image, thumbnail get`s automatically)', 'fv') ?></small>
                <div class="row">
                    <div class="col-sm-17">
                        <div class="input-group">
                            <div class="input-group-addon">full</div>
                            <input type="text" class="form-control" name="form[image]" id="image-src" value="<?php echo $unit->url ?>" placeholder="image url">
                        </div>
                        <input type="hidden" name="form[image_id]" id="image-id" value="<?php echo $unit->image_id ?>">
                    </div>
                    <div class="col-sm-2">
                        <img src="<?php echo @reset (FvFunctions::getPhotoThumbnailArr($unit) ); ?>" alt="" id="main-image-thumb" height="28">
                    </div>
                    <div class="col-sm-5">
                        <button type="button" class="btn" onclick="fv_wp_media_upload('input#image-src', 'input#image-id', 'img#main-image-thumb');">Select</button>
                    </div>
                </div>
            </div>

            <?php do_action('fv/admin/form_edit_photo/extra', $unit); ?>

            <input name="form[id]" type="hidden" value="<?php echo $unit->id ?>" />
            <?php wp_nonce_field('save_contestant', 'fv_nonce'); ?>
            <input class="status" name="form[status]" type="hidden" value="<?php echo $unit->status ?>" />
            <?php echo __('Status', 'fv') ?>  <span class="foto_status"> <?php echo fv_get_status_name($unit->status) ?> </span> |
            <a href="#" onclick=" changeStatus(this, '0'); return false; " ><?php echo __('Publish', 'fv') ?></a> &nbsp;
            <a href="#" onclick=" changeStatus(this, '1'); return false; " class="moderaion" ><?php echo __('To moderation', 'fv') ?></a> &nbsp;
            <a href="#" onclick=" changeStatus(this, '2'); return false; " class="draft" ><?php echo __('To draft', 'fv') ?></a> &nbsp;&nbsp;
        </form>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel and close</button>
    <button type="button" class="btn btn-primary" onclick="fv_save_contestant(this, <?php echo $unit->contest_id ?>); return false;">
        <?php echo __('Save', 'fv') ?>
    </button>
</div>
