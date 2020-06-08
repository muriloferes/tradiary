$(document).ready(() => {
	$('.btn-group .btn').click(function(){
		$(this).closest('.btn-group').find('.btn').removeClass('btn-primary').addClass('btn-light');
		$(this).removeClass('btn-light').addClass('btn-primary');
	});

	$('#grp-tempo .btn').click(() => buscar_dados_tabela());

	$('#dtoperacao').change(() => operacao_buscar());

	$('#totalbruto, #totalliquido, #deposito, #retirada').change(function(){
		$(this).val(parseFloat($(this).val()).toFixed(2));
	});

	operacao_fechar();
	buscar_dados_grafico();
});

function buscar_dados(){
	if($('#page-chart').is(':visible')){
		buscar_dados_grafico();
	}else{
		buscar_dados_tabela();
	}
}

function buscar_dados_grafico(){
	$('#grp-tempo').hide();
	$('#page-chart').show();
	$('#page-table').hide();

	$.service({
		loading: true,
		url: 'ajax/view/painel/buscar-dados-grafico.php',
		success: result => {
			for(const name in result.charts){
				const id = name.split('_').join('-');
				montar_grafico(id, result.charts[name]);
			}
		}
	});
}

function buscar_dados_tabela(){
	$('#grp-tempo').show();
	$('#page-chart').hide();
	$('#page-table').show();

	const tempo = $('#grp-tempo .btn.btn-primary').attr('tempo');

	$.service({
		loading: true,
		url: 'ajax/view/painel/buscar-dados-tabela.php',
		data: { tempo },
		success: result => {
			$('#page-table').html(result.table);
		}
	});
}

function montar_grafico(id, chartData){
	const element = document.getElementById(id);
	const chart = $(element).prop('chart');

	if(chart) chart.destroy();

	if(chartData.type === 'bar'){
		chartData.options = {
			...chartData.options,
			tooltips: {
				enabled: false
			},
			hover: {
				animationDuration: 0
			},
			animation: {
				duration: 500,
				onComplete: function(){
					const ctx = this.chart.ctx;
					ctx.font = Chart.helpers.fontString(10, 'normal', Chart.defaults.global.defaultFontFamily);
					ctx.fillStyle = this.chart.config.options.defaultFontColor;
					ctx.textAlign = 'center';
					ctx.textBaseline = 'bottom';
					this.data.datasets.forEach(function(dataset){
						for(let i = 0; i < dataset.data.length; i++){
							const model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
							ctx.fillText(dataset.data[i], model.x, model.y + (dataset.data[i] < 0 ? 12 : -2));
						}
					});
				}
			}
		};
	}

	chartData.plugins = [{
		beforeDraw: chartInstance => {
			if(chartInstance.config.type !== 'bar'){
				return true;
			}

			const drawLine = (lineAt, color, width = 1) => {
				const ctxPlugin = chartInstance.chart.ctx;
				const xAxe = chartInstance.scales[chartInstance.config.options.scales.xAxes[0].id];
				const yAxe = chartInstance.scales[chartInstance.config.options.scales.yAxes[0].id];
				ctxPlugin.strokeStyle = color;
				ctxPlugin.lineWidth = width;
				ctxPlugin.beginPath();
				lineAt = yAxe.getPixelForValue(lineAt);
				ctxPlugin.moveTo(xAxe.left, lineAt);
				ctxPlugin.lineTo(xAxe.right, lineAt);
				ctxPlugin.stroke();
			};

			const listSum = list => {
				if(list.length === 0){
					return 0;
				}else{
					return list.reduce((a, b) => (parseFloat(a) + parseFloat(b)));
				}
			};

			const positivos = [];
			const negativos = [];
			chartInstance.data.datasets.forEach(function(dataset){
				for(const value of dataset.data){
					if(value > 0) positivos.push(value);
					if(value < 0) negativos.push(value);
				}
			});
			const final = [...positivos, ...negativos];

			drawLine((listSum(positivos) / positivos.length), 'rgba(0, 200, 0, 1)', 1);
			drawLine((listSum(negativos) / negativos.length), 'rgba(255, 0, 0, 1)', 1);
			drawLine((listSum(final) / final.length), 'rgba(255, 255, 0, 1)', 3);
		}
	}];

	$(element).prop('chart', new Chart(id, chartData));
}

function operacao_abrir(){
	$('#form-operacao').show();
	$('#btn-operacao-alterar .fa-plus').hide();
	$('#btn-operacao-alterar .fa-minus').show();
}

function operacao_alternar(){
	if($('#form-operacao').is(':visible')){
		operacao_fechar();
	}else{
		operacao_abrir();
	}
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

function operacao_excluir(){
	const dtoperacao = $('#dtoperacao').val();
	if(!dtoperacao || dtoperacao.length === 0){
		alert('Informe a data da operação.');
		return false;
	}
	if(!confirm('Tem certeza que deseja excluír a operação do dia informado?')){
		return false;
	}
	$.service({
		url: 'ajax/view/painel/operacao-excluir.php',
		data: { dtoperacao },
		success: () => {
			operacao_limpar();
			buscar_dados();
			alert('Excluído com sucesso!');
		}
	});
}

function operacao_fechar(){
	$('#form-operacao').hide();
	$('#btn-operacao-alterar .fa-plus').show();
	$('#btn-operacao-alterar .fa-minus').hide();
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
			buscar_dados();
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