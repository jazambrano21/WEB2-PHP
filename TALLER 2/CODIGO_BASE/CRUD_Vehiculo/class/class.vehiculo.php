<?php
class vehiculo{
	private $id;
	private $placa;
	private $marca;
	private $motor;
	private $chasis;
	private $combustible;
	private $anio;
	private $color;
	private $foto;
	private $avaluo;
	private $con;
	
	function __construct($cn){
		$this->con = $cn;
	}
		
		
//*********************** 3.1 METODO update_vehiculo() **************************************************	
	
	public function update_vehiculo(){
		$this->id = $_POST['id'];
		$this->placa = $_POST['placa'];
		$this->motor = $_POST['motor'];
		$this->chasis = $_POST['chasis'];
			
		$this->marca = $_POST['marcaCMB'];
		$this->anio = $_POST['anio'];
		$this->color = $_POST['colorCMB'];
		$this->combustible = $_POST['combustibleRBT'];
		
		
		
		$sql = "UPDATE vehiculo SET placa='$this->placa',
									marca=$this->marca,
									motor='$this->motor',
									chasis='$this->chasis',
									combustible='$this->combustible',
									anio='$this->anio',
									color=$this->color
				WHERE id=$this->id;";
		//echo $sql;
		//exit;
		if($this->con->query($sql)){
			echo $this->_message_ok("modificó");
		}else{
			echo $this->_message_error("al modificar");
		}								
										
	}
	

//*********************** 3.2 METODO save_vehiculo() **************************************************	

	public function save_vehiculo(){
		
		
		$this->placa = $_POST['placa'];
		$this->motor = $_POST['motor'];
		$this->chasis = $_POST['chasis'];
		$this->avaluo = $_POST['avaluo'];

		
		$this->marca = $_POST['marcaCMB'];
		$this->anio = $_POST['anio'];
		$this->color = $_POST['colorCMB'];
		$this->combustible = $_POST['combustibleRBT'];
		
		 
				echo "<br> FILES <br>";
				echo "<pre>";
					print_r($_FILES);
				echo "</pre>";
		     
		
		
		$this->foto = $this->_get_name_file($_FILES['foto']['name'],12);
		
		$path = "../imagenes/autos/" . $this->foto;
		
		//exit;
		if(!move_uploaded_file($_FILES['foto']['tmp_name'],$path)){
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}
		
		$sql = "INSERT INTO vehiculo VALUES(NULL,
											'$this->placa',
											$this->marca,
											'$this->motor',
											'$this->chasis',
											'$this->combustible',
											'$this->anio',
											$this->color,
											'$this->foto',
											$this->avaluo);";
		//echo $sql;
		//exit;
		if($this->con->query($sql)){
			echo $this->_message_ok("guardó");
		}else{
			echo $this->_message_error("guardar");
		}								
										
	}


//*********************** 3.3 METODO _get_name_File() **************************************************	
	
	private function _get_name_file($nombre_original, $tamanio){
		$tmp = explode(".",$nombre_original); //Divido el nombre por el punto y guardo en un arreglo
		$numElm = count($tmp); //cuento el número de elemetos del arreglo
		$ext = $tmp[$numElm-1]; //Extraer la última posición del arreglo.
		$cadena = "";
			for($i=1;$i<=$tamanio;$i++){
				$c = rand(65,122);
				if(($c >= 91) && ($c <=96)){
					$c = NULL;
					 $i--;
				 }else{
					$cadena .= chr($c);
				}
			}
		return $cadena . "." . $ext;
	}
	
	
