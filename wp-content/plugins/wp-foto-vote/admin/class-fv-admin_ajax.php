<?php
defined('ABSPATH') or die("No script kiddies please!");
/**
 * The ajax functionality of the plugin.
 *
 * @package    FV
 * @subpackage FV/admin
 * @author     Maxim K <wp-vote@hotmail.com>
 */

class FV_Admin_Ajax
{

    /**
     * Rotate image and thumbnails
     *
     * @params $_POST['angle'] int
     * @params $_POST['contest_id'] int
     * @params $_POST['photo_id'] int
     *
     * @return void
    */
    public static function rotate_image()
    {
        try {
                if ( !isset($_POST['angle'])  OR  !isset($_POST['contest_id'])  OR  !isset($_POST['photo_id']) ) {
                    return "incorrect params";
                }
                if (  !FvFunctions::curr_user_can() ) {
                    return -1;
                }
                $angle = (int)$_POST['angle'];

                $photoObj = ModelCompetitors::query()->where_all( array('id'=>(int)$_POST['photo_id'], 'contest_id'=>(int)$_POST['contest_id'] ) )->findRow();
                if ( !is_object($photoObj) ) {
                    FvLogger::addLog("rotate_image - photo !is_object", $photoObj);
                }

                // Include the WP MEDIA classes
                require_once ABSPATH.WPINC."/class-wp-image-editor.php";
                require_once ABSPATH.WPINC."/class-wp-image-editor-gd.php";

                /* Loop through each of the image sizes. */
                //var_dump($sizes);

                if ( has_action( 'fv/admin/rotate_image' ) === false ) {
                    /* Get the image source, width, height, and whether it's intermediate. */
                    $image = get_attached_file( $photoObj->image_id );

                    $WP_Image_Editor_GD = new WP_Image_Editor_GD( $image );

                    $WP_Image_Editor_GD->load();

                    if (  $WP_Image_Editor_GD->rotate($angle) === true  ) {

                        $WP_Image_Editor_GD->save($image);
                        $attach_data = wp_generate_attachment_metadata( $photoObj->image_id, $image );
                        wp_update_attachment_metadata( $photoObj->image_id,  $attach_data );

                        FvLogger::addLog("rotate_image - rotated success " . $angle, $image);
                        die( fv_json_encode( array('res' => 'ok') ) );
                    } else {
                        FvLogger::addLog("rotate_image - error rotate ");
                        die( fv_json_encode( array('res' => 'err') ) );
                    }
                } else {
                    do_action( 'fv/admin/rotate_image', $photoObj, $angle );
                }

        } catch(Exception $ex) {
            FvLogger::addLog("rotate_image - some error ", $ex);
        }

    }


    public static function form_contestants()
    {
        if ( !FvFunctions::curr_user_can() || !check_admin_referer('fv_nonce', 'fv_nonce') ) {
            return;
        }

        if ( !isset($_POST['photos']) ) {
            die ( "no photos" );
        }
        $photos = $_POST['photos'];

        ob_start();
            include FV::$ADMIN_PARTIALS_ROOT . 'contest/_photos_list_form.php';
        $html = ob_get_clean();

        wp_die( fv_json_encode(array('html' => $html)) );

    }

}
