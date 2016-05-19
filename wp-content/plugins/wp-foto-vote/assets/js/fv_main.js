/*
 Plugin Name: WP Foto Vote
 Plugin URI: http://wp-vote.net/
 Description: Simple photo contest plugin with ability to user upload photos. Includes protection from cheating by IP and cookies. User log voting. After the vote invite to share post about contest in Google+, Twitter, Facebook, OK, VKontakte.
 Author: Maxim Kaminsky
 Plugin support EMAIL: support@wp-vote.net

 This is commercial script!
 */

"use strict";

window.jQuery || document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"><\/script>');

var $jQ = jQuery;
/*
function fv_fix_broken_images() {
    $jQ('.fv_contest_container').find('img').each(function() {
        if (!this.complete || typeof this.naturalWidth == "undefined" || this.naturalWidth == 0) {
            // image was broken, replace with your new image
            this.src = fv.plugin_url + '/assets/img/no-photo.png';
        }
    });
}

$jQ(window).load(function() {
    fv_fix_broken_images();
});
 FvLib.addHook('fv/ajax_go_to_page/ready', setTimeout(fv_fix_broken_images, 900), 99);
*/

// Global variable for save Image ID
var fv_current_id = -1,
	fb_post_id = 0,
    // not clicking
    fv_go = false,
	fv_subscribed = false,
    // evercookie UID
    fv_uid = '',
    // parmeter, that user must see message - solve Captcha, not you do it wrong
    fv_reCAPTCHA_first = true;


//** Voting function
function sv_vote(id, action, el) {

    if (action == undefined || !action) {
        action = 'vote';
    }
    fv_current_id = id;

    // Vars for modal window
	var status = "error",
		title = fv.lang.title_not_voted,
		msg = "",
        subtitle = "";

    // защита от скликивания
    if (!fv_go) {
        // check subscription

        if ( 	(fv.security_type == "default" || fv.security_type == "cookieAregistered" || fv.security_type == "defaultAfb")
				|| action == "subscribe"
				|| action == "check"
				|| fv.security_type != "default" && fv_subscribed
				|| fv_before_start_voting(el, action)
	  	) {

			// action before voting
			if ( !FvLib.callHook('fv/start_voting', fv.security_type, fv_subscribed, action) ) {
				return false;
			}

            //email = document.getElementsByName("fv_name");
            if (action != 'check') {
                FvModal.goStartVote(fv_current_id);
            }

            FvLib.logSave("start voting!");


            var send_data = {
				action: 'vote',
				contest_id: fv.contest_id,
				vote_id: fv_current_id,
				post_id: fv.post_id,
				referer: document.referrer,
                ds: window.screen.availWidth + "x" +window.screen.availHeight,
				uid: fv_uid,
				pp: fv_whorls['pp'],
				ff: fv_whorls['ff'],
				fuckcache: FvLib.randomStr(8),
				some_str: fv.some_str
			};

            if ( fv.security_type == "defaultArecaptcha" || fv.security_type == "cookieArecaptcha" ) {
                if ( (fv.recaptcha_session == true && fv_subscribed == true && fv.recaptcha_session_ready == false)
                    || fv.recaptcha_session == false ) {
                    send_data['recaptcha_response'] = grecaptcha.getResponse( FvModal.voteRecaptchaID );
                }
                if (fv.recaptcha_session == false) {
                    fv_subscribed = false;
                }
            }

            if (fv.security_type == "defaultAsubscr" && action == 'subscribe') {
                var fv_name = document.querySelectorAll("input.fv_name")[0],
                fv_email = document.querySelectorAll("input.fv_email")[0];

                if (!fv_name.checkValidity() || !fv_email.checkValidity()) {
                    return false;
                }
                send_data['fv_name'] = fv_name.value;
                send_data['fv_email'] = fv_email.value;
            }
            if (fv.security_type == "defaultAfb" && action == 'fb_shared') {
                send_data['fb_post_id'] = fb_post_id;
            } else if (fv.security_type == "defaultAfb") {
                fb_post_id = 0;
                send_data['check'] = true;
            }

            if (action == 'check') {
                send_data['check'] = true;
            }

			send_data = FvLib.applyFilters('fv/vote/send_data', send_data);
            if ( !fv.fast_ajax ) {
                var fv_ajax_url = fv.ajax_url;
            } else {
                var fv_ajax_url = fv.plugin_url + '/ajax.php';
            }

            $jQ.post(
                fv_ajax_url,
                send_data,
                function (data) {
                    data = FvLib.parseJson(data);
                    // if Voting for just one Photo and response not related with reCAPTHCA
                    if (fv.voting_frequency == 'once' && data.res != 6 && data.res != 66) {
                        fv_go = true;
                    }

                    if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) { FvLib.newImg(document); return; }

					//** apply filters for retrieved data
					data = FvLib.applyFilters('fv/vote/get_data', data);
                    // fix for Check action
                    if (typeof data.no_process == "string") {
                        return;
                    }

                    //$jQ('#sv_dialog #info .slogan').text(fv.lang.invite_friends);
                    if (data.res == 98) {
                        // Invalid security token
                        alert(fv.lang.invalid_token);
                        return false;
                    } else if (data.res == 1) {
						fvIncreaseVotesCount(fv_current_id, 1);
                        fv.data[fv_current_id].votes_count = ++fv.data[fv_current_id].votes_count;
                        // Если же человек не голосовал, то напмшем что голос учтен, и попросим лайкнуть
                        title = fv.lang.title_voted;
                        msg = fv.lang.msg_voted;
                        status = "success";
                        if ( fv.security_type == "defaultArecaptcha" || fv.security_type == "cookieArecaptcha" ) {
                            fv.recaptcha_session_ready = true;
                        }
                    } else if (data.res == 2) // has voted
                    {
                        // Если человек уже голосовал, сообщим ему об этом
                        msg = fv.lang.msg_you_are_voted;
                    } else if (data.res == 3) // 24 hours not not passed
                    {
                        // Если еще не прошло 24 часа, сообщим ему об этом
						if ( data['hours_leave'] ) {
                        	msg = fv.lang.msg_24_hours_not_passed.replace("*hours_leave*", data.hours_leave );
						} else {
							msg = fv.lang.msg_24_hours_not_passed;
						}

                    } else if (data.res == 4) // date end
                    {
                        // Конкурс закончился
                        msg = fv.lang.msg_konkurs_end;
                    } else if (data.res == 5) // not authorized;
                    {
                        msg = fv.lang.msg_not_authorized;
                        subtitle = fv.lang.subtitle_not_authorized;
                    } else if (data.res == 6) // wrong reCAPTCHA
                    {
                        FvModal.goRecaptchaVote(fv_current_id, true);
                        return false;
                    } else if (data.res == 66) // need reCAPTCHA
                    {
                        // if Enabled Save reCAPTCHA Session and we don't have session
                        fv.recaptcha_session_ready = false;
                        FvModal.goRecaptchaVote(fv_current_id, true && !fv_reCAPTCHA_first);
                        fv_reCAPTCHA_first = false;
                        return false;
                    } else if (data.res == "can_vote") // not authorized;
                    {
                        //sv_vote_send("fb", null, fv_current_id, true);
						FvModal.goFbVote();
                        return false;
                    } else {
                        // Что-то непонятное
                        msg = fv.lang.msg_err;
                    }

                    if (data.user_country && !FvLib.readCookie('user_country')) {
                        FvLib.createCookie('user_country', data.user_country, 99);
                    }

					//** apply filters for Modal data
					status = FvLib.applyFilters('fv/vote/modal_status', status);
					title = FvLib.applyFilters('fv/vote/modal_title', title);
					msg = FvLib.applyFilters('fv/vote/modal_msg', msg, data);
					subtitle = FvLib.applyFilters('fv/vote/modal_subtitle', subtitle);

					// Show modal
                    FvModal.goVoted(status, title, msg, subtitle);

                    if ( eval("typeof fv_hook_end_voting") === 'function' ) {
                        fv_hook_end_voting(data);
                    }
					// action before voting
					FvLib.callHook('fv/end_voting', fv.security_type, data);

                }).fail(function() {
                    FvLib.adminNotice(fv.lang.ajax_fail ,'error');
                });
        }
    } else {
        // Если человек уже точно голосовал, нажимает 2-рой раз
		FvModal.goVoted(status, title, fv.lang.msg_you_are_voted, subtitle);
    }
}

/**
 * Runs custom checks actions in Voting process
 *
 * Uses for decrease code size in main Vote function
 */
