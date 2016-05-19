<?php
?>

<div id="fv_constest_item">
        <div class="fv_name">
                <span class="name"><?php echo $contestant->name ?></span>
                <div class="back">
                        <a href="<?php echo remove_query_arg( 'contest_id', 	remove_query_arg( 'photo') ); ?>">
                                <i class="fvicon fvicon-login"></i> <?php echo $public_translated_messages['back_to_contest'] ?>
                        </a>
                </div>
        </div>
        <div class="fv_photo" style="width: <?php echo $image[1] + 10 ?>px;">
                <div class="fv_photo_votes">
                        <a href="#" onclick="sv_vote(<?php echo $contestant->id ?>); return false;">
                            <?php if( $hide_votes == false ): ?>
                                <i class="fvicon-heart3"></i> <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->votes_count ?></span>
                            <?php else: ?>
                                <i class="fvicon-heart3"></i>
                            <?php endif; ?>
                        </a>
                </div>
                <?php if( FvFunctions::ss('soc-counter', false) ): ?>
                    <div class="fv-svotes-container">
                        <a href="#0" class="fv-svotes-a" onclick="FvModal.goShare(<?php echo $contestant->id ?>); return false;" title="<?php echo $public_translated_messages['shares_count_text']; ?>">
                            <i class="fvicon-share"></i> <span class="fv-soc-votes fv_svotes_<?php echo $contestant->id ?>">0</span>
                        </a>
                    </div>
                <?php endif; ?>

                <img src="<?php echo $image[0] ?>"/>

                <?php if (!empty($next_id)): ?>
                        <div class="fv_next fv_nav">
                                <a href="<?php echo $page_url . '=' . $next_id ?>"
                                   title="<?php _e('Next', 'fv') ?>"><span class="fvicon-arrow-right"></span></a>
                        </div>
                <?php endif; ?>

                <?php if (!empty($prev_id)): ?>
                        <div class="fv_prev fv_nav">
                                <a href="<?php echo $page_url . '=' . $prev_id ?>"
                                   title="<?php _e('Previous', 'fv') ?>"><span class="fvicon-arrow-left"></span></a>
                        </div>
                <?php endif; ?>

        </div>
        <div class="fv_social">
                <span><?php _e('Share to friends', 'fv') ?></span>

                <div class="fv_social_icons">
                        <?php if (!get_option('fotov-voting-noshow-vk', false)): ?>
                                <a href="#" onclick="return sv_vote_send('vk', this,<?php echo $contestant->id ?>)"
                                   target="_blank"><img
                                            src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-vk.png') ?>"/></a>
                        <?php endif; ?>
                        <?php if (!get_option('fotov-voting-noshow-fb', false)): ?>
                                <a href="#" onclick="return sv_vote_send('fb', this,<?php echo $contestant->id ?>)"
                                   target="_blank"><img
                                            src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-fb.png') ?>"/></a>
                        <?php endif; ?>
                        <?php if (!get_option('fotov-voting-noshow-tw', false)): ?>
                                <a href="#" onclick="return sv_vote_send('tw', this,<?php echo $contestant->id ?>)"
                                   target="_blank"><img
                                            src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-tw.png') ?>"/></a>
                        <?php endif; ?>
                        <?php if (!get_option('fotov-voting-noshow-ok', false)): ?>
                                <a href="#" onclick="return sv_vote_send('ok', this,<?php echo $contestant->id ?>)"
                                   target="_blank"><img
                                            src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-ok.png') ?>"/></a>
                        <?php endif; ?>
                        <?php if (!get_option('fotov-voting-noshow-gp', false)): ?>
                                <a href="#" onclick="return sv_vote_send('gp', this,<?php echo $contestant->id ?>)"
                                   target="_blank"><img
                                            src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-gp.png') ?>"/></a>
                        <?php endif; ?>
                        <?php if (!get_option('fotov-voting-noshow-pi', false)): ?>
                                <a href="#" onclick="return sv_vote_send('pi', this,<?php echo $contestant->id ?>)"
                                   target="_blank"><img
                                            src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-pi.png') ?>"/></a>
                        <?php endif; ?>
                        <img class="snegovik"
                             src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/snegovik.png') ?>"/>
                </div>
        </div>
        <div class="fv_description"><?php echo $contestant->description ?></div>

        <div id="fv_most_voted" class="theme">
                <span class="title"><span><?php echo $public_translated_messages['other_title'] ?></span></span>
                <?php
                    include 'leaders.php';
                ?>
        </div>

        <div style="clear: both;"></div>
        <br/>

</div>