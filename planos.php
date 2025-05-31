<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos de Assinatura | YoungCash</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilo_planos.css">
</head>

<body>
    <header class="page-header">
        <div class="container">
            <nav class="navbar navbar-expand-md youngcash-navbar"> <a class="navbar-brand" href="index.php">YoungCash</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#target-navegacao" aria-controls="target-navegacao" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="target-navegacao">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="cadastro.php">Cadastre-se</a></li>
                        <li class="nav-item"><a class="nav-link active-planos" href="planos.php">Planos</a></li>
                    </ul>
                    <a href="login.php" class="btn btn-entrar-custom">Entrar</a>
                </div>
            </nav>

            <h1>Nossos Planos de Assinatura</h1>
            <p class="subtitle">Escolha o plano que melhor se adapta às suas necessidades e comece sua jornada de educação financeira hoje mesmo!</p>
        </div>
    </header>

    <main>
        <section class="plans-section">
            <div class="container">
                <div class="plans-grid">
                    <article class="plan">
                        <header class="plan-header">
                            <h2>Plano Mensal</h2>
                            <p class="plan-price">R$ 29,90<span>/mês</span></p>
                        </header>
                        <ul class="plan-features">
                            <li>Acesso ilimitado a todos os cursos e materiais.</li>
                            <li>Webinars mensais com especialistas em finanças.</li>
                            <li>Acesso a uma comunidade exclusiva de assinantes.</li>
                            <li>Newsletter semanal com dicas e novidades.</li>
                        </ul>
                        <footer class="plan-footer">
                            <button class="button-cta">Assinar Plano Mensal</button>
                        </footer>
                    </article>

                    <article class="plan plan-featured">
                        <header class="plan-header">
                            <span class="featured-badge">Mais Popular</span>
                            <h2>Plano Trimestral</h2>
                            <p class="plan-price">R$ 79,90 <span class="price-detail">(R$ 26,63/mês)</span></p>
                        </header>
                        <ul class="plan-features">
                            <li>Todos os benefícios do plano mensal.</li>
                            <li>Sessões de Q&A trimestrais com especialistas.</li>
                            <li>Acesso antecipado a novos cursos.</li>
                            <li><strong>10% de desconto em relação ao plano mensal!</strong></li>
                        </ul>
                        <footer class="plan-footer">
                            <button class="button-cta button-featured">Assinar Plano Trimestral</button>
                        </footer>
                    </article>

                    <article class="plan">
                        <header class="plan-header">
                            <h2>Plano Semestral</h2>
                            <p class="plan-price">R$ 149,90 <span class="price-detail">(R$ 24,98/mês)</span></p>
                        </header>
                        <ul class="plan-features">
                            <li>Todos os benefícios do plano trimestral.</li>
                            <li>Consultoria financeira personalizada (1 sessão/semestre).</li>
                            <li>Certificado de conclusão para cada curso.</li>
                            <li><strong>17% de desconto em relação ao plano mensal!</strong></li>
                        </ul>
                        <footer class="plan-footer">
                            <button class="button-cta">Assinar Plano Semestral</button>
                        </footer>
                    </article>
                </div>
            </div>
        </section>

        <section class="how-to-subscribe">
            <div class="container">
                <h2>Como Assinar</h2>
                <ol class="steps">
                    <li><span>1</span> Escolha o plano desejado.</li>
                    <li><span>2</span> Preencha o formulário de inscrição.</li>
                    <li><span>3</span> Realize o pagamento e comece a aprender!</li>
                </ol>
            </div>
        </section>

        <section class="faq-section">
            <div class="container">
                <h2>Perguntas Frequentes</h2>
                <div class="faq-item">
                    <p><strong>Posso cancelar minha assinatura a qualquer momento?</strong></p>
                    <p>Sim, você pode cancelar sua assinatura a qualquer momento, e não será cobrado novamente.</p>
                </div>
                <div class="faq-item">
                    <p><strong>Os cursos são atualizados regularmente?</strong></p>
                    <p>Sim, estamos sempre atualizando e adicionando novos cursos para garantir que você tenha acesso ao conteúdo mais relevante.</p>
                </div>
            </div>
        </section>
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
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
</body>

</html>