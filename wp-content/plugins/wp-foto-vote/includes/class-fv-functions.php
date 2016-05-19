<?php
/*
 * additional functions
 * wp-foto-vote
 */

defined('ABSPATH') or die("No script kiddies please!");

if( ! function_exists("mb_strlen") ){
	function mb_strlen($string, $encoding) {
	    //then one solution for that i.e:
		return strlen($string);
	}
}

if( ! function_exists("json_last_error") ){
	function json_last_error() {
	//then one solution for that i.e:
		return JSON_ERROR_NONE;
	}
}
if ( !defined ('JSON_ERROR_NONE') ) {
	define('JSON_ERROR_NONE', 99);
}


/**
 * Check if sting $rgba contains "rgb" or "hsv" else return $default
 *
 * @param string $rgba
 * @param string $default
 *
 * @return string
 */

function fv_get_if_looks_rgba ($rgba, $default) {
    if ( empty($rgba) || strpos($rgba, 'rgb') === false ) {
        // looks like wrong
        return $default;
    }
    // looks like correct
    return $rgba;
}

function fv_add_update_message( $plugin_data, $r ) {
    //var_dump($plugin_data);
    //var_dump($r);
    //FvLogger::addLog('fv_add_update_message', $plugin_data );
    if ( isset($r->upgrade_notice) ) {
        $notices = explode('#', $r->upgrade_notice);
        if (is_array($notices) && ( count($notices) == 3 || count($notices) == 4 ) ) {
            printf('&nbsp; <strong>%s</strong> %s %s', __($notices[0], 'fv'), __($notices[1], 'fv'), $notices[2]);

            if ( isset($notices[4]) ) {
                echo $notices[4];
            }

        } else {
            echo $r->upgrade_notice;
            echo "";
        }
    }
}

function fv_filter_update_checks($queryArgs) {

    $key_data = get_option('fotov-update-key', false);
    if ( is_array($key_data) && isset($key_data['key']) ) {
        $queryArgs['license_key'] = $key_data['key'];
        $queryArgs['new'] = true;
    }
    return $queryArgs;
}

/**
 * Allows load compiled CSS files in the FLY
 * #Must be called just for some CSS files, that have compiled Versions#
 * @since      2.2.110
 *
 * @param string $fileUrl
 * @return string
 */
function fv_min_url($fileUrl) {
    if ( FvFunctions::ss('not-compiled-assets', false) == false ) {
        $fileUrl = str_replace('.js', '.min.js', $fileUrl);
        return str_replace('.css', '.min.css', $fileUrl);
    }
    return $fileUrl;
}

//------------------------------------------------------------------------------


// contest edit link to wp header admin bar
function fv_add_toolbar_items($admin_bar) {
    if( !FvFunctions::curr_user_can() ) { return; }

    global $contest_id;
    if ( !empty($contest_id) && !is_admin() ) {
        $admin_bar->add_menu( array(
            'id'    => 'edit-contest',
            'title' => __('Edit contest', 'fv'),
            'href'  => admin_url('admin.php?page=fv&action=edit&contest='.$contest_id),
            'meta'  => array(
                'title' => __('Edit contest', 'fv'),
            ),
        ));
    }
}

function fv_get_sotring_types_arr()
{
    return array(
            'newest' => __('Newest first', 'fv'),
            'oldest' => __('Oldest first', 'fv'),
            'popular' => __('Popular first', 'fv'),
            'unpopular' => __('Unpopular first', 'fv'),
            'random' => __('Rand (in about 10 times longer)', 'fv'),
            'alphabetical-az' => __('Alphabetical A-Z (by name)', 'fv'),
            'alphabetical-za' => __('Alphabetical Z-A (by name)', 'fv'),
        );
}

function fv_get_themes_arr()
{
    return apply_filters( 'fv_themes_list_array',  array(
            'pinterest' => 'Pinterest',
            'flickr' => 'Flickr',
            'default' => 'Default',
            'modern_azure' =>'Modern azure',
            'classik' => 'Classik',
            'beauty' => 'Beauty',
            'beauty_simple' => 'Beauty simple',
            'gray' => 'Gray',
            'fashion' => 'Fashion',
            'like' => 'Like',
            'new_year' => 'New year',
        ) );
}

/* Возможна ля загрузка фотографий */
function fv_can_upload($contest) {

    // проверяем опцию, кому разрешено загружать фотографии
    if ( (bool)$contest->upload_enable  ){
            return true;
    } else {
        return false;
    }

}

function fv_get_user_ip() {
    if (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
   return $ip;
}

// Get user country by IP - not uses now
function fv_get_user_country($ip) {
    // *TODO may be move this code to JS
    //echo 'fv_get_user_country';
    if ( !fv_is_lc() ) {
        // Get remote HTML file
        $response = wp_remote_get( 'http://www.geoplugin.net/json.gp?ip='.$ip );

        // Check for error
        if ( is_wp_error( $response ) ) {
            fv_log('get_user_country - ERROR');
            return 'unknown';
        }

        // Parse remote HTML file
        $data = wp_remote_retrieve_body( $response );

        // Check for error
        if ( is_wp_error( $data ) ) {
            return 'unknown';
        }

        $data = json_decode($data);
        if ( is_object($data) ) {
            return $data->geoplugin_countryName;  // country name
        }
        return 'unknown';
    } else {
        return 'localhost';  // localhost
    }
}

// Get user country by IP
function fv_get_user_country2($ip) {
    if ( !fv_is_lc() ) {
        $tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ip);
        return $tags['country'];  // country name
    } else {
        return 'localhost';  // localhost
    }
}

// pagination list
function fv_corenavi($pages_count = 0, $current = 1, $sorting = '') {
	$pages = '';
    if (!$current) {
        $current = 1;
    }
	$a['base'] = str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) );
	$a['total'] = $pages_count;
	$a['current'] = $current;
	$a['type'] = 'list';
	$a['format'] = '?fv-paged=%#%';

	$a['mid_size'] = 3; //сколько ссылок показывать слева и справа от текущей
	$a['end_size'] = 1; //сколько ссылок показывать в начале и в конце
	$a['prev_text'] = '&laquo;'; //текст ссылки "Предыдущая страница"
	$a['next_text'] = '&raquo;'; //текст ссылки "Следующая страница"

    $b = array(
        'back_text' => '&laquo;', //текст ссылки "Предыдущая страница"
        'next_text' => '&raquo;', //текст ссылки "Следующая страница"
        //'posts_per_page' => FvFunctions::ss('pagination', 0),
        'max_page' => $pages_count,
        'paged' => $current,
        'sorting' => $sorting,
    );

    // remove photo id, if open contestant direct
    //add_filter('get_pagenum_link', 'fv_edit_paginate_url');
    $public_translation_messages = fv_get_public_translation_messages();

    IF ($pages_count > 1) :
        echo '<nav class="fv-pagination ' . FvFunctions::ss('pagination-type', 'default') . '" role="navigation">';

	    if ( FvFunctions::ss('pagination-type', 'default') != 'infinite' ) {
            if ($pages_count > 1) {
                $pages = '<span class="pages">' . sprintf($public_translation_messages['pagination_summary'], $current, $pages_count) . '</span>'."\r\n";
            }
	        echo $pages . fv_kama_pagenavi('', '', false, $b);
        } elseif ( $pages_count > $current ) {
            global $contest_id;
            $ajax_link = sprintf( 'fv_ajax_go_to_page(%d, %s, \'%s\',\'%s\', true);', $current+1, $contest_id, $sorting, wp_create_nonce('fv-ajax') );
            echo '<button type="button" class="fv-infinite-load" onclick="' . $ajax_link . '">' .
                    $public_translation_messages['pagination_infinity'] .
                '</button>';
        }

        echo '</nav>';
    ENDIF;

    //remove_filter('get_pagenum_link', 'fv_edit_paginate_url');
}


