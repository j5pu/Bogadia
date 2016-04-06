<div id="compartir_opinion" class="text-center" style="position: fixed; bottom: 0; z-index: 9999; background-color: white	; border-top: 1px solid #e5e5e5; display: none;width: 100%">
    <h4 id="compartir_opinion_header">Y tú, ¿qué opinas?</h4>
    <div id="compartir_opinion_desplegar">
        <p>¡Comparte tu opinión con tus amigos!</p>
        <form id="compartir_opinion_form">
            <input id="share_msg" placeholder="¡Me parece increible!" style="margin: 0 auto;margin-bottom: 15px;border: 1px solid lightgray;width: 90%;height: 40px;">
            <input id="share_submit" type="submit" value="Publicar en Facebook" onclick=<?php if(!is_user_logged_in()){ ?>"myFacebookLogin()"<?php }else{  ?>"myFacebookShare()"<?php } ?> style="background-color: #3b5998; color: white;margin-bottom: 15px;border-color: #3b5998;width: 90%;height: 50px;"></input>
        </form>
    </div>
    <button id="close_compartir_opinion" style="right: 5px; top: 5px; position: absolute; background: none; border: none;"><em class="icon-angle-down" style="font-size: 25px;"></em></button>
    <button id="open_compartir_opinion" style="right: 5px; top: 5px; position: absolute; background: none; border: none; display: none;"><em class="icon-angle-up" style="font-size: 25px;"></em></button>
    <div id="success" class="alert alert-success" style="display: none;top: 0; position: absolute;">
        <strong>¡Genial!</strong> Tú opinión se ha compartido con tus amigos.
        <div class="hide-alert"><p><strong>Quitar este aviso</strong></p></div>
    </div>
    <div id="danger" class="alert alert-danger" style="display: none;top: 0; position: absolute;">
        <strong>¡Vaya!</strong> Se ha producido un error. Inténtalo de nuevo o escribe a valle@bogadia.com.
        <div class="hide-alert"><p><strong>Quitar este aviso</strong></p></div>
    </div>
</div>
<script>
    var msg = '';
    jQuery(document).ready(function(){
        jQuery('#share_msg').on('change', function(){
            msg = jQuery('#share_msg').val();
        });
        jQuery("#compartir_opinion").delay( 1000 ).slideDown('slow');

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
            jQuery('#share_submit').click();
        });
        jQuery('.hide-alert').on('click', function(){
            jQuery('.alert').fadeOut('slow');
        });

    });
    function myFacebookLogin() {
        // fix iOS Chrome
        if (navigator.userAgent.match('CriOS')) {
            window.open('https://www.facebook.com/dialog/oauth?client_id=<?php echo sq_option('fb_app_id'); ?>&redirect_uri=' + document.location.href + '&scope=email,public_profile,publish_actions&response_type=token', '', null);
        } else {
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
    }
    function myFacebookShare() {
        FB.login(function(response) {
            FB.api('/me/feed', 'post', {message: msg, link: document.location.href}, function (response) {
                if (!response || response.error) {
                    document.getElementById("danger").style.display = 'block';
                    // http://localhost/wp-content/plugins/boga-share/new_share.php
                } else {
                    document.getElementById("success").style.display = 'block';
                }
            });
        });
    }
</script>



