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

// Não há necessidade de include_once('config.php') aqui, a menos que você vá
// interagir com o banco de dados nesta página (ex: buscar preços atualizados, salvar carrinho no BD).
// Por enquanto, o carrinho é gerenciado apenas pelo localStorage.
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <title>Meu Carrinho | YoungCash</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_carrinho.css">
</head>

<body>

  <header class="page-header-logado">
    <div class="container">
      <nav class="navbar navbar-expand-lg youngcash-navbar-logado">
        <a class="navbar-brand" href="home.php">YoungCash</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#targetNavegacaoCarrinho" aria-controls="targetNavegacaoCarrinho" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="targetNavegacaoCarrinho">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="home.php">Início</a></li>
            <li class="nav-item"><a class="nav-link" href="estudo.php">Aprenda</a></li>
            <li class="nav-item"><a class="nav-link" href="formularios.php">Painel</a></li>
            <li class="nav-item"><a class="nav-link" href="simulador_invest.php">Investimento</a></li>
          </ul>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link active" href="carrinho.php" title="Meu Carrinho">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge badge-pill badge-danger" id="contador-carrinho-nav">0</span>
              </a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdownCarrinho" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle"></i> Olá, <?php echo $nome_usuario_logado; ?>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserDropdownCarrinho">
                <a class="dropdown-item" href="perfil.php">Meu Perfil</a>
                <a class="dropdown-item" href="settings.php">Configurações</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="sair.php" id="link-sair">Sair</a>
              </div>
            </li>
          </ul>
        </div>
      </nav>
    </div>
  </header>

  <main class="main-content-carrinho">
    <div class="page-content-carrinho">
      <h1><i class="fas fa-shopping-cart"></i> Seu Carrinho de Compras</h1>

      <div id="lista-carrinho">
      </div>

      <div id="sumario-carrinho-container" style="display: none;">
        <div class="sumario-carrinho">
          <div id="total-carrinho" class="total-final">Total: R$ 0,00</div>
          <a href="pagamento.php" class="btn-finalizar" onclick="return finalizarCompraEConfirmar();">Finalizar Compra</a>
        </div>
      </div>
      <a href="cursos.php" class="continuar-comprando"><i class="fas fa-arrow-left"></i> Continuar comprando</a>
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
  <script src="js/script_carrinho.js"></script>
</body>

</html>