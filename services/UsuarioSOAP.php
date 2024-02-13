<?php
require_once '../vendor/econea/nusoap/src/nusoap.php';
require_once 'UsuarioService.php';  // Asegúrate de que esta ruta sea correcta

$namespace = 'UsuarioSoap';
$server = new soap_server();
$server->configureWSDL("UsuarioService", $namespace);
$server->wsdl->schemaTargetNamespace = $namespace;

$server->wsdl->addComplexType('InsertUsuario', /* ... */ );
$server->wsdl->addComplexType('response', /* ... */ );


// Registro para la función insertUsuario
$server->register(
    'UsuarioService.insertUsuario',
    array('username' => 'xsd:string', 'email' => 'xsd:string', 'pass' => 'xsd:string'),
    array('Resultado' => 'xsd:boolean'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Inserta un usuario a redes'
);

// Registro para la función updateUsuario
$server->register(
    'UsuarioService.updateUsuario',
    array('id' => 'xsd:int', 'username' => 'xsd:string', 'email' => 'xsd:string', 'pass' => 'xsd:string'),
    array('Resultado' => 'xsd:boolean'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Actualiza un usuario en redes'
);

// Registro para la función getUsuarioById
$server->register(
    'UsuarioService.getUsuarioById',
    array('id' => 'xsd:int'),
    array('Resultados' => 'xsd:string'), // Cambiado a xsd:anyType
    $namespace,
    false,
    'rpc',
    'encoded',
    'Obtiene un usuario por ID en redes'
);

//Registro para la función deleteUsuario
$server->register(
    'UsuarioService.deleteUsuario',
    array('id' => 'xsd:int'),
    array('Resultado' => 'xsd:boolean'),
    $namespace,
    false,
    'rpc',
    'encoded',
    'Elimina un usuario en redes'
);

$server->register(
    'UsuarioService.getAllUsuarios',
    array(), // No hay parámetros de entrada
    array('Resultados' => 'xsd:string'), // Ajusta el tipo de datos a string
    $namespace,
    false,
    'rpc',
    'encoded',
    'Obtiene todos los usuarios en redes'
);

$POST_DATA = file_get_contents("php://input");
$server->service($POST_DATA);
exit();
?>

