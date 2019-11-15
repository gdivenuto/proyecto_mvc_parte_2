<?php
class categoriasModelo extends ModeloBaseMySQLi
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function conectar()
	{
		return parent::conectarDB();
	}

	public function listar()
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT * FROM $this->tabla_categorias";

		$resultado = $this->ejecutarQuery($query);
		
		$datos = $this->obtenerCjtoRegistrosDB($resultado);
		
		$this->desconectarDB($conexion);
		
		return $datos;
	}

	public function obtenerRegistro($pid)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT * FROM $this->tabla_categorias WHERE c_id = $pid";
		  		  
		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerRegistroDB($resultado);
		
		$this->desconectarDB($conexion);
		
		return $registro;
	}
	
	// Se verifica la existencia de un nombre determinado
	public function existe($pnombre)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT c_nombre FROM $this->tabla_categorias WHERE c_nombre = '$pnombre'";
				 
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerRegistroDB($resultado);
		
		$this->desconectarDB($conexion);
		
		// Si existe el nombre o no
		return ( $dato[$this->nombre] != '' );
	}	
	
	public function validarDatos($datos)
	{
		$datos['c_nombre'] = $this->revisarValorAtributo($datos['c_nombre']);

		$datos['c_observaciones'] = $this->revisarValorAtributo($datos['c_observaciones']);
		
		return $datos;
	}
	
	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 *
	 * @see ModelBase::obtenerUltimoCodigo()
	 */ 
	public function obtenerUltimoId()
	{
		return parent::obtenerUltimoCodigo($this->tabla_categorias, 'c_id');
	}

	/**
	 * Se ingresa un registro
	 * 
	 * @param  [array] $datos Conjunto de datos del registro
	 * @return [boolean]      true|false
	 */
	public function insertar($datos)
	{
		// Primero se obtiene el siguiente Id
		$datos[$this->id] = $this->obtenerUltimoId() + 1;
		
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_categorias." (c_id, c_nombre, c_observaciones, c_habilitado)
				  VALUES(".$datos['c_id'].",
					     ".$datos['c_nombre'].",
					     ".$datos['c_observaciones'].",
					     '1'
					    )";
		
		if (!$this->ejecutarQuery($query))
			return false;
		
		$this->desconectarDB($conexion);
		
		return true;
	}

	/**
	 * Se modifica un registro
	 * 
	 * @param  [array] $datos Conjunto de datos del registro
	 * @return [boolean]      true|false
	 */
	public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_categorias."
				  SET c_nombre = ".$datos['c_nombre'].",
					  c_observaciones = ".$datos['c_bservaciones']."
				  WHERE c_id = ".$datos['c_id'];
		
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
		
		return true;	
	}

	/**
	 * Se elimina un registro
	 * 
	 * @param  [integer] $id Identifcador del registro
	 * @return [boolean]     true|false
	 */
	public function eliminar($id)
	{	
		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_categorias." WHERE c_id = ".$id;
		
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
		
		return true;	
	}

	/**
	 * Se modifica el estado habilitado|deshabilitado del registro
	 * 
	 * @param  integer $id 			Identifcador del registro
	 * @param  integer $habilitado  Valor actual
	 * @return boolean 				true|false
	 */
	public function modificarEstado($id, $habilitado)
	{
		$conexion = $this->conectar();
	
		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE ".$this->tabla_categorias."
				  SET c_habilitado = ".$valor_habilitado."
				  WHERE c_id = ".$id;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
	
		return true;
	}

	public function marcarEnEdicion($id)
	{
		$conexion = $this->conectar();
	
		$query = "UPDATE ".$this->tabla_categorias." SET c_editando = '1' WHERE c_id = ".$id;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
	
		return true;
	}

	public function desmarcarEnEdicion($id)
	{
		$conexion = $this->conectar();
	
		$query = "UPDATE ".$this->tabla_categorias." SET c_editando = '0' WHERE c_id = ".$id;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
	
		return true;
	}

	public function estaEnEdicion($id)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT c_id FROM ".$this->tabla_categorias." WHERE c_editando = '1' AND c_id = ".$id;
		
		$resultado = $this->ejecutarQuery($query);	
		
		$dato = $this->obtenerFila($resultado);

		$this->desconectarDB($conexion);
	
		// Si es distinto de vacío: se está editando
		return ($dato['c_id'] != '');
	}
	
	public function listarHabilitados()
	{	
		$conexion = $this->conectar();
						
		$sql = "SELECT * FROM ".$this->tabla_categorias." WHERE c_habilitado = 1 ORDER BY c_nombre";
			
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->obtenerCjtoRegistrosDB($resultado);
		
		$this->desconectarDB($conexion);
		
		return $datos;
	}
	
}