<?php
session_start();
include_once('config.php'); // Garanta que $conexao está definida aqui

$login_erro_msg = ''; // Para exibir mensagens de erro na página de login

if (isset($_POST['submit'])) {
    $email_formulario = trim($_POST['email'] ?? '');
    $senha_formulario = $_POST['senha'] ?? '';
    // $tipo_usuario_formulario = $_POST['tipo_usuario'] ?? ''; // Removido da lógica de verificação inicial

    if (empty($email_formulario) || empty($senha_formulario)) {
        $login_erro_msg = "Por favor, preencha o email e a senha.";
    } elseif (!filter_var($email_formulario, FILTER_VALIDATE_EMAIL)) {
        $login_erro_msg = "Formato de e-mail inválido.";
    } else {
        // Usar Prepared Statements para buscar o usuário
        $sql = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conexao, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email_formulario);
            mysqli_stmt_execute($stmt);
            $resultado_query = mysqli_stmt_get_result($stmt);

            if ($resultado_query && mysqli_num_rows($resultado_query) == 1) {
                $usuario_banco = mysqli_fetch_assoc($resultado_query);

                // Verificar a senha usando password_verify()
                if (password_verify($senha_formulario, $usuario_banco['senha'])) {
                    // Senha correta! Login bem-sucedido.
                    // Regenerar ID da sessão para segurança
                    session_regenerate_id(true); 
                    
                    $_SESSION['usuario_id'] = $usuario_banco['id'];
                    $_SESSION['nome'] = $usuario_banco['nome'];
                    $_SESSION['email'] = $usuario_banco['email'];
                    $_SESSION['tipo_usuario'] = $usuario_banco['tipo']; // 'tipo' vem do banco

                    // Redirecionar conforme o tipo de usuário do banco
                    if ($usuario_banco['tipo'] == 'professor') {
                        header("Location: homeProfessor.php");
                    } else { // Assumindo que 'estudante' ou outros tipos vão para home.php
                        header("Location: home.php");
                    }
                    exit();
                } else {
                    // Senha incorreta
                    $login_erro_msg = "E-mail ou senha incorretos.";
                }
            } else {
                // Usuário não encontrado
                $login_erro_msg = "E-mail ou senha incorretos.";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Erro ao preparar a consulta
            $login_erro_msg = "Erro no sistema (#1). Tente novamente mais tarde.";
            error_log("Erro ao preparar consulta de login: " . mysqli_error($conexao)); // Log para admin
        }
    }

    // Se houve erro no login, redirecionar de volta para login.php com a mensagem
    // Armazenar erro na sessão para exibir na página de login
    if (!empty($login_erro_msg)) {
        $_SESSION['login_error'] = $login_erro_msg;
        header("Location: login.php");
        exit();
    }

} else {
    // Acesso direto ao arquivo ou formulário não submetido
    header("Location: login.php");
    exit();
}
?>