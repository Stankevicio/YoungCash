<?php
session_start();

// Habilitar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está logado como estudante
if (!isset($_SESSION['usuario_id'], $_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'estudante') {
    header("Location: login.php");
    exit();
}

// Incluir config.php e verificar conexão PDO
if (file_exists('config.php')) {
    include_once('config.php');
} else {
    die("Erro crítico: Arquivo de configuração (config.php) não encontrado.");
}

if (!isset($conn) || !($conn instanceof PDO)) {
    die("Erro crítico: Conexão com o banco de dados não estabelecida ou inválida via config.php.");
}

$aluno_id = $_SESSION['usuario_id'];
$aluno = null;
$cursos_do_aluno_array = [];
$stats = [
    'total_cursos' => 0,
    'total_aulas_concluidas' => 0,
    'media_progresso' => 0.0
];
$iniciais = '';
$pagina_erro_mensagem = '';

try {
    // Buscar dados do aluno
    $stmt_aluno = $conn->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = :id AND tipo = :tipo_aluno");
    if (!$stmt_aluno) {
        throw new PDOException("Erro no prepare (aluno): " . implode(", ", $conn->errorInfo()));
    }
    $tipo_aluno_val = 'estudante';
    $stmt_aluno->bindParam(':id', $aluno_id, PDO::PARAM_INT);
    $stmt_aluno->bindParam(':tipo_aluno', $tipo_aluno_val, PDO::PARAM_STR);
    $stmt_aluno->execute();
    $aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);
    $stmt_aluno = null;

    if (!$aluno) {
        error_log("Aluno não encontrado com ID: " . $aluno_id . " ou tipo não é estudante para alunoPainel.php.");
        $_SESSION['error_message'] = "Erro: Aluno não encontrado ou acesso inválido.";
        header("Location: home.php");
        exit();
    }

    if (isset($aluno['nome'])) {
        $nomes = explode(' ', trim($aluno['nome']));
        if (count($nomes) > 0 && !empty($nomes[0])) {
            $iniciais = strtoupper(substr($nomes[0], 0, 1));
            if (count($nomes) > 1 && !empty(end($nomes))) {
                $iniciais .= strtoupper(substr(end($nomes), 0, 1));
            }
        } else {
            $iniciais = "A";
        }
    }

    // Buscar cursos do aluno
    $query_cursos_sql = "
        SELECT 
            c.id, c.nome, c.descricao, c.categoria, c.total_aulas,
            p.nome AS professor_nome,
            ac.data_inscricao, ac.progresso, ac.aulas_concluidas
        FROM cursos c
        JOIN aluno_cursos ac ON c.id = ac.curso_id
        LEFT JOIN usuarios p ON c.professor_id = p.id
        WHERE ac.aluno_id = :aluno_id
        ORDER BY c.nome ASC
    ";
    $stmt_cursos_aluno = $conn->prepare($query_cursos_sql);
    if (!$stmt_cursos_aluno) {
        throw new PDOException("Erro no prepare (cursos do aluno): " . implode(", ", $conn->errorInfo()));
    }
    $stmt_cursos_aluno->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);
    $stmt_cursos_aluno->execute();
    $cursos_do_aluno_array = $stmt_cursos_aluno->fetchAll(PDO::FETCH_ASSOC);
    $stmt_cursos_aluno = null;

    // Buscar estatísticas do aluno
    $query_stats_sql = "
        SELECT 
            COUNT(DISTINCT curso_id) AS total_cursos,
            IFNULL(SUM(aulas_concluidas), 0) AS total_aulas_concluidas,
            IFNULL(AVG(progresso), 0) AS media_progresso
        FROM aluno_cursos
        WHERE aluno_id = :aluno_id_stats
    ";
    $stmt_stats = $conn->prepare($query_stats_sql);
    if (!$stmt_stats) {
        throw new PDOException("Erro no prepare (estatísticas): " . implode(", ", $conn->errorInfo()));
    }
    $stmt_stats->bindParam(':aluno_id_stats', $aluno_id, PDO::PARAM_INT);
    $stmt_stats->execute();
    $stats_result = $stmt_stats->fetch(PDO::FETCH_ASSOC);
    if ($stats_result) {
        $stats = $stats_result;
    }
    $stmt_stats = null;

    // --- INÍCIO: DADOS DE EXEMPLO SE O ALUNO NÃO TIVER CURSOS (para visualização) ---
    if (empty($cursos_do_aluno_array)) {
        $cursos_do_aluno_array = [
            [
                'id' => 101,
                'nome' => 'Python para Data Science e Machine Learning COMPLETO',
                'descricao' => 'Organize suas finanças pessoais e comece a poupar.',
                'categoria' => 'Finanças Pessoais',
                'total_aulas' => 20,
                'professor_nome' => 'Prof. John',
                'data_inscricao' => date("Y-m-d H:i:s", strtotime("-5 days")),
                'progresso' => 75,
                'aulas_concluidas' => 15
            ],
            [
                'id' => 102,
                'nome' => 'Marketing Digital Completo Para Iniciantes 2024',
                'descricao' => 'Descubra como investir de forma inteligente.',
                'categoria' => 'Investimentos',
                'total_aulas' => 15,
                'professor_nome' => 'Profa. Demonstrativa',
                'data_inscricao' => date("Y-m-d H:i:s", strtotime("-2 days")),
                'progresso' => 33,
                'aulas_concluidas' => 5
            ],
            [
                'id' => 103,
                'nome' => 'Finanças Pessoais e Investimentos: Guia Completo',
                'descricao' => 'Garanta um futuro financeiro tranquilo.',
                'categoria' => 'Planejamento',
                'total_aulas' => 12,
                'professor_nome' => 'Prof. Demonstrativo',
                'data_inscricao' => date("Y-m-d H:i:s", strtotime("-10 days")),
                'progresso' => 10,
                'aulas_concluidas' => 1
            ]
        ];
        // Se os cursos de exemplo foram adicionados, recalcular as estatísticas para os cards de resumo
        if (empty($stats['total_cursos']) && !empty($cursos_do_aluno_array)) {
            $stats['total_cursos'] = count($cursos_do_aluno_array);
            $total_aulas_concluidas_exemplo = 0;
            $soma_progresso_exemplo = 0;
            foreach ($cursos_do_aluno_array as $ex_curso) {
                $total_aulas_concluidas_exemplo += (int)($ex_curso['aulas_concluidas'] ?? 0);
                $soma_progresso_exemplo += (float)($ex_curso['progresso'] ?? 0);
            }
            $stats['total_aulas_concluidas'] = $total_aulas_concluidas_exemplo;
            $stats['media_progresso'] = $stats['total_cursos'] > 0 ? round($soma_progresso_exemplo / $stats['total_cursos'], 1) : 0.0;
        }
    }
    // --- FIM: DADOS DE EXEMPLO ---

} catch (PDOException $e) {
    error_log("Erro PDO em alunoPainel.php: " . $e->getMessage());
    $pagina_erro_mensagem = "Ocorreu um erro ao buscar os dados da página. Por favor, tente novamente mais tarde.";
    // Para depuração: $pagina_erro_mensagem .= " Detalhe (dev): " . htmlspecialchars($e->getMessage());
}

