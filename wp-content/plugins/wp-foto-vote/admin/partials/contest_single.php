<div class="wrap" id="contest_edit">
    <?php do_action('fv_admin_notices'); ?>
    <h2>
        <?php echo ($action == 'add') ? __('New contest', 'fv') : __('Edit contest', 'fv') . ' #' . $contest->id; ?>
        <a href="?page=<?php echo $_REQUEST['page']; ?>&action=add" class="add-new-h2"><?php echo __('Add new', 'fv'); ?> </a>
    </h2>

<style type="text/css">
    #contest_edit .dashicons {
            margin-top: 2px;
    }
    #contest_edit #side-sortables {
        min-height: 50px !important;
    }
    .box {
        width: 25px;
        float: right;
        height: 100%;
    }
    .tooltip {
        width: 25px;
    }
    .dashicons-info:before {
        content: "\f348";
    }
    .gadash-title {
        float: left;
        margin-right: 10px;
        margin-top: 2px;
    }
    #titlediv {
        margin-bottom: 4px;
    }
    #titlediv #title {
        line-height: 120%;
    }   
    .clear_ip {
        text-align: left;
        margin-bottom: 15px;
    }
    .clear_ip:after {
        display: table;
        clear: both;
        content: ' ';
    }
    #sv_wrapper:after {
        clear: both;
        display: table;
        content: ' ';
    }
    
    .shortcode {
        width: 100%;
        display: block;
        font-size: 120%;
    }       
    
    #titlediv {
        width: 100%;
    }
    input.datetime {
        width: 130px;
    }
    
    .col6 {
        width: 48%;
        float: left;
    }
    
    .curtime #timestamp:before {
        top: 3px;
    }
    
    #post-body:after {
        display: table;
        clear: both;
        content: ' ';        
    }
    /* TABLE */
    #table_units td.votes_count, #table_units td.user_id, #table_units td.user_ip, #table_units td.added {
        text-align: center;
    }
    #table_units td.img {
        width: 45px;
        padding: 3px 2px;
        text-align: center;
        position: relative;
    }
    #table_units td.img.dropbox:before {
        position: absolute;
        left: 30%;
        top: 3px;
        background: url(<?php echo plugins_url('wp-foto-vote/assets/img/admin/dropbox.png') ?>);
        background-size: cover;
        width: 24px;
        height: 22px;
        content: '';
    }
    #table_units td.img.vimeo:before,
    #table_units td.img.youtube:before,
    #table_units td.img.cloudinary:before {
        font-family: 'icomoon_fv';
        speak: none;
        font-style: normal;
        font-weight: normal;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;

        position: absolute;
        left: 35%;
        top: 3px;
        content: "\e60f";
        font-size: 24px;
        line-height: 26px;

        width: 24px;
        height: 22px;
        color: rgb(94, 232, 247);
    }
    #table_units td.img.cloudinary:before {
        content: "\e600";
    }
    #table_units td.img.cloudinary.rotated:before {
        content: "\e648";
    }

    #table_units td.votes_count {
        width: 70px;
    }
    #table_units td.user_id {
        width: 70px;
    }
    #table_units td.added {
        width: 70px;
    }
    #table_units td.actions {
        width: 140px;
        min-width: 160px;
        padding: 10px 4px;
    }

    @media (max-width: 1430px) {
        #table_units tbody tr td.user_id, #table_units thead tr th:nth-child(7) {
            display: none;
        }
    }
    @media (max-width: 1340px) {
        #table_units tbody tr td.description, #table_units thead tr th:nth-child(3) {
            display: none;
        }
    }    
    @media (max-width: 1200px) {
        #table_units tbody tr td.upload_info, #table_units thead tr th:nth-child(5) {
            display: none;
        }
    }

    
