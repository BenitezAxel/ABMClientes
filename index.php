<?php

if(file_exists("datos.txt")) {
    $jsonClientes = file_get_contents("datos.txt");
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = [];
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";
$aMsg = array("mensaje" => "", "codigo" => "");

if(isset($_GET["do"]) && $_GET["do"] == "eliminar") {
    if($aClientes[$id]["imagen"] != "") {
        unlink("files/".$aClientes[$id]["imagen"]);
    }
    unset($aClientes[$id]);
    $jsonClientes = json_encode($aClientes);
    file_put_contents("datos.txt", $jsonClientes);
    $aMsg = array("mensaje" => "Eliminado correctamente", "codigo" => "danger");
    $id = "";
}

if($_POST) {
    $dni = trim($_POST["txtDNI"]);
    $nombre = trim($_POST["txtNombre"]);
    $telefono = trim($_POST["txtTelefono"]);
    $correo = trim($_POST["txtCorreo"]);
    $nombreImagen = "";
    
    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
        $nombreRandom = date("Ymdhmsi");
        $archivoTmp = $_FILES["archivo"]["tmp_name"];
        $nombreArchivo = $_FILES["archivo"]["name"];
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nombreImagen = "$nombreRandom.$extension";
        move_uploaded_file($archivoTmp, "files/$nombreImagen");
        
    }

    if(isset($_GET["id"]) && isset($_GET["id"]) >= 0) {
        if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
            if($aClientes[$id]["imagen"] != "") {
                unlink("files/".$aClientes[$id]["imagen"]);
            }
        }
        //actualizacion
        $aClientes[$id] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen);
            $aMsg = array("mensaje" => "Actualizado correctamente", "codigo" => "success");
    } else {
        //insertar
        $aClientes[] = array(
            "dni" => $dni,
            "nombre" => $nombre,
            "telefono" => $telefono,
            "correo" => $correo,
            "imagen" => $nombreImagen);
            $aMsg = array("mensaje" => "Insertado correctamente", "codigo" => "primary");
    }
    $jsonClientes = json_encode($aClientes);
    file_put_contents("datos.txt", $jsonClientes);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="fontawesome/css/fontawesome.min.css">
    <title>Document</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center py-3">
                <h1>Registro de clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <form action="" method="POST" enctype="multipart/form-data">
                <?php if($aMsg["mensaje"] != ""): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class = "alert alert-<?php echo $aMsg["codigo"]; ?>" role="alert">
                                <?php echo $aMsg["mensaje"];?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label for="txtDNI">DNI: </label><br>
                            <input type="text" class="form-control" id="txtDNI" name="txtDNI" placeholder="DNI" request value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtNombre">Nombre: </label><br>
                            <input type="text" class="form-control" id="txtNombre" name="txtNombre" placeholder="NOMBRE" request value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtTelefono">Telefono: </label><br>
                            <input type="tel" class="form-control" id="txtTelefono" name="txtTelefono" placeholder="Telefono" request value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Correo: </label><br>
                            <input type="email" id="txtCorreo" name="txtCorreo" class="form-control" placeholder="CORREO" request value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : ""; ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label for="txtCorreo">Archivo adjunto:</label>
                            <input type="file" name="archivo" id="archivo" class="form-control-file">
                        </div>
                        <div class="col-12 ">
                            <button type="submit" id="btn-Guardar" name="btn-Guardar" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 col-12">
                <table class="table table-hover border">
                    <tr>
                        <th>Imagen:</th>
                        <th>DNI:</th>
                        <th>Nombre:</th>
                        <th>Correo:</th>
                        <th>Acciones:</th>
                    </tr>
                    <?php foreach ($aClientes as $id => $cliente) : ?>
                        <tr>
                            <td><img src="files/<?php echo $cliente["imagen"]; ?>" alt="" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td style="width: 110px;">
                                <a href="index.php?id=<?php echo $id; ?>"><i class="fas fa-edit" style="border:1px solid #17a2b8;border-radius:40px;font-size:16px;background-color:#17a2b8;color:white;padding:11px 10px;"></i></a>
                                <a href="index.php?id=<?php echo $id; ?>&do=eliminar" ><i class="fas fa-trash-alt" style="border:1px solid #dc3545; border-radius:40px; font-size:16px; background-color:#dc3545;color:white; padding:11px 12px;"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <a href="index.php" ><i class="fas fa-plus" style="border: 1px solid #007bff; border-radius:40px;font-size:20px;background-color:#007bff;color:white;padding:8px 10px;position:fixed;bottom:20px;right:20px;"></i></a>
            </div>
        </div>
</body>

</html>