function fv_before_start_voting(el, action) {
    //&& action != 'subscribe'
    if ( FvLib.filterExists('fv/vote/before_start_voting') ) {
        return FvLib.applyFilters('fv/vote/before_start_voting', false, action);
    }

    if ( fv.security_type == "defaultArecaptcha" || fv.security_type == "cookieArecaptcha" ) {
        if ( fv.recaptcha_session == false ) {
            FvModal.goRecaptchaVote();
        } else {
            // try vote to check reCAPTCHA session
            return true;
        }
    } else if (!fv_subscribed) {
        if ( !fv.fast_ajax ) {
            var fv_ajax_url = fv.ajax_url;
        } else {
            var fv_ajax_url = fv.plugin_url + '/ajax.php';
        }

        //console.log({'contest_id':fv.contest_id});
        $jQ.get(
            fv_ajax_url,
            { 'action': 'fv_is_subscribed', 'contest_id': fv.contest_id, 'post_id': fv.post_id, 'uid': fv_uid, 'fuckcache': FvLib.randomStr(8) },
            function (data) {
                if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;

                data = FvLib.parseJson(data);
                //console.log("is_subscribed: ");
				//** apply filters for retrieved data
				data = FvLib.applyFilters('fv/before_start_voting/get_data', data);

                if (data.res == "is_subscribed") {
                    fv_subscribed = true;
                    sv_vote(fv_current_id);
                } else if (data.res == "not_subscribed") // has voted
                {
                    // User need to be verify
                    fv_subscribed = false;

                    if (fv.security_type == "defaultAsocial" || fv.security_type == "cookieAsocial") {
						FvModal.goStartSocialAuthorization();
                        //uLogin.initWidget("uLogin");


                        //$jQ('#uLoginLink img').click();

                        //setTimeout('uLogin.initWidget("uLogin");', 1500);
                        //$jQ('#uLogin img').click();

                    } else if (fv.security_type == "defaultAsubscr") {
						FvModal.goStartSubscribe();
                    }

                }
            }
        ).fail(function() {
            FvLib.adminNotice(fv.lang.ajax_fail ,'error');
        });;  // AJAX get :: END
    }
    return false;
}

var fv_recaptcha_ready = function(response) {
    fv_subscribed = true;
    sv_vote(fv_current_id, 'vote');
};

// This function Open share window for selected Social network
function sv_vote_send(service, el, id, for_vote) {
    if (typeof (id) !== 'undefined') {
        var current = fv.data[id];
    } else {
        var current = fv.data[fv_current_id];
    }

    //var url = sv_data['link'] + '#photo-' + [fv_current_id];
    var fv_url = FvLib.applyFilters('fv/share/page_url', fv.page_url + '=' + current.id);

	// action before voting
	if ( !FvLib.callHook('fv/share_start', service, current, for_vote) ) {
		return false;
	}

    // Title
    if (fv.social_title.length > 1) {
        var title = fv.social_title;
        title = fv.social_title.replace("*name*", current['name']);
    } else {
        var title = current['name'];
        title = title.replace("\\", '');
    }
	//** apply filters for Title
	title = FvLib.applyFilters('fv/share/title', title, current);

    // Description
    if (fv.social_descr.length > 3) {
        var description = fv.social_descr;
        description = fv.social_descr.replace("*name*", current['name']);
    } else {
        if (current['description'].length < 80) {
            var description = current['social_description'];
        } else {
            var description = current['social_description'].substr(0, 100);
        }
    }
	//** apply filters for Description
	description = FvLib.applyFilters('fv/share/description', description);


    // Social image
    if (fv.social_photo.length > 1) {
        var image = fv.social_photo;
    } else {
        var image = current['url'];
        //var image = '';
    }
	//** apply filters for Image
	image = FvLib.applyFilters('fv/share/image', image);

    var url = '';
    switch (service) {
        case 'fb':
            // if configured FB api key
            if ( typeof(FB) !== 'undefined' && fv.fv_appId.length > 3) {
                FB.ui({
                    method: 'feed',
					display: 'popup',
                    link: fv_url,
                    caption: title,
                    description: description,
                    picture: image
                }, function (response) {
					//** apply filters for retrieved data
					response = FvLib.applyFilters('fv/share/fb_data', response);

                    //console.log(response);
                    if ( typeof (response) === "undefined" || response == null) {
                        FvLib.logSave('was not shared');
                    } else if ( typeof (response.error_code) !== "undefined" ) {
						FvLib.logSave(response);
                    } else {
						FvLib.logSave('shared - post id is ' + response.post_id);
                        if (typeof (for_vote) !== "undefined") {
                            fb_post_id = response.post_id;
                            sv_vote(fv_current_id, "fb_shared");
                        }
                    }

                });
                return false;
            } else {
                url = "http://www.facebook.com/sharer.php?s=100&p[title]=" + encodeURIComponent(title) + "&p[summary]=" + encodeURIComponent(description) + "&p[url]=" + encodeURIComponent(fv_url) + "&p[images][0]=" + encodeURIComponent(image) + "&nocache-" + FvLib.randomStr(8);
            }
            break;
        case 'tw':
            url = "http://twitter.com/share?text=" + encodeURIComponent(title) + " " + description + "&url=" + encodeURIComponent(fv_url) + "&counturl=" + encodeURIComponent(fv_url) + "&nocache-" + FvLib.randomStr(8);
            break;
        case 'vk':
            url = "http://vk.com/share.php?title=" + encodeURIComponent(title) + "&description=" + encodeURIComponent(description) + "&url=" + encodeURIComponent(fv_url) + "&image=" + encodeURIComponent(image);
            break;
        case 'ok':
            url = "http://www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1&st.comments=" + encodeURIComponent(title) + ": " + encodeURIComponent(description) + "&st._surl=" + encodeURIComponent(fv_url);
            break;
        case 'gp':
            url = "https://plusone.google.com/_/+1/confirm?hl=" + fv.user_lang + "&url=" + encodeURIComponent(fv_url);
            break;
        case 'pi':
            //http://pinterest.com/pin/create/button/?url={URI-encoded URL of the page to pin}&media={URI-encoded URL of the image to pin}&description={optional URI-encoded description}
            url = "http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(fv_url) + '&media=' + encodeURIComponent(image) + '&description=' + encodeURIComponent(title + ' - ' + current['name']);
            break;
        default:
            return false;
    }
    //$jQ('.jquery-lightbox-overlay').click();

	// action before voting
	if ( !FvLib.callHook('fv/share_before_send', service, current, fv_url) ) {
		return false;
	}

	window.open(url, '', 'toolbar=0,status=0,width=626,height=436');

	// action before voting
	if ( !FvLib.callHook('fv/share_after_send', service, current, fv_url) ) {
		return false;
	}

    return false;
}


/*
* Check form and Send vote
 */
function fvCheckSubscribeFormAndVote() {
	if ( FvModal.isVisible() ) {
		var nameEl = FvModal.$el.find("input.fv_name");
		var emailEl = FvModal.$el.find("input.fv_email");
		var valid = true;
		// Check name
		if ( nameEl == undefined || nameEl.val().length <= 2 ){
			jQuery(nameEl).closest(".frm-field").addClass("is-error");
			valid = false;
		} else {
			jQuery(nameEl).closest(".frm-field").removeClass("is-error");
		}
		// Check email
		if ( emailEl == undefined || !FvLib.isValidEmail(emailEl.val()) ){
			jQuery(emailEl).closest(".frm-field").addClass("is-error");
			valid = false;
		} else {
			jQuery(emailEl).closest(".frm-field").removeClass("is-error");
		}

		// Send vote
		if ( valid && FvLib.applyFilters('fv/subscribe_validate', nameEl, emailEl) ) {
			sv_vote(fv_current_id, 'subscribe');
		}
	}
	return false;
}

/**
 * Increase votes count in Html element with specified ID above image after voting
 */
function fvIncreaseVotesCount(id, count) {
    var container = $jQ('.sv_votes_' + id);
    var val = parseInt(container.html(), 10);
    if (!val) val = 0;
    val += parseInt(count);
    container.html(val);
}


var EC = new evercookie({baseurl: fv.plugin_url + '/assets/evercookie'});
EC.get("fv_uid", function (value) {
        if (value === undefined || FvLib.strPos(value, 'br') > 0) {
            fv_uid = FvLib.randomStr(8);
            EC.set("fv_uid", fv_uid);
        } else {
            fv_uid = value;
        }
        //FvLib.log(fv_uid);
        FvLib.logSave('fv runned ' + fv_uid);
    },
    undefined,
    undefined,
    1
);

// Callback, when image not loaded
jQuery(".contest-block img.attachment-thumbnail").on("error",function() {
    this.src= fv.plugin_url + "/assets/img/no-photo.png";
    FvLib.adminNotice(fv.lang.img_load_fail ,'warning', true);
});

