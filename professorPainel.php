<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['tipo_usuario'] != 'professor') {
    // Idealmente, crie uma página específica para acesso negado
    // header("Location: acesso_negado.php"); 
    echo "Acesso negado. Esta área é apenas para professores."; // Mensagem simples por enquanto
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
$alunos_array = [];
$total_alunos = 0;
$cursos_por_aluno = [];

try {
    // Buscar dados do professor
    $stmt_professor = $conn->prepare("SELECT id, nome, email, tipo FROM usuarios WHERE id = :id AND tipo = :tipo_professor");
    $tipo_prof_val = 'Professor';
    $stmt_professor->bindParam(':id', $professor_id, PDO::PARAM_INT);
    $stmt_professor->bindParam(':tipo_professor', $tipo_prof_val, PDO::PARAM_STR);
    $stmt_professor->execute();
    $professor = $stmt_professor->fetch(PDO::FETCH_ASSOC);

    if (!$professor) {
        // Isso pode acontecer se o ID na sessão não corresponder a um professor
        error_log("Tentativa de acesso à homeProfessor com ID de sessão inválido ou não professor: " . $professor_id);
        echo "Erro: Não foi possível carregar os dados do professor.";
        exit();
    }

    // Buscar alunos cadastrados
    $query_alunos_sql = "SELECT id, nome, email, telefone FROM usuarios WHERE tipo = :tipo_aluno ORDER BY nome ASC";
    $stmt_alunos = $conn->prepare($query_alunos_sql);
    $tipo_aluno_val = 'estudante';
    $stmt_alunos->bindParam(':tipo_aluno', $tipo_aluno_val, PDO::PARAM_STR);
    $stmt_alunos->execute();
    $alunos_array = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);
    $total_alunos = $stmt_alunos->rowCount();

    // Buscar cursos dos alunos (exemplo simplificado)
    // ATENÇÃO: As tabelas 'cursos' e 'aluno_cursos' precisam existir!
    $query_cursos_sql = "SELECT c.nome as curso_nome, u.nome as aluno_nome, u.id as aluno_id 
                         FROM cursos c
                         JOIN aluno_cursos ac ON c.id = ac.curso_id
                         JOIN usuarios u ON ac.aluno_id = u.id
                         WHERE u.tipo = :tipo_aluno_cursos
                         ORDER BY u.nome ASC";
    $stmt_cursos = $conn->prepare($query_cursos_sql);
    $tipo_aluno_cursos_val = 'estudante'; // Para garantir que estamos pegando cursos de estudantes
    $stmt_cursos->bindParam(':tipo_aluno_cursos', $tipo_aluno_cursos_val, PDO::PARAM_STR);
    $stmt_cursos->execute();

    while ($row = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
        $cursos_por_aluno[$row['aluno_id']][] = $row['curso_nome'];
    }

} catch (PDOException $e) {
    error_log("Erro PDO na homeProfessor.php: " . $e->getMessage());
    // Em produção, não exiba $e->getMessage() diretamente ao usuário.
    die("Ocorreu um erro ao buscar os dados da página. Por favor, tente novamente mais tarde. Detalhe (dev): " . $e->getMessage());
}
$stmt_professor = null;
$stmt_alunos = null;
$stmt_cursos = null;
// $conn = null; // Não feche a conexão aqui se o restante da página precisar dela. Geralmente o PHP fecha no final do script.

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Professor | YoungCash</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/estilo_alunoPainel.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($professor['nome'] ?? 'P'); ?>&background=FFD700&color=1A1A1A&bold=true&size=80" alt="Foto do Professor" class="profile-img">
                <h3><?php echo htmlspecialchars($professor['nome'] ?? 'Nome Professor'); ?></h3>
                <p><?php echo htmlspecialchars($professor['tipo'] ?? 'Professor'); ?></p>
            </div>
            
            <nav class="nav-menu">
                <a href="homeProfessor.php" class="nav-item active">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Monitoramento</span>
                </a>
                <a href="professorCalendario.php" class="nav-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Agenda</span>
                </a>
                <a href="configuracoes.php" class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </a>
                <a href="sair.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1><i class="fas fa-tachometer-alt"></i> Monitoramento de Alunos</h1>
            </div>
            
            <div class="cards-container">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3>Total de Alunos</h3>
                            <h2><?php echo $total_alunos; ?></h2>
                        </div>
                        <div class="card-icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div><h3>Cursos Ativos (Exemplo)</h3><h2>15</h2></div>
                        <div class="card-icon"><i class="fas fa-book-open"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div><h3>Atividades Pendentes (Exemplo)</h3><h2>8</h2></div>
                        <div class="card-icon"><i class="fas fa-tasks"></i></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div><h3>Média de Progresso (Exemplo)</h3><h2>78%</h2></div>
                        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>
            </div>
            
            <div class="table-container">
                <h2><i class="fas fa-user-graduate"></i> Lista de Alunos</h2>
                <p>Visualize todos os alunos cadastrados na plataforma e seus cursos.</p>
                
                <table id="tabelaAlunos">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Cursos Inscritos</th>
                            <th>Progresso (Exemplo)</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($alunos_array)): ?>
                            <?php foreach ($alunos_array as $aluno): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                                <td><?php echo htmlspecialchars($aluno['telefone'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($cursos_por_aluno[$aluno['id']])): ?>
                                        <?php foreach ($cursos_por_aluno[$aluno['id']] as $curso_nome): ?>
                                            <span class="badge badge-primary"><?php echo htmlspecialchars($curso_nome); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span>Nenhum curso</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $progresso = rand(30, 100); // Progresso aleatório para exemplo ?>
                                    <div style="background: #eee; border-radius: 10px; height: 10px; width: 100%;">
                                        <div style="background: var(--primary); width: <?php echo $progresso; ?>%; height: 100%; border-radius: 10px;"></div>
                                    </div>
                                    <small><?php echo $progresso; ?>% completo</small>
                                </td>
                                <td>
                                    <button class="action-btn view" onclick="verAluno(<?php echo $aluno['id']; ?>)">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                    <button class="action-btn edit" onclick="editarAluno(<?php echo $aluno['id']; ?>)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">Nenhum aluno encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelaAlunos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                responsive: true
            });
        });
        
        function verAluno(id) {
            // Você pode redirecionar ou abrir um modal com detalhes
            // window.location.href = 'detalhes_aluno.php?id=' + id;
            alert('Ver detalhes do aluno ID: ' + id); // Exemplo
        }
        
        function editarAluno(id) {
            // window.location.href = 'editar_aluno.php?id=' + id;
            alert('Editar aluno ID: ' + id); // Exemplo
        }
    </script>
</body>
</html>