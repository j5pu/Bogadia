var msg = '';
var user_id = "";
var bogashare = localStorage.getItem('bogashare');
/*ga('create', {
    trackingId: 'UA-55975132-1',
    cookieDomain: 'auto',
    'name': 'bogashare',
    'cookieName': 'gaCookie'
});*/

function check_response(response){
    if (!response){
        ga('send', 'event', 'Bogashare', 'NoAnswer', 'error');
    }else{
        if (response.error) {
            ga('send', 'event', 'Bogashare', 'ErrorCompartir', 'error');
            jQuery('.share_submit').html('¡Vaya! Se ha producido un error');
        } else {
            FB.api('/v2.6/' + response.id + '?fields=privacy', function(response2){
                if(response2.privacy.value == 'SELF' || response2.privacy.value == 'CUSTOM'){
                    ga('send', 'event', 'Bogashare', 'CompartirConsigoMismo', 'error');
                    jQuery('.share_submit').html('No vale compartirlo solo contigo');
                }else{
                    ga('send', 'event', 'Bogashare', 'ExitoCompartir', 'Confirmacion');
                    store_share_ajax_call(response2.id);
                }
            });
        }
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
                        ga('send', 'event', 'Bogashare', 'UsuarioNoregistrado', 'Error');
                    }else{
                        ga('send', 'event', 'Bogashare', 'ExitoCompartir', 'Confirmacion');
                    }
                }
            });
        }
    );
}
function store_share_ajax_call(fb_post_id){
    jQuery('.share_submit').html('Incribiéndote en el concurso...');
    jQuery.ajax({
            method: "POST",
            url: "/wp-content/plugins/boga-share/new_share.php",
            data: {
                post_id: jQuery('#compartir_opinion').data('postid'),
                user_fb_id: localStorage.getItem('fb_user_id'),
                comment: fb_post_id,
            }
        })
        .done(function( msg ) {
            localStorage.setItem('bogashare', 1);
            jQuery('.share_submit').html('¡Genial! Ya estás participando.');
            ga('send', 'event', 'Bogashare', 'ExitoApuntado', 'Confirmacion');
        });
}
function myFacebookLogin() {
    jQuery('#bogashareModal').modal({show:true});
    jQuery('.share_submit').html('Compartiendo... <img id="bogashare_spinner" src="/wp-content/plugins/boga-share/assets/img/spinner2.gif" style="display: none;">');
    jQuery('#bogashare_spinner').delay(100).fadeIn('slow');

    if (navigator.userAgent.match('CriOS')) {
        // fix iOS Chrome
        var ios_chrome = localStorage.getItem('ios_chrome');
        if (ios_chrome == null){
            localStorage.setItem('ios_chrome', '1');
            localStorage.setItem('ios_chrome_msg', msg);
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
    } else {
        FB.login(function (FB_response) {
                if (FB_response.authResponse) {
                    localStorage.setItem('fb_user_id', FB_response.authResponse.userID);
                    fb_intialize_share(FB_response, '');
                }
                FB.api('/me/feed', 'post', {message: '', link: document.location.href},function(response) {
                    check_response(response);
                });
            },
            {
                scope: 'email,public_profile,publish_actions',
                auth_type: 'rerequest',
                return_scopes: true
            });
    }
}
jQuery(document).ready(function(){

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
        }, 3000);

        jQuery('#bogashareModal').modal({show:true, backdrop: 'static'});
        jQuery('.share_submit').html('Compartiendo... <img id="bogashare_spinner" src="/wp-content/plugins/boga-share/assets/img/spinner2.gif" style="display: none;">');
        jQuery('#bogashare_spinner').delay(100).fadeIn('slow');

    }else{
        if(!bogashare){
            setTimeout(function(){
                jQuery('#bogashareModal').modal({show:true, backdrop: 'static'});
                ga('send', 'event', 'Bogashare', 'Interstitial', 'Mostrar');
            }, 8000);
        }
    }
    jQuery('.share_submit').on('click', function(){
        ga('send', 'event', 'Bogashare', 'Compartir', 'Click');
    });
    jQuery('button#close-buton.close').on('click', function(){
        ga('send', 'event', 'Bogashare', 'Cerrar', 'Click');
    });
});