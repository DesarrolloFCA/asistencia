<?php
// Definir el nombre del archivo
$nombreArchivo = "archivo.txt";

// Contenido a escribir en el archivo
$contenido = "archivo creado";

// Intentar crear y escribir en el archivo
if (file_put_contents($nombreArchivo, $contenido) !== false) {
    echo "Archivo creado exitosamente.";
} else {
    echo "Error al crear el archivo.";
}
?>
