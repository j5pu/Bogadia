<table id="table_units" class="display">
    <thead>
        <tr>
            <th><?php echo __('Thumb', 'fv') ?></th>            
            <th><?php echo __('Name', 'fv') ?></th>
            <th><?php echo __('Description', 'fv') ?></th>
            <th><?php echo __('Votes count', 'fv') ?></th>
            <th><?php echo __('Upload info', 'fv') ?></th>
            <th><?php echo __('User email', 'fv') ?></th>
            <th><?php echo __('User id', 'fv') ?></th>
            <th><?php echo __('User ip', 'fv') ?></th>
            <th><?php echo __('Status', 'fv') ?></th>
            <th><?php echo __('Added', 'fv') ?></th>
            <th><?php echo __('Actions', 'fv') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($contest->items as $unit) : ?>
        <?php include '_table_units_tr.php'; ?>
    <?php endforeach; ?>        
    </tbody>
</table>