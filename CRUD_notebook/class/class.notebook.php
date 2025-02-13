<?php
class NOTEBOOK
{
    private $idnotebook;
    private $precio;
    private $foto;
    private $Color_idColor;
    private $Marca_idMarca;
    private $con;

    function __construct($cn)
    {
        $this->con = $cn;
    }

    // Método para actualizar una notebook en la base de datos
    public function update_notebook()
    {
        $this->idnotebook = $_POST['idnotebook'];
        $this->precio = $_POST['precio'];
        $this->foto = isset($_POST['foto']) ? $_POST['foto'] : null;
        $this->Color_idColor = $_POST['Color_idColor'];
        $this->Marca_idMarca = $_POST['Marca_idMarca'];

        $sql = "UPDATE notebook SET 
                    precio = '$this->precio',
                    foto = " . ($this->foto ? "'$this->foto'" : "NULL") . ",
                    Color_idColor = '$this->Color_idColor',
                    Marca_idMarca = '$this->Marca_idMarca'
                WHERE idnotebook = $this->idnotebook;";

        if ($this->con->query($sql)) {
            echo $this->_message_ok("modificó");
        } else {
            echo $this->_message_error("al modificar");
        }
    }

    // Métodos auxiliares para manejar mensajes de éxito o error
    private function _message_ok($accion)
    {
        return "<h3>Se ha $accion correctamente el registro.</h3>";
    }

    private function _message_error($accion)
    {
        return "<h3>Error $accion el registro.</h3>";
    }
}

	public function save_notebook()
	{
		$this->precio = $_POST['precio'];
		$this->Color_idColor = $_POST['Color_idColor'];
		$this->Marca_idMarca = $_POST['Marca_idMarca'];

		// Manejo de la imagen (foto)
		if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
			$this->foto = $this->_get_name_file($_FILES['foto']['name'], 12);
			$path = "../imagenes/notebooks/" . $this->foto;

			if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
				echo $this->_message_error("Cargar la imagen");
				exit;
			}
		} else {
			$this->foto = NULL;
		}

		$sql = "INSERT INTO notebook (precio, foto, Color_idColor, Marca_idMarca) 
				VALUES ('$this->precio', 
						" . ($this->foto ? "'$this->foto'" : "NULL") . ",
						'$this->Color_idColor', 
						'$this->Marca_idMarca')";

		if ($this->con->query($sql)) {
			echo $this->_message_ok("guardó");
		} else {
			echo $this->_message_error("guardar");
		}
	}

	// Método auxiliar para generar un nombre único para el archivo subido
	private function _get_name_file($original_name, $length)
	{
		$ext = pathinfo($original_name, PATHINFO_EXTENSION);
		return substr(md5(time() . $original_name), 0, $length) . "." . $ext;
	}

	// Métodos auxiliares para manejar mensajes de éxito o error
	private function _message_ok($accion)
	{
		return "<h3>Se ha $accion correctamente el registro.</h3>";
	}

	private function _message_error($accion)
	{
		return "<h3>Error al $accion el registro.</h3>";
	}

	//*********************** 3.3 METODO _get_name_File() **************************************************	
	// Método auxiliar para generar un nombre único para el archivo subido
	private function _get_name_file($nombre_original, $tamanio)
	{
		$tmp = explode(".", $nombre_original); // Dividir el nombre en partes por el punto
		$numElm = count($tmp); // Contar el número de elementos en el arreglo
		$ext = strtolower($tmp[$numElm - 1]); // Extraer la extensión en minúsculas

		$cadena = "";
		for ($i = 1; $i <= $tamanio; $i++) {
			$c = rand(65, 122); // Generar un número aleatorio entre A-Z y a-z
			if (($c >= 91) && ($c <= 96)) { // Evitar caracteres especiales
				$i--; // Si el número generado no es válido, repetir la iteración
			} else {
				$cadena .= chr($c); // Convertir el número a un carácter
			}
		}

		return $cadena . "." . $ext; // Devolver el nuevo nombre con su extensión original
	}


	//*************************************** PARTE I ************************************************************


	// Método para obtener un combo (select) con los colores disponibles en la BD
	private function _get_combo_colores($nombre, $defecto = null)
	{
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT idColor, descripcion FROM color;";
		$res = $this->con->query($sql);

		while ($row = $res->fetch_assoc()) {
			$selected = ($defecto == $row['idColor']) ? 'selected' : '';
			$html .= '<option value="' . $row['idColor'] . '" ' . $selected . '>' . $row['descripcion'] . '</option>' . "\n";
		}

		$html .= '</select>';
		return $html;
	}


		// Método para obtener un combo (select) con las marcas disponibles en la BD
	private function _get_combo_marcas($nombre, $defecto = null)
	{
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT idMarca, descripcion FROM marca;";
		$res = $this->con->query($sql);

		while ($row = $res->fetch_assoc()) {
			$selected = ($defecto == $row['idMarca']) ? 'selected' : '';
			$html .= '<option value="' . $row['idMarca'] . '" ' . $selected . '>' . $row['descripcion'] . '</option>' . "\n";
		}

		$html .= '</select>';
		return $html;
	}


	// Método para generar radio buttons con los colores disponibles en la BD
