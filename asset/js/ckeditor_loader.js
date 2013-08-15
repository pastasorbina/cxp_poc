var ckeditor_default_height = 300;
var ckeditor_default_width = 800;

$(document).ready(function(){

	/**
	 * integrate ckfinder to ckeditor
	 */
	CKFinder.setupCKEditor( null, { basePath : asset_url+'ckfinder/', rememberLastFolder : false } ) ;


	/**
	 * initialize ckeditor
	 */
	$( 'textarea.ckeditor' ).each( function(e) {

		// setup height and width
		var height = $(this).attr('height');
		var width = $(this).attr('width');
		if(!height) { height = ckeditor_default_height; }
		if(!width) { width = ckeditor_default_width; }

		$(this).ckeditor({
			height: height,
			width: width,
			toolbar: 'Editor'
		});
	});

	$( 'textarea.ckeditor_basic' ).each( function(e) {

		// setup height and width
		var height = $(this).attr('height');
		var width = $(this).attr('width');
		if(!height) { height = ckeditor_default_height; }
		if(!width) { width = ckeditor_default_width; }

		$(this).ckeditor({
			height: height,
			width: width,
			toolbar: 'Basic',
		});
	});



});
