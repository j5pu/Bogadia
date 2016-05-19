<?php
defined('ABSPATH') or die("No script kiddies please!");

// Если есть ошибки
if (isset($errors)) {
    echo $errors;
}

$upload_only_autorized = ( get_option('fotov-upload-autorize', false) == '' )? false : true;

$class = ( isset($show_opened) && $show_opened == true )? '' : 'hidden';

$block_class = ( isset($tabbed) && $tabbed == true )? 'tabbed_c' : '';
$block_style = ( isset($tabbed) && $tabbed == true )? 'style="display: none;"' : '';

$randInt = rand(99, 499);

?>

<div class="fv_upload <?php echo $block_class ?>" <?php echo $block_style ?>>

<?php if ( (fv_can_upload($contest) OR isset($only_form) ) && ( ($upload_only_autorized  && is_user_logged_in()) || !$upload_only_autorized) ): ?>
    <h2><span class="fvicon-download2"></span>
        <a onclick="jQuery('.fv_upload_form-<?php echo $contest->id . $randInt ?>').toggleClass('hidden'); return false;" href="#0">
            <?php echo $public_translated_messages['upload_form_title']; ?>
        </a>
    </h2>
    
    <form class="fv_upload_form fv_upload_<?php echo $contest->upload_theme ?> fv_upload_form-<?php echo $contest->id . $randInt . ' ' . $class ?>" data-w="<?php echo $word ?>" enctype="multipart/form-data"  method="POST"
          onsubmit="<?php echo (!fv_is_old_ie()) ? "fv_upload_image(this); return false;" : ''; ?>">
        <?php FvFormHelper::render_form( $public_translated_messages, $contest ); ?>
        <input type="hidden" name="contest_id" id="contest_id" value="<?php echo $contest->id ?>" />
        <input type="hidden" name="post_id" id="post_id" value="<?php echo $post->ID ?>" />
        <?php wp_nonce_field('client-file-upload'); ?>
        <input type="hidden" name="go-upload" value="1" />
    </form>

    <div class="fv_upload_messages"></div>

    <?php do_action("fv_after_upload_form"); ?>

<?php elseif ( $upload_only_autorized && !is_user_logged_in() ):?>
    <h2><?php echo $public_translated_messages['upload_form_title']; ?></h2>
    <p>
        <?php echo apply_filters( 'fv/upload_form/need_login_text', wp_kses_data(stripcslashes(sprintf($public_translated_messages['upload_form_need_login'], wp_login_url(), wp_registration_url()))) );  ?>
    </p>
    <?php
    if ( FvFunctions::ss('upload-show-login-form') ):
        wp_login_form();
        if ( has_action('wordpress_social_login') ) {
            do_action( 'wordpress_social_login' );
        }
    endif;
    ?>
<?php endif;?>
</div>