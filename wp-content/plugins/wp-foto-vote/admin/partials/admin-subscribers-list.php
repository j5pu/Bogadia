<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>

<style type="text/css">
        #fw-pagination a.active {
            color: #FFF;
            font-weight: bold;
            margin-left: 5px;
            margin-right: 5px;
        }
        #fw-pagination a.active:first-child {
            margin-left: 0px;
        }
        #fw-pagination a{
            font-weight: 400;
            font-size: 14px;
            display: inline-block;
            padding: 6px;
            background: #cccccc;
            -webkit-border-radius:3px;
            -moz-border-radius:3px;
            border-radius:3px;
        }
        #fw-pagination {
            margin-left: 10px;
        }
    </style>

<div class="wrap">
    <hr />
    <h2><?php _e('Log subscribers (15 entries) / can clear on the page, where you can edit a poll (post)', 'fv') ?></h2>
    <p><?php _e('If you select voting security - `IP + cookies + evercookie + Subscribe form`, after users voted, there adds new records.', 'fv') ?></p>

    <button type="button" onclick="fv_export('subscribers_list', '<?php echo wp_create_nonce('fv_export_nonce') ?>', 'period', jQuery('select[name=\'export-period\'] option:selected').val() );"><?php _e('Export to csv (output max 5000 rows)', 'fv') ?></button>
    <select name="export-period">
        <option value="15"><?php _e('last 15 days', 'fv') ?></option>
        <option value="30"><?php _e('last 30 days', 'fv') ?></option>
        <option value="60"><?php _e('last 60 days', 'fv') ?></option>
        <option value="90"><?php _e('last 90 days', 'fv') ?></option>
        <option value="180"><?php _e('last 180 days', 'fv') ?></option>
        <option value="360"><?php _e('last 360 days', 'fv') ?></option>
    </select>

    <?php if (is_array($stats)): ?>
        <div id="fw-pagination" class="tablenav-pages">
            Page:
            <?php  // pagination
                $url = admin_url( 'admin.php?page=fv-subscribers-list');
                $total_pages = ceil(count($stats) / 50);
                for ($i=0; $i<=$total_pages-1; $i++) { 
                    $class = ($page == $i)? 'active': '';
                    echo "<a class='{$class}' href='{$url}&fv-page=".$i."'>".$i."</a> ";
                }; 
            ?>
        </div>
        <table class="wp-list-table widefat fixed">
            <thead>
            <tr valign="top">
                <th scope="col" class="manage-column"><?php _e('Post', 'fv') ?></th>
                <th scope="col" class="manage-column">added</th>
                <th scope="col" class="manage-column"><?php _e('name', 'fv') ?></th>
                <th scope="col" class="manage-column"><?php _e('email', 'fv') ?></th>
            </tr>
            </thead>
        <?php
        $i = 0;
        foreach ($stats as $sValue):
            if ($sValue->email):
        ?>
            <tr class="<?php echo ($i % 2 == 0)? 'alternate' : ''; ?>">
                <td><a target="_blank" href="http://<?php echo $_SERVER['HTTP_HOST']; ?>?page_id=<?php echo $sValue->post_id ?>"><?php echo $sValue->post_id ?></a></td>
                <td><?php echo $sValue->changed ?></td>
                <td><?php echo $sValue->name ?></td>
                <td><?php echo $sValue->email ?></td>
            </tr>
        <?php
            endif;
            $i++;
        endforeach;
        ?>
        </table>
    <?php endif; ?>        
</div>        