<table class="form-table">

    <!-- ============ Leaders Vote ============ -->
    <tr valign="top" class="no-padding">
        <td colspan="3"><h3><?php _e('Voting leaders', 'fv') ?></h3></td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('How many leaders show in page?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select count of voting leaders', 'fv') ); ?>
        <td>
            <select name="fotov-leaders-count">
                <option value="3" <?php selected('3', get_option('fotov-leaders-count')); ?>><?php _e('Three', 'fv') ?></option>
                <option value="2" <?php selected('2', get_option('fotov-leaders-count')); ?>><?php _e('Two', 'fv') ?></option>
                <option value="1" <?php selected('1', get_option('fotov-leaders-count')); ?>><?php _e('One', 'fv') ?></option>
                <option value="4" <?php selected('4', get_option('fotov-leaders-count')); ?>><?php _e('Four', 'fv') ?></option>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Leaders style type?', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Select - how to display contest leaders?', 'fv') ); ?>
        <td>
            <select name="fotov-leaders-type">
                <option value="text" <?php selected('text', get_option('fotov-leaders-type')); ?>><?php _e('Text', 'fv') ?></option>
                <option value="block" <?php selected('block', get_option('fotov-leaders-type')); ?>><?php _e('Block', 'fv') ?></option>
                <option value="block_2" <?php selected('block_2', get_option('fotov-leaders-type')); ?>><?php _e('Block 2', 'fv') ?></option>
                <option value="table_1" <?php selected('table_1', get_option('fotov-leaders-type')); ?>><?php _e('Table 1', 'fv') ?></option>
                <option value="table_2" <?php selected('table_2', get_option('fotov-leaders-type')); ?>><?php _e('Table 2', 'fv') ?></option>
            </select>
        </td>
    </tr>


    <tr valign="top">
        <th scope="row"><?php _e('Leaders thumbnails size & leaders block item width (for "block" and "table" types)', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Thumbnails size in leaders block & leaders block item width', 'fv') ); ?>
        <td> width:<input type="number" name="fv[lead-thumb-width]" value="<?php echo FvFunctions::ss('lead-thumb-width', 280); ?>" min="0" max="1200"/> px. /
            height:<input type="number" name="fv[lead-thumb-height]" value="<?php echo FvFunctions::ss('lead-thumb-height', 200); ?>" min="0" max="1200"/> px.
            / hard crop: <input type="checkbox" name="fv[lead-thumb-crop]" <?php echo checked( FvFunctions::ss('lead-thumb-crop', true) ); ?>/>
            <small><?php _e('In hard crop means that thumbnail size will be 100% equal (if checked) or proportional (if unchecked) on the larger side.', 'fv'); ?></small>
        </td>
    </tr>


    <tr valign="top">
        <th scope="row"><?php _e('Primary background color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Title background color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[lead-primary-bg]" class="color" value="<?php echo FvFunctions::ss('lead-primary-bg', '#e6e6e6', 7); ?>"/>
            <span>Select color <button type="button" onclick="fv_reset_color(this, '#e6e6e6');">reset</button></span>
            &nbsp;<small>(In "block" type used as title background color, in "table" type used as heading background, in "New year" theme not work)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Primary text color', 'fv') ?></th>
        <?php echo fv_get_td_tooltip_code( __('Title text color', 'fv') ); ?>
        <td class="fv-colorpicker">
            <input type="text" name="fv[lead-primary-color]" class="color" value="<?php echo FvFunctions::ss('lead-primary-color', '#ffffff', 7); ?>"/>
            <span>Select color <button type="button" onclick="fv_reset_color(this, '#ffffff');">reset</button></span>
            &nbsp;<small>(In "block" type used as title text color, in "table" type used as photo name color, in "New year" theme not work)</small>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row"><?php _e('Round leaders thumbnails corners?', 'fv') ?> </th>
        <?php echo fv_get_td_tooltip_code( __('Set up 1-5% for slightly round corners, or st up 50% for give circled image. [used "border-radius" css attribute]', 'fv') ); ?>
        <td> width:<input type="number" name="fv[lead-thumb-round]" value="<?php echo FvFunctions::ss('lead-thumb-round', 0); ?>" min="0" max="50"/> %
            &nbsp;<small>(<?php _e('2% - a few rounded, 50% - circled images', 'fv'); ?>)</small>
        </td>
    </tr>

</table>