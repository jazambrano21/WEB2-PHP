const form = document.getElementById("FormContacto");

const expresion = {
	email:  /^[a-z][\w.-]+@\w[\w.-]+\.[\w.-]*[a-z][a-z]$/i,
	telefono: /^\d{10}$/	
}

//función para validar email
function emailValido() {
	var valor = document.getElementById("email").value;
	var vemail = false;
	//validación email con expresiones regulares
	if (!expresion.email.test(valor)){
		alert('La direccion de correo electronica ingresada no es valida');
		vemail = false;
	}else{
		console.log("La direccion de correo electronica ingresada es Correcta");
		vemail = true;
	}
	return vemail;
}

//función para validar telefono
function telefonoValido(){
	var valor = document.getElementById("telefono").value;
	var vtelefono = false;
	//validación telefono con expresiones regulares
	if(valor.length == 10 && !isNaN(valor)){
		vtelefono = true;
	}else{
		alert('El numero de telefono es incorrecto, por favor ingrese 10 caracteres numericos');
	}
	return vtelefono;
}

function delay(n){
    return new Promise(function(resolve){
        setTimeout(resolve,n*1000);
    });
}

function validar(){
	//llamado a la funcion email valido y telefono valido
	if(emailValido() && telefonoValido()){
		alert("Los datos ingresados son Correctos");
		return true;
	}else{
		return false;
	}
}

//función para deshabilitar el boton
async function deshabilitar(){
	//llamado a la función validar
	if(validar() == true){
		var boton = document.getElementById('btn');
		boton.disabled = true;
		//cambiar el mensaje original (enviar) 
		boton.value = 'Enviando datos ..........';
		await delay(2);
		boton.form.submit();
	}

}