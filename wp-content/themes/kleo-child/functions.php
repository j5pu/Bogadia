<?php
/**
 * @package WordPress
 * @subpackage Kleo
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Kleo 1.0
 */

/**
 * Kleo Child Theme Functions
 * Add custom code below
*/ 

/* 
*
* Shortcode para sacar en home los 6 ultimos post despues de los del slider
*
*/

function posts_home(){
	$id_noticias = get_cat_ID( 'noticias' );
	global $not_post_in;
	// The Query
	$args = array(
		'post_status'  => 'publish',
		'posts_per_page' => 29,
		'orderby' => 'date',
		'order'    => 'DESC',
		'cat'	=> '-566, -'.$id_noticias
	);
	query_posts( $args );
	
	/*echo '<div class="row">';*/
	// The Loop
	$c=1;

	while ( have_posts() ) : the_post();
		$post_id = get_the_ID();
		if($c>0){ 
			$category = get_the_category();
			foreach ($category as $struct ) {
				if ( $struct->cat_name == 'Streetstyle'){
					$category[0] = $struct;
				}
			}

			$link = get_permalink();
			$title = get_the_title();

			if ($c>1 && $c%2 == 0){
				echo '<div class="portada_posts portada_posts_left">';
			}else if($c>1 && $c%2 !== 0){
				echo '<div class="portada_posts portada_posts_right">';
			}else{
				echo '<div class="portada_posts">';				
			}

			if ($c==1) {
				echo '<a href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $post_id, 'full' ).'</a>';
			}else{	
				echo '<a href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $post_id, 'medium' ).'</a>';
			}
			//echo '<div class="hr-title hr-long"><abbr><a href="'.get_category_link($category[0]->term_id ).'">'.$category[0]->cat_name.'</a></div>';			
			echo '<a href="'.$link.'" title="'.$title.'"><h2 class="title_home">'.$title.'</h2></a>';
			echo '<h3 class="subtitle_home">'.get_the_excerpt().'</h3>';
			echo '</div>';
			//Mirar los post que ya han salido y cargararlo en la variable de wordpress que permite obviar los que se han mostrado
			$not_post_in[] = get_the_ID();
		}else{
				//Mirar los post que ya han salido y cargararlo en la variable de wordpress que permite obviar los que se han mostrado
				$not_post_in[] = get_the_ID();
		}

		$c++;
	endwhile;
	/*echo '</div>';*/
	echo '<div style="clear:both;"></div>';
	// Reset Query
	wp_reset_query();	
}
add_shortcode( 'PostsRecents', 'posts_home' );

/*Ocultar título de las categorias*/
function kleo_title()
	{
		$output = "";
        if (is_tag()) {
            $output = single_tag_title('',false);
        }
		elseif(is_tax()) {
            $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            $output = $term->name;
        }
		elseif ( is_category() ) {
            $output = single_cat_title('', false);
        }
		elseif (is_day())
		{
			$output = __('Archive for date:','kleo_framework')." ".get_the_time('F jS, Y');
		}
		elseif (is_month())
		{
			$output = __('Archive for month:','kleo_framework')." ".get_the_time('F, Y');
		}
		elseif (is_year())
		{
			$output = __('Archive for year:','kleo_framework')." ".get_the_time('Y');
		}
        elseif (is_author())  {
            $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));

            if( isset( $curauth->nickname ) ) {
                $output .= $curauth->nickname;
            }
        }
		elseif ( is_archive() )  {
			$output = post_type_archive_title( '', false );
		}
		elseif (is_search())
		{
			global $wp_query;
			if(!empty($wp_query->found_posts))
			{
				if($wp_query->found_posts > 1)
				{
					$output =  $wp_query->found_posts ." ". __('search results for:','kleo_framework')." ".esc_attr( get_search_query() );
				}
				else
				{
					$output =  $wp_query->found_posts ." ". __('search result for:','kleo_framework')." ".esc_attr( get_search_query() );
				}
			}
			else
			{
				if(!empty($_GET['s']))
				{
					$output = __('Search results for:','kleo_framework')." ".esc_attr( get_search_query() );
				}
				else
				{
					$output = __('To search the site please enter a valid term','kleo_framework');
				}
			}

		}
        elseif ( is_front_page() && !is_home() ) {
            $output = get_the_title(get_option('page_on_front'));
            
		} elseif ( is_home() ) {
            if (get_option('page_for_posts')) {
                $output = get_the_title('',false);
            } else {
                $output = __('',false);
            }
            
		} elseif ( is_404() ) {
            $output = __('Error 404 - Page not found','kleo_framework');
		}
		else {
			$output = get_the_title();
		}
        
		if (isset($_GET['paged']) && !empty($_GET['paged']))
		{
			$output .= " (".__('Page','kleo_framework')." ".$_GET['paged'].")";
		}
    
		return $output;
	}

