<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Gestión de Notebooks</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	
	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- Font Awesome 6.0.0 -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
	<?php
		require_once("constantes.php");
		include_once("class/class.notebook.php");
		
		$cn = conectar();
		$notebook = new NOTEBOOK($cn);
		
		if (isset($_GET['d'])) {
			$dato = base64_decode($_GET['d']);
			$tmp = explode("/", $dato);
			$op = $tmp[0];
			$id = $tmp[1];
			
			if ($op == "del") {
				echo $notebook->delete_notebook($id);
			} elseif ($op == "det") {
				echo $notebook->get_detail_notebook($id);
			} elseif ($op == "new") {
				echo $notebook->get_form();
			} elseif ($op == "act") {
				echo $notebook->get_form($id);
			}
		
		} else {
			if (isset($_POST['Guardar']) && $_POST['op'] == "new") {
				$notebook->save_notebook();
			} elseif (isset($_POST['Guardar']) && $_POST['op'] == "update") {
				$notebook->update_notebook();
			} else {
				echo $notebook->get_list();
			}	
		}
		
	//*******************************************************
		function conectar() {
			$c = new mysqli(SERVER, USER, PASS, BD);
			
			if ($c->connect_errno) {
				die("Error de conexión: " . $c->connect_errno . ", " . $c->connect_error);
			}
			
			$c->set_charset("utf8");
			return $c;
		}
	//**********************************************************	
	?>	
</body>
</html>
