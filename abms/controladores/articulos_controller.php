<?php
if ( !isset($_SESSION) ) 
    session_start(); 

//Incluye el modelo que corresponde
require_once("modelos/articulos.php");
//require_once("modelos/proveedores.php");
require_once("modelos/categorias.php");

//Incluye la vista que corresponde
require_once("vistas/articulos.php");

class articulos_controller extends ControladorBase
{
	private $directorio_destino;
	private $extensiones_validas;
	private $tamanio_maximo_foto;
	
	public function __construct()
	{
		parent::__construct();

		// Se crea una instancia del modelo
		$this->modelo            = new articulosModelo();
		//$this->modelo_proveedores = new proveedoresModel();
		$this->modelo_categorias = new categoriasModelo();
	
		// Se crea una instancia de la Vista
		$this->vista = new VistaArticulos();

		// Directorio donde se guardará el archivo
		$this->directorio_destino = '../fotos_articulos/';

		// Extensiones permitidas
		$this->extensiones_validas = array("jpeg", "jpg", "png");

		// Tamaño máximo permitido para la foto
		$this->tamanio_maximo_foto = 2097152;// 2 MB = 2*1024*1024;
	}

	public function guardarRegistroOriginal($original)
	{
		$_SESSION['a_id_original']              = $original['a_id'];
		$_SESSION['a_codigo_original']          = $original['a_codigo'];
		$_SESSION['a_nombre_original']          = $original['a_nombre'];
		$_SESSION['a_descripcion_original']     = $original['a_descripcion'];
		$_SESSION['a_foto_original']            = $original['a_foto'];
		$_SESSION['a_precio_compra_original']   = $original['a_precio_compra'];
		$_SESSION['a_precio_venta_original']    = $original['a_precio_venta'];
		$_SESSION['a_cantidad_original']        = $original['a_cantidad'];
		$_SESSION['a_cantidad_minima_original'] = $original['a_cantidad_minima'];
		$_SESSION['a_id_categoria_original']    = $original['a_id_categoria'];
		$_SESSION['a_id_proveedor_original']    = $original['a_id_proveedor'];
		$_SESSION['a_habilitado_original']      = $original['a_habilitado'];
	}
	
