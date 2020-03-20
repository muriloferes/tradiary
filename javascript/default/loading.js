$.loading = function(status){
	if(status === true){
		$('body').loading(true);
	}else{
		$('body').loading(false);
	}
};

$.fn.loading = function(status){
	$(this).each(function(){
		if(status){
			if(!$(this).prop('loading')){
				const loading = document.createElement('div');
				$(this).after(loading);
				$(loading).addClass('loading');
				$(loading).css('margin-top', ($(this).outerHeight() * -1));
				$(loading).height($(this).outerHeight());
				$(loading).width($(this).outerWidth());
				$(this).prop('loading', loading);
			}
		}else{
			const loading = $(this).prop('loading');
			if(loading){
				$(loading).remove();
			}
			$(this).prop('loading', null);
		}
	});
	return this;
};
