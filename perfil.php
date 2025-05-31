<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Valores padrão caso a busca no banco falhe ou config.php não seja encontrado
$nome_usuario_logado = isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Usuário';
$email_usuario_logado = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'email@exemplo.com';
$membro_desde_data_formatada = "Data não disponível";

// Incluir config.php e buscar dados do perfil do banco
if (file_exists('config.php')) {
    include_once('config.php');
} else {
    error_log("CRÍTICO: config.php não encontrado em perfil.php");
    // A página ainda será renderizada com os dados da sessão/placeholders se config.php faltar
}

if (isset($conn) && $conn instanceof PDO) { // Verifica se $conn existe e é um objeto PDO
    try {
        $stmt = $conn->prepare("SELECT nome, email, data_criacao FROM usuarios WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['usuario_id'], PDO::PARAM_INT);
        $stmt->execute();
        $dados_perfil_db = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null; // É uma boa prática limpar o statement

        if ($dados_perfil_db) {
            $nome_usuario_logado = htmlspecialchars($dados_perfil_db['nome']);
            $email_usuario_logado = htmlspecialchars($dados_perfil_db['email']);

            if (!empty($dados_perfil_db['data_criacao'])) {
                $timestamp = strtotime($dados_perfil_db['data_criacao']);
                if ($timestamp !== false) {
                    // Array para converter o número do mês para nome em português
                    $meses_pt = [
                        1 => 'Janeiro',
                        2 => 'Fevereiro',
                        3 => 'Março',
                        4 => 'Abril',
                        5 => 'Maio',
                        6 => 'Junho',
                        7 => 'Julho',
                        8 => 'Agosto',
                        9 => 'Setembro',
                        10 => 'Outubro',
                        11 => 'Novembro',
                        12 => 'Dezembro'
                    ];
                    $mes_num = (int)date('n', $timestamp); // Pega o número do mês (1-12)
                    $ano = date('Y', $timestamp);
                    if (isset($meses_pt[$mes_num])) {
                        $membro_desde_data_formatada = $meses_pt[$mes_num] . " de " . $ano;
                    } else {
                        $membro_desde_data_formatada = date('d/m/Y', $timestamp); // Fallback para formato numérico
                    }
                } else {
                    $membro_desde_data_formatada = "Data de criação inválida";
                }
            } else {
                $membro_desde_data_formatada = "Não informado";
            }
        } else {
            error_log("Dados do perfil não encontrados no banco para usuário ID: " . $_SESSION['usuario_id']);
            // Mantém os valores da sessão ou placeholders se não encontrar no banco
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados do perfil (PDOException) em perfil.php: " . $e->getMessage());
        // Mantém os valores da sessão ou placeholders em caso de erro
        $membro_desde_data_formatada = "Erro ao buscar data";
    } catch (Exception $e) { // Captura outras exceções gerais
        error_log("Erro geral ao buscar dados do perfil em perfil.php: " . $e->getMessage());
        $membro_desde_data_formatada = "Erro ao buscar data";
    }
} else {
    error_log("Conexão com banco de dados (\$conn) não disponível ou inválida em perfil.php");
    // A página será renderizada com os dados da sessão/placeholders
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Meu Perfil | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo_perfil.css">
</head>

<body>

    <header class="page-header-logado">
        <div class="container">
            <nav class="navbar navbar-expand-lg youngcash-navbar-logado">
                <a class="navbar-brand" href="home.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#targetNavegacaoPerfil" aria-controls="targetNavegacaoPerfil" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="targetNavegacaoPerfil">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''); ?>" href="home.php">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'active' : ''); ?>" href="cursos.php">Aprenda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'alunoPainel.php' ? 'active' : ''); ?>" href="alunoPainel.php">Painel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'suporte.php' ? 'active' : ''); ?>" href="suporte.php">Suporte</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo (basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : ''); ?>" href="#" id="navbarUserDropdownPerfil" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Olá, <?php echo $nome_usuario_logado; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserDropdownPerfil">
                                <a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'active' : ''); ?>" href="perfil.php">Meu Perfil</a>
                                <a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''); ?>" href="settings.php">Configurações</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="sair.php" id="link-sair">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-profile-content">
        <div class="container profile-content-container">
            <div class="profile-header">
                <img src="img/perfil.webp" alt="Foto de Perfil">
                <div>
                    <h1><?php echo $nome_usuario_logado; ?></h1>
                    <p>Email: <?php echo $email_usuario_logado; ?></p>
                    <p>Membro desde: <?php echo $membro_desde_data_formatada; ?></p>
                        </div>
                </div>

                <h2>Meus Cursos</h2>
                <div class="courses">
                    <div class="course">
                        <h3>Educação Financeira Básica</h3>
                        <p>Última Atividade: 20 de Novembro de 2024</p>
                        <div class="progress-container">
                            <div class="progress-bar"><span style="width: 100%;"></span></div>
                            <div class="progress-text">100%</div>
                        </div>
                    </div>
                    <div class="course">
                        <h3>Investimentos Avançados</h3>
                        <p>Última Atividade: 15 de Novembro de 2024</p>
                        <div class="progress-container">
                            <div class="progress-bar"><span style="width: 75%;"></span></div>
                            <div class="progress-text">75%</div>
                        </div>
                    </div>
                    <div class="course">
                        <h3>Planejamento de Aposentadoria</h3>
                        <p>Última Atividade: 10 de Novembro de 2024</p>
                        <div class="progress-container">
                            <div class="progress-bar"><span style="width: 50%;"></span></div>
                            <div class="progress-text">50%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container profile-content-container recommendations">
                <h2>Recomendações de Cursos</h2>
                <div class="course">
                    <h3>Gestão de Finanças Pessoais</h3>
                    <p>Aprenda a controlar seu orçamento e poupar dinheiro de forma inteligente.</p>
                    <div class="star-rating">
                        <span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star">★</span>
                    </div>
                </div>
                <div class="course">
                    <h3>Introdução ao Mercado de Ações</h3>
                    <p>Entenda como o mercado de ações funciona e como investir com segurança.</p>
                    <div class="star-rating">
                        <span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star">★</span>
                    </div>
                </div>
                <div class="course">
                    <h3>Planejamento Financeiro para a Aposentadoria</h3>
                    <p>Prepare-se para a aposentadoria com estratégias de investimento e economia.</p>
                    <div class="star-rating">
                        <span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span><span class="star filled">★</span>
                    </div>
                </div>
            </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="row py-4 align-items-center">
                <nav class="navbar navbar-expand col-md-7 col-12 justify-content-center justify-content-md-start mb-3 mb-md-0">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home Pública</a></li>
                        <li class="nav-item"><a class="nav-link" href="home.php">Minha Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="planos.html">Planos</a></li>
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
        const linkSair = document.getElementById('link-sair');
        if (linkSair) {
            linkSair.addEventListener('click', function() {
                // Limpa o carrinho do localStorage ao clicar em sair (se a chave for 'cursosNoCarrinho')
                localStorage.removeItem('cursosNoCarrinho');
            });
        }
    </script>
</body>

</html>