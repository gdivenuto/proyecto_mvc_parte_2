<?php
class articulosModelo extends ModeloBaseMySQLi
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
				
		$filtro = "";

		// Para filtrar por Proveedor
		if ( isset($this->filtro['f_id_proveedor']) && $this->filtro['f_id_proveedor'] != 0 )
			$filtro .= " AND a_id_proveedor = ".$this->filtro['f_id_proveedor'];
		
		// Para filtrar por Categoria
		if ( isset($this->filtro['f_id_categoria']) && $this->filtro['f_id_categoria'] != 0 )
			$filtro .= " AND a_id_categoria = ".$this->filtro['f_id_categoria'];
		
		// Para filtrar por Nombre o Descripción
		if ( isset($this->filtro['valor_buscado']) && $this->filtro['valor_buscado'] != '' ) {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['valor_buscado']);

			$filtro .= " AND (a_nombre LIKE '%".$criterio_ingresado."%' OR a_descripcion LIKE '%".$criterio_ingresado."%')";
		}

		// para limitar el listado
		$registro_inicial = ( isset($this->filtro['inicio']) && $this->filtro['inicio'] != '' ) ? $this->filtro['inicio'] : 0;
		
		$sql = "SELECT *, (SELECT c_nombre FROM ".$this->tabla_categorias." WHERE c_id = a_id_categoria) AS nombre_categoria
				FROM ".$this->tabla_articulos."
			    WHERE a_habilitado <> 3
			    ".$filtro."
			    ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
			    LIMIT ".$registro_inicial.", ".$this->filtro['rango']."
			   ";

		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->obtenerCjtoRegistrosDB($resultado);
		
		$this->desconectarDB($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		// PARA LA BUSQUEDA
		$filtro = "";

		// Para filtrar por Proveedor
		if ( isset($this->filtro['f_id_proveedor']) && $this->filtro['f_id_proveedor'] != 0 )
			$filtro .= " AND a_id_proveedor = ".$this->filtro['f_id_proveedor'];
		
		// Para filtrar por Categoria
		if ( isset($this->filtro['f_id_categoria']) && $this->filtro['f_id_categoria'] != 0 )
			$filtro .= " AND a_id_categoria = ".$this->filtro['f_id_categoria'];
		
		// Para filtrar por Nombre o Descripción
		if ( isset($this->filtro['valor_buscado']) && $this->filtro['valor_buscado'] != '' ) {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['valor_buscado']);

			$filtro .= " AND (a_nombre LIKE '%".$criterio_ingresado."%' OR a_descripcion LIKE '%".$criterio_ingresado."%')";
		}

		$query = "SELECT COUNT(a_id) AS cantidad
				  FROM ".$this->tabla_articulos."
				  WHERE a_habilitado <> 3
				  ".$filtro."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerRegistroDB($resultado);
		
		return $dato['cantidad'];
	}

	public function obtenerRegistro($id)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT * FROM ".$this->tabla_articulos." WHERE a_id = ".$id;
		  		  
		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerRegistroDB($resultado);
		
		return $registro;
	}
	
	/**
	 * Se obtiene el listado de habilitados
	 * 
	 * @return array $datos
	 */
	public function listarHabilitados()
	{
		$conexion = $this->conectar();

		$sql = "SELECT * 
				FROM ".$this->tabla_articulos." 
				WHERE a_habilitado = 1
				ORDER BY a_id
			   ";
		
		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->obtenerCjtoRegistrosDB($resultado);
		
		$this->desconectarDB($conexion);
		
		return $datos;
	}	
	
	// Se verifica la existencia de un código determinado
	public function existe($a_codigo)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT a_codigo FROM ".$this->tabla_articulos." WHERE a_codigo = '".$a_codigo."'";
				 
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerRegistroDB($resultado);
		
		return ($dato['a_codigo']);
	}	
	
	public function validarDatos($datos)
	{
		$datos['a_codigo']          = $this->revisarValorAtributo($datos['a_codigo']);
		
		$datos['a_nombre']          = $this->revisarValorAtributo($datos['a_nombre']);
		
		$datos['a_descripcion']     = $this->revisarValorAtributo($datos['a_descripcion']);
		
		$datos['a_foto']            = $this->revisarValorAtributo($datos['a_foto']);
		
		$datos['a_precio_compra']   = $this->revisarValorAtributo($datos['a_precio_compra'], '0.00');
		
		$datos['a_precio_venta']    = $this->revisarValorAtributo($datos['a_precio_venta'], '0.00');
		
		$datos['a_cantidad']        = $this->revisarValorAtributo($datos['a_cantidad'], 0);
		
		$datos['a_cantidad_minima'] = $this->revisarValorAtributo($datos['a_cantidad_minima'], 0);
		
		$datos['a_id_categoria']    = $this->revisarValorAtributo($datos['a_id_categoria'], 0);
		
		$datos['a_id_proveedor']    = $this->revisarValorAtributo($datos['a_id_proveedor'], 0);
		
		return $datos;
	}
	
	/**
	 * Se verifica si el registro no ha sido modificado por otro usuario
	 *
	 * @return boolean
	 */
	public function noLoModificoOtroUsuario()
	{	
		$filtro_a_codigo          = $this->adaptarValorStringParaFiltro('a_codigo');
		
		$filtro_a_nombre          = $this->adaptarValorStringParaFiltro('a_nombre');
		
		$filtro_a_descripcion     = $this->adaptarValorStringParaFiltro('a_descripcion');
		
		$filtro_a_foto            = $this->adaptarValorStringParaFiltro('a_foto');
		
		$filtro_a_precio_compra   = $this->adaptarValorNumericoParaFiltro('a_precio_compra');
		
		$filtro_a_precio_venta    = $this->adaptarValorNumericoParaFiltro('a_precio_venta');
		
		$filtro_a_cantidad        = $this->adaptarValorNumericoParaFiltro('a_cantidad');
		
		$filtro_a_cantidad_minima = $this->adaptarValorNumericoParaFiltro('a_cantidad_minima');
		
		$filtro_a_id_proveedor    = $this->adaptarValorNumericoParaFiltro('a_id_proveedor');
		
		$filtro_a_id_categoria    = $this->adaptarValorNumericoParaFiltro('a_id_categoria');

		$conexion = $this->conectar();
		
		$query = "SELECT a_id
				  FROM ".$this->tabla_articulos." 
				  WHERE a_id = ".$_SESSION['a_id_original']."
				  ".$filtro_a_codigo."
				  ".$filtro_a_nombre."
				  ".$filtro_a_descripcion."
				  ".$filtro_a_foto."
				  ".$filtro_a_precio_compra."
				  ".$filtro_a_precio_venta."
				  ".$filtro_a_cantidad."
				  ".$filtro_a_cantidad_minima."
				  ".$filtro_a_id_proveedor."
				  ".$filtro_a_id_categoria."
				  AND a_habilitado = ".$_SESSION['a_habilitado_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$datos = $this->obtenerRegistroDB($resultado);
		
		$this->desconectarDB($conexion);

		return ($datos['a_id']);		
	}	
	
	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 *
	 * @see ModelBase::obtenerUltimoCodigo()
	 */ 
	public function obtenerUltimoId()
	{
		return parent::obtenerUltimoCodigo($this->tabla_articulos, 'a_id');
	}

	public function insertar($datos) {
		// Primero se obtiene el siguiente Id
		$datos['a_id'] = $this->obtenerUltimoId() + 1;
		
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_articulos."(a_id, a_codigo, a_nombre, a_descripcion, a_foto, a_precio_compra, a_precio_venta, a_cantidad, a_cantidad_minima, a_id_proveedor, a_id_categoria, a_editando, a_habilitado)
				  VALUES(".$datos['a_id'].", 
				  		 ".$datos['a_codigo'].",
					     ".$datos['a_nombre'].",
					     ".$datos['a_descripcion'].",
					     ".$datos['a_foto'].",
						 ".$datos['a_precio_compra'].",
						 ".$datos['a_precio_venta'].",
						 ".$datos['a_cantidad'].",
						 ".$datos['a_cantidad_minima'].",
						 ".$datos['a_id_proveedor'].",
						 ".$datos['a_id_categoria'].",
						 '0',
						 '1'
					    )";
		
		if (!$this->ejecutarQuery($query))
			return false;
			
		$this->desconectarDB($conexion);
		
		return true;
	}

	public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_articulos."
				  SET a_codigo = ".$datos['a_codigo'].",
				  	  a_nombre = ".$datos['a_nombre'].",
				  	  a_descripcion = ".$datos['a_descripcion'].",
				  	  a_foto = ".$datos['a_foto'].",
					  a_precio_compra = ".$datos['a_precio_compra'].",
					  a_precio_venta = ".$datos['a_precio_venta'].",
					  a_cantidad = ".$datos['a_cantidad'].",
					  a_cantidad_minima = ".$datos['a_cantidad_minima'].",
					  a_id_proveedor = ".$datos['a_id_proveedor'].",
				      a_id_categoria = ".$datos['a_id_categoria']."
				  WHERE a_id = ".$datos['a_id'];

		if (!$this->ejecutarQuery($query))
			return false;
			
		$this->desconectarDB($conexion);
		
		return true;
	}

	/**
	 * Se obtiene el nombre de la foto
	 * @param  [type] $id [description]
	 * @return [type]           [description]
	 */
    public function obtenerNombreFoto($id)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT a_foto FROM ".$this->tabla_articulos." WHERE a_id = ".$id;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerRegistroDB($resultado);

		$this->desconectarDB($conexion);
		
		return $dato['a_foto'];
    }
	
	public function eliminar($id)
	{	
		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_articulos." WHERE a_id = ".$id;
		
		if (!$this->ejecutarQuery($query))
			return false;
			
		$this->desconectarDB($conexion);
		
		return true;	
	}
	
	/**
	 * Se modifica el estado habilitado|deshabilitado del Usuario
	 * 
	 * @param  integer $id
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($id, $habilitado)
	{
		$conexion = $this->conectar();
	
		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE ".$this->tabla_articulos." SET a_habilitado = ".$valor_habilitado." WHERE a_id = ".$id;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
	
		return true;
	}

	public function marcarEnEdicion($id) {
		$conexion = $this->conectar();
	
		$query = "UPDATE ".$this->tabla_articulos."
				  SET a_editando = '1'
				  WHERE a_id = ".$id;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
	
		return true;
	}

	public function desmarcarEnEdicion($id) {
		$conexion = $this->conectar();
	
		$query = "UPDATE ".$this->tabla_articulos."
				  SET a_editando = '0'
				  WHERE a_id = ".$id;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectarDB($conexion);
	
		return true;
	}

	public function estaEnEdicion($id) {
		$conexion = $this->conectar();
		
		$query = "SELECT a_id
				  FROM ".$this->tabla_articulos." 
				  WHERE a_editando = '1'
				  AND a_id = ".$id;
		
		$resultado = $this->ejecutarQuery($query);	
		
		$dato = $this->obtenerRegistroDB($resultado);

		// distinto de vacío = se encuentra en edición
		return ($dato['a_id'] != '');
	}
	
	/**
	 * Se audita la consulta de un registro determinado
	 * @param  [array] $registro Info del registro
	 */
	public function auditarConsultaRegistro($registro) {
		
		$modelo = new auditoriaModel();
		
		$datos_log = Array();
		$datos_log['au_operacion']     = "CONSULTA";
		$datos_log['au_tabla']         = $this->tabla_articulos;
		$datos_log['au_id_registro']   = $registro['a_id'];
		$datos_log['au_observaciones'] = addslashes("Se consulta el articulo ".$registro['a_nombre'].".");
		
		// SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);
	}
}
?>