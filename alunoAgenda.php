<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se o usuário está logado e é aluno
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'estudante') {
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
$eventos_calendario = [];
$iniciais = ''; // Inicializar para evitar warning se $aluno não for encontrado

try {
    // Buscar dados do aluno
    $stmt_aluno = $conn->prepare("SELECT id, nome, email FROM usuarios WHERE id = :id AND tipo = :tipo_aluno");
    if (!$stmt_aluno) {
        throw new PDOException("Erro no prepare (aluno): " . implode(", ", $conn->errorInfo()));
    }

    $tipo_aluno_val = 'estudante';
    $stmt_aluno->bindParam(':id', $aluno_id, PDO::PARAM_INT);
    $stmt_aluno->bindParam(':tipo_aluno', $tipo_aluno_val, PDO::PARAM_STR);
    $stmt_aluno->execute();
    $aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        error_log("Aluno não encontrado com ID: " . $aluno_id . " ou tipo não é estudante para alunoAgenda.php.");
        // Em vez de die, você pode redirecionar ou mostrar uma mensagem mais amigável
        $_SESSION['error_message'] = "Erro: Aluno não encontrado ou acesso inválido.";
        header("Location: home.php"); // Ou uma página de erro
        exit();
    }
    $stmt_aluno = null;

    // Extrair iniciais para o avatar APÓS buscar $aluno
    if (isset($aluno['nome'])) {
        $nomes = explode(' ', $aluno['nome']);
        if (count($nomes) > 0) {
            $iniciais = strtoupper(substr($nomes[0], 0, 1));
            if (count($nomes) > 1) {
                $iniciais .= strtoupper(substr(end($nomes), 0, 1));
            }
        }
    }


    // Buscar eventos (ajuste esta query para buscar eventos relevantes para o aluno ou cursos dele)
    // ATENÇÃO: A tabela 'eventos' precisa existir!
    $query_eventos_sql = "SELECT id, nome AS title, data_evento AS start, descricao, curso_associado, cor 
                          FROM eventos 
                          ORDER BY data_evento ASC";
    // Se quiser eventos do aluno: WHERE aluno_id = :aluno_id OR aluno_id IS NULL

    $stmt_eventos = $conn->prepare($query_eventos_sql);
    if (!$stmt_eventos) {
        throw new PDOException("Erro na preparação da consulta de eventos: " . implode(", ", $conn->errorInfo()));
    }

    // Se filtrar por aluno_id:
    // $stmt_eventos->bindParam(':aluno_id', $aluno_id, PDO::PARAM_INT);

    $stmt_eventos->execute();
    $eventos_do_banco = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
    $stmt_eventos = null;

    foreach ($eventos_do_banco as $evento) {
        $eventos_calendario[] = [
            'id' => $evento['id'],
            'title' => $evento['title'],
            'start' => $evento['start'],
            // 'end' => $evento['data_fim_evento_se_existir'], // Se seus eventos tiverem duração
            'description' => $evento['descricao'] ?? '',
            'color' => $evento['cor'] ?? '#FFD700', // Cor do evento, ou um padrão
            'extendedProps' => [
                'curso' => $evento['curso_associado'] ?? 'Evento Geral'
            ]
        ];
    }
} catch (PDOException $e) {
    error_log("Erro PDO em alunoAgenda.php: " . $e->getMessage());
    // Mostrar uma mensagem amigável para o usuário, mas logar o erro real
    $pagina_erro_mensagem = "Ocorreu um erro ao buscar os dados da agenda. Por favor, tente novamente mais tarde.";
    // die($pagina_erro_mensagem . " Detalhe (dev): " . htmlspecialchars($e->getMessage())); // Para depuração
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Agenda | YoungCash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />

    <link rel="stylesheet" href="css/estilo_alunoAgenda.css">
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($iniciais ?: 'A'); ?>&background=FFD700&color=1A1A1A&bold=true&size=80" alt="Foto do Aluno" class="profile-img">
                <h3><?php echo htmlspecialchars($aluno['nome'] ?? 'Aluno'); ?></h3>
                <p>Estudante</p>
            </div>
            <nav class="nav-menu">
                <a href="home.php" class="nav-item">
                    <i class="fas fa-home"></i> <span>Início</span>
                </a>
                <a href="alunoPainel.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'alunoPainel.php' ? 'active' : ''); ?>">
                    <i class="fas fa-tachometer-alt"></i> <span>Meu Painel</span>
                </a>
                <a href="alunoAgenda.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'alunoAgenda.php' ? 'active' : ''); ?>">
                    <i class="fas fa-calendar-alt"></i> <span>Agenda</span>
                </a>
                <a href="configuracoes.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''); ?>">
                    <i class="fas fa-cog"></i> <span>Configurações</span>
                </a>
                <a href="sair.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> <span>Sair</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1><i class="fas fa-calendar-check"></i> Minha Agenda</h1>
            </header>

            <?php if (isset($pagina_erro_mensagem)): // Exibe erro geral, se houver 
            ?>
                <p style="color:red; background:pink; padding:10px;"><?php echo htmlspecialchars($pagina_erro_mensagem); ?></p>
            <?php endif; ?>

            <div id='calendar'></div>
        </main>
    </div>

    <div id="modal-evento" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="evento-titulo"></h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>Curso/Referência:</strong> <span id="evento-curso"></span></p>
                <p><strong>Data:</strong> <span id="evento-data"></span></p>
                <p><strong>Descrição:</strong> <span id="evento-descricao"></span></p>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/pt-br.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('modal-evento');
            const closeButtons = document.querySelectorAll('.close-modal');

            const eventosParaCalendario = <?php echo json_encode($eventos_calendario, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;

            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'pt-br',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    events: eventosParaCalendario,
                    eventClick: function(info) {
                        if (modal) { // Verifica se o modal existe
                            document.getElementById('evento-titulo').textContent = info.event.title;
                            document.getElementById('evento-curso').textContent = info.event.extendedProps.curso || 'Não especificado';
                            document.getElementById('evento-data').textContent = info.event.start ? info.event.start.toLocaleString('pt-BR', {
                                dateStyle: 'long',
                                timeStyle: 'short'
                            }) : 'Data não disponível'; // Formato mais amigável
                            document.getElementById('evento-descricao').textContent = info.event.extendedProps.description || 'Sem descrição adicional.';
                            modal.style.display = 'flex';
                        }
                    },
                    navLinks: true,
                    editable: false,
                    selectable: true,
                    dayMaxEvents: true,
                    contentHeight: 'auto', // Para o calendário se ajustar melhor ao conteúdo
                    eventColor: '#394263', // Cor padrão para eventos (pode ser var(--yc-primary))
                    eventTimeFormat: { // Formato de hora
                        hour: '2-digit',
                        minute: '2-digit',
                        meridiem: false, // usa formato 24h
                        hour12: false
                    }
                });
                calendar.render();
            } else {
                console.error("Elemento #calendar não encontrado no DOM.");
            }

            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (modal) modal.style.display = 'none';
                });
            });

            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    if (modal) modal.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>