private function _get_radio_colores($nombre, $defecto = null)
{
    $html = '<table border="0" align="left">';

    $sql = "SELECT idColor, descripcion FROM color;";
    $res = $this->con->query($sql);

    while ($row = $res->fetch_assoc()) {
        $checked = ($defecto === null || $defecto == $row['idColor']) ? 'checked' : '';

        $html .= '
        <tr>
            <td>' . $row['descripcion'] . '</td>
            <td><input type="radio" value="' . $row['idColor'] . '" name="' . $nombre . '" ' . $checked . '/></td>
        </tr>';
    }

    $html .= '</table>';
    return $html;
}



	//************************************* PARTE II ****************************************************	

	public function get_form($id = NULL)
	{
		if ($id == NULL) {
			$this->precio = NULL;
			$this->foto = NULL;
			$this->Color_idColor = NULL;
			$this->Marca_idMarca = NULL;
	
			$flag = NULL;
			$op = "new";
		} else {
			$sql = "SELECT * FROM notebook WHERE idnotebook=$id;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
	
			if ($res->num_rows == 0) {
				$mensaje = "Intento de actualizar la notebook con id= " . $id;
				echo $this->_message_error($mensaje);
			} else {
				$this->precio = $row['precio'];
				$this->foto = $row['foto'];
				$this->Color_idColor = $row['Color_idColor'];
				$this->Marca_idMarca = $row['Marca_idMarca'];
	
				$flag = "disabled";
				$op = "update";
			}
		}
	
		$html = '
		<div class="container mt-5">
			<div class="card shadow-lg border-0 rounded-lg">
				<div class="card-header bg-dark text-white text-center py-3">
					<h3><i class="fas fa-laptop"></i> Registro de Notebook</h3>
				</div>
				<div class="card-body bg-light p-4">
					<form name="notebook" method="POST" action="index.php" enctype="multipart/form-data">
						<input type="hidden" name="idnotebook" value="' . $id . '">
						<input type="hidden" name="op" value="' . $op . '">
	
						<div class="mb-3">
							<label for="precio" class="form-label fw-bold"><i class="fas fa-dollar-sign me-2"></i>Precio</label>
							<input type="text" class="form-control" id="precio" name="precio" value="' . $this->precio . '" required>
						</div>
	
						<div class="mb-3">
							<label for="Color_idColor" class="form-label fw-bold"><i class="fas fa-palette me-2"></i>Color</label>
							' . $this->_get_combo_colores("Color_idColor", $this->Color_idColor) . '
						</div>
	
						<div class="mb-3">
							<label for="Marca_idMarca" class="form-label fw-bold"><i class="fas fa-tags me-2"></i>Marca</label>
							' . $this->_get_combo_marcas("Marca_idMarca", $this->Marca_idMarca) . '
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
					<h3><i class="fas fa-laptop"></i> Lista de Notebooks</h3>
				</div>
				<div class="card-body bg-light p-4">
					<div class="mb-3 text-center">
						<a href="index.php?d=' . $d_new_final . '" class="btn btn-success">
							<i class="fas fa-plus-circle"></i> Nueva
						</a>
						<a href="../" class="btn btn-secondary">
							<i class="fas fa-arrow-left"></i> Menú Principal
						</a>
					</div>
					<div class="table-responsive">
						<table class="table table-striped table-hover text-center">
							<thead class="table-dark">
								<tr>
									<th>Precio</th>
									<th>Color</th>
									<th>Marca</th>
									<th>Foto</th>
									<th colspan="3">Acciones</th>
								</tr>
							</thead>
							<tbody>
		';
	
		// Consulta para obtener las notebooks junto con los nombres de color y marca
		$sql = "SELECT n.idnotebook, n.precio, c.descripcion AS color, m.descripcion AS marca, n.foto 
				FROM notebook n
				JOIN color c ON n.Color_idColor = c.idColor
				JOIN marca m ON n.Marca_idMarca = m.idMarca;";
		
		$res = $this->con->query($sql);
		
		while ($row = $res->fetch_assoc()) {
			$d_del = "del/" . $row['idnotebook'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['idnotebook'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['idnotebook'];
			$d_det_final = base64_encode($d_det);
	
			$html .= '
			<tr>
				<td>$' . number_format($row['precio'], 2) . '</td>
				<td>' . $row['color'] . '</td>
				<td>' . $row['marca'] . '</td>
				<td>';
				
			// Mostrar la imagen si existe, o un placeholder si no
			if (!empty($row['foto'])) {
				$html .= '<img src="../../img' . $row['foto'] . '" alt="Notebook" width="50">';
			} else {
				$html .= '<i class="fas fa-image fa-2x text-muted"></i>';
			}
	
			$html .= '</td>
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
	


	public function get_detail_notebook($id)
	{
		$sql = "SELECT n.idnotebook, n.precio, c.descripcion AS color, m.descripcion AS marca, n.foto 
				FROM notebook n
				JOIN color c ON n.Color_idColor = c.idColor
				JOIN marca m ON n.Marca_idMarca = m.idMarca
				WHERE n.idnotebook = $id;";
		
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$num = $res->num_rows;
	
		if ($num == 0) {
			$mensaje = "Intento de ver detalles de la notebook con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$foto = !empty($row['foto']) 
				? '<img src="../../img' . $row['foto'] . '" class="img-fluid img-thumbnail" width="300px"/>'
				: '<i class="fas fa-image fa-5x text-muted"></i><br>Imagen no disponible';
	
			$html = '
		<div class="container mt-5">
			<div class="card shadow-lg border-0 rounded-lg">
				<div class="card-header bg-dark text-white text-center py-3">
					<h3><i class="fas fa-laptop"></i> Detalles de la Notebook</h3>
				</div>
				<div class="card-body bg-light p-4">
					<table class="table table-striped table-hover text-center">
						<tr>
							<td class="fw-bold"><i class="fas fa-dollar-sign"></i> Precio</td>
							<td>$' . number_format($row['precio'], 2) . '</td>
						</tr>
						<tr>
							<td class="fw-bold"><i class="fas fa-palette"></i> Color</td>
							<td>' . $row['color'] . '</td>
						</tr>
						<tr>
							<td class="fw-bold"><i class="fas fa-tags"></i> Marca</td>
							<td>' . $row['marca'] . '</td>
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
	


	public function delete_notebook($id)
	{
		// Verificar si la notebook existe antes de eliminar
		$sql_check = "SELECT foto FROM notebook WHERE idnotebook = $id;";
		$res_check = $this->con->query($sql_check);
		
		if ($res_check->num_rows == 0) {
			echo $this->_message_error("La notebook con ID $id no existe");
			return;
		}
	
		// Obtener la foto asociada antes de eliminar el registro
		$row = $res_check->fetch_assoc();
		$foto = $row['foto'];
	
		// Eliminar la notebook
		$sql_delete = "DELETE FROM notebook WHERE idnotebook = $id;";
		
		if ($this->con->query($sql_delete)) {
			// Si hay una imagen asociada, eliminarla del servidor
			if (!empty($foto) && file_exists("../imagenes/notebooks/" . $foto)) {
				unlink("../../img" . $foto);
			}
	
			echo $this->_message_ok("ELIMINÓ");
		} else {
			echo $this->_message_error("eliminar");
		}
	}

	//*************************************************************************


	//*************************************************************************	

		// Método para mostrar mensaje de error
	private function _message_error($tipo)
	{
		$html = '
		<div class="container mt-5">
			<div class="alert alert-danger text-center" role="alert">
				<h4 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Error al ' . strtolower($tipo) . '.</h4>
				<p>Ocurrió un problema durante la operación. Por favor, contacte al soporte técnico.</p>
				<hr>
				<a href="index.php" class="btn btn-danger">
					<i class="fas fa-arrow-left"></i> Regresar
				</a>
			</div>
		</div>';
		return $html;
	}

	// Método para mostrar mensaje de éxito
	private function _message_ok($tipo)
	{
		$html = '
		<div class="container mt-5">
			<div class="alert alert-success text-center" role="alert">
				<h4 class="alert-heading"><i class="fas fa-check-circle"></i> Operación exitosa</h4>
				<p>El registro se ' . strtolower($tipo) . ' correctamente.</p>
				<hr>
				<a href="index.php" class="btn btn-success">
					<i class="fas fa-arrow-left"></i> Regresar
				</a>
			</div>
		</div>';
		return $html;
	}
//****************************************************************************	
} // FIN SCRPIT