FvLib.addHook('doc_ready', function() {

    if ( !fv.single ) {
        // IF contest have any photos
        if ( document.querySelectorAll('.fv_contest_container .contest-block').length > 0 ) {
            // Try preload first full image
            var first_image_url = jQuery(".contest-block:first").find('a.fv_lightbox').attr('href');
            // if This url looks like correct Image url
            if ( first_image_url != undefined && first_image_url.match(/\.(jpeg|jpg|gif|png)$/) != null ) {
                setTimeout( function() {
                    var img = new Image();
                    img.src = first_image_url;
                }, 400 );
            }

            if ( fv.lazy_load ) {
                jQuery(".contest-block img.fv-lazy").unveil(100, function() {
                    //jQuery(this).load(function() {
                    FvLib.callHook('fv/public/lazy_new_loaded');
                    //});
                });
            }

            // if in query exists variable `photo`, then try to find link and open this photo
            if ( FvLib.queryString('photo') ) {
                setTimeout( function() {
                    $jQ('a[name="photo-' + FvLib.queryString('photo') + '"]').click();
                }, 1000 );
            }

        } else {
            FvLib.adminNotice(fv.lang.empty_contest, 'warning');
        }
    }

	// Add action to lost focus email field in upload form for Validate email
    jQuery(".fv_upload_form input[name='foto-email']").blur(function() {
        fv_validate_upload_email_and_show_errors(false);
    });

    if ( fv.soc_shows.email == 'inline' || fv.security_type == "defaultArecaptcha" || fv.security_type == "cookieArecaptcha" ) {
        jQuery.getScript('https://www.google.com/recaptcha/api.js?render=explicit');
    }

    // =================================================
    // TOOLBAR
    if ( document.querySelector('.fv_toolbar') != null ) {
        jQuery(".fv_toolbar .fv_sorting").change(function() {
            location.replace( jQuery(this).val() );
        });

        var ink, d, x, y;
        jQuery(".fv_toolbar .tabbed_a").click(function(e) {
            if ( jQuery(this).hasClass('active') ) {
                return false;
            }

            var target = jQuery(this).data('target');

            // Content
            jQuery( '.tabbed_c:not(' + target + ')' ).hide();
            jQuery( '.tabbed_c' + jQuery(this).data('target') ).fadeIn();

            // Links
            jQuery( '.fv_toolbar .tabbed_a' ).removeClass('active');
            jQuery(this).addClass('active');

            // Animations
            if(jQuery(this).find(".ink").length === 0){
                jQuery(this).prepend("<span class='ink'></span>");
            }

            ink = jQuery(this).find(".ink");
            ink.removeClass("animate");

            if(!ink.height() && !ink.width()){
                d = Math.max(jQuery(this).outerWidth(), jQuery(this).outerHeight());
                ink.css({height: d, width: d});
            }

            x = e.pageX - jQuery(this).offset().left - ink.width()/2;
            y = e.pageY - jQuery(this).offset().top - ink.height()/2;

            ink.css({top: y+'px', left: x+'px'}).addClass("animate");
        });

    }

    //if ( FvLib.queryString('fv-scroll') &&  jQuery('.' + FvLib.queryString('fv-scroll')).length == 1 ) {
    if ( window.location.hash.substring(1) == 'contest' ) {
        jQuery('html, body').animate({
            scrollTop: jQuery( '.fv_contest_container' ).offset().top - 30
        }, 500);
    }
    // TOOLBAR :: END
    // =================================================

    if ( !fv.single && fv.cache_support ) {
        var send_data = [];
        for(var key in fv.data) {
            if ( key != 'link' ) {
                send_data.push( key );
            }
        }
        //console.log( send_data );
        /*for (var i=0; i<fv.data.length; i++) {
            send_data.push( fv.data[i].id );
        }*/
        $jQ.post(
            fv.ajax_url + '?fuckcache=' + FvLib.randomStr(10),
            {action: 'fv_ajax_get_votes', ids: send_data},
            function (data) {
                data = FvLib.parseJson(data);
                //console.log(data);
                if ( data.res = 'ok' && typeof data.votes != "undefinded" ) {
                    for(var key in data.votes) {
                        if ( document.querySelector('.sv_votes_' + key) != null ) {
                            document.querySelector('.sv_votes_' + key).innerHTML = data.votes[key];
                        }
                    }
                }
            }
        ).fail(function() {
            FvLib.adminNotice(fv.lang.ajax_fail, 'error');
        });;
    }

    if ( !fv.contest_enabled ) {
        FvLib.adminNotice(fv.lang.inactive_contest, 'warning');
    }

	FvLib.callHook('fv/init');
});

if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) { fv_new_text(document); }

function fv_ajax_go_to_page(page, contest_id, sorting, s_string, infinite) {
    // check, may be data is loading
    if ( jQuery('.fv-contest-photos-container').hasClass('preload') ) {
        return;
    }
    if (infinite == undefined || !infinite) {
        infinite = false;
    }
    var params = {
        action: 'fv_ajax_go_to_page',
        contest_id: contest_id,
        post_id: fv.post_id,
        'fv-sorting': sorting,
        'fv-page': page,
        some_str: s_string
    };

    params = FvLib.applyFilters('fv/ajax_go_to_page/params', params);

    jQuery('.fv-contest-photos-container').addClass('preload');
    jQuery.get(
        fv.ajax_url,
        params,
        function (response) {
            //jQuery('.fv-contest-photos-container').removeClass('preload');
            response = FvLib.parseJson(response);

            //console.log( response );

            if ( response.result == "ok" ) {
                FvLib.callHook('fv/ajax_go_to_page/resp_ok', page, contest_id);

                var $photos_container = jQuery('.fv-contest-photos-container');
                if ( !infinite ) {
                    $photos_container.replaceWith(response.html);
                    fv.data = response.photos_data;
                    fv.page_url = response.share_page_url;
                    jQuery('#photo_id').attr("data-url", response.share_page_url + '=');
                    setTimeout(
                        function(){
                            jQuery('html, body').animate({scrollTop: jQuery('.fv_contest_container').offset().top - 50}, 500)
                        }, 300
                    );

                } else {
                    $photos_container.removeClass('preload').find('.infinite').remove();
                    var infiniteContainerSelector = FvLib.applyFilters('fv/fv_ajax_go_to_page/infinite_selector', '.fv-contest-photos-container-inner');
                    var $infiniteHtml = jQuery(response.html);
                    if ( infiniteContainerSelector != false ) {
                        $photos_container.find(infiniteContainerSelector).append( $infiniteHtml.find('.contest-block') );
                    } else {
                        $photos_container.append( $infiniteHtml.find('.contest-block') );
                    }
                    $photos_container.append( $infiniteHtml.find('nav') );

                    fv.data = jQuery.extend({}, fv.data, response.photos_data);;
                    fv.page_url = response.share_page_url;
                    /*jQuery('html, body').animate({
                        scrollTop: $photos_container.offset().top + $photos_container.height() - 250
                    }, 500);*/
                }

                FvLib.callHook('fv/ajax_go_to_page/ready', page, contest_id);

                if ( page > 1 ) {
                    window.history.pushState('', '', fv.paged_url + page + '#contest' );
                } else {
                    window.history.pushState('', '', fv.paged_url.replace("?fv-page=","") + '#contest' );
                }

                if ( fv.social_counter ) {
                    fv_run_social_counter();
                }

            } else if ( response.result == "fail" ) {
                alert( response.msg );
            }
        }
    );
}

/**
 * Function used for send social data taken from Ulogin.ru to server, for save in $_SESSION
 * After successful sending Run vote function
 */
function ulogin_data(token) {
    $jQ('#sv_dialog #fv_social_form').hide();
    jQuery.getJSON("//ulogin.ru/token.php?host=" +
        encodeURIComponent(location.toString()) + "&token=" + token + "&callback=?",
        function (data) {
            data = jQuery.parseJSON(data.toString());
            if (!data.error) {
                var send_data = { 'action': 'fv_soc_login', 'contest_id': fv.contest_id, 'fuckcache': FvLib.randomStr(8), 'some_str': fv.some_str };
                send_data['email'] = data.email;
                send_data['soc_name'] = data.first_name;
                send_data['soc_profile'] = data.profile;
                send_data['soc_network'] = data.network;
                send_data['soc_uid'] = data.uid;

                $jQ.post(fv.ajax_url, send_data,
                    function (data) {
                        if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;
                        data = FvLib.parseJson(data);
                        if (data.res == "authorized") {
                            fv_subscribed = true;
                            sv_vote(fv_current_id);
                        }
                    });
                //console.log(data);
            }
        });
}

/**
 * Function used for Check Fb login state and run subscribe (save soc. data to $_SESSION)
 */
function fv_fb_login() {
    if (FB.getAuthResponse() != null) {
        fv_fb_subscribe();
        return;
    }
    // try log In
    FB.login(function (response) {
        //do whatever you need to do after a (un)successfull login
        if (response.status == 'connected') {
            // the user is logged in and has authenticated your APP
            console.log( response );
            fv_fb_subscribe();

        } else if (response.status == 'not_authorized') {
            // the user is logged in to Facebook,
            // but has not authenticated your app
            alert('not_authorized');
        } else {
            // the user isn't logged in to Facebook.
            alert('the user isn`t logged in to Facebook.');
        }

    }, {scope: 'public_profile,email'});
}

/**
 * GET Fb user data (name, email) and run subscribe (save soc. data to $_SESSION)
 */
function fv_fb_subscribe() {
    FB.api('/me?fields=name,email,age_range', function (fb_user_info) {
        console.log( fb_user_info );
        var send_data = { 'action': 'fv_soc_login', 'contest_id': fv.contest_id, 'fuckcache': FvLib.randomStr(8), 'some_str': fv.some_str };
        send_data['email'] = '-';
        if ( fb_user_info.hasOwnProperty('email') ) {
            send_data['email'] = fb_user_info.email;
        }
        send_data['soc_name'] = '-';
        if ( fb_user_info.hasOwnProperty('name') ) {
            send_data['soc_name'] = fb_user_info.name;
        }
        if ( fb_user_info.hasOwnProperty('age_range') ) {
            if ( typeof(fb_user_info.age_range.min) != "undefined" ) {
                send_data['soc_name'] = send_data['soc_name'] + ' / ' + fb_user_info.age_range.min + '+';
            }
            if ( typeof(fb_user_info.age_range.max) != "undefined" ) {
                send_data['soc_name'] += ' / -' + fb_user_info.age_range.max;
            }
        }
        send_data['soc_profile'] = 'https://www.facebook.com/profile.php?id=' + fb_user_info.id;
        send_data['soc_network'] = 'facebook';
        send_data['soc_uid'] = fb_user_info.id;

        $jQ.post(fv.ajax_url, send_data,
        function (data) {
            if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;
            data = FvLib.parseJson(data);
            if (data.res == "authorized") {
                fv_subscribed = true;
                sv_vote(fv_current_id);
            }
        });
        //console.log(data);

    });
}

