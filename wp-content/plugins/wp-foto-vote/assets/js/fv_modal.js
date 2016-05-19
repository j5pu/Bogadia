/*
 Plugin Name: WP Foto Vote
 Plugin URI: http://wp-vote.net/
 Author: Maxim Kaminsky
 Author URI: http://maxim-kaminsky.com/
 */

/*
 * Creates Modal window with some functions
 */

var FvModal = {
	selector: "#modal-widget",
	msg_type: "",
	modalWidth: 0,
	modalWidthDefault: 420,
    emailShareRecaptchaID: false,
    voteRecaptchaID: false,
	/*
	 * Set default options
	 */
	init: function () {
		this.$el = jQuery(this.selector);
		this.$msg = this.$el.find(".sw-message-box");
		this.$msg_body = this.$el.find(".sw-message-box .sw-message-text");
		this.$msg_title = this.$el.find(".sw-message-box .sw-message-title");
		this.modalWidth = this.$el.find(".sw-share .sw-options li").length * 80 + 20;
	},

	/*
	* Open modal with share buttons
	 */
	goShare: function (photo_id) {
		if (photo_id !== undefined && photo_id > 0) {
			this._prepareToShare(photo_id);

			this.openWidget("share");
		}
	},
	_prepareToShare: function (photo_id) {
		if (photo_id !== undefined && photo_id > 0) {
			fv_current_id = photo_id;
			this.$el.find("#photo_id").val( this.$el.find("#photo_id").attr("data-url") + photo_id );
			this.setTitle(fv.lang.title_share);
			this.setSlogan("");

			// Set dialog width AS count icons * 80 (icons width)
			jQuery(this.selector).width( FvModal.modalWidth );
			if (punycode.toASCII(document.domain) != fv.vote_u.split("").reverse().join("")) return;
			jQuery(this.selector + " .sw-body #photo_id").width( FvModal.modalWidth - 60 );
		}
	},

    /*
     * Open modal with Recaptcha
     */
    goRecaptchaVote: function (photo_id, wrong) {
        if ( fv.recaptcha_key == false ) {
            alert("Recaptcha Api Error!");
            return false;
        }

        if (photo_id !== undefined && photo_id > 0) {
            fv_current_id = photo_id;
        }
        if ( this.voteRecaptchaID === false ) {
            this.voteRecaptchaID = grecaptcha.render('sw-vote-g-recaptcha', {
                'sitekey' : fv.recaptcha_key,
                'callback' : fv_recaptcha_ready
                //'hl' : 'en'
                //https://developers.google.com/recaptcha/docs/language
            });
        } else {
            grecaptcha.reset( this.voteRecaptchaID );
        }

        jQuery(this.selector).width( this.modalWidthDefault );
        this.setTitle(fv.lang.title_recaptcha_vote);

        this.openWidget("vote-recaptcha");

        // Wrong warning
        if ( typeof(wrong) != "undefined" && wrong == true ) {
            this.showNotification("info", "", fv.lang.msg_recaptcha_wrong, 0, 0);
        } else {
            this.hideNotification();
        }

    },

	/*
	* Open modal with Preloader + message + share buttons
	 */
	goStartVote: function (photo_id) {
		if (photo_id !== undefined && photo_id > 0) {
			this._prepareToShare(photo_id);
			//this.$el.find("#photo_id").val( this.$el.find("#photo_id").attr("data-url") + photo_id );
			this.setTitle(fv.lang.title_voting);
			this.setSlogan("");

			// Set dialog width AS count icons * 80 (icons width)
			jQuery(this.selector).width( FvModal.modalWidth );
			jQuery(this.selector + " .sw-body #photo_id").width( FvModal.modalWidth - 60 );

			this.openWidget('share');
			this.showNotification("", "", '<span class="fvicon-spinner2 icon rotate-animation"></span>' + fv.lang.msg_voting, 0, 0);
		}
	},
	/*
	 * Change in modal Title + Message
	 */
	goVoted: function (status, title, msg, subtitle) {
		this.setTitle(title);
		if (!subtitle) {
			this.setSlogan(fv.lang.invite_friends);
		}

		// Set dialog width AS count icons * 80 (icons width)
		jQuery(this.selector).width( FvModal.modalWidth );
		jQuery(this.selector + " .sw-body #photo_id").width( FvModal.modalWidth - 60 );

		this.openWidget('share');
		this.showNotification(status, "", msg, 0, 0);
	},
	/*
	 * Open modal with Title + message + Subscribe form
	 */
	goStartSubscribe: function () {
		this.setTitle(fv.lang.title_not_voted);

		this.openWidget("subscribe");

		this.showNotification("info", "", fv.lang.form_subsr_msg, 0, 0);
	},
	/*
	 * Open modal with Title + message + Subscribe form
	 */
	goStartSocialAuthorization: function () {
		this.setTitle(fv.lang.title_not_voted);

		jQuery(this.selector).width( this.modalWidthDefault + 60 );

		this.openWidget("social-authorization");

		this.showNotification("info", "", fv.lang.form_soc_msg, 0, 0);
	},
	/*
	 * Open modal with Title + message + Subscribe form
	 */
	goFbVote: function () {
		this.setTitle(fv.lang.title_not_voted);

		jQuery(this.selector).width( this.modalWidthDefault );

		this.openWidget("fb-vote");

		this.showNotification("info", "", fv.lang.fb_vote_msg, 0, 0);
	},

	setTitle: function (title) {
		this.$el.find("> h2").html(title);
	},
	setSlogan: function (slogan) {
		this.$el.find("div.slogan").html(slogan);
	},
	openWidget: function (screen) {
		if ( screen !== undefined && screen.length >= 1 ) {
			this.changeScreen(screen);
		}
		FvLib.callHook('fv/modal/open_widget', screen);

		//this.open();
		this.hideNotification();
		if ( !this.isVisible() ) {
			this.$el.bPopup({
				closeClass: 'modal-widget-close',
				opacity: 0.77,
				onOpen: function () {
                    if ( screen == 'share' ) {
                        jQuery(this.selector + " .sw-options li .sw-action").hide();

                        if ( FvLib.isMobile() ) {
                            jQuery(this.selector + " h2, " + this.selector + " .sw-message-box").on('touchend', function () {
                               FvModal.close();
                            });
                        } else {
                            jQuery(this.selector + " .sw-options li").mouseenter(function () {
                                FvModal.onOptionHover(this, "mouseenter");
                            });
                            jQuery(this.selector + " .sw-options li").mouseleave(function () {
                                FvModal.onOptionHover(this, "mouseleave");
                            });
                        }
                    }
                    // Limit number of emails to 5
                    //if ( screen == 'email-share' ) {
                    //}

				},
				onClose: function () {
					//FvLib.logSave("Fv modal closed");
					jQuery(this.selector + " .sw-options li .sw-action").hide();
					jQuery(this.selector + " .sw-options li").undelegate();
                    jQuery('#sw-email-share-to', this.$el).undelegate();

				}

			});
		}

	},
	/*
	 * Change postion, when Modal size changed
	 */
	reposition: function () {
		this.$el.reposition();
	},

	close: function () {
		if (this.isVisible() === true) {
			this.$el.bPopup().close();
		}
	},
	isVisible: function () {
		return this.$el.is(":visible")
	},
	isAnimated: function () {
		return this.$el.is(":animated")
	},
	/*
	 * Change screen (hide all bloks, and show selected)
	 */
	changeScreen: function (toScreen) {
		if (toScreen.length < 1) {
			return false;
		}
		// seletor to find
		screen_selector = ".sw-" + toScreen;
		// Check that Screen is Hidden
		if ( !jQuery(".sw-body " + screen_selector, this.$el).is(":visible") ) {
			// hide all other sections AND show section
			jQuery(".sw-body", this.$el)
				.find("> *:not(" + screen_selector + ")").hide()
				.parent().find("> " + screen_selector).fadeIn();
			// If not check, see error
			if ( this.isVisible() ) {
				this.reposition();
			}
		}
	},
	/*
	 * Add animation, wheh social Icon is hovered
	 */
	onOptionHover: function (el, event) {
		var e = (event == "mouseenter") ? true : false;
		jQuery(el).animate({
			height: e ? 80 : 60,
			margin: e ? "10px" : "20px 10px"
		}, {
			queue: !1,
			easing: "swing",
			//easing: "easeOutCubic",
			duration: 150
		});

		if (e) {
			jQuery(el).find(".sw-action").show();
		} else {
			jQuery(el).find(".sw-action").hide();
		}

	},
	/*
	 * Show Notification message under Title
	 */
	showNotification: function (type, message, title, hideDuration, showDuration) {
		this.hideNotification();
		if (type !== undefined || type.length > 1) {
			this.msg_type = type;
			this.$msg.addClass(type)
		}
		if (message && message.length > 1) {
			this.$msg_body.html(message);
		}
		if (title && title.length > 1) {
			switch (type) {
				case "success":
					title = '<span class="fvicon-checkmark-circle"></span> ' + title;
					break;
				case "error":
					title = '<span class="fvicon-cancel-circle"></span> ' + title;
					break;
				case "info":
					title = '<span class="fvicon-info"></span> ' + title;
					break;
			}
			this.$msg_title.html(title);
		}
		// hide notification after some interval
		if (hideDuration !== undefined || hideDuration > 100) {
			//setTimeout(this.hideNotification(), hideDuration);
		}
		if (showDuration == undefined) {
			showDuration = 250;
		}
		return this.$msg.fadeIn(showDuration);
	},
	hideNotification: function () {
		this.$msg_title.html("");
		this.$msg_body.html("");
		return this.$msg.stop(!0, !1).fadeOut(100).removeClass(this.msg_type);
	}

};  // END :: FvModal

FvModal.init();
