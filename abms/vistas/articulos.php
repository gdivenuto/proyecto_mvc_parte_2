<?php
if ( !isset($_SESSION) ) 
    session_start(); 

class VistaArticulos extends VistaBase
{
	private $directorio;
	private $controlador;
    private $formulario;
	
    public function __construct()
    {
    	parent::__construct();
    	
		$this->directorio  = 'abms';
		$this->controlador = 'articulos';
		$this->formulario  = 'formArticulo';
	}
    
	public function listar($datos, $mensaje = '', $tipo_mensaje = '', $filtro)
	{
		$cantidad 	      = count($datos['info']);
		//$cant_proveedores = count($datos['proveedores']);
		$cant_categorias  = count($datos['categorias']);
    	?>
    	<input type="hidden" id="mensaje" name="mensaje" value="<?php echo $mensaje; ?>">
        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?php echo $tipo_mensaje; ?>">
        <input type="hidden" id="directorio" name="directorio" value="<?php echo $this->directorio; ?>">
		<input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>">

		<div class="row">
			<div class="col-md-12 titulo_entidad">Listado de Art&iacute;culos</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<input type="text" id="valor_buscado" name="valor_buscado" value="<?php echo utf8_encode($filtro['valor_buscado']); ?>" class="form-control input-sm" placeholder="Busque aqu&iacute;...">
			</div>
			<?php /** ?>
			<div class="col-lg-2">
                <select id="f_id_proveedor" name="f_id_proveedor" class="form-control input-sm">
                    <option value="0">Proveedor ...</option>
                    <?php
                    for ( $p=0; $p < $cant_proveedores; $p++ )
                        echo '<option value="'.$datos['proveedores'][$p]['p_id'].'" >'.$datos['proveedores'][$p]['p_razon_social'].'</option>';
                    ?>
                </select>
            </div>
            <?php /**/ ?>
			<div class="col-lg-2">
                <select id="f_id_categoria" name="f_id_categoria" class="form-control input-sm">
                    <option value="0">Categor&iacute;a ...</option>
                    <?php
                    for ( $c=0; $c < $cant_categorias; $c++ )
                        echo '<option value="'.$datos['categorias'][$c]['c_id'].'" >'.$datos['categorias'][$c]['c_nombre'].'</option>';
                    ?>
                </select>
            </div>
			<div class="col-md-4">
				<button type="button" id="btLimpiar" class="btn btn-default btn-sm" title="Limpiar criterio de b&uacute;squeda"><span class="glyphicon glyphicon-repeat"></span>&nbsp;Limpiar</button>
				<button type="button" id="btNuevo" class="btn btn-primary btn-sm" title="Nuevo"><span class="glyphicon glyphicon-plus"></span>&nbsp;Nuevo</button>
			</div>
		</div>
		<!-- Grilla -->
		<div class="row espacio_externo_top_10">
			<div class="col-md-12">
			<?php
			if ( $cantidad > 0 ) {
			?>
				<div class="table-responsive">
				  	<table class="table table-hover table-bordered table-condensed">
				    	<thead>
				    		<tr class="active">
								<th width="32" colspan="2">&nbsp;</th>
								<th>C&oacute;digo</th>
								<th>Nombre</th>
								<th>Categor&iacute;a</th>
								<th class="text-center">$ Costo</th>
								<th class="text-center">$ Venta</th>
								<th class="text-center">Cantidad</th>
								<th class="text-center">Cant. M&iacute;nima</th>
                                <th class="text-center">Habilitado</th>
							</tr>
				    	</thead>
				    	<tbody>
				    		<?php
							for ($i=0; $i < $cantidad; $i++) {
								$dato = &$datos['info'][$i];
							?>
								<tr <?php echo ($dato['a_habilitado'] == '0') ? ' class="text-muted"' : ''; ?> > 
									
									<td width="16">
										<a style="width:21px;height:16px;display:block;" title="Editar" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar&id=<?php echo $dato['a_id']; ?>&pagina=<?php echo $filtro['pagina']; ?>', '#contenidoAjaxPrincipal');">
											<span class="glyphicon glyphicon-pencil"></span>
										</a>
									</td>
									
									<td width="16">
										<a title="Eliminar" href="javascript:if(confirm('¿Desea eliminar el registro?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&id=<?php echo $dato['a_id']; ?>&pagina=<?php echo $filtro['pagina']; ?>', '#contenidoAjaxPrincipal');};">
											<span class="glyphicon glyphicon-trash"></span>
										</a>
									</td>
									
									<td><?php echo ($dato['a_codigo']) ? $dato['a_codigo'] : '&nbsp;'; ?></td>
									<td><?php echo ($dato['a_nombre']) ? $dato['a_nombre'] : '&nbsp;'; ?></td>
									<td><?php echo ($dato['nombre_categoria']) ? $dato['nombre_categoria'] : '&nbsp;'; ?></td>
									<td class="text-right"><?php echo ($dato['a_precio_compra']) ? number_format($dato['a_precio_compra'], 2, ',', '.') : '&nbsp;'; ?></td>
									<td class="text-right bg-primary"><?php echo ($dato['a_precio_venta']) ? number_format($dato['a_precio_venta'], 2, ',', '.') : '&nbsp;'; ?></td>
									<td class="text-right"><?php echo ($dato['a_cantidad']) ? $dato['a_cantidad'] : 0; ?></td>
									<td class="text-right"><?php echo ($dato['a_cantidad_minima']) ? $dato['a_cantidad_minima'] : 0; ?></td>
									<td class="text-center">
										<?php 
										if ( $dato['a_habilitado'] == '1') {
										?>
											<a title="Deshabilitar registro" href="javascript:if(confirm('¿Desea deshabilitar el registro?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=modificarEstado&id=<?php echo $dato['a_id']; ?>&habilitado=<?php echo $dato['a_habilitado']; ?>&pagina=<?php echo $filtro['pagina']; ?>', '#contenidoAjaxPrincipal');};">
												<span class="glyphicon glyphicon-ok"></span>
											</a>
										<?php
										} else {
										?>
											<a title="Habilitar registro" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=modificarEstado&id=<?php echo $dato['a_id']; ?>&habilitado=<?php echo $dato['a_habilitado']; ?>&pagina=<?php echo $filtro['pagina']; ?>', '#contenidoAjaxPrincipal');">
												<span class="glyphicon glyphicon-remove"></span>
											</a>
										<?php
										}
										?>
									</td>
								</tr>
							<?php
							}
							?>
				    	</tbody>
				  	</table>
				</div>
			<?php
			} else
				echo '<div class="alert alert-info">No se han encontrado resultados.</div>';
			?>
			</div>
		</div>
		<!-- Paginador -->
		<div class="row">
			<div class="col-md-6">
				<?php echo "Registros en la p&aacute;gina actual: ".$i."&nbsp;&nbsp;|&nbsp;&nbsp;Total de resultados: ".$filtro['cantidad']; ?>
			</div>
			<div class="col-md-6">
				<?php 
				if ( $filtro['cantidad'] > 0 ){
					// Se arma el criterio del buscador para la url del paginador
                    $criterio_buscador = "&valor_buscado=".$filtro['valor_buscado']."&f_id_categoria=".$filtro['f_id_categoria'];
                
					if ( $filtro['pagina'] != 1 ){
					?>
						<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar<?php echo $criterio_buscador; ?>&pagina=1', '#contenidoAjaxPrincipal');" title="Ver los primeros <?php echo $filtro['rango']; ?> registros"><span class="glyphicon glyphicon-fast-backward"></span></a>
					<?php
					}
					if ( $filtro['pagina_ant'] != 0 ){
					?>
						<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar<?php echo $criterio_buscador; ?>&pagina=<?php echo $filtro['pagina_ant']; ?>', '#contenidoAjaxPrincipal');" title="Ver los <?php echo $filtro['rango']; ?> registros anteriores"><span class="glyphicon glyphicon-backward"></span></a>
					<?php
					}
					?>
					<span class="adm_numeros text-center">
						<?php echo "&nbsp;".$filtro['pagina']." de ".$filtro['nro_paginas']."&nbsp;"; ?>
					</span>
					<?php
					if ( $filtro['pagina'] != $filtro['nro_paginas'] ){
					?>
						<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar<?php echo $criterio_buscador; ?>&pagina=<?php echo $filtro['pagina_sgte']; ?>', '#contenidoAjaxPrincipal');" title="Ver los <?php echo $filtro['rango']; ?> registros siguientes"><span class="glyphicon glyphicon-forward"></span></a>
					<?php
					}
					if ( $filtro['pagina'] != $filtro['nro_paginas'] ){
					?>
						<a href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=listar<?php echo $criterio_buscador; ?>&pagina=<?php echo $filtro['nro_paginas']; ?>', '#contenidoAjaxPrincipal');" title="Ver los &uacute;ltimos <?php echo $filtro['rango']; ?> registros"><span class="glyphicon glyphicon-fast-forward"></span></a>
					<?php
					}
				}
				?>
			</div>
		</div>
		<script type="text/javascript">
			//	SE REFRESCA EL LISTADO CON EL RESULTADO DEVUELTO SEGUN EL VALOR A FILTRAR EN EL CAMPO ESPECIFICADO
			function buscar() {
				var url = 'abms/index.php?controlador='+$('#controlador').val()+'&accion=listar&valor_buscado='+$('#valor_buscado').val()+'&f_id_categoria='+$('#f_id_categoria').val();
				
				refrescar(url, '#contenidoAjaxPrincipal');
			}
			
			$('#valor_buscado').keypress( function(e) {
			    if ( $('#valor_buscado').val() != '' && e.which == 13 )
			        buscar();
			});
			
			$('f_id_categoria').value = '<?php echo ($filtro['f_id_categoria']) ? $filtro['f_id_categoria'] : 0; ?>';

			$('#f_id_categoria').change( function() {
				buscar();
			});

			$('#btLimpiar').click( function() {
				refrescar('abms/index.php?controlador='+$('#controlador').val()+'&accion=listar&pagina=<?php echo $filtro['pagina']; ?>', '#contenidoAjaxPrincipal');
			});
			
			$('#btNuevo').click( function() {
				refrescar('abms/index.php?controlador='+$('#controlador').val()+'&accion=editar', '#contenidoAjaxPrincipal');
			});

			mostrarModal();
		</script>
		<?php
	}
	