function fv_identify_plugins(){
    // fetch and serialize plugins
    var plugins = "";
    // in Mozilla and in fact most non-IE browsers, this is easy
    if (navigator.plugins) {
        var np = navigator.plugins;
        var plist = new Array();
        // sorting navigator.plugins is a right royal pain
        // but it seems to be necessary because their order
        // is non-constant in some browsers
        for (var i = 0; i < np.length; i++) {
            plist[i] = np[i].name + "; ";
            plist[i] += np[i].description + "; ";
            plist[i] += np[i].filename + ";";
            for (var n = 0; n < np[i].length; n++) {
                plist[i] += " (" + np[i][n].description +"; "+ np[i][n].type +
                    "; "+ np[i][n].suffixes + ")";
            }
            plist[i] += ". ";
        }
        plist.sort();
        for (i = 0; i < plist.length; i++) {
            plugins+= i+": " + plist[i];
        }
    }
    // in IE, things are much harder; we use PluginDetect to get less
    // information (only the plugins listed below & their version numbers)
    if (plugins == "") {
        var pp = new Array();
        pp[0] = "Java"; pp[1] = "QuickTime"; pp[2] = "DevalVR"; pp[3] = "Shockwave";
        pp[4] = "Flash"; pp[5] = "WindowsMediaplayer"; pp[6] = "Silverlight";
        pp[7] = "VLC";
        var version;
        for ( p in pp ) {
            version = PluginDetect.getVersion(pp[p]);
            if (version)
                plugins += pp[p] + " " + version + "; "
        }
        plugins += fv_ieAcrobatVersion();
    }
    return plugins;
}

function fv_ieAcrobatVersion() {
    // estimate the version of Acrobat on IE using horrible horrible hacks
    if (window.ActiveXObject) {
        for (var x = 2; x < 10; x++) {
            try {
                oAcro=eval("new ActiveXObject('PDF.PdfCtrl."+x+"');");
                if (oAcro)
                    return "Adobe Acrobat version" + x + ".?";
            } catch(ex) {}
        }
        try {
            oAcro4=new ActiveXObject('PDF.PdfCtrl.1');
            if (oAcro4)
                return "Adobe Acrobat version 4.?";
        } catch(ex) {}
        try {
            oAcro7=new ActiveXObject('AcroPDF.PDF.1');
            if (oAcro7)
                return "Adobe Acrobat version 7.?";
        } catch (ex) {}
        return "";
    }
}

// fetch client-side vars
var fv_whorls = new Object();

function fv_fetch_client_whorls(){
    // this is a backup plan
    if ( fv_whorls['pp'] !== undefined ) {
        return;
    }

    try {
        fv_whorls['pp'] = FvLib.murmurhash3_32_gc( fv_identify_plugins(), 991 );
    } catch(ex) {
        fv_whorls['pp'] = 0;
        FvLib.logSave("plugins - permission denied")
    }

    //fv_whorls['fonts'] = get_fonts();
};
setTimeout("fv_fetch_client_whorls()",500);


