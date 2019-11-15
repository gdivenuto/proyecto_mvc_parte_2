<?php
// Clase abstracta utilizando la librería mysqli de PHP
abstract class ModeloBaseMySQLi
{
	private $conexion;
	private $servidor;
    private $usuario;
    private $password;
    private $base_datos;

    protected $tabla_categorias;
    protected $tabla_articulos;
	
    protected $filtro;
	
    public function __construct()
    {
		$this->servidor   = "localhost";
		$this->base_datos = "negocio";
		$this->usuario    = "root";
		$this->password   = "gabriel";
		
    	$this->filtro = "";

		// Nombre de las tablas de la DB
		$this->tabla_categorias = $this->base_datos.'.categorias';
		$this->tabla_articulos  = $this->base_datos.'.articulos';
	}

	/**
	 * Establece la conexión con la base de datos
	 * @return resource $this->conexion
	 */
	public function conectarDB()
	{
		// Se conecta a la base de datos
		$this->conexion = new mysqli($this->servidor, $this->usuario, $this->password, $this->base_datos);
		
		// Si surgió un error
		if ($this->conexion->connect_errno)
			throw new RuntimeException("Falló la conexión a MySQL:" . $this->conexion->connect_error);
				
		// Se establece la codificación utf-8
		$this->conexion->set_charset("utf8");
		
		return $this->conexion;	
	}

    /**
     * Se cierra la conexión
     * @param $pconexion
     */
    public function desconectarDB($pconexion)
    {
		$pconexion->close();	
	}
	
	/**
	 * Asigna un conjunto de valores a utilizar como filtro en las querys de cada Modelo
	 * @param array $filtro
	 */
    public function setFiltro($filtro)
    {
		$this->filtro = $filtro;
    }
    
    /**
     * Ejecuta una query determinada,
     * en caso de surgir un error se registra en un archivo de log de errores y lanza una excepción
     * @param string $query
     * @return resource $resultado
     */
    public function ejecutarQuery($query)
    {
		if ($query == null)
			return null;

		// Se ejecuta la query
		$resultado = $this->conexion->query($query);
		
		// Si surgió un error
		if ( !$resultado ) {
			// Se registra el error
			$this->registrarErrorSQL($this->conexion->errno, $this->conexion->error, $query);
			
			// Se lanza la excepción
			throw new RuntimeException("Error al ejecutar la query:".$this->conexion->error);
		}
		
		return $resultado;
	}	

	/**
	 * Se registra el error al ejecutar una consulta SQL determinada, en el directorio "sgl/log/"
	 * 
	 * @param integer $numero_error
	 * @param string $texto_error
	 * @param string $query_error
	 */
	public function registrarErrorSQL($numero_error, $texto_error, $query_error)
	{
		$info_del_error  = "#####################################################";
		$info_del_error .= "\n Usuario: ".$this->usuario;
		$info_del_error .= "\n Fecha y hora: ".date("d/m/Y H:i")." hs.";
		$info_del_error .= "\n #####################################################";
		$info_del_error .= "\n Error # ".$numero_error;
		$info_del_error .= "\n\n Mensaje del Error: ".$texto_error;
		$info_del_error .= "\n\n En la siguiente consulta SQL:\n\n";
		$info_del_error .= $query_error;
				
		fputs(fopen("error_al_ejecutar_query.txt", 'w'), print_r($info_del_error, true));
	}
	
    /**
     * Devuelve un array asociativo con la información obtenida de una query determinada,
     * en caso que la query no devuelva información retorna null
     * @param resource $resultado
     * @return NULL|array asociativo
     */
    public function obtenerCjtoRegistrosDB($resultado)
	{
		// Si no se recibió ningún resultado
		if ($resultado == null)
			return null;

		$datos = null;
		while ( $row = $resultado->fetch_assoc() ) {
			$datos[] = $row;
		}
		
		return $datos;
    }
    
	/**
	 * Se obtiene un array asociativo con el resultado de una query
	 * @param resource $resultado
	 * @return array Registro obtenido
	 */
	public function obtenerRegistroDB($resultado)
	{
		return ($resultado != null) ? $resultado->fetch_assoc() : null;
	}
	
