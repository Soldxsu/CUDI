<?php
include("conexion.php");

$jornada = $_REQUEST["jornada_id"];
$itinerario = $_REQUEST["itinerario_id"];
$materia = $_REQUEST["materia_id"];
$aula = $_REQUEST["aula_id"];

$sql= "INSERT INTO `dias`(`id_dia`, `jornada_id`, `itinerario_id`, `materia_id`, `aula_id`) VALUES ('','$jornada','$itinerario','$materia','$aula')";
$result = $con->query($sql);
?>