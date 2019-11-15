<?php
/* Clase con funcionalidad general para el proyecto
***************************************************/
class LibreriaGeneral
{
	private static $instancia;
	
	/**
	 * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
	 * valores desde cualquier script.
	 */
	public static function ObtenerInstancia()
	{
		// Si la instancia no esta definida la creo, sino devuelvo la existente
		if (!isset(self::$instancia))
		{
			$claseActual = __CLASS__;			// Obtengo la clase actual
			self::$instancia = new $claseActual; // Creo una instancia
		}
	
		// Devuelvo la instancia existente.
		return self::$instancia;
	}
	
	/**
	 * Es invocado cuando se clona un instancia.
	 * Con este método podemos emitir un mensaje de error y
	 * proceder a detener la ejecución del script por operación inválida
	 * al intentar clonar una instancia de Singleton
	 *
	 * E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
	 */
	public function __clone()
	{
		trigger_error("Operación Inválida: No se puede clonar una instancia de ". get_class($this) .".", E_USER_ERROR );
	}
	
	/**
	 * __sleep es invocado cuando un objeto es serializado
	 * se evita serializar una instancia de Singleton
	 */
	public function __sleep()
	{
		trigger_error("No se puede serializar una instancia de ". get_class($this) .".");
	}
	
	/**
	* __wakeup es invocado cuando un objeto es deserializado
	* se evita deserializar una instancia de Singleton
	*/
	public function __wakeup()
	{
		trigger_error("No se puede deserializar una instancia de ". get_class($this) .".");
	}
	
	/**
	 * Recibe un valor determinado, se eliminan las etiquetas HTML y PHP (con strip_tags), 
	 * se reemplaza algunos caracteres por su equivalente en HTML
	 * 
	 * @param string|integer|array $valor_recibido
	 * @param string $valor_por_defecto
	 * @return string|integer|array
	 */
	public static function recoge($valor_recibido, $valor_por_defecto = '')
	{	
		$valor_a_devolver = ( isset($_REQUEST[$valor_recibido]) && ($_REQUEST[$valor_recibido] != '') ) ? trim(strip_tags($_REQUEST[$valor_recibido])) : trim(strip_tags($valor_por_defecto));
		
		// Si estan habilitadas las comillas magicas
		// característica ELIMINADA a partir de PHP 5.4.0
	    if (get_magic_quotes_gpc())
			// Se eliminan las barras invertidas de $valor_a_devolver
	        $valor_a_devolver = stripslashes($valor_a_devolver);
	    
	    $valor_a_devolver = str_replace('&', '&amp;',  $valor_a_devolver);
	    $valor_a_devolver = str_replace('"', '&quot;', $valor_a_devolver);
		
	    return $valor_a_devolver;
	}
	
	/**
	 * Para recortar un texto según un límite determinado
	 * @param string $string
	 * @param integer $charlimit
	 * @return string, cadena recortada
	 */
	public static function recortarTexto($string, $charlimit)
	{
	    if (substr($string, $charlimit-1, 1) != ' ') {
			$string = substr($string, 0, $charlimit);
			$array = explode(' ',$string);
			array_pop($array);
			$new_string = implode(' ',$array);
			
			return $new_string.' ...';
	    } else
			return substr($string, 0, $charlimit-1).' ...';
	} 
	
	/**
	 * Para convertir los saltos de linea y las tabulaciones en su respectiva etiqueta u operador html
	 * @param string $textohtml
	 * @return mixed
	 */
	public static function convertir_salto_linea($textohtml) 
	{
	    $textohtml = str_replace("\n", "<br>", $textohtml);
	    $textohtml = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $textohtml);
	
