<?php
// Se incluye el modelo de Categorias
require_once("modelos/categorias.php");

// Se incluye la vista de Categorias
require_once("vistas/categorias.php");

class categorias_controller extends ControladorBase
{
	public function __construct()
	{
		parent::__construct();

		// Se crea una instancia del modelo
		$this->modelo = new categoriasModelo();
	
		// Se crea una instancia de la Vista
		$this->vista = new VistaCategorias();
	}
	
	public function listar($mensaje = '', $tipo_mensaje = '')
	{
		// Se obtienen el listado de registros
		$listado = $this->modelo->listar();

		$this->vista->listar($listado, $mensaje, $tipo_mensaje);
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '')
	{
		// SI NO SE VIENE DEL FORMULARIO DE EDICION POR UN ERROR
		if ($datos_formulario === null)	{
			$id = LibreriaGeneral::recoge('id', 0);
			
			// SE OBTIENEN SUS DATOS
			$datos = $this->modelo->obtenerRegistro($id);
			
			// Si existe en la base de datos
			if ( $datos['c_id'] )
				// Se marca para saber que se encuentra en edición
				$this->modelo->marcarEnEdicion($datos['c_id']);
			else
				// En caso de editarse un NUEVO registro
				$datos = null;
		}
		else
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		
		// SE MUESTRA EL FORMULARIO DE EDICION
		$this->vista->editar($datos, $mensaje, $tipo_mensaje);
	}

	public function insertar()
	{
		$datos = $_REQUEST;
		
		// Si no ha sido utilizado el nombre
		if ( !$this->modelo->existe($datos['c_nombre']) )
			if ( $this->modelo->insertar($datos) )
				// Se muestra la grilla con el mensaje al usuario
				$this->listar("La Categor&iacute;a ".$datos['c_nombre']." se ingres&oacute; con &eacute;xito", 1);
			else
				// Se vuelve al formulario con el mensaje al usuario
				$this->editar($datos, "Error al ingresar la Categor&iacute;a ".$datos['c_nombre'], 2);
		else
			// Se vuelve al formulario con el mensaje al usuario
			$this->editar($datos, "El nombre ".$datos['c_nombre']." no se encuentra disponible, debe utilizar otro.", 2);
	}

	public function modificar()
	{
		$datos = $_REQUEST;
		
		if( $this->modelo->modificar($datos) ) {
			// Se desmarca para informar que ya se editó
			$this->modelo->desmarcarEnEdicion($datos['c_id']);
			// Se muestra la grilla con el mensaje al usuario
			$this->listar("La Categor&iacute;a ".$datos['c_nombre']." se modific&oacute; con &eacute;xito", 1);
		} else
			// Se vuelve al formulario con el mensaje al usuario
			$this->editar($datos, "La Categor&iacute;a ".$datos['c_nombre']." se ha ingresado previamente", 2);
	}
	
	public function eliminar()
	{
		$id = LibreriaGeneral::recoge('id', 0);
		
		if ( $this->modelo->eliminar($id) )
			$this->listar("La Categor&iacute;a se elimin&oacute; con &eacute;xito", 1);
		else
			$this->listar("Error al eliminar la Categor&iacute;a", 2);
	}

	public function modificarEstado()
	{
		parent::modificarEstado();
	}

}
?>