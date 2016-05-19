<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
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
 */

$theme_imgs_url = FvFunctions::get_theme_url($theme, 'img/');

?>

<div class="fv_constest_item contest-block" style="width: <?php echo $fv_block_width . 'px'; ?>;">
    <div class="fv_photo" style="width: <?php echo $thumbnail[1] ?>px;">
        <div class="fv_photo_votes">
            <?php if ($konurs_enabled): ?>  
                <a href="#" class="fv_vote"  onclick="sv_vote(<?php echo $id ?>); return false;">
                    <i class="fvicon-heart3"></i>
                    <?php if( $hide_votes == false ): ?>
                        <span class="fv-votes sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <i class="fvicon-heart3"></i> <span class="fv-votes sv_votes_<?php echo $id ?>"><?php echo $votes ?></span>
            <?php endif; ?>               
        </div>
        <?php if( FvFunctions::ss('soc-counter', false) ): ?>
            <div class="fv-svotes-container">
                <a href="#0" class="fv-svotes-a" onclick="FvModal.goShare(<?php echo $id ?>); return false;" title="<?php echo $public_translated_messages['shares_count_text']; ?>">
                    <i class="fvicon-share"></i> <span class="fv-soc-votes fv_svotes_<?php echo $id ?>">0</span>
                </a>
            </div>
        <?php endif; ?>
		 <a name="<?php echo 'photo-'.$id; ?>" data-id="<?php echo $id; ?>"  class="fv_lightbox nolightbox noLightbox" rel="fw" href="<?php echo $image_full ?>" title="<?php echo htmlspecialchars(stripslashes($name)) ?>" data-title="<?php echo $data_title ?>">
             <?php
             if ( FvFunctions::lazyLoadEnabled($theme) && !(defined('DOING_AJAX') && DOING_AJAX) ) {
                printf('<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mO4d/fufwAIzQOYASGzMgAAAABJRU5ErkJggg=="
                    data-lazy-src="%s" width="%s" height="%s" class="attachment-thumbnail fv-lazy" alt="%s"/>', $thumbnail[0], $thumbnail[1], $thumbnail[2], htmlspecialchars(stripslashes($name)));
             } else {
                printf('<img src="%s" width="%s" height="%s" class="attachment-thumbnail" alt="%s"/>', $thumbnail[0], $thumbnail[1], $thumbnail[2], htmlspecialchars(stripslashes($name)));
             }
             ?>
        </a>
    </div>
    
    <div class="fv_name">
        <span><?php echo $name ?></span>
    </div>
    
    <div class="fv_description"><?php echo $additional ?></div>    
    
    <div class="fv_social">
        <div class="fv_social_icons">
            <?php if (!get_option('fotov-voting-noshow-vk', false)): ?>
                <a href="#" onclick="return sv_vote_send('vk', this ,<?php echo $id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-vk.png" alt="Share in VK"/></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-fb', false)): ?>
                <a href="#" onclick="return sv_vote_send('fb', this,<?php echo $id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-fb.png" alt="Share in Facebook"/></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-tw', false)): ?>
                <a href="#" onclick="return sv_vote_send('tw', this,<?php echo $id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-tw.png" alt="Share in Twitter"/></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-ok', false)): ?>
                <a href="#" onclick="return sv_vote_send('ok', this,<?php echo $id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-ok.png" alt="Share in OK"/></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-gp', false)): ?>
                <a href="#" onclick="return sv_vote_send('gp', this,<?php echo $id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-gp.png" alt="Share in Google+"/></a>
            <?php endif; ?>
            <?php if (!get_option('fotov-voting-noshow-pi', false)): ?>
                <a href="#" onclick="return sv_vote_send('pi', this,<?php echo $id ?>)" target="_blank"><img src="<?php echo $theme_imgs_url; ?>soc-pi.png" alt="Share in Pinterest"/></a>
            <?php endif; ?>     
        </div>
    </div>
    
</div>