</style>

    <form name="post" action="<?php echo admin_url( 'admin.php?page=fv&action=edit&contest=' ); ?><?php echo ($action == 'add') ? '-1' : $contest->id ?>" method="post" id="post">
        <?php wp_nonce_field( 'fv_edit_contest_action','fv_edit_contest_nonce' ); ?>
        <input type="hidden" name="contest_id" value="<?php echo ($action == 'add') ? '-1' : $contest->id ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div id="submitdiv" class="postbox ">
                            <div id="box-contest-sidebar" class="handlediv" title="Нажмите, чтобы переключить"><br></div>
                                <h3 class="hndle"><span><?php echo __('Save changes', 'fv') ?></span></h3>
                            <div class="inside">
                                <div class="submitbox" id="submitpost">

                                    <div id="minor-publishing">

                                        <div id="misc-publishing-actions">
                                            <div class="misc-pub-section">
                                                <?php echo __('Shortcode, to show contest on page/post:', 'fv') ?>
                                                <input type="text" class="shortcode" readonly="" value='[fv id="<?php echo (isset($contest->id))? $contest->id : '' ?>"]'>
                                                <br/>
                                                <?php echo __('Shortcode, to show only upload form:', 'fv') ?>
                                                <input type="text" class="shortcode" readonly="" value='[fv_upload_form contest_id="<?php echo (isset($contest->id))? $contest->id : '' ?>" show_opened="false"]'>
                                                <br/>
                                                <?php echo __('Shortcode, to show only countdown:', 'fv') ?>
                                                <input type="text" class="shortcode" readonly="" value='[fv_countdown contest_id="<?php echo (isset($contest->id))? $contest->id : '' ?>"]'>
                                                <br/>
                                                <?php echo __('Shortcode, to show only leaders block:', 'fv') ?>
                                                <input type="text" class="shortcode" readonly="" value='[fv_leaders contest_id="<?php echo (isset($contest->id))? $contest->id : '' ?>"]'>

                                                <br/>
                                            <?php if ( isset($contest->id) ) : ?>
                                                    <button class="button" type="button" onclick="fv_export('contest_data', '<?php echo wp_create_nonce('fv_export_nonce') ?>', 'contest_id', <?php echo $contest->id ?>);">
                                                        <span class="dashicons dashicons-upload"></span> <?php _e('Export all photos to csv', 'fv') ?>
                                                    </button>
                                                    <br/><small><a href="http://youtu.be/JNp15MjZwUs" target="_blank"><?php _e('Import CSV to Google Drive', 'fv') ?></a></small>
                                            <?php endif; ?>
                                            </div>
                                            <hr>
                                        </div>
                                        <div class="clear"></div>
                                    </div>

                                    <div id="major-publishing-actions">

                                        <div class="clear_ip">
                                            <button type="button" class="button" onclick="fv_clear_stats(<?php echo $contest->id ?>); return false;">
                                                    <span class="dashicons dashicons-trash"></span> <?php echo __('Clear ip list for this contest', 'fv') ?>
                                            </button>
                                        </div>
                                        <div class="clear_votes">
                                            <button type="button" class="button" onclick="fv_clear_votes(<?php echo $contest->id ?>); return false;">
                                                <span class="dashicons dashicons-no-alt"></span> <?php echo __('Reset all votes in this contest', 'fv') ?>
                                            </button>
                                        </div>
                                        <br/>
                                        <div id="delete-action">
                                            <a class="submitdelete deletion"
                                               onclick="return confirm('<?php _e('Are you sure', 'fv') ?>');"
                                               href="<?php echo admin_url( 'admin.php?page=fv&action=delete&contest=' ); ?><?php echo ($action == 'add') ? '-1' : $contest->id ?>">
                                                <?php _e('Delete', 'fv') ?>
                                            </a>
                                        </div>

                                        <div id="publishing-action">
                                            <span class="spinner"></span>
                                            <input name="original_publish" type="hidden" id="original_publish" value="<?php echo __('Save', 'fv'); ?>">
                                            <button type="submit" name="publish" id="publish" class="button button-primary button-large" accesskey="s">
                                                    <?php _e('Save contest settings', 'fv'); ?>
                                            </button>
                                            <br/><small><?php _e('Tip: this button only saves settings at left.', 'fv'); ?></small>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div id="formatdiv" class="postbox " style="display: none;">
                            <div class="handlediv" title="Нажмите, чтобы переключить"><br></div>
                                <h3 class="hndle"><span>Формат</span></h3>
                            <div class="inside">
                                <div id="post-formats-select">
                                    <input type="radio" name="post_format" class="post-format" id="post-format-0" value="0" checked="checked"> <label for="post-format-0" class="post-format-icon post-format-standard">Стандартный</label>
                                    <br><input type="radio" name="post_format" class="post-format" id="post-format-video" value="video"> <label for="post-format-video" class="post-format-icon post-format-video">Видео</label>
                                    <br><input type="radio" name="post_format" class="post-format" id="post-format-aside" value="aside"> <label for="post-format-aside" class="post-format-icon post-format-aside">Заметка</label>
                                    <br><input type="radio" name="post_format" class="post-format" id="post-format-quote" value="quote"> <label for="post-format-quote" class="post-format-icon post-format-quote">Цитата</label>
                                    <br>
                                </div>
                            </div>
                        </div>



                    </div></div>
                <div id="postbox-container-2" class="postbox-container b-wrap">

                    <div id="titlediv">
                        <div id="titlewrap">
                            <input type="text" name="contest_title" size="30" placeholder="<?php echo __('Enter contest name', 'fv') ?>" value="<?php echo ($action == 'add') ? '' : $contest->name ?>" id="title" required="true">
                        </div>
                    </div>
                    <div class="row"><?php
                        include 'contest/_contest_settings_vote.php';
                        include 'contest/_contest_settings_upload.php';
                        include 'contest/_contest_settings_design.php';
                        include 'contest/_contest_settings_other.php';
                    ?></div>

                </div>
            </div><!-- /post-body -->
            <div class="metabox-holder columns-1">
                <div id="fv_votes_workplace" class="postbox ">
                    <div class="handlediv" title="Нажмите, чтобы переключить"><br></div>
                    <h3 class="hndle"><span><?php echo __('Contestants', 'fv'); ?></span></h3>
                    <div class="inside b-wrap">
                            <?php if ($action != 'add'): ?>
                                <div id="sv_table" class="table-responsive" >
                                        <?php include '_table_units.php'; ?>
                                </div>

                                <button type="button" onclick="fv_form_contestant(this, <?php echo $contest->id ?>, '', '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;" class="button">
                                    <span class="dashicons dashicons-welcome-add-page"></span><?php echo __('Add one photo', 'fv'); ?>
                                </button>
                                <button type="button" onclick="fv_many_contestants(this, <?php echo $contest->id ?>, '<?php echo wp_create_nonce('fv_nonce') ?>'); return false;" class="button">
                                    <span class="dashicons dashicons-welcome-add-page"></span><?php echo __('Add many photos (test mode)', 'fv'); ?>
                                </button>
                                <input type="button" onclick="contestTable.draw(); return false;" value="<?php echo __('Redraw table', 'fv'); ?>" class="button">
                            <?php else: ?>
                                <h2><?php echo __('Save contest for editing contestants!', 'fv'); ?></h2>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div><!-- /poststuff -->
    </form>

</div>  <!-- .wrap :: END -->

<!-- edit contestat popup -->
<div class="modal fade b-wrap" id="fv_popup" tabindex="-1" role="dialog" aria-labelledby="fv_popup_label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>
