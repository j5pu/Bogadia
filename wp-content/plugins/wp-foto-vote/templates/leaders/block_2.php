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
 * $thumb_size
 * */

$block_leaders_width = round(100/count($most_voted), 2) . '%';

$active = 0;
// Move most voted item to 2 place, at it will be it middle
if (count($most_voted) > 2) {
    $most_voted[999] = $most_voted[0];
    $most_voted[0] = $most_voted[1];
    $most_voted[1] = $most_voted[999];
    unset($most_voted[999]);
    $active = 1;
}

$title_bg = FvFunctions::ss('lead-primary-bg', '#e6e6e6', 7);
$img_border_radius = FvFunctions::ss('lead-thumb-round', 0);

?>
<style>
    .fv-leaders-block2--item{
        border-color:<?php echo FvFunctions::ss('lead-primary-color', '#f7941d', 7); ?>!important;
        background:<?php echo FvFunctions::ss('lead-primary-bg', '#e6e6e6', 7); ?>!important;
    }
    .fv-leaders-block2--item-title{color:<?php echo FvFunctions::ss('lead-primary-color', '#f7941d', 7); ?>!important;}
    <?php if ( $img_border_radius > 0 && $img_border_radius <= 50 ): ?>
        img.fv-leaders--image{border-radius:<?php echo $img_border_radius; ?>%!important;}
    <?php endif; ?>
</style>

<div class="fv-leaders fv-leaders-block2">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo $title; ?></span>
        </span>
    <?php endif; ?>
    <div class="fv-leaders-block2--container fv-leaders-block2--container-<?php echo count($most_voted); ?>">
        <?php $i = 0; foreach ($most_voted as $key => $photo):
            $thumb = FvFunctions::getPhotoThumbnailArr($photo, $thumb_size);
            $link = ( !empty($page_url) ) ? $page_url . '=' . $photo->id: '#photo-' . $photo->id;
            ?>
            <div class="fv-leaders-block2--item <?php echo ($i == $active)? 'fv-leaders-block2--item-tall':''; ?>"  style="width:<?php echo $block_leaders_width; ?>;">
                <div class="fv-leaders-block2--item-title"><?php echo $photo->name; ?></div>
                <div class="fv-leaders-block2--image-wrap">
                    <a href="<?php echo $link; ?>"><img class="fv-leaders--image" src="<?php echo $thumb[0]; ?>" alt="<?php echo $photo->name; ?>"  width="<?php echo $thumb[1]; ?>"/></a>
                </div>
                <div class="fv-leaders-block2--item-votes"><i class="fvicon-heart3"></i> <?php echo $photo->votes_count; ?></div>
            </div>
        <?php $i++; endforeach; ?>
    </div>
</div>
