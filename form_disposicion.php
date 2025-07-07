<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0;url=disposicionaulica.php">
    <title>Document</title>
</head>
</html>
<?php
include("conexion.php");

$jornada = $_REQUEST["jornada_id"];
$itinerario = $_REQUEST["itinerario_id"];
$materia = $_REQUEST["materia_id"];
$aula = $_REQUEST["aula_id"];

$sql= "INSERT INTO `dias`(`id_dia`, `jornada_id`, `itinerario_id`, `materia_id`, `aula_id`) VALUES ('','$jornada','$itinerario','$materia','$aula')";
$result = $conn->query($sql);
?>