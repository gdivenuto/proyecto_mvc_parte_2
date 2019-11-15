<?php
class VistaCategorias extends VistaBase
{
    private $controlador;
    private $formulario;

    public function __construct()
    {
        parent::__construct();
        
        $this->controlador = 'categorias';
        $this->formulario  = 'formCategoria';
    }

    public function listar($datos, $mensaje = '', $tipo_mensaje = '')
    {
        $cantidad = count($datos);
        ?>
        <input type="hidden" id="mensaje" name="mensaje" value="<?php echo $mensaje; ?>">
        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?php echo $tipo_mensaje; ?>">
        <input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>">

        <div class="row">
            <div class="col-md-12 titulo_entidad">Listado de Categor&iacute;as</div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button type="button" id="btNuevo" class="btn btn-primary btn-sm" title="Nueva Categor&iacute;a">
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;Nueva Categor&iacute;a
                </button>
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
                                <th class="ancho_columna_60">Nro.</th>
                                <th>Nombre</th>
                                <th>Observaciones</th>
                                <th class="text-center ancho_columna_90">Habilitada&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i=0; $i < $cantidad; $i++) {
                                $dato = &$datos[$i];
                            ?>
                                <tr <?php echo ($dato['c_habilitado'] == '0') ? ' class="text-muted"' : ''; ?> > 
                                    <td width="16">
                                        <a style="width:21px;height:16px;display:block;" title="Editar registro" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=editar&id=<?php echo $dato['c_id']; ?>', '#contenidoAjaxPrincipal');">
                                            <span class="glyphicon glyphicon-pencil"></span>
                                        </a>
                                    </td>
                                    <td width="16">
                                        <a title="Eliminar registro" href="javascript:if(confirm('¿Desea eliminar el registro?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=eliminar&id=<?php echo $dato['c_id']; ?>', '#contenidoAjaxPrincipal');};">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </td>
                                    <td class="text-right ancho_columna_60"><?php echo $dato['c_id']; ?></td>
                                    <td><?php echo $dato['c_nombre']; ?></td>
                                    <td><?php echo ($dato['c_observaciones']) ? $dato['c_observaciones'] : '&nbsp;'; ?></td>
                                    <td class="text-center ancho_columna_90">
                                        <?php 
                                        if ( $dato['c_habilitado'] == '1') {
                                        ?>
                                            <a title="Deshabilitar registro" href="javascript:if(confirm('¿Desea deshabilitar el registro?')){refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=modificarEstado&id=<?php echo $dato['c_id']; ?>&habilitado=<?php echo $dato['c_habilitado']; ?>', '#contenidoAjaxPrincipal');};">
                                                <span class="glyphicon glyphicon-ok"></span>
                                            </a>
                                        <?php
                                        } else {
                                        ?>
                                            <a title="Habilitar registro" href="javascript:refrescar('abms/index.php?controlador=<?php echo $this->controlador; ?>&accion=modificarEstado&id=<?php echo $dato['c_id']; ?>&habilitado=<?php echo $dato['c_habilitado']; ?>', '#contenidoAjaxPrincipal');">
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
        
        <script type="text/javascript">
            $('#btNuevo').click( function() {
                refrescar('abms/index.php?controlador='+$('#controlador').val()+'&accion=editar', '#contenidoAjaxPrincipal');
            });
            // Si hay un mensaje se muestra en una modal
            mostrarModal();
        </script>
        <?php
    }
    
    public function editar($datos = null, $mensaje = '', $tipo_mensaje = '')
    {
        $titulo_operacion   = ( isset($datos['c_id']) ) ? 'Edici&oacute;n' : 'Alta';
        $operacion          = ( isset($datos['c_id']) ) ? 'modificar' : 'insertar';
        $id_definido        = ( isset($datos['c_id']) ) ? $datos['c_id'] : '';
        $nombre             = ( isset($datos['c_nombre']) ) ? $datos['c_nombre'] : '';
        ?>
        <input type="hidden" id="mensaje" name="mensaje" value="<?php echo $mensaje; ?>">
        <input type="hidden" id="tipo_mensaje" name="tipo_mensaje" value="<?php echo $tipo_mensaje; ?>">

        <div class="row">
            <div class="col-md-12 titulo_entidad"><?php echo $titulo_operacion; ?> de la Categor&iacute;a</div>
        </div>
        <div>
            <form class="form-horizontal" role="form" action="abms/index.php" method="post" name="<?php echo $this->formulario; ?>" id="<?php echo $this->formulario; ?>">
                
                <input type="hidden" id="nombre_formulario" name="nombre_formulario" value="#<?php echo $this->formulario; ?>" />

                <input type="hidden" id="controlador" name="controlador" value="<?php echo $this->controlador; ?>" />
                
                <input type="hidden" id="accion" name="accion" value="<?php echo $operacion; ?>" />
                    
                <input type="hidden" id="c_habilitado" name="c_habilitado" value="<?php echo $datos['c_habilitado']; ?>" />

                <input type="hidden" id="c_id" name="c_id" value="<?php echo $id_definido; ?>" />
            
                <input type="hidden" id="c_nombre_actual" name="c_nombre_actual" value="<?php echo $nombre; ?>" />

                <div class="form-group">
                    <label for="c_nombre" class="col-lg-2 control-label">Nombre</label>
                    <div class="col-lg-4">
                        <input type="text" id="c_nombre" name="c_nombre" value="<?php echo $nombre; ?>" class="form-control" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="c_observaciones" class="col-lg-2 control-label">Observaciones</label>
                    <div class="col-lg-4">
                        <textarea id="c_observaciones" name="c_observaciones" class="form-control" rows="3"><?php echo ($datos['c_observaciones'] != '') ? $datos['c_observaciones'] : ''; ?></textarea>
                     </div>
                </div>
                <div class="row">
                    <div class="col-md-9 col-md-offset-2">
                        <!-- Botón Guardar -->
                        <button type="button" id="btGuardar" class="btn btn-primary btn-sm" title="Guardar informaci&oacute;n"><span class="glyphicon glyphicon-ok"></span>&nbsp;Guardar</button>
                        <!-- Botón Cancelar -->
                        <button type="button" id="btCancelar" class="btn btn-default btn-sm" title="Cancelar operaci&oacute;n"><span class="glyphicon glyphicon-chevron-left"></span>&nbsp;Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
        <script>
            var nombre_formulario = $('#nombre_formulario').val();

            function validarCategoria() {
                // Si no se ha ingresado un nombre
                if ( $('#c_nombre').val() == '' ) {
                    mostrarCartel("Debe ingresar el nombre de la Categor"+i_acentuada+"a.", 2);
                    $('#c_nombre').focus();
                }
                else {
                    $.ajax({
                        type: "POST",
                        url: "abms/index.php", // El script a dónde se realizará la petición
                        data: $(nombre_formulario).serialize(), // Adjuntar los campos del formulario enviado
                        success: function(data){
                            $("#contenidoAjaxPrincipal").html(data); // Mostrar la respuesta del script PHP
                        }
                    });

                    return false; // Evitar ejecutar el submit del formulario
                }
            }

            $('#btGuardar').click(function(){
                validarCategoria();
            });
            
            $('#btCancelar').click(function(){
                refrescar('abms/index.php?controlador='+$('#controlador').val()+'&accion=listar', '#contenidoAjaxPrincipal');
            });

            // Para tabular con la tecla Enter
            $('#c_nombre').keypress( function(e) {
                // Sólo si se ingresó un valor
                if ( $('#c_nombre').val() != '' && e.which == 13 )
                    $('#c_observaciones').focus();
            });
           
            $(nombre_formulario).on('submit', function(evt){
                // Para evitar ejecutar el submit del formulario
                evt.preventDefault();
                // triggerHandler() no ejecuta el evento keypress inmediatamente, sólo cuando se ejecuta en el campo respectivo
                $('#c_nombre').triggerHandler('keypress');
            });
            
            // Se comienza editando el nombre
            $('#c_nombre').focus();

            // Si hay un mensaje se muestra en una modal
            mostrarModal();
        </script>
        <?php
    }

}
?>