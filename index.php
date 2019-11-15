<!DOCTYPE html>
<html lang="es">
  	<head>
	    <meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	    <title>Proyecto MVC</title>
	    <link rel="icon" type="image/png" href="imagenes/logo_50x49.png" sizes="32x32">
	    
		<!--  jQuery v1.12.4 -->
	    <script src="js/jquery.js"></script>
	    <!-- JavaScript original de Bootstrap v3 -->
	    <script src="librerias/bootstrap/js/bootstrap.min.js"></script>
	    <!-- CSS original de Bootstrap v3 -->
	    <link href="librerias/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	    
	    <!-- CSS propio del proyecto -->
	    <link href="css/propio.css" rel="stylesheet">
	    
	    <!-- JS propio del proyecto -->
	    <script src="js/propio.js"></script>
  	</head>
  	<body>
	    <div class="container-fluid">
	    	<!-- Menú de navegación -->
			<div class="row">
				<div id="contenedor_menu_superior" class="col-md-12 col-lg-12">
					<div class="row">
						<nav class="navbar navbar-default menu_fondo" role="navigation">
							<!-- El logotipo y el icono que despliega el menú se agrupan para mostrarlos mejor en los dispositivos móviles -->
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
								  	<span class="sr-only"></span>
								  	<span class="icon-bar"></span>
								  	<span class="icon-bar"></span>
								  	<span class="icon-bar"></span>
								</button>
								<a href="#" class="navbar-brand">
									<img src="imagenes/logo_50x49.png" class="img-responsive">
								</a>
							</div>
							<!-- Agrupar los enlaces de navegación, los formularios y cualquier otro elemento que se pueda ocultar al minimizar la barra -->
							<div class="collapse navbar-collapse navbar-ex1-collapse padding_top_20">
							    <!-- Menú Superior -->
							    <ul class="nav navbar-nav">
								    <li class="dropdown">
							      		<li><a class="menu_item" href="javascript:refrescar('abms/index.php?controlador=categorias&accion=listar', '#contenidoAjaxPrincipal');">CATEGOR&Iacute;AS</a></li>
							      	</li>
									<li class="dropdown">
							      		<li><a class="menu_item" href="javascript:refrescar('abms/index.php?controlador=articulos&accion=listar', '#contenidoAjaxPrincipal');">ART&Iacute;CULOS</a></li>
							      	</li>
								</ul>
							</div>
						</nav>
					</div>
				</div>
			</div>
			<!-- Contenido que se refrezca utilizando Ajax -->
			<div class="row">
				<div id="contenidoAjaxPrincipal" class="col-sm-12 col-md-12">
				</div>
			</div>
		</div>

	    <!-- Modal -->
		<div id="modal_general" class="modal fade">
		 	<div class="modal-dialog">   
		    	<div class="modal-content">
		    		<div class="modal-body">
			            <p id="mensaje_en_modal"></p>
			        </div>
			        <div class="modal-footer">
			            <a href="#" data-dismiss="modal" class="btn"><span class="glyphicon glyphicon-remove"></span>&nbsp;Cerrar</a>
			        </div>
			    </div>
		 	</div>
		</div>
      	<a id="muestra_modal" href="#modal_general" data-toggle="modal" style="display:none"></a>

	    <script>
	        $(document).ready(function(){
	        	// Aquí con Ajax se ejecuta la acción del controlador respectivo
	            refrescar('abms/index.php?controlador=articulos&accion=listar', '#contenidoAjaxPrincipal');
	        });
	    </script>
  	</body>
</html>
