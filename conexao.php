<?php
/**
 * Exibição de erros desativada por defeito.
 * Para ativar durante o desenvolvimento, definir a variável de ambiente APP_DEBUG=1.
 */
function isDebug() {
    return filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
}
ini_set('display_errors', isDebug() ? '1' : '0'); // Exibe erros apenas em modo debug
ini_set('display_startup_errors', isDebug() ? '1' : '0'); // Exibe erros de inicialização apenas em modo debug
error_reporting(E_ALL); // Relata todos os tipos de erros (no log)

/**
 * Lê a configuração da base de dados PostgreSQL.
 *
 * Usa as variáveis de ambiente DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASSWORD.
 * As que não estiverem definidas são lidas do ficheiro config.php (ignorado pelo Git;
 * ver config.example.php). DB_PORT é opcional e usa 5432 por defeito.
 *
 * @return array|null Configuração com as chaves DB_*, ou null se estiver incompleta
 */
function getDbConfig() {
    $ficheiro = __DIR__ . '/config.php';
    $local = is_file($ficheiro) ? require $ficheiro : [];
    $padrao = ['DB_PORT' => '5432'];

    $config = [];
    foreach (['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'] as $chave) {
        $valor = getenv($chave);
        if ($valor === false) {
            $valor = $local[$chave] ?? $padrao[$chave] ?? null;
        }
        if ($valor === null) {
            return null;
        }
        $config[$chave] = $valor;
    }
    return $config;
}

/**
 * Devolve a string de ligação para pg_connect().
 *
 * @return string|null String de ligação, ou null se a configuração estiver incompleta
 */
function getPgConnectionString() {
    $config = getDbConfig();
    if ($config === null) {
        return null;
    }
    $valor = function ($v) {
        return "'" . addcslashes($v, "'\\") . "'";
    };
    return sprintf(
        'host=%s port=%s dbname=%s user=%s password=%s',
        $valor($config['DB_HOST']),
        $valor($config['DB_PORT']),
        $valor($config['DB_NAME']),
        $valor($config['DB_USER']),
        $valor($config['DB_PASSWORD'])
    );
}

/**
 * Cria e retorna uma conexão PDO com o banco de dados PostgreSQL.
 *
 * @return PDO Conexão ativa com o banco de dados
 */
function getConnection() {
    $config = getDbConfig();
    if ($config === null) {
        http_response_code(500);
        echo "Configuração da base de dados em falta: defina DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASSWORD "
            . "ou copie config.example.php para config.php e preencha os valores.";
        exit;
    }

    try {
        // Cria uma nova conexão PDO com o banco PostgreSQL
        $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
        $conn = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
        // Define o modo de erro para exceções
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        // Exibe mensagem de erro (detalhe apenas em modo debug) e encerra o script
        http_response_code(500);
        error_log("Erro na ligação à base de dados: " . $e->getMessage());
        echo "Erro na ligação à base de dados" . (isDebug() ? ": " . $e->getMessage() : ".");
        exit;
    }
}
?>