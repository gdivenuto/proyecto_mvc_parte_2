<?php
abstract class ControladorBase 
{ 
	protected $nombre_controlador;
	protected $modelo;
	protected $vista;
	protected $campo_orden_por_defecto;
	protected $rango_paginacion;

	// Mensajes de cada operación
	protected $mensaje_registro_existente;
	protected $mensaje_ingreso_ok;
	protected $mensaje_ingreso_error;
	protected $mensaje_modificacion_ok;
	protected $mensaje_modificacion_error;
	protected $mensaje_eliminacion_ok;
	protected $mensaje_eliminacion_error;
	protected $mensaje_modificacion_estado_ok;
	protected $mensaje_modificacion_estado_error;
	
	public function __construct()
    {
    	$this->rango_paginacion = 12;

    	$this->mensaje_registro_existente        = "El registro ya se encuentra registrado.";
		$this->mensaje_ingreso_ok                = "Se ha ingresado con &eacute;xito.";
		$this->mensaje_ingreso_error             = "No se ha ingresado.";
		$this->mensaje_modificacion_ok           = "Se ha modificado con &eacute;xito.";
		$this->mensaje_modificacion_error        = "No se ha modificado.";
		$this->mensaje_eliminacion_ok            = "Se ha eliminado con &eacute;xito.";
		$this->mensaje_eliminacion_error         = "No se ha eliminado.";
		$this->mensaje_modificacion_estado_ok    = "Se ha modificado el estado del registro con &eacute;xito.";
		$this->mensaje_modificacion_estado_error = "No se ha podido modificar el estado del registro.";
    }

	/**
	 * Se ingresa un registro determinado
	 */
	public function insertar()
	{
		// Se reciben los datos
		$datos = $_REQUEST;

		// Si ya existe el registro
		if ( $this->modelo->existe($datos) )
			$this->listar($this->mensaje_registro_existente, 2);
		elseif ( $this->modelo->insertar($datos) )
			// Se muestra la grilla
			$this->listar($this->mensaje_ingreso_ok, 1);
		else
			$this->listar($this->mensaje_ingreso_error, 2);
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificar()
	{
		// Se reciben los datos
		$datos = $_REQUEST;

		// Si se modificó
		if( $this->modelo->modificar($datos) )
			// Se muestra la grilla
			$this->listar($this->mensaje_modificacion_ok, 1);
    	else
    		// sino se sigue editando
    		$this->editar($datos, $this->mensaje_modificacion_error, 2);
	}
	
    /**
     * Se elimina un registro determinado
    */
	public function eliminar()
	{
		$id 	= LibreriaGeneral::recoge('id', 0);
		
		if ( $this->modelo->eliminar($id) )
			// Se muestra la grilla con el mensaje respectivo
			$this->listar($this->mensaje_eliminacion_ok, 1);
		else
			// Se muestra la grilla con el mensaje respectivo
			$this->listar($this->mensaje_eliminacion_error, 2);
	}
	
	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado()
	{
		$id         = LibreriaGeneral::recoge('id', 0);
		$habilitado = LibreriaGeneral::recoge('habilitado');
		
		// Se habilita|deshabilita
		if ( $this->modelo->modificarEstado($id, $habilitado) )
			$this->listar($this->mensaje_modificacion_estado_ok, 1);
		else
			$this->listar($this->mensaje_modificacion_estado_error, 2);
	}
}
?>