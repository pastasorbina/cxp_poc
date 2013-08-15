
    function init_stack()

	/////////////////////////



	function push_url_state(url) {
	   history.pushState({}, "page", url);
	}

	function add_card(index) {
		//search for the last card by index
		var card_name = '';
		var numofcard =  $('.card').size();
		var index = -1;
		if(numofcard >= 1) {
			index = $('.box').children('div:last').attr('data-index');
		}
		//and then append new card
		var new_index = parseInt(index)+1;
		$('.box').append('<div class="card" data-index="'+new_index+'" data-href="" data-name="'+card_name+'" data-status="" ></div>');
		return(new_index);
	}

	function get_curr_index(){
		//get last card index
		var numofcard =  $('.card').size();
		var index = -1;
		if(numofcard >= 1) {
			index = $('.box').children('div:last').attr('data-index');
		}
		return index;
	}

	function get_prev_index(index){
		var prev_index = index-1;
		return prev_index;
	}

	function slide_card_prev(reload_after_slide){
		var curr_index = get_curr_index();
		var curr_card = $('.card[data-index=' + curr_index + ']');
		prev_index = get_prev_index(curr_index);

		if(prev_index >= 0) {
			var prev_card = $('.card[data-index=' + prev_index + ']');
			if(reload_after_slide == true) {
			   reload_card(prev_card)
			}
			$(prev_card).slideDown('fast');
			$(curr_card).fadeOut('fast', function(){
				$(curr_card).remove();
				debug_card();
			});
			return prev_card;
		}
	}

	function reload_card(card , callback){
		var href = $(card).attr('data-href');
		start_loading()
		$.post(href , {ajax:true} , function(data) {
			$(card).html(data);
			if (callback != null) callback();
		} , 'html').complete(function() {
			finish_loading()
		});
	}

	function animate_updated_row(row_obj){
		var initial_bg = $(row_obj).children('td').css('background-color');
		$(row_obj).children('td').animate({ 'background-color':'#B9F29D' }, 'slow').delay(2000).animate({ 'background-color':initial_bg }, 'slow');
	}


	function debug_card__bak(){
		var debug_card = $('.debug_card');
		$(debug_card).html('');
		var head = '<div class="btn-toolbar"><div class="btn-group">';
		var tail = '</div></div>';
		var string = '';
		var card = $('.card');
		var numofcard = card.length;
		$(card).each(function(e){
			var add_class = '';
			if(e == (numofcard-1)){ add_class = 'btn-info'; }
			var name_string = $(this).find('#page_name').attr('data-value');
			string = string + '<span class="btn '+add_class+' ">'+name_string+'</span>';
		});
		$(debug_card).html(head+string+tail);
	}

	function debug_card($type){
		var type = 'breadcrumb';
		var debug_card = $('.debug_card');
		$(debug_card).html('');
		var head = '<div class="cardcrumb_wrap"><ul class="breadcrumb cardcrumb">';
		var tail = '</ul></div>';
		var string = '';
		var card = $('.card');
		var numofcard = card.length;
		$(card).each(function(e){
			var add_class = '';
			if(e == (numofcard-1)){ add_class = 'active'; }
			var name_string = $(this).find('#page_name').attr('data-value');
			string = string + '<li class="'+add_class+'">'+name_string+'</li><li class="divider"> / </li>';
		});
		$(debug_card).html(head+string+tail);
	}

	function get_card(card){
		if(!card){
			//if doesn't supply card object, return current card (last card)
			card = $('.box').children('.card:last');
		}
		var card_data = new Array();
		card_data['obj'] = card;
		card_data['index'] = $(card).attr('data-index');
		card_data['href'] = $(card).attr('data-href');
		card_data['name'] = $(card).attr('data-name');
		card_data['status'] = $(card).attr('data-status');
		return card_data;
	}

	function draw_sorter_arrow() {
	  var paging_form_obj = $(get_card().obj).find('#ajax_filter_form');

	  var prev_field = $(paging_form_obj).find('#orderby').val();
	  var prev_dir = $(paging_form_obj).find('#ascdesc').val();

	  var t = $(get_card().obj).find('a[data-field=' + prev_field + ']');

	  // Change the arrow
	  if (prev_dir == 'asc') t.append('<span class="sorterarrow"> <i class="icon-chevron-up"></i></span>');
	  else t.append('<span class="sorterarrow"> <i class="icon-chevron-down"></i></span>');
	}

	//INITIAL LOADING
	$(document).one('ready', function() {
		//initially add box div with index starting at 0
		var initial_href = mod_url + 'view_list';
		$.post(initial_href, {ajax:true} , function(data) {
				start_loading();
				var new_index = add_card();
				var new_card = $('.card[data-index=' + new_index + ']');
				$(new_card).html(data);
				$(new_card).attr('data-href', initial_href);
		} , 'html').complete(function() {
			//push_url_state(initial_href);
			finish_loading();
			debug_card();
		});
	});


	$(document).ready(function() {

		$('.card_next').live('click',function(e){
			e.preventDefault();
			start_loading();
			var href = $(this).attr('href');
			var old_index = get_curr_index();
			var new_index = add_card(old_index);

			var old_card = $('.card[data-index=' + old_index + ']');
			var new_card = $('.card[data-index=' + new_index + ']');

			//param GET modifier
			var param_get = $(this).attr('data-param-get');
			if(param_get != undefined && param_get != ''){ href = href+param_get; }

			$.post(href , {ajax:true} , function(data) {
				$(new_card).html(data);
			} , 'html').complete(function() {
					finish_loading();
					old_card.slideUp('fast');
					$(new_card).attr('data-href', href);
					//push_url_state(href);
					debug_card();
				});
		});

		$('.card_reload').live('click',function(e){
			e.preventDefault();
			start_loading();
			var card = get_card();
			var href = $(this).attr('href');
			$(card.obj).attr('data-href', href);
			reload_card(card.obj , function() {
			   debug_card();
			});
		});
		
		$('.card_prev').live('click',function(e){
			e.preventDefault();
			
			if ($(this).attr('data-reload' , 'true')) slide_card_prev(true);
			else slide_card_prev();
		});
		
		// AJAX form onSubmit
		$('#form_add_edit').live('submit', function(e) {
			e.preventDefault();
			start_loading();
			$(this).ajaxSubmit({
				success: function(data) {
					if (data.status == 'ok') {
						//start_loading();
						var card = slide_card_prev();
						debug_card();
						//refresh card
						var href = $(card).attr('data-href');
						$.post(href , {ajax:true} , function(data) {
							$(card).html(data);
						} , 'html').complete(function() {
							//on complete, animate updated row
							var row = $(card).find('tr[data-rowid=' + data.id + ']');
							animate_updated_row(row);
						});
						//finish_loading();
					} else {
						alert(data.msg);
					}
					finish_loading();
				},
				dataType: 'json'
			});
		});

		// AJAX LIST SELECT onCLick
		$(".ajax_list_select").live('click', function(e){
			e.preventDefault();
			var href = $(this).attr('href');
			var old_index = get_curr_index();
			var new_index = add_card(old_index);

			var old_card = $('.card[data-index=' + old_index + ']');
			var new_card = $('.card[data-index=' + new_index + ']');

			var inputid = $(this).attr('data-inputid');
			var labelid = $(this).attr('data-labelid');
			var param = $(this).attr('data-param');

			//param GET modifier
			var param_get = $(this).attr('data-param-get');
			if(param_get != '' && param_get != undefined){ href = href+param_get; }

			$.post(href , {ajax:true, inputid:inputid , labelid:labelid, param:param} , function(data) {
				start_loading();
				$(new_card).html(data);
			} , 'html').complete(function() {
					old_card.slideUp('fast');
					$(new_card).attr('data-href', href);
					//put the parameters in card object's attribute
					if(param != undefined && param != '' ){ $(new_card).attr('data-param' , param); }
					if(inputid != undefined && inputid != '' ){ $(new_card).attr('data-inputid' , inputid); }
					if(labelid != undefined && labelid != '' ){ $(new_card).attr('data-labelid' , labelid); }
					debug_card();
					finish_loading();
				});
		});

		// AJAX do selection
		$(".ajax_do_select").live('click', function(e){
			e.preventDefault();
			var id = $(this).attr('data-id');
			var label = $(this).attr('data-label');
			var card = get_card();
			//if inputid is set to card object, use it, if not, use the default set in link
			var inputid = $(card.obj).attr('data-inputid');
			if(inputid == undefined) { var inputid = $(this).attr('data-inputid'); }
			//if labelid is set to card object, use it, if not, use the default set in link
			var labelid = $(card.obj).attr('data-labelid');
			if(labelid == undefined) { var labelid = $(this).attr('data-labelid'); }

			var prev_card = slide_card_prev();
			var target_input = $(prev_card).find('#'+inputid);
			if(!labelid){
				var target_label = $(prev_card).find('#'+inputid+'_label');
			} else {
				var target_label = $(prev_card).find('#'+labelid);
			}

			$(target_input).val(id);
			$(target_input).text(id);
			$(target_input).trigger('change');
			$(target_label).val(label);
			$(target_label).text(label);
			$(target_label).trigger('change');

			var input_offset = $(target_input).offset();
			var label_offset = $(target_label).offset();
			if(label_offset.top == 0){ var offset = input_offset; } else { var offset = label_offset; }
			$('body').animate( { scrollTop: offset.top - 100 }, 'fast' );
		});


		// AJAX paging
		$(".ajax_prev_page, .ajax_next_page").live('click', function(e){
			e.preventDefault();
			var paging_form_obj = $(this).closest('.card').find('#ajax_filter_form');
			//console.log(paging_form_obj);
			var pagenum = $(this).attr('data-pagenum');
			$(paging_form_obj).find('#pagenum').val(pagenum);
			$(paging_form_obj).trigger('submit');
		});

		// AJAX paging form
		$('#ajax_filter_form').live('submit', function(e) {
			e.preventDefault();
			start_loading();
			$(this).ajaxSubmit({
				success: function(data) {
					if (data.status == 'ok') {
						var card = get_card();
						$(card.obj).attr('data-href', data.href);
						reload_card(card.obj , draw_sorter_arrow);
						debug_card();
					} else {
						alert(data.msg);
					}
					finish_loading();
				},
				dataType: 'json'
			});
		});

		//AJAX do_selected
		$('.do_selected').live('click', function(e) {
			e.preventDefault();
			var action = $(this).attr('data-action');
			var form = $('#ajax_selected_do_form');
			var selected_action = $('#selected_action');
			var selected_id = $('.selected_id').val();
			$(selected_action).val(action);

			var answer = confirm("are you sure？");
			if (answer){
				start_loading();
				$(form).ajaxSubmit({
					success: function(ret) {
						if (ret.status == 'ok') {
							var card = get_card();
							reload_card(card.obj);
							debug_card();
						} else {
							alert(ret.msg);
						}
						finish_loading();
					},
					dataType: 'json'
				});
			}
		});

		 //AJAX Delete
		 $('.ajax_delete').live('click',function(e){
			e.preventDefault();
			start_loading();
			var href = $(this).attr('href');
			var answer = confirm("are you sure？");
			if (answer){
			   start_loading();
			   $.post(href , {ajax:true} , function(ret) {
				  if (ret.status == 'ok') {
						var card = get_card();
						reload_card(card.obj);
						debug_card();
				  } else {  alert(ret.msg);  }
				  } , 'json').complete(function() {
						finish_loading();
			   });
			} else {
			}
		 });

	  	 // Table sorter
		 $('.table_sorter').live('click' , function(e) {
			var t = $(this);
			var paging_form_obj = $(this).closest('.card').find('#ajax_filter_form');

			var prev_field = $(paging_form_obj).find('#orderby').val();
			var prev_dir = $(paging_form_obj).find('#ascdesc').val();
			if (prev_field == t.data('field')) {
			   if (prev_dir == 'asc') $(paging_form_obj).find('#ascdesc').val('desc');
			   else $(paging_form_obj).find('#ascdesc').val('asc');
			}
			else {
			   $(paging_form_obj).find('#orderby').val(t.data('field'));
			   $(paging_form_obj).find('#ascdesc').val('asc');
			}
			$(paging_form_obj).trigger('submit');

		 });

		/* make sure when form search is submitted, offset is returned back to 0 @will */
		//$(document.frmSearch).bind('submit', function(e) {
		//	e.preventDefault();
		//	document.frmSearch.offset.value = 0;
		//	this.submit();
		//});
		//
		//$('.export_excel').live('click', function(e) {
		//	e.preventDefault();
		//	document.location = site_url + base_class + '/' + 'export_excel';
		//});
		//
		//$('.add_entry').click(function(e) {
		//	e.preventDefault();
		//	$('#form1').resetForm();
		//	$('#form_add_edit').dialog('option' , 'title' , 'Add ' + base_title);
		//	$('#form_method').val('add');
		//	$('#form_add_edit').dialog('open');
		//	setting.handle_after_add();
		//});
		//
		//$('.edit_entry').click(function(e) {
		//	e.preventDefault();
		//	var id = $(this).attr('id');
		//	$('#form_add_edit').dialog('option' , 'title' , 'Edit ' + base_title);
		//	$('#form_method').val('edit');
		//	$('#current_id').val(id);
		//
		//	$('#lobar').show();
		//
		//	// Load the detail
		//	$.post(site_url + base_class + '/load_edit' , {id : id} , function(data) {
		//		if (data._status == 'ok') {
		//			for (row in data) {
		//				$('#' + row).val(data[row]);
		//			}
		//			// Handle
		//			setting.handle_after_edit_load(id);
		//			$('#form_add_edit').dialog('open');
		//		}
		//		else {
		//			alert(data._msg);
		//		}
		//	} , 'json').complete(function() { $('#lobar').hide(); });
		//});
		//
		//$('#delete_entry').click(function(e) {
		//	e.preventDefault();
		//	if(confirm("Are you sure?")) {
		//		$('#form_del_action').val('delete');
		//		$('#form_del').submit();
		//	}
		//});
		//
		//$('#suspend_entry').click(function(e) {
		//	e.preventDefault();
		//	$('#form_del_action').val('suspend');
		//	$('#form_del').submit();
		//});
		//
		//$('#suspend_entry').click(function(e) {
		//	e.preventDefault();
		//	$('#form_del_action').val('suspend');
		//	$('#form_del').submit();
		//});
		//
		////fix for style (change status to disable)
		//$('#disable_entry').click(function(e) {
		//	e.preventDefault();
		//	$('#form_del_action').val('disable');
		//	$('#form_del').submit();
		//});
		////fix for style (change status to enabled)
		//$('#enable_entry').click(function(e) {
		//	e.preventDefault();
		//	$('#form_del_action').val('enable');
		//	$('#form_del').submit();
		//});
		//
		//$('#activate_entry').click(function(e) {
		//	e.preventDefault();
		//	$('#form_del_action').val('activate');
		//	$('#form_del').submit();
		//});
		//
		//$("#form_add_edit").dialog({
		//	width: setting.width,
		//	height: setting.height,
		//	autoOpen: false,
		//	modal: true,
		//	buttons: {
		//		Cancel: function() {
		//			$(this).dialog('close');
		//		},
		//		'Save': function() {
		//			$('#form1').submit();
		//		}
		//	}
		//});
		//
		//$('#form1').submit(function(e) {
		//	e.preventDefault();
		//	$('#form1').ajaxSubmit({
		//		success: function(data ) {
		//			if (data.status == 'ok') {
		//				location.reload(true);
		//			}
		//			else {
		//				alert(data.msg);
		//			}
		//		},
		//		dataType: 'json'
		//	});
		//});
		//
		//$('#check_all').click(function(e) {
		//	if ($(this).attr('checked') == true) {
		//		$('.entry_check').attr('checked' , true);
		//	}
		//	else {
		//		$('.entry_check').attr('checked' , false);
		//	}
		//});
		//
		//$('#per_page_select').change(function(e) {
		//	document.frmSearch.per_page.value = $(this).val();
		//	document.frmSearch.submit();
		//});

		// Datepicker
		//$(".datepicker").datepicker({
		//	dateFormat: 'yy-mm-dd'
		//});
		//$(".datetimepicker").datetimepicker({
		//	dateFormat: 'yy-mm-dd'
		//});
		// Datepicker fix
		//$('#ui-datepicker-div').css('z-index' , '2007');

		// Time Picker
		//$(".clockpicker").clockpick({
		//	starthour: 6,
		//	endhour: 20,
		//	minutedivisions: 12,
		//	military: true
		//});





		/**
		 * open modal dialog box
		 * @will
		 */
		//$('.dview').live('click',function(e) {
		//	e.preventDefault();
		//
		//	var target_dialog = "#dbox";
		//	var id = $(this).attr('id');
		//	var title = $(this).attr('title');
		//	var url = $(this).attr('href');
		//
		//	var params_string = $.trim($(this).attr('rel'));
		//	var params_arr = params_string.split(";");
		//	var params = new Array();
		//	for( var i=0; i<params_arr.length; i++) {
		//		var params_arr2 = $.trim(params_arr[i]).split("=");
		//		var params_key = $.trim(params_arr2[0]);
		//		var params_value = $.trim(params_arr2[1]);
		//		params[params_key] = params_value;
		//	}
		//	console.log(params['target']);
		//	if(params['dialog']) { target_dialog = params['dialog']; }
		//	if(params['width']) { setting.width = params['width']; }
		//	if(params['height']) { setting.height = params['height']; }
		//	if(params['position']) { setting.position = params['position']; }
		//
		//	console.log(setting.height);
		//	console.log(setting.width);
		//
		//	$.params = params;
		//	$('#lobar').show();
		//	$.post(url , {id : id} , function(data) {
		//		$( target_dialog ).html(data);
		//		$( target_dialog ).dialog({
		//			height: setting.height,
		//			width: setting.width,
		//			position: setting.position,
		//			modal: true,
		//			title: title
		//		});
		//	} , 'html').complete(function() { $('#lobar').hide(); });
		//});


		//if( use_ajax ) {
		//	$('a.add, button.add').each(function(a) {
		//		var url = $(this).attr('href');
		//		var new_url = url.replace("/add", "/ajax_add");
		//		$(this).attr('href', new_url);
		//	});
		//}



		/**
		 * if Use AJAX, load ajax functions
		 * @will
		 */
		/*
		if( use_ajax ) {
			//bind add and edit button
			$('a.add, button.add').each(function(a) {
				var url = $(this).attr('href');
				var new_url = url.replace("/add", "/ajax_add");
				$(this).attr('href', new_url);
				$(this).addClass("dview");
			});

			$('a.edit, button.edit').each(function(a) {
				var url = $(this).attr('href');
				var new_url = url.replace("/edit", "/ajax_edit");
				$(this).attr('href', new_url);
				$(this).addClass("dview")
			});


			$('#formSubmit').bind('keypress', function(e) { //13 = 'enter'
				if(e.which == 13) {
					$(this).submit();
				}
			});

			$('#formSubmit').bind('submit', function(e) {
				e.preventDefault();
					$('#formSubmit').ajaxSubmit({
						url: mod_url + 'ajax_submit_action',
						success: function(data) {
							if (data.status == 'ok') {
								location.reload(true);
							} else {
								alert(data.msg);
							}
						},
						dataType: 'json'
					});
			});

			//bind cancel button to close dialog
			$('.cancel').one('click', function(e) {
				e.preventDefault();
				$(this).closest('.ui-dialog-content').dialog('close');
			});
		}
		*/

	});
