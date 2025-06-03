<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Definir o tipo de conteúdo da resposta como JSON NO INÍCIO DO SCRIPT
header('Content-Type: application/json');

// Caminhos para o PHPMailer (ajuste se a pasta PHPMailer-master estiver em outro local)
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

$response = ['status' => 'error', 'message' => 'Ocorreu um erro desconhecido ao processar sua solicitação.']; // Resposta padrão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailDestinatario = $_POST['email'] ?? null;
    $nomeDestinatario = $_POST['nome'] ?? 'Leitor(a) YoungCash'; // Pega o nome, com um fallback
    // Você pode pegar outros campos do POST se precisar para o corpo do e-mail ou logs
    // $telefone = $_POST['telefone'] ?? null;
    // $profissao = $_POST['profissao'] ?? null; // Corrigido de 'couses' para 'profissao'

    if (empty($emailDestinatario) || !filter_var($emailDestinatario, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Formato de e-mail inválido ou e-mail não fornecido.';
        echo json_encode($response);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ariteomiran@gmail.com'; // SEU E-MAIL GMAIL
        $mail->Password = 'udvq gavn krzl zefn';   // SUA SENHA DE APLICATIVO GMAIL
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Remetente e Destinatário
        $mail->setFrom('ariteomiran@gmail.com', 'YoungCash News');
        $mail->addAddress($emailDestinatario, $nomeDestinatario ?: null);

        // Conteúdo do E-mail
        $mail->isHTML(true);
        $dataAtual = date('d/m/Y');
        $mail->Subject = 'YoungCash Informa: Novidades do Mercado Financeiro - ' . $dataAtual;

        // Corpo do e-mail HTML (o mesmo que você tinha)
        $mail->Body = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; margin:0; padding:20px; background-color:#f4f4f4; }
                .email-container { background-color: #ffffff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
                h2 { color: #0056b3; margin-bottom:20px; text-align:center; }
                .news-section { margin-top: 20px; }
                .news-item { background-color: #fdfdfd; border: 1px solid #eee; padding: 15px; margin-bottom: 15px; border-radius: 5px;}
                .news-title { font-weight: bold; color: #333; font-size:1.1em; margin-bottom:8px; }
                .news-image { width: 100%; max-width: 550px; height: auto; margin-top: 10px; border-radius:4px; }
                p { margin-bottom: 1em; }
                .footer-email { margin-top:25px; font-size:0.9em; text-align:center; color:#777; }
            </style>
        </head>
        <body>
            <div class="email-container">
                <h2>Principais Notícias do Mundo Financeiro - ' . $dataAtual . '</h2>
                <p>Olá ' . htmlspecialchars($nomeDestinatario) . ',</p>
        
                <div class="news-section">
                    <div class="news-item">
                        <p class="news-title">Mercados Globais em Alta Após Abertura Positiva de Wall Street</p>
                        <p>Os mercados globais registraram um crescimento significativo hoje...</p>
                        <img src="cid:wall_street" alt="Wall Street" class="news-image">
                    </div>
                    <div class="news-item">
                        <p class="news-title">Banco Central Europeu Aumenta Taxa de Juros</p>
                        <p>O Banco Central Europeu anunciou um aumento em sua taxa de juros...</p>
                        <img src="cid:european_central_bank" alt="Banco Central Europeu" class="news-image">
                    </div>
                </div>
        
                <p class="footer-email">Atenciosamente,<br><strong>Equipe YoungCash</strong></p>
            </div>
        </body>
        </html>';

        $mail->AltBody = 'Para ver esta mensagem, por favor, use um visualizador de e-mail compatível com HTML!';

        // Anexando imagens embutidas (EXEMPLO - VOCÊ PRECISA TER ESSES ARQUIVOS)
        // Crie uma pasta 'img/email_assets/' no mesmo nível do seu script enviar_email.php
        // e coloque as imagens lá, ou ajuste os caminhos.
        try {
            // Exemplo: $caminhoImagens = __DIR__ . '/img/email_assets/';
            // if (file_exists($caminhoImagens . 'wall_street.jpg')) $mail->addEmbeddedImage($caminhoImagens . 'wall_street.jpg', 'wall_street');
            // if (file_exists($caminhoImagens . 'european_central_bank.jpg')) $mail->addEmbeddedImage($caminhoImagens . 'european_central_bank.jpg', 'european_central_bank');
        } catch (Exception $e) {
            error_log("Erro ao embutir imagem no e-mail (enviar_email.php): " . $e->getMessage());
        }

        $mail->send();
        $response['status'] = 'success';
        $response['message'] = 'Obrigado por se inscrever! Você receberá nossas notícias em breve em ' . htmlspecialchars($emailDestinatario) . '.';
    } catch (Exception $e) {
        error_log("Erro PHPMailer em enviar_email.php: " . $mail->ErrorInfo . " || Exception: " . $e->getMessage());
        $response['message'] = "Não foi possível enviar o e-mail no momento. Por favor, tente novamente mais tarde.";
        // $response['debug_message'] = $mail->ErrorInfo; // Para depuração, não para produção
    }
} else {
    $response['message'] = 'Método de requisição inválido para enviar_email.php.';
}

// Envia a resposta JSON e termina o script
echo json_encode($response);
exit;