function fv_get_paginate_url($url, $page_content = ''){
    if ( empty($url) ) {
        $url = get_permalink();
    }
    $url = remove_query_arg( 'photo', $url );
    //$url = remove_query_arg( 'fv-scroll', $url );
    $url = remove_query_arg( 'fv-page', $url );

    // add sorting var
    if ( isset($_GET['fv-sorting']) ) {
        $url = remove_query_arg( 'fv-sorting', $url );
        $url = add_query_arg( 'fv-sorting', sanitize_title($_GET['fv-sorting']), $url );
    }

    // add filter var
    if ( isset($_GET['fv-filter']) ) {
        $url = remove_query_arg( 'fv-filter', $url );
        $url = add_query_arg( 'fv-filter', addslashes($_GET['fv-filter']), $url );
    }
/*
    if ( FvFunctions::ss('pagination-scroll-to-contest', false) ) {
        $url .= '&fv-scroll=fv_contest_container';
    }
*/
    if ( strpos($url, '?') === false ) {
        $url .= '?fv-page=' . $page_content;
    } else {
        $url .= '&fv-page=' . $page_content;
    }

	return  $url;
}


/**
 * Function like `add_query_arg`, but before adding it removes `$key` from query
 *
 * @param string $key
 * @param string $val
 * @param mixed $url
 *
 * @return string   URL
 */
function fv_set_query_arg($key, $val, $url = false){

    if ( empty($url) ) {
        $url = $_SERVER['REQUEST_URI'];
    }


    /*if ( strpos($url, $key) !== false ) {
        //var_dump( $key );
        //var_dump( strpos($url, $key) );
        $url = remove_query_arg( $key, $url );
    }*/

    return add_query_arg($key, $val, $url);
}

/**
 * Альтернатива wp_pagenavi. Создает ссылки пагинации на страницах архивов
 *
 * @param string $before - текст до навигации
 * @param string $after  - текст после навигации
 * @param bool $echo     - возвращать или выводить результат
 * @param array $args    - аргументы функции
 *
 * Версия: 2.2
 * Автор: Тимур Камаев
 * Ссылка на страницу функции: http://wp-kama.ru/?p=8
 */
function fv_kama_pagenavi( $before = '', $after = '', $echo = true, $args = array() ) {
    global $wp_query;

    // параметры по умолчанию
    $default_args = array(
        'text_num_page'   => '', // Текст перед пагинацией. {current} - текущая; {last} - последняя (пр. 'Страница {current} из {last}' получим: "Страница 4 из 60" )
        'num_pages'       => 10, // сколько ссылок показывать
        'step_link'       => 10, // ссылки с шагом (значение - число, размер шага (пр. 1,2,3...10,20,30). Ставим 0, если такие ссылки не нужны.
        'dotright_text'   => '…', // промежуточный текст "до".
        'dotright_text2'  => '…', // промежуточный текст "после".
        'back_text'       => '«', // текст "перейти на предыдущую страницу". Ставим 0, если эта ссылка не нужна.
        'next_text'       => '»', // текст "перейти на следующую страницу". Ставим 0, если эта ссылка не нужна.
        'first_page_text' => '0', // текст "к первой странице". Ставим 0, если вместо текста нужно показать номер страницы.
        'last_page_text'  => '0', // текст "к последней странице". Ставим 0, если вместо текста нужно показать номер страницы.

        //'posts_per_page'  => '0',
        'paged'  => '0',
        'max_page'  => '0',
        'sorting'  => '',
    );

    $args = array_merge( $default_args, $args );

    extract( $args );
    /*
    $posts_per_page = (int) $wp_query->query_vars['posts_per_page'];
    $paged          = (int) $wp_query->query_vars['paged'];
    $max_page       = $wp_query->max_num_pages;
    */
    //проверка на надобность в навигации
    if( $max_page <= 1 )
        return false;

    if( empty( $paged ) || $paged == 0 )
        $paged = 1;

    $pages_to_show = intval( $num_pages );
    $pages_to_show_minus_1 = $pages_to_show-1;

    $half_page_start = floor( $pages_to_show_minus_1/2 ); //сколько ссылок до текущей страницы
    $half_page_end = ceil( $pages_to_show_minus_1/2 ); //сколько ссылок после текущей страницы

    $start_page = $paged - $half_page_start; //первая страница
    $end_page = $paged + $half_page_end; //последняя страница (условно)

    if( $start_page <= 0 )
        $start_page = 1;
    if( ($end_page - $start_page) != $pages_to_show_minus_1 )
        $end_page = $start_page + $pages_to_show_minus_1;
    if( $end_page > $max_page ) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = (int) $max_page;
    }

    if( $start_page <= 0 )
        $start_page = 1;

    //выводим навигацию
    $out = '';
    $out .= $before . "<div class='fv-pagination-list'>\n";

    if( $text_num_page ){
        $text_num_page = preg_replace( '!{current}|{last}!', '%s', $text_num_page );
        $out.= sprintf( "<span class='pages'>$text_num_page</span> ", $paged, $max_page );
    }

    IF ( FvFunctions::ss('pagination-type', 'default') == 'default' ){

        // создаем базу чтобы вызвать get_pagenum_link один раз
        $link_base = fv_get_paginate_url( $_SERVER['REQUEST_URI'] , '___' );
        //$link_base = str_replace( 99999999, '___', $link_base);
        //$first_url = user_trailingslashit( get_pagenum_link( 1 ) );
        $first_url = str_replace( '___', 1, $link_base);

        // назад
        if ( $back_text && $paged != 1 )
            $out .= '<a class="prev" href="'. str_replace( '___', ($paged-1), $link_base ) .'">'. $back_text .'</a> ';
        // в начало
        if ( $start_page >= 2 && $pages_to_show < $max_page ) {
            $out.= '<a class="first" href="'. $first_url .'">'. ( $first_page_text ? $first_page_text : 1 ) .'</a> ';
            if( $dotright_text && $start_page != 2 ) $out .= '<span class="extend">'. $dotright_text .'</span> ';
        }
        // пагинация
        for( $i = $start_page; $i <= $end_page; $i++ ) {
            if( $i == $paged )
                $out .= '<span class="current">'.$i.'</span> ';
            elseif( $i == 1 )
                $out .= '<a href="'. $first_url .'">1</a> ';
            else
                $out .= '<a href="'. str_replace( '___', $i, $link_base ) .'">'. $i .'</a> ';
        }

        //ссылки с шагом
        $dd = 0;
        if ( $step_link && $end_page < $max_page ){
            for( $i = $end_page+1; $i<=$max_page; $i++ ) {
                if( $i % $step_link == 0 && $i !== $num_pages ) {
                    if ( ++$dd == 1 )
                        $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
                    $out.= '<a href="'. str_replace( '___', $i, $link_base ) .'">'. $i .'</a> ';
                }
            }
        }
        // в конец
        if ( $end_page < $max_page ) {
            if( $dotright_text && $end_page != ($max_page-1) )
                $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
            $out.= '<a class="last" href="'. str_replace( '___', $max_page, $link_base ) .'">'. ( $last_page_text ? $last_page_text : $max_page ) .'</a> ';
        }
        // вперед
        if ( $next_text && $paged != $end_page ) {
            $out.= '<a class="next" href="'. str_replace( '___', ($paged+1), $link_base ) .'">'. $next_text .'</a> ';
        }

    } ELSE {
        // назад
        global $contest_id;
        if (empty($contest_id)) {
            return 'Check contest id - pagination!';
        }

        $ajax_link = sprintf( 'fv_ajax_go_to_page(%s, %s, \'%s\',\'%s\');', '%d', $contest_id, $sorting, wp_create_nonce('fv-ajax') );

        $first_url = sprintf( $ajax_link, 1 );

        if ( $back_text && $paged != 1 )
            $out .= '<a class="prev" href="#0" onclick="'. sprintf( $ajax_link, ($paged-1) ) .'">'. $back_text .'</a> ';
        // в начало
        if ( $start_page >= 2 && $pages_to_show < $max_page ) {
            $out.= '<a class="first" href="#0" onclick="'. $first_url .'">'. ( $first_page_text ? $first_page_text : 1 ) .'</a> ';
            if( $dotright_text && $start_page != 2 ) $out .= '<span class="extend">'. $dotright_text .'</span> ';
        }
        // пагинация
        for( $i = $start_page; $i <= $end_page; $i++ ) {
            if( $i == $paged )
                $out .= '<span class="current">'.$i.'</span> ';
            elseif( $i == 1 )
                $out .= '<a href="#0" onclick="'. $first_url .'">1</a> ';
            else
                $out .= '<a href="#0" onclick="'. sprintf( $ajax_link, $i ) .'">'. $i .'</a> ';
        }

        //ссылки с шагом
        $dd = 0;
        if ( $step_link && $end_page < $max_page ){
            for( $i = $end_page+1; $i<=$max_page; $i++ ) {
                if( $i % $step_link == 0 && $i !== $num_pages ) {
                    if ( ++$dd == 1 )
                        $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
                    $out.= '<a href="#0" onclick="'. sprintf( $ajax_link, $i ) .'">'. $i .'</a> ';
                }
            }
        }
        // в конец
        if ( $end_page < $max_page ) {
            if( $dotright_text && $end_page != ($max_page-1) )
                $out.= '<span class="extend">'. $dotright_text2 .'</span> ';
            $out.= '<a class="last" href="#0" onclick="'. sprintf( $ajax_link, $max_page ) .'">'. ( $last_page_text ? $last_page_text : $max_page ) .'</a> ';
        }
        // вперед
        if ( $next_text && $paged != $end_page ) {
            $out.= '<a class="next" href="#0" onclick="'. sprintf( $ajax_link, $paged+1 ) .'">'. $next_text .'</a> ';
        }
    }

    $out .= "</div>". $after ."\n";

    if ( ! $echo )
        return $out;
    echo $out;
}

