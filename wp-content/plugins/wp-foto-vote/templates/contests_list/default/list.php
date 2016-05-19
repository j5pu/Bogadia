<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
 * === Variables passed to this script: ===
 *** PHOTO DATA ***
 * $contests - Contest array
 * $contests->cover_img - CONTEST THUMBNAIL SRC (array [0] - src, [1] - width, [2] - height)
 * $args => shortcode params:
 *          array(
 *           'theme' => 'default',
 *           'type' => 'active',     // active, upload_opened, finished
 *           'count' => '6',
 *           'on_row' => '4',
 *          )
 */
?>

<div class="contest-list">

<?php foreach($contests as $CONTEST): ?>
    <div class="contest-list-display" style="width:<?php echo $width; ?>px;">
        <figure>
            <a href="<?php echo get_permalink($CONTEST->page_id) ?>">
                <img alt="Love" class="display-image" src="<?php echo $CONTEST->cover_image_url[0] ?>" style="display: block;">

            </a>
            <a href="<?php echo get_permalink($CONTEST->page_id) ?>" class="figcaption">
                <h3><?php echo $CONTEST->name ?></h3>
                <span></span>
            </a>
            <?php if ( isset($CONTEST->upload_started) && !$CONTEST->upload_started ): ?>
                <span class="contest-list-not-active"><?php _e('not active', 'fv') ?></span>
            <?php endif; ?>
        </figure>
        <!--figure-->
        <div class="box-detail">
            <h5 class="heading">
                <a href="<?php echo get_permalink($CONTEST->page_id) ?>"><?php echo $CONTEST->name ?></a>
                <?php if ( FvFunctions::curr_user_can() ): ?>
                    <a title="<?php _e('Visible only for admins', 'fv') ?>" href="<?php echo admin_url('admin.php?page=fv&action=edit&contest=' . $CONTEST->id) ?>" target="_blank">
                        <i class="fvicon-pencil"></i>
                    </a>
                <?php endif; ?>
            </h5>
            <ul class="list-inline contest-list-details">
                <li class="pull-left"><?php echo $CONTEST->cover_text ?></li>
                <li class="pull-right">
                    <span class="photos-summary"><i class="fvicon-camera2"></i> <?php echo $CONTEST->P_count ?></span> &nbsp;
                    <span class="votes-summary"><i class="fvicon-heart"></i> <?php echo $CONTEST->P_votes_count ?></span>
                </li>
            </ul>
        </div>
        <!--.box-detail-->
    </div>
    <!--.contest-list-display-->
<?php endforeach; ?>

</div>

