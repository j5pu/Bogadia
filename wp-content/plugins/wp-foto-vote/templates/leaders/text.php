<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *
 * $hide_title - $bool
 * $title - Leaders title
 *
 * $most_voted - array() with photos
 * $variables -
 *
 * $contest_id
 * $contest_enabled
 * $page_url
 * $leaders_width
 *
 */
?>

<div class="fv-most-voted text">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
            <?php echo $title; ?></span>
        </span>
    <?php endif; ?>

    <?php $i = 1; foreach ($most_voted as $key => $unit): ?>
        <a href="#photo-<?php echo $key ?>"><strong><?php echo $unit->name ?></strong></a>
        <span id="fv-most-voted-<?php echo $key ?>"> <?php echo $unit->votes_count ?></span>
        <?php if (($i != count($most_voted))) echo ', ' ?>
    <?php $i++; endforeach; ?>
</div>
