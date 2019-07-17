(function( $ ) {
	$(function() {
		
		$( 'input.wts-clp-color-picker' ).wpColorPicker();
		
		/*
		var wts_clp_media_init = function(selector, button_selector)  {
			var wts_clp_clicked_button = false;
			
			$(selector).each(function (i, input) { 
				var button = $( input ).next( button_selector );
			
				button.click(function (event) { 
					event.preventDefault();
					var selected_img;
					wts_clp_clicked_button = jQuery(this);

					// check for media manager instance
					if(wp.media.frames.frame) {
						wp.media.frames.frame.open();
						return;
					}
					// configuration of the media manager new instance
					wp.media.frames.frame = wp.media({
						title: 'Select image',
						multiple: false,
						library: {
							type: 'image'
						},
						button: {
							text: 'Use selected image'
						}
					});

					// Function used for the image selection and media manager closing
					var wts_clp_media_set_image = function() {
						var selection = wp.media.frames.frame.state().get('selection');

						// no selection or no image selected
						if (!selection) {
							return;
						}

						// iterate through selected elements
						selection.each(function(attachment) {
							var url = attachment.attributes.url;
							wts_clp_clicked_button.prev(selector).val(url);
						});
					};

					wp.media.frames.frame.on('close', wts_clp_media_set_image);
					wp.media.frames.frame.on('select', wts_clp_media_set_image);
					wp.media.frames.frame.open();
				});
		   });
		};

		wts_clp_media_init( '.wts-clp-image-picker', '.wts-clp-image-picker-button' );
		*/	
	});
	
	
	
	var mediaUploader;

	$( '.wts-clp-image-picker-button' ).click(function(e) {
		e.preventDefault(); 
		var attchmnt_global = '';
		var selector = $( this ).data( 'for' );
		$( selector ).addClass( 'get_url' );
		
		// If the uploader object has already been created, reopen the dialog
		  if (mediaUploader) {
		  mediaUploader.open();
		  return;
		}
		// Extend the wp.media object
		mediaUploader = wp.media.frames.file_frame = wp.media({
		  title: 'Choose Image',
		  button: {
		  text: 'Choose Image'
		}, multiple: false });

		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function() {
			attachment = mediaUploader.state().get('selection').first().toJSON();
			
			$( "#wts_clp_loginpageSettings_form .get_url" ).each(function() {
				//add url
				$( this ).val( attachment.url );
				$( this ).removeClass( 'get_url' );
			});	
		   
		});
		/*
		//on media uploader close
		mediaUploader.on('close',function() {
			// remove class
			$( "#wts_clp_loginpageSettings_form .get_url" ).each(function() {
				$( this ).removeClass( 'get_url' );
			});
		});		
		*/
		
		// Open the uploader dialog
		mediaUploader.open();
  });
  
})( jQuery );