<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$nome_usuario_logado = isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Usuário';
// config.php include (opcional, se não usar BD nesta página específica)
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Simulador de Investimento | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo_invest.css">
</head>

<body>

    <header class="page-header-logado">
        <div class="container">
            <nav class="navbar navbar-expand-lg youngcash-navbar-logado">
                <a class="navbar-brand" href="home.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#targetNavegacaoSimulador" aria-controls="targetNavegacaoSimulador" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="targetNavegacaoSimulador">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="home.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="cursos.php">Aprenda</a></li>
                        <li class="nav-item"><a class="nav-link" href="alunoPainel.php">Painel</a></li>
                        <li class="nav-item"><a class="nav-link active" href="invest.php">Investimento</a></li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdownSimulador" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Olá, <?php echo $nome_usuario_logado; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserDropdownSimulador">
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

    <main class="main-content-simulador">
        <div class="simulador-container">
            <h1>Simulador de Investimento</h1>
            <div class="form-group">
                <label for="valorInvestido">Valor a Investir (R$):</label>
                <input type="number" id="valorInvestido" placeholder="Ex: 1000" required>
            </div>
            <div class="form-group">
                <label for="taxaJuros">Taxa de Juros Anual (%):</label>
                <input type="number" id="taxaJuros" placeholder="Ex: 5" required>
            </div>
            <div class="form-group">
                <label for="periodo">Período (anos):</label>
                <input type="number" id="periodo" placeholder="Ex: 5" required>
            </div>
            <button id="travarMetaBtn" onclick="travarMeta()">Calcular e Travar Meta</button>
            <button id="mudarMetaBtn" style="display: none;" onclick="mudarMeta()">Mudar Meta</button>

            <div id="chartContainer">
                <canvas id="investmentChart"></canvas>
            </div>

            <div id="resultadoInvestimento" class="result-container" style="display: none;">
                <h3>Valor Final Estimado: R$ <span id="valorFinal"></span></h3>
            </div>

            <button class="back-button" onclick="window.location.href='home.php';">Voltar para Home</button>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>

    <script src="js/script_invest.js"></script>
</body>

</html>