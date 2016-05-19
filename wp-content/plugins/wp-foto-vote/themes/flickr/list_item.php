<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * $photo - PHOTO (object)
 * $id - PHOTO ID (int)
 * $thumbnail - PHOTO THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $image_full - PHOTO FULL SRC (string)
 * $name - PHOTO NAME (string - max 255)
 * $description - PHOTO DESCRIPTION (string - max 255)
 * $photo->full_description - PHOTO FULL DESCRIPTION (string - max 500)
 * DEPRECATED $additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code> * $votes - PHOTO VOTES COUNT (int)
* $upload_info - json decoded Upload form fields*
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
*
*** OTHER ***
 * $leaders - is this leaders block? (bool)
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $page_url - PAGE URL (string)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $hide_votes - NEED HIDE VOTES? (bool)
 */

?>

<div class="photo-display-item item contest-block" style="" data-h="<?php echo $thumbnail[2]; ?>" data-w="<?php echo $thumbnail[1]; ?>">

    <div class="thumb">
        <div class="photo_container pc_ju">
            <a name="<?php echo (!$leaders) ? 'photo-' . $id : ''; ?>" data-id="<?php echo $id; ?>"
               class="<?php if (!fv_photo_in_new_page($theme)): ?>fv_lightbox nolightbox no-lightbox noLightbox<?php endif; ?>" rel="fw"
               title="<?php echo htmlspecialchars(stripslashes($name)); ?>" data-title="<?php echo $data_title ?>"
               href="<?php echo $image_full ?>">
                <?php
                    if ( FvFunctions::lazyLoadEnabled($theme) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
                        printf('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                 data-lazy-src="%s" height="%s" class="pc_img fv-lazy" alt="%s"/>', $thumbnail[0], $thumbnail[2], htmlspecialchars(stripslashes($name)));
                    }
                    else {
                        printf('<img src="%s" height="%s" class="pc_img" alt="%s"/>', $thumbnail[0], $thumbnail[2], htmlspecialchars(stripslashes($name)));
                    }
                    //<img src="<?php echo $thumbnail[0]; ? >" alt="< ?php echo stripslashes($name) ? >" class="" border="0">
                ?>
            </a>
        </div>

        <div class="meta">
            <div class="title">
                <a href="#0" onclick="jQuery('a[name=photo-<?php echo $id ?>]')[0].click(); return false;" class="title">
                    <?php echo substr($additional, 0, 80) ?>
                </a>
            </div>

            <div class="attribution-block">
                <span class="attribution">
                    <span><?php echo mb_substr($name, 0, 35, 'UTF-8') ?> </span>
                </span>
            </div>

            <span class="inline-icons">
                <?php if( FvFunctions::ss('soc-counter', false) ): ?>
                    <i class="fvicon-share"></i> <span class="fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
                <?php endif; ?>

                <a data-track="favorite" href="#0" class="fave-star-inline canfave fv_vote" <?php if ($konurs_enabled): ?> onclick="sv_vote(<?php echo $id ?>); return false;" <?php endif; ?>>
                    <snap class="fvicon fvicon-star3"></snap>
                    <?php if( $hide_votes == false ): ?>
                        <span class="fave-count count sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                    <?php endif; ?>
                </a>
                <a href="#0" onclick="jQuery('a[name=photo-<?php echo $id ?>]')[0].click(); return false;" class="lightbox-inline">
                    <span class="fvicon-expand2"></span>
                </a>
            </span>
        </div>
    </div>
</div>