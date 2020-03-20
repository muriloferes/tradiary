$(document).ready(() => {
	$('#senha').keypress(e => {
		if(e.key === 'Enter'){
			entrar();
		}
	});
});

function entrar(){
	$.service({
		loading: true,
		url: 'ajax/view/login/entrar.php',
		data: {
			idusuario: $('#idusuario').val(),
			senha: $('#senha').val()
		}
	});
}