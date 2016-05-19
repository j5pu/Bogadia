<form class="form-inline photos_list_form">
    <div class="form-group col-md-6">
        <input type="text" name="form[name]" value=""  class="form-control">
    </div><!-- .form-group -->

    <div class="form-group col-md-7">
        <input type="text" name="form[description]" value=""  class="form-control">
    </div><!-- .form-group -->

    <div class="form-group col-md-2">
        <input type="text" name="form[votes]" value="" class="form-control"/>
    </div><!-- .form-group -->

    <div class="form-group photo col-md-7">
        <input type="text" name="form[image]" class="form-control" value="<?php echo $Photo['sizes']['full']['url'] ?>">
        <input type="hidden" name="form[image_id]" value='<?php echo $Photo['id'] ?>' />
    </div><!-- .form-group -->

    <div class="form-group col-md-2">
        <a href="<?php echo $Photo['sizes']['full']['url'] ?>" target="_blank">
            <img src="<?php echo $Photo['sizes']['thumbnail']['url'] ?>" height="27" style="margin-top: 2px; margin-left: 2px; display: inline-block;" />
        </a>
    </div><!-- .form-group -->

    <input type="hidden" name="form[social_description]" value='' />
    <input type="hidden" name="form[full_description]" value='' />
    <input type="hidden" name="form[status]" value='<?php echo ST_PUBLISHED; ?>' />
    <input type="hidden" name="form[id]" value="0" />
    <?php wp_nonce_field('save_contestant', 'fv_nonce'); ?>
</form>