	/**
	 * Se listan los registros
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  string $p_pagina     [description]
	 */
	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = '')
	{
		$filtro = Array();
		
		// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
		if ( LibreriaGeneral::recoge('mensaje') ) {
			$mensaje = LibreriaGeneral::recoge('mensaje');
			$tipo_mensaje = LibreriaGeneral::recoge('tipo_mensaje');
		}
		
		// Se obtiene el valor de la pagina
		$filtro['pagina'] = ($p_pagina == '') ? LibreriaGeneral::recoge('pagina', 1) : $p_pagina;

		// se establece el valor a buscar en el modelo
		$filtro['valor_buscado'] = LibreriaGeneral::recoge('valor_buscado');
		
		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ( $campo_orden != '' )
			$filtro['campo_orden'] = $campo_orden;
		else {
			//por defecto
			$filtro['campo_orden'] = 'a_codigo';
			$_SESSION['ultimo_campo'] = '';
		}
		
		// Se filtra por Proveedor
		$filtro['f_id_proveedor'] = LibreriaGeneral::recoge('f_id_proveedor', 0);
		
		// Se filtra por Categoria
		$filtro['f_id_categoria'] = LibreriaGeneral::recoge('f_id_categoria', 0);
		
		// DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');
		
		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'] ) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		}
		else
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ( $_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '' ) ? 'desc' : 'asc';

		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;
					
		if ( $filtro['pagina'] == '' ) {
			// al comienzo no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina 
		}
		else
			// si no se busca
			if ( $filtro['valor_buscado'] == '' )
				// se calcula el valor del registro inicial de la pagina deseada
				$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
 
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior
		
		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
		
		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();
		
		//NUMERO TOTAL DE PAGINAS 
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);
 		
		//Se le pide al modelo todos los items
		$datos['info'] = $this->modelo->listar();

		// Se obtienen los proveedores habilitados
		//$datos['proveedores'] = $this->modelo_proveedores->listarHabilitados();

		// Se obtienen las categorias habilitadas
		$datos['categorias'] = $this->modelo_categorias->listarHabilitados();

		$this->vista->listar($datos, $mensaje, $tipo_mensaje, $filtro);
	}
	 
	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '')
	{	
		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			$id = LibreriaGeneral::recoge('id', 0);

			// SE OBTIENEN SUS DATOS
			$datos = $this->modelo->obtenerRegistro($id);
			
			// Si existe en la base de datos
			if ( $datos['a_id'] ) {
				// Se marca para saber que se encuentra en edición
				$this->modelo->marcarEnEdicion($datos['a_id']);

				// Se guarda el registro en sesión para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);
				
				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);
			
			} else
				// En caso de editarse un NUEVO registro
				$datos = null;
		} else
			$datos = $datos_formulario;// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
		
		// Se obtienen los proveedores habilitados
		//$datos['proveedores'] = $this->modelo_proveedores->listarHabilitados();

		// Se obtienen las categorias habilitadas
		$datos['categorias'] = $this->modelo_categorias->listarHabilitados();

		$this->vista->editar($datos, $mensaje, $tipo_mensaje);
	}
	
	/**
	 * Se ingresa un registro determinado
	 */
	public function insertar()
	{
		$datos = $_REQUEST;
		
		// Si no ha sido utilizado el nombre
		if ( !$this->modelo->existe($datos['a_codigo']) )
			if ( $this->modelo->insertar($datos) )
				// Se muestra la grilla con el mensaje al usuario
				$this->listar("El art&iacute;culo ".$datos['a_nombre']." se ingres&oacute; con &eacute;xito", 1);
			else
				// Se vuelve al formulario con el mensaje al usuario
				$this->editar($datos, "Error al ingresar el art&iacute;culo ".$datos['a_nombre'], 2);
		else
			// Se vuelve al formulario con el mensaje al usuario
			$this->editar($datos, "El c&oacute;digo ".$datos['a_codigo']." no se encuentra disponible, debe utilizar otro.", 2);
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificar()
	{
		$datos = $_REQUEST;
		
		if( $this->modelo->modificar($datos) ) {
			// Se desmarca para informar que ya se editó
			$this->modelo->desmarcarEnEdicion($datos['a_id']);
			// Se muestra la grilla con el mensaje al usuario
			$this->listar("El art&iacute;culo ".$datos['a_nombre']." se modific&oacute; con &eacute;xito", 1);
		} else
			// Se vuelve al formulario con el mensaje al usuario
			$this->editar($datos, "El art&iacute;culo ".$datos['a_nombre']." se ha ingresado previamente", 2);
	}

	/**
	 * Se guarda la información y una posible imagen.
	 */
	public function guardar()
	{
		$info_archivo = $_FILES;
		$datos = $_POST;
				
		// Si se recibió el archivo de la foto principal
		if ( $info_archivo["archivo"]["type"] != '' )
			// Se carga la foto princiapl
			$datos['a_foto'] = $this->cargarFotoPrincipal($datos, $info_archivo);
				
		// Si existe
		if ( $this->modelo->existe($datos['a_codigo']) ) {
			// 	Si no ha sido modificado previamente
			if ( $this->modelo->noLoModificoOtroUsuario() )
				// Si se modificó
				if ( $this->modelo->modificar($datos) ) {
					// Se desmarca para informar que ya se editó
					$this->modelo->desmarcarEnEdicion($datos['a_id']);
					// Se muestra la grilla
					$this->listar($this->mensaje_modificacion_ok, 1, $datos['pagina']);
		    	} else
		    		// sino, se vuelve al formulario, informando el error
		    		$this->editar($datos, $this->mensaje_modificacion_error, 2);
			else
				// sino, se vuelve al formulario, informando la modificación por otro usuario
				$this->editar($datos, $this->mensaje_modificacion_previa, 2);
		} else {
			// Se ingresa
			if ( $this->modelo->insertar($datos) )
				// Se muestra la grilla con un mensaje de éxito
				$this->listar($this->mensaje_ingreso_ok, 1, $datos['pagina']);
			else
				// Se muestra la grilla con un mensaje del error
				$this->listar($this->mensaje_ingreso_error, 2, $datos['pagina']);
		}
	}

	/**
	 * Se carga la foto Principal
	 * @param  [type] $datos        [description]
	 * @param  [type] $info_archivo [description]
	 */
	private function cargarFotoPrincipal($datos, $info_archivo)
	{
		// Se divide el nombre del archivo por cada punto
		$auxiliar = explode(".", $info_archivo["archivo"]["name"]);
		// Se toma la última parte del nombre (su extensión)
		$extension_archivo = end($auxiliar);

		// Si el tipo de archivo es png, jpg ó jpeg
		// y su extensión es válida
		if ( ( ($info_archivo["archivo"]["type"] == 'image/png') || 
			   ($info_archivo["archivo"]["type"] == 'image/jpg') || 
			   ($info_archivo["archivo"]["type"] == 'image/jpeg')
			 ) && in_array($extension_archivo, $this->extensiones_validas)
		   )
		{
			// Si su tamaño supera los 2MB
			if ( $info_archivo["archivo"]["size"] != '' && $info_archivo["archivo"]["size"] > $this->tamanio_maximo_foto ) {
				$error = true;
				// se vuelve al formulario, informando al usuario
				$this->editar($datos, "El tama&ntilde;o del archivo (".$info_archivo["archivo"]["size"].") <b>es mayor a 2 MB</b>.", 2);
			} else {
				// Si surgió un error al intentar cargar el archivo
				if ( $info_archivo["archivo"]["error"] > 0 ) {
					$error = true;
					// se vuelve al formulario, informando al usuario
					$this->editar($datos, "Error al cargar la foto: ".$info_archivo["archivo"]["error"], 2);
				} else {
					$nombre_archivo = $datos['a_id'].'.'.$extension_archivo;

					$ruta_origen = $info_archivo['archivo']['tmp_name'];

					$ruta_destino = $this->directorio_destino.$nombre_archivo;

					if ( ! move_uploaded_file($ruta_origen, $ruta_destino) ) {
						$error = true;
						// se vuelve al formulario, informando al usuario
						$this->editar($datos, "Error al subir el archivo.", 2);
					} else
						//$datos['n_foto'] = $nombre_archivo;
						return $nombre_archivo;
				}
			}
		} else {
			$error = true;
			// se vuelve al formulario, informando al usuario
			$this->editar($datos, "El archivo <b>no</b> es una imagen.", 2);
		}
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar()
	{	
		$id 	= LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		// Se obtiene el nombre de la foto a eliminar
		$nombre_foto = $this->modelo->obtenerNombreFoto($id);
		
		// Si existe la foto
		if ( is_file($this->directorio_destino.$nombre_foto) )
			// Se elimina del directorio respectivo
			unlink($this->directorio_destino.$nombre_foto);
		
		if ( $this->modelo->eliminar($id) )
			$this->listar($this->mensaje_eliminacion_ok, 1, $pagina);
		else
			$this->listar($this->mensaje_eliminacion_error, 2, $pagina);
		
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado()
	{
		parent::modificarEstado();
	}

}
?>