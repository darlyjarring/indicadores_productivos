<?php
$host     = "db.owykvbhrvhopbqfyarzz.supabase.co"; // Reemplazar con tus credenciales
$port     = "5432";
$dbname   = "postgres";
$user     = "postgres";
$password = "KDLjar*-1990";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>
