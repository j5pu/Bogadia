<?php
    defined('ABSPATH') or die("No script kiddies please!");
?>
<div class="fv_leaders"><?php
    //$orders = fv_get_sotring_types_arr();
    $selected_order = array_rand( fv_get_sotring_types_arr() );
    $related_photos = ModelCompetitors::query()
        ->where_all(array('contest_id'=> $contestant->contest_id, 'status'=> ST_PUBLISHED))
        ->limit( get_option('fotov-leaders-count', 3) )
        ->sort_by('RAND()')
        ->find(false, false, true);

    //fv_new_year_most_voted($contestant->contest_id);
    $thumbnails_size = array(
        'width'=>FvFunctions::ss('lead-thumb-width', 280),
        'height'=>FvFunctions::ss('lead-thumb-height', 200),
        'crop'=>FvFunctions::ss('lead-thumb-crop', true),
    );
    
    $i = 1;     
    foreach ($related_photos as $key => $photo):
        $thumbnail = FvFunctions::getPhotoThumbnailArr($photo, $thumbnails_size);

        $image_full = fv_generate_contestant_link( $contestant->id, get_permalink() );
    
        $style = '';
        if ( get_option('fotov-leaders-count', 3) == 4 ) {
            $style ='style="width: 18%;"';
        }
	
	?>
		<div class="fv_constest_item contest-block" <?php echo $style; ?>>
			 <div class="fv_photo">
                 <?php if( $hide_votes == false ): ?>
                     <div class="fv_photo_votes">
                         <i class="fvicon-heart3"></i><span class="fv-votes sv_votes_<?php echo $photo->id ?>"><?php echo $photo->votes_count ?></span>
                     </div>
                 <?php endif; ?>

				  <a href="<?php echo $image_full . '=' . $photo->id; ?>" title="<?php echo htmlspecialchars(stripslashes($photo->name)) ?>">
                    <img src="<?php echo $thumbnail[0] ?>" class="attachment-thumbnail"/>
				  </a>
			 </div>

			 <div class="fv_name">
				  <span><?php echo $photo->name ?></span>
			 </div>

		</div>
<?php   
        $i++; 
    endforeach; 
?>
</div>