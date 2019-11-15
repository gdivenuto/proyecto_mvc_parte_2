
function mostrarImagenCargando(capa_destino)
{
	jQuery(capa_destino).html('<div class="text-center"><img src="imagenes/cargando.gif"></div>');
}

function refrescar(enlace, capa_destino)
{
	jQuery(capa_destino).ajaxStart(mostrarImagenCargando(capa_destino));
 	jQuery(capa_destino).load(enlace);
}

function mostrarModal()
{
	// Si hay un mensaje que mostrar
	if ( $('#mensaje').val() != '' ) {
		// Si es un mensaje de Error
		if ($('#tipo_mensaje').val() == '2')
			$('#mensaje_en_modal').html('<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;'+$('#mensaje').val()+'</div>');
		// Si es un mensaje de Advertencia
		else if ($('#tipo_mensaje').val() == '3')
			$('#mensaje_en_modal').html('<div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;'+$('#mensaje').val()+'</div>');
		// sino, es un mensaje de éxito
		else
			$('#mensaje_en_modal').html('<div class="alert alert-success"><span class="glyphicon glyphicon-ok-sign"></span>&nbsp;'+$('#mensaje').val()+'</div>');
		
		// Se muestra la modal, ejecutando el evento click del enlace
		$('#muestra_modal').click();
	}
}

function ocultarModal(id_modal) {
	$(id_modal).modal('hide');
	if ($('.modal-backdrop').is(':visible')) {
	  	$('body').removeClass('modal-open'); 
	 	$('.modal-backdrop').remove(); 
	};
}

function mostrarCartel(mensaje, tipo_mensaje) {
	// Si es un mensaje de error
	if (tipo_mensaje == 2)
		$('#mensaje_en_modal').html('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;ATENCI&Oacute;N<br><br>'+mensaje+'</div>');
	else if (tipo_mensaje == 3)
		$('#mensaje_en_modal').html('<div class="alert alert-warning"><span class="glyphicon glyphicon-warning-sign"></span>&nbsp;ADVERTENCIA<br><br>'+mensaje+'</div>');
	else // sino de éxito
		$('#mensaje_en_modal').html('<div class="alert alert-success"><span class="glyphicon glyphicon-ok-sign"></span>&nbsp;OK<br><br>'+mensaje+'</div>');
	
	// Se muestra la modal
	$('#muestra_modal').click();
}

function soloEnteros(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    permitidos = "0123456789";
    especiales = [8,9,13,37,39];
    tecla_especial = false;

    for (var i in especiales)
		if ( key == especiales[i] ) {
			tecla_especial = true;
			break;
        } 
   
	// SI LA TECLA NO ES NUMERICA NI ESPECIAL
    if (permitidos.indexOf(tecla)==-1 && !tecla_especial)
        return false;
}

function formatearMoneda(elemento)
{
	// SE TOMA EL VALOR INGRESADO
	var valor_moneda = elemento.value;
	// ej: 4.200,50
	
	// REEMPLAZA LA COMA POR EL PUNTO
	var sin_coma = valor_moneda.replace(',', '.');
	// ej: 4.200.50
	//alert(sin_coma);
	
	// SE SEPARA POR CADA PUNTO
	var partes = sin_coma.split('.');
	// ej: 4 200 50
	
	// SE TOMA LA ULTIMA PARTE, LA DECIMAL
	var parte_decimal = partes[partes.length-1];
	// ej: 50
	//alert(parte_decimal);
	
	// SE FORMA LA PARTE ENTERA
	var parte_entera = '';
	var i;
	//alert(partes.length-1);
	for (i=0; i < partes.length-1; i++)
		parte_entera += partes[i];
	
	//alert(parte_decimal);
	
	// SE UNE LA PARTE ENTERA CON LA DECIMAL
	var union = '';
	
	if (parte_entera)
		union = parte_entera+'.'+parte_decimal;
	else
		union = parte_decimal;

	// ej: 4200.50
	//alert(union);
	
	// SE ASIGNA EL VALOR AL ELEMENTO
	elemento.value = union;
}
