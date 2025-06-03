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
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Suporte | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo_suporte.css">
</head>

<body>

    <header class="page-header-logado">
        <div class="container">
            <nav class="navbar navbar-expand-lg youngcash-navbar-logado">
                <a class="navbar-brand" href="home.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#targetNavegacaoSuporte" aria-controls="targetNavegacaoSuporte" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="targetNavegacaoSuporte">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="home.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link" href="cursos.php">Aprenda</a></li>
                        <li class="nav-item"><a class="nav-link" href="alunoPainel.php">Painel</a></li>
                        <li class="nav-item"><a class="nav-link" href="invest.php">Investimento</a></li>
                        <li class="nav-item"><a class="nav-link active" href="suporte.php">Suporte</a></li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="carrinho.php" title="Meu Carrinho">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge badge-pill badge-danger" id="contador-carrinho-nav">0</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdownSuporte" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Olá, <?php echo $nome_usuario_logado; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserDropdownSuporte">
                                <a class="dropdown-item" href="perfil.php">Meu Perfil</a>
                                <a class="dropdown-item" href="configuracoes.php">Configurações</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="sair.php" id="link-sair">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="main-content-suporte">
        <div class="support-container">
            <div class="chat-box">
                <div class="chat-header">
                    <h3><i class="fas fa-headset"></i> Suporte ao Cliente YoungCash</h3>
                </div>
                <div class="chat-body" id="chat-body">
                    <div class="message bot-message">Olá! Sou seu assistente virtual YoungCash. Como posso te ajudar hoje? Você pode perguntar sobre: trocar senha, formas de pagamento, dicas financeiras, etc.</div>
                </div>
                <div class="chat-footer">
                    <input type="text" id="user-input" placeholder="Digite sua dúvida..." autofocus>
                    <button onclick="sendMessage()" aria-label="Enviar mensagem"><i class="fas fa-paper-plane"></i> Enviar</button>
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
        function appendMessage(text, sender) {
            const chatBody = document.getElementById('chat-body');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', sender + '-message'); // Adicionada classe 'message'

            // Para evitar XSS se as respostas do bot incluírem HTML por engano
            const textNode = document.createTextNode(text);
            messageDiv.appendChild(textNode);

            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function botReply(userMessage) {
            let response = 'Desculpe, não entendi. Poderia reformular sua dúvida ou escolher um dos tópicos comuns como "trocar senha", "pagamento" ou "dicas financeiras"?'; // Resposta padrão
            const lowerUserMessage = userMessage.toLowerCase();

            if (lowerUserMessage.includes('olá') || lowerUserMessage.includes('oi') || lowerUserMessage.includes('bom dia') || lowerUserMessage.includes('boa tarde') || lowerUserMessage.includes('boa noite')) {
                response = 'Olá! Sou o assistente YoungCash. Como posso te ajudar sobre finanças, cursos ou sua conta?';
            } else if (lowerUserMessage.includes('trocar senha') || lowerUserMessage.includes('mudar senha') || lowerUserMessage.includes('redefinir senha')) {
                response = 'Para alterar sua senha, vá até a página de "Configurações" no menu do seu perfil. Lá você encontrará a opção para definir uma nova senha.';
            } else if (lowerUserMessage.includes('pagamento') || lowerUserMessage.includes('formas de pagamento') || lowerUserMessage.includes('assinar') || lowerUserMessage.includes('planos')) {
                response = 'Você pode ver nossos planos e formas de pagamento na página de <a href="planos.php">Planos de Assinatura</a>. Aceitamos cartão de crédito e boleto.';
            } else if (lowerUserMessage.includes('dica') && (lowerUserMessage.includes('investir') || lowerUserMessage.includes('economia') || lowerUserMessage.includes('financeira'))) {
                response = 'Uma ótima dica financeira é sempre separar uma parte da sua renda para investimentos, mesmo que seja pouco. Comece pela reserva de emergência! Explore nossos cursos na seção "Aprenda" para mais dicas.';
            } else if (lowerUserMessage.includes('cancelar') && (lowerUserMessage.includes('curso') || lowerUserMessage.includes('assinatura'))) {
                response = 'Para questões sobre cancelamento, por favor, verifique os termos na página do curso ou entre em contato com nosso suporte humano através do link no rodapé.';
            } else if (lowerUserMessage.includes('obrigado') || lowerUserMessage.includes('valeu')) {
                response = 'De nada! Fico feliz em ajudar. Se tiver mais alguma dúvida, é só perguntar.';
            } else if (lowerUserMessage.includes('problema') || lowerUserMessage.includes('ajuda') || lowerUserMessage.includes('dúvida')) {
                response = 'Por favor, descreva seu problema ou dúvida com mais detalhes. Se preferir, pode buscar ajuda em nosso FAQ ou contatar o suporte avançado.';
            }
            // Adicione mais 'else if' para outras palavras-chave e respostas

            // Para permitir HTML na resposta do bot (cuidado com XSS se a resposta vier de fontes não confiáveis)
            const chatBody = document.getElementById('chat-body');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', 'bot-message');
            messageDiv.innerHTML = response; // Permite que links <a> funcionem
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        function sendMessage() {
            const userInputField = document.getElementById('user-input');
            const userInputText = userInputField.value;

            if (userInputText.trim() !== '') {
                appendMessage(userInputText, 'user');
                userInputField.value = '';

                // Simula um pequeno delay para a resposta do bot
                setTimeout(function() {
                    botReply(userInputText);
                }, 500 + Math.random() * 500); // Delay entre 0.5s e 1s
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const userInputField = document.getElementById('user-input');
            if (userInputField) {
                userInputField.addEventListener('keypress', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Impede o comportamento padrão do Enter (que poderia ser submeter um form)
                        sendMessage();
                    }
                });
            }
            // Atualizar ano no rodapé e contador do carrinho
            if (document.getElementById('currentYear')) {
                document.getElementById('currentYear').textContent = new Date().getFullYear();
            }
            const contadorCarrinhoNav = document.getElementById('contador-carrinho-nav');
            if (contadorCarrinhoNav) {
                const carrinho = JSON.parse(localStorage.getItem('cursosNoCarrinho')) || [];
                contadorCarrinhoNav.textContent = carrinho.length.toString();
            }
            // Link de sair para limpar o carrinho
            const linkSair = document.getElementById('link-sair');
            if (linkSair) {
                linkSair.addEventListener('click', function() {
                    localStorage.removeItem('cursosNoCarrinho');
                });
            }
        });
    </script>
</body>

</html>