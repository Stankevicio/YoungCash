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

// --- DADOS DOS CURSOS COM TEMAS DE EDUCAÇÃO FINANCEIRA, MAS COM AS IMAGENS ORIGINAIS ---
$cursos_todos = [
    1 => [
        'id' => 1,
        'img' => 'img/educacao-financeira5.jpg', // Imagem original mantida
        'titulo' => 'Orçamento Pessoal Inteligente: Do Zero à Organização',
        'subtitulo' => 'Aprenda a controlar seus gastos, criar um orçamento eficaz e alcançar seus objetivos.',
        'autor' => 'Carolina Finanças', // Novo autor
        'avaliacao' => '⭐ 4,9 (12.345)',
        'preco_atual' => 'R$29,90',
        'preco_antigo' => 'R$149,90',
        'tag' => 'Essencial',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=1',
        'o_que_aprendera_lista' => ['Como rastrear suas despesas e receitas.', 'Criar um orçamento mensal realista.', 'Definir metas financeiras.', 'Estratégias para reduzir dívidas.'],
        'desconto_info' => '⏳ Últimas horas com 80% OFF!'
    ],
    2 => [
        'id' => 2,
        'img' => 'img/educacao-financeira2.png', // Imagem original mantida
        'titulo' => 'Investimentos para Iniciantes: Comece com Segurança',
        'subtitulo' => 'Perca o medo de investir! Entenda os principais tipos de investimento.',
        'autor' => 'Ricardo Investe',
        'avaliacao' => '⭐ 4,7 (22.870)',
        'preco_atual' => 'R$39,90',
        'preco_antigo' => 'R$229,90',
        'tag' => 'Popular',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=2',
        'o_que_aprendera_lista' => ['Renda fixa vs. Renda variável.', 'Tesouro Direto e CDBs.', 'Diversificação de carteira.', 'Escolhendo uma corretora.'],
        'desconto_info' => '🔥 Curso em alta!'
    ],
    3 => [
        'id' => 3,
        'img' => 'img/educacao-financeira3.jpg', // Imagem original mantida
        'titulo' => 'Mentalidade Financeira de Sucesso',
        'subtitulo' => 'Reprograme sua relação com o dinheiro e desenvolva hábitos de prosperidade.',
        'autor' => 'Sofia MentePróspera',
        'avaliacao' => '⭐ 4,8 (8.910)',
        'preco_atual' => 'R$27,90',
        'preco_antigo' => 'R$189,90',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=3',
        'o_que_aprendera_lista' => ['Identificar crenças limitantes.', 'Desenvolver disciplina financeira.', 'Comportamento e finanças.', 'Visão de longo prazo.'],
        'desconto_info' => '✨ Transforme sua vida financeira!'
    ],
    4 => [
        'id' => 4,
        'img' => 'img/educacao-financeira4.png', // Imagem original mantida
        'titulo' => 'Fontes de Renda Extra Online',
        'subtitulo' => 'Explore formas de gerar renda complementar usando a internet em 2025.',
        'autor' => 'Marcos Empreende',
        'avaliacao' => '⭐ 4,6 (15.200)',
        'preco_atual' => 'R$22,90',
        'preco_antigo' => 'R$159,90',
        'tag' => 'Novo',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=4',
        'o_que_aprendera_lista' => ['Negócios online de baixo custo.', 'Monetizar habilidades e hobbies.', 'Trabalho freelancer.', 'Marketing de afiliados.'],
        'desconto_info' => '🚀 Lance sua ideia online!'
    ],
    5 => [
        'id' => 5,
        'img' => 'img/educacao-financeira6.jpg', // Imagem original mantida
        'titulo' => 'Planejamento para Aposentadoria Tranquila',
        'subtitulo' => 'Aprenda a planejar seu futuro e viver com segurança financeira na aposentadoria.',
        'autor' => 'Valéria FuturoSeguro',
        'avaliacao' => '⭐ 4,9 (9.850)',
        'preco_atual' => 'R$45,90',
        'preco_antigo' => 'R$299,90',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=5',
        'o_que_aprendera_lista' => ['Previdência pública e privada.', 'Cálculo de necessidades futuras.', 'Investimentos para longo prazo.', 'Estratégias de resgate.'],
        'desconto_info' => '⏳ Vagas se esgotando!'
    ],
    6 => [
        'id' => 6,
        'img' => 'img/educacao-financeira7.webp', // Imagem original mantida
        'titulo' => 'Saindo das Dívidas: Guia Prático',
        'subtitulo' => 'Métodos eficazes para negociar, quitar dívidas e alcançar sua liberdade financeira.',
        'autor' => 'Roberto SemDívidas',
        'avaliacao' => '⭐ 4,7 (11.500)',
        'preco_atual' => 'R$35,90',
        'preco_antigo' => 'R$219,90',
        'tag' => 'Recomendado',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=6',
        'o_que_aprendera_lista' => ['Diagnóstico de endividamento.', 'Técnicas de negociação.', 'Evitando novas dívidas.', 'Plano de quitação eficiente.'],
        'desconto_info' => '🎁 Bônus: Planilha Exclusiva!'
    ],
    7 => [
        'id' => 7,
        'img' => 'img/educacao-financeira1.png', // Imagem original mantida
        'titulo' => 'Imposto de Renda para Investidores',
        'subtitulo' => 'Declare seus investimentos em Renda Fixa e Variável corretamente e evite problemas.',
        'autor' => 'Contadora Ana Fiscal',
        'avaliacao' => '⭐ 4,8 (7.230)',
        'preco_atual' => 'R$22,90',
        'preco_antigo' => 'R$159,90',
        'link_detalhes' => 'cursos_detalhe_pagina.php?id=7',
        'o_que_aprendera_lista' => ['Regras do IR para investimentos.', 'Declaração de Ações e FIIs.', 'Cálculo e pagamento de DARFs.', 'Evitando a malha fina.'],
        'desconto_info' => '🔔 Atualizado para 2025!'
    ]
];

