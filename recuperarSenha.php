<?php
session_start();
$feedback_html = '';
$trigger_popup_js = ''; // Para o script que vai mostrar o popup

if (isset($_SESSION['recupera_feedback'])) {
  $feedback_type = $_SESSION['recupera_feedback']['type'];
  $feedback_message_text = htmlspecialchars($_SESSION['recupera_feedback']['message']);

  if ($feedback_type === 'success_popup') {
    // O texto do popup é fixo no HTML, mas se quisesse usar o da sessão:
    // $popup_message_for_js = addslashes($feedback_message_text);
    // $trigger_popup_js = "<script> document.addEventListener('DOMContentLoaded', function() { const p = document.querySelector('#popup p'); if(p) p.textContent = '$popup_message_for_js'; /* ... mostrar popup ... */ }); </script>";

    $trigger_popup_js = "
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('overlay');
        const popup = document.getElementById('popup');
        if (overlay && popup) {
            overlay.style.display = 'block';
            popup.classList.add('show');
        }
    });
</script>";
  } else { // Para 'error'
    $feedback_html = "<div class='alert alert-danger' role='alert'>" . $feedback_message_text . "</div>";
  }
  unset($_SESSION['recupera_feedback']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <title>Recuperar Senha | YoungCash</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/estilo_recuperarSenha.css">
</head>

<body>
  <div class="recovery-container">
    <div class="recovery-header">
      <h1><i class="fas fa-key"></i> Recuperar Senha</h1>
      <p>Redefina sua senha para acessar sua conta</p>
    </div>

    <div class="recovery-body">
      <?php if (!empty($feedback_html)) echo $feedback_html; // Exibe mensagens de erro aqui 
      ?>

      <form id="frmRecup" method="POST" action="recuperacaoSenha.php">
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="Digite seu e-mail" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="novaSenha" placeholder="Digite sua nova senha" required>
        </div>
        <button type="submit" name="submit" class="btn-recovery">
          <i class="fas fa-sync-alt"></i> Redefinir Senha
        </button>
      </form>
      <a href="login.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Voltar ao Login
      </a>
    </div>
  </div>

  <div id="overlay"></div>
  <div id="popup">
    <h3><i class="fas fa-check-circle"></i> Sucesso!</h3>
    <p>Sua senha foi alterada com sucesso.</p>
    <button id="btnClose">Continuar para Login</button>
  </div>

  <script>
    const overlay = document.getElementById('overlay');
    const popup = document.getElementById('popup');
    const btnClose = document.getElementById('btnClose');

    function hidePopup() {
      if (popup) popup.classList.remove('show');
      if (overlay) overlay.style.display = 'none';
      window.location.href = 'login.php'; // Redireciona para login após fechar
    }

    if (btnClose) btnClose.addEventListener('click', hidePopup);
    if (overlay) {
      overlay.addEventListener('click', function(event) {
        if (event.target === overlay) { // Somente se clicar no overlay diretamente
          hidePopup();
        }
      });
    }

    // Animação ao carregar
    document.addEventListener('DOMContentLoaded', () => {
      const container = document.querySelector('.recovery-container');
      if (container) {
        container.style.transition = 'opacity 0.5s ease-in-out, transform 0.5s ease-in-out'; // Adicionada transição de transform
        container.style.opacity = '0';
        container.style.transform = 'translateY(20px)';
        setTimeout(() => {
          container.style.opacity = '1';
          container.style.transform = 'translateY(0)';
        }, 50);
      }
    });
  </script>
  <?php if (!empty($trigger_popup_js)) echo $trigger_popup_js; // Ecoa o script para mostrar o popup, se houver sucesso 
  ?>
</body>

</html>