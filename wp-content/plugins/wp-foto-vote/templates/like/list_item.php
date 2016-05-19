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
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
*** OTHER ***
 * $leaders - is this leaders block? (bool)
 * $fv_block_width - contest block width (int)
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $page_url - PAGE URL (string)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 * $upload_info - json decoded Upload form fields
 * $hide_votes - NEED HIDE VOTES? (bool)
 */
?>

<div class="contest-block <?php echo (!$konurs_enabled)? 'ended': ''; ?>" style="width: <?php echo get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH) ; ?>px;">

    <div class="spinner">
        <span class="fvicon-spinner icon rotate-animation"></span>
    </div>
    <?php
    if ( $leaders ) {
        printf('<img src="%s" class="attachment-thumbnail" />', $thumbnail[0]);
    } else {
        if ( FvFunctions::lazyLoadEnabled($theme) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
            printf('<img src="" data-lazy-src="%s" width="%s" class="attachment-thumbnail fv-lazy"/>', $thumbnail[0], $thumbnail[1]);
        } else {
            printf('<img src="%s" width="%s" class="attachment-thumbnail"/>', $thumbnail[0], $thumbnail[1]);
        }
    }
    ?>

    <div class="vote-heart fv_button fv_vote" onclick="sv_vote(<?php echo $id ?>, 'vote', this);">
        <span class="fvicon-heart"></span>
        <?php if( $hide_votes == false ): ?>
            <span class="sv_votes sv_votes_<?php echo $id ?><?php echo (!$konurs_enabled)? ' ended': ''; ?>"><?php echo $votes ?></span>
        <?php endif; ?>
    </div>

    <?php if( FvFunctions::ss('soc-counter', false) ): ?>
        <a href="#0" class="fv-share-counter" onclick="FvModal.goShare(<?php echo $id ?>); return false;" >
            <i class="fvicon-share"></i> <span class="fv_svotes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
        </a>
    <?php endif; ?>

    <a name="photo-<?php echo $id ?>"  data-id="<?php echo $id; ?>" class="fv_lightbox no-lightbox nolightbox noLightbox" rel="fw" href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars(stripslashes($name)) ?>" data-title="<?php echo $data_title ?>">
        <span class="fvicon-expand"></span>
    </a>

</div>