<?php
session_start();

// Incluir o arquivo de configuração do banco de dados (que define $conn como objeto PDO)
if (file_exists('config.php')) {
    include_once('config.php');
} else {
    die("Erro crítico: Arquivo de configuração não encontrado.");
}

$login_error_message = ''; // Para armazenar a mensagem de erro

// Verifica se há uma mensagem de erro de login na sessão (após um redirecionamento)
if (isset($_SESSION['login_error_message'])) {
    $login_error_message = $_SESSION['login_error_message'];
    unset($_SESSION['login_error_message']); // Limpa a mensagem da sessão após recuperá-la
}

// Verifica se é uma submissão de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!isset($conn) || !$conn) {
        $_SESSION['login_error_message'] = 'Erro no sistema de login (#LCFG). Tente novamente mais tarde.';
        header("Location: login.php");
        exit();
    }

    $email = trim($_POST['email'] ?? '');
    $senha_digitada = $_POST['senha'] ?? '';
    // $tipo_usuario_form = $_POST['tipo_usuario'] ?? ''; // O tipo é verificado após autenticação, a partir do banco

    if (empty($email) || empty($senha_digitada)) {
        $_SESSION['login_error_message'] = 'Por favor, preencha o e-mail e a senha.';
        header("Location: login.php");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error_message'] = 'Formato de e-mail inválido.';
        header("Location: login.php");
        exit();
    }

    try {
        if (!isset($conn) || !$conn) {
            throw new Exception("Conexão com banco de dados não disponível.");
        }

        $sql = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = :email_param";
        $stmt = $conn->prepare($sql);

        // Em vez de bindParam, passamos o array diretamente para execute():
        $stmt->execute([':email_param' => $email]); // $email vem do formulário

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($senha_digitada, $usuario['senha'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['tipo_usuario'] = $usuario['tipo'];

                header("Location: " . ($usuario['tipo'] == 'professor' ? 'professorPainel.php' : 'home.php'));
                exit();
            } else {
                $_SESSION['login_error_message'] = 'E-mail ou senha incorretos.';
            }
        } else {
            $_SESSION['login_error_message'] = 'E-mail ou senha incorretos.';
        }
    } catch (PDOException $e) {
        error_log("Erro PDO em login.php: " . $e->getMessage());
        $_SESSION['login_error_message'] = 'Ocorreu um erro no sistema (#LDPDO2). Tente novamente mais tarde.';
    } catch (Exception $e) {
        error_log("Erro geral em login.php: " . $e->getMessage());
        $_SESSION['login_error_message'] = 'Ocorreu um erro no sistema (#LGEN2). Tente novamente mais tarde.';
    }

    header("Location: login.php");
    exit();
    // } // Fim do if POST
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | YoungCash</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo_login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-sign-in-alt"></i> Acessar Conta</h1>
            <p>Entre para continuar sua jornada educacional</p>
        </div>

        <div class="login-body">
            <?php
            // Exibe a mensagem de erro de login, se houver
            if (!empty($login_error_message)):
            ?>
                <div class="login-error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($login_error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="user-type">
                    <label>
                        <input type="radio" name="tipo_usuario" value="estudante" checked>
                        <i class="fas fa-graduation-cap"></i> Estudante
                    </label>
                    <label>
                        <input type="radio" name="tipo_usuario" value="professor">
                        <i class="fas fa-chalkboard-teacher"></i> Professor
                    </label>
                </div>

                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="senha" placeholder="Senha" required>
                </div>

                <button type="submit" name="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>

                <div class="login-links">
                    <a href="recuperarSenha.php" class="login-link">
                        <i class="fas fa-key"></i> Esqueceu a senha?
                    </a>
                    <a href="#" class="login-link">
                        <i class="fas fa-question-circle"></i> Ajuda
                    </a>
                </div>

                <div class="divider">ou</div>

                <a href="cadastro.php" class="btn-register">
                    <i class="fas fa-user-plus"></i> Criar nova conta
                </a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Se você quiser alguma animação ou lógica JS específica ao carregar, pode adicionar aqui.
            // A animação fadeIn já está sendo feita pelo CSS.
        });
    </script>
</body>

</html>