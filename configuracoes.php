<?php
// Sempre iniciar a sessão no topo absoluto do script
session_start();

// Habilitar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir o arquivo de configuração do banco de dados
if (file_exists('config.php')) {
    include_once('config.php'); // Este arquivo deve definir $conn como um objeto PDO
} else {
    die("Erro crítico: Arquivo de configuração do banco de dados (config.php) não encontrado.");
}

// Verifica se o usuário está logado usando 'usuario_id' e 'email' da sessão
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$current_user_email = $_SESSION['email'];
$current_user_id = $_SESSION['usuario_id'];
$nome_usuario_logado = isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Usuário';

$feedback_html = ''; // Para mensagens de sucesso ou erro

// Processamento do formulário quando ele é enviado (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    if (!isset($conn) || !$conn) {
        $_SESSION['settings_feedback'] = ['type' => 'error', 'message' => 'Erro: Conexão com o banco de dados não estabelecida.'];
        header("Location: configuracoes.php");
        exit();
    }

    $new_email = trim($_POST['email'] ?? $current_user_email);
    $new_password_input = trim($_POST['password'] ?? '');

    if (empty($new_email)) {
        $_SESSION['settings_feedback'] = ['type' => 'error', 'message' => 'O campo de e-mail não pode ficar vazio.'];
        header("Location: configuracoes.php");
        exit();
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['settings_feedback'] = ['type' => 'error', 'message' => 'Formato de e-mail inválido.'];
        header("Location: configuracoes.php");
        exit();
    }

    try {
        if (!empty($new_password_input)) {
            $new_password_hashed = password_hash($new_password_input, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE usuarios SET email = :new_email, senha = :new_password WHERE id = :user_id");
            if (!$stmt) {
                throw new PDOException("Falha ao preparar a consulta de atualização (com senha): " . implode(", ", $conn->errorInfo()));
            }
            $stmt->execute([':new_email' => $new_email, ':new_password' => $new_password_hashed, ':user_id' => $current_user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET email = :new_email WHERE id = :user_id");
            if (!$stmt) {
                throw new PDOException("Falha ao preparar a consulta de atualização (sem senha): " . implode(", ", $conn->errorInfo()));
            }
            $stmt->execute([':new_email' => $new_email, ':user_id' => $current_user_id]);
        }

        $_SESSION['email'] = $new_email;

        $_SESSION['settings_feedback'] = ['type' => 'success', 'message' => 'Configurações atualizadas com sucesso!'];
        header("Location: configuracoes.php");
        exit();
    } catch (PDOException $e) {
        error_log("Erro PDO em configuracoes.php ao atualizar usuário ID {$current_user_id}: " . $e->getMessage());

        $user_message = 'Erro ao atualizar as configurações. Tente novamente mais tarde.';
        if ($e->errorInfo[1] == 1062) {
            $user_message = 'Erro: O novo e-mail fornecido já está em uso por outra conta.';
        } else if (strpos(strtolower($e->getMessage()), "unknown column") !== false) {
            $user_message = 'Erro de configuração: Uma coluna esperada não foi encontrada. (' . htmlspecialchars($e->getMessage()) . ')';
        }

        $_SESSION['settings_feedback'] = ['type' => 'error', 'message' => $user_message];
        header("Location: configuracoes.php");
        exit();
    }
}

if (isset($_SESSION['settings_feedback'])) {
    $alert_class = $_SESSION['settings_feedback']['type'] === 'success' ? 'feedback-success' : 'feedback-error';
    $feedback_html = "<div class='feedback-message " . $alert_class . "'>" . htmlspecialchars($_SESSION['settings_feedback']['message']) . "</div>";
    unset($_SESSION['settings_feedback']);
}

$email_para_formulario = htmlspecialchars($current_user_email);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Configurações da Conta | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo_configuracoes.css">
    </link>
</head>

<body>

    <header class="page-header-logado">
        <div class="container">
            <nav class="navbar navbar-expand-lg youngcash-navbar-logado">
                <a class="navbar-brand" href="home.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#targetNavegacaoSettings" aria-controls="targetNavegacaoSettings" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="targetNavegacaoSettings">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''); ?>" href="home.php">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'active' : ''); ?>" href="cursos.php">Aprenda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="alunoPainel.php">Painel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="invest.php">Investimento</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'configuracoes.php' ? 'active' : ''); ?>" href="configuracoes.php">Configurações</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdownSettings" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Olá, <?php echo $nome_usuario_logado; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserDropdownSettings">
                                <a class="dropdown-item" href="perfil.php">Meu Perfil</a>
                                <a class="dropdown-item" href="configuracoes.php">Configurações</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="sair.php">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content-settings">
        <div class="container-settings">
            <h2>Configurações da Conta</h2>

            <?php if (!empty($feedback_html)) echo $feedback_html; ?>

            <form method="POST" action="configuracoes.php">
                <div>
                    <label for="email">Novo Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_para_formulario; ?>" required>
                </div>
                <div>
                    <label for="password">Nova Senha (deixe em branco para não alterar):</label>
                    <input type="password" id="password" name="password" placeholder="Digite uma nova senha, se desejar">
                </div>
                <br>
                <button type="submit" name="submit">Salvar Alterações</button>
            </form>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="row py-4 align-items-center">
                <nav class="navbar navbar-expand col-md-7 col-12 justify-content-center justify-content-md-start mb-3 mb-md-0">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home Pública</a></li>
                        <li class="nav-item"><a class="nav-link" href="home.php">Minha Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="planos.php">Planos</a></li>
                    </ul>
                </nav>
                <div class="col-md-5 col-12 text-center text-md-right social-icon">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-square"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center copyright-section">
                    <p class="text-muted small">&copy; <span id="currentYear"></span> YoungCash. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script>
        if (document.getElementById('currentYear')) {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
        }
    </script>
</body>

</html>