	    return $textohtml;
	} 
	
	/**
	 * Elimina los acentos en una cadena determinada
	 * @param string $cadena
	 * @return string
	 */
	public static function eliminarAcentos($cadena)
	{
		$a_buscar = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
		$reemplazo = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
		
		return utf8_encode(strtr(utf8_decode($cadena), utf8_decode($a_buscar), $reemplazo));
	}
	
	/**
	 * Reemplaza las vocales acentuadas por su mayúscula respectiva
	 * @param string $cadena
	 * @return string $cadena, la cadena con vocales reemplazadas
	 */
	public static function reemplazarPorMayusculaAcentuada($cadena) 
	{ 
		$cadena = str_replace('á','Á',$cadena);
		$cadena = str_replace('é','É',$cadena);
		$cadena = str_replace('í','Í',$cadena);
		$cadena = str_replace('ó','Ó',$cadena);
		$cadena = str_replace('ú','Ú',$cadena);
		$cadena = str_replace('ñ','Ñ',$cadena);
		
		return $cadena; 
	}

	/**
	 * Convierte una cadena a mayúsculas, incluyendo los acentos y la eñe
	 * @param  string $cadena Cadena a convertir
	 * @return string         Cadena convertida
	 */
	public static function aMayuscula($cadena) 
	{ 
		return self::reemplazarPorMayusculaAcentuada(strtoupper($cadena));
	}

	/**
	 * Serializa una colección de datos
	 * @param Array $coleccion
	 * @return string, devuelve los datos serializados en una cadena
	 */
	public static function serializarColeccion($coleccion)
	{
		return base64_encode(json_encode($coleccion));
	}
	
	/**
	 * Deserializa una cadena de datos
	 * @param string $cadena_serializada
	 * @return mixed
	 */
	public static function deserializarColeccion($cadena_serializada)
	{
		return json_decode(base64_decode($cadena_serializada), true);
	}
	
	/**
	 * Se guarda en un archivo txt el contenido de un elemento determinado
	 * @param string $nombre_archivo
	 * @param mixed $elemento_a_verificar
	 */
	public static function registrarLog($identificador, $elemento_a_verificar)
	{
		fputs(fopen($identificador.".txt", 'w'), print_r($elemento_a_verificar, true));
	}
	
	/**
	 * Se eliminan los espacios vacíos en cualquier posición de la cadena
	 * @param string $cadena
	 * @return string cadena sin espacios vacíos
	 */
	public static function eliminarEspacios($cadena)
	{
		return str_replace(' ','',$cadena);
	}
	
	public static function eliminarComillaSimple($cadena)
	{
		return str_replace("'", "", $cadena);
	}
	
	/**
	 * Se elimina un directorio determinado,
	 * previamente se elimina su contenido recursivamente
	 * @param string $directorio
	 */
	public static function eliminarDirectorio($directorio)
	{
		// Si se puede abrir el directorio respectivo
		if ( $dir_abierto = @opendir($directorio) )
		{
			// Mientras encuentre un archivo
			while ( false !== ( $archivo = readdir($dir_abierto) ) )
			{
				// Se descartan . y ..
				if ( $archivo != '..' && $archivo != '.' )
					// Se elimina el archivo
					if ( !@unlink($directorio.'/'.$archivo) )
						self::eliminarDirectorio($directorio.'/'.$archivo);
			}
			// Se cierra el directorio
			closedir($dir_abierto);
	
			// Se elimina el directorio, el cual ya se encuentra vacío
			@rmdir($directorio);
		}
	}

	/**
	 * Devuelve el número del día que le corresponde en la semana
	 * @param  integer $anio [description]
	 * @param  integer $mes  [description]
	 * @param  integer $dia  [description]
	 * @return integer       Número del día que le corresponde en la semana
	 */
	public static function obtenerNumeroDia($anio,$mes,$dia)
	{
	    return date("w",mktime(0, 0, 0, $mes, $dia, $anio));
	}

	/**
	 * Devuelve el nombre del día en la semana
	 * @param  string $fecha 		Fecha en formato yyyy-mm-dd
	 * @return string $nombre_dia   Nombre del día
	 */
	public static function obtenerNombreDia($fecha)
	{
		// Nombres de días de la semana (0 = domingo, 6 = sabado)
		$nombres_dias = array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
		
		// Se separa la fecha por su guión
		$partes = explode('-', $fecha);
		
		$anio = $partes[0];
		$mes = $partes[1];
		$dia = $partes[2];
		
		// Se obtiene el número del día en la semana
		$numero_dia_en_semana = self::obtenerNumeroDia($anio, $mes, $dia);
		
		// Se obtiene el nombre del día, según su número en la semana
		$nombre_dia = $nombres_dias[$numero_dia_en_semana];
		
		return $nombre_dia;
	}

	public static function mostrarNombreNumeroDiaActual()
	{
		return self::obtenerNombreDia(date("Y-m-d")).' '.date("d");
	}

	public static function obtenerNombreMes($numero_mes)
	{
		// Nombres de Meses
		$nombres_meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

		// Devuelve el nombre del Mes
		return $nombres_meses[$numero_mes-1];
	}

	public static function mostrarAnioConMiles($fecha)
	{
		return number_format(date("Y"), 0, '', '.');
	}

	public static function mostrarFechaActualConLetras()
	{
		return self::mostrarNombreNumeroDiaActual().' de '.self::obtenerNombreMes(date("m")).' de '.date("Y");
	}

	public static function obtenerFechaConLetras($fecha)
	{
		// Se separa la fecha por su guión
		$partes = explode('-', $fecha);
		$anio = $partes[0];
		$mes  = $partes[1];
		$dia  = $partes[2];

		$nombre_dia = self::obtenerNombreDia($fecha);
		$nombre_mes = self::obtenerNombreMes($mes);

		return $nombre_dia.'&nbsp;'.$dia.' de '.$nombre_mes.' de '.$anio;
	}

	public static function comprobarValorNumerico($numero)
	{
	    return (preg_match('/^[1-9][0-9]*$/', $numero));
	}
	
	public static function seEncuentra($coleccion, $pnombre_campo, $pvalor_buscado)
	{
		foreach ($coleccion as $nro => $contenido) {
			foreach ($contenido as $clave => $valor_a_comparar) {
				// Si la clave es justamente el nombre del campo por el cual buscar 
				if ($clave == $pnombre_campo)
					// Si se encontró el valor buscado
					if ($valor_a_comparar == $pvalor_buscado)
						return true;
			}
		}
		return false;
	}

	// Fecha en formato: dd de nombre_mes de yyyy
	public static function formatearFechaGregoriano($fecha)
	{
		// Se separan las partes de la fecha
		$partes = explode('-', $fecha);
		
		$anio = $partes[0];
		$mes = $partes[1];
		$dia = $partes[2];
		
		// Se obtiene el número del día en la semana
		$nombre_mes = self::obtenerNombreMes($mes);
		
		return $dia." de ".$nombre_mes." de ".number_format($anio, 0, '', '.');
	}

	// Se le da el formato dia/mes/anio completo
	public static function formatearFecha($fecha)
	{
		if ($fecha) {
			if ( $fecha != '0000-00-00' ) {
				$fec_partes  = explode("-",$fecha);
				$fecha_a_ver = $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0];

				return $fecha_a_ver;
			} else
				return '';
		} else
			return '';
	}

	public static function mostrarVideoYoutube($url, $ancho = 170, $alto = 205)
	{
		parse_str( parse_url( $url, PHP_URL_QUERY ) );

		$id_video = !empty( $v ) ? $v : $url;

		return '<iframe width="'.$ancho.'" height="'.$alto.'" src="https://www.youtube.com/embed/'.$id_video.'" frameborder="0" allowfullscreen></iframe>';
	}

}
?>