//*************************************** PARTE I ************************************************************
	
	    
	 /*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_db($tabla,$valor,$etiqueta,$nombre,$defecto){
		$html = '<select name="' . $nombre . '">';
		$sql = "SELECT $valor,$etiqueta FROM $tabla;";
		$res = $this->con->query($sql);
		while($row = $res->fetch_assoc()){
			//ImpResultQuery($row);
			$html .= ($defecto == $row[$valor])?'<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' . "\n" : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}
	
	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_anio($nombre,$anio_inicial,$defecto){
		$html = '<select name="' . $nombre . '">';
		$anio_actual = date('Y');
		for($i=$anio_inicial;$i<=$anio_actual;$i++){
			$html .= ($i == $defecto)? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n":'<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}
	
	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_radio($arreglo,$nombre,$defecto){
		
		$html = '
		<table border=0 align="left">';
		
		//CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION
		
		foreach($arreglo as $etiqueta){
			$html .= '
			<tr>
				<td>' . $etiqueta . '</td>
				<td>';
				
				if($defecto == NULL){
					// OPCION PARA GRABAR UN NUEVO VEHICULO (id=0)
					$html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>';
				
				}else{
					// OPCION PARA MODIFICAR UN VEHICULO EXISTENTE
					$html .= ($defecto == $etiqueta)? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '"/></td>';
				}
			
			$html .= '</tr>';
		}
		$html .= '
		</table>';
		return $html;
	}
	
	
//************************************* PARTE II ****************************************************	

public function get_form($id=NULL){
    
    if($id == NULL){
        $this->placa = NULL;
        $this->marca = NULL;
        $this->motor = NULL;
        $this->chasis = NULL;
        $this->combustible = NULL;
        $this->anio = NULL;
        $this->color = NULL;
        $this->foto = NULL;
        $this->avaluo = NULL;
        
        $flag = NULL;
        $op = "new";
        
    } else {
        $sql = "SELECT * FROM vehiculo WHERE id=$id;";
        $res = $this->con->query($sql);
        $row = $res->fetch_assoc();
        
        $num = $res->num_rows;
        if($num == 0){
            $mensaje = "tratar de actualizar el vehículo con id= ".$id;
            echo $this->_message_error($mensaje);
        } else {   
            $this->placa = $row['placa'];
            $this->marca = $row['marca'];
            $this->motor = $row['motor'];
            $this->chasis = $row['chasis'];
            $this->combustible = $row['combustible'];
            $this->anio = $row['anio'];
            $this->color = $row['color'];
            $this->foto = $row['foto'];
            $this->avaluo = $row['avaluo'];
            
            $flag = "disabled";
            $op = "update";
        }
    }
    
    $combustibles = ["Gasolina", "Diesel", "Eléctrico"];

    $html = '
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card border-0 shadow-lg p-4 rounded-4" style="max-width: 800px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
            <div class="card-header text-center bg-dark text-white rounded-3">
                <h3 class="fw-bold mb-0"><i class="bi bi-car-front-fill"></i> Información del Vehículo</h3>
            </div>
            <div class="card-body">
                <form name="vehiculo" method="POST" action="index.php" enctype="multipart/form-data">
                    
                    <input type="hidden" name="id" value="' . $id . '">
                    <input type="hidden" name="op" value="' . $op . '">

                    <div class="row g-3">
                        <!-- Placa -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-key-fill"></i> Placa:</label>
                            <input type="text" class="form-control border-0 shadow-sm" name="placa" value="' . $this->placa . '" required>
                        </div>
                        
                        <!-- Marca -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-badge-ad-fill"></i> Marca:</label>
                            ' . $this->_get_combo_db("marca", "id", "descripcion", "marcaCMB", $this->marca) . '
                        </div>

                        <!-- Motor -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-gear-fill"></i> Motor:</label>
                            <input type="text" class="form-control border-0 shadow-sm" name="motor" value="' . $this->motor . '" required>
                        </div>

                        <!-- Chasis -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-box-fill"></i> Chasis:</label>
                            <input type="text" class="form-control border-0 shadow-sm" name="chasis" value="' . $this->chasis . '" required>
                        </div>

                        <!-- Combustible -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-fuel-pump-fill"></i> Combustible:</label>
                            <div class="form-check">
                                ' . $this->_get_radio($combustibles, "combustibleRBT", $this->combustible) . '
                            </div>
                        </div>

                        <!-- Año -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-calendar-event-fill"></i> Año:</label>
                            ' . $this->_get_combo_anio("anio", 1980, $this->anio) . '
                        </div>

                        <!-- Color -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-palette-fill"></i> Color:</label>
                            ' . $this->_get_combo_db("color", "id", "descripcion", "colorCMB", $this->color) . '
                        </div>

                        <!-- Foto -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-camera-fill"></i> Foto:</label>
                            <input type="file" class="form-control border-0 shadow-sm" name="foto" ' . $flag . '>
                        </div>

                        <!-- Avalúo -->
                        <div class="col-md-12">
                            <label class="form-label"><i class="bi bi-cash-stack"></i> Avalúo ($):</label>
                            <input type="number" class="form-control border-0 shadow-sm" name="avaluo" value="' . $this->avaluo . '" ' . $flag . ' required>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg shadow-sm px-4">
                            <i class="bi bi-save-fill"></i> Guardar
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>';

    return $html;
}




	public function get_list(){
		$d_new = "new/0";
		$d_new_final = base64_encode($d_new);
		$html = '
		<div class="container mt-5">
			<div class="text-center mb-4">
				<h2 class="fw-bold text-uppercase text-primary">Lista de Vehículos</h2>
			</div>
			<div class="text-end mb-3 text-center">
				<a href="index.php?d=' . $d_new_final . '" class="btn btn-primary btn-lg shadow">
					<i class="bi bi-plus-circle"></i> Agregar Vehículo
				</a>
			</div>
			<div class="table-responsive">
				<table class="table table-hover table-bordered shadow rounded-4 overflow-hidden">
					<thead class="table-dark text-center">
						<tr>
							<th>Placa</th>
							<th>Marca</th>
							<th>Color</th>
							<th>Año</th>
							<th>Avalúo</th>
							<th colspan="3">Acciones</th>
						</tr>
					</thead>
					<tbody>';
	
		$sql = "SELECT v.id, v.placa, m.descripcion as marca, c.descripcion as color, v.anio, v.avaluo  
				FROM vehiculo v, color c, marca m 
				WHERE v.marca=m.id AND v.color=c.id;";    
		$res = $this->con->query($sql);
	
		while($row = $res->fetch_assoc()){
			$d_del = "del/" . $row['id'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['id'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['id'];
			$d_det_final = base64_encode($d_det);                    
	
			$html .= '
				<tr class="text-center align-middle">
					<td class="fw-semibold">' . $row['placa'] . '</td>
					<td>' . $row['marca'] . '</td>
					<td>' . $row['color'] . '</td>
					<td>' . $row['anio'] . '</td>
					<td>$' . number_format($row['avaluo'], 2) . '</td>
					<td>
						<a href="index.php?d=' . $d_del_final . '" class="btn btn-danger btn-sm shadow-sm" data-bs-toggle="tooltip" title="Eliminar">
							<i class="bi bi-trash"></i>
						</a>
					</td>
					<td>
						<a href="index.php?d=' . $d_act_final . '" class="btn btn-warning btn-sm shadow-sm text-white" data-bs-toggle="tooltip" title="Editar">
							<i class="bi bi-pencil-square"></i>
						</a>
					</td>
					<td>
						<a href="index.php?d=' . $d_det_final . '" class="btn btn-info btn-sm shadow-sm text-white" data-bs-toggle="tooltip" title="Ver detalles">
							<i class="bi bi-eye"></i>
						</a>
					</td>
				</tr>';
		}
	
		$html .= '  
					</tbody>
				</table>
			</div>
		</div>
	
		<script>
			// Activar tooltips de Bootstrap
			document.addEventListener("DOMContentLoaded", function() {
				var tooltipTriggerList = [].slice.call(document.querySelectorAll("[data-bs-toggle=\'tooltip\']"));
				var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
					return new bootstrap.Tooltip(tooltipTriggerEl);
				});
			});
		</script>';
	
		return $html;
	}	
	
	public function get_detail_vehiculo($id){
		$sql = "SELECT v.placa, m.descripcion as marca, v.motor, v.chasis, v.combustible, v.anio, c.descripcion as color, v.foto, v.avaluo  
				FROM vehiculo v, color c, marca m 
				WHERE v.id=$id AND v.marca=m.id AND v.color=c.id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		
		$num = $res->num_rows;
	
		if($num==0){
			$mensaje = "tratar de editar el vehiculo con id= ".$id;
			echo $this->_message_error($mensaje);
		} else { 
			$html = '
			<div class="container mt-5">
				<div class="text-center mb-4">
					<h2>Detalles del Vehículo</h2>
				</div>
				<div class="row justify-content-center">
					<div class="col-md-8">
						<div class="card shadow-lg">
							<div class="card-header bg-primary text-white">
								<h5>Información del Vehículo</h5>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-6">
										<p><strong>Placa:</strong> ' . $row['placa'] . '</p>
										<p><strong>Marca:</strong> ' . $row['marca'] . '</p>
										<p><strong>Motor:</strong> ' . $row['motor'] . '</p>
										<p><strong>Chasis:</strong> ' . $row['chasis'] . '</p>
										<p><strong>Combustible:</strong> ' . $row['combustible'] . '</p>
										<p><strong>Año:</strong> ' . $row['anio'] . '</p>
										<p><strong>Color:</strong> ' . $row['color'] . '</p>
									</div>
									<div class="col-md-6">
										<p><strong>Avalúo:</strong> <span class="text-success">$' . $row['avaluo'] . ' USD</span></p>
										<p><strong>Valor Matrícula:</strong> <span class="text-success">$' . $this->_calculo_matricula($row['avaluo']) . ' USD</span></p>
										<img src="../imagenes/autos/' . $row['foto'] . '" class="img-fluid rounded shadow" alt="Foto del vehículo">
									</div>
								</div>
							</div>
							<div class="card-footer text-center">
								<a href="index.php" class="btn btn-outline-primary btn-lg">
									<i class="bi bi-arrow-left-circle"></i> Regresar
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>';
			
			return $html;
		}
	}
	
	public function delete_vehiculo($id){
		$sql = "DELETE FROM vehiculo WHERE id=$id;";
			if($this->con->query($sql)){
			echo $this->_message_ok("ELIMINÓ");
		}else{
			echo $this->_message_error("eliminar");
		}	
	}
	
//*************************************************************************

	private function _calculo_matricula($avaluo){
		return number_format(($avaluo * 0.10),2);
	}
	
//*************************************************************************	
	
	private function _message_error($tipo){
		$html = '
		<div class="container mt-5 d-flex justify-content-center">
			<div class="card border-0 shadow-lg p-4 rounded-4 text-center" style="max-width: 500px;">
				<div class="card-header bg-danger text-white rounded-3">
					<h4><i class="bi bi-exclamation-triangle-fill"></i> Error</h4>
				</div>
				<div class="card-body">
					<p class="fw-bold text-danger">Error al ' . $tipo . '. Favor contactar a soporte técnico.</p>
					<a href="index.php" class="btn btn-dark btn-lg mt-3"><i class="bi bi-arrow-left"></i> Regresar</a>
				</div>
			</div>
		</div>';
		return $html;
	}

	private function _message_ok($tipo){
		$html = '
		<div class="container mt-5 d-flex justify-content-center">
			<div class="card border-0 shadow-lg p-4 rounded-4 text-center" style="max-width: 500px;">
				<div class="card-header bg-success text-white rounded-3">
					<h4><i class="bi bi-check-circle-fill"></i> Éxito</h4>
				</div>
				<div class="card-body">
					<p class="fw-bold text-success">El registro se ' . $tipo . ' correctamente.</p>
					<a href="index.php" class="btn btn-dark btn-lg mt-3"><i class="bi bi-arrow-left"></i> Regresar</a>
				</div>
			</div>
		</div>';
		return $html;
	}

//****************************************************************************	
	
} // FIN SCRPIT
?>

