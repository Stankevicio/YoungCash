<?php
session_start(); // Iniciar sessão para potencial uso de mensagens de feedback

// Habilitar exibição de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir config.php (que deve definir $conn como objeto PDO)
if (file_exists('config.php')) {
    include_once('config.php');
} else {
    $critical_error_message = "Erro crítico: Arquivo de configuração não encontrado.";
    // Não podemos usar a sessão para feedback aqui se session_start() não foi chamado ou se o erro é muito cedo.
    // Se config.php é essencial, podemos ter que parar.
}

// Inicialização de variáveis para repopular o formulário e para mensagens
$tipo_usuario = $_POST['tipo_usuario'] ?? 'estudante';
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$data_nasc = $_POST['data_nascimento'] ?? '';
$senha_digitada_cadastro = ''; // Será pego do POST apenas se o método for POST

$feedback_message = ''; // Para exibir mensagens de sucesso ou erro

// Recuperar mensagem de feedback da sessão (se houver um redirecionamento anterior ou para consistência)
if (isset($_SESSION['cadastro_feedback'])) {
    $alert_class = $_SESSION['cadastro_feedback']['type'] === 'success' ? 'alert-success' : 'alert-danger';
    $feedback_message = "<div class='alert " . $alert_class . "'>" . htmlspecialchars($_SESSION['cadastro_feedback']['message']) . "</div>";
    unset($_SESSION['cadastro_feedback']);
}
if (isset($critical_error_message) && empty($feedback_message)) { // Se config.php falhou
    $feedback_message = "<div class='alert alert-danger'>" . htmlspecialchars($critical_error_message) . "</div>";
}


// Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && !isset($critical_error_message)) {

    if (!isset($conn) || !($conn instanceof PDO)) {
        $_SESSION['cadastro_feedback'] = ['type' => 'error', 'message' => 'Erro de sistema: Conexão com o banco de dados não disponível. (#CADPOST)'];
        header("Location: cadastro.php");
        exit();
    }

    $senha_digitada_cadastro = $_POST['senha'] ?? '';
    $certificado_destino_db = NULL; // Resetar para cada submissão
    $current_error = '';

    if (empty($nome) || empty($email) || empty($telefone) || empty($data_nasc) || empty($senha_digitada_cadastro)) {
        $current_error = "Todos os campos obrigatórios devem ser preenchidos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $current_error = "Formato de e-mail inválido.";
    }

    if ($tipo_usuario == 'professor' && empty($current_error)) {
        if (!isset($_FILES['certificado']) || empty($_FILES['certificado']['name']) || $_FILES['certificado']['error'] != UPLOAD_ERR_OK) {
            $current_error = "Para professores, o envio do certificado é obrigatório e deve ser um arquivo válido. Código do erro: " . ($_FILES['certificado']['error'] ?? 'Nenhum arquivo enviado');
        } else {
            $certificado_nome = $_FILES['certificado']['name'];
            $certificado_tmp = $_FILES['certificado']['tmp_name'];
            $caminho_base_upload = 'uploads/certificados/';
            $diretorio_upload_absoluto = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $caminho_base_upload;

            if (!is_dir($diretorio_upload_absoluto)) {
                if (!mkdir($diretorio_upload_absoluto, 0775, true)) {
                    $current_error = "Falha ao criar o diretório de certificados: " . htmlspecialchars($diretorio_upload_absoluto) . ". Verifique as permissões.";
                }
            }

            if (empty($current_error) && !is_writable($diretorio_upload_absoluto)) {
                $current_error = "Diretório de certificados (" . htmlspecialchars($diretorio_upload_absoluto) . ") não tem permissão de escrita.";
            }

            if (empty($current_error)) {
                $extensoes_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];
                $extensao = strtolower(pathinfo($certificado_nome, PATHINFO_EXTENSION));

                if (!in_array($extensao, $extensoes_permitidas)) {
                    $current_error = "Tipo de arquivo não permitido para certificado. Apenas PDF, JPG, JPEG ou PNG.";
                } else {
                    $tamanho_maximo = 5 * 1024 * 1024; // 5MB
                    if ($_FILES['certificado']['size'] > $tamanho_maximo) {
                        $current_error = "O arquivo de certificado é muito grande. Tamanho máximo: 5MB.";
                    } else {
                        $certificado_nome_unico = uniqid('cert_', true) . '.' . $extensao;
                        $certificado_destino_fisico = $diretorio_upload_absoluto . $certificado_nome_unico;

                        if (!move_uploaded_file($certificado_tmp, $certificado_destino_fisico)) {
                            $current_error = "Falha ao salvar o certificado. Erro ao mover o arquivo.";
                        } else {
                            $certificado_destino_db = $caminho_base_upload . $certificado_nome_unico;
                        }
                    }
                }
            }
        }
    }

    if (empty($current_error)) {
        $senha_hashed = password_hash($senha_digitada_cadastro, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios(tipo, nome, email, telefone, data_nasc, senha, certificado) 
                VALUES (:tipo, :nome, :email, :telefone, :data_nasc, :senha, :certificado)";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':tipo' => $tipo_usuario,
                ':nome' => $nome,
                ':email' => $email,
                ':telefone' => $telefone,
                ':data_nasc' => $data_nasc,
                ':senha' => $senha_hashed,
                ':certificado' => $certificado_destino_db
            ]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['cadastro_feedback'] = ['type' => 'success', 'message' => "Cadastro de '$nome' realizado com sucesso! Você já pode fazer login"];
                // Limpar campos para o formulário após o redirecionamento (se a sessão for usada para repopular)
                // Ou resetar as variáveis aqui se não for redirecionar
                $_POST = array(); // Limpa o POST para evitar re-submissão acidental
                $nome = $email = $telefone = $data_nasc = '';
                // $tipo_usuario = 'estudante'; // Resetar para o padrão ou manter a seleção
                $certificado_destino_db = NULL;
                header("Location: cadastro.php"); // Recarrega para mostrar mensagem de sucesso e limpar form
                exit();
            } else {
                // Isso pode acontecer se a query não afetar linhas mas não der erro SQL
                $_SESSION['cadastro_feedback'] = ['type' => 'error', 'message' => 'Não foi possível realizar o cadastro. Nenhuma linha afetada. (#CINS0)'];
            }
        } catch (PDOException $e) {
            error_log("Erro PDO no cadastro.php: " . $e->getMessage());
            if ($e->errorInfo[1] == 1062) {
                $_SESSION['cadastro_feedback'] = ['type' => 'error', 'message' => 'Este e-mail já está cadastrado. Tente outro ou faça <a href="login.php" class="alert-link">login</a>.'];
            } else {
                $_SESSION['cadastro_feedback'] = ['type' => 'error', 'message' => 'Erro ao processar seu cadastro (#CPDO). Detalhe: ' . htmlspecialchars($e->getMessage())];
            }
        }
        $stmt = null;
        header("Location: cadastro.php"); // Recarrega para mostrar mensagem de erro da sessão
        exit();
    } else {
        // Se houve erro de validação ou upload, armazena na sessão para exibir
        $_SESSION['cadastro_feedback'] = ['type' => 'error', 'message' => $current_error];
        header("Location: cadastro.php"); // Recarrega para mostrar o erro e repopular o form
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Cadastro | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/estilo_cadastro.css">
</head>

<body>

    <header class="page-header">
        <div class="container">
            <nav class="navbar navbar-expand-md youngcash-navbar">
                <a class="navbar-brand" href="index.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#target-navegacao-cadastro" aria-controls="target-navegacao-cadastro" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="target-navegacao-cadastro">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cadastro.php' ? 'active-cadastro' : ''); ?>" href="cadastro.php">Cadastre-se</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="planos.php">Planos</a>
                        </li>
                    </ul>
                    <a href="login.php" class="btn btn-entrar-custom">Entrar</a>
                </div>
            </nav>
            <h1>Crie Sua Conta</h1>
            <p class="subtitle">Faça parte da comunidade YoungCash e transforme sua vida financeira!</p>
        </div>
    </header>

    <main class="registration-form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    <div class="registration-card">
                        <div class="registration-header">
                            <h2><i class="fas fa-user-plus"></i> Formulário de Cadastro</h2>
                            <p>Preencha os campos abaixo para começar.</p>
                        </div>

                        <div class="form-container-cadastro">
                            <?php
                            // Exibe mensagens de erro ou sucesso (se $feedback_message for usado)
                            if (!empty($feedback_message)) echo $feedback_message;
                            // Ou mantenha sua lógica original com $erro e $sucesso se preferir não usar sessão para feedback nesta página
                            // if (!empty($erro)) echo $erro; 
                            // if (!empty($sucesso)) echo $sucesso; 
                            ?>

                            <form action="cadastro.php" method="POST" enctype="multipart/form-data" id="registrationForm">
                                <div class="user-type-selector">
                                    <label>
                                        <input type="radio" name="tipo_usuario" value="estudante" <?php echo ($tipo_usuario == '' || $tipo_usuario == 'estudante') ? 'checked' : ''; ?>>
                                        <i class="fas fa-graduation-cap mr-2"></i> Estudante
                                    </label>
                                    <label>
                                        <input type="radio" name="tipo_usuario" value="professor" <?php echo $tipo_usuario == 'professor' ? 'checked' : ''; ?>>
                                        <i class="fas fa-chalkboard-teacher mr-2"></i> Professor
                                    </label>
                                </div>

                                <div class="input-group-custom">
                                    <input type="text" name="nome" id="nome" class="form-control" required value="<?php echo htmlspecialchars($nome); ?>" placeholder=" ">
                                    <i class="fas fa-user input-icon"></i>
                                    <label for="nome" class="floating-label">Nome Completo</label>
                                </div>

                                <div class="input-group-custom">
                                    <input type="email" name="email" id="email" class="form-control" required value="<?php echo htmlspecialchars($email); ?>" placeholder=" ">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <label for="email" class="floating-label">Email</label>
                                </div>

                                <div class="input-group-custom">
                                    <input type="tel" name="telefone" id="telefone" class="form-control" required value="<?php echo htmlspecialchars($telefone); ?>" placeholder=" ">
                                    <i class="fas fa-phone input-icon"></i>
                                    <label for="telefone" class="floating-label">Telefone (com DDD)</label>
                                </div>

                                <div class="input-group-custom">
                                    <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" required value="<?php echo htmlspecialchars($data_nasc); ?>" placeholder=" "> <i class="fas fa-birthday-cake input-icon"></i>
                                    <label for="data_nascimento" class="floating-label">Data de Nascimento</label>
                                </div>

                                <div class="input-group-custom">
                                    <input type="password" name="senha" id="senha" class="form-control" required placeholder=" ">
                                    <i class="fas fa-lock input-icon"></i>
                                    <label for="senha" class="floating-label">Senha</label>
                                </div>

                                <div id="certificadoField" class="certificado-field-custom <?php echo $tipo_usuario == 'professor' ? 'active' : ''; ?>">
                                    <label for="certificado" class="cert-label"><i class="fas fa-file-certificate"></i> Comprovação de Qualificação (PDF, JPG, PNG)</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" name="certificado" id="certificado" accept=".pdf,.jpg,.jpeg,.png" <?php echo $tipo_usuario == 'professor' ? 'required' : ''; ?>>
                                        <label for="certificado" class="file-input-styled-label">Selecionar Arquivo</label>
                                    </div>
                                    <span id="fileNameDisplay" class="file-name-display">Nenhum arquivo selecionado</span>
                                </div>

                                <button type="submit" name="submit" class="btn-submit-custom">
                                    <i class="fas fa-user-plus"></i> Cadastrar
                                </button>
                            </form>

                            <div class="login-link-custom">
                                Já tem uma conta? <a href="login.php">Faça login aqui</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="row">
            <div class="col-12 text-center border-top pt-3">
                <p class="text-muted small">&copy; <span id="currentYear"></span> YoungCash. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <script>
        // Seu JavaScript existente para tipo de usuário, nome do arquivo, floating labels, e currentYear
        // (Mantido como você forneceu)
        document.addEventListener('DOMContentLoaded', () => {
            const tipoUsuarioRadios = document.querySelectorAll('input[name="tipo_usuario"]');
            const certificadoField = document.getElementById('certificadoField');
            const certificadoInput = document.getElementById('certificado');
            const fileNameDisplay = document.getElementById('fileNameDisplay');

            function toggleCertificadoField() {
                let isProfessor = false;
                tipoUsuarioRadios.forEach(radio => {
                    if (radio.checked && radio.value === 'professor') {
                        isProfessor = true;
                    }
                });
                if (isProfessor) {
                    certificadoField.classList.add('active');
                    if (certificadoInput) certificadoInput.setAttribute('required', '');
                } else {
                    certificadoField.classList.remove('active');
                    if (certificadoInput) {
                        certificadoInput.removeAttribute('required');
                        certificadoInput.value = '';
                    }
                    if (fileNameDisplay) fileNameDisplay.textContent = 'Nenhum arquivo selecionado';
                }
            }
            tipoUsuarioRadios.forEach(radio => {
                radio.addEventListener('change', toggleCertificadoField);
            });

            if (certificadoInput) {
                certificadoInput.addEventListener('change', function() {
                    const fileName = this.files[0] ? this.files[0].name : 'Nenhum arquivo selecionado';
                    if (fileNameDisplay) fileNameDisplay.textContent = fileName;
                });
            }

            document.querySelectorAll('.input-group-custom .form-control').forEach(input => {
                const label = input.parentElement.querySelector('.floating-label');
                if (!label) return;

                function checkValue(element) {
                    if (element.value && element.value.length > 0) {
                        element.classList.add('has-value');
                        label.classList.add('active');
                    } else {
                        element.classList.remove('has-value');
                        if (element.type !== 'date' && document.activeElement !== element) {
                            label.classList.remove('active');
                        }
                    }
                    if (element.type === 'date') {
                        if (element.value) {
                            label.classList.add('active');
                        } else if (document.activeElement !== element) {
                            label.classList.remove('active');
                        }
                    }
                }
                checkValue(input);
                input.addEventListener('input', function() {
                    checkValue(this);
                });
                input.addEventListener('focus', function() {
                    label.classList.add('active');
                });
                input.addEventListener('blur', function() {
                    if (!this.value && this.type !== 'date') {
                        label.classList.remove('active');
                    } else if (this.type === 'date' && !this.value) {
                        label.classList.remove('active');
                    }
                });
            });

            const currentYearElem = document.getElementById('currentYear');
            if (currentYearElem) {
                currentYearElem.textContent = new Date().getFullYear();
            }
        });
    </script>
</body>

</html>