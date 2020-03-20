$(document).ready(() => {
	$('#senha').keypress(e => {
		if(e.key === 'Enter'){
			entrar();
		}
	});

	$('#idusuario').val(localStorage.getItem('idusuario'));
	$('#senha').val(localStorage.getItem('senha'));
});

function entrar(){
	localStorage.setItem('idusuario', $('#idusuario').val());
	localStorage.setItem('senha', $('#senha').val());

	$.service({
		loading: true,
		url: 'ajax/view/login/entrar.php',
		data: {
			idusuario: $('#idusuario').val(),
			senha: $('#senha').val()
		},
		success: () => (window.location.href = 'painel')
	});
}