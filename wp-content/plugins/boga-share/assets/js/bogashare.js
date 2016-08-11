jQuery(document).ready(function() {
    jQuery('#bogashare_banner').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_banner', 'click');
        var share_buttons = jQuery('#mobile_share_button').html();
        jQuery('#buttons_container').html(share_buttons);
        jQuery('#mobile_share_button').velocity('fadeOut');
        jQuery('#bogacontest_login_modal').modal({show:true});
    });
    jQuery('#whatsapp_share_button, #whatsapp_share_button_2').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_whatsapp', 'click');

    });
    jQuery('#facebook_share_button, #facebook_share_button_2').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_facebook', 'click');

    });
    jQuery('#twitter_share_button, #twitter_share_button_2').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_twitter', 'click');

    });
    jQuery('#pinterest_share_button, #pinterest_share_button_2').on('click', function(){
        ga('send', 'event', 'Bogashare2', 'click_pinterest', 'click');

    });
    jQuery('#bogacontest_login_modal').on('hidden.bs.modal', function () {
        jQuery('#mobile_share_button').velocity('fadeIn');
    })
});
