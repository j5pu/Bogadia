<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>
<div class="fv_leaders">
<?php
    $most_voted = fv_new_year_most_voted($contestant->contest_id);
    $i = 1; foreach ($most_voted as $key => $unit):
	$thumbnail = FvFunctions::getPhotoThumbnailArr($unit, "medium");
    //wp_get_attachment_image_src($unit->image_id, "medium");
	if ( !get_option('fotov-photo-in-new-page', false) ) {
		 $image_full = $unit->url;
	} else {
		 $image_full = add_query_arg('contest_id', $contest_id,$page_url . '=' . $unit->id);
	}
	$style = '';
	if ( get_option('fotov-leaders-count', 3) == 4 ) {
		$style ='style="width: 18%;"';
	}
	
	?>
		<div class="fv_constest_item contest-block" <?php echo $style; ?>>
			 <div class="fv_photo">
                 <?php if( $hide_votes == false ): ?>
                     <div class="fv_photo_votes">
                         <i class="fvicon-heart3"></i><span class="fv-votes sv_votes_<?php echo $unit->id ?>"><?php echo $unit->votes_count ?></span>
                     </div>
                 <?php endif; ?>

				  <a name="<?php echo 'photo-'.$unit->id; ?>" class="<?php if( !get_option('fotov-photo-in-new-page', false) ): ?> fv_lightbox nolightbox <?php endif; ?>" rel="fw"
					  href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars(stripslashes($unit->name)) ?>" style="cursor: pointer;">
						<img src="<?php echo $thumbnail[0] ?>" />
				  </a>
			 </div>

			 <div class="fv_name">
				  <span><?php echo $unit->name ?></span>
			 </div>

		</div>

<?php $i++; endforeach; ?>
</div>