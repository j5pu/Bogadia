<div class="fv_toolbar--container">
    <ul class="fv_toolbar">
        <li>
            <a href="#0" class="tabbed_a active" data-target=".fv_contest_container">
                <i class="fvicon-images"></i> <?php echo fv_get_transl_msg('toolbar_title_gallery'); ?>
            </a>
        </li>
        <?php if ($upload_enabled): ?>
            <li>
                <a href="#0" class="tabbed_a" data-target=".fv_upload"><i class="fvicon-download2"></i>
                    <?php echo fv_get_transl_msg('toolbar_title_upload'); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php do_action('fv/toolbar/middle_hook', $contest); ?>
        <li class="fv_toolbar-dropdown">
            <span>
                <?php echo fv_get_transl_msg('toolbar_title_sorting'); ?>
            </span>
            <select class="fv_sorting">
                <?php foreach( fv_get_sotring_types_arr() as $sort_type => $sort_name ) : ?>
                    <option value="<?php echo fv_set_query_arg('fv-sorting', $sort_type, fv_set_query_arg('fv-scroll', 'fv_toolbar')); ?>" <?php selected($sort_type, $fv_sorting) ?>>
                        <?php echo fv_get_transl_msg('toolbar_title_sorting_' . $sort_type, $sort_name);  ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </li>
    </ul>
</div>
