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
 * $description - PHOTO DESCRIPTION (string - max 500)
 * $photo->full_description - PHOTO FULL DESCRIPTION (string - max 1255)
 * DEPRECATED $additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code>
 * $votes - PHOTO VOTES COUNT (int)
 * $upload_info - json decoded Upload form fields*
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
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

$rel = apply_filters('fv/public/theme/list_item/rel', 'fw', $photo);
?>


<div class="contest-block clg-item is-gallery" style="width: <?php echo (!$leaders) ? $fv_block_width . 'px' : $fv_block_width . '%'; ?>;">
        <div class="clg-item-head" style="width: <?php echo $thumbnail[1]; ?>px; height: <?php echo $thumbnail[2]; ?>px;">
                <a name="<?php echo (!$leaders) ? 'photo-' . $id : ''; ?>" data-id="<?php echo $id; ?>" class="<?php if (!fv_photo_in_new_page($theme)): ?> fv_lightbox nolightbox no-lightbox noLightbox<?php endif; ?>" rel="<?php echo $rel; ?>" href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars(stripslashes($name)) ?>" data-title="<?php echo $data_title ?>">
                    <?php
                    if ( $leaders ) {
                        printf('<img src="%s" class="clg-cover-image" /> alt="%s"', $thumbnail[0], htmlspecialchars(stripslashes($name)));
                    } else {
                        if ( FvFunctions::lazyLoadEnabled($theme) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
                            printf('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
                                data-lazy-src="%s" width="%s" height="%s" class="clg-cover-image fv-lazy" alt="%s"/>', $thumbnail[0], $thumbnail[1], $thumbnail[2], htmlspecialchars(stripslashes($name)));
                        } else {
                            printf('<img src="%s" width="%s" height="%s" class="clg-cover-image" alt="%s"/>', $thumbnail[0], $thumbnail[1], $thumbnail[2], htmlspecialchars(stripslashes($name)));
                        }
                    }
                    // <img class="clg-cover-image" src="<?php echo $thumbnail[0]; >" alt=" echo htmlspecialchars(stripslashes($name)) ? >" width="< ?php echo $thumbnail[1]; ? >">
                    ?>
                </a>

                <div class="clg-head-overlay" style="opacity: 1; display: none;">
                        <a href="#" onclick="$jQ('a[name=photo-<?php echo $id ?>]').click(); return false;" class="fvicon clg-head-view"></a>

                        <div class="clg-head-social">
                            <?php if ($konurs_enabled): ?>
                                <span class="clg-like-button fv_vote fvicon-heart2" onclick="sv_vote(<?php echo $id ?>); return false;" title="<?php echo $public_translated_messages['vote_button_text']; ?>"></span>
                            <?php endif; ?>
                                <span class="clg-facebook-share fvicon-facebook" onclick="return sv_vote_send('fb', this ,<?php echo $id ?>);" ></span>
                                <span class="clg-share fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>);" ></span>
                                <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
                        </div>
                </div>
        </div>
        <?php do_action('fv/public/punterest_theme/list_item/extra', $photo); ?>
        <div class="clg-item-info">
                <div class="clg-body-social">
                    <span class="clg-like-button fvicon-heart2" onclick="sv_vote(<?php echo $id ?>); return false;" title="<?php echo $public_translated_messages['vote_button_text']; ?>"></span>
                    <span class="clg-facebook-share fvicon-facebook" onclick="return sv_vote_send('fb', this ,<?php echo $id ?>);" ></span>
                    <span class="clg-share fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>);" ></span>
                    <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
                </div>

                <div>

                    <p class="clg-info-social clg-info-row">
                        <?php if( FvFunctions::ss('soc-counter', false) ): ?>
                            <i class="fvicon-share"></i><span class="fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
                        <?php endif; ?>

                        <?php if( $hide_votes == false ): ?>
                            <span class="clg-info-likes fvicon- sv_votes_<?php echo $id ?>" title="<?php echo $votes ?>"><?php echo $votes ?></span>
                        <?php endif; ?>
                    </p>


                    <div class="clg-info-title sv_title"><?php echo mb_substr($name, 0, 30, 'UTF-8') ?></div>
                </div>
                <p class="clg-info-row">
                        <span class="clg-by">
                                <?php echo $additional; ?>
                        </span>
                </p>
        </div>
</div>