<?php
session_start(); // Iniciar sessão para mensagens de feedback

// Configurações de erro (boas para desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detalhes da conexão com o banco (usando mysqli para este script, conforme seu código original)
$servername = "db";
$username = "root";
$password = "Siena5173";
$database = "projeto_pim";

$mysqli_conn = new mysqli($servername, $username, $password, $database);

if ($mysqli_conn->connect_error) {
    // Em caso de falha crítica na conexão, define um feedback de erro e redireciona
    $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => "Falha crítica na conexão com o banco de dados. Por favor, tente mais tarde. (#DBConnect)"];
    header("Location: recuperarSenha.php");
    exit();
}

// Coletar dados do POST
$email = $_POST['email'] ?? null;
$novaSenha_digitada = $_POST['novaSenha'] ?? null;

// Validações básicas
if (empty($email) || empty($novaSenha_digitada)) {
    $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => 'E-mail e nova senha são obrigatórios.'];
    header("Location: recuperarSenha.php");
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => 'Formato de e-mail inválido.'];
    header("Location: recuperarSenha.php");
    exit();
}
// Adicionar validação de força da senha aqui se desejar (ex: mínimo de caracteres)
if (strlen($novaSenha_digitada) < 6) { // Exemplo: mínimo de 6 caracteres
    $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => 'A nova senha deve ter pelo menos 6 caracteres.'];
    header("Location: recuperarSenha.php");
    exit();
}


$novaSenhaHasheada = password_hash($novaSenha_digitada, PASSWORD_DEFAULT);

$sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
$stmt = $mysqli_conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ss", $novaSenhaHasheada, $email);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Senha atualizada com sucesso! Define feedback para o popup.
            $_SESSION['recupera_feedback'] = ['type' => 'success_popup', 'message' => 'Sua senha foi alterada com sucesso!'];
            header("Location: recuperarSenha.php"); // Redireciona para mostrar o popup
            exit();
        } else {
            // Nenhuma linha afetada - email não encontrado ou a senha não mudou
            $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => 'Não foi possível redefinir a senha. Verifique se o e-mail está correto.'];
            header("Location: recuperarSenha.php");
            exit();
        }
    } else {
        // Erro na execução do statement
        error_log("Erro ao executar update de senha (mysqli) para email " . $email . ": " . $stmt->error);
        $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => "Erro ao tentar redefinir a senha (#EXEC)."];
        header("Location: recuperarSenha.php");
        exit();
    }
    $stmt->close();
} else {
    // Erro na preparação do statement
    error_log("Erro ao preparar update de senha (mysqli): " . $mysqli_conn->error);
    $_SESSION['recupera_feedback'] = ['type' => 'error', 'message' => "Erro no sistema ao tentar redefinir a senha (#PREP)."];
    header("Location: recuperarSenha.php");
    exit();
}

$mysqli_conn->close();
