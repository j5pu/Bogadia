<?php
?>

<div id="fv_constest_item">
    <div class="fv_name">
        <span><?php echo $contestant->name ?> 123 вава dfds</span>
    </div>
    <div class="fv_photo">
        <div class="fv_photo_votes"></div>
        <img src="<?php echo $image[0] ?>" />
    </div>
    <div class="fv_social">
        <span><?php _e('Share to friends','fv') ?></span>
        <div class="fv_social_icons">
            <?php if (!get_option('fotov-voting-noshow-vk', false)): ?>
                <a href="#" onclick="return sv_vote_send('vk', this)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-vk.png') ?>" /></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-fb', false)): ?>
                <a href="#" onclick="return sv_vote_send('fb', this)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-fb.png') ?>" /></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-tw', false)): ?>
                <a href="#" onclick="return sv_vote_send('tw', this)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-tw.png') ?>" /></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-ok', false)): ?>
                <a href="#" onclick="return sv_vote_send('ok', this)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-ok.png') ?>" /></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-gp', false)): ?>
                <a href="#" onclick="return sv_vote_send('gp', this)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-gp.png') ?>" /></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-pi', false)): ?>
                <a href="#" onclick="return sv_vote_send('pi', this)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-pi.png') ?>" /></a>
            <?php endif; ?>     
        </div>
    </div>
    <div class="fv_description"></div>
</div>