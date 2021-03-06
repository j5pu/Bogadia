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
 * DEPRECATED $additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code>
 * $votes - PHOTO VOTES COUNT (int)
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
 * $data_title - title for lightbox link, must be used as <a data-title="<?php echo $data_title ?>" href="##">##</a>
 */
?>
<div class="sv_unit contest-block" style="width: <?php echo ( !$leaders )? $fv_block_width . 'px' : $fv_block_width . '%' ; ?>;">
	<div align="center">
            <a name="<?php echo ( !$leaders )? 'photo-'.$id : ''; ?>" data-id="<?php echo $id; ?>" class="<?php if( !fv_photo_in_new_page($theme) ): ?> fv_lightbox nolightbox <?php endif; ?>" rel="fw" href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars(stripslashes($name)); ?>" data-title="<?php echo $data_title ?>">
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
	<div class="contest-block-title"><strong><?php echo $name; ?></strong></div>
	<div class="contest-block-description"><em><?php echo $additional; ?></em></div>
        <?php if ( !$leaders ): ?>
            <div class="contest-block-votes">
                <?php if( $hide_votes == false ): ?>
                    <?php echo $public_translated_messages['vote_count_text']; ?>:
                    &nbsp;<span class="contest-block-votes-count sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                <?php endif; ?>
                <a href="#" class="fv-small-action-btn fvicon-share" onclick="FvModal.goShare(<?php echo $id ?>); return false;" >
                    <?php if( FvFunctions::ss('soc-counter', false) ): ?>
                        <span class="fv-soc-votes fv_svotes_<?php echo $id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
                    <?php endif; ?>
                </a>
                <?php do_action('fv/contest_list_item/actions_hook', $photo, $konurs_enabled, $theme); ?>
            </div>

            <div class="fv-share-btns text-center">
                <?php if ( !FvFunctions::ss('voting-noshow-fb') ): ?>
                    <a class="fv-share-btn ss_fb" href="#0" onclick="return sv_vote_send('fb', this,<?php echo $id ?>);"><i class="fvicon-facebook"></i></a>
                <?php endif; ?>
                <?php if ( !FvFunctions::ss('voting-noshow-tw') ): ?>
                    <a class="fv-share-btn ss_tw" href="#0" onclick="return sv_vote_send('tw', this,<?php echo $id ?>);"><i class="fvicon-twitter"></i></a>
                <?php endif; ?>
                <?php if ( !FvFunctions::ss('voting-noshow-gp') ): ?>
                    <a class="fv-share-btn ss_gp" href="#0" onclick="return sv_vote_send('gp', this,<?php echo $id ?>);"><i class="fvicon-googleplus"></i></a>
                <?php endif; ?>
                <?php if ( !FvFunctions::ss('voting-noshow-pi') ): ?>
                    <a class="fv-share-btn ss_pi" href="#0" onclick="return sv_vote_send('pi', this,<?php echo $id ?>);"><i class="fvicon-pinterest3"></i></a>
                <?php endif; ?>
                <?php if ( !FvFunctions::ss('voting-noshow-vk') ): ?>
                    <a class="fv-share-btn ss_vk" href="#0" onclick="return sv_vote_send('vk', this,<?php echo $id ?>);"><i class="fvicon-vk3"></i></a>
                <?php endif; ?>
            </div>

            <?php if ($konurs_enabled): ?>
			<div class="fv_button text-center">
                    <button class="fv_vote" onclick="sv_vote(<?php echo $id ?>)"><i class="hand"><?php echo $public_translated_messages['vote_button_text']; ?></i></button>
                </div>    
            <?php endif; ?>         
        <?php endif; ?>         
</div>