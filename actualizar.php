<?php
include 'conexion.php';

$id_dia = '';
$jornada_id = ''; 
$itinerario_id = '';
$materia_id = '';
$aula_id = '';
$form_title = 'Modificar Disposición Áulica';

if (isset($_GET['id'])) {
    $id_dia = $_GET['id'];
    $sql = "SELECT * FROM dias WHERE id_dia = $id_dia";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jornada_id   = $row['jornada_id']; 
        $itinerario_id= $row['itinerario_id'];
        $materia_id   = $row['materia_id'];
        $aula_id      = $row['aula_id'];
    } else {
        echo "<p style='color:red;'>Registro no encontrado.</p>";
        exit;
    }
} else {
    echo "<p style='color:red;'>ID no proporcionado.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jornada_id    = $_POST['jornada_id']; 
    $itinerario_id = $_POST['itinerario_id'];
    $materia_id    = $_POST['materia_id'];
    $aula_id       = $_POST['aula_id'];

    $sql = "UPDATE dias SET jornada_id='$jornada_id', itinerario_id=$itinerario_id, materia_id=$materia_id, aula_id=$aula_id WHERE id_dia=$id_dia";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Registro actualizado exitosamente.</p>";
        header('Location: disposicionaulica.php');
        exit;
    } else {
        echo "<p style='color:red;'>Error: ".$conn->error."</p>";
    }
}

$jornada_options    = $conn->query("SELECT id_jornada, dias FROM jornada");
$aulas_options      = $conn->query("SELECT id_aula, numero FROM aulas");
$materias_options   = $conn->query("SELECT id_materia, nombre FROM materias");
$itinerarios_options= $conn->query("SELECT id_itinerario, CONCAT(hora_inicio, ' - ', hora_fin) AS horario FROM itinerario");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?php echo $form_title; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600&display=swap" rel="stylesheet">

  <style>
  * {
    margin: 0; 
    padding: 0; 
    box-sizing: border-box;
  }

  html, body {
    overflow-x: hidden;       
  }

  body {
    min-height: 100vh;     
    font-family: 'Lexend', sans-serif;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    background: url('img/aula.jpg') no-repeat left center;
    background-size: cover;
    position: relative;
  }
  
  body::before {
    content: "";
    position: absolute;
    right: 0; top: 0;
    width: 55%; height: 100%;
    background: linear-gradient(to right,rgb(255, 255, 255), rgb(250, 252, 255), rgb(247, 249, 252),  #6BD4E2);
    clip-path: polygon(0 0,100% 0,100% 100%,0 100%,10% 50%);
    z-index: 0;
  }

  .form-container {
    position: relative; z-index: 1;
    width: 100%; max-width: 600px;
    margin-right: 5%;
    padding: 2rem 3rem;
    display: flex; flex-direction: column; gap: 1.2rem;
  }

  .form-container img.logo {
    display: block;
    margin: 0 auto 1rem;
    width: 190px;
  }

  .form-container h2 {
    text-align: center;
    color: #0c2c60;
    margin-bottom: 1.5rem;
  }

  .form-group {
    display: flex; 
    flex-direction: column;
    margin-bottom: 1.2rem;
  }

  label {
    margin-bottom: 0.6rem;
    font-weight: bold;
  }

  select {
    padding: 0.6rem;
    font-size: 1rem;
    background: #fff;
    border: 3px solid transparent;
    border-image-source: linear-gradient(to right, #00e5ff, #0047ff);
    border-image-slice: 1;
    width: 100%;
    box-sizing: border-box;
  }

  .form-row {
    display: flex;
    gap: 2rem;             
  }
  .form-row .form-group {
    flex: 1;                 
  }

  button {
  width: 270px;
  height: 60px;
  border: 3px solid #315cfd;
  transition: all 0.3s;
  cursor: pointer;
  border-radius: 8px;
  background: white;
  font-size: 0.9em;
  font-weight: 550;
  margin-right:20px;
  }

  button:hover {
  background: #315cfd;
  color: white;
  font-size: 1.2em;
}
  .back-button {
    text-align: center;
    padding: 0.6rem;
    background: #444;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    margin-top: 1rem;
  }
  .back-button:hover {
    background: #222;
  }

  @media (max-width:900px) {
    html, body { 
        overflow-x: auto; 
    }
    
    body::before {
      width:100%; clip-path:none;
    }

    body {
      justify-content:center;
      background-position:center top;
    }

    .form-container {
      margin: 1rem;
    }

    .form-row {
      flex-direction: column;
      gap: 1rem;
    }
  }
  </style>
</head>
<body>
  <div class="form-container">
    <img src="img/Cudi.png" alt="Logo CUDI" class="logo">
    <h2><?php echo $form_title; ?></h2>
    
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label for="jornada_id">Jornada:</label>
          <select name="jornada_id" required>
            <option value="">Seleccione una jornada</option>
            <?php while($jornada = $jornada_options->fetch_assoc()): ?>
              <option value="<?php echo $jornada['id_jornada']; ?>" <?php echo ($jornada_id == $jornada['id_jornada']) ? 'selected' : ''; ?>>
                <?php echo $jornada['dias']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="itinerario_id">Horario:</label>
          <select name="itinerario_id" required>
            <option value="">Seleccione un horario</option>
            <?php while($itinerario = $itinerarios_options->fetch_assoc()): ?>
              <option value="<?php echo $itinerario['id_itinerario']; ?>" <?php echo ($itinerario_id == $itinerario['id_itinerario']) ? 'selected' : ''; ?>>
                <?php echo $itinerario['horario']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="materia_id">Materia:</label>
          <select name="materia_id" required>
            <option value="">Seleccione una materia</option>
            <?php while($materia = $materias_options->fetch_assoc()): ?>
              <option value="<?php echo $materia['id_materia']; ?>" <?php echo ($materia_id == $materia['id_materia']) ? 'selected' : ''; ?>>
                <?php echo $materia['nombre']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        
        <div class="form-group">
          <label for="aula_id">Aula:</label>
          <select name="aula_id" required>
            <option value="">Seleccione un aula</option>
            <?php while($aula = $aulas_options->fetch_assoc()): ?>
              <option value="<?php echo $aula['id_aula']; ?>" <?php echo ($aula_id == $aula['id_aula']) ? 'selected' : ''; ?>>
                <?php echo $aula['numero']; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      
      <button type="submit">Actualizar Disposición</button>
    </form>
    
    <a href="disposicionaulica.php" class="back-button">Volver a Disposición Áulica</a>
  </div>
</body>
</html> 