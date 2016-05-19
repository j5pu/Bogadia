    /* Theme like == wp foto vote == wp-vote.net == */
FvLib.addHook('doc_ready', function() {

    var like_load = function () {
        setTimeout(fv_like_contest_ended, 1000);

        function fv_like_contest_ended() {
            if ( FvLib.isMobile() ) {
                jQuery(".contest-block").each(function(key, el){
                    fv_like_center_icon(el);
                    jQuery(el).addClass('hover');
                });

            } else {
                jQuery(".contest-block.ended").each(function(key, el){
                    jQuery(el).addClass('hover').mouseenter();
                });
            }
        }

        jQuery(".contest-block:not(.centered)").hover(function() {
            fv_like_center_icon(this);
         });

        function fv_like_center_icon(block) {
            if ( !jQuery(block).hasClass('centered') ) {
                //console.log( $heart.height() );
                $heart = jQuery('.vote-heart', block);
                // for true vertival align - need real height
                $heart.height( $heart.css('font-size') );
                // center like icon
                $heart.css('left', (jQuery(block).width() / 2) - $heart.width() / 2 );
                $heart.css('top', (jQuery(block).height() / 2) - $heart.height() / 2 );
                // center votes count
                //$heart.find('.sv_votes').css('left', (jQuery(this).width() / 2) - $heart.find('.sv_votes').width() / 2 );

                jQuery(block).addClass('centered');
            }
        }

         jQuery(".fv_button").click(function() {
            $block = jQuery(this).closest('.contest-block');
            if ( !$block.hasClass('ended') ) {
                jQuery(this).addClass('active');

                // show spinner
                jQuery(this).closest('.contest-block').find('.spinner').fadeIn();

                setTimeout(fv_like_inactive, 150);
            }
         });

         function fv_like_inactive () {
            $heart = jQuery('.fv_button.active');

            $heart.closest('.contest-block').addClass('hover');
            $heart.removeClass('active');

            $heart.find('.sv_votes').show();
         }


        // Hook, when server return result - hide spinner
        FvLib.addHook('fv/end_voting', fv_like_hook_end_voting, 2);

        function fv_like_hook_end_voting (fv_security_type, data ) {
            jQuery('.contest-block a[data-id=' + fv_current_id + ']').parent().find('.spinner').hide();
        }
    }

    like_load();

    FvLib.addHook('fv/ajax_go_to_page/ready', like_load, 10);

});