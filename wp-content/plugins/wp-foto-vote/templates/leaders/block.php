<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 * $most_voted - array() with photos
 * $variables -
 *
 * $contest_id
 * $contest_enabled
 * $page_url
 * $leaders_width
 *
 * thumb_size
 * */

if ( isset($thumb_size['width']) ) {
    $block_leaders_width = (int)$thumb_size['width'] . 'px';
} else {
    $block_leaders_width = round(95/count($most_voted)) . '%';
}

?>
<style>
    img.fv-leaders--image{border-radius:<?php echo FvFunctions::ss('lead-thumb-round', 0); ?>%!important;}
    .fv-leaders--details{
        background-color:<?php echo FvFunctions::ss('lead-primary-bg', '#e6e6e6', 7); ?>!important;
        color:<?php echo FvFunctions::ss('lead-primary-color', '#e6e6e6', 7); ?>!important;
    }
</style>

<div class="fv-leaders block">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo $title; ?></span>
        </span>
    <?php endif; ?>

    <div class="fv-leaders--container">
        <?php $i = 1; foreach ($most_voted as $key => $photo):
            $thumb = FvFunctions::getPhotoThumbnailArr($photo, $thumb_size);
            $link = ( !empty($page_url) ) ? $page_url . '=' . $photo->id: '#photo-' . $photo->id;
            ?>
            <div class="fv-leaders--item" style="width:<?php echo $block_leaders_width; ?>;">
                <div class="fv-leaders--image-wrap">
                    <a href="<?php echo $link; ?>"><img class="fv-leaders--image" src="<?php echo $thumb[0]; ?>" alt="<?php echo $photo->name; ?>"/></a>
                </div>

                <div class="fv-leaders--details">
                    <div class="fv-leaders--votes"><i class="fvicon-heart3"></i> <?php echo $photo->votes_count; ?></div>
                    <div class="fv-leaders--name"><?php echo $photo->name; ?></div>
                </div>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>
