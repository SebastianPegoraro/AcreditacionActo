<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Empezamos</title>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <?php
      //Consutlo en la base de datos por si existe el agasajado
      $dniagasajado = $_POST['dniagasajado'];
      $conexion = mysqli_connect("localhost","root","admin123","empleado_publico_2017") or die("Problemas con la conexión");
      $registros = mysqli_query($conexion,"SELECT nombre, anios, jubilado FROM agasajados WHERE dni = $dniagasajado") or die("Problemas en el select:(Seguro no pusiste el DNI o no es agasajado)".mysqli_error($conexion));
      $regulacion=mysqli_fetch_array($registros);
      $nombre = $regulacion[0];
      if ($nombre != "" && $regulacion[1] == 0 && $regulacion[2] == 0) {

        //Depende del tipo es Jubilado o 25 años
        if ($_POST['tipo'] == 'anio') {
          $tipo = 'M';
        } elseif ($_POST['tipo'] == 'jubilados') {
          $tipo = 'P';
        } else {
          header("Location: error.html");
        }

        //Almacenamos si sube o no en una variable para que no lance excepciones ni errores cuando se ejecuta y no tiene valor.
        if (isset($_POST['nosube'])) {
          $subeono = $_POST['nosube'];
        } else {
          $subeono = " ";
        }

        //Seteamos al que se registro para asignarle un numero
        if ($tipo == 'M') {
          mysqli_query($conexion, "INSERT INTO lista25anios(nombre,ubicacion,subeono) VALUES ('$nombre',0,'$subeono')") or die("Problemas en el insert 25 años:".mysqli_error($conexion));
          $resultado = mysqli_query($conexion,"SELECT COUNT(*) from lista25anios") or die("Problemas en el count 25 años:".mysqli_error($conexion));
        } elseif ($tipo == 'P') {
          mysqli_query($conexion, "INSERT INTO listajubilado(nombre,ubicacion,subeono) VALUES ($nombre,0,'$subeono')") or die("Problemas en el insert jubilados:".mysqli_error($conexion));
          $resultado = mysqli_query($conexion,"SELECT COUNT(*) from listajubilado") or die("Problemas en el count jubilados:".mysqli_error($conexion));
        }
        $res=mysqli_fetch_array($resultado);

        //Agrego los 0 extras para la etiqueta
        if ($res[0] < 10) {
          $res[0] = '00'.$res[0];
        } elseif ($res[0] < 100) {
          $res[0] = '0'.$res[0];
        }

        //Se trae el nombre del agasajado
        $consultanombre = mysqli_query($conexion,"SELECT nombre FROM agasajados WHERE dni = $dniagasajado") or die("Problemas en el select:".mysqli_error($conexion));
        $nombre=mysqli_fetch_array($consultanombre);

        //Se agrega a la lista de asistencia
        if ($tipo == 'M') {
          mysqli_query($conexion,"INSERT INTO lista25anios(nombre,ubicacion,subeono) VALUES ('$nombre[0]',$res[0],'$subeono')") or die("Problemas en el select".mysqli_error($conexion));
        } elseif ($tipo == 'P') {
          mysqli_query($conexion,"INSERT INTO listajubilado(nombre,ubicacion,subeono) VALUES ($nombre[0],$res[0],'$subeono')") or die("Problemas en el select".mysqli_error($conexion));
        }

      }else {
        header("Location: error-ingresado.html");
      }

    ?>
    <div class="container">
      <div id="HTMLtoPDF">
        <div class="row">
          <h1><?php echo $tipo ?>-<?php echo $res[0] ?></h1>
        </div>
        <div class="row">
          <h3><?php echo $reg[0] ?></h3>
        </div>
      </div>
      <a href="#" onclick="HTMLtoPDF()">DESCARGAR</a>
    </div>



    <script src="js/jquery-3.2.1.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/jspdf.js" type="text/javascript"></script>
    <script src="js/pdfFromHTML.js" type="text/javascript"></script>
  </body>
</html>
