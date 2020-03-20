$(document).ready(() => {
	$('.btn-group .btn').click(function(){
		$(this).closest('.btn-group').find('.btn').removeClass('btn-primary').addClass('btn-light');
		$(this).removeClass('btn-light').addClass('btn-primary');
	});

	$('#totalbruto, #totalliquido').on('change', function(){
		$(this).val(parseFloat($(this).val()).toFixed(2));
	});
});