<table class="form-table">

    <!-- ============ Design BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Design', 'fv') ?></h3></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Contest block design', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select how your contest block will looks.', 'fv') ); ?>
        <td>
            <select name="fv[theme]" class="form-control">
                <?php foreach (fv_get_themes_arr() as $key => $theme_title): ?>
                    <option value="<?php echo $key ?>" <?php selected( FvFunctions::ss('theme', 'pinterest'), $key ); ?>><?php echo $theme_title ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Contest block width (min. 180 px.)', 'fv') ?> </th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Change to fit the width of the voting blocks your site', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="number" name="fotov-block-width" value="<?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH); ?>" min="0" max="1000"/> px.
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Thumbnail retrieving type:', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Function, that used for retrieving thumbnail image.', 'fv') ); ?>
        <td>
            <select name="fv[thumb-retrieving]" class="form-control">
                <option value="plugin_default" <?php selected( FvFunctions::ss('thumb-retrieving', 'plugin_default'), 'plugin_default' ); ?>>Plugin default (1 sql query per image)</option>
                <option value="wordpress_default" <?php selected( FvFunctions::ss('thumb-retrieving', 'plugin_default'), 'wordpress_default' ); ?>>Wordpress default (2 sql queries per image)</option>
            </select>
            <br/><small>If you have some problems with "Plugin default" try "Wordpress" </small>
            <br/><small><strong>Note:</strong> If you installed and activated
                <a href="https://jetpack.me/support/photon/" target="_blank">Jetpack + Photon module</a>,
                than it will be used by default.</small>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Thumbnail image size <small>(changes on the fly; each changing creates new file for each contestant, because of this not change this often)</small>', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Thumbnails size in photos list, better not much more than `Contest block size`', 'fv') ); ?>
        <td> width: <input type="number" name="fotov-image-width" value="<?php echo get_option('fotov-image-width', 220); ?>" min="0" max="1200"/> px. /
            height: <input type="number" name="fotov-image-height" value="<?php echo get_option('fotov-image-height', 999); ?>" min="0" max="1200"/> px. /
            hard crop: <input type="checkbox" name="fotov-image-hardcrop" <?php echo checked( get_option('fotov-image-hardcrop', false), 'on' ); ?>/>
            <br/>
            <small><?php _e('In hard crop means that thumbnail size will be 100% equal (if checked) or proportional (if unchecked) on the larger side.', 'fv'); ?><br/>
            <?php _e('If you want fit just to one side, then set up one size to 999, as example 280*999 will not crop image by height (best way for Pinterest theme).', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('List contest block width (min. 180 px.) <small>Can be overridden in `fv_contests_list` shortcode</small>', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Change to fit the width of the contest list blocks your site', 'fv') ); ?>
        <td>
            <input type="number" name="fv[list-block-width]" value="<?php echo FvFunctions::ss('list-block-width', FV_CONTEST_BLOCK_WIDTH); ?>" min="0" max="1000"/> px.
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Contest list thumbnail image size <small>(changes on the fly)</small>', 'fv') ?> </th>
        <td class="tooltip">
            <div class="box" title="<?php _e('Thumbnails size in photos list, better not much more than `Contest block size`', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td> width:<input type="number" name="fv[list-thumb-width]" value="<?php echo FvFunctions::ss('list-thumb-width', 200); ?>" min="0" max="1200"/> px. /
            height:<input type="number" name="fv[list-thumb-height]" value="<?php echo FvFunctions::ss('list-thumb-height', 200); ?>" min="0" max="1200"/> px.
            / hard crop: <input type="checkbox" name="fv[list-thumb-crop]" <?php echo checked( FvFunctions::ss('list-thumb-crop', true) ); ?>/>
            <small><?php _e('In hard crop means that thumbnail size will be 100% equal (if checked) or proportional (if unchecked) on the larger side.', 'fv'); ?></small>
        </td>
    </tr>
    <tr valign="top" class="no-padding">
        <td colspan="3">
            <hr/>
            <input type="submit" class="button-primary" value="<?php _e('Save all Changes', 'fv') ?>" /> &nbsp;<small>You can save here, if not wan't scroll to bottom.</small><br>
            <hr/>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable lazy load images?', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If you enable this, on page load lads just 3 images, <br/>other just user scroll page', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fv[lazy-load]" <?php echo ( FvFunctions::ss('lazy-load') ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp;<small>(Not works in Fashion theme)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Hide votes count?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you don`t want show to users votes count, check it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[hide-votes]" <?php echo ( FvFunctions::ss('hide-votes') ) ? 'checked' : ''; ?>/> <?php _e('Hide', 'fv') ?>
            <small>(Don`t remember also remove votes count from "Lightbox title format")</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Enable cache support?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If you using cache plugins, after refresh page votes will not changes.<br/> For fix this plugin will AJAX update votes on page after it loaded.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[cache-support]" <?php echo ( FvFunctions::ss('cache-support') ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
            &nbsp; <small>(Does not works, if in wp-config.php not defined "WP_CACHE")</small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top" class="important">
        <th scope="row"><?php _e('Show toolbar (under contest)?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('If want allow user change photos order and it looks good on your design, then enable it.', 'fv') ); ?>
        <td>
            <input type="checkbox" name="fv[show-toolbar]" <?php checked( FvFunctions::ss('show-toolbar', false) ); ?>/> <?php _e('Yes', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar background color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Toolbar background color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-bg-color]" class="color" value="<?php echo FvFunctions::ss('toolbar-bg-color', '#232323', 7); ?>"/>
            Select color <button type="button" onclick="fv_reset_color(this, '#232323');">reset</button>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar text / links color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Text and link color placed on Toolbar', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-text-color]" class="color" value="<?php echo FvFunctions::ss('toolbar-text-color', '#FFFFFF', 7); ?>"/>
            Select color <button type="button" onclick="fv_reset_color(this, '#FFFFFF');">reset</button>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar active links background', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Link active background color placed on Toolbar', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-link-abg-color]" class="color" value="<?php echo FvFunctions::ss('toolbar-link-abg-color', '#454545', 7); ?>"/>
            Select color <button type="button" onclick="fv_reset_color(this, '#454545');">reset</button>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Toolbar select color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Input and select fields placed on Toolbar background color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[toolbar-select-color]" class="color" value="<?php echo FvFunctions::ss('toolbar-select-color', '#1f7f5c', 7); ?>"/>
            Select color <button type="button" onclick="fv_reset_color(this, '#1f7f5c');">reset</button>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Open contest image in new page?', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e("If did't want use lightbox or want add more description<br/> about photo - use this option.", 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-photo-in-new-page" <?php echo ( get_option('fotov-photo-in-new-page', false) ) ? 'checked' : ''; ?>/> <?php _e('Yes', 'fv') ?>
            <small><?php _e('(Now works only in `new_year`, `flickr` and `default` theme) and not compatible with "Ajax" and "Infinity" pagination', 'fv') ?></small>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Pagination', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Break photos into pages', 'fv') ); ?>
        <td>
            <input type="number" name="fv[pagination]" value="<?php echo FvFunctions::ss('pagination', 0); ?>" min="0" max="200"/>
            <?php _e('Per page', 'fv') ?>
            &nbsp;<small>(if number < 8, pagination will be disabled)</small>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php _e('Pagination type', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Pagination type - simply or ajax', 'fv') ); ?>
        <td>
            <select name="fv[pagination-type]">
                <option value="default" <?php selected( FvFunctions::ss('pagination-type', 'default'), 'default' ); ?>>default</option>
                <option value="ajax" <?php selected( FvFunctions::ss('pagination-type', 'default'), 'ajax' ); ?>>ajax</option>
                <option value="infinite" <?php selected( FvFunctions::ss('pagination-type', 'default'), 'infinite' ); ?>>Infinite scroll</option>
            </select>
            &nbsp;<small>(ajax works faster without refresh page, but may not always works)</small>
        </td>
    </tr>

    <!--<tr valign="top">
        <th scope="row"><?php _e('Scroll to contest after go to next/prev page?', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If you enable this, after clicking into page number, <br/>and reload page it will scrolled to photos', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fv[pagination-scroll-to-contest]" <?php //echo ( FvFunctions::ss('pagination-scroll-to-contest') ) ? 'checked' : ''; ?>/> Yes
        </td>
    </tr>-->

    <tr valign="top" class="no-padding">
        <td colspan="3">
            <hr/>
            <input type="submit" class="button-primary" value="<?php _e('Save all Changes', 'fv') ?>" /> &nbsp;<small>You can save here, if not wan't scroll to bottom.</small><br>
            <hr/>
        </td>
    </tr>

    <tr valign="top" class="no-padding">
        <td colspan="3"><hr/></td>
    </tr>
    <!-- ============ Lightbox BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Lightbox settings', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Disable plugin lightbox?', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If you have some conflicts with you standard lightbox(image preview plugin), check it', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input type="checkbox" name="fotov-voting-no-lightbox" <?php echo ( get_option('fotov-voting-no-lightbox', false) ) ? 'checked' : ''; ?>/> <?php _e('Disable', 'fv') ?>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Lightbox title format?', 'fv') ?></th>
        <td class="tooltip">
            <div class="box" title="<?php _e('You can change text, that shows in lightbox', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <input name="fv[lightbox-title-format]" class="all-options" value="<?php echo ( isset($settings['lightbox-title-format']) ) ? $settings['lightbox-title-format'] : '{name} <br/>{votes}'; ?>"/> <br/>
            <small>You can use {name}, {votes}, {description}, {full_description}</small>
        </td>
    </tr>

    <!-- ============ Custom CSS BLOCK ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Custom CSS', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row" style="vertical-align: middle;"><?php _e('Custom CSS styles', 'fv') ?>:</th>
        <td class="tooltip">
            <div class="box" title="<?php _e('If you want do little customization,<br/> use this textarea.', 'fv') ?>" data-tipped-options="position: 'top'">
                <span class="dashicons dashicons-info"></span>
                <div class='position topleft'><i></i></div>
            </div>
        </td>
        <td>
            <textarea name="fotov-custom-css" class="large-text" rows="5" cols="70"><?php echo get_option('fotov-custom-css', ''); ?></textarea> <br/>
            <small><?php _e('Add some styles. "Ctrl + Enter" to show autocomplete.', 'fv') ?></small>
        </td>
    </tr>


</table>