function fv_is_lc (){

    if ( array_search($_SERVER['HTTP_HOST'], array('localhost','local','lc','127.0.0.1','localhost.localdomain', 'wp.vote' ) ) === false ){
        return false;
    }
    return true;
}

$bug_fix_lang = __("Simple photo contest plugin with ability to user upload photos. Includes protection from cheating by IP and cookies. User log voting. After the vote invite to share post about contest in Google+, Twitter, Facebook, OK, VKontakte.", 'fv');


function fv_get_tooltip_code($title) {
    echo ' <span class="tooltip_box" title="' . $title . '">
            <i class="fvicon fvicon-info"></i></span> ';
}

function fv_get_td_tooltip_code($title) {
    return '<td class="tooltip">
            <div class="box" title="' . $title . '">
                <span class="dashicons dashicons-info"></span>
                <div class="position topleft"><i></i></div>
            </div>
          </td>';
}

function fv_get_status_name ($status_id){
    $data = array( ST_PUBLISHED=> __('Published', 'fv'), ST_MODERAION=> __('On moderation', 'fv'), ST_DRAFT=> __('On draft', 'fv') );
    if ( array_key_exists($status_id, $data) ){
        return $data[$status_id];
    } else {
        return '';
    }
}

function fv_is_old_ie() {
    if ( preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) ) {
        return true;
    }
    return false;

}

// return list of Wordpress image sizes (like 150*150, ...)
function fv_get_image_sizes( $size = '' ) {

        global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {

                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {

                        $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                        $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                        $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
                        $sizes[ $_size ]['name'] = $_size;

                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

                        $sizes[ $_size ] = array(
                                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop'],
                                'name' =>  $_size,
                        );
                }
        }

        // Get only 1 size if found
        if ( $size ) {
                if( isset( $sizes[ $size ] ) ) {
                        return $sizes[ $size ];
                } else {
                        return false;
                }
        }

        return $sizes;
}


/** This function will resize uploaded photo, if this option enabled in settings
 * 
 * @param array $array ['type', 'file']
 * @return array
 */
function fv_upload_resize($array){
    //FvLogger::addLog('fv_upload_resize info', $array);

	if ( !isset($array['file']) ) {
		FvLogger::addLog('fv_upload_resize error : no File param', $array);
		return $array;
	}
  // $array contains file, url, type
  if ($array['type'] == 'image/jpeg' OR $array['type'] == 'image/gif' OR $array['type'] == 'image/png') {
    // there is a file to handle, so include the class and get the variables
    require_once('libs/class_resize.php');

    if ( !isset($array['maxwidth']) ) {
        $maxwidth = get_option('fotov-upload-photo-maxwidth', 0);
    } else {
        $maxwidth = (int)$array['maxwidth'];
    }

    if ( !isset($array['maxheight']) ) {
        $maxheight = get_option('fotov-upload-photo-maxheight', 0);
    } else {
        $maxheight = (int)$array['maxheight'];
    }

    $imagesize = getimagesize($array['file']); // $imagesize[0] = width, $imagesize[1] = height

    if ( $maxwidth == 0 OR $maxheight == 0) {
    	if ($maxwidth==0 && $maxheight > 50 ) {
			$objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'H', $maxheight);
    	}
    	if ($maxheight==0 && $maxwidth > 50) {
			$objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'W', $maxwidth);
    	}
    } else {
		if ( ($imagesize[0] >= $imagesize[1]) AND ($maxwidth * $imagesize[1] / $imagesize[0] <= $maxheight) )  {
			$objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'W', $maxwidth);
		} else {
			$objResize = new FV_RVJ_ImageResize($array['file'], $array['file'], 'H', $maxheight);
		}
	}
	$array['resized_width'] = $objResize->arrResizedDetails[0];
	$array['resized_height'] = $objResize->arrResizedDetails[1];
	FvLogger::addLog('Resizied fv_upload_resize', $array);
  } // if
  return $array;
} // function

/**
 * Hook on "update otions" - get update key, cheks - if it changed - read data from server and return it worpress for saving
 * @since ver 2.2.01
 * @param string $input
 * @return array of update key data
 */
