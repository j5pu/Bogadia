<div id="compartir_opinion" class="text-center" style="position: fixed; bottom: 0; z-index: 9999; background-color: white	; border-top: 1px solid #e5e5e5; display: none;width: 100%" data-postid="<?php echo get_the_ID(); ?>">
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
<?php if(is_user_logged_in()){ ?>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?php echo sq_option('fb_app_id'); ?>', // App ID
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
</script>
<?php } ?>
