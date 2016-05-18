<?php
require_once('../../../wp-load.php');
global $wpdb;
$results = $wpdb->get_results( "SELECT user_fb_id FROM wp_bogashare", OBJECT );
$contestans_fb_ids = array();
$contestans_ids = array();
foreach($results as $result){
     array_push($contestans_fb_ids, $result->user_fb_id);
}
$contestans_fb_ids = array_unique($contestans_fb_ids);
$results = $wpdb->get_results( "SELECT user_id,meta_value,display_name FROM wp_usermeta INNER JOIN wp_users ON wp_usermeta.user_id = wp_users.ID WHERE meta_key = '_fbid' AND meta_value IN (" . implode(',', array_map('intval', $contestans_fb_ids)) . ")", OBJECT );

?>
<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */

get_header(); ?>



<?php get_template_part( 'page-parts/general-title-section' ); ?>

<?php get_template_part( 'page-parts/general-before-wrap' );

echo '<ul class="media-list">';
foreach($results as $contestant){
    echo '<li class="media col-xs-6 col-sm-4 portada_posts"><div class="media"><a href="https://www.facebook.com/'. $contestant->meta_value . '"><img class="media-object img-responsive" src="https://graph.facebook.com/'. $contestant->meta_value . '/picture?type=large"></a></div><div class="media-body"><h4 class="media-heading">'. $contestant->display_name . '</h4></div></li>';
}
echo '</ul>';
?>


<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>

<script>
    jQuery(document).ready();
</script>
