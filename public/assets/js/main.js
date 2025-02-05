var hidden = true;
var count = 1;

var curRow = null;
var curRowID = null;

var edit = false;

var tempVkupno = 0;

var Type = 1;

var data = new Array();
$(document).ready(function(){
	
});

function ChangeType(t){
	Type = t.value;
	
	
	$('#fakturi').hide();
	$('#profakturi').hide();
	$('#ponudi').hide();
	
	switch(Type){
		case "1":
			$('#i_faktura').attr('placeholder','Број фактура');
			$('#fakturi').show();
			break;
		case "2":
			$('#i_faktura').attr('placeholder','Број профактура');
			$('#profakturi').show();
			break;
		case "3":
			$('#i_faktura').attr('placeholder','Број понуда');
			$('#ponudi').show();
			break;
	}
}

function FindRow(t){

	curRow = $(t).parent('td').parent('tr');

	curRowID = $(curRow).attr('id');
	
	var opis = $(curRow.children('td')[1]).text();
	var cena = parseFloat($(curRow.children('td')[4]).text());
	var kolicina = parseFloat($(curRow.children('td')[2]).text());
	$('#i_opis').val(opis);
	$('#i_kolicina').val(kolicina);
	$('#i_cena').val(cena);
	
	edit = true;
	
	var vkupno = kolicina * cena;
	
	vkupno = vkupno.toFixed(2);
	
	tempVkupno = parseFloat(vkupno);
	
}

function EditRow(){
	
}

function AddRow(){
	var opis = $('#i_opis').val();
	
	if(opis.length == 0){
		alert('Внеси вредности');
		return;
	}
	
	
	if(hidden)
		ShowPreview();
		
	hidden = false;
	
	var datum = $('#i_datum').val();
	var br_faktura = $('#i_faktura').val();
	var primac = $('#i_primac').val();
	var grad = $('#i_grad').val();
	
	var kolicina = parseFloat($('#i_kolicina').val());
	var cena = parseFloat($('#i_cena').val());
	
	var vkupno = kolicina * cena;
	
	vkupno = vkupno.toFixed(2);
	
	vkupno = parseFloat(vkupno);
	
	var html = '<tr id="' + count + '">'+
					'<td>' + count + '</td>'+
					'<td>' + opis + '</td>'+
					'<td>' + kolicina + '</td>'+
					'<td>x</td>'+
					'<td>' + cena + '</td>'+
					'<td>=</td>'+
					'<td>' + vkupno + '</td>'+
					'<td><input onclick="FindRow(this)" type="radio" name="edit" /></td>'+
				'</tr>';
		
	
	if(edit){
		
		$(curRow.children('td')[1]).text(opis);
		$(curRow.children('td')[2]).text(kolicina);
		$(curRow.children('td')[4]).text(cena);
		$(curRow.children('td')[6]).text(vkupno);
		
		curRow = null;
		
		
		$('input[type="radio"]').attr('checked',false);
	}else{
		$('.preview #ex').append(html);
	}
				
	
	var total = parseFloat($('#totals').text());		
	
	total = total + vkupno;
	
	total = total - tempVkupno;
	
	$('#totals').text(total);
	
	data[0] = new Object();
	data[0].total = total;
	data[0].datum = datum;
	data[0].br_faktura = br_faktura;
	data[0].primac = primac;
	data[0].grad = grad;
	data[0].t_type = Type;
	
	id = count;
	if(edit){
		id = curRowID;
		edit = false;
	}
	
	data[id] = new Object();
	
	data[id].id = count;
	data[id].opis = opis;
	data[id].kolicina = kolicina;
	data[id].cena = cena;
	data[id].vkupno = vkupno;
	
	count++;
}

function Save(){
	$.ajax({
		type: "POST",
		url: '/functions.php',
		data: "data=" + JSON.stringify(data),
		success: function(url){
			
			window.open(
			  '/' + url,
			  '_blank'
			);
		},
		error: function(a,b,c){
			
		},
		dataType: 'json'
	});
}

function ShowPreview(){
	$('.preview').toggle();
}