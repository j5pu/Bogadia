<!-- This file is used to markup the public-facing widget. -->
<?php
//var_dump ($r);
//var_dump ( $r->have_posts() );
if ($r) :
	$contest_link = fv_generate_contestant_link($contest_id, $link);

    $thumb_size = array(
        'width' => $show_photo_size,
        'height' => $show_photo_size,
        'crop' => true,
    );

    $output = '';
	if ($title)
		$output .= $before_title . $title . $after_title;
	$output .= '<ul class="contestant-list">';
	foreach ($r as $contestant) :
		//$r->the_post();
		if ($link) {
			$photo_url = $contest_link . '=' . $contestant->id;
		} else {
			$photo_url = '';
		}
		$post_content_class = 'contestant-content';
		if ($show_photo) {
            $img_src = FvFunctions::getPhotoThumbnailArr($contestant, $thumb_size);
			$thumbnail = $img_src[0];
			$thumblink = sprintf('<div class="contestant-thumb"><a href="%1$s" title="%3$s"><img src="%3$s" alt="%2$s" title="%2$s" width="%4$d"/></a></div>', $photo_url, __('View', 'fv'), $thumbnail, $show_photo_size);
		} else {
			$thumblink = '';
			$post_content_class .= ' no-image';
		}
		$format = '<li>%1$s<div class="%2$s"><h4><a href="%3$s" title="%4$s">%4$s</a></h4><small>%5$s: %6$s</small></div></li>';
		$output.= sprintf($format, $thumblink, $post_content_class, $photo_url, $contestant->name, __('Votes', 'fv'), $contestant->votes_count);
	endforeach;
	$output .= '</ul>';

endif;
echo $output;
