<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bem Vindo! | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo_index.css">
</head>

<body>
    <header class="page-header">
        <div class="container">
            <nav class="navbar navbar-expand-md youngcash-navbar">
                <a class="navbar-brand" href="index.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#target-navegacao" aria-controls="target-navegacao" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="target-navegacao">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="cadastro.php">Cadastre-se</a></li>
                        <li class="nav-item"><a class="nav-link" href="planos.php">Planos</a></li>
                    </ul>
                    <a href="login.php" class="btn btn-entrar-custom">Entrar</a>
                </div>
            </nav>
        </div>

        <div id="home">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="display-4">Seja bem vindo ao YoungCash!</h1>
                        <p>Venha aprender tudo sobre educação financeira! Pare de depender dos outros quando o assunto é dinheiro.</p>
                        <div>
                            <a href="home.php" class="btn btn-youngcash-destaque">Venha pro YoungCash</a>
                        </div>
                    </div>
                    <div class="col-md-6 d-none d-md-block">
                        <img src="img/homg-img.svg" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <article class="col-md-6">
                    <h2>Conheça mais sobre o mundo dos Investimentos!</h2>
                    <p>Com a nossa plataforma, você pode aprender a investir com base na sua meta pessoal!</p>
                    <a class="btn btn-veja-mais" href="#">Veja mais!</a>
                </article>
                <article class="col-md-6">
                    <img src="img/saiba.png" class="img-fluid" style="max-width: 80%; margin-top: 60px; margin-bottom: 50px">
                </article>
            </div>

            <div class="row pt-5 align-items-center">
                <article class="col-md-7 order-md-2">
                    <h2>Faça o seu dinheiro trabalhar por você!</h2>
                    <p>Aprenda o melhor jeito de utilizar aquele dinheiro que sobra no final do mês!</p>
                    <a class="btn btn-veja-mais" href="#">Veja mais!</a>
                </article>
                <article class="col-md-5 order-md-1">
                    <img src="img/juros.png" class="img-fluid" style="max-width: 60%; margin-bottom: 60px">
                </article>
            </div>
            <hr>
        </div>
    </section>

    <section class="section-padding section-bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h2 class="mb-5">Por que escolher YoungCash?</h2>
                </div>
                <article class="col-md-4 info-feature-item">
                    <img src="img/facil.png" class="img-fluid" alt="Facilidade">
                    <h3>Aprendendo de forma Fácil</h3>
                    <p>O YoungCash vai além do básico e permite que você aprenda coisas essenciais sobre como gerenciar suas finanças. Simples como tem que ser!</p>
                </article>
                <article class="col-md-4 info-feature-item">
                    <img src="img/economize.png" class="img-fluid" alt="Economia">
                    <h3>Economize seu Tempo</h3>
                    <p>Tempo é dinheiro! Quanto mais tempo o seu dinheiro fica parado, menos ele irá render!</p>
                </article>
                <article class="col-md-4 info-feature-item" style="margin-bottom: 40px">
                    <img src="img/suporte.png" class="img-fluid" alt="Suporte">
                    <h3>Conteúdo de Qualidade</h3>
                    <p>O YoungCash possui uma gama de cursos voltados para inteligência financeira. Explore nossa aba da aprendizado e conheça o que o seu dinheiro pode fazer!</p>
                </article>
            </div>
            <hr>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="text-center mb-4" style="margin-top: 30px">Simulador de Metas Financeiras</h2>
                    <form id="finance-form">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" placeholder="Insira seu nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="meta" class="form-label">Meta Financeira (R$)</label>
                            <input type="number" class="form-control" id="meta" placeholder="Qual é sua meta financeira?" required>
                        </div>
                        <div class="mb-3">
                            <label for="prazo" class="form-label">Prazo (Meses)</label>
                            <input type="number" class="form-control" id="prazo" placeholder="Em quantos meses?" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Calcular</button>
                    </form>
                    <div id="resultado" class="mt-4"></div>
                </div>
            </div>
        </div>
    </section>

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/script_meta.js"></script>
    <script>
        // Para atualizar o ano no rodapé
        if (document.getElementById('currentYear')) {
            document.getElementById('currentYear').textContent = new Date().getFullYear();
        }
    </script>
</body>

</html>