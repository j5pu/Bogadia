<?php
/*   <button id="share_submit_post_mobile" class="share_submit" onclick="myFacebookLogin()"><em class="icon-facebook"></em> | Compartir</button>*/
function encodeURIComponent($str) {
$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
return strtr(rawurlencode($str), $revert);
}
$post = $GLOBALS['post'];
$url = get_permalink($post->ID) ;
$title = $post->post_title;
$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' )[0];
$share_buttons = '<div id="mobile_share_button" class="">';
$share_buttons .= '<div class="text-left col-xs-10">';
$share_buttons .= '<a id="whatsapp_share_button" class="bogashare_icon_holder" target="_blank" href="whatsapp://send?text='. encodeURIComponent($title .' '. $url) .'" data-action="share/whatsapp/share"><i class="icon-whatsapp bogashare_social" style="background-color: #43d854;"></i></a>';
$share_buttons .= '<a id="facebook_share_button" class="bogashare_icon_holder" target="_blank" href="http://www.facebook.com/sharer/sharer.php?u='. $url .'"><em class="icon-facebook bogashare_social" style="background-color: #3b5998;"></em></a>';
$share_buttons .= '<a id="twitter_share_button" class="bogashare_icon_holder" target="_blank" href="http://twitter.com/intent/tweet?status='. encodeURIComponent($title .' '. $url .' @Bogadiamag') .'"><em class="icon-twitter bogashare_social" style="background-color: #00aced;"></em></a>';
$share_buttons .= '<a id="pinterest_share_button" class="bogashare_icon_holder" target="_blank" href="http://pinterest.com/pin/create/bookmarklet/?media='. $image .'&url='. $url .'&is_video=false&description='. $title .'"><i class="icon-pinterest-circled bogashare_social" style="background-color: #bd081c;"></i></a>';
$share_buttons .= '</div>';
$share_buttons .= '<div class="text-left col-xs-2" style="right: 15px;">';
$share_buttons .= '<a class="bogashare_icon_holder" target="_blank" href="whatsapp://send?text='. encodeURIComponent($title .' '. $url) .'" data-action="share/whatsapp/share"><i class="bogashare_vote icon-heart" style="color: ;"></i></a>';
$share_buttons .= '</div>';
$share_buttons .= '</div>';
