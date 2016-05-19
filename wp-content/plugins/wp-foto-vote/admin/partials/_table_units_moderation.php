<table id="table_units" class="display">
    <thead>
        <tr>
            <th><?php echo __('Thumb', 'fv') ?></th>            
            <th><?php echo __('Name', 'fv') ?></th>
            <th><?php echo __('Contest', 'fv') ?></th>
            <th><?php echo __('Upload info', 'fv') ?></th>
            <th><?php echo __('User email', 'fv') ?></th>
            <th><?php echo __('User id', 'fv') ?></th>
            <th><?php echo __('User ip', 'fv') ?></th>
            <th><?php echo __('Added', 'fv') ?></th>
            <th><?php echo __('Action', 'fv') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $key => $unit) : ?>
        <tr class="status<?php echo $unit->status ?>">
            <td>
                <a href="<?php echo $unit->url ?>" target="_blank"><img src="<?php echo reset (FvFunctions::getPhotoThumbnailArr($unit) ); ?>" width="80" /></a>
            </td>            
            <td class="name"><?php echo $unit->name ?></td>
            <td class="Contest"><?php echo $unit->contest_name ?></td>
            <td class="upload_info"><?php echo FvFunctions::showUploadInfo($unit->upload_info); ?></td>
            <td class="user_email"><?php echo $unit->user_email ?></td>
            <td class="user_id"><?php echo $unit->user_id ?></td>
            <td class="user_ip"><?php echo $unit->user_ip ?></td>
            <td class="name"><?php echo date('d/m/Y',$unit->added_date) ?></td>            
            <td class="actions">
                <a href="#" onclick="fv_approve_contestant(this, <?php echo $unit->id ?>, <?php echo $unit->contest_id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><?php _e('Approve', 'fv') ?></a> / 
                <a href="#" onclick="fv_delete_contestant(this, <?php echo $unit->id ?>, <?php echo $unit->contest_id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;"><?php _e('Delete', 'fv') ?></a>
            </td>            
        </tr>
    <?php endforeach; ?>        
    </tbody>
</table>
