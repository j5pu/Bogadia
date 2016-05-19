<div id="fv_constest_item" class="contest-block">
	<div class="fv_name">
		<span><?php echo $contestant->name ?></span>
	</div>
	<div class="fv_photo" style="width: <?php echo $image[1] + 10 ?>px;">

		<img src="<?php echo $image[0] ?>" />

		<?php if (!empty($next_id)): ?>
			<div class="fv_next fv_nav">
				<a href="<?php echo $page_url . '=' . $next_id ?>" title="<?php _e('Next', 'fv') ?>"><span class="fvicon-arrow-right"></span></a>
			</div>
		<?php endif; ?>

		<?php if (!empty($prev_id)): ?>
			<div class="fv_prev fv_nav">
				<a href="<?php echo $page_url . '=' . $prev_id ?>" title="<?php _e('Previous', 'fv') ?>"><span class="fvicon-arrow-left"></span></a>
			</div>
		<?php endif; ?>
		
		<div style="clear: both;"></div>
        <?php if( $hide_votes == false ): ?>
            <div class="fv_photo_votes">
                <?php echo $public_translated_messages['vote_count_text']; ?>:
                <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->votes_count ?></span>
            </div>
        <?php endif; ?>

		<?php if ($konurs_enabled): ?>
			<div class="fv_button"><input type="button" class="fv_vote" value="<?php echo $public_translated_messages['vote_button_text']; ?>" onclick="sv_vote(<?php echo $contestant->id?>)" /></div>
		<?php endif; ?>

        <?php if( FvFunctions::ss('soc-counter', false) ): ?>
            <a href="#0" class="fv-small-action-btn fvicon-share" onclick="FvModal.goShare(<?php echo $contestant->id ?>); return false;" >
                <span class="fv-soc-votes fv_svotes_<?php echo $contestant->id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
            </a>
        <?php endif; ?>
		
	</div>
		
	<div class="fv_social">
		<span><?php _e('Share to friends', 'fv') ?></span>
		<div class="fv_social_icons">
			<?php if (!get_option('fotov-voting-noshow-vk', false)): ?>
				<a href="#0" onclick="return sv_vote_send('vk', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-vk.png') ?>" /></a>
			<?php endif; ?>
			<?php if (!get_option('fotov-voting-noshow-fb', false)): ?>
				<a href="#0" onclick="return sv_vote_send('fb', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-fb.png') ?>" /></a>
			<?php endif; ?>
			<?php if (!get_option('fotov-voting-noshow-tw', false)): ?>
				<a href="#0" onclick="return sv_vote_send('tw', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-tw.png') ?>" /></a>
			<?php endif; ?>
			<?php if (!get_option('fotov-voting-noshow-ok', false)): ?>
				<a href="#0" onclick="return sv_vote_send('ok', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-ok.png') ?>" /></a>
			<?php endif; ?>
			<?php if (!get_option('fotov-voting-noshow-gp', false)): ?>
				<a href="#0" onclick="return sv_vote_send('gp', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-gp.png') ?>" /></a>
			<?php endif; ?>
			<?php if (!get_option('fotov-voting-noshow-pi', false)): ?>
				<a href="#0" onclick="return sv_vote_send('pi', this,<?php echo $contestant->id ?>)" target="_blank"><img src="<?php echo plugins_url('wp-foto-vote/themes/new_year/img/soc-pi.png') ?>" /></a>
			<?php endif; ?>     
		</div>
	</div>

	<div class="fv_description"><?php echo $contestant->description ?></div>
</div>