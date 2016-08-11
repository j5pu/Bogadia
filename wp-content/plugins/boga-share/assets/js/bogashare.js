function after_share(){
    jQuery('#buttons_container').append('<h3 style="margin-top: 10px;">¡Genial! Ya estás participando. ¡Mucha suerte!</h3>');
}

function bind_share_buttons(){
    jQuery('#whatsapp_share_button').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_whatsapp', 'click');
        after_share()
    });
    jQuery('#facebook_share_button').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_facebook', 'click');
        after_share()
    });
    jQuery('#twitter_share_button').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_twitter', 'click');
        after_share()
    });
    jQuery('#pinterest_share_button').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_pinterest', 'click');
        after_share()
    });
}

jQuery(document).ready(function() {
    jQuery('#bogashare_banner').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_banner', 'click');
        var share_buttons = jQuery('#mobile_share_button').html();
        jQuery('#buttons_container').html(share_buttons);
        jQuery('#mobile_share_button').velocity('fadeOut');
        jQuery('#bogacontest_login_modal').modal({show:true});
        bind_share_buttons()
    });
    jQuery('#bogacontest_login_modal').on('hidden.bs.modal', function () {
        jQuery('#mobile_share_button').velocity('fadeIn');
        bind_share_buttons()
    })
    bind_share_buttons()
});
