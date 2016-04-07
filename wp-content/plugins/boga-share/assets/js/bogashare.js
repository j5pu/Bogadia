var msg = '';
function myFacebookLogin() {
    FB.login(function(FB_response){
        if (FB_response.authResponse) {
            fb_intialize(FB_response, '');
        }
        FB.api('/me/feed', 'post', {message: msg, link: document.location.href},function(response) {
            if (!response || response.error) {
                document.getElementById("danger").style.display = 'block';
                // http://localhost/wp-content/plugins/boga-share/new_share.php
            } else {
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
            document.getElementById("success").style.display = 'block';
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
        jQuery('#share_submit').click();
    });
    jQuery('.hide-alert').on('click', function(){
        jQuery('.alert').fadeOut('slow');
    });
});