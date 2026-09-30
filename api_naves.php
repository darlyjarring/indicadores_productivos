<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$host     = "db.owykvbhrvhopbqfyarzz.supabase.co";
$port     = "5432";
$dbname   = "postgres";
$user     = "postgres";
$password = "KDLjar*-1990";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Recibir parámetros opcionales de fecha desde la consulta JS
    $desde = isset($_GET['desde']) ? $_GET['desde'] . ' 00:00:00' : null;
    $hasta = isset($_GET['hasta']) ? $_GET['hasta'] . ' 23:59:59' : null;

    // Consulta SQL con alias estándar para alinearse con el Frontend
    $sql = "SELECT 
                id,
                nave,
                viaje,
                linea,
                etb,
                etd,
                ata,
                atd,
                status,
                origen,
                destino,
                servicio,
                total_ingresado,
                ingresado_dentro_cutoff,
                ingresado_fuera_cutoff
            FROM naves";

    if ($desde && $hasta) {
        $sql .= " WHERE (etb BETWEEN :desde AND :hasta) 
                   OR (ata BETWEEN :desde AND :hasta)";
    }

    $sql .= " ORDER BY COALESCE(etb, ata) ASC";

    $stmt = $pdo->prepare($sql);

    if ($desde && $hasta) {
        $stmt->bindParam(':desde', $desde);
        $stmt->bindParam(':hasta', $hasta);
    }

    $stmt->execute();
    $naves = $stmt->fetchAll();

    echo json_encode($naves);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => "Error de conexión a PostgreSQL: " . $e->getMessage()
    ]);
}
?>
