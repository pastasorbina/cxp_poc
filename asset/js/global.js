
String.prototype.sformat = function() {
	var s = this,
		i = arguments.length;

	while (i--) {
		s = s.replace(new RegExp('\\{' + i + '\\}', 'gm'), arguments[i]);
	}
	return s;
};

$(document).ready(function() {

	/**
	 * for bootstrap dropdown that has .hover class, automatically open and close dropdown on mouseenter/mouseout
	 */
	$('.dropdown.hover').each(function() {
		return false;
		var dropdown = $(this);
		$(dropdown).mouseenter(function(){
			$('.dropdown').removeClass('open');
			$(dropdown).addClass('open');
			$(dropdown).mouseleave(function(){ $(dropdown).removeClass('open'); })
		});
	});

	/* confirm before action */
	$(".areyousure").live('click', function(e) {
		e.preventDefault();
		var answer = confirm("are you sure ?");
		if (answer){
			window.location = $(this).attr('href');
		}
	});

	$(".sbmenu ul").each(function() {
		if($(this).parent().attr('class') != 'active' ){
			$(this).hide();
		}
	});
	$(".sbmenu a.expander").each(function() {
		$(this).click(function(e) {
			e.preventDefault();
			$(this).siblings("ul").toggle(100);
		});
	});

	$('.close_dialog').live('click', function(e) {
		e.preventDefault();
		$(this).closest('.ui-dialog-content').dialog('close');
	})


	// Datepicker
	$(".datepicker").live('focus', function(){
		$(this).datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
	//$(".datepicker").datepicker({
	//	dateFormat: 'yy-mm-dd'
	//});

	$(".datetimepicker").live('focus', function(){
		$(this).datetimepicker({
			dateFormat: 'yy-mm-dd'
		});
	});

	// Datepicker fix
	$('#ui-datepicker-div').css('z-index' , '2007');

	// Time Picker
	//$(".clockpicker").clockpick({
	//	starthour: 6,
	//	endhour: 20,
	//	minutedivisions: 12,
	//	military: true
	//});

	//roll to top
	$("a.backtotop").live('click', function(e) {
		e.preventDefault();
		$('html').animate( { scrollTop: 0 }, 'slow' );
	});

	//image alternator
	$("img.alternator").hover(function() {
		//handler in
		var alternate = $(this).attr('data-alternate');
		var old_img = $(this).attr('src');
		$(this).css('opacity','0');
		$(this).attr('data-alternate', old_img);
		$(this).animate({ opacity:1 }, 'slow');
		$(this).attr('src', alternate);
	}, function(){
		//handler out
		var alternate = $(this).attr('data-alternate');
		var old_img = $(this).attr('src');
		$(this).css('opacity','0');
		$(this).attr('data-alternate', old_img);
		$(this).animate({ opacity:1 }, 'fast');
		$(this).attr('src', alternate);
	});

	//toggle entries
	$('.do_toggle_entry').live('click', function(e) {
		var id = $(this).attr('data-id');
		$('.toggle_group').children('.toggle_content').hide();
		$('#content_'+id).show();
	});
	$('.do_toggle_showall').live('click', function(e) {
		$('.toggle_group').children('.toggle_content').show();
	});
	$('.do_toggle_hideall').live('click', function(e) {
		$('.toggle_group').children('.toggle_content').hide();
	});
	//trigger first toggle entries to show
	$('.toggle_group .do_toggle_entry').first().trigger('click');
});


function toggle_navbar() {
	$('.navbar-inner').toggle();
	var navbar_status = $('.navbar-inner').css('display');
	if(navbar_status == 'none') {
		$('.body_container').addClass('no_navbar');
		$('.toggle_headbar i').addClass('icon-chevron-down');
		$('.toggle_headbar i').removeClass('icon-chevron-up');
	} else {
		$('.body_container').removeClass('no_navbar');
		$('.toggle_headbar i').addClass('icon-chevron-up');
		$('.toggle_headbar i').removeClass('icon-chevron-down');
	}
}


