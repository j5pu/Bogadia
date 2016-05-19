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

$img_border_radius = FvFunctions::ss('lead-thumb-round', 0);
?>

<style>
    .fv-most-voted-table2 thead{background:<?php echo FvFunctions::ss('lead-primary-bg', '#e6e6e6', 7); ?>!important;}
    .fv-most-voted-table2{color:<?php echo FvFunctions::ss('lead-primary-color', '#f7941d', 7); ?>!important;}
    <?php if ( $img_border_radius > 0 && $img_border_radius <= 50 ): ?>
        img.fv-leaders--image{border-radius:<?php echo $img_border_radius; ?>%!important;}
    <?php endif; ?>
</style>

<div class="fv-leaders">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
                <?php echo $title; ?></span>
            </span>
    <?php endif; ?>

    <table class="fv-most-voted-table2">
        <thead>
        <tr>
            <th><?php echo fv_get_transl_msg('lead_table_rank'); ?></th>
            <th><?php echo fv_get_transl_msg('lead_table_photo'); ?></th>
            <th><?php echo fv_get_transl_msg('lead_table_votes'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($most_voted as $key => $photo):
            $thumb = FvFunctions::getPhotoThumbnailArr($photo, $thumb_size);
            $link = ( !empty($page_url) ) ? $page_url . '=' . $photo->id: '#photo-' . $photo->id;
            ?>
            <tr>
                <td class="fv-most-voted-table2--img-td">
                    <a href="<?php echo $link; ?>" class="fv-most-voted-table2--a">
                        <img class="fv-leaders--image" src="<?php echo $thumb[0]; ?>" alt="<?php echo stripslashes($photo->name); ?>" width="<?php echo $thumb[1]; ?>">
                    </a>
                </td>
                <td  class="fv-most-voted-table2--title-td"><?php echo stripslashes($photo->name); ?></td>
                <td><?php echo $photo->votes_count; ?></td>
            </tr>
            <?php
            $i++;
        endforeach; ?>
        </tbody>
    </table>
</div>