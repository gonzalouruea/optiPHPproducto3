<?php 
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $codPostal = $_POST['codPostal'];
    $ciudad = $_POST['ciudad'];
    $pais = $_POST['pais'];
    $password = $_POST['password'];
    $rol = 'usuario'; // Por defecto todos los nuevos usuarios son 'usuario'
    
    if(empty($nombre) || empty($apellido1) || empty($apellido2) || empty($email) || 
       empty($direccion) || empty($codPostal) || empty($ciudad) || empty($pais) || empty($password)) {
        echo "Debes rellenar todos los campos";
    } else {
        try {
            $sql = "INSERT INTO transfer_viajeros(nombre, apellido1, apellido2, direccion, codigoPostal, ciudad, pais, email, password, rol)
                    VALUES (:nombre, :apellido1, :apellido2, :direccion, :codigoPostal, :ciudad, :pais, :email, :password, :rol)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':codigoPostal', $codPostal);
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':pais', $pais);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':rol', $rol);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);

            $stmt->execute();
            $exito = "El usuario ha sido creado con éxito";
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Código para violación de clave única (email duplicado)
                $error = "El email ya está registrado. Por favor, usa otro email.";
            } else {
                $error = "Error al crear el usuario: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- El resto del HTML permanece igual -->