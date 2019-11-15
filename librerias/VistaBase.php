<?php
abstract class VistaBase 
{
	protected $nombre_proyecto;

    public function __construct()
    {
    	$this->nombre_proyecto = 'Cursos de Programaci&oacute;n';
    }
    
    /**
     * Devuelve el color de fondo y texto según un estado determinado
     * @param integer $estado
     * @return string $color_fondo_y_texto
     */
    public function mostrarColorEstado($estado)
    {
    	$color_fondo_y_texto = "";
    
    	switch ($estado)
    	{
    		case '1':
    			$color_fondo_y_texto = "background-color: #FCF8E3;color: #C09853;";// AMARILLO PASTEL
    			break;
    		case '2':
    			$color_fondo_y_texto = "background-color: #F2DEDE;color: #B94A48;";// ROJO PASTEL
    			break;
    		case '3':
    			$color_fondo_y_texto = "background-color: #DFF0D8;color: #468847;";// VERDE PASTEL
    			break;
    		case '4':
    			$color_fondo_y_texto = "background-color: #0C99D5;color: #FFFFFF;";// AZUL
    			break;
    	}
    
    	return $color_fondo_y_texto;
    }

}
?>