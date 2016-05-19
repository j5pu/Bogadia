<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>
    
<div class="wrap">
    <style type="text/css">
        .ml50 {
            margin-left: 30px;
        }
        .tablenav .actions label {
            display: inline-block;
        }
        .tablenav .actions #fv-filter-contest {
            float: none;
        }

        #table_units {
            background: white;
        }

        .postbox {
            padding: 10px;
        }
    </style>


    <h2><?php _e('Moderation users uploaded photos', 'fv') ?></h2>
   <div class="postbox"><div class="inside">
        <?php include '_table_units_moderation.php'; ?>
   </div></div>

        
</div>