	public function editar($datos = null, $mensaje = '', $tipo_mensaje = '')
	{
		$titulo_operacion   = ( isset($datos['a_id']) ) ? 'Edici&oacute;n' : 'Alta';
		$solo_lectura		= ( isset($datos['a_id']) ) ? 'readonly' : '';
		$color_segun_accion = ( isset($datos['a_id']) ) ? ' class="text-muted"' : '';// Si se edita, se ensombrece el texto
		$id_definido        = ( isset($datos['a_id']) ) ? $datos['a_id'] : '';
		$valor_pagina       = ( isset($datos['pagina']) ) ? $datos['pagina'] : 1;
		$usuario   			= ( isset($datos['u_usuario']) ) ? $datos['u_usuario'] : '';

		$cant_categorias = count($datos['categorias']);
		?>
		<form class="form-horizontal" role="form" action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>" enctype="multipart/form-data">
				
			<input type="hidden" id="mensaje" name="mensaje" value="<?php echo $mensaje; ?>">
    		<input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?php echo $tipo_mensaje; ?>">

			<input type="hidden" id="nombre_formulario" name="nombre_formulario" value="#<?php echo $this->formulario; ?>" />
			<input type="hidden" id="directorio" name="directorio" value="<?php echo $this->directorio; ?>" />
			<input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
			<input type="hidden" id="accion" name="accion" value="guardar" />
			<input type="hidden" id="pagina" name="pagina" value="<?php echo $valor_pagina; ?>" />
			
			<input type="hidden" id="a_id" name="a_id" value="<?php echo $id_definido; ?>" />
			<!-- Nombre de la foto -->
			<input type="hidden" id="a_foto" name="a_foto" value="<?php echo $datos['a_foto']; ?>" />
			<!-- Estado habilitado -->
			<input type="hidden" id="a_habilitado" name="a_habilitado" value="<?php echo $datos['a_habilitado']; ?>" />

			<div class="row">
				<div class="col-md-12 titulo_entidad"><?php echo $titulo_operacion; ?> del Art&iacute;culo</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="a_id_categoria" class="col-lg-3 control-label">Categor&iacute;a</label>
						<div class="col-lg-3">
							<select id="a_id_categoria" name="a_id_categoria" class="form-control input-sm">
			                    <option value="0">---</option>
			                    <?php
			                    for ( $c=0; $c < $cant_categorias; $c++ )
			                        echo '<option value="'.$datos['categorias'][$c]['c_id'].'" >'.$datos['categorias'][$c]['c_nombre'].'</option>';
			                    ?>
			                </select>
						</div>
					</div>
					<div class="form-group">
						<label for="a_codigo" class="col-lg-3 control-label">C&oacute;digo</label>
						<div class="col-lg-9">
							<input type="text" id="a_codigo" name="a_codigo" value="<?php echo $datos['a_codigo']; ?>" class="form-control" >
						</div>
					</div>
					<div class="form-group">
						<label for="a_nombre" class="col-lg-3 control-label">Nombre</label>
						<div class="col-lg-9">
							<input type="text" id="a_nombre" name="a_nombre" value="<?php echo $datos['a_nombre']; ?>" class="form-control" >
						</div>
					</div>
					
					<div class="form-group">
		                <label for="a_precio_compra" class="col-lg-3 control-label">Precio de Costo $</label>
		                <div class="col-lg-2">
		                    <input type="text" id="a_precio_compra" name="a_precio_compra" value="<?php echo ($datos['a_precio_compra'] != '') ? $datos['a_precio_compra'] : '0.00'; ?>" class="form-control text-right" onkeypress="return soloEnteros(event)" onchange="formatearMoneda(this);" tabindex="">
		                </div>
		                <label for="a_precio_venta" class="col-lg-3 control-label">Precio de Venta $</label>
		                <div class="col-lg-2">
		                    <input type="text" id="a_precio_venta" name="a_precio_venta" value="<?php echo ($datos['a_precio_venta'] != '') ? $datos['a_precio_venta'] : '0.00'; ?>" class="form-control text-right" onkeypress="return soloEnteros(event)" onchange="formatearMoneda(this);" tabindex="">
		                </div>
		            </div>
		            <div class="form-group">
		                <label for="a_cantidad" class="col-lg-3 control-label">Cantidad</label>
		                <div class="col-lg-2">
		                    <input type="text" id="a_cantidad" name="a_cantidad" value="<?php echo ($datos['a_cantidad'] != '') ? $datos['a_cantidad'] : 0; ?>" class="form-control text-right" onkeypress="return soloEnteros(event)" tabindex="">
		                </div>
		                <label for="a_cantidad_minima" class="col-lg-3 control-label">Cantidad m&iacute;nima</label>
		                <div class="col-lg-2">
		                    <input type="text" id="a_cantidad_minima" name="a_cantidad_minima" value="<?php echo ($datos['a_cantidad_minima'] != '') ? $datos['a_cantidad_minima'] : 0; ?>" class="form-control text-right" onkeypress="return soloEnteros(event)" tabindex="">
		                </div>
		            </div>
		            <div class="form-group">
	                    <label for="a_descripcion" class="col-lg-3 control-label">Descripci&oacute;n</label>
	                    <div class="col-lg-9">
	                        <textarea id="a_descripcion" name="a_descripcion" class="form-control" rows="10"><?php echo ($datos['a_descripcion'] != '') ? $datos['a_descripcion'] : ''; ?></textarea>
	                     </div>
	                </div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<div class="col-md-12 text-center">
							<img src="./fotos_articulos/<?php echo (isset($datos['a_foto']) && $datos['a_foto'] != '') ? $datos['a_foto'] : 'no_disponible.jpg'; ?>" class="img-thumbnail">
						</div>
					</div>
					<div class="form-group">
		            	<label for="n_texto" class="col-lg-3 control-label">Foto</label>
		                <div class="col-lg-6">
							<input type="file" id="archivo" name="archivo" class="form-control">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-9 col-md-offset-2">
					<!-- Botón Guardar -->
					<button type="button" id="btGuardar" class="btn btn-sm btn-primary" title="Guardar informaci&oacute;n"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
					<!-- Botón Cancelar -->
					<button type="button" id="btCancelar" class="btn btn-sm btn-default" title="Cancelar operaci&oacute;n"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
				</div>
			</div>
		</form>
		<script>
			// Categoría
			$('#a_id_categoria').val('<?php echo (isset($datos['a_id_categoria'])) ? $datos['a_id_categoria'] : 1; ?>');
			
			function validarArticulo()
			{
				var mensaje = '';
				var error = false;
				// Get form
		        var form = $('#formArticulo')[0];
		        // Se crea un objeto FormData
		        var data = new FormData(form);

		        if ( $('#a_id_categoria').val() == '0' ) {
                    mensaje += "Debe seleccionar una Categoria.<br>";
                    $('#a_id_categoria').focus();
                    error = true;
                }
				
				if ( $('#a_nombre').val() == '' ) {
					mensaje += "Debe ingresar un Nombre.\n";
					$('#a_nombre').focus();
					error = true;
				}
				
				if ( error )
					mostrarCartel(mensaje, 2);
				else {
					$.ajax({
		            	type: "POST",
		            	enctype: 'multipart/form-data',
			            url: $('#directorio').val()+"/index.php",
			            data: data,
			            processData: false,
			            contentType: false,
			            cache: false,
			            timeout: 600000,
			            success: function(data) {
			            	// Se muestra la respuesta
			                $("#contenidoAjaxPrincipal").html(data);
			            }
			        });
					// Se evita ejecutar el submit del formulario
			        return false;
		    	}
			}
		 	
			$('#btGuardar').click(function(){
				validarArticulo();
			});
			
			$('#btCancelar').click(function(){
				refrescar($('#directorio').val()+'/index.php?controlador='+$('#controlador').val()+'&accion=listar&pagina='+$('#pagina').val(), '#contenidoAjaxPrincipal');
			});
			
			// Se comienza editando la Categoría
			$('#a_id_categoria').focus();

			mostrarModal();
		</script>	
		<?php
	}
}
?>