/*Función para mostrar metas de post. Modificada para que cargue el avatar de la redactora de 64*64*/
if ( ! function_exists( 'kleo_entry_meta' ) ) :
    /**
     * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
     * Create your own kleo_entry_meta() to override in a child theme.
     * @since 1.0
     */
    function kleo_entry_meta( $echo = true, $att = array() ) {

		function print_affiliate_link_post($redsocial, $url, $post){
			global $current_user_id;
			return $url .'?utm_source='. $redsocial .'&utm_medium=www.bogadia.com&utm_term='. $current_user_id .'&utm_content='. $post->ID .'&utm_campaign=SHARE_POST' ;
		}

        global $kleo_config;
        $meta_list = array();
        $author_links = '';
        $meta_elements =  sq_option( 'blog_meta_elements', $kleo_config['blog_meta_defaults'] );

        // Translators: used between list items, there is a space after the comma.
        if ( in_array( 'categories', $meta_elements ) ) {
            $categories_list = get_the_category_list(__(', ', 'kleo_framework'));
        }

        // Translators: used between list items, there is a space after the comma.
        if ( in_array('tags', $meta_elements ) ) {
            $tag_list = get_the_tag_list('Te puede interesar: ','','');
        }
        $date = sprintf('<time class="entry-date" datetime="%2$s">%3$s</time>' .			//Eliminado el link en la fecha que llevaba al mismo post
            '<time class="modify-date hide hidden updated" datetime="%4$s">%5$s</time>',
            esc_url( get_permalink() ),
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_html( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
        );

        if ( is_array( $meta_elements ) && !empty( $meta_elements ) ) {


            if ( in_array( 'author_link', $meta_elements ) || in_array( 'avatar', $meta_elements ) ) {

                /* If buddypress is active then create a link to Buddypress profile instead */
                if (function_exists( 'bp_is_active' ) ) {
                    $author_link = esc_url( bp_core_get_userlink( get_the_author_meta( 'ID' ), $no_anchor = false, $just_link = true ) );
                    $author_title = esc_attr( sprintf( __( 'View %s\'s profile', 'kleo_framework' ), get_the_author() ) );
                } else {
                    $author_link = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
                    $author_title = esc_attr( sprintf( __( 'View all POSTS by %s', 'kleo_framework' ), get_the_author() ) );
                }

                $author = sprintf( '<a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s %4$s</a>',
                    $author_link,
                    $author_title,
                    in_array( 'avatar', $meta_elements ) ? get_avatar( get_the_author_meta( 'ID' ), 64) : '', // Modificado de 50 a 64 para que cargue el avatar de 64x64px
                    in_array( 'author_link', $meta_elements ) ? '<span class="author-name">' . get_the_author() . '</span>' : ''
                );

                $meta_list[] = '<small class="meta-author author vcard">' . $author . '</small>';
            }

            if ( function_exists( 'bp_is_active' ) ) {
                if ( in_array( 'profile', $meta_elements ) ) {
                    $author_links .= '<a href="' . bp_core_get_userlink( get_the_author_meta( 'ID' ), $no_anchor = false, $just_link = true ) . '">' .
                        '<i class="icon-user-1 hover-tip" ' .
                        'data-original-title="' . esc_attr(sprintf(__('View profile', 'kleo_framework'), get_the_author())) . '"' .
                        'data-toggle="tooltip"' .
                        'data-placement="top"></i>' .
                        '</a>';
                }

                if ( bp_is_active( 'messages' ) ) {
                    if ( in_array( 'message', $meta_elements ) ) {
                        $author_links .= '<a href="' . wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( get_the_author_meta( 'ID' ) ) ) . '">' .
                            '<i class="icon-mail hover-tip" ' .
                            'data-original-title="' . esc_attr(sprintf(__('Contact %s', 'kleo_framework'), get_the_author())) . '" ' .
                            'data-toggle="tooltip" ' .
                            'data-placement="top"></i>' .
                            '</a>';
                    }
                }
            }

            if ( in_array( 'archive', $meta_elements ) ) {
                $author_links .= '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' .
                    '<i class="icon-docs hover-tip" ' .
                    'data-original-title="' . esc_attr(sprintf(__('View all posts by %s', 'kleo_framework'), get_the_author())) . '" ' .
                    'data-toggle="tooltip" ' .
                    'data-placement="top"></i>' .
                    '</a>';
            }

        }

        echo '<h2 class="subtitle_post">'.get_the_excerpt().'</h2>';
		function encodeURIComponent($str) {
			$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
			return strtr(rawurlencode($str), $revert);
		}
		$post = $GLOBALS['post'];
		$url = get_permalink($post->ID) ;
		$title = $post->post_title .' @bogadiamag';
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' )[0];
		$share_buttons = '<div id="mobile_share_button" class="row">';
		$share_buttons .= '<div class="text-center col-xs-12">';
		$share_buttons .= '<a id="whatsapp_share_button" class="bogashare_icon_holder" target="_blank" href="whatsapp://send?text='. encodeURIComponent($title .' '. print_affiliate_link_post('whatsapp', $url, $post)) .'" data-action="share/whatsapp/share"><i class="icon-whatsapp bogashare_social" style="background-color: #43d854;"></i></a>';
		$share_buttons .= '<a id="facebook_share_button" class="bogashare_icon_holder" target="_blank" href="http://www.facebook.com/sharer/sharer.php?u='. print_affiliate_link_post('facebook', $url, $post) .'"><em class="icon-facebook bogashare_social" style="background-color: #3b5998;"></em></a>';
		$share_buttons .= '<a id="twitter_share_button" class="bogashare_icon_holder" target="_blank" href="http://twitter.com/intent/tweet?status='. encodeURIComponent($title .' '. print_affiliate_link_post('twitter', $url, $post) ) .'"><em class="icon-twitter bogashare_social" style="background-color: #00aced;"></em></a>';
		$share_buttons .= '<a id="pinterest_share_button" class="bogashare_icon_holder" target="_blank" href="http://pinterest.com/pin/create/bookmarklet/?media='. $image .'&url='. print_affiliate_link_post('pinterest', $url, $post) .'&is_video=false&description='. $title .'"><i class="icon-pinterest-circled bogashare_social" style="background-color: #bd081c;"></i></a>';
		$share_buttons .= '</div>';
		$share_buttons .= '</div>';
		echo $share_buttons;
		global $share_buttons;

        if ( $author_links != '' ) {
            $meta_list[] = '<small class="meta-links">' . $author_links . '</small>';
        }

        if (in_array( 'date', $meta_elements ) ) {
            $meta_list[] = '<small>' . $date . '</small>';
        }

        $cat_tag = array();

        if ( isset( $categories_list ) && $categories_list ) {
            $cat_tag[] = $categories_list;
        }

        if ( isset( $tag_list ) && $tag_list ) {
            $cat_tag[] = $tag_list;
        }
        if (!empty($cat_tag)) {
            $meta_list[] = '<small class="meta-category">'.implode(", ",$cat_tag).'</small>';
        }

        //comments
        if ((!isset($att['comments']) || (isset($att['comments']) && $att['comments'] !== false)) && in_array( 'comments', $meta_elements )) {
            $meta_list[] = '<small class="meta-comment-count"><a href="'. get_permalink().'#comments">'.get_comments_number() .
                ' <i class="icon-chat-1 hover-tip" ' .
                'data-original-title="'.sprintf( _n( 'This article has one comment', 'This article has %1$s comments', get_comments_number(), 'kleo_framework' ),number_format_i18n( get_comments_number() ) ).'" ' .
                'data-toggle="tooltip" ' .
                'data-placement="top"></i>' .
                '</a></small>';
        }

        $meta_separator = isset( $att['separator'] ) ? $att['separator'] : sq_option( 'blog_meta_sep', ', ') ;
        if ( $echo ) {
            echo implode( $meta_separator, $meta_list );
        }
        else {
            return implode( $meta_separator, $meta_list );
        }
    }
