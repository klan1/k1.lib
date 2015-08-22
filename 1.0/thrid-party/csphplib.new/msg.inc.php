<?php
//mensages de ERROR interno
$carvac_err_msg['0101'] = "LLAMADO INVALIDO DE LA APLICACION";
$carvac_err_msg['0102'] = "LLAMADO INVALIDO DE LA ACCION";
$carvac_err_msg['0102'] = "LLAMADO INVALIDO DE LA SECCION";
$carvac_err_msg['0103'] = "LLAMADO INVALIDO DEL REPORTE";
$carvac_err_msg['0104'] = "Terminal con licencia de uso";
$carvac_err_msg['0105'] = "Terminal sin licencia de uso";
$carvac_err_msg['0106'] = "El usuario o la contraseña no pueder estar en blanco";
$carvac_err_msg['0107'] = "Usuario o contraseña invalida";
$carvac_err_msg['0108'] = "No se han ingresado uno o mas registros de vacunacion ya que existen iguales en el \'Registro de Vacunacion\'";
$carvac_err_msg['0109'] = "Debe elegir una sede para ingresar";

// Base de datos
$carvac_err_msg['0201'] = "Los parametros del servidor no son correctos";
$carvac_err_msg['0202'] = "La conexion con el servicio de base de datos no ha sido posible, contacte al administrador del sistema.";
$carvac_err_msg['0203'] = "La base de datos no se ha podido abrir, contacte al administrador del sistema.";
$carvac_err_msg['0204'] = "Se debe efectuar una conexion a el servicio de bases de datos antes de hacer algun QUERY";
$carvac_err_msg['0205'] = "La clase sql requiere de un OBJETO de tipo cs_db_manager";
$carvac_err_msg['0206'] = "Error en la ejecucion del comando SQL";
$carvac_err_msg['0207'] = "Se ha llamado una clase que requiere de un OBJETO de tipo SQL";
$carvac_err_msg['0208'] = "Se ha llamado una clase que requiere de un OBJETO de tipo CS_DB_MANAGER";

// Licenciamineto
$carvac_err_msg['0301'] =  "Esta terminal esta fuera de los permisos de la licencia\Comuniquese con nuestra empresa para mas informacion.";
// Seguridad interna del programa
$carvac_err_msg['0401'] =  "Restringido";
$carvac_err_msg['0402'] =  "No tiene permiso de administrar el sistema";
$carvac_err_msg['0403'] =  "Los reportes no le son permitidos";
$carvac_err_msg['0404'] =  "No tiene permitido el uso de los Reportes Adicionales";
$carvac_err_msg['0405'] =  "No tiene permiso de uso, para ver las dosis use -Imprimir Carnet-";
$carvac_err_msg['0406'] =  "No puede actualizar dosis del carnet de vacunacion";
$carvac_err_msg['0407'] =  "No puede borrar dosis del carnet de vacunacion";
$carvac_err_msg['0408'] =  "Algunos datos fueron restringidos por seguridad de nuestros clientes";
$carvac_err_msg['0409'] =  "No tiene permiso de modificar los datos de ningun cliente";


//Reportes y busquedas
$carvac_err_msg['0501'] =  "El paciente no existe en la tabla local ni en la tabla externa predeterminada si la hay";
$carvac_err_msg['0502'] =  "Busqueda de pacientes sin resultados";
$carvac_err_msg['0503'] =  "Por favor ingrese alguno de los datos para la busqueda";
$carvac_err_msg['0505'] =  "El usuario no tiene reaciones reportadas a la fecha";
$carvac_err_msg['0506'] =  "No se han encontrado dosis aplicadas";
$carvac_err_msg['0507'] =  "No hay eventos disponibles para contar";
$carvac_err_msg['0508'] =  "No hay reacciones reportadas de la forma preguntada";
$carvac_err_msg['0509'] =  "No hay pacientes pendientes para este mes";
$carvac_err_msg['0510'] =  "Debe especificar al menos un sintoma para este reporte";
$carvac_err_msg['0511'] =  "Debe seleccionar una vacuna";
//$carvac_err_msg['0512'] =  "Error al importar el Paciente";

//Panel de Control
$carvac_err_msg['0601'] =  "El Registro no se puede borrar, esta en uso";

//General
$carvac_err_msg['1001'] =  "Debe espesificar una sede, ya que este login no tiene una predeterminada.";
?>