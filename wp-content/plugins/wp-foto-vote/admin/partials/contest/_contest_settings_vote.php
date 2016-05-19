<div class="meta-box-sortables col-lg-12">
    <div id="fv_votes_workplace" class="postbox ">
        <div id="box-vote-settings" class="handlediv" title="Нажмите, чтобы переключить"><br></div>
        <h3 class="hndle"><span><?php echo __('Voting settings', 'fv') ?></span></h3>
        <div class="inside">
            <div id="sv_wrapper" class="b-wrap">

                <fieldset>
                    <div class="row">
                         <div class="form-group col-sm-12">
                             <label><i class="fvicon fvicon-calendar"></i>
                                <?php echo __('Date start', 'fv'); ?>
                             </label>
                            <input type="text" class="datetime form-control" id="date_start" name="date_start" value="<?php echo ($action == 'add') ? date("Y-m-d H:i:s", current_time('timestamp') - 7200) : $contest->date_start ?>">
                            <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>

                        </div>

                        <div class="form-group col-sm-12">
                            <label><i class="fvicon fvicon-calendar"></i>
                                <?php echo __('Date finish', 'fv'); ?>
                                <?php fv_get_tooltip_code(__('When time ends, vote buttons will be hidden,<br/> and user can only see results', 'fv')) ?>
                            </label>
                            <input type="text" class="datetime form-control" id="date_finish" name="date_finish" value="<?php echo ($action == 'add') ? date("Y-m-d H:i:s", current_time('timestamp') + 1209600) : $contest->date_finish ?>">
                            <small><?php echo __('year-month-day h:m:s', 'fv') ?></small>

                        </div>

                        <div class="clear"></div>

                    </div>

                    <div class="form-group">
                        <label for="fv_voting_frequency">
                            <i class="fvicon fvicon-history"></i> <?php _e('Frequency of voting', 'fv') ?> <?php fv_get_tooltip_code(__('Select type of voting - how many user can vote in contest', 'fv')) ?>
                        </label>
                        <select id="fv_voting_frequency" name="fv_voting_frequency" class="form-control">
                            <option value="once" <?php ( isset($contest->id) )? selected('once', $contest->voting_frequency): '' ?>><?php _e('Once for one photo for all time', 'fv') ?></option>
                            <option value="onceF2" <?php ( isset($contest->id) )? selected('onceF2', $contest->voting_frequency) : ''?>><?php _e('Once for 2 photos for all time', 'fv') ?></option>
                            <option value="onceF3" <?php ( isset($contest->id) )? selected('onceF3', $contest->voting_frequency) : ''?>><?php _e('Once for 3 photos for all time', 'fv') ?></option>
                            <option value="onceF10" <?php ( isset($contest->id) )? selected('onceF10', $contest->voting_frequency) : ''?>><?php _e('Once for 10 photos for all time', 'fv') ?></option>
                            <option value="onceFall" <?php ( isset($contest->id) )? selected('onceFall', $contest->voting_frequency): '' ?>><?php _e('For each photo once', 'fv') ?></option>
                            <option value="24hFonce" <?php ( isset($contest->id) )? selected('24hFonce', $contest->voting_frequency): '' ?>><?php _e('For one photo once for 24 hours', 'fv') ?></option>
                            <option value="24hF2" <?php ( isset($contest->id) )? selected('24hF2', $contest->voting_frequency): '' ?>><?php _e('For 2 photos once for 24 hours', 'fv') ?></option>
                            <option value="24hF3" <?php ( isset($contest->id) )? selected('24hF3', $contest->voting_frequency): '' ?>><?php _e('For 3 photos once for 24 hours', 'fv') ?></option>
                            <option value="24hFall" <?php ( isset($contest->id) )? selected('24hFall', $contest->voting_frequency): '' ?>><?php _e('For all photos once for 24 hours', 'fv') ?></option>
                            <?php do_action('fv/admin/contest_settings/voting_frequency', $contest); ?>
                        </select>
                        <small><?php _e('how ofter user can vote', 'fv') ?></small>
                    </div>

                    <div class="form-group">
                        <span class="dashicons dashicons-shield-alt"></span> <label for="fv_security_type"><?php echo __('Contest security type', 'fv') ?>
                            <?php fv_get_tooltip_code(__('Select - how secure contest voting process?', 'fv')) ?></label>
                        <?php //defaultArecapcha  defaultAregistered ?>
                        <select id="fv_security_type" name="fv_security_type" class="pure-input">
                            <option value="default" <?php ( isset($contest->id) )? selected('default', $contest->security_type) : ''?>>IP + cookies + evercookie</option>
                            <option value="defaultArecaptcha" <?php ( isset($contest->id) )? selected('defaultArecaptcha', $contest->security_type): '' ?>>IP+cookies+evercookie + Recaptcha (required Recaptcha KEY)</option>
                            <option value="cookieArecaptcha" <?php ( isset($contest->id) )? selected('cookieArecaptcha', $contest->security_type): '' ?>>cookies+evercookie + Recaptcha (required Recaptcha KEY)</option>
                            <option value="defaultAsubscr" <?php ( isset($contest->id) )? selected('defaultAsubscr', $contest->security_type): '' ?>>IP+cookies+evercookie + Subscribe form</option>
                            <option value="defaultAfb" <?php ( isset($contest->id) )? selected('defaultAfb', $contest->security_type): '' ?>>IP+cookies+evercookie + Facebook Share (required FB app ID)</option>
                            <option value="defaultAsocial" <?php ( isset($contest->id) )? selected('defaultAsocial', $contest->security_type): '' ?>>IP+cookies+evercookie + Social autorization</option>
                            <option value="cookieAsocial" <?php ( isset($contest->id) )? selected('cookieAsocial', $contest->security_type): '' ?>>cookies+evercookie+ Social autorization</option>
                            <option value="cookieAregistered" <?php ( isset($contest->id) )? selected('cookieAregistered', $contest->security_type): '' ?>>cookies+evercookie + Autorized user</option>
                            <?php do_action('fv/admin/contest_settings/security_type', $contest); ?>
                        </select>
                    </div>

                </fieldset>

            </div>
        </div>
    </div>
</div>