    /**
     * Devuelve el último código o id de una tabla determinada
     * @param string $tabla
     * @param string $campo
     * @return integer código o id
     */
    public function obtenerUltimoCodigo($tabla, $campo)
	{    
		$conexion = $this->conectarDB();
		
		$query = "SELECT MAX(".$campo.") AS ultimo_codigo FROM ".$tabla;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerRegistroDB($resultado);
		
		$ultimo_codigo = ($dato['ultimo_codigo'] != null) ? $dato['ultimo_codigo'] : 0;
				
		$this->desconectarDB($conexion);
		
		return $ultimo_codigo;
    }
	
    /**
     * Se verifica si una fecha determinada es válida
     * @param string $fecha
     * @return boolean
     */
	public function esFechaValida($fecha)
	{
	    if ( $fecha != null && $fecha != '' ) {
		    $fecha_partes = explode("/",$fecha);
			$mes  = $fecha_partes[1];
			$dia  = $fecha_partes[0];
			$anio = $fecha_partes[2];
		    
		    // checkdate devuelve TRUE si la fecha dada es válida, si no, devuelve FALSE
		    return checkdate($mes, $dia, $anio);
	    } else
		    return false;
	}
    
    /**
     * Devuelve una fecha en formato año_completo-mes-dia para MySQL
     * @param string $fecha
     * @return null|string $fecha_mysql
     */
    public function formatearFechaMySQL($fecha)
	{	
		if ($fecha == null)
			return null;
		else {
			$fecha_partes = explode("/", $fecha);

			return $fecha_partes[2].'-'.$fecha_partes[1].'-'.$fecha_partes[0];
		}
    }
    
    /**
     * Se revisa un valor determinado para utilizar en una query, 
     * si no posee valor o es cero, le asigna null 
     * @param string $dato
     * @param string $valor_predeterminado
     * @return string Valor revisado
     */
	public function revisarValorAtributo($dato, $valor_predeterminado = "null")
	{
		return ( $dato != '' ) ? "'".strip_tags(trim($dato))."'" : $valor_predeterminado;
	}
	
	/**
	 * Revisa el valor de la fecha, si es válida se convierte al formato yyyy-mm-dd, sino devuelve null
	 * @param $fecha 	En formato dd/mm/yyyy
	 * @param $con_hora Valor booleano para determinar si se toma en cuenta la hora o no
	 * @return fecha en formato yyyy-mm-dd ó la cadena 'null' para utilizarla en una query
	 */
	public function revisarValorFechaAtributo($fecha, $con_hora = false)
	{
		// Si está definida la fecha y es válida
		if ( isset($fecha) && $this->esFechaValida($fecha) )
			return ($con_hora) ? "'".$this->formatearFechaMySQL($fecha).date(" H:i:s")."'" : "'".$this->formatearFechaMySQL($fecha)."'";
		else
			return "null";
	}
	
	/**
	 * Adapta la sintáxis de una línea en una query según el valor (Texto) de un campo determinado
	 * @param string $campo
	 * @return string Línea adaptada para la query
	 */
    public function adaptarValorStringParaFiltro($campo)
    {  
		$filtro = '';
		if ( $_SESSION[$campo.'_original'] != '' )
			$filtro = " AND ".$campo." = '".addslashes($_SESSION[$campo.'_original'])."'";
		elseif ( is_null($_SESSION[$campo.'_original']) )
			$filtro = " AND ".$campo." IS NULL";
		else
			$filtro = " AND ".$campo." = ''";
		
		return $filtro;
    }
	
    /**
     * Adapta la sintáxis de una línea en una query según el valor (Numérico) de un campo determinado
     * @param string $campo
     * @return string Línea adaptada para la query
     */
    public function adaptarValorNumericoParaFiltro($campo)
    {  
		$filtro = '';
		if ( $_SESSION[$campo.'_original'] != '' )
			$filtro = " AND ".$campo." = ".$_SESSION[''.$campo.'_original']."";
		else
			if ( is_null($_SESSION[$campo.'_original']) )
				$filtro = " AND ".$campo." IS NULL";
		
		return $filtro;
    }
    
}
?>