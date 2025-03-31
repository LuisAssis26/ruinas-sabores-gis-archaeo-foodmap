<?php
function criarConexao() {
    $host = 'localhost';
    $port = '5432';
    $dbname = 'REMOVED';
    $user = 'REMOVED';
    $password = 'REMOVED';

    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}
?>

