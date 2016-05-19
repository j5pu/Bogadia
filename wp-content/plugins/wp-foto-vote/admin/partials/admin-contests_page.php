<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>

<div class="wrap">
    <h2><?php echo __('Photo contests', 'fv'); ?>
        <a href="?page=<?php echo $_REQUEST['page']; ?>&action=add" class="add-new-h2"><?php echo __('Add new', 'fv'); ?> </a>
    </h2>

    <?php
        if (isset($action) && $action == 'delete') {
            echo '<div id="setting-error-settings_updated" class="updated settings-error">
                <p>
                    <strong>' . __('Contest deleted.', 'fv') . '</strong>
                </p>
             </div>';
        }
    ?>

    <div class="fv_content_wrapper">
        <?php
        //Create an instance of our package class...
        $testListTable = new FV_List_Contests();
        //Fetch, prepare, sort, and filter our data...
        $testListTable->prepare_items();

        ?>        

        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
            <p><?php echo __('Create contest, copy shortcode and insert it into the page/post text.', 'fv') ?></p>
            <p>[fv_contests_list type="active,upload_opened,finished" count=""] <?php _e('- for shows contests list', 'fv') ?></p>
        </div>

        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display() ?>
        </form>        
    </div>  <!-- .fv_content_wrapper :: END -->

    <div id="fv-sidebar"><?php include('_sidebar.php') ?></div><!-- #fv-sidebar :: END -->
     

</div>  
    
<style type="text/css">
    .box {
        width: 25px;
        float: right;
        height: 100%;
    }
    .tooltip {
        width: 25px;
    }
    .no-padding, .no-padding td {
        padding: 0;        
    }
    .dashicons-info:before {
        content: "\f348";
    }
    @media (min-width: 468px) {
        .fv_content_wrapper {
            float: left;
            width: 74%;
        }

        #fv-sidebar {
            width: 25%;
            max-width: 270px;
            /*padding: 0 0 0 10px;*/
            float: right;
        }
    }
    #fv-sidebar .inside {
        margin: 6px 0 0;
    }
    #fv-sidebar h3 {
        font-size: 14px;
        padding: 8px 12px;
        margin: 0;
        line-height: 1.4;
    }
    .gadash-title {
        float: left;
        margin-right: 10px;
        margin-top: 2px;
    }    
    
    .wp-list-table th#name {
        width: 22%;
    }

</style>