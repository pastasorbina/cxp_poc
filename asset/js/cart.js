function load_display_cart(){
		$.post(site_url + 'cart/display_cart' , {} , function(data) {
			$('#snippet_cart_box').html(data);
		} , 'html').complete(function() {
			load_total_qty();
			$('#lobar').hide();
		});
	}
	
	

function ajax_get_cart_qty(){
	$.post(site_url + 'cart/ajax_get_cart_qty' , {} , function(data) {
		$('.cart_total_qty').html(data['total_qty']);
		if(data['total_qty'] <= 0) {
			reset_cart();
		}
	} , 'json').complete(function() { });
}
	
function load_cart_snippet() {
	url = site_url+ "cart/ajax_view_cart_snippet/";
	$.post(url, {}, function(data) {
		$('#cart_snippet_inner').html(data);
	}, 'html');
}

function ajax_get_cart_subtotal() {
	url = site_url+ "cart/ajax_get_cart_subtotal/";
	$.post(url, {}, function(data) {
		var cart_subtotal = data.cart_subtotal;
		$('.cart_subtotal').html(cart_subtotal);
		//alert(total_price);
	}, 'json');
}


$(document).ready(function(e) {
	
	
	$('#toggle_cart_snippet').live('click', function(e) {
		e.preventDefault();
		$('#cart_snippet_wrap').toggle();
	});
	
	
	$('.cart_submit').live('click', function(e) {
		var obj = $(this);
		var p_id = $(this).parent('form').children('.cart_p_id').val();
		var pr_id = $(this).parent('form').children('.cart_pr_id').val();
		var quantity = $(this).parent('form').children('.cart_quantity').val();
		var size = $(this).parent('form').children('.cart_size').val();
		var href = site_url+"cart/insert";
		$.post(href, {
						p_id:p_id,
						pr_id:pr_id,
						quantity:quantity,
						size:size
					}, function(data) {
			if (data.status == 'ok') {
				wgm_open_modal(site_url+'cart/ajax_success/');
				
				$('#size').trigger('change');
			 } else {
				alert(data.msg);
				console.log(data.msg);
			 }  
		}, 'json' ).complete( function(e) {
			load_cart_snippet();
			ajax_get_cart_qty();
			ajax_get_cart_subtotal();
		}); 
	});

});