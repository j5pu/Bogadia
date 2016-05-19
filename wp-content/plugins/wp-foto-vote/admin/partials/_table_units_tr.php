<?php
    /*
     ** Params $unit - contestant item
     */
    //$image_src = ($unit->image_id)? wp_get_attachment_image_src($unit->image_id) : '';
    $image_src = FvFunctions::getPhotoThumbnailArr($unit);
    $img_class = '';
    if ( isset($image_src[4]) ) {
        $img_class= $image_src[4];
    } elseif ( isset($unit->options['provider']) ) {
        $img_class = $unit->options['provider'];
    }
?>
        <tr class="id<?php echo $unit->id ?> status<?php echo $unit->status ?> <?php echo ( isset($edit) )? 'edited' : ''; ?>">
            <td class="img <?php echo $img_class ?>"><a href="<?php echo $unit->url ?>" target="_blank"><img src="<?php echo ( is_array($image_src) )? $image_src[0] : ''; ?>" width="50" /></a></td>
            <td class="name"><?php echo $unit->name ?></td>
            <td class="description"><?php echo $unit->description ?></td>
            <td class="votes_count"><?php echo $unit->votes_count ?></td>
            <td class="upload_info"><?php echo FvFunctions::showUploadInfo($unit->upload_info); ?></td>
            <td class="user_email"><?php echo $unit->user_email ?></td>
            <td class="user_id"><a href="<?php echo admin_url('user-edit.php?user_id='.$unit->user_id) ?>" target="_blank"><?php echo $unit->user_id ?></a></td>
            <td class="user_ip"><?php echo $unit->user_ip ?></td>
            <td><?php echo __(fv_get_status_name($unit->status), 'fv') ?></td>
            <td class="added"><?php echo date('d/m/Y',$unit->added_date) ?></td>            
            <td class="actions">
                <a href="#0" onclick="fv_form_contestant(this, <?php echo $unit->contest_id ?>, <?php echo $unit->id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><?php _e('Edit', 'fv') ?></a>
                / <a href="#0" onclick="fv_delete_contestant(this, <?php echo $unit->id ?>, <?php echo $unit->contest_id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><?php _e('Delete', 'fv') ?></a>
                <a href="#0" title="<?php _e("rotate right", 'fv') ?>" onclick="fv_rotate_image(this, 270, <?php echo $unit->contest_id ?>, <?php echo $unit->id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><span class="dashicons dashicons-imgedit-rright rotate_img"></span></a>

                <a href="#0" title="<?php _e("rotate left", 'fv') ?>" onclick="fv_rotate_image(this, 90, <?php echo $unit->contest_id ?>, <?php echo $unit->id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><span class="dashicons dashicons-imgedit-rleft rotate_img"></span></a>
            </td>
        </tr>