<?php
/**
 * Template Name: Staff
 *
 * Description: Template withour sidebar
 *
 * @package WordPress
 * @subpackage Kleo
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Kleo 1.0
 */

get_header(); ?>

<?php
//create full width template
kleo_switch_layout('no');
?>

<?php /*get_template_part('page-parts/general-title-section'); */?>

<?php /*get_template_part('page-parts/general-before-wrap'); */?>

<?php
function showStaff(){
?>
	<h2>DIRECCIÓN:</h2>

	<?php $user_info = get_userdata(11); ?>

	<div id="authorarea" class="vcard author">
		<a class="author-link photo" href="<?php echo esc_url( get_author_posts_url( '11' ) ); ?>" rel="author">
		<?php echo get_avatar( '11', 150 ); ?></a>
		<a class="author-link url" href="<?php echo esc_url( get_author_posts_url( '11') ); ?>" rel="author">
		<h2 class="fn"><?php echo $user_info->display_name; ?></h2></a>
		<?php
		$address = get_user_meta( 11, 'shipping_city', true );
		?>
		<div class="authorinfo city">
		<?php echo $address; ?>
		</div>
		<div class="authorinfo role">
			<?php echo $user_info->description; ?><br/>
		</div>
	</div>        

	<h2>REDACCIÓN:</h2>

	<?php $user_info = get_userdata(35); ?>

	<div id="authorarea" class="vcard author">
		<a class="author-link photo" href="<?php echo esc_url( get_author_posts_url( '35' ) ); ?>" rel="author">
		<?php echo get_avatar( '35', 150 ); ?></a>
		<a class="author-link url" href="<?php echo esc_url( get_author_posts_url( '35') ); ?>" rel="author">
		<h2 class="fn"><?php echo $user_info->display_name; ?></h2></a>
		<?php
		$address = get_user_meta( 35, 'shipping_city', true );
		?>
		<div class="authorinfo city">
		<?php echo $address; ?>
		</div>
		<div class="authorinfo role">
			<?php echo $user_info->description; ?><br/>
		</div>
	</div>        

	<?php $user_info = get_userdata(70); ?>

	<div id="authorarea" class="vcard author">
		<a class="author-link photo" href="<?php echo esc_url( get_author_posts_url( '70' ) ); ?>" rel="author">
		<?php echo get_avatar( '70', 150 ); ?></a>
		<a class="author-link url" href="<?php echo esc_url( get_author_posts_url( '70') ); ?>" rel="author">
		<h2 class="fn"><?php echo $user_info->display_name; ?></h2></a>
		<?php
		$address = get_user_meta( 70, 'shipping_city', true );
		?>
		<div class="authorinfo city">
		<?php echo $address; ?>
		</div>
		<div class="authorinfo role">
			<?php echo $user_info->description; ?><br/>
		</div>
	</div>        

	<!--<h2>FOTOGRAFÍA:</h2>-->

<?php

	$user_query = new WP_User_Query( array( 'role' => 'fotografo' ) );

	// Get the results
	$authors = $user_query->get_results();

	// Check for results
	if (!empty($authors)) {
	    // loop trough each author
	    foreach ($authors as $author)
	    {
	        // get all the user's data
	        $user_info = get_userdata($author->ID);
	    ?>

	    <div id="authorarea" class="vcard author">
			<a class="author-link photo" href="#" rel="author">
			<?php echo get_avatar( $author->ID, 150 ); ?></a>
			<a class="author-link url" href="#" rel="author">
			<h2 class="fn"><?php echo $user_info->display_name; ?></h2></a>
			<?php
			$address = get_user_meta( $author->ID, 'shipping_city', true );
			?>
			<div class="authorinfo city">
			<?php echo $address; ?>
			</div>
			<div class="authorinfo role">
			<?php echo $user_info->description; ?><br/>
			</div>
		</div>        

	    <?php
	    }
	}

?>

	<h2>REDACTORES COLABORADORES:</h2>

<?php

	$user_query1 = new WP_User_Query( array( 'role' => 'redactor-colaborador' ) );
	$authors = $user_query1->get_results();

	// Check for results
	if (!empty($authors)) {
	    // loop trough each author
	    foreach ($authors as $author)
	    {
	        // get all the user's data
	        $user_info = get_userdata($author->ID);
	    ?>

	    <div id="authorarea" class="vcard author">
			<a class="author-link photo" href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" rel="author">
			<?php echo get_avatar( $author->ID, 150 ); ?></a>
			<a class="author-link url" href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" rel="author">
			<h2 class="fn"><?php echo $user_info->display_name; ?></h2></a>
			<?php
			$address = get_user_meta( $author->ID, 'shipping_city', true );
			?>
			<div class="authorinfo city">
			<?php echo $address; ?>
			</div>
			<div class="authorinfo role">
			<?php echo $user_info->description; ?><br/>
			</div>
		</div>        

	    <?php
	    }
	}
?>
	<h2>FOTÓGRAFOS COLABORADORES:</h2>

<?php

	$user_query1 = new WP_User_Query( array( 'role' => 'fotografo-colaborador' ) );
	$authors = $user_query1->get_results();

	// Check for results
	if (!empty($authors)) {
	    // loop trough each author
	    foreach ($authors as $author)
	    {
	        // get all the user's data
	        $user_info = get_userdata($author->ID);
	    ?>

	    <div id="authorarea" class="vcard author">
			<a class="author-link photo" href="#" rel="author">
			<?php echo get_avatar( $author->ID, 150 ); ?></a>
			<a class="author-link url" href="#" rel="author">
			<h2 class="fn"><?php echo $user_info->display_name; ?></h2></a>
			<?php
			$address = get_user_meta( $author->ID, 'shipping_city', true );
			?>
			<div class="authorinfo city">
			<?php echo $address; ?>
			</div>
			<div class="authorinfo role">
			<?php echo $user_info->description; ?><br/>
			</div>
		</div>        

	    <?php
	    }
	}

?>

<?php
}
add_shortcode( 'staff', 'showStaff');
?>

<?php
if ( have_posts() ) :
	// Start the Loop.
	while ( have_posts() ) : the_post();

		/*
		 * Include the post format-specific template for the content. If you want to
		 * use this in a child theme, then include a file called called content-___.php
		 * (where ___ is the post format) and that will be used instead.
		 */
		get_template_part( 'content', 'page' );

	endwhile;

endif;

get_template_part('page-parts/general-after-wrap'); ?>

<?php get_footer(); ?>