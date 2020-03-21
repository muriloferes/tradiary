$(document).ready(() => {
	$('.btn-group .btn').click(function(){
		$(this).closest('.btn-group').find('.btn').removeClass('btn-primary').addClass('btn-light');
		$(this).removeClass('btn-light').addClass('btn-primary');
		buscar_dados();
	});

	$('#dtoperacao').change(() => operacao_buscar());

	$('#totalbruto, #totalliquido, #deposito, #retirada').change(function(){
		$(this).val(parseFloat($(this).val()).toFixed(2));
	});

	operacao_fechar();
	buscar_dados();
});

let chart = null;
function buscar_dados(){
	const coluna = $('#grp-valor .btn.btn-primary').attr('coluna');
	const tempo = $('#grp-tempo .btn.btn-primary').attr('tempo');

	$.service({
		loading: true,
		url: 'ajax/view/painel/buscar-dados.php',
		data: { coluna, tempo },
		success: result => {
			if(chart !== null) chart.destroy();

			chart = new Chart('data-chart', result.chart);
			$('#data-table').html(result.table);
		}
	});
}

function operacao_abrir(){
	$('#form-operacao').show();
}

function operacao_buscar(){
	const dtoperacao = $('#dtoperacao').val();
	if(!dtoperacao || dtoperacao.length === 0){
		return true;
	}
	$.service({
		loading: true,
		url: 'ajax/view/painel/operacao-buscar.php',
		data: { dtoperacao },
		success: result => {
			if(!result.data){
				return true;
			}
			for(const id in result.data){
				$(`#${id}`).val(result.data[id]);
			}
		}
	});
}

function operacao_fechar(){
	$('#form-operacao').hide();
}

function operacao_gravar(){
	const dtoperacao = $('#dtoperacao').val();
	if(!dtoperacao || dtoperacao.length === 0){
		alert('Informe a data da operação.');
		return false;
	}
	$.service({
		url: 'ajax/view/painel/operacao-gravar.php',
		data: {
			dtoperacao,
			contratos: $('#contratos').val(),
			totalbruto: $('#totalbruto').val(),
			totalliquido: $('#totalliquido').val(),
			deposito: $('#deposito').val(),
			retirada: $('#retirada').val()
		},
		success: () => {
			operacao_limpar();
			alert('Gravado com sucesso!');
		}
	});
}

function operacao_limpar(){
	$('#dtoperacao').val('');
	$('#contratos').val('');
	$('#totalbruto').val('');
	$('#totalliquido').val('');
	$('#deposito').val('');
	$('#retirada').val('');
}