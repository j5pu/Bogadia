var msg = '';
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
                user_fb_id: FB.getUserID(),
                comment: msg,
            }
        })
        .done(function( msg ) {
            document.getElementById("success").style.display = 'block';
            alert( "Data Saved: " + msg );
        });
}
function myFacebookLogin() {
    FB.login(function(FB_response){
        if (FB_response.authResponse) {
            fb_intialize_share(FB_response, '');
        }
        FB.api('/me/feed', 'post', {message: msg, link: document.location.href},function(response) {
            if (!response || response.error) {
                document.getElementById("danger").style.display = 'block';
                // http://localhost/wp-content/plugins/boga-share/new_share.php
            } else {
                store_share_ajax_call();
                document.getElementById("success").style.display = 'block';

            }
        });
    },
    {
        scope: 'email,public_profile,publish_actions',
        auth_type: 'rerequest',
        return_scopes: true
    });
}
function myFacebookShare() {
    FB.api('/me/feed', 'post', {message: msg, link: document.location.href}, function (response) {
        if (!response || response.error) {
            document.getElementById("danger").style.display = 'block';
            // http://localhost/wp-content/plugins/boga-share/new_share.php
        } else {
            store_share_ajax_call();
        }
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