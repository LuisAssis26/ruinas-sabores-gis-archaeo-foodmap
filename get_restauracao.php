<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

$conn = pg_connect("REMOVED");

if (!$conn) {
    echo json_encode(["erro" => "Erro na ligação à base de dados"]);
    exit;
}

$dados = [
    "cafes" => [],
    "restaurantes" => [],
];

// cafes
$sql = "
       			SELECT    
    \"name\",
    ST_X(ST_Centroid(geom)) AS longitude, 
    ST_Y(ST_Centroid(geom)) AS latitude
FROM (
    SELECT (ST_Dump(geom)).geom, \"name\" 
    FROM public.cafes
) AS dump
WHERE GeometryType(geom) = 'POINT';

    ";
$res = pg_query($conn, $sql);
if ($res) {
    while ($linha = pg_fetch_assoc($res)) {
        $dados["cafes"][] = $linha;
    }
}

// restaurantes
$sql = "
SELECT    
            \"name\",
            ST_X(ST_Centroid(geom)) AS longitude, 
            ST_Y(ST_Centroid(geom)) AS latitude
        FROM (
            SELECT (ST_Dump(geom)).geom, \"name\" 
            FROM public.restaurantes
        ) AS dump
        WHERE GeometryType(geom) = 'POINT';
    ";
$res = pg_query($conn, $sql);
if ($res) {
    while ($linha = pg_fetch_assoc($res)) {
        $dados["restaurantes"][] = $linha;
    }
}


echo json_encode($dados);
?>