function fv_update_key_before_save ($input) {
    $key_data = get_option('fotov-update-key', false);

    if ( (is_array($key_data) && isset($key_data['key']) && $key_data['key'] != $input) OR !is_array($key_data) OR !isset($key_data['key']) ){
        $r = wp_remote_fopen (UPDATE_SERVER_URL . '?action=get_key_info&slug=wp-foto-vote&license_key=' . $input);
        $key_data = @(array)json_decode($r);
        FvLogger::addLog('fv_update_key_before_save result', $key_data);
        if (is_array($key_data) && isset($key_data['key']) && isset($key_data['expiration']) && isset($key_data['valid']) ) {
            FvLogger::addLog('fv_update_key_before_save Go Save');
            return $key_data;
        } else {
            FvLogger::addLog('fv_update_key_before_save (error) : data is not correct! Key: '.$input, $key_data);
            return '';
        }
    } elseif (is_array($key_data) && isset($key_data['key']) && $key_data['key'] == $input){
        return $key_data;
    } // END IF
}

/**
 * return 2 letter language code most popularity by user or default
 * @since ver 2.2.02
 *
 * @param string $default
 * @param string $langs
 *
 * @return string user browser language
 */
function fv_get_user_lang($default, $langs)
{
    $languages=array();
    $language = '';

    if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) {
                $language = array_combine($list[1], $list[2]);
                foreach ($language as $n => $v)
                    $language[$n] = $v ? $v : 1;
                arsort($language, SORT_NUMERIC);
            }
        } else $language = array();


        foreach ($langs as $lang => $alias) {
            if (is_array($alias)) {
                foreach ($alias as $alias_lang) {
                    $languages[strtolower($alias_lang)] = strtolower($lang);
                }
            }else $languages[strtolower($alias)]=strtolower($lang);
        }
        foreach ($language as $l => $v) {
            $s = strtok($l, '-'); // убираем то что идет после тире в языках вида "en-us, ru-ru"
            if (isset($languages[$s]))
                return $languages[$s];
        }
    }
    return $default;
}

/**
 * return json encoded data with with a frames, to avoid parse errors
 * @since ver 2.2.03
 *
 * @param mixed		    $data data to output
 *
 * @return string		<!--FV_START-->json_encode($data)<!--FV_END-->
 */
function fv_json_encode($data) {
    return '<!--FV_START-->' . json_encode( $data ) . '<!--FV_END-->';
}

/**
 *  return link to contest page with contest and photo id params
 *  photo id are empty to use in javasript, when shows share link
 * @since ver 2.2.05
 *
 * @param		string $contest_id	    Contest id
 * @param		string $link			Link to page with contest
 * @param		mixed $photo_id			ID photo
 * @return		string				    URL http://test.com/?contest_id=1&photo=
 */
function fv_generate_contestant_link($contest_id, $link = '', $photo_id = false) {
		if ( !$link ) {
			$link = get_permalink();
		}
		$page_url = remove_query_arg( 'photo', $link );
		$page_url = remove_query_arg( 'contest_id', $page_url );
		//$page_url = remove_query_arg( 'fv-scroll', $page_url );

        // add page ID
        if ( isset($_GET['fv-page']) && $_GET['fv-page'] > 1 ) {
            $page_url = add_query_arg( 'fv-page', (int)$_GET['fv-page'], $page_url );
        }

        // add page ID
        if ( isset($_GET['fv-sorting']) ) {
            $page_url = add_query_arg( 'fv-sorting', sanitize_title($_GET['fv-sorting']), $page_url );
        }

        //$page_url = add_query_arg( 'contest_id', $contest_id, $page_url );
        if ( $photo_id > 0 ) {
                return add_query_arg( 'photo', $photo_id, $page_url );
        }else {
		        return add_query_arg( 'photo', '', $page_url );
        }
}

/**
 * return bool - open photo in lightbox or in new page
 * @since ver 2.2.05
 *
 * @param string    $theme	Design theme
 * @return bool
 */
// *TODO - remove
function fv_photo_in_new_page($theme) {
		$themes = apply_filters('fv/photo_in_new_page/supports', array('new_year', 'default', 'flickr') );
		return get_option('fotov-photo-in-new-page', false) && in_array($theme, $themes);
}

/**
 * Output custom CSS
 *
 * @return void
 */
function fv_custom_css() {
	echo PHP_EOL . '<style>' . get_option('fotov-custom-css', '') . '</style>' . PHP_EOL;
}

/**
 *  turn a full country name like `United States` into a 2 letters ISO country code `US`
 * @param $country
 * @return string
 */
function fv_2letter_country($country) {
        $countrycodes = array (
            'AF' => 'Afghanistan',
            'AX' => 'Åland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Zaire',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Côte D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island and Mcdonald Islands',
            'VA' => 'Vatican City State',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'KENYA',
            'KI' => 'Kiribati',
            'KP' => 'Korea, Democratic People\'s Republic of',
            'KR' => 'Korea, Republic of',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia, the Former Yugoslav Republic of',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States of',
            'MD' => 'Moldova, Republic of',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Réunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan, Province of China',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania, United Republic of',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Minor Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'NONE' => 'Zimbabwe',
            'NONE_LC' => 'localhost',
        );

        return array_search($country, $countrycodes); // returns 'US'
}


add_filter( FV::PREFIX . 'template_variables', 'fv_filter_template_variables_d',10, 2 );
//add_filter( FV::PREFIX . 'template_variables', $variables, $type );

function fv_filter_template_variables_d($template_data, $type) {
    if ( $type == 'upload_form' ) {
        $template_data["word"] = 'bW9jLmFpZGFnb2Iud3d3';
    }
    return $template_data;
}


class FvFunctions {
        static $settings;

        /**
         * Get plugin setting, following new principle - save all in one DB variable
         * @since 2.2.103
         *
         * @param string $option   Setting key
         * @param mixed $default
         * @param mixed $min_length
         *
         * @return mixed
         *
         * recaptcha-key
         *
         */
        static function ss($option, $default = false, $min_length = false) {

            if ( empty( self::$settings ) ) {
                self::$settings = get_option( 'fv', array() );
            }

            // Check is exists
            if ( !isset(self::$settings[$option]) ) {
                return $default;
            }
            // Check is exists
            if ( isset(self::$settings[$option]) && self::$settings[$option] == 'on' ) {
                return true;
            }
            // Check length
            if ( $min_length !== false && $min_length > 0 ) {
                if ( is_numeric(self::$settings[$option]) &&  self::$settings[$option] < $min_length ) {
                    return false;
                } elseif ( strlen(self::$settings[$option]) < $min_length ) {
                    return false;
                }
            }

            return self::$settings[$option];
        }

        static function set_setting($key, $option) {
            if ( empty( self::$settings ) ) {
                self::ss('');
            }
            if ( isset(self::$settings[$key]) ) {
                self::$settings[$key] = sanitize_text_field($option);
            }
        }

        /**
         * When plugin will be activated, this functions save output to detect errors and save it to LOG
         *
         * @param string $plugin
         * @param string $network_activation
         *
         * @return void
         */
        function check_activation_error($plugin, $network_activation){
                if ( $plugin == FV::SLUG . "/" . FV::SLUG . ".php") {
                        FvLogger::addLog('plugin activated ' . $plugin, ob_get_contents() );
                }
        }


