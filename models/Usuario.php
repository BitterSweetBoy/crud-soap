<?php
class Usuario extends Conectar
{

    public function insert_usuario($username, $email, $pass)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO users (id, username, email, pass) VALUES (NULL,?,?,?);";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, $pass);
        $stmt->execute();
    }
    public function update_usuario($id, $username, $email, $pass)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE users SET username=?, email=?, pass=? WHERE id=?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, $email);
        $stmt->bindValue(3, $pass);
        $stmt->bindValue(4, $id);
        $stmt->execute();
    }

    public function delete_usuario($id)
    {
        $conectar = parent::conexion();
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    public function getAllUsuarios()
    {
        $conectar = parent::conexion();
        $sql = "SELECT * FROM users";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result); // Serializa a JSON
    }

    public function getUsuarioById($id)
{
    $conectar = parent::conexion();
    $sql = "SELECT * FROM users WHERE id=?";
    $stmt = $conectar->prepare($sql);
    $stmt->bindValue(1, $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return json_encode($result);
}
}
