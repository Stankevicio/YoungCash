<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Iniciar sessão se for usar para feedback
session_start();

// Caminhos para o PHPMailer (ajuste se a pasta PHPMailer-master estiver em outro local)
require __DIR__ . '/PHPMailer-master/src/Exception.php';
require __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/PHPMailer-master/src/SMTP.php';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailDestinatario = $_POST['email'] ?? null;
    $nomeDestinatario = $_POST['nome'] ?? ''; // Supondo que você também tenha um campo 'nome' no formulário

    if (empty($emailDestinatario) || !filter_var($emailDestinatario, FILTER_VALIDATE_EMAIL)) {
        // $_SESSION['email_feedback'] = ['type' => 'error', 'message' => 'Formato de e-mail inválido ou e-mail não fornecido.'];
        // header("Location: pagina_do_formulario.php"); // Redireciona de volta para o formulário
        // exit;
        die("Formato de e-mail inválido ou e-mail não fornecido."); // Simples para este exemplo
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
        $mail->CharSet = 'UTF-8'; // Para caracteres especiais

        // Remetente e Destinatário
        $mail->setFrom('ariteomiran@gmail.com', 'YoungCash News'); // Quem está enviando
        $mail->addAddress($emailDestinatario, $nomeDestinatario ?: null); // Para quem você está enviando (do formulário)
        // $mail->addReplyTo('seu_email_de_resposta@example.com', 'Informação');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        // Conteúdo do E-mail
        $mail->isHTML(true); // Define o formato do e-mail para HTML

        $dataAtual = date('d/m/Y');
        $mail->Subject = 'YoungCash Informa: Novidades do Mercado Financeiro - ' . $dataAtual;

        // Corpo do e-mail HTML
        // (Mantive seu HTML, apenas corrigi a atribuição e adicionei $dataAtual)
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
        
                <div class="news-section">
                    <div class="news-item">
                        <p class="news-title">Mercados Globais em Alta Após Abertura Positiva de Wall Street</p>
                        <p>Os mercados globais registraram um crescimento significativo hoje, impulsionados pela forte abertura da Bolsa de Valores de Nova York...</p>
                        <img src="cid:wall_street" alt="Wall Street" class="news-image">
                    </div>
                    
                    <div class="news-item">
                        <p class="news-title">Banco Central Europeu Aumenta Taxa de Juros</p>
                        <p>O Banco Central Europeu anunciou um aumento em sua taxa de juros de referência...</p>
                        <img src="cid:european_central_bank" alt="Banco Central Europeu" class="news-image">
                    </div>
                    
                    <div class="news-item">
                        <p class="news-title">Bitcoin e Criptomoedas: Mercado Volátil em Alta</p>
                        <p>O mercado de criptomoedas experimentou um crescimento notável nas últimas 24 horas...</p>
                        <img src="cid:bitcoin_growth" alt="Crescimento do Bitcoin" class="news-image">
                    </div>
                </div>
        
                <p class="footer-email">Atenciosamente,<br><strong>Equipe YoungCash</strong></p>
            </div>
        </body>
        </html>';

        // AltBody para clientes de e-mail que não suportam HTML
        $mail->AltBody = 'Para ver esta mensagem, por favor, use um visualizador de e-mail compatível com HTML!';

        // Anexando imagens embutidas (EXEMPLO - VOCÊ PRECISA TER ESSES ARQUIVOS)
        // Crie uma pasta 'img/email_assets/' no mesmo nível do seu script enviar_email.php
        // e coloque as imagens lá, ou ajuste os caminhos.
        try {
            $caminhoImagens = __DIR__ . '/img/email_assets/'; // Supondo que as imagens estejam em 'img/email_assets/'
            if (file_exists($caminhoImagens . 'wall_street.jpg')) $mail->addEmbeddedImage($caminhoImagens . 'wall_street.jpg', 'wall_street');
            if (file_exists($caminhoImagens . 'european_central_bank.jpg')) $mail->addEmbeddedImage($caminhoImagens . 'european_central_bank.jpg', 'european_central_bank');
            if (file_exists($caminhoImagens . 'bitcoin_growth.jpg')) $mail->addEmbeddedImage($caminhoImagens . 'bitcoin_growth.jpg', 'bitcoin_growth');
            // Adicione as outras imagens aqui...
        } catch (Exception $e) {
            error_log("Erro ao embutir imagem no e-mail: " . $e->getMessage());
            // O e-mail ainda será enviado, mas sem as imagens embutidas se houver erro aqui.
        }

        $mail->send();

        // Feedback de sucesso (usando sessão para exibir na página do formulário após redirecionamento)
        // $_SESSION['email_feedback'] = ['type' => 'success', 'message' => 'Notícias enviadas com sucesso para ' . htmlspecialchars($emailDestinatario) . '!'];
        // header("Location: pagina_do_formulario.php");
        // exit();
        echo 'E-mail de notícias enviado com sucesso para ' . htmlspecialchars($emailDestinatario) . '!'; // Mensagem simples por enquanto

    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail PHPMailer: " . $mail->ErrorInfo . " || Exception: " . $e->getMessage());
        // Feedback de erro
        // $_SESSION['email_feedback'] = ['type' => 'error', 'message' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"];
        // header("Location: pagina_do_formulario.php");
        // exit();
        echo "Erro ao enviar e-mail: {$mail->ErrorInfo}"; // Mensagem simples por enquanto
    }
} else {
    // Se o formulário não foi enviado, redireciona ou mostra mensagem
    // header("Location: pagina_do_formulario.php");
    echo "Nenhum formulário enviado.";
    // exit();
}