if(!PluginDetect)var PluginDetect={getNum:function(e,t){if(!this.num(e))return null;var i;return i="undefined"==typeof t?/[\d][\d\.\_,-]*/.exec(e):new RegExp(t).exec(e),i?i[0].replace(/[\.\_-]/g,","):null},hasMimeType:function(e){if(PluginDetect.isIE)return null;var t,i,n,a=e.constructor==String?[e]:e;for(n=0;n<a.length;n++)if(t=navigator.mimeTypes[a[n]],t&&t.enabledPlugin&&(i=t.enabledPlugin,i.name||i.description))return t;return null},findNavPlugin:function(e,t){var i,n=e.constructor==String?e:e.join(".*"),a=t===!1?"":"\\d",r=new RegExp(n+".*"+a+"|"+a+".*"+n,"i"),s=navigator.plugins;for(i=0;i<s.length;i++)if(r.test(s[i].description)||r.test(s[i].name))return s[i];return null},AXO:window.ActiveXObject,getAXO:function(e,t){var i=null,n=!1;try{i=new this.AXO(e),n=!0}catch(a){}if("undefined"!=typeof t){try{i.closeKeyStore(),i=null,CollectGarbage()}catch(r){}return n}return i},num:function(e){return"string"!=typeof e?!1:/\d/.test(e)},compareNums:function(e,t){var i,n,a,r=this,s=window.parseInt;if(!r.num(e)||!r.num(t))return 0;if(r.plugin&&r.plugin.compareNums)return r.plugin.compareNums(e,t);for(i=e.split(","),n=t.split(","),a=0;a<Math.min(i.length,n.length);a++){if(s(i[a],10)>s(n[a],10))return 1;if(s(i[a],10)<s(n[a],10))return-1}return 0},formatNum:function(e){if(!this.num(e))return null;var t,i=e.replace(/\s/g,"").replace(/[\.\_]/g,",").split(",").concat(["0","0","0","0"]);for(t=0;4>t;t++)/^(0+)(.+)$/.test(i[t])&&(i[t]=RegExp.$2);return/\d/.test(i[0])||(i[0]="0"),i[0]+","+i[1]+","+i[2]+","+i[3]},initScript:function(){var e=this,t=navigator.userAgent;if(e.isIE=!1,e.IEver=e.isIE&&/MSIE\s*(\d\.?\d*)/i.exec(t)?parseFloat(RegExp.$1,10):-1,e.ActiveXEnabled=!1,e.isIE){var i,n=["Msxml2.XMLHTTP","Msxml2.DOMDocument","Microsoft.XMLDOM","ShockwaveFlash.ShockwaveFlash","TDCCtl.TDCCtl","Shell.UIHelper","Scripting.Dictionary","wmplayer.ocx"];for(i=0;i<n.length;i++)if(e.getAXO(n[i],1)){e.ActiveXEnabled=!0;break}e.head="undefined"!=typeof document.getElementsByTagName?document.getElementsByTagName("head")[0]:null}e.isGecko=!e.isIE&&"string"==typeof navigator.product&&/Gecko/i.test(navigator.product)&&/Gecko\s*\/\s*\d/i.test(t)?!0:!1,e.GeckoRV=e.isGecko?e.formatNum(/rv\s*\:\s*([\.\,\d]+)/i.test(t)?RegExp.$1:"0.9"):null,e.isSafari=!e.isIE&&/Safari\s*\/\s*\d/i.test(t)?!0:!1,e.isChrome=/Chrome\s*\/\s*\d/i.test(t)?!0:!1,e.onWindowLoaded(0)},init:function(e,t){if("string"!=typeof e)return-3;e=e.toLowerCase().replace(/\s/g,"");var i,n=this;return"undefined"==typeof n[e]?-3:(i=n[e],n.plugin=i,("undefined"==typeof i.installed||1==t)&&(i.installed=null,i.version=null,i.version0=null,i.getVersionDone=null,i.$=n),n.garbage=!1,n.isIE&&!n.ActiveXEnabled&&n.plugin!=n.java?-2:1)},isMinVersion:function(){return-3},getVersion:function(e,t,i){var n,a=PluginDetect,r=a.init(e);return 0>r?null:(n=a.plugin,1!=n.getVersionDone&&(n.getVersion(t,i),null===n.getVersionDone&&(n.getVersionDone=1)),a.cleanup(),n.version||n.version0)},getInfo:function(e,t,i){var n,a={},r=PluginDetect,s=r.init(e);return 0>s?a:(n=r.plugin,"undefined"!=typeof n.getInfo&&(null===n.getVersionDone&&r.getVersion(e,t,i),a=n.getInfo()),a)},cleanup:function(){var e=this;e.garbage&&"undefined"!=typeof window.CollectGarbage&&window.CollectGarbage()},isActiveXObject:function(e){var t,i=this,n="/",a='<object width="1" height="1" style="display:none" '+i.plugin.getCodeBaseVersion(e)+">"+i.plugin.HTML+"<"+n+"object>";i.head.firstChild?i.head.insertBefore(document.createElement("object"),i.head.firstChild):i.head.appendChild(document.createElement("object")),i.head.firstChild.outerHTML=a;try{i.head.firstChild.classid=i.plugin.classID}catch(r){}t=!1;try{i.head.firstChild.object&&(t=!0)}catch(r){}try{t&&i.head.firstChild.readyState<4&&(i.garbage=!0)}catch(r){}return i.head.removeChild(i.head.firstChild),t},codebaseSearch:function(e){var t=this;if(!t.ActiveXEnabled)return null;if("undefined"!=typeof e)return t.isActiveXObject(e);var i,n,a,r,s=[0,0,0,0],l=t.plugin.digits,o=function(e,i){var n=(0==e?i:s[0])+","+(1==e?i:s[1])+","+(2==e?i:s[2])+","+(3==e?i:s[3]);return t.isActiveXObject(n)},u=!1;for(i=0;i<l.length;i++){for(a=2*l[i],s[i]=0,n=0;20>n&&!(1==a&&i>0&&u);n++){if(!(a-s[i]>1)){if(a-s[i]==1){a--,!u&&o(i,a)&&(u=!0);break}!u&&o(i,a)&&(u=!0);break}r=Math.round((a+s[i])/2),o(i,r)?(s[i]=r,u=!0):a=r}if(!u)return null}return s.join(",")},dummy1:0};PluginDetect.onDetectionDone=function(){return-1},PluginDetect.onWindowLoaded=function(e){var t=PluginDetect,i=window;t.EventWinLoad===!0||(t.winLoaded=!1,t.EventWinLoad=!0,"undefined"!=typeof i.addEventListener?i.addEventListener("load",t.runFuncs,!1):"undefined"!=typeof i.attachEvent?i.attachEvent("onload",t.runFuncs):("function"==typeof i.onload&&(t.funcs[t.funcs.length]=i.onload),i.onload=t.runFuncs)),"function"==typeof e&&(t.funcs[t.funcs.length]=e)},PluginDetect.funcs=[0],PluginDetect.runFuncs=function(){var e,t=PluginDetect;for(t.winLoaded=!0,e=0;e<t.funcs.length;e++)"function"==typeof t.funcs[e]&&(t.funcs[e](t),t.funcs[e]=null)},PluginDetect.quicktime={mimeType:["video/quicktime","application/x-quicktimeplayer","image/x-macpaint","image/x-quicktime"],progID:"QuickTimeCheckObject.QuickTimeCheck.1",progID0:"QuickTime.QuickTime",classID:"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B",minIEver:7,HTML:'<param name="src" value="A14999.mov" /><param name="controller" value="false" />',getCodeBaseVersion:function(e){return'codebase="#version='+e+'"'},digits:[8,64,16,0],clipTo3digits:function(e){if(null===e||"undefined"==typeof e)return null;var t,i,n,a=this.$;return t=e.split(","),i=a.compareNums(e,"7,60,0,0")<0&&a.compareNums(e,"7,50,0,0")>=0?t[0]+","+t[1].charAt(0)+","+t[1].charAt(1)+","+t[2]:t[0]+","+t[1]+","+t[2]+","+t[3],n=i.split(","),n[0]+","+n[1]+","+n[2]+",0"},getVersion:function(){var e,t=null,i=this.$,n=!0;if(i.isIE){var a;i.IEver>=this.minIEver&&i.getAXO(this.progID0,1)?t=i.codebaseSearch():(a=i.getAXO(this.progID),a&&a.QuickTimeVersion&&(t=a.QuickTimeVersion.toString(16),t=t.charAt(0)+"."+t.charAt(1)+"."+t.charAt(2))),this.installed=t?1:i.getAXO(this.progID0,1)?0:-1}else navigator.platform&&/linux/i.test(navigator.platform)&&(n=!1),n&&(e=i.findNavPlugin(["QuickTime","(Plug-in|Plugin)"]),e&&e.name&&i.hasMimeType(this.mimeType)&&(t=i.getNum(e.name))),this.installed=t?1:-1;this.version=this.clipTo3digits(i.formatNum(t))}},PluginDetect.java={mimeType:"application/x-java-applet",classID:"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93",DTKclassID:"clsid:CAFEEFAC-DEC7-0000-0000-ABCDEFFEDCBA",DTKmimeType:"application/npruntime-scriptable-plugin;DeploymentToolkit",JavaVersions:[[1,9,2,25],[1,8,2,25],[1,7,2,25],[1,6,2,25],[1,5,2,25],[1,4,2,25],[1,3,1,25]],searchJavaPluginAXO:function(){var e=null,t=this,i=t.$,n=[],a=[1,5,0,14],r=[1,6,0,2],s=[1,3,1,0],l=[1,4,2,0],o=[1,5,0,7],u=!1;return i.ActiveXEnabled?(u=!0,i.IEver>=t.minIEver?(n=t.searchJavaAXO(r,r,u),n.length>0&&u&&(n=t.searchJavaAXO(a,a,u))):(u&&(n=t.searchJavaAXO(o,o,!0)),0==n.length&&(n=t.searchJavaAXO(s,l,!1))),n.length>0&&(e=n[0]),t.JavaPlugin_versions=[].concat(n),e):null},searchJavaAXO:function(e,t,i){var n,a,r,s,l,o,u,c,p,g=this.$,d=[];g.compareNums(e.join(","),t.join(","))>0&&(t=e),t=g.formatNum(t.join(","));var v,f="1,4,2,0",h="JavaPlugin."+e[0]+e[1]+e[2]+(e[3]>0?"_"+(e[3]<10?"0":"")+e[3]:"");for(n=0;n<this.JavaVersions.length;n++)for(a=this.JavaVersions[n],r="JavaPlugin."+a[0]+a[1],u=a[0]+"."+a[1]+".",l=a[2];l>=0;l--)if(p="JavaWebStart.isInstalled."+u+l+".0",!(g.compareNums(a[0]+","+a[1]+","+l+",0",t)>=0)||g.getAXO(p,1)){for(v=g.compareNums(a[0]+","+a[1]+","+l+",0",f)<0?!0:!1,o=a[3];o>=0;o--){if(s=l+"_"+(10>o?"0"+o:o),c=r+s,g.getAXO(c,1)&&(v||g.getAXO(p,1))&&(d[d.length]=u+s,!i))return d;if(c==h)return d}if(g.getAXO(r+l,1)&&(v||g.getAXO(p,1))&&(d[d.length]=u+l,!i))return d;if(r+l==h)return d}return d},minIEver:7,getFromMimeType:function(e){var t,i,n,a,r,s=this.$,l=new RegExp(e),o={},u=0,c=[""];for(t=0;t<navigator.mimeTypes.length;t++)a=navigator.mimeTypes[t],l.test(a.type)&&a.enabledPlugin&&(a=a.type.substring(a.type.indexOf("=")+1,a.type.length),n="a"+s.formatNum(a),"undefined"==typeof o[n]&&(o[n]=a,u++));for(i=0;u>i;i++){r="0,0,0,0";for(t in o)o[t]&&(n=t.substring(1,t.length),s.compareNums(n,r)>0&&(r=n));c[i]=o["a"+r],o["a"+r]=null}return/windows|macintosh/i.test(navigator.userAgent)||(c=[c[0]]),c},queryJavaHandler:function(){var e=PluginDetect.java,t=window.java;e.hasRun=!0;try{"undefined"!=typeof t.lang&&"undefined"!=typeof t.lang.System&&(e.value=[t.lang.System.getProperty("java.version")+" ",t.lang.System.getProperty("java.vendor")+" "])}catch(i){}},queryJava:function(){var e=this,t=e.$,i=navigator.userAgent;if("undefined"!=typeof window.java&&navigator.javaEnabled()&&!e.hasRun)if(t.isGecko){if(t.hasMimeType("application/x-java-vm")){try{var n=document.createElement("div"),a=document.createEvent("HTMLEvents");a.initEvent("focus",!1,!0),n.addEventListener("focus",e.queryJavaHandler,!1),n.dispatchEvent(a)}catch(r){}e.hasRun||e.queryJavaHandler()}}else/opera.9\.(0|1)/i.test(i)&&/mac/i.test(i)||e.hasRun||e.queryJavaHandler();return e.value},forceVerifyTag:[],jar:[],VENDORS:["Sun Microsystems Inc.","Apple Computer, Inc."],init:function(){var e=this,t=e.$;"undefined"!=typeof e.app&&e.delJavaApplets(t),e.hasRun=!1,e.value=[null,null],e.useTag=[2,2,2],e.app=[0,0,0,0,0,0],e.appi=3,e.queryDTKresult=null,e.OTF=0,e.BridgeResult=[[null,null],[null,null],[null,null]],e.JavaActive=[0,0,0],e.All_versions=[],e.DeployTK_versions=[],e.MimeType_versions=[],e.JavaPlugin_versions=[],e.funcs=[];var i=e.NOTF;i&&(i.$=t,i.javaInterval&&clearInterval(i.javaInterval),i.EventJavaReady=null,i.javaInterval=null,i.count=0,i.intervalLength=250,i.countMax=40),e.lateDetection=t.winLoaded,e.lateDetection||t.onWindowLoaded(e.delJavaApplets)},getVersion:function(e,t){var i,n=this,a=n.$,r=null,s=null,l=null,o=navigator.javaEnabled();null===n.getVersionDone&&n.init();var u;if("undefined"!=typeof t&&t.constructor==Array)for(u=0;u<n.useTag.length;u++)"number"==typeof t[u]&&(n.useTag[u]=t[u]);for(u=0;u<n.forceVerifyTag.length;u++)n.useTag[u]=n.forceVerifyTag[u];if("undefined"!=typeof e&&(n.jar[n.jar.length]=e),0==n.getVersionDone)return(!n.version||n.useAnyTag())&&(i=n.queryExternalApplet(e),i[0]&&(l=i[0],s=i[1])),void n.EndGetVersion(l,s);var c=n.queryDeploymentToolKit();if("string"==typeof c&&c.length>0&&(r=c,s=n.VENDORS[0]),a.isIE)r||-1==c||(r=n.searchJavaPluginAXO(),r&&(s=n.VENDORS[0])),r||n.JavaFix(),r&&(n.version0=r,o&&a.ActiveXEnabled&&(l=r)),(!l||n.useAnyTag())&&(i=n.queryExternalApplet(e),i[0]&&(l=i[0],s=i[1]));else{var p,g,d,v,f;f=a.hasMimeType(n.mimeType),v=f&&o?!0:!1,0==n.MimeType_versions.length&&f&&(i=n.getFromMimeType("application/x-java-applet.*jpi-version.*="),""!=i[0]&&(r||(r=i[0]),n.MimeType_versions=i)),!r&&f&&(i="Java[^\\d]*Plug-in",d=a.findNavPlugin(i),d&&(i=new RegExp(i,"i"),p=i.test(d.description)?a.getNum(d.description):null,g=i.test(d.name)?a.getNum(d.name):null,r=p&&g?a.compareNums(a.formatNum(p),a.formatNum(g))>=0?p:g:p||g)),!r&&f&&/macintosh.*safari/i.test(navigator.userAgent)&&(d=a.findNavPlugin("Java.*\\d.*Plug-in.*Cocoa",!1),d&&(p=a.getNum(d.description),p&&(r=p))),r&&(n.version0=r,o&&(l=r)),(!l||n.useAnyTag())&&(d=n.queryExternalApplet(e),d[0]&&(l=d[0],s=d[1])),l||(d=n.queryJava(),d[0]&&(n.version0=d[0],l=d[0],s=d[1],n.installed==-.5&&(n.installed=.5))),null!==n.installed||l||!v||/macintosh.*ppc/i.test(navigator.userAgent)||(i=n.getFromMimeType("application/x-java-applet.*version.*="),""!=i[0]&&(l=i[0])),!l&&v&&/macintosh.*safari/i.test(navigator.userAgent)&&(null===n.installed?n.installed=0:n.installed==-.5&&(n.installed=.5))}null===n.installed&&(n.installed=l?1:r?-.2:-1),n.EndGetVersion(l,s)},EndGetVersion:function(e,t){var i=this,n=i.$;i.version0&&(i.version0=n.formatNum(n.getNum(i.version0))),e&&(i.version=n.formatNum(n.getNum(e)),i.vendor="string"==typeof t?t:""),1!=i.getVersionDone&&(i.getVersionDone=0)},queryDeploymentToolKit:function(){var e,t=this,i=t.$,n=null,a=null;if((i.isGecko&&i.compareNums(i.GeckoRV,i.formatNum("1.6"))<=0||i.isSafari||i.isIE&&!i.ActiveXEnabled)&&(t.queryDTKresult=0),null!==t.queryDTKresult)return t.queryDTKresult;if(i.isIE&&i.IEver>=6?(t.app[0]=i.instantiate("object",[],[]),n=i.getObject(t.app[0])):!i.isIE&&i.hasMimeType(t.DTKmimeType)&&(t.app[0]=i.instantiate("object",["type",t.DTKmimeType],[]),n=i.getObject(t.app[0])),n){if(i.isIE&&i.IEver>=6)try{n.classid=t.DTKclassID}catch(r){}try{var s,l=n.jvms;if(l&&(a=l.getLength(),"number"==typeof a))for(e=0;a>e;e++)s=l.get(a-1-e),s&&(s=s.version,i.getNum(s)&&(t.DeployTK_versions[e]=s))}catch(r){}}return i.hideObject(n),t.queryDTKresult=t.DeployTK_versions.length>0?t.DeployTK_versions[0]:0==a?-1:0,t.queryDTKresult},queryExternalApplet:function(e){var t=this,i=t.$,n=t.BridgeResult,a=t.app,r=t.appi,s="&nbsp;&nbsp;&nbsp;&nbsp;";if("string"!=typeof e||!/\.jar\s*$/.test(e))return[null,null];if(t.OTF<1&&(t.OTF=1),!i.isIE&&(i.isGecko||i.isChrome)&&!i.hasMimeType(t.mimeType)&&!t.queryJava()[0])return[null,null];t.OTF<2&&(t.OTF=2),!a[r]&&t.canUseObjectTag()&&t.canUseThisTag(0)&&(a[1]=i.instantiate("object",[],[],s),a[r]=i.isIE?i.instantiate("object",["archive",e,"code","A.class","type",t.mimeType],["archive",e,"code","A.class","mayscript","true","scriptable","true"],s):i.instantiate("object",["archive",e,"classid","java:A.class","type",t.mimeType],["archive",e,"mayscript","true","scriptable","true"],s),n[0]=[0,0],t.query1Applet(r)),!a[r+1]&&t.canUseAppletTag()&&t.canUseThisTag(1)&&(a[r+1]=i.instantiate("applet",["archive",e,"code","A.class","alt",s,"mayscript","true"],["mayscript","true"],s),n[1]=[0,0],t.query1Applet(r+1)),i.isIE&&!a[r+2]&&t.canUseObjectTag()&&t.canUseThisTag(2)&&(a[r+2]=i.instantiate("object",["classid",t.classID],["archive",e,"code","A.class","mayscript","true","scriptable","true"],s),n[2]=[0,0],t.query1Applet(r+2));var l,o=0;for(l=0;l<n.length&&(a[r+l]||t.canUseThisTag(l));l++)o++;return o==n.length&&(t.getVersionDone=1,t.forceVerifyTag.length>0&&(t.getVersionDone=0)),t.getBR()},canUseAppletTag:function(){return!this.$.isIE||navigator.javaEnabled()?!0:!1},canUseObjectTag:function(){return!this.$.isIE||this.$.ActiveXEnabled?!0:!1},useAnyTag:function(){var e,t=this;for(e=0;e<t.useTag.length;e++)if(t.canUseThisTag(e))return!0;return!1},canUseThisTag:function(e){var t=this,i=t.$;if(3==t.useTag[e])return!0;if(!t.version0||!navigator.javaEnabled()||i.isIE&&!i.ActiveXEnabled){if(2==t.useTag[e])return!0;if(1==t.useTag[e]&&!t.getBR()[0])return!0}return!1},getBR:function(){var e,t=this.BridgeResult;for(e=0;e<t.length;e++)if(t[e][0])return[t[e][0],t[e][1]];return[t[0][0],t[0][1]]},delJavaApplets:function(e){var t,i=e.java.app;for(t=i.length-1;t>=0;t--)e.uninstantiate(i[t])},query1Applet:function(e){var t=this,i=t.$,n=null,a=null,r=i.getObject(t.app[e],!0);try{r&&(n=r.getVersion()+" ",a=r.getVendor()+" ",i.num(n)&&(t.BridgeResult[e-t.appi]=[n,a],i.hideObject(t.app[e])),i.isIE&&n&&4!=r.readyState&&(i.garbage=!0,i.uninstantiate(t.app[e])))}catch(s){}},NOTF:{isJavaActive:function(){}},append:function(e,t){for(var i=0;i<t.length;i++)e[e.length]=t[i]},getInfo:function(){var e,t={},i=this,n=i.$,a=i.installed;t={All_versions:[],DeployTK_versions:[],MimeType_versions:[],DeploymentToolkitPlugin:0==i.queryDTKresult?!1:!0,vendor:"string"==typeof i.vendor?i.vendor:"",OTF:i.OTF<3?0:3==i.OTF?1:2};var r=[null,null,null];for(e=0;e<i.BridgeResult.length;e++)r[e]=i.BridgeResult[e][0]?1:1==i.JavaActive[e]?0:i.useTag[e]>=1&&i.OTF>=1&&3!=i.OTF&&(2!=e||n.isIE)&&(null!==i.BridgeResult[e][0]||1==e&&!i.canUseAppletTag()||1!=e&&!i.canUseObjectTag()||a==-.2||-1==a)?-1:null;t.objectTag=r[0],t.appletTag=r[1],t.objectTagActiveX=r[2];var s=t.All_versions,l=t.DeployTK_versions,o=t.MimeType_versions,u=i.JavaPlugin_versions;for(i.append(l,i.DeployTK_versions),i.append(o,i.MimeType_versions),i.append(s,l.length>0?l:o.length>0?o:u.length>0?u:"string"==typeof i.version?[i.version]:[]),e=0;e<s.length;e++)s[e]=n.formatNum(n.getNum(s[e]));var c,p=null;n.isIE||(c=n.hasMimeType(o.length>0?i.mimeType+";jpi-version="+o[0]:i.mimeType),c&&(p=c.enabledPlugin)),t.name=p?p.name:"",t.description=p?p.description:"";var g=null;return 0!=a&&1!=a||""!=t.vendor||(/macintosh/i.test(navigator.userAgent)?g=i.VENDORS[1]:!n.isIE&&/windows/i.test(navigator.userAgent)?g=i.VENDORS[0]:/linux/i.test(navigator.userAgent)&&(g=i.VENDORS[0]),g&&(t.vendor=g)),t},JavaFix:function(){}},PluginDetect.devalvr={mimeType:"application/x-devalvrx",progID:"DevalVRXCtrl.DevalVRXCtrl.1",classID:"clsid:5D2CF9D0-113A-476B-986F-288B54571614",getVersion:function(){var e,t=null,i=this.$;if(i.isIE){var n,a,r;if(a=i.getAXO(this.progID,1)){if(n=i.instantiate("object",["classid",this.classID],["src",""]),r=i.getObject(n))try{r.pluginversion&&(t="00000000"+r.pluginversion.toString(16),t=t.substr(t.length-8,8),t=parseInt(t.substr(0,2),16)+","+parseInt(t.substr(2,2),16)+","+parseInt(t.substr(4,2),16)+","+parseInt(t.substr(6,2),16))}catch(s){}i.uninstantiate(n)}this.installed=t?1:a?0:-1}else e=i.findNavPlugin("DevalVR"),e&&e.name&&i.hasMimeType(this.mimeType)&&(t=e.description.split(" ")[3]),this.installed=t?1:-1;this.version=i.formatNum(t)}},PluginDetect.flash={mimeType:["application/x-shockwave-flash","application/futuresplash"],progID:"ShockwaveFlash.ShockwaveFlash",classID:"clsid:D27CDB6E-AE6D-11CF-96B8-444553540000",getVersion:function(){var e,t,i=function(e){if(!e)return null;var t=/[\d][\d\,\.\s]*[rRdD]{0,1}[\d\,]*/.exec(e);return t?t[0].replace(/[rRdD\.]/g,",").replace(/\s/g,""):null},n=this.$,a=null,r=null,s=null;if(n.isIE){for(t=15;t>2;t--)if(r=n.getAXO(this.progID+"."+t)){s=t.toString();break}if("6"==s)try{r.AllowScriptAccess="always"}catch(l){return"6,0,21,0"}try{a=i(r.GetVariable("$version"))}catch(l){}!a&&s&&(a=s)}else e=n.findNavPlugin("Flash"),e&&e.description&&n.hasMimeType(this.mimeType)&&(a=i(e.description));return this.installed=a?1:-1,this.version=n.formatNum(a),!0}},PluginDetect.shockwave={mimeType:"application/x-director",progID:"SWCtl.SWCtl",classID:"clsid:166B1BCA-3F9C-11CF-8075-444553540000",getVersion:function(){var e,t=null,i=null,n=this.$;if(n.isIE){try{i=n.getAXO(this.progID).ShockwaveVersion("")}catch(a){}"string"==typeof i&&i.length>0?t=n.getNum(i):n.getAXO(this.progID+".8",1)?t="8":n.getAXO(this.progID+".7",1)?t="7":n.getAXO(this.progID+".1",1)&&(t="6")}else e=n.findNavPlugin("Shockwave for Director"),e&&e.description&&n.hasMimeType(this.mimeType)&&(t=n.getNum(e.description));this.installed=t?1:-1,this.version=n.formatNum(t)}},PluginDetect.div=null,PluginDetect.pluginSize=1,PluginDetect.DOMbody=null,PluginDetect.uninstantiate=function(e){var t=this;if(e)try{e[0]&&e[0].firstChild&&e[0].removeChild(e[0].firstChild),e[0]&&t.div&&t.div.removeChild(e[0]),t.div&&0==t.div.childNodes.length&&(t.div.parentNode.removeChild(t.div),t.div=null,t.DOMbody&&t.DOMbody.parentNode&&t.DOMbody.parentNode.removeChild(t.DOMbody),t.DOMbody=null),e[0]=null}catch(i){}},PluginDetect.getObject=function(e,t){var i=null;try{e&&e[0]&&e[0].firstChild&&(i=e[0].firstChild)}catch(n){}try{t&&i&&"undefined"!=typeof i.focus&&"undefined"!=typeof document.hasFocus&&!document.hasFocus()&&i.focus()}catch(n){}return i},PluginDetect.getContainer=function(e){var t=null;return e&&e[0]&&(t=e[0]),t},PluginDetect.hideObject=function(e){var t=this.getObject(e);t&&t.style&&(t.style.height="0")},PluginDetect.instantiate=function(e,t,i,n){var a,r,s,l=function(e){var t=e.style;t&&(t.border="0px",t.padding="0px",t.margin="0px",t.fontSize=u.pluginSize+3+"px",t.height=u.pluginSize+3+"px",t.visibility="visible",e.tagName&&"div"==e.tagName.toLowerCase()?(t.width="100%",t.display="block"):e.tagName&&"span"==e.tagName.toLowerCase()&&(t.width=u.pluginSize+"px",t.display="inline"))},o=document,u=this,c=o.getElementsByTagName("body")[0]||o.body,p=o.createElement("span"),g="/";for("undefined"==typeof n&&(n=""),a="<"+e+' width="'+u.pluginSize+'" height="'+u.pluginSize+'" ',r=0;r<t.length;r+=2)a+=t[r]+'="'+t[r+1]+'" ';for(a+=">",r=0;r<i.length;r+=2)a+='<param name="'+i[r]+'" value="'+i[r+1]+'" />';if(a+=n+"<"+g+e+">",!u.div){if(u.div=o.createElement("div"),s=o.getElementById("plugindetect"))l(s),s.appendChild(u.div);else if(c)try{c.firstChild&&"undefined"!=typeof c.insertBefore?c.insertBefore(u.div,c.firstChild):c.appendChild(u.div)}catch(d){}else try{o.write('<div id="pd33993399">o<'+g+"div>"),c=o.getElementsByTagName("body")[0]||o.body,c.appendChild(u.div),c.removeChild(o.getElementById("pd33993399"))}catch(d){try{u.DOMbody=o.createElement("body"),o.getElementsByTagName("html")[0].appendChild(u.DOMbody),u.DOMbody.appendChild(u.div)}catch(d){}}l(u.div)}if(u.div&&u.div.parentNode&&u.div.parentNode.parentNode){u.div.appendChild(p);try{p.innerHTML=a}catch(d){}return l(p),[p]}return[null]},PluginDetect.windowsmediaplayer={mimeType:["application/x-mplayer2","application/asx"],progID:"wmplayer.ocx",classID:"clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6",getVersion:function(){var e=null,t=this.$,i=null;if(this.installed=-1,t.isIE)i=t.getAXO(this.progID),i&&(e=i.versionInfo);else if(t.hasMimeType(this.mimeType)){(t.findNavPlugin(["Windows","Media","(Plug-in|Plugin)"],!1)||t.findNavPlugin(["Flip4Mac","Windows","Media"],!1))&&(this.installed=0);var n=t.isGecko&&t.compareNums(t.GeckoRV,t.formatNum("1.8"))<0;if(!n&&t.findNavPlugin(["Windows","Media","Firefox Plugin"],!1)){var a=t.instantiate("object",["type",this.mimeType[0]],[]),r=t.getObject(a);r&&(e=r.versionInfo),t.uninstantiate(a)}}e&&(this.installed=1),this.version=t.formatNum(e)}},PluginDetect.silverlight={mimeType:"application/x-silverlight",progID:"AgControl.AgControl",digits:[9,20,9,12,31],getVersion:function(){var e=this.$,t=(document,null),i=null,n=!1;if(e.isIE){i=e.getAXO(this.progID);var a,r,s,l=[1,0,1,1,1],o=function(e){return(10>e?"0":"")+e.toString()},u=function(e,t,i,n,a){return e+"."+t+"."+i+o(n)+o(a)+".0"},c=function(e,t){var n=u(0==e?t:l[0],1==e?t:l[1],2==e?t:l[2],3==e?t:l[3],4==e?t:l[4]);try{return i.IsVersionSupported(n)}catch(a){}return!1};if(i&&"undefined"!=typeof i.IsVersionSupported){for(a=0;a<this.digits.length;a++){for(s=l[a],r=s+(0==a?0:1);r<=this.digits[a]&&c(a,r);r++)n=!0,l[a]=r;if(!n)break}n&&(t=u(l[0],l[1],l[2],l[3],l[4]))}}else{var p=[null,null],g=e.findNavPlugin("Silverlight Plug-in",!1),d=e.isGecko&&e.compareNums(e.GeckoRV,e.formatNum("1.6"))<=0;g&&e.hasMimeType(this.mimeType)&&(t=e.formatNum(g.description),t&&(l=t.split(","),parseInt(l[2],10)>=30226&&parseInt(l[0],10)<2&&(l[0]="2"),t=l.join(",")),e.isGecko&&!d&&(n=!0),n||d||!t||(p=e.instantiate("object",["type",this.mimeType],[]),i=e.getObject(p),i&&("undefined"!=typeof i.IsVersionSupported&&(n=!0),n||(i.data="data:"+this.mimeType+",","undefined"!=typeof i.IsVersionSupported&&(n=!0))),e.uninstantiate(p)))}this.installed=n?1:-1,this.version=e.formatNum(t)}},PluginDetect.vlc={mimeType:"application/x-vlc-plugin",progID:"VideoLAN.VLCPlugin",compareNums:function(e,t){var i,n,a,r,s,l,o=e.split(","),u=t.split(",");for(i=0;i<Math.min(o.length,u.length);i++){if(l=/([\d]+)([a-z]?)/.test(o[i]),n=parseInt(RegExp.$1,10),r=2==i&&RegExp.$2.length>0?RegExp.$2.charCodeAt(0):-1,l=/([\d]+)([a-z]?)/.test(u[i]),a=parseInt(RegExp.$1,10),s=2==i&&RegExp.$2.length>0?RegExp.$2.charCodeAt(0):-1,n!=a)return n>a?1:-1;if(2==i&&r!=s)return r>s?1:-1}return 0},getVersion:function(){var e,t=this.$,i=null;if(t.isIE){if(e=t.getAXO(this.progID))try{i=t.getNum(e.VersionInfo,"[\\d][\\d\\.]*[a-z]*")}catch(n){}this.installed=e?1:-1}else t.hasMimeType(this.mimeType)&&(e=t.findNavPlugin(["VLC","(Plug-in|Plugin)"],!1),e&&e.description&&(i=t.getNum(e.description,"[\\d][\\d\\.]*[a-z]*"))),this.installed=i?1:-1;this.version=t.formatNum(i)}},PluginDetect.initScript();


