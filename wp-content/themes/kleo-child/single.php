<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */

get_header(); ?>

<?php
//Specific class for post listing */
if ( kleo_postmeta_enabled() ) {
	$meta_status = ' with-meta';
	if ( sq_option( 'blog_single_meta', 'left' ) == 'inline' ) {
		$meta_status .= ' inline-meta';
	}
	add_filter( 'kleo_main_template_classes', create_function( '$cls','$cls .= "' . $meta_status . '"; return $cls;' ) );
}

/* Related posts logic */
$related = sq_option( 'related_posts', 1 );
if ( ! is_singular('post') ) {
    $related = sq_option( 'related_custom_posts', 0 );
}
//post setting
if(get_cfield( 'related_posts') != '' ) {
	$related = get_cfield( 'related_posts' );
}
?>

<?php get_template_part( 'page-parts/general-title-section' ); ?>

<?php get_template_part( 'page-parts/general-before-wrap' );?>

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'content', get_post_format() ); ?>

	<?php $boga_related_post->main_related_post(); ?>

<?php endwhile; ?>

<?php if(in_category('streetstyle')) { ?>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- Streetstyle New -->
	<ins class="adsbygoogle streetStyleAdv"
	     style="display:block"
	     data-ad-client="ca-pub-9006336585437783"
	     data-ad-slot="4441404959"
	     data-ad-format="auto"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
<?php } ?>
<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>