endif;


/*
*
* Elminar permalink author
*
*/

// The first part //  - He quitado el page de la paginacion 
add_filter('author_rewrite_rules', 'no_author_base_rewrite_rules');
function no_author_base_rewrite_rules($author_rewrite) {
    global $wpdb;
    $author_rewrite = array();
    $authors = $wpdb->get_results("SELECT user_nicename AS nicename from $wpdb->users");   
    foreach($authors as $author) {
        $author_rewrite["({$author->nicename})/page/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
		$author_rewrite["({$author->nicename})/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
        $author_rewrite["({$author->nicename})/?$"] = 'index.php?author_name=$matches[1]';
    }  
    return $author_rewrite;
}
 
// The second part //
add_filter('author_link', 'no_author_base', 1000, 2);
function no_author_base($link, $author_id) {
    $link_base = trailingslashit(get_option('home'));
    $link = preg_replace("|^{$link_base}author/|", '', $link);
    return $link_base . $link;
}

/*Ocultar sidebar en tablet y móvil

add_filter( 'kleo_page_layout' , 'remove_sidebar_mobile' );
function remove_sidebar_mobile() {
 
    if ( wp_is_mobile() ) {
        $layout = 'no';
    }else{
       $layout = 'right';
    }
    return $layout;
}
*/

/*
*
* Shortcode para sacar fotografo
*
*/
function photographer_box( $atts ){
	$user_photographer = get_user_by( "email", $atts['email'] );
	?>

	<div class="photographer-box">
		<h2 class="newTitleAuthor">
		<a class="author-link photo newAuthorPhoto" href="<?php bloginfo('wpurl'); ?>/equipo" rel="author"> 
			<?php echo get_avatar( $user_photographer -> id, 100 ); ?>
		</a>
		<span>Fotografías de </span>
		<a class="author-link url" href="<?php bloginfo('wpurl'); ?>/equipo" rel="author">
			<?php echo $user_photographer->display_name ; ?>
		</a>
		</h2>
	</div>

	<?php
}
add_shortcode( 'photoBox', 'photographer_box');

/* 
*
* Shortcode sidebar Lo más visto
*
*/
function losmasvistossidebar(){
	//Sql para la obtencion delos posts:
	global $not_post_in;
	$current_post = get_the_ID();
	$likes_posts_args = array(
			'post__not_in' => array($current_post),
			'numberposts' => 4,
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'meta_key' => 'views',
			'post_type' => 'post',
			'post_status' => 'publish', 
			'date_query' => array('column' => 'post_date_gmt', 'after' => '1 week ago') // Muestra los post más leidos solo del último mes.	
		);	
	$likes_posts = get_posts($likes_posts_args);
		echo '<h4 class="widget-title">Lo más visto</h4>';
		foreach( $likes_posts as $likes_post ) {			
			echo '<div class="post-sidebar">';
			$img = $likes_post->ID;
			$link = get_permalink($img);
			$title = get_the_title($img);			
			echo '<a class="sidebar-img" href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $img, 'thumbnail' ).'</a>'.'<h5 class="title-post-sidebar"><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h5>';	
			echo '</div>';
			wp_reset_query();
		}

}
add_shortcode( 'MasVistosSidebar', 'losmasvistossidebar' );
/* 
*
* Shortcode sidebar Notibogadia
*
*/
function lasUltimasNoticiasSidebar(){
	$id_noticias = get_cat_ID( 'noticias' );
	//Sql para la obtencion delos posts:
	global $not_post_in;
	$current_post = get_the_ID();
	$lasts_posts_args = array(
			'post__not_in' => array($current_post),
			'numberposts' => 4,
			'orderby' => 'post_date',
			'order' => 'DESC',
			'post_type' => 'post',
			'post_status' => 'publish',
			'cat' => $id_noticias				
		);	
	$lasts_posts = get_posts($lasts_posts_args);
	?>
	<h4 class="widget-title">#NotiBogadia</h4>
	<?php
		foreach( $lasts_posts as $last_post ) {		
			$category = get_the_category($last_post->ID);
			echo '<div class="post-sidebar">';
			$img = $last_post->ID;
			$link = get_permalink($img);
			$title = get_the_title($img);		
			echo '<a class="sidebar-img" href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $img, 'thumbnail' ).'</a>'.'<h5 class="title-post-sidebar"><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h5>';			
			echo '</div>';
			wp_reset_query();
		}

}
add_shortcode( 'UltimasNoticias', 'lasUltimasNoticiasSidebar' );

/* 
*
* Shortcode sidebar Lo más visto [categoría]
*
*/
function populares_Categoria_Sidebar( $atts ){
	$id_cat = get_cat_ID( $atts['cat'] );
	//Sql para la obtencion delos posts:
	global $not_post_in;
	$current_post = get_the_ID();
	$lasts_posts_args = array(
			'post__not_in' => array($current_post),
			'numberposts' => 4,
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'meta_key' => 'views',
			'post_type' => 'post',
			'post_status' => 'publish', 
			'date_query' => array('column' => 'post_date_gmt', 'after' => '1 month ago'), // Muestra los post más leidos solo del último mes.	
			'cat' => $id_cat			
		);	
	?>
	<h4 class="widget-title">Lo más visto en <?php echo $atts['cat'];?></h4>
	<?php
	$lasts_posts = get_posts($lasts_posts_args);
		foreach( $lasts_posts as $last_post ) {	
			$category = get_the_category($last_post->ID);
			echo '<div class="post-sidebar">';
			$img = $last_post->ID;
			$link = get_permalink($img);
			$title = get_the_title($img);		
			echo '<a class="sidebar-img" href="'.$link.'" title="'.$title.'">'.get_the_post_thumbnail( $img, 'thumbnail' ).'</a>'.'<h5 class="title-post-sidebar"><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h5>';
			echo '</div>';
			wp_reset_query();
		}

}
add_shortcode( 'popularesCategoria', 'populares_Categoria_Sidebar' );

/*
*
*Si el usuario está registrado y/o está en la tienda mostrar logo Shop
]
**/

function streetstyle_script() {
	
	if (in_category('streetstyle')) {
		wp_enqueue_script( 'streetstyleScript', '/wp-content/themes/kleo-child/assets/js/streetstyleScript.js',true);
	}
	    //wp_enqueue_script( 'script_child', '/wp-content/themes/kleo-child/assets/js/script_child.js',true);

	/*if ( !is_user_logged_in() && !is_page('tienda/') && !is_page('tienda') && !is_page('por-que-bogadia') && !is_page('phetnia') && !is_page('manifiesto-neon')&& !is_page('maria-cidfuentes-2') && !is_page('lucrecia')&& !is_page('cart') && !is_product() && !is_woocommerce() && (WC()->cart->get_cart_contents_count()) == 0) { 
		wp_enqueue_style ( 'style_functions', '/wp-content/themes/kleo-child/style_functions.css');
	}*/
}

add_action( 'wp_enqueue_scripts', 'streetstyle_script' );

/*Añadir código Zanox al <head>*/

function zanox() {
	echo '<meta name="verification" content="6c2c0d0251a189774a6fe4252ce561a5" />' ;
}
add_action('wp_head', 'zanox');

/*Añadir código Pinterest al <head>*/

function pinterest() {
	echo '<meta name="p:domain_verify" content="fd4dd19485ea9f51eccc6866100da866"/>' ;
}
add_action('wp_head', 'pinterest');

function remove_style_id($link) {
	return preg_replace("/id='.*-css'/", "", $link);
}
add_filter('style_loader_tag', 'remove_style_id');

$counter = 0;
function remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) ){
		$src = remove_query_arg( 'ver', $src );
	}