var fv_soc_counter_callbacks = {};

function fv_get_count_Fb_all(photos_count) {
    /*
     FB.api(
     '/http%3A%2F%2Fwp-vote.net%2Fdemo-photo-contest-pinterest-theme%2F%3Fcontest_id%3D2%26photo%3D2',
     'GET',
     {},
     function(response) {
     // Insert your code here
     }
     );
     */

    //http://graph.facebook.com/?ids=http://wp-vote.net/,http://wp-vote.net/pricing/
    /*
     {
     "http://wp-vote.net/": {
     "id": "http://wp-vote.net/",
     "shares": 8
     },
     "http://wp-vote.net/pricing/": {
     "id": "http://wp-vote.net/pricing/",
     "shares": 1
     }
     }
     */
    //http://api.facebook.com/restserver.php?method=links.getStats&urls=http://wp-vote.net/,http://wp-vote.net/pricing/
    /*

     <?xml version="1.0" encoding="UTF-8"?>
     <links_getStats_response xmlns="http://api.facebook.com/1.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://api.facebook.com/1.0/ http://api.facebook.com/1.0/facebook.xsd" list="true">
     <link_stat>
     <url>http://wp-vote.net/</url>
     <normalized_url>http://www.wp-vote.net/</normalized_url>
     <share_count>3</share_count>
     <like_count>4</like_count>
     <comment_count>1</comment_count>
     <total_count>8</total_count>
     <click_count>0</click_count>
     <comments_fbid>316931095121571</comments_fbid>
     <commentsbox_count>0</commentsbox_count>
     </link_stat>
     <link_stat>
     <url>http://wp-vote.net/pricing/</url>
     <normalized_url>http://www.wp-vote.net/pricing/</normalized_url>
     <share_count>1</share_count>
     <like_count>0</like_count>
     <comment_count>0</comment_count>
     <total_count>1</total_count>
     <click_count>0</click_count>
     <comments_fbid>645097812250216</comments_fbid>
     <commentsbox_count>0</commentsbox_count>
     </link_stat>
     </links_getStats_response>

     */
    var urls = '';

    for(var ID in fv.data) {
        if (fv.data.hasOwnProperty(ID) && ID != 'link') {
            //var attr = object[index];
            urls += encodeURIComponent(fv.page_url + '=' + ID) + ',';
        }
    }
    urls = urls.substring(0, urls.length - 1);

    //var link = 'https://api.facebook.com/method/fql.query?query=select total_count from link_stat where url="' + encodeURIComponent(url) + '"&format=json&callback=?';
    var link = 'http://graph.facebook.com/?ids=' + urls;
    jQuery.getJSON(link, function(dataArr) {

        for(var ID in fv.data) {
            if ( fv.data.hasOwnProperty(ID) && ID != 'link' && !fv.data[ID].hasOwnProperty('sc') ) {
                FvLib.logSave('get FB for ' + ID + ' #' + dataArr[fv.page_url + '=' + ID]['shares'] );
                if ( dataArr[fv.page_url + '=' + ID].hasOwnProperty("shares") && dataArr[fv.page_url + '=' + ID]['shares'] > 0 ) {
                    fv_add_soc_count( ID, dataArr[fv.page_url + '=' + ID]['shares'] );
                }
            }
        }

    });
}

