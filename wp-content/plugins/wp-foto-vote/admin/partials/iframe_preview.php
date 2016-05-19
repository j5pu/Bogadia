<?php

        // This include gives us all the WordPress functionality
        $parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
        require_once( $parse_uri[0] . 'wp-load.php' );

        defined('ABSPATH') or die("No script kiddies please!");

        if( ! FvFunctions::curr_user_can()  ){         die();        }

        if ( isset($_GET['theme']) ) {
                $theme = $_GET['theme'];
        } else {
                die("Error: no theme");
        }

        $fv_block_width = intval(get_option('fotov-block-width', FV_CONTEST_BLOCK_WIDTH));

        $id = 1;
        $name = "Test photo";
        $description = "Text text long";
        $additional = "Test text long";
        $votes = 88;

        $image_full = "";

        $thumbnail[0] = plugins_url( "wp-foto-vote/admin/images/demo.jpg");
        $thumbnail[1] = FV_CONTEST_BLOCK_WIDTH - 5;

        $leaders = false;
        $konurs_enabled = true;
        $public_translated_messages = fv_get_public_translation_messages();
?>

<!doctype html>
<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
<html class="no-js" lang="en">
<head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Preview</title>

        <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" />
        <link rel="stylesheet" href="<?php echo plugins_url( "wp-foto-vote/themes/" . $theme . "/public_list_tpl.css" ) ?>" />
</head>
<body>

<?php
    include FV_ROOT . '/themes/' . $theme . '/unit.php';
?>

</body>
</html>