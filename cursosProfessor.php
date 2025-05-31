<?php
// Ativar exibição de erros para desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar se o usuário está logado e é professor
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'professor') {
    header("Location: login.php");
    exit();
}

include_once('config.php');

// Processar formulário de criação de curso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $professor_id = $_SESSION['usuario_id'];

    $query = "INSERT INTO cursos (nome, descricao, professor_id) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conexao, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $nome, $descricao, $professor_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: meus_cursos.php?sucesso=1");
        exit();
    } else {
        $erro = "Erro ao criar curso: " . mysqli_error($conexao);
    }
}

// Buscar dados do professor
$professor_id = $_SESSION['usuario_id'];
$query_professor = "SELECT * FROM usuarios WHERE id = ?";
$stmt_professor = mysqli_prepare($conexao, $query_professor);
mysqli_stmt_bind_param($stmt_professor, "i", $professor_id);
mysqli_stmt_execute($stmt_professor);
$result_professor = mysqli_stmt_get_result($stmt_professor);
$professor = mysqli_fetch_assoc($result_professor);

// Extrair iniciais para o avatar
$iniciais = '';
$nomes = explode(' ', $professor['nome']);
if (count($nomes) > 0) {
    $iniciais = strtoupper(substr($nomes[0], 0, 1));
    if (count($nomes) > 1) {
        $iniciais .= strtoupper(substr(end($nomes), 0, 1));
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Curso | YoungCash</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/estilo_cursosProfessor.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar Estilo Moderno -->
        <div class="sidebar">
            <div class="profile">
                <div class="avatar"><?= $iniciais ?></div>
                <div class="profile-name"><?= htmlspecialchars($professor['nome']) ?></div>
                <div class="profile-role">Professor</div>
            </div>

            <div class="nav-menu">
                <div class="nav-item">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <a href="professorPainel.php"><span>Monitoramento</span></a>
                </div>
                <div class="nav-item active">
                    <i class="fas fa-book"></i>
                    <span>Meus Cursos</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-calendar-alt"></i>
                    <a href="professorCalendario.php"><span>Agenda</span></a>
                </div>
                <div class="nav-item">
                    <i class="fas fa-cog"></i>
                    <span>Configurações</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1><i class="fas fa-plus-circle"></i> Criar Novo Curso</h1>
                <a href="meus_cursos.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>

            <div class="form-container">
                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?= $erro ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nome">Nome do Curso</label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Descrição do Curso</label>
                        <textarea id="descricao" name="descricao" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 12px 25px;">
                        <i class="fas fa-save"></i> Criar Curso
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>