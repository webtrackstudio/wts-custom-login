(function( $ ) {	
	$( function() {
		$( '#wts_clp_login_IsCaptchaRequired' ).change( function () {
			if(!$('#wts_clp_login_IsCaptchaRequired').is(':checked'))
			{
				//check box is not checked
				$('#wts_clp_login_IsCaptchaRequired').val(0);
				$('#wts_clp_captcha_keys_container').removeClass( 'active' );	
			}
			else
			{
				$('#wts_clp_login_IsCaptchaRequired').val(1);
				$('#wts_clp_captcha_keys_container').addClass( 'active' );
			}
		 });

		//enable disable buttons for wordpress update control	
		$( '.en_dis_btns' ).off( 'click' ).on( 'click', function() {		

			var option_val = $( this ).data( 'option' );
			var input_id = $( this ).data( 'input' );
			var opponent = $( this ).data( 'opponent' );
			
			$( this ).addClass( 'btn_active' );
			$( opponent ).removeClass( 'btn_active' );
			$( input_id ).val( option_val );
		});
		
		//Provide function to copy url
		$( '#wtsSaveMyUrl' ).off( 'click' ).on( 'click', function() {		

			 /* Get the text field */
			  var copyText = document.getElementById("urlme");

			  /* Select the text field */
			  copyText.select();

			  /* Copy the text inside the text field */
			  document.execCommand("copy");

			  $('#wtsSaveMyUrl').attr('title',"Copied url: " + copyText.value);
		});
	});
	
	//To show theme image 
	$("input[name=wts_clp_login_selected_theme]").on("change", function()
	{
		
		var theme = $(this).val();
		//alert(theme);
		var imgHtml = '<img src="' + wts_clp.pluginDir + 'assets/themes/' + theme + '/images/theme_image.jpeg" ></img>';
		//console.log( imgHtml );
		$('.selected-theme-image-div').html( imgHtml );
	});	
	
})( jQuery );