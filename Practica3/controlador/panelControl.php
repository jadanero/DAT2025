<?php
// Panel web de control del sistema IoT DAT
// Permite visualizar los datos recibidos, las acciones pendientes y los resultados ejecutados
// Tambien permite anadir nuevas acciones manualmente.

// Archivos que usa el servidor
$datosFile = "datosRecibidos.txt";
$accionesFile = "accionesPendientes.txt";
$resultadosFile = "resultadosRecibidos.txt";

// Crear ficheros si no existen
foreach ([$datosFile, $accionesFile, $resultadosFile] as $file) {
    if (!file_exists($file)) file_put_contents($file, "");
}


// Anadir una nueva acción desde el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idSonda"]) && isset($_POST["accion"])) {
    $id = trim($_POST["idSonda"]);
    $accion = trim($_POST["accion"]);
    $param = trim($_POST["param"]);

    if ($id !== "" && $accion !== "") {
        $json = json_encode([
            "IDsonda" => $id,
            "accion" => $accion,
            "param" => $param
        ]);
        file_put_contents($accionesFile, "$id:$json\n", FILE_APPEND);
        $mensaje = "Acción añadida correctamente para la sonda $id";
    } else {
        $mensaje = "Debes indicar ID de sonda y acción";
    }
}

// Cargar contenidos de los tres ficheros
$datos = @file($datosFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$acciones = @file($accionesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$resultados = @file($resultadosFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Control IoT DAT</title>
<style>
    body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 40px; }
    h1 { color: #0a58ca; }
    h2 { margin-top: 30px; color: #084298; }
    .bloque { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
    pre { background: #e9ecef; padding: 10px; border-radius: 5px; overflow-x: auto; }
    form { margin-top: 10px; }
    input, select { padding: 5px; margin: 3px; }
    .mensaje { margin-top: 10px; font-weight: bold; }
</style>
</head>
<body>

<h1> Panel de Control – Sistema IoT DAT</h1>

<?php if (isset($mensaje)): ?>
    <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
<?php endif; ?>

<!-- Añadir nueva accion -->
<div class="bloque">
    <h2> Anadir nueva accion</h2>
    <form method="post">
        <label>ID Sonda:</label>
        <input type="text" name="idSonda" placeholder="Ej: 34" required>
        <label>Acción:</label>
        <input type="text" name="accion" placeholder="Ej: abrir_valvula" required>
        <label>Parámetro:</label>
        <input type="text" name="param" placeholder="Ej: A">
        <button type="submit">Añadir</button>
    </form>
</div>

<!-- Datos recibidos -->
<div class="bloque">
    <h2> Datos recibidos de sondas</h2>
    <?php if (empty($datos)): ?>
        <p><i>No hay datos recibidos todavia.</i></p>
    <?php else: ?>
        <pre><?php foreach ($datos as $line) echo htmlspecialchars($line) . "\n"; ?></pre>
    <?php endif; ?>
</div>

<!-- Acciones pendientes -->
<div class="bloque">
    <h2> Acciones pendientes</h2>
    <?php if (empty($acciones)): ?>
        <p><i>No hay acciones pendientes.</i></p>
    <?php else: ?>
        <pre><?php foreach ($acciones as $line) echo htmlspecialchars($line) . "\n"; ?></pre>
    <?php endif; ?>
</div>

<!-- Resultados recibidos -->
<div class="bloque">
    <h2> Resultados de acciones ejecutadas</h2>
    <?php if (empty($resultados)): ?>
        <p><i>No hay resultados registrados todavía.</i></p>
    <?php else: ?>
        <pre><?php foreach ($resultados as $line) echo htmlspecialchars($line) . "\n"; ?></pre>
    <?php endif; ?>
</div>

</body>
</html>

