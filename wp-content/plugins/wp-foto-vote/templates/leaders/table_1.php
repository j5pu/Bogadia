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
?>

<style>
    .fv-most-voted-table thead{background:<?php echo FvFunctions::ss('lead-primary-bg', '#e6e6e6', 7); ?>!important;}
    .fv-most-voted-table .fv-most-voted-table--a{color:<?php echo FvFunctions::ss('lead-primary-text', '#f7941d', 7); ?>!important;}
    img.fv-leaders--image{border-radius:<?php echo FvFunctions::ss('lead-thumb-round', 0); ?>%!important;}
</style>

<div class="fv-leaders">
    <?php if ( !isset($hide_title) || $hide_title !== true ): ?>
        <span class="title"><span>
                <?php echo $title; ?></span>
            </span>
    <?php endif; ?>
    <table class="table table-striped table-hover fv-most-voted-table">
        <thead>
        <tr>
            <th><i class="fvicon-star"></i> <?php echo fv_get_transl_msg('lead_table_rank'); ?></th>
            <th><i class="fvicon-image"></i> <?php echo fv_get_transl_msg('lead_table_photo'); ?></th>
            <th><i class="fvicon-heart2"></i> <?php echo fv_get_transl_msg('lead_table_votes'); ?></th>
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
                <td class="fv-most-voted-table--rank"><span><?php echo $i; ?></span></td>
                <td class="fv-most-voted-table--contestant">
                    <span>
                        <a href="<?php echo $link; ?>" class="fv-most-voted-table--a">
                            <img class="fv-leaders--image" src="<?php echo $thumb[0]; ?>" alt="<?php echo stripslashes($photo->name); ?>" width="<?php echo $thumb[1]; ?>">
                        </a>
                        <div class="fv-most-voted-table--info">
                            <a href="<?php echo $link; ?>" class="trans"><?php echo stripslashes($photo->name); ?></a>
                            <strong class="fv-most-voted-table--info-title"><?php echo stripslashes($photo->name); ?></strong>
                            <span>#<?php echo $photo->id; ?></span>
                            <span><?php echo fv_get_transl_msg('vote_count_text'), ': ', $photo->votes_count; ?></span>
                        </div>
                    </span>
                </td>
                <td class="fv-most-voted-table--votes-td"><span><?php echo $photo->votes_count; ?></span></td>
            </tr>
        <?php
            $i++;
        endforeach; ?>
        </tbody>
    </table>
</div>