$total_cursos = (int)($stats['total_cursos'] ?? 0);
$total_aulas_concluidas = (int)($stats['total_aulas_concluidas'] ?? 0);
$media_progresso = round(floatval($stats['media_progresso'] ?? 0), 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Aluno | YoungCash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo_alunoPainel.css">
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($iniciais ?: 'A'); ?>&background=FFD700&color=1A1A1A&bold=true&size=80" alt="Foto do Aluno" class="profile-img">
                <h3><?php echo htmlspecialchars($aluno['nome'] ?? 'Aluno'); ?></h3>
                <p><?php echo htmlspecialchars($aluno['tipo'] ?? 'Estudante'); ?></p>
            </div>
            <nav class="nav-menu">
                <a href="home.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''); ?>">
                    <i class="fas fa-home"></i> <span>Início</span>
                </a>
                <a href="alunoPainel.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'alunoPainel.php' ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i> <span>Meu Painel</span>
                </a>
                <a href="cursos.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'cursos.php' ? 'active' : ''); ?>">
                    <i class="fas fa-book-open-reader"></i> <span>Explorar Cursos</span>
                </a>
                <a href="alunoAgenda.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'alunoAgenda.php' ? 'active' : ''); ?>">
                    <i class="fas fa-calendar-alt"></i> <span>Agenda</span>
                </a>
                <a href="settings.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''); ?>">
                    <i class="fas fa-cog"></i> <span>Configurações</span>
                </a>
                <a href="sair.php" class="nav-item" id="link-sair-painel">
                    <i class="fas fa-sign-out-alt"></i> <span>Sair</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1><i class="fas fa-tachometer-alt"></i> Painel do Aluno</h1>
            </header>

            <?php if (!empty($pagina_erro_mensagem)): ?>
                <div class="alert alert-danger" role="alert" style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($pagina_erro_mensagem); ?>
                </div>
            <?php endif; ?>

            <section class="cards-container">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Cursos Inscritos</h3>
                            <h2><?php echo $total_cursos; ?></h2>
                        </div>
                        <div class="card-icon"><i class="fas fa-book-open"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Aulas Concluídas</h3>
                            <h2><?php echo $total_aulas_concluidas; ?></h2>
                        </div>
                        <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Progresso Médio</h3>
                            <h2><?php echo $media_progresso; ?>%</h2>
                        </div>
                        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Novos Cursos</h3>
                            <h2>Explorar</h2>
                        </div>
                        <div class="card-icon"><i class="fas fa-search"></i></div>
                    </div>
                    <button class="btn btn-primary" style="width: 100%; margin-top: 10px;" onclick="window.location.href='cursos.php'">
                        <i class="fas fa-plus"></i> Descobrir
                    </button>
                </div>
            </section>

            <section class="cursos-lista-container">
                <h2 class="cursos-section-title"><i class="fas fa-list-alt"></i> Meus Cursos em Andamento</h2>

                <div class="cursos-grid">
                    <?php if (!empty($cursos_do_aluno_array)): ?>
                        <?php foreach ($cursos_do_aluno_array as $curso): ?>
                            <?php
                            $data_inscricao_curso = $curso['data_inscricao'] ?? null;
                            $isNew = $data_inscricao_curso ? (strtotime($data_inscricao_curso) > strtotime('-7 days')) : false;
                            $progresso = round(floatval($curso['progresso'] ?? 0), 1);
                            ?>
                            <a href="aula.php?curso_id=<?php echo htmlspecialchars($curso['id']); ?>&aula=<?php echo (int)($curso['aulas_concluidas'] ?? 0) + 1; ?>" class="curso-card" title="Acessar curso: <?php echo htmlspecialchars($curso['nome'] ?? ($curso['nome_curso'] ?? '')); ?>">
                                <?php if ($isNew): ?><span class="badge badge-new">Novo</span><?php endif; ?>
                                <div class="curso-imagem">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="curso-content">
                                    <span class="curso-categoria"><?php echo htmlspecialchars($curso['categoria'] ?? 'Indefinida'); ?></span>
                                    <h3 class="curso-title"><?php echo htmlspecialchars($curso['nome'] ?? ($curso['nome_curso'] ?? 'Nome do Curso')); ?></h3>
                                    <p class="curso-professor"><strong>Professor:</strong> <?php echo htmlspecialchars($curso['professor_nome'] ?? 'N/A'); ?></p>
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $progresso; ?>%"></div>
                                        </div>
                                        <div class="progress-text"><?php echo $progresso; ?>% completo</div>
                                    </div>
                                    <div class="curso-stats">
                                        <div class="curso-stat"><i class="fas fa-play-circle"></i> <?php echo (int)($curso['aulas_concluidas'] ?? 0); ?>/<?php echo (int)($curso['total_aulas'] ?? 0); ?> aulas</div>
                                        <div class="curso-stat"><i class="fas fa-calendar-alt"></i> Inscrito em: <?php echo $data_inscricao_curso ? date('d/m/Y', strtotime($data_inscricao_curso)) : 'N/A'; ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-book-open"></i>
                            <h3>Você ainda não está matriculado em nenhum curso.</h3>
                            <p>Explore nossa plataforma e encontre cursos que combinam com seus interesses!</p>
                            <button class="btn btn-primary" onclick="window.location.href='cursos.php'">
                                <i class="fas fa-search"></i> Explorar Cursos
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Script para limpar carrinho no logout
        const linkSairPainel = document.getElementById('link-sair-painel');
        if (linkSairPainel) {
            linkSairPainel.addEventListener('click', function() {
                localStorage.removeItem('cursosNoCarrinho'); // Use a chave correta do seu carrinho
                console.log('Carrinho do localStorage limpo ao sair do painel.');
            });
        }
    </script>
</body>

</html>