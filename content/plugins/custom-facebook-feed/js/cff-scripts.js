var cff_js_exists = (typeof cff_js_exists !== 'undefined') ? true : false;
if(!cff_js_exists){

	jQuery(document).ready(function() {
		
		jQuery('#cff .cff-item').each(function(){
			var $self = jQuery(this);

			//Wpautop fix
			if( $self.find('.cff-viewpost-facebook').parent('p').length ){
				$self.find('.cff-viewpost-facebook').unwrap('p');
			}
			if( $self.find('.cff-author').parent('p').length ){
				$self.find('.cff-author').eq(1).unwrap('p');
				$self.find('.cff-author').eq(1).remove();
			}
			if( $self.find('#cff .cff-link').parent('p').length ){
				$self.find('#cff .cff-link').unwrap('p');
			}

			//Expand post
			var expanded = false,
				$post_text = $self.find('.cff-post-text .cff-text'),
				text_limit = $self.closest('#cff').attr('data-char');
			if (typeof text_limit === 'undefined' || text_limit == '') text_limit = 99999;
			
			//If the text is linked then use the text within the link
			if ( $post_text.find('a.cff-post-text-link').length ) $post_text = $self.find('.cff-post-text .cff-text a');
			var	full_text = $post_text.html();
			if(full_text == undefined) full_text = '';
			var short_text = full_text.substring(0,text_limit);
			
			//Cut the text based on limits set
			$post_text.html( short_text );
			//Show the 'See More' link if needed
			if (full_text.length > text_limit) $self.find('.cff-expand').show();
			//Click function
			$self.find('.cff-expand a').unbind('click').bind('click', function(e){
				e.preventDefault();
				var $expand = jQuery(this),
					$more = $expand.find('.cff-more'),
					$less = $expand.find('.cff-less');
				if (expanded == false){
					$post_text.html( full_text );
					expanded = true;
					$more.hide();
					$less.show();
				} else {
					$post_text.html( short_text );
					expanded = false;
					$more.show();
					$less.hide();
				}
				//Add target attr to post text links via JS so aren't included in char count
				$self.find('.cff-text a').add( $self.find('.cff-post-desc a') ).attr({
					'target' : '_blank',
					'rel' : 'nofollow'
				});
				cffLinkHashtags();
			});

			//Hide the shared link box if it's empty
			$sharedLink = $self.find('.cff-shared-link');
			if( $sharedLink.text() == '' ){
				$sharedLink.remove();
			}

			function cffLinkHashtags(){
				//Link hashtags
				var cffTextStr = $self.find('.cff-text').html(),
					cffDescStr = $self.find('.cff-post-desc').html(),
					regex = /(^|\s)#(\w*[a-z\u00E0-\u00FC一-龠ぁ-ゔァ-ヴー]+\w*)/gi,
					// regex = /#(\w*[a-z\u00E0-\u00FC一-龠ぁ-ゔァ-ヴー]+\w*)/gi,
					linkcolor = $self.find('.cff-text').attr('data-color');

				function replacer(hash){
					//Remove white space at beginning of hash
					var replacementString = jQuery.trim(hash);
					//If the hash is a hex code then don't replace it with a link as it's likely in the style attr, eg: "color: #ff0000"
					if ( /^#[0-9A-F]{6}$/i.test( replacementString ) ){
						return replacementString;
					} else {
						return ' <a href="https://www.facebook.com/hashtag/'+ replacementString.substring(1) +'" target="_blank" rel="nofollow" style="color:#' + linkcolor + '">' + replacementString + '</a>';
					}
				}

				if(cfflinkhashtags == 'true'){
					//Replace hashtags in text
					var $cffText = $self.find('.cff-text');
					if($cffText.length > 0){
						//Add a space after all <br> tags so that #hashtags immediately after them are also converted to hashtag links. Without the space they aren't captured by the regex.
						cffTextStr = cffTextStr.replace(/<br>/g, "<br> ");
						$cffText.html( cffTextStr.replace( regex , replacer ) );
					}
				}

				//Replace hashtags in desc
				if( $self.find('.cff-post-desc').length > 0 ) $self.find('.cff-post-desc').html( cffDescStr.replace( regex , replacer ) );
			}
			cffLinkHashtags();

			//Add target attr to post text links via JS so aren't included in char count
			$self.find('.cff-text a').add( $self.find('.cff-post-desc a') ).attr({
				'target' : '_blank',
				'rel' : 'nofollow'
			});


			//Share toolip function
	        $self.find('.cff-share-link').unbind().bind('click', function(){
	            $self.find('.cff-share-tooltip').toggle();
	        });

			
		}); //End .cff-item each
	});

} //End cff_js_exists check