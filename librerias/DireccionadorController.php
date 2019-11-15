<?php
if ( !isset($_SESSION) )
	session_start();

class DireccionadorController
{
	static function direccionar()
	{
		// Se incluye lo necesario para el proyecto
		// ----------------------------------------
		
		// Clase con métodos estáticos para utilizar en todo el proyecto
		require_once("LibreriaGeneral.php");
		
		// Clase base de los Modelos para trabajar con MySQLi
		require_once("ModeloBaseMySQLi.php");
		
		// Clase base de los Controladores
		require_once("ControladorBase.php"); 
		
		// Clase base de las Vistas
		require_once("VistaBase.php");
		
		// Se obtiene una instancia de LibreriaGeneral para utilizar métodos generales en todo el proyecto
		LibreriaGeneral::ObtenerInstancia();

		// Se procesa la redirección
		// --------------------------
		
		// Se recibe y completa el nombre del Controlador
		$nombre_controlador = LibreriaGeneral::recoge('controlador').'_controller';
		
		// Se recibe el nombre de la accion
		$nombre_accion = LibreriaGeneral::recoge('accion');
		
		// Ruta del archivo del controlador
		$ruta_controlador = 'controladores/'.$nombre_controlador.'.php';

		// Si existe la definicion de la clase controladora solicitada
		if ( is_file($ruta_controlador) )
			// Se incluye la definicion de la clase controladora solicitada
			require $ruta_controlador;
		else
			throw new Exception("El controlador ".$nombre_controlador." no existe.");
		
		// Si no existe la clase que se busca y su acción
		if ( ! is_callable(array($nombre_controlador, $nombre_accion)) )
			throw new Exception($nombre_controlador."->".$nombre_accion." no existe");
		
		// Se crea una instancia del controlador
		$controlador = new $nombre_controlador();
		
		// y se llama a la accion correspondiente
		$controlador->$nombre_accion();
	}
}
?>