function fv_get_count_Tw(url, id) {
    //var link = 'http://urls.api.twitter.com/1/urls/count.json?url=' + encodeURIComponent(url);
    var link = 'https://cdn.api.twitter.com/1/urls/count.json?url=' + encodeURIComponent(url) + '&callback=?';

    jQuery.getJSON(link, function(dataObj) {
        FvLib.logSave('get TW for ID ' + id + ' #' + dataObj.count) ;
        if ( dataObj.hasOwnProperty("count") && dataObj.count > 0 ) {
            fv_add_soc_count(id, dataObj.count);
        }
    });
}

function fv_get_count_Vk(url, id) {

    if ( !window.VK || !window.VK.Share ) {
        window.VK = {};
        window.VK.Share = {
            count: function(idx, shares) {
                FvLib.logSave('get VK for ID ' + idx + ' #' + shares) ;
                if ( shares > 0 ) {
                    fv_add_soc_count( idx, shares );
                }
            }
        };
    }

    var link = 'http://vkontakte.ru/share.php?act=count&index=' + id + '&url=' + encodeURIComponent(url);

    jQuery.getScript(link);
}

function fv_get_count_Ok(url, id) {

    if (!window.ODKL || !window.ODKL.updateCount) {
        window.ODKL = {};
        window.ODKL.updateCount = function(idx, shares) {
            FvLib.logSave('get OK for ID ' + idx + ' #' + shares) ;
            if ( shares > 0 ) {
                fv_add_soc_count( idx, shares );
            }
        };
    }

    var link = 'http://connect.ok.ru/dk?st.cmd=extLike&ref=' + encodeURIComponent(url) + '&uid=' + id;
    jQuery.getScript(link);
}

