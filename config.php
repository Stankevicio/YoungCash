<?php
// Habilitar exibição de erros para desenvolvimento (é bom ter aqui também)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tenta obter os valores das variáveis de ambiente.
// Se não encontrar, usa os valores padrão definidos.
$dbHost = getenv('DB_HOST') ?: 'db';                     // Host do banco (deve ser 'db' no Docker)
$dbUsername = getenv('DB_USER') ?: 'root';               // Usuário do banco
$dbPassword = getenv('DB_PASSWORD') ?: 'Siena5173';      // Senha do banco
$dbName = getenv('DB_NAME') ?: 'projeto_pim';            // Nome do banco de dados
$dbCharset = 'utf8mb4';                                  // Charset recomendado

// DSN (Data Source Name) para a conexão PDO com MySQL
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

// Opções para a conexão PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lançar exceções em caso de erro
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Modo de fetch padrão para resultados (array associativo)
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desabilitar emulação de prepared statements para segurança e performance
];

$conn = null; // Inicializa a variável de conexão

try {
    // Tenta criar a instância PDO (a conexão) e atribui a $conn
    $conn = new PDO($dsn, $dbUsername, $dbPassword, $options);
    
    // Mensagem de debug (opcional, remover para produção):
    // echo "DEBUG config.php: Conexão PDO efetuada com sucesso e atribuída a \$conn!"; 

} catch (PDOException $e) {
    error_log("Erro de conexão PDO no config.php: " . $e->getMessage());
    die("Falha crítica ao conectar ao banco de dados. Verifique as configurações. Detalhe (dev): " . $e->getMessage());
}

// Se o script chegou até aqui, $conn é um objeto PDO válido.
?>