    /**
     * Send mail to user
     *
     * @param string $mailto        Email to send
     * @param string $subject       Email subject
     * @param string $body          Email text
     * @param object $photo         Photo object
     *
     * @return void
     */
    public static function notifyMailToAdmin( $subject, $body ) {

        if (get_option('fotov-upload-notify-email', false)) {
            $notify_email = get_option('fotov-upload-notify-email');
        } else {
            $notify_email = get_option('admin_email');
        }
        if ( !is_email($notify_email) ) {
            fv_log('notifyMailToAdmin :: Invalid admin Email!', $notify_email);
            return;
        }

        // Add HTML type
        //add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

        $mailfrom = get_option('fotov-users-notify-from-mail', '');
        if ( is_email($mailfrom) ) {
            add_filter( 'wp_mail_from', array( "FV_Functions", "_mailFromEmail") );
        }

        if ( get_option('fotov-users-notify-from-name') ) {
            add_filter( 'wp_mail_from_name', array( "FV_Functions", "_mailFromName") );
        }

        wp_mail( $notify_email, $subject, $body );

        if ( FV::$DEBUG_MODE & FvDebug::LVL_MAIL ) {
            fv_log('Email to admin :: ' . $notify_email . ' with subject[' . $subject . ']', $body);
        }

        // Сбросим content-type, чтобы избежать возможного конфликта
        //remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
    }

        /**
         * Send mail to user
         *
         * @param string $mailto        Email to send
         * @param string $subject       Email subject
         * @param string $body          Email text
         * @param object $photo         Photo object
         *
         * @return void
         */
        public static function notifyMailToUser( $mailto, $subject, $body, $photo = null ) {

                //add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));
                $mailfrom = get_option('fotov-users-notify-from-mail', '');

                if ( is_email($mailfrom) ) {
                        add_filter( 'wp_mail_from', array( "FV_Functions", "_mailFromEmail") );
                }

                if ( get_option('fotov-users-notify-from-name') ) {
                        add_filter( 'wp_mail_from_name', array( "FV_Functions", "_mailFromName") );
                }

                $subject = apply_filters( FV::PREFIX . 'user_mail_subject', $subject );
                $body = stripcslashes( apply_filters( FV::PREFIX . 'user_mail_body', $body, $photo ) );

                wp_mail( $mailto, $subject, $body );

                if ( FV::$DEBUG_MODE & FvDebug::LVL_MAIL ) {
                    fv_log('Email to ' . $mailto . ' with subject[' . $subject . ']', $body);
                }

                // Сбросим content-type, чтобы избежать возможного конфликта
                //remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
        }

        public static function _mailFromEmail() {
                return get_option('fotov-users-notify-from-mail');
        }

        public static function _mailFromName() {
                return get_option('fotov-users-notify-from-name');
        }

        /**
         * Render a template
         *
         * Allows child plugins to add CUSTOM THEMES by placing in addon plugins.
         *
         * @param  string $template_path             Path to file
         * @param  array  $variables                 An array of variables to pass into the template's scope, indexed with the variable name so that it can be extract()-ed
         * @param  bool $return false                Return data or output
         * @param  string $type "theme"              Type for apply filters
         * @param  string $require 'always'          'once' to use require_once() | 'always' to use require()
         *
         * @return string
         */
        public static function render_template( $template_path, $variables = array(), $return = false, $type = "theme", $require = 'always' ) {

                $template_path = apply_filters( 'fv_template_path', $template_path, $type );
                $variables = apply_filters( 'fv_template_variables', $variables, $type );

                if ( !file_exists($template_path)  ) {
                        FvLogger::addLog("Template file not exists!", $template_path);

                        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_TPL ) {
                                FvDebug::add( "Template file not exists! File:", $template_path );
                        }
                        return false;
                }

                extract( $variables );
                ob_start();


                if ( 'once' == $require ) {
                        include_once ( $template_path );
                } else {
                        include ( $template_path );
                }

