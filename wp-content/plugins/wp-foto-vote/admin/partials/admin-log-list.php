<?php
defined('ABSPATH') or die("No script kiddies please!");
?>

<script>
        jQuery(document).ready(function () {
                jQuery('select#fv-filter-contest').on('change', function () {
                        var contestFilter = jQuery("select#fv-filter-contest").val();

                        if (contestFilter != '') {
                                document.location.href = 'admin.php?page=fv-vote-log&contest_id=' + contestFilter;
                        } else {
                                document.location.href = 'admin.php?page=fv-vote-log';
                        }
                });

                jQuery('select#fv-filter-contest-photo').on('change', function () {
                        var contestFilter = jQuery("select#fv-filter-contest").val();
                        var contestFilterPhoto = jQuery("select#fv-filter-contest-photo").val();

                        if (contestFilter != '') {
                                document.location.href = 'admin.php?page=fv-vote-log&contest_id=' + contestFilter + '&photo_id=' + contestFilterPhoto;
                        } else {
                                document.location.href = 'admin.php?page=fv-vote-log';
                        }
                });
        });

        //});

</script>

<div class="wrap">
        <?php do_action('fv_admin_notices'); ?>
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

                #fw-pagination a {
                        font-weight: 400;
                        font-size: 14px;
                        display: inline-block;
                        padding: 6px;
                        background: #cccccc;
                        -webkit-border-radius: 3px;
                        -moz-border-radius: 3px;
                        border-radius: 3px;
                }

                #fw-pagination {
                        margin-left: 10px;
                }

                .ml50 {
                        margin-left: 30px;
                }

                .tablenav .actions label {
                        display: inline-block;
                }

                .tablenav .actions select[name*="contest-filter"] {
                        float: none;
                }
        </style>

        <h2><?php _e('Voting log / can be cleared on the page, where you can edit contest', 'fv') ?></h2>

        <p>
            <?php _e('There your can see votes log. It can help you to check voting for fraud.
            As example if your see a lot similar `browsers` voted for one person or many empty `refer`.<br/>
            Also you can compare `change` field for check voting activity almost at the same time by one photos.', 'fv') ?>
            <span style="color:red;"><?php _e('!Important - removing records from log not decreases photo votes.', 'fv') ?></span>
        </p>

        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="log-filter" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                <!-- Now we can render the completed list table -->
                <?php $Table->search_box( __('Search by `ip, browser, referer, country, user_id` > 1 symbol', "fv"), 'search_field') ?>
                <!-- Now we can render the completed list table -->
                <?php $Table->display() ?>
        </form>

        <h3><?php _e('Export data as csv (exports max 5000 rows, if have more - use PhpMyAdmin or filter by Contest ID)', 'fv') ?></h3>
        <button type="button" onclick="fv_export('log_list', '<?php echo wp_create_nonce('fv_export_nonce') ?>', 'period', jQuery('select[name=\'export-period\'] option:selected').val() );"><?php _e('Export to csv', 'fv') ?></button>
        <select name="export-period">
                <option value="15"><?php _e('last 15 days', 'fv') ?></option>
                <option value="30"><?php _e('last 30 days', 'fv') ?></option>
                <option value="60"><?php _e('last 60 days', 'fv') ?></option>
                <option value="90"><?php _e('last 90 days', 'fv') ?></option>
                <option value="180"><?php _e('last 180 days', 'fv') ?></option>
                <option value="180"><?php _e('last 360 days', 'fv') ?></option>
        </select>


</div>