<?php
/**
 * Variables:
 *
 * $photos - array {
 * [0] = >
     * {
     *      'id' => 1,
     *      'sizes' => {
     *          'thumbnail' => {
     *              height: 150
     *              orientation: "landscape"
     *              url: "http://wp.vote/wp-content/uploads/2012/12/vpuh_261-150x150.jpg"
     *              width: 150
     *          },
     *          'full' => {***}
     *       },

     * }
 * }
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="fv_popup_label">
        <?php echo __('Adding photos:', 'fv') ?>
        <small><?php echo __('Please don\'t use " (double quotes) in fields', 'fv') ?></small>
    </h4>
</div>

<div class="modal-body">

    <div class="photos_list b-wrap"><?php foreach($photos as $Photo): ?>
        <div class="form-inline">
            <div class="form-group col-md-6">
                <label><?php echo __('Name', 'fv') ?></label>
            </div><!-- .misc-pub-section -->

            <div class="form-group col-md-7">
                <label><?php echo __('Short Descr', 'fv') ?></label> <small>max 255 symb.</small>
            </div><!-- .misc-pub-section -->

            <div class="form-group col-md-2">
                <label><?php echo __('Votes', 'fv') ?></label>
            </div>

             <div class="form-group photo col-md-8">
                <label><?php echo __('Photo', 'fv') ?> </label>
            </div><!-- .misc-pub-section -->
        </div>

        <div class="clearfix"></div>
        <?php include '_photos_list_from_item.php'; ?>
    <?php endforeach; ?></div>
    <div class="clearfix"></div>
</div>

<div class="modal-footer">
    <div class="buttons">
        <a class="button" onclick="fv_save_contestants(); return false;"><?php echo __('Save', 'fv') ?></a>
    </div>
</div>
