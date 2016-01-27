//this file mostly contains the modified version of functions 
//taken from buddypress.js to add/delete activity and other activity related action

jQuery( document ).ready( function(){

/**
 * For Allowing User to post first comment to a media Item when the media item has no entry in the activity table
 * Thanks to @apeatling, this function is taken from bp-default/_inc/global.js but slightly modified to use prefix mpp-
 */

	/* Textarea focus */
	jq(document).on('focus', '#mpp-whats-new', function() {
		jq("#mpp-whats-new-options").animate({
			height:'50px'
		});
		jq("form#mpp-whats-new-form textarea").animate({
			height:'50px'
		});
		jq("#mpp-aw-whats-new-submit").prop("disabled", false);

		var $whats_new_form = jq("form#mpp-whats-new-form");
		if ( $whats_new_form.hasClass("submitted") ) {
			$whats_new_form.removeClass("submitted");	
		}
	});

	/* On blur, shrink if it's empty */
	jq(document).on('blur', '#mpp-whats-new',  function(){
		if (!this.value.match(/\S+/)) {
			this.value = "";
			jq("#mpp-whats-new-options").animate({
				height:'0'
			});
			jq("form#mpp-whats-new-form textarea").animate({
				height:'20px'
			});
			jq("#mpp-aw-whats-new-submit").prop("disabled", true);
		}
	});


	/* New posts Activity comment(not the replies to comment) on media/gallery */
	jq(document).on('click', 'input#mpp-aw-whats-new-submit', function() {
		var activity_list = '';
		var button = jq(this);
		var form = button.closest("form#mpp-whats-new-form");

		form.children().each( function() {
			if ( jq.nodeName(this, "textarea") || jq.nodeName(this, "input") )
				jq(this).prop( 'disabled', true );
		});

		/* Remove any errors */
		jq('div.error').remove();
		
		button.addClass('loading');
		button.prop('disabled', true);
		form.addClass("submitted");
		
		/* Default POST values */
		var object = '';
		var item_id = form.find("#mpp-whats-new-post-in").val();
		var content = form.find("textarea#mpp-whats-new").val();

		/* Set object for non-profile posts */
		if ( item_id > 0 ) {
			object = form.find("#mpp-whats-new-post-object").val();
		}

		var mpp_type = jq(form).find('#mpp-activity-type').val();
		var mpp_id = jq(form).find('#mpp-item-id').val();
		jq.post( ajaxurl, {
			action: 'mpp_add_comment',
			'cookie': mpp_get_cookies(),
			'_wpnonce_post_update': form.find("input#_wpnonce_post_update").val(),
			'content': content,
			'object': object,
			'item_id': item_id,
			'mpp-id': mpp_id,
			'mpp-type'	: mpp_type, //media or gallery
			'_bp_as_nonce': jq('#_bp_as_nonce').val() || ''
		},
		function(response) {

			form.children().each( function() {
				if ( jq.nodeName(this, "textarea") || jq.nodeName(this, "input") ) {
					jq(this).prop( 'disabled', false );
				}
			});
			button.prop('disabled', false);
			/* Check for errors and append if found. */
			if ( response[0] + response[1] == '-1' ) {
				form.prepend( response.substr( 2, response.length ) );
				jq( 'form#' + form.attr('id') + ' div.error').hide().fadeIn( 200 );
			} else {
				activity_list = jq(form.parents('.mpp-activity').get(0) );
				if ( 0 == (activity_list.find("ul.mpp-activity-list")).length ) {
					jq("div.error").slideUp(100).remove();
					jq("div#message").slideUp(100).remove();
					activity_list.append( '<ul id="mpp-activity-stream" class="mpp-activity-list item-list">' );
				}

				activity_list.find("ul#mpp-activity-stream").prepend(response);
				activity_list.find("ul#mpp-activity-stream li:first").addClass('new-update just-posted');

				
				form.find("textarea#mpp-whats-new").val('');
			}

			form.find("#mpp-whats-new-options").animate({
				height:'0px'
			});
			form.find("textarea").animate({
				height:'20px'
			});
			form.find("#mpp-whats-new-submit").prop("disabled", false).removeClass('loading');
		});

		return false;
	});
	
	/* Stream event delegation */
	jq(document).on('click', 'div.mpp-activity', function(event) {
		var target = jq(event.target),
			type, parent, parent_id,
			li, id, link_href, nonce, timestamp,
			oldest_page, just_posted;

		/* Favoriting activity stream items */
		if ( target.hasClass('fav') || target.hasClass('unfav') ) {
			type      = target.hasClass('fav') ? 'fav' : 'unfav';
			parent    = target.closest('.activity-item');
			parent_id = parent.attr('id').substr( 9, parent.attr('id').length );

			target.addClass('loading');

			jq.post( ajaxurl, {
				action: 'activity_mark_' + type,
				'cookie': mpp_get_cookies(),
				'id': parent_id
			},
			function(response) {
				target.removeClass('loading');

				target.fadeOut( 200, function() {
					jq(this).html(response);
					jq(this).attr('title', 'fav' === type ? _mppStrings.remove_fav : _mppStrings.mark_as_fav);
					jq(this).fadeIn(200);
				});

				if ( 'fav' === type ) {
					if ( !jq('.item-list-tabs #activity-favs-personal-li').length ) {
						if ( !jq('.item-list-tabs #activity-favorites').length ) {
							jq('.item-list-tabs ul #activity-mentions').before( '<li id="activity-favorites"><a href="#">' + _mppStrings.my_favs + ' <span>0</span></a></li>');
						}

						jq('.item-list-tabs ul #activity-favorites span').html( Number( jq('.item-list-tabs ul #activity-favorites span').html() ) + 1 );
					}

					target.removeClass('fav');
					target.addClass('unfav');

				} else {
					
				}

				
			});

			return false;
		}

		/* Delete activity stream items */
		if ( target.hasClass('delete-activity') ) {
			li        = target.parents('div.mpp-activity ul li');
			id        = li.attr('id').substr( 9, li.attr('id').length );
			link_href = target.attr('href');
			nonce     = link_href.split('_wpnonce=');
			timestamp = li.prop( 'class' ).match( /date-recorded-([0-9]+)/ );
			nonce     = nonce[1];

			target.addClass('loading');

			jq.post( ajaxurl, {
				action: 'delete_activity',
				'cookie': mpp_get_cookies(),
				'id': id,
				'_wpnonce': nonce
			},
			function(response) {

				if ( response[0] + response[1] === '-1' ) {
					li.prepend( response.substr( 2, response.length ) );
					li.children('#message').hide().fadeIn(300);
				} else {
					li.slideUp(300);

					// reset vars to get newest activities
					if ( timestamp && activity_last_recorded === timestamp[1] ) {
						newest_activities = '';
						activity_last_recorded  = 0;
					}
				}
			});

			return false;
		}

		// Spam activity stream items
		if ( target.hasClass( 'spam-activity' ) ) {
			li        = target.parents( 'div.mpp-activity ul li' );
			timestamp = li.prop( 'class' ).match( /date-recorded-([0-9]+)/ );
			target.addClass( 'loading' );

			jq.post( ajaxurl, {
				action: 'bp_spam_activity',
				'cookie': encodeURIComponent( document.cookie ),
				'id': li.attr( 'id' ).substr( 9, li.attr( 'id' ).length ),
				'_wpnonce': target.attr( 'href' ).split( '_wpnonce=' )[1]
			},

			function(response) {
				if ( response[0] + response[1] === '-1' ) {
					li.prepend( response.substr( 2, response.length ) );
					li.children( '#message' ).hide().fadeIn(300);
				} else {
					li.slideUp( 300 );
					// reset vars to get newest activities
					if ( timestamp && activity_last_recorded === timestamp[1] ) {
						newest_activities = '';
						activity_last_recorded  = 0;
					}
				}
			});

			return false;
		}

		/* Load more updates at the end of the page */
		if ( target.parent().hasClass('mpp-load-more') ) {
			if ( bp_ajax_request ) {
				bp_ajax_request.abort();
			}

			target.parent().find('.mpp-load-more').addClass('loading');

			if ( null === jq.cookie('bp-activity-oldestpage') ) {
				jq.cookie('bp-activity-oldestpage', 1, {
					path: '/'
				} );
			}

			oldest_page = ( jq.cookie('bp-activity-oldestpage') * 1 ) + 1;
			just_posted = [];

			jq('.mpp-activity-list li.just-posted').each( function(){
				just_posted.push( jq(this).attr('id').replace( 'mpp-activity-','' ) );
			});

			load_more_args = {
				action: 'activity_get_older_updates',
				'cookie': mpp_get_cookies(),
				'page': oldest_page,
				'exclude_just_posted': just_posted.join(',')
			};

			load_more_search = mpp_get_querystring('s');

			if ( load_more_search ) {
				load_more_args.search_terms = load_more_search;
			}

			bp_ajax_request = jq.post( ajaxurl, load_more_args,
			function(response)
			{
				target.parent().find('.mpp-load-more').removeClass('loading');
				jq.cookie( 'bp-activity-oldestpage', oldest_page, {
					path: '/'
				} );
				jq('ul.mpp-activity-list').append(response.contents);

				target.parent().hide();
			}, 'json' );

			return false;
		}

		/* Load newest updates at the top of the list */
		if ( target.parent().hasClass('load-newest') ) {

			event.preventDefault();

			target.parent().hide();

			/**
			 * If a plugin is updating the recorded_date of an activity
			 * it will be loaded as a new one. We need to look in the
			 * stream and eventually remove similar ids to avoid "double".
			 */
			activity_html = jq.parseHTML( newest_activities );

			jq.each( activity_html, function( i, el ){
				if( 'LI' === el.nodeName && jq(el).hasClass( 'just-posted' ) ) {
					if( jq( '#' + jq(el).attr( 'id' ) ).length ) {
						jq( '#' + jq(el).attr( 'id' ) ).remove();
					}
				}
			} );

			// Now the stream is cleaned, prepend newest
			jq( 'ul.mpp-activity-list' ).prepend( newest_activities );

			// reset the newest activities now they're displayed
			newest_activities = '';
		}
	});

	// Activity "Read More" links inside the gallery/media activity
	jq(document).on('click', 'div.mpp-activity .activity-read-more a', function(event) {
		
		var target = jq(event.target),
			link_id = target.parent().attr('id').split('-'),
			a_id    = link_id[4],
			type    = link_id[1], /* activity or acomment */
			inner_class, a_inner;

		inner_class = type === 'acomment' ? 'mpp-acomment-content' : 'mpp-activity-inner';
		a_inner = jq('#' + type + '-' + a_id + ' .' + inner_class + ':first' );
		jq(target).addClass('loading');

		jq.post( ajaxurl, {
			action: 'get_single_activity_content',//should we override it too?
			'activity_id': a_id
		},
		function(response) {
			jq(a_inner).slideUp(300).html(response).slideDown(300);
		});

		return false;
	});

	/**** Activity Comments *******************************************************/

	/* Hide all activity comment forms */
	jq('form.mpp-ac-form').hide();
	
	/* Activity list event delegation */
	jq(document).on( 'click', '.mpp-activity', function(event) {
		
	var target = jq(event.target),
			id, ids, a_id, c_id, form,
			form_parent, form_id,
			tmp_id, comment_id, comment,content,
			ajaxdata,
			ak_nonce,
			show_all_a, new_count,
			link_href, comment_li, nonce;

		/* Comment / comment reply links */
		if ( target.hasClass('mpp-acomment-reply') || target.parent().hasClass('mpp-acomment-reply') ) {
			if ( target.parent().hasClass('mpp-acomment-reply') ) {
				target = target.parent();
			}
			
			var id = target.attr('id');
			ids = id.split('-');

			var a_id = ids[3]
			var c_id = target.attr('href').substr( 10, target.attr('href').length );
			
			var form = jq( '#mpp-ac-form-' + a_id );

			form.css( 'display', 'none' );
			form.removeClass('root');
			jq('.mpp-ac-form').hide();

			/* Hide any error messages */
			form.children('div').each( function() {
				if ( jq(this).hasClass( 'error' ) )
					jq(this).hide();
			});

			if ( ids[2] !== 'comment' ) {
				jq('#mpp-acomment-' + c_id).append( form );
			} else {
				jq('#mpp-activity-' + a_id + ' .mpp-activity-comments').append( form );
			}

			if ( form.parent().hasClass( 'mpp-activity-comments' ) ) {
				form.addClass('root');
			}

			form.slideDown( 200 );
			jq.scrollTo( form, 500, {
				offset:-100,
				easing:'swing'
			} );
			jq('#mpp-ac-form-' + ids[3] + ' textarea').focus();

			return false;
		}

		/* Activity comment posting */
		if ( target.attr('name') == 'mpp_ac_form_submit' ) {
			var form        = target.parents( 'form' );
			var form_parent = form.parent();
			var form_id     = form.attr('id').split('-');

			if ( !form_parent.hasClass('mpp-activity-comments') ) {
				var tmp_id = form_parent.attr('id').split('-');
				var comment_id = tmp_id[2];
			} else {
				var comment_id = form_id[3];
			}
			content = jq( '#' + form.attr('id') + ' textarea' );
			
			/* Hide any error messages */
			jq( '#' + form.attr('id') + ' div.error').hide();
			target.addClass('loading').prop('disabled', true);
			content.addClass('loading').prop('disabled', true);

			var ajaxdata = {
				action: 'mpp_add_reply',
				'cookie': mpp_get_cookies(),
				'_wpnonce_new_activity_comment': jq("input#_wpnonce_new_activity_comment").val(),
				'comment_id': comment_id,
				'form_id': form_id[3],
				'content': content.val()
			};

			// Akismet
			ak_nonce = jq('#_bp_as_nonce_' + comment_id).val();
			if ( ak_nonce ) {
				ajaxdata['_bp_as_nonce_' + comment_id] = ak_nonce;
			}

			jq.post( ajaxurl, ajaxdata, function(response) {
				target.removeClass('loading');
				content.removeClass('loading');
				/* Check for errors and append if found. */
				if ( response[0] + response[1] == '-1' ) {
					form.append( jq( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );
				} else {
					var activity_comments = form.parent();
					form.fadeOut( 200, function() {
						if ( 0 == activity_comments.children('ul').length ) {
							if ( activity_comments.hasClass('mpp-activity-comments') ) {
								activity_comments.prepend('<ul></ul>');
							} else {
								activity_comments.append('<ul></ul>');
							}
						}

						/* Preceeding whitespace breaks output with jQuery 1.9.0 */
						var the_comment = jq.trim( response );

						activity_comments.children('ul').append( jq( the_comment ).hide().fadeIn( 200 ) );
						form.children('textarea').val('');
						activity_comments.parent().addClass('has-comments');
					} );

					jq( '#' + form.attr('id') + ' textarea').val('');

					/* Increase the "Reply (X)" button count */
					jq('#mpp-activity-' + form_id[3] + ' a.mpp-acomment-reply span').html( Number( jq('#mpp-activity-' + form_id[3] + ' a.mpp-acomment-reply span').html() ) + 1 );

					// Increment the 'Show all x comments' string, if present
					var show_all_a = activity_comments.find('.show-all').find('a');
					if ( show_all_a ) {
						new_count = jq('li#mpp-activity-' + form_id[3] + ' a.mpp-acomment-reply span').html();
						show_all_a.html( _mppStrings.show_x_comments.replace( '%d', new_count ) );
					}
				}
				jq(target).prop('disabled', false);
				jq(content).prop('disabled', false);
			});

			return false;
		}

		/* Deleting an activity comment */
		if ( target.hasClass('mpp-acomment-delete') ) {
			var link_href = target.attr('href');
			var comment_li = target.parent().parent();
			var form = comment_li.parents('div.mpp-activity-comments').children('form');

			var nonce = link_href.split('_wpnonce=');
			nonce = nonce[1];

			var comment_id = link_href.split('cid=');
			comment_id = comment_id[1].split('&');
			comment_id = comment_id[0];

			target.addClass('loading');

			/* Remove any error messages */
			jq('.mpp-activity-comments ul .error').remove();

			/* Reset the form position */
			comment_li.parents('.mpp-activity-comments').append(form);

			jq.post( ajaxurl, {
				action: 'delete_activity_comment',
				'cookie': mpp_get_cookies(),
				'_wpnonce': nonce,
				'id': comment_id
			},
			function(response) {
				/* Check for errors and append if found. */
				if ( response[0] + response[1] === '-1' ) {
					comment_li.prepend( jq( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );
				} else {
					var children  = jq( '#' + comment_li.attr('id') + ' ul' ).children('li'),
						child_count = 0,
						count_span, new_count, show_all_a;

					jq(children).each( function() {
						if ( !jq(this).is(':hidden') ) {
							child_count++;
						}
					});
					comment_li.fadeOut(200, function() {
						comment_li.remove();
					});

					/* Decrease the "Reply (X)" button count */
					count_span = jq('#' + comment_li.parents('#mpp-activity-stream > li').attr('id') + ' a.mpp-acomment-reply span');
					new_count = count_span.html() - ( 1 + child_count );
					count_span.html(new_count);

					// Change the 'Show all x comments' text
					show_all_a = comment_li.siblings('.show-all').find('a');
					if ( show_all_a ) {
						show_all_a.html( _mppStrings.show_x_comments.replace( '%d', new_count ) );
					}

					/* If that was the last comment for the item, remove the has-comments class to clean up the styling */
					if ( 0 === new_count ) {
						jq(comment_li.parents('#mpp-activity-stream > li')).removeClass('has-comments');
					}
				}
			});


			return false;
		}

				// Spam an activity stream comment
		if ( target.hasClass( 'spam-activity-comment' ) ) {
			link_href  = target.attr( 'href' );
			comment_li = target.parent().parent();

			target.addClass('loading');

			// Remove any error messages
			jq( '.mpp-activity-comments ul div.error' ).remove();

			// Reset the form position
			comment_li.parents( '.mpp-activity-comments' ).append( comment_li.parents( '.mpp-activity-comments' ).children( 'form' ) );

			jq.post( ajaxurl, {
				action: 'bp_spam_activity_comment',
				'cookie': encodeURIComponent( document.cookie ),
				'_wpnonce': link_href.split( '_wpnonce=' )[1],
				'id': link_href.split( 'cid=' )[1].split( '&' )[0]
			},

			function ( response ) {
				// Check for errors and append if found.
				if ( response[0] + response[1] === '-1' ) {
					comment_li.prepend( jq( response.substr( 2, response.length ) ).hide().fadeIn( 200 ) );

				} else {
					var children  = jq( '#' + comment_li.attr( 'id' ) + ' ul' ).children( 'li' ),
						child_count = 0,
						parent_li;

					jq(children).each( function() {
						if ( !jq( this ).is( ':hidden' ) ) {
							child_count++;
						}
					});
					comment_li.fadeOut( 200 );

					// Decrease the "Reply (X)" button count
					parent_li = comment_li.parents( '#mpp-activity-stream > li' );
					jq( '#' + parent_li.attr( 'id' ) + ' a.mpp-acomment-reply span' ).html( jq( '#' + parent_li.attr( 'id' ) + ' a.mpp-acomment-reply span' ).html() - ( 1 + child_count ) );
				}
			});

			return false;
		}

		/* Showing hidden comments - pause for half a second */
		if ( target.parent().hasClass('show-all') ) {
			target.parent().addClass('loading');

			setTimeout( function() {
				target.parent().parent().children('li').fadeIn(200, function() {
					target.parent().remove();
				});
			}, 600 );

			return false;
		}

		// Canceling an activity comment
		if ( target.hasClass( 'mpp-ac-reply-cancel' ) ) {
			jq(target).closest('.mpp-ac-form').slideUp( 200 );
			return false;
		}
	});
/* Escape Key Press for cancelling comment forms */
	jq(document).keydown( function(e) {
		e = e || window.event;
		if (e.target) {
			element = e.target;
		} else if (e.srcElement) {
			element = e.srcElement;
		}

		if( element.nodeType === 3) {
			element = element.parentNode;
		}

		if( e.ctrlKey === true || e.altKey === true || e.metaKey === true ) {
			return;
		}

		var keyCode = (e.keyCode) ? e.keyCode : e.which;

		if ( keyCode === 27 ) {
			if (element.tagName === 'TEXTAREA') {
				if ( jq(element).hasClass('mpp-ac-input') ) {
					jq(element).parent().parent().parent().slideUp( 200 );
					return false;
				}
			}
		}
	});

})	;
//a replacement of bp_get_querystring for themes that does not have it
function mpp_get_querystring( n ) {
	var half = location.search.split( n + '=' )[1];
	return half ? decodeURIComponent( half.split('&')[0] ) : null;
}

//copy of bp_get_cookies since some themes might not include that
/* Returns a querystring of BP cookies (cookies beginning with 'bp-') */
function mpp_get_cookies() {
	// get all cookies and split into an array
	var allCookies   = document.cookie.split(";");

	var bpCookies    = {};
	var cookiePrefix = 'bp-';

	// loop through cookies
	for (var i = 0; i < allCookies.length; i++) {
		var cookie    = allCookies[i];
		var delimiter = cookie.indexOf("=");
		var name      = jq.trim( unescape( cookie.slice(0, delimiter) ) );
		var value     = unescape( cookie.slice(delimiter + 1) );

		// if BP cookie, store it
		if ( name.indexOf(cookiePrefix) == 0 ) {
			bpCookies[name] = value;
		}
	}

	// returns BP cookies as querystring
	return encodeURIComponent( jq.param(bpCookies) );
}