function fv_get_count_Mm_all(url, id) {
    var urls = '';

    for(var ID in fv.data) {
        if (fv.data.hasOwnProperty(ID) && ID != 'link') {
            //var attr = object[index];
            urls += encodeURIComponent(fv.page_url + '=' + ID) + ',';
        }
    }
    urls = urls.substring(0, urls.length - 1);

    var callbk_name = 'mm_all';
    var link = 'http://connect.mail.ru/share_count?callback=1&url_list=' + urls + '&func=fv_soc_counter_callbacks.' + callbk_name;

    fv_soc_counter_callbacks[callbk_name] = function(respObj){
        // upon success, remove the name
        delete fv_soc_counter_callbacks[callbk_name];

        if ( jQuery.isEmptyObject(respObj) ) {
            FvLib.logSave('res MM  - all 0') ;
            return false;
        }

        for(var ID in fv.data) {
            if ( fv.data.hasOwnProperty(ID) && ID != 'link' && !fv.data[ID].hasOwnProperty('sc') ) {
                if ( respObj[fv.page_url + '=' + ID].hasOwnProperty("shares") && respObj[fv.page_url + '=' + ID]['shares'] > 0 ) {
                    FvLib.logSave('res MM for = ' + fv.page_url + '=' + ID + ' #' + respObj[fv.page_url + '=' + ID]['shares']) ;

                    fv_add_soc_count( ID, respObj[fv.page_url + '=' + ID]['shares'] );
                }
            }
        }
    };

    jQuery.getScript(link);
}

function fv_get_count_Gp(url, id) {

    /*if (!window.services) {
     window.services = {};
     window.services.gplus = {
     cb: function(number) {
     console.log('res GP for = ID ' + window.services.gplus.id + ' #' + number) ;

     if (typeof number === 'string') {
     number = number.replace(/\D/g, '');
     }
     },
     counter: 0,
     id: 0
     };
     }
     window.services.gplus.counter = 0;
     window.services.gplus.id = id;

     jQuery.getScript(link);*/

    var callbk_name = 'gp'+id;
    var link = 'https://share.yandex.net/counter/gpp/?callback=fv_soc_counter_callbacks.' + callbk_name + '&url=' + encodeURIComponent(url);

    jQuery.getScript(link);

    fv_soc_counter_callbacks[callbk_name] = function(shares){
        // upon success, remove the name
        delete fv_soc_counter_callbacks[callbk_name];
        shares = parseInt(shares);
        FvLib.logSave('res GP for = ' + id + ' #' + shares) ;

        if (shares > 0) {
            fv_add_soc_count(id, shares);
        }
    };
}
function fv_get_count_Pi(url, id) {
    var callbk_name = 'pi'+id;
    var link = 'http://api.pinterest.com/v1/urls/count.json?callback=fv_soc_counter_callbacks.' + callbk_name + '&url=' + encodeURIComponent(url);

    jQuery.getScript(link);

    fv_soc_counter_callbacks[callbk_name] = function(shares_data){
        // upon success, remove the name
        delete fv_soc_counter_callbacks[callbk_name];
        var shares = parseInt(shares_data.count);
        FvLib.logSave('res Pi for = ' + id + ' #' + shares) ;

        if (shares > 0) {
            fv_add_soc_count(id, shares);
        }
    };
}

/**
 * Increase votes count in Html element with specified ID above image after voting
 */
function fv_add_soc_count(id, count) {
    var container = $jQ('.fv_svotes_' + id);
    var val = parseInt(container.html(), 10);
    if (!val) val = 0;
    val += parseInt(count);
    container.text(val);
}

function fv_run_social_counter() {
    // Soc COunters
    var photos_count = Object.keys(fv.data).length - 1;
    if( photos_count > 0 ) {
        if ( fv.soc_counters.fb ) {
            fv_get_count_Fb_all(photos_count);    //+
        }
        if ( fv.soc_counters.mm ) {
            fv_get_count_Mm_all(photos_count);    //+
        }
        for(var ID in fv.data) {
            if ( fv.data.hasOwnProperty(ID) && ID != 'link' && !fv.data[ID].hasOwnProperty('sc') ) {
                //var attr = object[index];
                var link = fv.page_url + '=' + ID;
                /*if ( fv.soc_counters.tw ) {
                    fv_get_count_Tw(link, ID);    //+
                }*/
                if ( fv.soc_counters.vk ) {
                    fv_get_count_Vk(link, ID);    //+
                }
                if ( fv.soc_counters.ok ) {
                    fv_get_count_Ok(link, ID);    //+
                }

                if ( fv.soc_counters.gp ) {
                    fv_get_count_Gp(link, ID);      //+
                }
                if ( fv.soc_counters.pi ) {
                    fv_get_count_Pi(link, ID);      //+
                }
                fv.data[ID]['sc'] = 1;
            }
        }

    }
}

if ( fv.soc_counter ) {
    jQuery( window ).load(function() {
        fv_run_social_counter();
    });

    FvLib.addHook('fv/ajax_go_to_page/ready', fv_run_social_counter, 11);
}

jQuery(document).ready( function(){
    if (jQuery("#bogadia_switcher").length){
        jQuery('.clg-like-button, .tabbed_a').on('click', function(){
            jQuery('#bogacontest_show_login').trigger('click');
            jQuery('.b-modal').fadeOut('fast');
            jQuery('#modal-widget').slideUp('slow');
        });
    }
});