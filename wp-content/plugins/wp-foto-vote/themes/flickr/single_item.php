<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * $contestant->id - PHOTO ID (int)
 * $image - PHOTO THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $contestant->image_id - PHOTO FULL SRC (string)
 * $contestant->image_full - PHOTO FULL SRC (string)
 * $contestant->name - PHOTO NAME (string)
 * $contestant->description - PHOTO DESCRIPTION (string)
 * $contestant->additional - PHOTO ADDITIONAL DESCRIPTION (string), uses as <code> mb_substr($additional, 0, 30, 'UTF-8') </code>
 * $contestant->votes_count - PHOTO VOTES COUNT (int)
 * $contestant->upload_info - json decoded Upload form fields*
*** OTHER ***
 * $prev_id
 * next_id
 * $public_translated_messages - TRANSLATED MESSAGES (array)
 * $contest_id - CONTEST ID (int)
 * $page_url - PAGE URL (string)
 * $theme - USED THEME (string)
 * $konurs_enabled - IS CONTEST ENABLED (bool)
 */
?>
<div class="photo-single-item contest-block fv-bs-grid">

    <div class="content-heading">
        <?php echo $contestant->name ?>
        <div class="back pull-right">
            <a href="<?php echo remove_query_arg( 'contest_id', remove_query_arg( 'photo') ); ?>">
                <i class="fvicon fvicon-login"></i> <?php echo $public_translated_messages['back_to_contest'] ?>
            </a>
        </div>
    </div>

    <div class="main-image col-md-12">
        <div class="controlArrow controlArrow-prev "><a href="<?php echo $page_url . '=' . $prev_id ?>" class="fvicon-arrow-left2"></a></div>
        <div class="controlArrow controlArrow-next"><a href="<?php echo $page_url . '=' . $next_id ?>" class="fvicon-arrow-right2"></a></div>
        <p><img src="<?php echo $image[0] ?>" alt="<?php echo $contestant->name ?>" class="mainImage img-thumbnail"></p>
    </div>


    <div class="col-md-8">
        <div class="clearfix">
            <div class="image-details">
                <h3 class="block-heading">
                    <?php _e('Description', 'fv') ?>
                    <?php if ( isset($is_most_voted) && $is_most_voted ): ?>
                        &nbsp;
                        <img src="<?php echo FvFunctions::get_theme_url ( $theme, 'images/award.png' ); ?>" alt="leader" width="32"/>
                        <?php _e( sprintf('Contest %s place Winner', $most_voted_place), 'fv') ?>
                    <?php endif; ?>
                </h3>

                <p><?php echo $contestant->additional ?></p>
                <!-- .col-md-4 -->
            </div>
        </div>


        <div class="comments-block">
            <h3 class="block-heading"><?php _e('Comments', 'fv') ?></h3>
            <div class="fb-comments" data-href="<?php echo 'http://' . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" data-numposts="5" data-colorscheme="light" data-width="100%"></div>
            <?php //comment_form( array(), $contestant->image_id ); ?>
        </div>

    <!--.col-md-8-->
    </div>


    <div class="col-md-4">
        <span class="btn-group  btn-group-xs">
           <button type="button" class="btn btn-success favoritebtn fv_vote"  onclick="sv_vote(<?php echo $contestant->id ?>); return false;"><i class="fvicon-star"></i> Favorite</button>
        </span>
        <span class="sidebar-image-details">
            <i class="fvicon-star3"></i> <span class="sv_votes_<?php echo $contestant->id ?>"><?php echo $contestant->votes_count ?></span>
            <?php if( FvFunctions::ss('soc-counter', false) ): ?>
                &nbsp;<i class="fvicon-share"></i> <span class="fv_svotes_<?php echo $contestant->id ?>" title="<?php echo $public_translated_messages['shares_count_text']; ?>">0</span>
            <?php endif; ?>
        </span>
        <div class="clearfix"></div>

        <h3 class="block-heading"><?php _e('Share', 'fv') ?></h3>
        <div class="clearfix">
            <div class="more-from-site">
                <ul class="action-bar clearfix">
                    <li>
                        <a href="#0" class="twitter" onclick="return sv_vote_send('tw', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-twitter"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#0" class="facebook" onclick="return sv_vote_send('fb', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-facebook"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#0" class="gplus" onclick="return sv_vote_send('gp', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-googleplus3"></span>
                        </a>
                    </li>
                    <li>
                        <a href="#0" class="pintrest" onclick="return sv_vote_send('pi', this ,<?php echo $contestant->id ?>);">
                            <span class="fvicon-pinterest3"></span>
                        </a>
                    </li>
                </ul>      </div>
        </div>
        <div class="exif" style="display: none">
            <h3 class="block-heading"><?php _e('EXIF Data', 'fv') ?></h3>
            <div class="clearfix exif-info">
                <p>
                    <strong><?php _e('Model', 'fv') ?> </strong>
                    <span class="exif-model">Canon EOS 5D Mark II</span>
                </p>
                <p>
                    <strong><?php _e('Focal Length', 'fv') ?> </strong>
                    <span class="exif-focal-length">24mm</span>
                </p>
                <p><strong><?php _e('Shutter Speed', 'fv') ?> </strong>
                    <span class="exif-shutter-speed">1/640</span></p>
                <p>
                    <strong><?php _e('Aperture', 'fv') ?> </strong>
                    <span class="exif-aperture">F5.6</span>
                </p>
                <p>
                    <strong><?php _e('ISO', 'fv') ?> </strong>
                    <span class="exif-iso">100</span>
                </p>
                <p>
                    <strong><?php _e('Taken At', 'fv') ?> </strong>
                    <span class="exif-taken-at">Sat, Oct 30, 2010 6:32 PM</span>
                </p>
            </div>
        </div>

        <h3 class="block-heading"><?php _e('More', 'fv') ?></h3>
        <div class="clearfix">
            <div class="more-from-site"><?php foreach( $most_voted as $MOST ):
                    $image = FvFunctions::getPhotoThumbnailArr($MOST, 'thumbnail');
                ?>
                    <a href="<?php echo $page_url . '=' . $MOST->id; ?>"><img src="<?php echo $image[0]; ?>" alt="<?php echo $MOST->name; ?>"></a>
            <?php endforeach; ?></div>
        </div>
    </div>

</div>