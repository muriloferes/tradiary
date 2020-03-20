$.service = function(settings){

	// Segue com os mesmos argumentos do metodo "$.ajax" (url, data, success, error, beforeSend, etc...)
	settings = $.extend({
		// Argumentos padroes do "$.ajax"
		method: 'post',
		dataType: 'json',

		// Os argumentos listados abaixo sao os que nao existem no "$.ajax"
		autoError: true, // Se deve fazer tratamento automatico para erros
		loading: false // Se deve bloquear a tela com componente loading (pode receber um componente tambem)
	}, settings);

	// Clona as configuracoes
	const finalSettings = { ...settings };
	
	// Modifica o metodo "beforeSend"
	finalSettings.beforeSend = (jqXHR) => {
		if(settings.loading){
			if(typeof settings.loading === 'boolean'){
				$.loading(true);
			}else{
				$(settings.loading).loading(true);
			}
		}
		if(typeof settings.beforeSend === 'function'){
			settings.beforeSend(jqXHR);
		}
	};

	// Modifica o metodo "complete"
	finalSettings.complete = (jqXHR, textStatus) => {
		if(settings.loading){
			if(typeof settings.loading === 'boolean'){
				$.loading(false);
			}else{
				$(settings.loading).loading(false);
			}
		}
		if(settings.progress){
			$.loading(false);
		}
		if(typeof settings.complete === 'function'){
			settings.complete(jqXHR, textStatus);
		}
	};

	// Verifica se deve faze o tratamento automatico de erro
	if(settings.autoError){
		finalSettings.error = (jqXHR, textStatus, errorThrown) => {
			if(!['abort'].includes(textStatus)){
				alert('Desculpe, houve uma falha de conexão com o servidor.\nPor favor, tente novamente.');
			}
			if(typeof settings.error === 'function'){
				settings.error(jqXHR, textStatus, errorThrown);
			}
		};
		finalSettings.success = (result) => {
			if(result.status === '2'){
				alert(result.message);
			}else{
				if(typeof settings.success === 'function'){
					settings.success(result);
				}
			}
		};
	}

	delete finalSettings.autoError;
	delete finalSettings.loading;

	return $.ajax(finalSettings);
};
