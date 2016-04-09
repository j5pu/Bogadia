var msg = '';
var user_id = "";
function check_response(response){
    if (!response || response.error) {
        jQuery("#danger").fadeIn('slow');
        jQuery('#bogashare_spinner').fadeOut('slow');
        jQuery("#danger").delay( 2000 ).fadeOut('slow');
        jQuery('#compartir_opinion_desplegar').fadeIn('slow');
    } else {
        store_share_ajax_call();
        jQuery('#bogashare_spinner').fadeOut('slow');
        jQuery("#compartir_opinion").delay( 2000 ).fadeOut('slow');
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
    // fix iOS Chrome
/*
    if (navigator.userAgent.match('CriOS')) {
*/  var ios_chrome = localStorage.getItem('ios_chrome');
    if (ios_chrome == null){
        localStorage.setItem('ios_chrome', '1')
        localStorage.setItem('ios_chrome_msg', msg)
        window.open('https://www.facebook.com/dialog/oauth?client_id=' + jQuery('#compartir_opinion').data('appid') + '&redirect_uri=' + document.location.href + '&scope=email,public_profile,publish_actions&response_type=token', '', null);
        jQuery("#fb-root").bind("facebook:init", function () {
            var accToken = jQuery.getUrlVar('#access_token');
            if (accToken) {
                var fbArr = {scopes: "email,public_profile,publish_actions"};
                fb_intialize_share(fbArr, accToken);
            }
        });
    }else{
        localStorage.setItem('fb_user_id', FB.getUserID());
        FB.api('/me/feed', 'post', {message: msg, link: document.location.href},function(response) {
            check_response(response);
        });
        localStorage.removeItem('ios_chrome');
    }
/*    } else {
        FB.login(function (FB_response) {
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
    }*/
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
    if (localStorage.getItem('ios_chrome') == '1'){
        localStorage.setItem('ios_chrome', '2');
        window.fbAsyncInit = function() {
            FB.init({
                appId      : jQuery('#compartir_opinion').data('appid'), // App ID
                version    : 'v2.1',
                status     : true, // check login status
                cookie     : true, // enable cookies to allow the server to access the session
                xfbml      : true,  // parse XFBML
                oauth      : true
            });
            jQuery('#fb-root').trigger('facebook:init');
        };
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/<?php echo apply_filters('kleo_facebook_js_locale', 'en_US'); ?>/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        setTimeout(function(){
            localStorage.setItem('fb_user_id', FB.getUserID());
            FB.api('/me/feed', 'post', {message: localStorage.getItem('ios_chrome_msg'), link: document.location.href},function(response) {
                check_response(response);
            });
        }, 3000)
        jQuery("#compartir_opinion").slideDown('slow');
        jQuery('#compartir_opinion_desplegar').fadeOut('slow');
        jQuery('#bogashare_spinner').delay(100).fadeIn('slow');
    }else{
        jQuery("#compartir_opinion").delay( 20000).slideDown('slow');

    }
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