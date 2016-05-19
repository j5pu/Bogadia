<div class="meta-box-sortables col-lg-12">
    <div id="fv_votes_workplace" class="postbox ">
        <div id="box-vote-settings" class="handlediv" title="Нажмите, чтобы переключить"><br></div>
        <h3 class="hndle"><span><?php echo __('Design / social settings', 'fv') ?></span></h3>
        <div class="inside">
            <div id="sv_wrapper">

                <fieldset>
                    <div class="row">
                        <div class="form-group col-sm-8">
                                <label><i class="fvicon fvicon-share"></i> <?php echo __('Title contest for soc. networks ', 'fv') ?>
                                    <?php fv_get_tooltip_code(__('*name* will be replaced by name of the contestant.<br /> Only works for Vkontakte, Twitter and Pinterest, <br /> others socials take title from title page. <br /> <br /> If not specified - the name of the contestant taken', 'fv')) ?>
                                </label>
                                <input type="text" name="fv_social_title" class="form-control" value="<?php echo ($action == 'add') ? '' : stripcslashes($contest->soc_title) ?>">
                        </div>
                        <div class="form-group col-sm-8">
                                <label><?php echo __('Description contest for soc. networks', 'fv') ?>
                                    <?php fv_get_tooltip_code(__('*name* will be replaced by name of the contestant.<br /> Only works for Vkontakte and Twitter<br /> take the rest of the description of the description page. <br /> <br /> If not specified - soc. networks use at its discretion', 'fv')) ?>
                                </label>
                                <input type="text" name="fv_social_descr" class="form-control" value="<?php echo ($action == 'add') ? '' : stripcslashes($contest->soc_description) ?>">
                        </div><!-- .misc-pub-section -->

                        <div class="form-group col-sm-8">
                                <label><?php echo __('Picture for social networks', 'fv') ?>
                                    <?php fv_get_tooltip_code(__('Only works for Vkontakte and Pinterest.<br /><br /> If not specified - is taken from field `Image`', 'fv')) ?>
                                </label>
                                <input type="text" name="fv_social_photo" class="form-control" value="<?php echo ($action == 'add') ? '' : $contest->soc_picture ?>">
                        </div><!-- .misc-pub-section -->

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-8">
                            <label for="fv_timer">
                                <i class="fvicon fvicon-stopwatch"></i> </span><?php echo __('Timer', 'fv') ?>
                                <?php fv_get_tooltip_code(__('If you want show countdown timer, check this option', 'fv')) ?>
                            </label>
                            <select id="fv_timer" name="fv_timer" class="form-control">
                                <option value="no" <?php ( isset($contest->id) )? selected('no', $contest->timer) : ''?>> <?php _e('No show', 'fv') ?></option>
                                <?php foreach ($countdowns as $key => $countdown_title): ?>
                                    <option value="<?php echo $key ?>" <?php ( isset($contest->id) )?  selected($key, $contest->timer): ''; ?>><?php echo $countdown_title ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-8">
                            <label for="lightbox_theme"><?php echo __('Lightbox theme', 'fv'); ?></label>
                            <select id="lightbox_theme" name="lightbox_theme" class="form-control">
                                <?php foreach (FvFunctions::getLightboxArr() as $lightbox => $theme_title): ?>
                                    <option value="<?php echo $lightbox ?>" <?php ( isset($contest->id) )? selected($lightbox, $contest->lightbox_theme): '' ?>><?php echo $theme_title ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-8">
                            <label for="sorting"><?php echo __('Photos order', 'fv'); ?> <?php fv_get_tooltip_code(__('Output order of pictures on the page', 'fv')) ?></label>
                            <select id="sorting" name="sorting" class="form-control">
                                <?php foreach (fv_get_sotring_types_arr() as $key => $sort_title): ?>
                                    <option value="<?php echo $key ?>" <?php ( isset($contest->id) )?  selected($key, $contest->sorting): ''; ?>><?php echo $sort_title ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="show_leaders" <?php echo ($action == 'add' || !$contest->show_leaders) ? '' : 'checked' ?>>
                            <?php echo __('Show leaders block', 'fv'); ?>
                        </label>
                        <small>(This option will show Leaders block above contest photos. Yuo can also use leaders Shortcode and off this option for show Leaders in other place/page.
                            <a href="<?php echo admin_url('admin.php?page=fv-settings#leaders'); ?>" target="_blank">More settings here</a>)</small>
                    </div>

                    <div class="clear"></div>

                </fieldset>

            </div>
        </div>
    </div>
</div>