/*	if (strpos($src, 'dynamic.css') == false && strpos($src, 'font') == false) {
		global $counter;
		$counter++;
		if ($counter%2==0){
				$src = str_replace("www.bogadia", "bogacdn.appspot", $src);
		}else{
			$src = str_replace("www.bogadia", "bogacdn2.appspot", $src);
		}
	}*/
	return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );

add_filter( 'script_loader_tag', function ( $tag, $handle ) {

	if ( 'photon' !== $handle )
		return $tag;

	return str_replace( ' src', ' data-pagespeed-no-defer src', $tag );
}, 10, 2 );

include('boga_related_post.php');
$boga_related_post = new boga_related_post();

function show_bogashare_contestants(){
	global $wpdb;
	$results = $wpdb->get_results( "SELECT user_fb_id FROM wp_bogashare ORDER BY date ASC", OBJECT );
	$contestans_fb_ids = array();
	foreach($results as $result){
		array_push($contestans_fb_ids, $result->user_fb_id);
	}
	$contestans_fb_ids = array_unique($contestans_fb_ids);
/*	$results = $wpdb->get_results( "SELECT user_id,user_fb_id,display_name, post_title FROM wp_usermeta INNER JOIN wp_users ON wp_usermeta.user_id = wp_users.ID INNER JOIN wp_bogashare ON wp_bogashare.user_fb_id = wp_usermeta.meta_value INNER JOIN wp_posts ON wp_bogashare.post_id = wp_posts.ID WHERE wp_usermeta.meta_key = '_fbid' AND wp_usermeta.meta_value IN (" . implode(',', array_map('intval', $contestans_fb_ids)) . ") ORDER BY wp_bogashare.date DESC", OBJECT );*/
	$results = $wpdb->get_results( "SELECT user_fb_id,display_name, post_title FROM wp_usermeta INNER JOIN wp_users ON wp_usermeta.user_id = wp_users.ID INNER JOIN wp_bogashare ON wp_bogashare.user_fb_id = wp_usermeta.meta_value INNER JOIN wp_posts ON wp_bogashare.post_id = wp_posts.ID WHERE wp_usermeta.meta_key = '_fbid' AND wp_usermeta.meta_value = wp_bogashare.user_fb_id ORDER BY wp_bogashare.date DESC", OBJECT );
	$results = array_map("unserialize", array_unique(array_map("serialize", (array)$results)));

	echo '<p>'. count($contestans_fb_ids, 0) .' concursantes totales.</p>';
	echo '<p>'. count($results, 0) .' concursantes registrados.</p>';
	echo '<p>'. (count($contestans_fb_ids, 0) - count($results, 0)) .' concursantes NO registrados.</p>';
	echo '<ul class="media-list">';
	foreach($results as $contestant){
		echo '<li class="media portada_posts"><a href="https://www.facebook.com/'. $contestant->user_fb_id . '"><img style="float: left;" class="media-object" src="https://graph.facebook.com/'. $contestant->user_fb_id . '/picture?type=large"></a><div class="media-body"><h4 class="media-heading">'. $contestant->display_name . '</h4><p> Compartió <strong>'. $contestant->post_title .'</strong></p></div></li>';
	}
	echo '</ul>';
}
add_shortcode('bogashare_contestants', 'show_bogashare_contestants');

add_action( 'phpmailer_init', 'send_smtp_email' );