$cursos_recomendados = array_slice($cursos_todos, 0, 4, true);
$cursos_visualizados = array_slice($cursos_todos, 4, null, true);

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <title>Aprenda | YoungCash</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="css/estilo_cursos.css">
    </head>

<body>

    <header class="page-header-logado">
        <div class="container">
            <nav class="navbar navbar-expand-lg youngcash-navbar-logado">
                <a class="navbar-brand" href="home.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#targetNavegacaoEstudo" aria-controls="targetNavegacaoEstudo" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="targetNavegacaoEstudo">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="home.php">Início</a></li>
                        <li class="nav-item"><a class="nav-link active" href="cursos.php">Aprenda</a></li>
                        <li class="nav-item"><a class="nav-link" href="alunoPainel.php">Painel</a></li>
                        <li class="nav-item"><a class="nav-link" href="invest.php">Investimento</a></li>
                        <li class="nav-item"><a class="nav-link" href="suporte.php">Suporte</a></li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="carrinho.php" title="Meu Carrinho">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge badge-pill badge-danger" id="contador-carrinho-nav">0</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdownEstudo" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> Olá, <?php echo $nome_usuario_logado; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUserDropdownEstudo">
                                <a class="dropdown-item" href="perfil.php">Meu Perfil</a>
                                <a class="dropdown-item" href="settings.php">Configurações</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="sair.php" id="link-sair-cursos">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="page-content-cursos">
        <div class="carrinho-icon-container">
            <span class="icon-carrinho" onclick="window.location.href='carrinho.php'" title="Ver carrinho">
                <i class="fas fa-shopping-cart"></i>
            </span>
        </div>

        <h2 class="section-title-cursos">Recomendado para Você</h2>
        <div class="curso-container-scrollable">
            <?php if (!empty($cursos_recomendados)): ?>
                <?php foreach ($cursos_recomendados as $id => $curso): ?>
                    <div class="curso-card curso-card-clicavel" data-curso-id="<?php echo $id; // Usando o ID do array $cursos_todos 
                                                                                ?>">
                        <img src="<?php echo htmlspecialchars($curso['img']); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>">
                        <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        <p class="autor"><?php echo htmlspecialchars($curso['autor']); ?></p>
                        <p class="avaliacao"><?php echo htmlspecialchars($curso['avaliacao']); ?></p>
                        <p class="preco">
                            <span class="preco-atual"><?php echo htmlspecialchars($curso['preco_atual']); ?></span>
                            <?php if (!empty($curso['preco_antigo'])): ?>
                                <span class="preco-antigo"><?php echo htmlspecialchars($curso['preco_antigo']); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($curso['tag'])): ?>
                            <div class="btn-tag"><?php echo htmlspecialchars($curso['tag']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum curso recomendado no momento.</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title-cursos">Os Alunos Estão Visualizando</h2>
        <div class="curso-container-scrollable">
            <?php if (!empty($cursos_visualizados)): ?>
                <?php foreach ($cursos_visualizados as $id => $curso): ?>
                    <div class="curso-card curso-card-clicavel" data-curso-id="<?php echo $id; // Usando o ID do array $cursos_todos 
                                                                                ?>">
                        <img src="<?php echo htmlspecialchars($curso['img']); ?>" alt="<?php echo htmlspecialchars($curso['titulo']); ?>">
                        <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                        <p class="autor"><?php echo htmlspecialchars($curso['autor']); ?></p>
                        <p class="avaliacao"><?php echo htmlspecialchars($curso['avaliacao']); ?></p>
                        <p class="preco">
                            <span class="preco-atual"><?php echo htmlspecialchars($curso['preco_atual']); ?></span>
                            <?php if (!empty($curso['preco_antigo'])): ?>
                                <span class="preco-antigo"><?php echo htmlspecialchars($curso['preco_antigo']); ?></span>
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($curso['tag'])): ?>
                            <div class="btn-tag"><?php echo htmlspecialchars($curso['tag']); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum curso sendo visualizado no momento.</p>
            <?php endif; ?>
        </div>
    </main>

    <div class="modal fade" id="cursoModal" tabindex="-1" role="dialog" aria-labelledby="cursoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-yc">
                    <h5 class="modal-title" id="modal-curso-titulo">Título do Curso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-body-yc">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 curso-detalhe-main">
                                <h2 id="modal-curso-subtitulo" style="font-size: 1.2em; color: var(--yc-text-muted); margin-bottom:10px;"></h2>
                                <p class="autor">Criado por: <span id="modal-curso-autor"></span></p>
                                <p class="avaliacao"><span id="modal-curso-avaliacao"></span></p>
                                <div class="learn-box">
                                    <h4>O que você aprenderá</h4>
                                    <ul id="modal-curso-aprendera-lista"></ul>
                                </div>
                            </div>
                            <div class="col-md-4 curso-detalhe-sidebar">
                                <img src="" alt="Thumbnail do Curso" id="modal-curso-img" class="video-thumbnail" style="width:100%; height:auto; border-radius:var(--yc-border-radius); margin-bottom:15px;">
                                <div class="price-info">
                                    <div class="price"><span id="modal-curso-preco-atual"></span> <span class="old-price" id="modal-curso-preco-antigo"></span></div>
                                    <div class="discount" id="modal-curso-desconto"></div>
                                </div>
                                <button class="btn-modal btn-add-cart" id="modal-btn-add-carrinho">Adicionar ao carrinho</button>
                                <a href="#" id="modal-btn-comprar-agora-link"><button class="btn-modal btn-buy-now">Comprar agora</button></a>
                                <a href="#" id="modal-link-detalhes" class="btn btn-sm btn-outline-secondary btn-block mt-2" target="_blank">Ver página do curso completo</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="popupFeedbackCarrinho" class="yc-feedback-popup-overlay" style="display:none;">
        <div class="yc-feedback-popup-content">
            <span class="yc-popup-close" onclick="fecharPopupFeedbackCarrinho()">&times;</span>
            <h3 id="feedbackPopupTitulo"><i class="fas"></i> <span></span></h3>
            <p id="feedbackPopupMensagem"></p>
            <button onclick="fecharPopupFeedbackCarrinho()" class="btn-popup-ok">OK</button>
        </div>
    </div>

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
        window.youngCashCursosData = {
            cursos: <?php echo json_encode($cursos_todos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>
        };
    </script>
    <script src="js/script_cursos.js" defer></script>
</body>

</html>