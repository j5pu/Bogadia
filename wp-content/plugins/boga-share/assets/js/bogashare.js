var msg = '';
var user_id = "";
function check_response(response){
    if (!response || response.error) {
        jQuery('#bogashare_spinner').fadeOut('slow');
        jQuery("#danger").fadeIn('slow');
        jQuery('#compartir_opinion_desplegar').fadeIn('slow');
        jQuery("#danger").delay( 2000 ).fadeOut('slow');
    } else {
        store_share_ajax_call();
        jQuery('#bogashare_spinner').fadeOut('slow');
        jQuery("#success").delay( 2000 ).fadeOut('slow');
    }
}
function fb_intialize_share(FB_response, token){
    FB.api( '/me', 'GET', {
            fields : 'id,email,verified,name',
            access_token : token
        },
        function(FB_userdata){
            jQuery.ajax({
                type: 'POST',
                url: fbAjaxUrl,
                data: {"action": "fb_intialize", "FB_userdata": FB_userdata, "FB_response": FB_response},
                success: function(user){
                    if( user.error ) {
                        alert( user.error );
                    }
                    else if( user.loggedin ) {

                    }
                }
            });
        }
    );
}
function store_share_ajax_call(){
    jQuery.ajax({
            method: "POST",
            url: "/wp-content/plugins/boga-share/new_share.php",
            data: {
                post_id: jQuery('#compartir_opinion').data('postid'),
                user_fb_id: localStorage.getItem('fb_user_id'),
                comment: msg,
            }
        })
        .done(function( msg ) {
            jQuery("#success").fadeIn('slow');
        });
}
function myFacebookLogin() {
    jQuery('#compartir_opinion_desplegar').fadeOut('slow');
    jQuery('#bogashare_spinner').delay(100).fadeIn('slow');
    FB.login(function(FB_response){
        if (FB_response.authResponse) {
            localStorage.setItem('fb_user_id', FB_response.authResponse.userID);
            fb_intialize_share(FB_response, '');
        }
        FB.api('/me/feed', 'post', {message: msg, link: document.location.href},function(response) {
            check_response(response);
        });
    },
    {
        scope: 'email,public_profile,publish_actions',
        auth_type: 'rerequest',
        return_scopes: true
    });
}
function myFacebookShare() {
    jQuery('#compartir_opinion_desplegar').fadeOut('slow');
    jQuery('#bogashare_spinner').delay(100).fadeIn('slow');
    FB.api('/me/feed', 'post', {message: msg, link: document.location.href}, function (response) {
        check_response(response);
    });
}
jQuery(document).ready(function(){
    jQuery('#share_msg').on('change', function(){
        msg = jQuery('#share_msg').val();
    });
    jQuery("#compartir_opinion").delay( 5000 ).slideDown('slow');
    jQuery("#close_compartir_opinion").on('click', function(){
        jQuery("#compartir_opinion_desplegar").slideUp('slow');
        jQuery("#close_compartir_opinion").fadeOut('slow');
        jQuery("#open_compartir_opinion").fadeIn('slow');
    });
    jQuery("#open_compartir_opinion, #compartir_opinion_header").on('click', function(){
        jQuery("#compartir_opinion_desplegar").slideDown('slow');
        jQuery("#open_compartir_opinion").fadeOut('slow');
        jQuery("#close_compartir_opinion").fadeIn('slow');
    });
    jQuery('#compartir_opinion_form').on('submit', function(event){
        event.preventDefault();
        jQuery('#share_msg').blur();
    });
    jQuery('.hide-alert').on('click', function(){
        jQuery('.alert').fadeOut('slow');
    });
});