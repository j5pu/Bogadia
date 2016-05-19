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

<div class="contest-block" style="width: <?php echo ( !$leaders )? $fv_block_width . 'px' : $fv_block_width . '%' ; ?>;">
    <a name="photo-<?php echo $id?>" ></a>
    <div>
        <div class="contest-block--img-wrap">
            <a name="photo-<?php echo $id?>"  data-id="<?php echo $id; ?>" class="<?php if( !fv_photo_in_new_page($theme) ): ?>fv_lightbox nolightbox no-lightbox noLightbox<?php endif; ?>" rel="fw" href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars(stripslashes($name)) ?>" data-title="<?php echo $data_title ?>" style="cursor: pointer;">
                <?php
                if ( $leaders ) {
                    printf('<img src="%s" class="attachment-thumbnail" />', $thumbnail[0]);
                } else {
                    if ( FvFunctions::lazyLoadEnabled($theme) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
                        printf('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mO4d/fufwAIzQOYASGzMgAAAABJRU5ErkJggg=="
                                data-lazy-src="%s" width="%s" height="%s" class="attachment-thumbnail fv-lazy" alt="%s"/>', $thumbnail[0], $thumbnail[1], $thumbnail[2], htmlspecialchars(stripslashes($name)));
                    } else {
                        printf('<img src="%s" width="%s" height="%s" class="attachment-thumbnail" alt="%s"/>', $thumbnail[0], $thumbnail[1], $thumbnail[2], htmlspecialchars(stripslashes($name)));
                    }
                }
                ?>
            </a>
        </div>
        <div class="sv_title">
            <div class="clearfix"></div>
            <div class="sv_name"><?php echo mb_substr($name, 0, 30, 'UTF-8') ?></div>
            <?php if( $hide_votes == false ): ?>
                <div class="sv_votes"> <span class="sv_votes_<?php echo $id?>" title="<?php echo $public_translated_messages['vote_count_text']; ?>: "> <?php echo $votes ?> </span> </div>
            <?php endif; ?>
            <div style="clear:both;"> </div>
        </div>
    </div>
    <div class="clearfix"></div>

	<div class="fv_button">
        <?php if ($konurs_enabled): ?>
            <button class="fv_vote" onclick="sv_vote(<?php echo $id?>)">
                <?php echo $public_translated_messages['vote_button_text']; ?>
            </button>
        <?php endif; ?>
        <a href="#0" class="fv-small-action-btn fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>); return false;" >
            <?php if( FvFunctions::ss('soc-counter', false) ): ?>
                <span class="fv-soc-votes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
            <?php endif; ?>
        </a>
    </div>

</div>