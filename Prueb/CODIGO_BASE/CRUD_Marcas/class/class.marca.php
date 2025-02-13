<?php
class MARCA
{
	private $id;
	private $descripcion;
	private $pais;
	private $direccion;
	private $foto;
	private $con;

	function __construct($cn)
	{
		$this->con = $cn;
	}


	//*********************** 3.1 METODO update_vehiculo() **************************************************	

	public function update_vehiculo()
	{
		$this->id = $_POST['id'];
		$this->descripcion = $_POST['descripcion'];
		$this->pais = $_POST['pais'];
		$this->direccion = $_POST['direccion'];

		$sql = "UPDATE marca SET descripcion='$this->descripcion',
									pais='$this->pais',
									direccion='$this->direccion'
				WHERE id=$this->id;";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("modificó");
		} else {
			echo $this->_message_error("al modificar");
		}
	}


	//*********************** 3.2 METODO save_vehiculo() **************************************************	

	public function save_vehiculo()
	{


		$this->descripcion = $_POST['descripcion'];
		$this->pais = $_POST['pais'];
		$this->direccion = $_POST['direccion'];


		/*echo "<br> FILES <br>";
		echo "<pre>";
		print_r($_FILES);
		echo "</pre>";
		*/


		$this->foto = $this->_get_name_file($_FILES['foto']['name'], 12);

		$path = "../imagenes/sellos/" . $this->foto;

		//exit;
		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}

		$sql = "INSERT INTO marca VALUES(NULL,
											'$this->descripcion',
											'$this->pais',
											'$this->direccion',
											'$this->foto');";
		//echo $sql;
		//exit;
		if ($this->con->query($sql)) {
			echo $this->_message_ok("guardó");
		} else {
			echo $this->_message_error("guardar");
		}
	}


	//*********************** 3.3 METODO _get_name_File() **************************************************	

	private function _get_name_file($nombre_original, $tamanio)
	{
		$tmp = explode(".", $nombre_original); //Divido el nombre por el punto y guardo en un arreglo
		$numElm = count($tmp); //cuento el número de elemetos del arreglo
		$ext = $tmp[$numElm - 1]; //Extraer la última posición del arreglo.
		$cadena = "";
		for ($i = 1; $i <= $tamanio; $i++) {
			$c = rand(65, 122);
			if (($c >= 91) && ($c <= 96)) {
				$c = NULL;
				$i--;
			} else {
				$cadena .= chr($c);
			}
		}
		return $cadena . "." . $ext;
	}


	//*************************************** PARTE I ************************************************************


	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_db($tabla, $valor, $etiqueta, $nombre, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		while ($row = $res->fetch_assoc()) {
			//ImpResultQuery($row);
			$html .= ($defecto == $row[$valor]) ? '<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_anio($nombre, $anio_inicial, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$anio_actual = date('Y');
		for ($i = $anio_inicial; $i <= $anio_actual; $i++) {
			$html .= ($i == $defecto) ? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n" : '<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_radio($arreglo, $nombre, $defecto)
	{

		$html = '
		<table border=0 align="left">';

		//CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION

		foreach ($arreglo as $etiqueta) {
			$html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';

			if ($defecto == NULL) {
				// OPCION PARA GRABAR UN NUEVO VEHICULO (id=0)
				$html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>';
			} else {
				// OPCION PARA MODIFICAR UN VEHICULO EXISTENTE
				$html .= ($defecto == $etiqueta) ? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
			}

			$html .= '</tr>';
		}
		$html .= '
		</table>';
		return $html;
	}


	//************************************* PARTE II ****************************************************	

	public function get_form($id = NULL)
	{
		if ($id == NULL) {
			$this->descripcion = NULL;
			$this->pais = NULL;
			$this->direccion = NULL;
			$this->foto = NULL;
	
			$flag = NULL;
			$op = "new";
		} else {
			$sql = "SELECT * FROM marca WHERE id=$id;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
	
			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "Intento de actualizar el vehículo con id= " . $id;
				echo $this->_message_error($mensaje);
			} else {
				$this->descripcion = $row['descripcion'];
				$this->pais = $row['pais'];
				$this->direccion = $row['direccion'];
				$this->foto = $row['foto'];
	
				$flag = "disabled";
				$op = "update";
			}
		}
		
		$html = '
		<div class="container mt-5">
			<div class="card shadow-lg border-0 rounded-lg">
				<div class="card-header bg-dark text-white text-center py-3">
					<h3><i class="fas fa-car-side"></i> Registro de Vehículo</h3>
				</div>
				<div class="card-body bg-light p-4">
					<form name="vehiculo" method="POST" action="index.php" enctype="multipart/form-data">
						<input type="hidden" name="id" value="' . $id . '">
						<input type="hidden" name="op" value="' . $op . '">
						
						<div class="mb-3">
							<label for="descripcion" class="form-label fw-bold"><i class="fas fa-id-badge me-2"></i>Placa</label>
							<input type="text" class="form-control" id="descripcion" name="descripcion" value="' . $this->descripcion . '" required>
						</div>
						<div class="mb-3">
							<label for="pais" class="form-label fw-bold"><i class="fas fa-cogs me-2"></i>Motor</label>
							<input type="text" class="form-control" id="pais" name="pais" value="' . $this->pais . '" required>
						</div>
						<div class="mb-3">
							<label for="direccion" class="form-label fw-bold"><i class="fas fa-car me-2"></i>Chasis</label>
							<input type="text" class="form-control" id="direccion" name="direccion" value="' . $this->direccion . '" required>
						</div>
						<div class="mb-3">
							<label for="foto" class="form-label fw-bold"><i class="fas fa-camera me-2"></i>Foto</label>
							<input type="file" class="form-control" id="foto" name="foto" ' . $flag . '>
						</div>
						<div class="text-center">
							<button type="submit" name="Guardar" class="btn btn-success w-100">
								<i class="fas fa-save"></i> Guardar
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<!-- Bootstrap 5 CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
		
		<!-- Font Awesome 6.0.0 -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		';
		
		return $html;
	}

	public function get_list()
	{
		$d_new = "new/0";
		$d_new_final = base64_encode($d_new);
		$d_menu = "menu";
		$d_menu_final = base64_encode($d_menu);
		$html = '
		<div class="container mt-5">
			<div class="card shadow-lg border-0 rounded-lg">
				<div class="card-header bg-dark text-white text-center py-3">
					<h3><i class="fas fa-list-alt"></i> Lista de Marcas</h3>
				</div>
				<div class="card-body bg-light p-4">
					<div class="mb-3 text-center">
						<a href="index.php?d=' . $d_new_final . '" class="btn btn-success">
							<i class="fas fa-plus-circle"></i> Nuevo
						</a>
						<a href="../" class="btn btn-secondary">
							<i class="fas fa-arrow-left"></i> Menú Principal
						</a>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover text-center">
							<thead class="table-dark">
								<tr>
									<th>Descripción</th>
									<th>País</th>
									<th>Dirección</th>
									<th colspan="3">Acciones</th>
								</tr>
							</thead>
							<tbody>
		';
		
		$sql = "SELECT m.id, m.descripcion, m.pais, m.direccion, m.foto FROM marca m;";
		$res = $this->con->query($sql);
		while ($row = $res->fetch_assoc()) {
			$d_del = "del/" . $row['id'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['id'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['id'];
			$d_det_final = base64_encode($d_det);
			$html .= '
			<tr>
				<td>' . $row['descripcion'] . '</td>
				<td>' . $row['pais'] . '</td>
				<td>' . $row['direccion'] . '</td>
				<td>
					<a href="index.php?d=' . $d_del_final . '" class="btn btn-danger btn-sm rounded-pill">
						<i class="fas fa-trash-alt"></i> Borrar
					</a>
				</td>
				<td>
					<a href="index.php?d=' . $d_act_final . '" class="btn btn-warning btn-sm rounded-pill">
						<i class="fas fa-edit"></i> Actualizar
					</a>
				</td>
				<td>
					<a href="index.php?d=' . $d_det_final . '" class="btn btn-info btn-sm rounded-pill">
						<i class="fas fa-eye"></i> Detalle
					</a>
				</td>
			</tr>';
		}
		$html .= '</tbody></table></div></div></div>';
		return $html;
	}
	


	public function get_detail_marca($id)
	{
		$sql = "SELECT * FROM marca WHERE id=$id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$num = $res->num_rows;
	
		if ($num == 0) {
			$mensaje = "tratar de editar la marca con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$foto = !empty($row['foto']) ? '<img src="../imagenes/sellos/' . $row['foto'] . '" class="img-fluid img-thumbnail" width="300px"/>' : 'No disponible';
			$html = '
		<div class="container mt-5">
			<div class="card shadow-lg border-0 rounded-lg">
				<div class="card-header bg-dark text-white text-center py-3">
					<h3><i class="fas fa-car-alt"></i> Datos de la Marca</h3>
				</div>
				<div class="card-body bg-light p-4">
					<table class="table table-striped table-hover text-center">
						<tr>
							<td class="fw-bold"><i class="fas fa-id-badge"></i> Placa</td>
							<td>' . $row['descripcion'] . '</td>
						</tr>
						<tr>
							<td class="fw-bold"><i class="fas fa-tag"></i> Marca</td>
							<td>' . $row['pais'] . '</td>
						</tr>
						<tr>
							<td class="fw-bold"><i class="fas fa-cogs"></i> Motor</td>
							<td>' . $row['direccion'] . '</td>
						</tr>
						<tr>
							<td colspan="2">' . $foto . '</td>
						</tr>
					</table>
				</div>
				<div class="card-footer text-center">
					<a href="index.php" class="btn btn-info">
						<i class="fas fa-arrow-left"></i> Regresar
					</a>
				</div>
			</div>
		</div>';
			return $html;
		}
	}


	public function delete_marca($id)
	{
		$sql = "DELETE FROM marca WHERE id=$id;";
		if ($this->con->query($sql)) {
			echo $this->_message_ok("ELIMINÓ");
		} else {
			echo $this->_message_error("eliminar");
		}
	}

	//*************************************************************************


	//*************************************************************************	

	private function _message_error($tipo)
	{
		$html = '
		<div class="container mt-5">
			<div class="alert alert-danger text-center" role="alert">
				<h4 class="alert-heading">Error al ' . $tipo . '.</h4>
				<p>Favor contactar a soporte técnico.</p>
				<hr>
				<a href="index.php" class="btn btn-danger"><i class="fas fa-arrow-left"></i> Regresar</a>
			</div>
		</div>';
		return $html;
	}
	
	private function _message_ok($tipo)
	{
		$html = '
		<div class="container mt-5">
			<div class="alert alert-success text-center" role="alert">
				<h4 class="alert-heading">Éxito</h4>
				<p>El registro se ' . $tipo . ' correctamente.</p>
				<hr>
				<a href="index.php" class="btn btn-success"><i class="fas fa-arrow-left"></i> Regresar</a>
			</div>
		</div>';
		return $html;
	}
	

	//****************************************************************************	

} // FIN SCRPIT
