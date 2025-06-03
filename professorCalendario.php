<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'professor') {
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

$professor_id = $_SESSION['usuario_id'];
$professor = null;
$iniciais = '';
$notas = []; // Para armazenar as notas do calendário
$pagina_erro_mensagem = ''; // Para mensagens de erro do try-catch

try {
    // Buscar dados do professor
    $stmt_professor = $conn->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = :id AND tipo = :tipo_professor");
    if (!$stmt_professor) {
        throw new PDOException("Erro no prepare (professor): " . implode(", ", $conn->errorInfo()));
    }

    $tipo_professor_val = 'professor'; // Garante que estamos buscando um professor
    $stmt_professor->bindParam(':id', $professor_id, PDO::PARAM_INT);
    $stmt_professor->bindParam(':tipo_professor', $tipo_professor_val, PDO::PARAM_STR);
    $stmt_professor->execute();
    $professor = $stmt_professor->fetch(PDO::FETCH_ASSOC);
    $stmt_professor = null;

    if (!$professor) {
        error_log("Professor não encontrado com ID: " . $professor_id . " para professorCalendario.php.");
        // Redirecionar ou mostrar mensagem amigável
        $_SESSION['error_message'] = "Erro: Dados do professor não encontrados ou acesso inválido.";
        header("Location: login.php"); // Ou uma página de erro apropriada
        exit();
    }

    // Extrair iniciais para o avatar
    if (isset($professor['nome'])) {
        $nomes = explode(' ', trim($professor['nome']));
        if (count($nomes) > 0 && !empty($nomes[0])) {
            $iniciais = strtoupper(substr($nomes[0], 0, 1));
            if (count($nomes) > 1 && !empty(end($nomes))) {
                $iniciais .= strtoupper(substr(end($nomes), 0, 1));
            }
        } else {
            $iniciais = "P";
        } // Fallback para iniciais
    }

    // Processar notas do calendário (via POST AJAX)
    
} catch (PDOException $e) {
    error_log("Erro PDO em professorCalendario.php: " . $e->getMessage());
    $pagina_erro_mensagem = "Ocorreu um erro ao processar os dados da agenda. Por favor, tente novamente mais tarde.";
    // Em produção, você pode querer apenas logar o erro e não usar die()
    // die($pagina_erro_mensagem . " Detalhe (dev): " . htmlspecialchars($e->getMessage())); 
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agenda do Professor | YoungCash</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" />
    <link rel="stylesheet" href="css/estilo_professorPainel.css">

</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="profile">
                <div class="avatar"><?php echo htmlspecialchars($iniciais ?: 'P'); ?></div>
                <div class="profile-name"><?php echo htmlspecialchars($professor['nome'] ?? 'Professor'); ?></div>
                <div class="profile-role"><?php echo htmlspecialchars($professor['tipo'] ?? 'Professor'); ?></div>
            </div>
            <nav class="nav-menu">
                <a href="professorPainel.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'homeProfessor.php' ? 'active' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Monitoramento</span>
                </a>
                <a href="professorCalendario.php" class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'professorCalendario.php' ? 'active' : ''); ?>">
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
                <h1><i class="fas fa-calendar-alt"></i> Agenda do Professor</h1>
            </header>

            <?php if (!empty($pagina_erro_mensagem)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($pagina_erro_mensagem); ?>
                </div>
            <?php endif; ?>

            <div id="calendar"></div>
        </main>
    </div>

    <div id="modal-notas" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title-notas" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modal-title-notas">Adicionar / Editar Nota</h2>
                <button type="button" class="close-modal" aria-label="Fechar modal">&times;</button>
            </div>
            <form id="form-nota">
                <div class="form-group">
                    <label for="nota-text">Nota para <span id="data-selecionada-display"></span>:</label>
                    <textarea id="nota-text" name="nota" class="form-control" placeholder="Digite sua nota aqui..."></textarea>
                </div>
                <input type="hidden" id="data-input" name="data_selecionada" />
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary close-modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Nota</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('modal-notas');
            const closeModalButtons = modal.querySelectorAll('.close-modal');
            const formNota = document.getElementById('form-nota');
            const notaText = document.getElementById('nota-text');
            const dataSelecionadaSpan = document.getElementById('data-selecionada-display'); // Para exibir a data formatada
            const dataInput = document.getElementById('data-input'); // Input hidden para o form

            // Notas do servidor (PHP -> JS)
            const notasDoProfessor = <?php echo json_encode($notas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE); ?>;

            // Mapear notas para eventos do FullCalendar
            const eventosParaCalendario = Object.keys(notasDoProfessor).map(dataISO => {
                // Se a nota for apenas um placeholder ou vazia, não crie um evento visível, apenas marque o dia.
                // Se quiser eventos visíveis para todas as notas, ajuste esta lógica.
                if (notasDoProfessor[dataISO] && notasDoProfessor[dataISO].trim() !== '') {
                    return {
                        title: '📝 Nota', // Ou um trecho da nota
                        start: dataISO,
                        allDay: true,
                        extendedProps: {
                            nota: notasDoProfessor[dataISO]
                        },
                        classNames: ['has-note'], // Para estilização customizada se necessário
                        display: 'list-item', // Ou 'block' para um evento mais visível
                        color: 'var(--secondary)' // Laranja para notas
                    };
                }
                return null; // Para não criar evento se a nota for vazia
            }).filter(evento => evento !== null); // Remove entradas nulas

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: eventosParaCalendario,
                dateClick: function(info) {
                    // info.dateStr é a data clicada no formato YYYY-MM-DD
                    const notaExistente = notasDoProfessor[info.dateStr] || '';
                    openModal(info.dateStr, notaExistente);
                },
                eventClick: function(info) {
                    // info.event.startStr é a data do evento
                    openModal(info.event.startStr, info.event.extendedProps.nota);
                },
                dayCellDidMount: function(info) {
                    // Adiciona um indicador visual (ponto) para dias com notas
                    if (notasDoProfessor[info.dateStr] && notasDoProfessor[info.dateStr].trim() !== '') {
                        info.el.classList.add('has-note-dot');
                    }
                },
                navLinks: true,
                editable: false, // Se o professor pode arrastar notas para outras datas
                dayMaxEvents: true
            });

            calendar.render();

            function openModal(dataISO, notaAtual = '') {
                const dataFormatada = new Date(dataISO + 'T00:00:00').toLocaleDateString('pt-BR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                if (dataSelecionadaSpan) dataSelecionadaSpan.textContent = dataFormatada;
                if (dataInput) dataInput.value = dataISO;
                if (notaText) notaText.value = notaAtual;
                if (modal) modal.style.display = 'flex';
                if (notaText) notaText.focus();
            }

            closeModalButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (modal) modal.style.display = 'none';
                });
            });

            if (formNota) {
                formNota.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(formNota);

                    fetch('professorCalendario.php', { // Envia para o próprio script
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const eventDate = data.data;
                                const notaSalva = data.nota;

                                notasDoProfessor[eventDate] = notaSalva; // Atualiza o array local de notas

                                // Remove eventos antigos para esta data (se houver)
                                const eventsToRemove = calendar.getEvents().filter(ev => ev.startStr === eventDate);
                                eventsToRemove.forEach(ev => ev.remove());

                                // Adiciona novo evento se a nota não estiver vazia
                                if (notaSalva.trim() !== '') {
                                    calendar.addEvent({
                                        title: '📝 Nota',
                                        start: eventDate,
                                        allDay: true,
                                        extendedProps: {
                                            nota: notaSalva
                                        },
                                        classNames: ['has-note'],
                                        display: 'list-item',
                                        color: 'var(--secondary)'
                                    });
                                }

                                // Re-renderiza a célula do dia para atualizar o indicador de ponto
                                const dayCell = calendarEl.querySelector(`.fc-day[data-date="${eventDate}"]`);
                                if (dayCell) {
                                    if (notaSalva.trim() !== '') {
                                        dayCell.classList.add('has-note-dot');
                                    } else {
                                        dayCell.classList.remove('has-note-dot');
                                    }
                                }
                                calendar.render(); // Re-renderiza o calendário para garantir atualização visual

                                if (modal) modal.style.display = 'none';
                            } else {
                                alert('Erro ao salvar a nota. Tente novamente.');
                            }
                        })
                        .catch(error => {
                            console.error('Erro na comunicação AJAX:', error);
                            alert('Erro na comunicação com o servidor ao salvar a nota.');
                        });
                });
            }

            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>

</html>