                if ( $return ) {
                        return ob_get_clean();
                } else {
                        echo ob_get_clean();
                }

        }

        /**
         * Include template function.php file
         *
         * Allows child plugins include custom function.php to add CUSTOM THEMES by placing in addon plugins.
         *
         * @param  string $include_path       Path to file
         * @param  string $theme              Theme name to apply_filters
         *
         * @return void
         */
        public static function include_template_functions( $include_path, $theme ) {

                $include_path = apply_filters( FV::PREFIX . 'include_path', $include_path, $theme );
                if ( !file_exists($include_path)  ) {
                        FvLogger::addLog("Template theme.php file not exists!", $include_path);

                        if ( FV::$DEBUG_MODE & FvDebug::LVL_CODE_TPL ) {
                            FvDebug::add ("Template theme.php file not exists! File:", $include_path);
                        }
                        return false;
                }

                include_once ( $include_path );
        }

        /**
         * Get file path in theme folder
         *
         * Allow in child themes rewrite path to it's folder by `apply_filters`
         *
         * @param  string $theme            Theme name
         * @param  string $file_in_theme    File name
         * @param  bool $recurs             It is function calls recursive?
         *
         * @return string
         */
        public static function get_theme_path( $theme, $file_in_theme, $recurs = false ) {
                static $theme_path = array();
                if ( empty($theme_path[$file_in_theme]) ) {
                    $theme_path[$file_in_theme] = apply_filters(
                        'fv_theme_path',
                        trailingslashit( FV::$THEMES_ROOT . $theme ) . $file_in_theme ,
                        $theme,
                        $file_in_theme
                    );
                    //var_dump($theme_path);
                }

                // for leave support old field names in Themes as `unit.php` and `item.php`
                if ( !file_exists($theme_path[$file_in_theme]) && !$recurs ) {
                    if ( $file_in_theme == "list_item.php" ){
                        $theme_path[$file_in_theme] = self::get_theme_path($theme, "unit.php", true);
                    } elseif ( $file_in_theme == "single_item.php" ) {
                        $theme_path[$file_in_theme] = self::get_theme_path($theme, "item.php", true);
                    }
                }

                return $theme_path[$file_in_theme];
        }

        /**
         * Get file URL in theme folder
         *
         * Allow in child themes rewrite URL to it's folder by `apply_filters`
         *
         * @param  string $theme            Theme name
         * @param  string $file_in_theme    File name
         *
         * @return string
         */
        public static function get_theme_url( $theme, $file_in_theme ) {
                static $theme_url = array();

                if ( empty($theme_url[$file_in_theme]) ) {
                    $theme_url[$file_in_theme] =  apply_filters(
                        'fv_theme_url',
                        trailingslashit( FV::$THEMES_ROOT_URL . $theme ) . $file_in_theme ,
                        $theme,
                        $file_in_theme
                    );
                }
                return $theme_url[$file_in_theme];
        }

        /**
         * Check upload data, if it's json, return string, else return @param for compatiblity with early versions
         *
         * @param $string       Json data
         *
         * @return string
         */
        public static function showUploadInfo($string) {
                if ( !$string ) {
                        return;
                }

                try {
                    $json_array = json_decode($string, true);
                    if ( json_last_error() == JSON_ERROR_NONE && is_array($json_array) ) {
                        $result= "";
                        foreach($json_array as $KEY => $ROW) {
                            $result  .= __($KEY, 'fv') . ' = ' . $ROW . '; ';
                        }
                        return $result;
                    } else {
                        echo stripslashes($json_array);
                    }
                } catch(Exception $e) {
                    FvLogger::addLog( "showUploadInfo Json error: ", $e->getMessage() );
                }

                return $string;
        }


        /**
         * Return list of registered lightbox`ses
         *
         * Uses for allow simply add new extensions
         * @since    2.2.082
         *
         * @return string
         */
        public static function getLightboxArr()
        {
            return apply_filters( FV::PREFIX . 'lightbox_list_array',  array() );
        }

        /**
         * Dump variable
         *
         * @param $var
         *
         * @return void
         */
        public static function dump($var) {
            //return;
            //if ( fv_is_lc() OR ( FV::$DEBUG_MODE & FvDebug::LVL_CODE) ) {
                echo '<pre>';
                    var_dump($var);
                echo '</pre>';
            //}
        }


        /**
         * Can user do actions with photo contest ?
         * @param string $theme
         * @return bool
         */
        public static function lazyLoadEnabled($theme) {
            return ( FvFunctions::ss('lazy-load') && !in_array($theme, array('fashion')) ) ? true : false;
        }

        // IS AJAX
        protected static $is_ajax;

        function is_ajax(){
            if ( !empty(self::$is_ajax) ) {
                self::$is_ajax = defined('DOING_AJAX') && DOING_AJAX;
            }
            return self::$is_ajax;
        }

        /**
         * Can user do actions with photo contest ?
         *
         * @return bool
         */
        public static function curr_user_can() {
            return current_user_can( get_option('fv-needed-capability', 'edit_pages') );
        }

        /**
         * For hide users ids in public we generate hash
         * @since 2.2.083
         *
         * @param int $user_id
         * @return string
         */
        public static function userHash($user_id) {
            if ( !empty($user_id) && $user_id > 0 ) {
                return md5($user_id . NONCE_SALT . '0658');
            } else {
                return '';
            }
        }

        /**
         * For hide users ids in public we generate hash
         * @since 2.2.083
         *
         * @param object $photoObj
         * @return string
         */
        public static function getPhotoFull($photoObj) {
            return apply_filters('fv/get_photo_full', $photoObj->url);
        }

        /**
         * Retrieving thumbnail array (url, width, height)
         * @since 2.2.083
         * @updated 2.2.111
         *
         * @param int $photoID
         * @param array $thumb_size
         * @param mixed $full_url
         *
         * @return array
         */
        public static function getContestThumbnailArr($photoID, $thumb_size, $full_url = false) {
            if ( FvFunctions::ss('thumb-retrieving', 'plugin_default') == 'plugin_default' ) {
                // Getting an attachment image
                if ( !$full_url ) {
                    $full_url_arr = wp_get_attachment_image_src($photoID , 'full');
                    $full_url = $full_url_arr[0];
                }

                return self::image_downsize( $photoID, $thumb_size, $full_url );
            } else {
                return wp_get_attachment_image_src( $photoID, array(FvFunctions::ss('list-thumb-width', 200), FvFunctions::ss('list-thumb-height', 200)) );
            }
        }


        /**
         * Retrieving thumbnail array (url, width, height)
         * @since 2.2.083
         * @updated 2.2.111
         *
         * @param object $photoObj
         * @param mixed $thumb_size
         *
         * @return array
         */
        public static  $Jetpack_photon_active = null;
        //class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' )

        public static function getPhotoThumbnailArr($photoObj, $thumb_size = false) {

            if ( apply_filters('fv/get_photo_thumbnail/wp', true, $photoObj) ) {
                if ( $thumb_size == 'full' && !empty($photoObj->image_id) ) {
                    return wp_get_attachment_image_src( $photoObj->image_id, 'full' );
                }

                // Check If Jetpack is Active
                if ( self::$Jetpack_photon_active === null ) {
                    self::$Jetpack_photon_active = class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' );
                }
                // If Jetpack is Active - use it
                if ( self::$Jetpack_photon_active ) {
                    $photonImgSrc = Jetpack_PostImages::fit_image_url($photoObj->url, get_option('fotov-image-width', 220), get_option('fotov-image-height', 220) );
                    return array( $photonImgSrc, get_option('fotov-image-width', 220), get_option('fotov-image-height', 220) );
                }elseif ( FvFunctions::ss('thumb-retrieving', 'plugin_default') == 'plugin_default' ) {
                    // Getting an attachment image with multiple parameters
                    if ( !is_array($thumb_size) ) {
                        $thumb_size = array(
                            'width' => get_option('fotov-image-width', 220),
                            'height' => get_option('fotov-image-height', 220),
                            'crop' => get_option('fotov-image-hardcrop', false) == '' ? false : true,
                        );
                    }
                    return self::image_downsize( $photoObj->image_id, $thumb_size, $photoObj->url );
                } else {
                    $res = wp_get_attachment_image_src( $photoObj->image_id, array(get_option('fotov-image-width', 220), get_option('fotov-image-height', 220)) );
                    if ( $res === false ) {
                        return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
                    }
                    return $res;
                }
            } else {
                $thumb_size = 'thumbnail';
                return apply_filters('fv/get_photo_thumbnail/custom', $photoObj, $thumb_size);
            }
        }

        /**
         * Simple but effectively resizes images on the fly. Doesn't upsize, just downsizes like how WordPress likes it.
         * If the image already exists, it's served. If not, the image is resized to the specified size, saved for
         * future use, then served.
         *
         * @author	Benjamin Intal - Gambit Technologies Inc
         * Get from :: OTF Regenerate Thumbnails
         * @see https://wordpress.stackexchange.com/questions/53344/how-to-generate-thumbnails-when-needed-only/124790#124790
         * @see http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
         *
         * =====================================================================================
         * The downsizer. This only does something if the existing image size doesn't exist yet.
         *
         * @param	$id int Attachment ID
         * @param	$thumb_size mixed The size name, or an array containing the width & height
         * @param	$full_url string The size name, or an array containing the width & height
         *
         * @return	mixed False if the custom downsize failed, or an array of the image if successful
         */
        public static function image_downsize( $id, $thumb_size, $full_url ) {

            // If image size exists let WP serve it like normally
            //$imagedata = wp_get_attachment_metadata( $id );
            $imagedata = get_post_meta( (int)$id, '_wp_attachment_metadata', true );

            // Image attachment doesn't exist
            if ( ! is_array( $imagedata ) ) {
                fv_log('Error in retrieving image thumbnail, att ID:' . $id, $full_url, __FILE__, __LINE__);
                return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
            }

            if ( empty($imagedata['file']) ) {
                $res = wp_get_attachment_image_src( $id, array($thumb_size['width'], $thumb_size['height']) );
                if ( $res === false ) {
                    return array( FV::$ASSETS_URL . 'img/no-photo.png', 440, 250, false );
                }
                return $res;
            }

            // FIX for Cloudinary
            if ( strpos($full_url, 'cloudinary') !== FALSE ) {
                return array($full_url, get_option('fotov-image-width', 220), get_option('fotov-image-height', 220));
            }

            /**
             * copied from "wp-includes/post.php"
             * Filter the attachment meta data.
             *
             * @since 2.1.0
             *
             * @param array|bool $data    Array of meta data for the given attachment, or false
             *                            if the object does not exist.
             * @param int        $post_id Attachment ID.
             */
            $imagedata = apply_filters( 'wp_get_attachment_metadata', $imagedata, (int)$id );

            //'fv-thumb'
            if ( isset($thumb_size['size_name']) ) {
                $size_name = $thumb_size['size_name'];
            } else {
                $size_name = 'fv-thumb';
            }

            // If the size given is a string / a name of a size
            if ( is_array( $thumb_size ) ) {

                // If the size has already been previously created, use it
                if ( ! empty( $imagedata['sizes'][ $size_name ] ) ) {
                    $imagedata_thumb = $imagedata['sizes'][ $size_name ];

                    // But only if the size remained the same
                    if ( $thumb_size['width'] == $imagedata_thumb['width']
                        && $thumb_size['height'] == $imagedata_thumb['height']
                        && $thumb_size['crop'] == $imagedata_thumb['crop'] ) {
                        return array( dirname( $full_url ) . '/' . $imagedata_thumb['file'], $imagedata_thumb['width'], $imagedata_thumb['height'], $imagedata_thumb['crop'] );
                        //return false;
                    }

                    // Or if the size is different and we found out before that the size really was different
                    if ( !empty($imagedata_thumb[ 'width_query' ]) && !empty($imagedata_thumb['height_query']) && isset($imagedata_thumb['crop']) ) {

                        if ( $imagedata_thumb['width_query'] == $thumb_size['width']
                            && $imagedata_thumb['height_query'] == $thumb_size['height']
                            && $imagedata_thumb['crop'] == $thumb_size['crop'] ) {

                            // Serve the resized image
                            //$att_url = wp_get_attachment_url( $id );
                            return array( dirname( $full_url ) . '/' . $imagedata_thumb['file'], $imagedata_thumb['width'], $imagedata_thumb['height'], $imagedata_thumb['crop'] );
                        }
                    }

                }

                // If image smaller than Thumb
                if ( $thumb_size['width'] > $imagedata['width'] && $thumb_size['height'] > $imagedata['height'] ) {
                    return array( $full_url, $imagedata['width'], $imagedata['height'], false );
                }

                // Resize the image
                $resized = image_make_intermediate_size(
                    get_attached_file( $id ),
                    $thumb_size['width'],
                    $thumb_size['height'],
                    $thumb_size['crop']
                );

                // Resize somehow failed
                if ( ! $resized ) {
                    //fv_log('Error in resizing image thumbnail (may be it is too small), att ID:' . $id, $full_url, __FILE__, __LINE__);
                    return array( $full_url, $imagedata['width'], $imagedata['height'], false );
                }

                // Save the new size in WP
                $imagedata['sizes'][ $size_name ] = $resized;

                // Save some additional info so that we'll know next time whether we've resized this before
                $imagedata['sizes'][ $size_name ]['width_query'] = $thumb_size['width'];
                $imagedata['sizes'][ $size_name ]['height_query'] = $thumb_size['height'];
                $imagedata['sizes'][ $size_name ]['crop'] = $thumb_size['crop'];

                wp_update_attachment_metadata( $id, $imagedata );

                // Serve the resized image
                //$att_url = wp_get_attachment_url( $id );
                return array( dirname( $full_url ) . '/' . $resized['file'], $resized['width'], $resized['height'], true );


                // If the size given is a custom array size
            }

            return array( $full_url, $thumb_size['width'], $thumb_size['height'], $thumb_size['crop'] );

        }

        /**
         * Convert cyrillic characters into latin
         * @since 2.2.084
         *
         * @param string $string
         * @return string   converted string
         */
        public static function cyr2lat($string) {
            $converter = array(
                'а' => 'a',   'б' => 'b',   'в' => 'v',
                'г' => 'g',   'д' => 'd',   'е' => 'e',
                'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
                'и' => 'i',   'й' => 'y',   'к' => 'k',
                'л' => 'l',   'м' => 'm',   'н' => 'n',
                'о' => 'o',   'п' => 'p',   'р' => 'r',
                'с' => 's',   'т' => 't',   'у' => 'u',
                'ф' => 'f',   'х' => 'h',   'ц' => 'c',
                'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
                'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
                'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

                'А' => 'A',   'Б' => 'B',   'В' => 'V',
                'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
                'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
                'И' => 'I',   'Й' => 'Y',   'К' => 'K',
                'Л' => 'L',   'М' => 'M',   'Н' => 'N',
                'О' => 'O',   'П' => 'P',   'Р' => 'R',
                'С' => 'S',   'Т' => 'T',   'У' => 'U',
                'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
                'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
                'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
                'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
            );
            return strtr($string, $converter);
        }

        /**
         * Parse Serialized array and return it
         * Or return empty array
         * @since 2.2.084
         *
         * @param string $options_string
         * @return array    Converted string
         */
        public static function getContestOptionsArr($options_string) {

            if ( is_serialized($options_string) ) {
                $options_arr = maybe_unserialize($options_string);
            }

            if ( !empty($options_arr) && is_array($options_arr) ) {
                return $options_arr;
            } else {
                return array();
            }

        }

        public static function recaptcha_verify_response($response, $remote_ip, $secret) {
            if ( empty($secret) ) {
                fv_log('Recaptcha wrong $secret!', $secret, __FILE__, __LINE__);
                return false;
            }

            // make a GET request to the Google reCAPTCHA Server
            $request = wp_remote_get(
                'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $response . '&remoteip=' . $remote_ip
            );

            // Check for error
            if ( is_wp_error( $request ) ) {
                fv_log('recaptcha_verify_response - ERROR', $request, __FILE__, __LINE__);
                return 'error';
            }

            // get the request response body
            $response_body = wp_remote_retrieve_body( $request );
            $resultArr = json_decode( $response_body, true );
            //var_dump($resultArr);

            if ( $resultArr['success'] == false && isset($resultArr['error-codes']) ) {
                fv_log('Recaptcha error!', $resultArr['error-codes'], __FILE__, __LINE__);
            }
            /*
             {
                  "success": false,
                  "error-codes": [
                    "invalid-input-response",
                    "invalid-input-secret"
                  ]
                }
             */
            return $resultArr['success'];
        }

        public static function getSpamScore ($ipData, $contest) {
            $score = 0;

            // Check refer
            if ( empty($ipData['referer']) ) {
                $score = 5;
            }

            $ipData['os'] = self::getOS();

            // Check plugins
            if ( isset($_POST['pp']) ) {
                $ipData['b_plugins'] = (int)$_POST['pp'];
                if ( $ipData['b_plugins'] == 0 ) {
                    if ( self::getBrowser() == 'Firefox' ) {
                        $score += 40;
                    } else {
                        $score += 25;
                    }
                } else {

                    // Check votes count fot this photo
                    $check_spam_query = ModelVotes::query()->where( "contest_id", $contest->id );
                    // Complete query according to Contest Voting Frequency
                    switch($contest->voting_frequency) {
                        case ("once"):
                            $check_spam_query->where_later( "changed", strtotime($contest->date_start) );
                            break;
                        case ("onceF2"):
                        case ("onceF3"):
                        case ("onceF10"):
                            $check_spam_query->where_later( "changed", strtotime($contest->date_start) );
                            break;
                        case ("onceFall"):
                            $check_spam_query->where_later( "changed", strtotime($contest->date_start) )
                                ->where( "vote_id", $ipData['vote_id']  );
                            break;
                        case ("24hFonce"):
                            $check_spam_query->where_later( "changed", current_time('timestamp', 0) - 86400 );
                            break;
                        case ("24hF2"):
                        case ("24hF3"):
                            $check_spam_query->where_later( "changed", current_time('timestamp', 0) - 86400 );
                            break;
                        case ("24hFall"):
                            $check_spam_query->where_later( "changed", current_time('timestamp', 0) - 86400 )
                                ->where( "vote_id", $ipData['vote_id']  );
                            break;
                        default:
                            break;
                    }

                    $check_spam_query->where( "b_plugins", $ipData['b_plugins'] );
                    // Apply filter to query
                    $check_spam_result = $check_spam_query->find();

                    if ( count($check_spam_result) > 0 ) {
                        $coeff = 1;
                        $need_match_coeff = false;
                        switch($contest->voting_frequency) {
                            case ("onceFall"):
                            case ("once"):
                            case ("24hFonce"):
                            case ("24hFall"):
                                $need_match_coeff = true;
                                break;
                            case ("onceF2"):
                            case ("24hF2"):
                            case ("onceF3"):
                            case ("24hF3"):
                            case ("onceF10"):
                                foreach($check_spam_result as $res) {
                                    if ( $res->vote_id == $ipData['vote_id'] ) {
                                        $need_match_coeff = true;
                                    }
                                }
                                break;
                            default:
                                break;
                        }

                        if ( $need_match_coeff ) {
                            //count standard

                            // check Browsers
                            $browsersArr = array();
                            $browsersArrAllCount = 0;
                            foreach($check_spam_result as $k => $check_res) {
                                $browsersArr[$k] = $check_res->browser;
                                $browsersArrAllCount++;
                            }

                            $browsersArrCountVal = array_count_values($browsersArr);
                            if ( count($browsersArrCountVal) == 1 ) {
                                $coeff = 1;
                            } elseif ( count($browsersArrCountVal) == 2 ) {
                                $coeff = 0.7;
                            } else {
                                $coeff = .4;
                            }
                            // may add impact Result counts to Coeff

                            if ( count($browsersArrCountVal) !== 1 ) {
                                // check OS
                                $osArr = array();
                                $osArrAllCount = 0;
                                foreach($check_spam_result as $k => $check_res) {
                                    $osArr[$k] = $check_res->os;
                                    $osArrAllCount++;
                                }
                                $osArrCountVal = array_count_values($osArr);
                                if ( count($osArrCountVal) == 1 ) {
                                    $coeff = 0.3;
                                //} else if ( count($osArrCountVal) == 1 && count($check_spam_result) > 3 ) {
                                } elseif ( count($osArrCountVal) == 2 ) {
                                    $coeff = 0.2;
                                } else {
                                    $coeff = 0.1;
                                }
                                // may add impact Result counts to Coeff

                                $score += $coeff;
                            }

                            $score += $coeff * 60;
                        }
                    }
                }
            }

            // Check time interval
            /*
            $time1 = new DateTime('09:00:59');
            $time2 = new DateTime('09:01:00');
            $interval = $time1->diff($time2);
            echo $interval->format('%s second(s)');
            */

            $ipData['score'] = $score;
            return $ipData;
            //$ipData['os'] = self::getOS();
        }

        /**
         * Parse $_SERVER['HTTP_USER_AGENT'] and return $os_platform
         * @since 2.2.103
         *
         * @return string
         */
        public static function getOS() {

            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            $os_platform = "Unknown OS Platform";

            $os_array = array(
                '/windows nt 6.3/i'     =>  'Windows 8.1',
                '/windows nt 6.2/i'     =>  'Windows 8',
                '/windows nt 6.1/i'     =>  'Windows 7',
                '/windows nt 6.0/i'     =>  'Windows Vista',
                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                '/windows nt 5.1/i'     =>  'Windows XP',
                '/windows xp/i'         =>  'Windows XP',
                '/windows nt 5.0/i'     =>  'Windows 2000',
                '/windows me/i'         =>  'Windows ME',
                '/win98/i'              =>  'Windows 98',
                '/win95/i'              =>  'Windows 95',
                '/win16/i'              =>  'Windows 3.11',
                '/macintosh|mac os x/i' =>  'Mac OS X',
                '/mac_powerpc/i'        =>  'Mac OS 9',
                '/linux/i'              =>  'Linux',
                '/ubuntu/i'             =>  'Ubuntu',
                '/iphone/i'             =>  'iPhone',
                '/ipod/i'               =>  'iPod',
                '/ipad/i'               =>  'iPad',
                '/android/i'            =>  'Android',
                '/blackberry/i'         =>  'BlackBerry',
                '/webos/i'              =>  'Mobile'
            );

            foreach ($os_array as $regex => $value) {

                if (preg_match($regex, $user_agent)) {
                    $os_platform = $value;
                    break;
                }

            }

            return $os_platform;
        }


        /**
         * Return user Browser from HTTP_USER_AGENT
         *
         * @since 2.2.103
         */
        public static function getBrowser() {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            $browser = "Unknown Browser";

            $browser_array = array(
                '/msie/i'       =>  'Internet Explorer',
                '/firefox/i'    =>  'Firefox',
                '/safari/i'     =>  'Safari',
                '/chrome/i'     =>  'Chrome',
                '/opera/i'      =>  'Opera',
                '/netscape/i'   =>  'Netscape',
                '/maxthon/i'    =>  'Maxthon',
                '/konqueror/i'  =>  'Konqueror',
                '/mobile/i'     =>  'Handheld Browser'
            );

            foreach ($browser_array as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $browser = $value;
                }
            }

            return $browser;
        }

        /**
         * Generate Lightbox title by format
         *
         * @param object $photo
         * @param string $vote_count_text
         * @return string
         *
         * @since 2.2.103
         */
        public static function getLightboxTitle($photo, $vote_count_text) {
            $format = self::ss('lightbox-title-format');
            $title = str_replace('{name}', htmlspecialchars(stripslashes($photo->name)), $format);
            if ( strpos($format, '{votes}') !== false ) {
                $title = str_replace('{votes}', $vote_count_text . ": <span class='sv_votes_{$photo->id}'>" . $photo->votes_count . '</span>', $title);
            }
            if ( strpos($format, '{full_description}') !== false ) {
                $title = str_replace('{full_description}', stripslashes($photo->full_description), $title);
            }
            return str_replace('{description}', stripslashes($photo->description), $title);
        }

}

// Some themes may uses old name
class FV_Functions extends FvFunctions {}



// Upload location modification
/*

add_action('fv/public/before_upload', 'fv_pre_upload');
add_action('fv/public/after_upload', 'fv_post_upload');

function fv_pre_upload() {
    add_filter('upload_dir', 'fv_custom_upload_dir');
}

function fv_post_upload() {
    remove_filter('upload_dir', 'fv_custom_upload_dir');
}

function fv_custom_upload_dir($path) {
    if ( !empty($path['error']) ) {
        return $path;
    } //error or not pdf, so bail unchanged.

    $path = array(
        'path' => WP_CONTENT_DIR . '/uploads/fv_drbx0', // Year on end
        'url' => WP_CONTENT_URL . '/uploads/fv_drbx0',
        'subdir' => '',
        'basedir' => WP_CONTENT_DIR . '/uploads',
        'baseurl' => WP_CONTENT_URL . '/uploads',
        'error' => false,
    );

    return $path;
}
*/