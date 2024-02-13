<?php
// En services/UsuarioService.php
require_once "../config/conexion.php";
require_once "../models/Usuario.php";

class UsuarioService {

    public function insertUsuario($username, $email, $pass) {
        $usuario = new Usuario();
        $usuario->insert_usuario($username, $email, $pass);
        return array('Resultado' => true);
    }

    public function updateUsuario($id, $username, $email, $pass) {
        $usuario = new Usuario();
        $usuario->update_usuario($id, $username, $email, $pass);
        return array('Resultado' => true);
    }

    public function getUsuarioById($id){
        $usuario = new Usuario();
        $result = $usuario->getUsuarioById($id); // Llamada al método getUsuarioById
        return $result;
    }

    public function deleteUsuario($id) {
        $usuario = new Usuario();
        $usuario->delete_usuario($id);
        return array('Resultado' => true);
    }
    
    public function getAllUsuarios() {
        $usuario = new Usuario();
        $result = $usuario->getAllUsuarios(); // Agrega este método a tu clase Usuario
        return $result;
    }

}
?>
