<?php
session_start();

//print_r($_SESSION);

if ((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)) {
   unset($_SESSION['email']);
   unset($_SESSION['senha']);
   header('Location: login.php');
}

$logado = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home | YoungCash</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/estilo_home.css">

</head>

<body>
   <header class="header">

      <section class="flex">

         <a href="#home" class="logo">YoungCash</a>



         <nav class="navbar">
            <a href="#home">Inicio</a>
            <a href="cursos.php">Aprenda</a>
            <a href="alunoPainel.php">Painel</a>
            <a href="invest.php">Investimento</a>
            <a href="#reviews">opniões</a>
            <a href="#contact">contato</a>
            <a href="perfil.php">Meu Perfil</a>
         </nav>

         <div id="menu-btn" class="fas fa-bars"></div>
      </section>

   </header>

   <section class="home" id="home">

      <div class="row">

         <div class="content">
            <h3>Young <span>Cash</span></h3>
            <a href="#contact" class="btn">contato</a>
         </div>

         <div class="image">
            <img src="img/homg-img.svg" alt="">
         </div>

      </div>

   </section>

   <section class="count">

      <div class="box-container">

         <div class="box">
            <i class="fas fa-graduation-cap"></i>
            <div class="content">
               <h3>150+</h3>
               <p>cursos</p>
            </div>
         </div>

         <div class="box">
            <i class="fas fa-user-graduate"></i>
            <div class="content">
               <h3>1300+</h3>
               <p>estudantes</p>
            </div>
         </div>

         <div class="box">
            <i class="fas fa-chalkboard-user"></i>
            <div class="content">
               <h3>80+</h3>
               <p>professores</p>
            </div>
         </div>

         <div class="box">
            <i class="fas fa-face-smile"></i>
            <div class="content">
               <h3>100%</h3>
               <p>satisfação</p>
            </div>
         </div>

      </div>

   </section>

   <section class="about" id="about">

      <div class="row">

         <div class="image">
            <img src="img/about-img.svg" alt="">
         </div>

         <div class="content">
            <h3>Porque nos-escolher??</h3>
            <p>O YoungCash pode ser uma boa escolha para quem busca aprendizado online por ser uma plataforma com grande variedade de cursos na área financeira, desde temas básicos a especializados, e por oferecer flexibilidade de aprendizado, com cursos gratuitos e pagos.
            </p>
            <a href="#contact" class="btn">contato</a>
         </div>

      </div>

   </section>

   <section class="courses" id="courses">

      <h1 class="heading">Cursos <span></span></h1>

      <div class="swiper course-slider">

         <div class="swiper-wrapper">

            <div class="swiper-slide slide">
               <img src="img/course-1.svg" alt="">
               <h3>Educação Financeira Básica</h3>
               <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Similique, repellat!</p>
            </div>

            <div class="swiper-slide slide">
               <img src="img/course-2.svg" alt="">
               <h3>Investimentos Avançados</h3>
               <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Similique, repellat!</p>
            </div>

            <div class="swiper-slide slide">
               <img src="img/course-3.svg" alt="">
               <h3>Planejamento de Aposentadoria</h3>
               <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Similique, repellat!</p>
            </div>

            <div class="swiper-slide slide">
               <img src="img/course-4.svg" alt="">
               <h3>Gestão de Finanças Pessoais</h3>
               <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Similique, repellat!</p>
            </div>

            <div class="swiper-slide slide">
               <img src="img/course-5.svg" alt="">
               <h3>Introdução ao Mercado de Ações</h3>
               <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Similique, repellat!</p>
            </div>

            <div class="swiper-slide slide">
               <img src="img/course-6.svg" alt="">
               <h3>Planejamento Financeiro para a Aposentadoria</h3>
               <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Similique, repellat!</p>
            </div>

         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>

   <section class="teachers" id="teachers">

      <h1 class="heading">Professores <span></span></h1>

      <div class="swiper teachers-slider">

         <div class="swiper-wrapper">

            <div class="swiper-slide slide">
               <img src="img/tutor-1.png" alt="">
               <div class="share">
                  <a href="#" class="fab fa-facebook-f"></a>
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-instagram"></a>
                  <a href="#" class="fab fa-linkedin"></a>
               </div>
               <h3>john</h3>
            </div>

            <div class="swiper-slide slide">
               <img src="img/tutor-2.png" alt="">
               <div class="share">
                  <a href="#" class="fab fa-facebook-f"></a>
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-instagram"></a>
                  <a href="#" class="fab fa-linkedin"></a>
               </div>
               <h3>Lia</h3>
            </div>

            <div class="swiper-slide slide">
               <img src="img/tutor-3.png" alt="">
               <div class="share">
                  <a href="#" class="fab fa-facebook-f"></a>
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-instagram"></a>
                  <a href="#" class="fab fa-linkedin"></a>
               </div>
               <h3>tadeu</h3>
            </div>

            <div class="swiper-slide slide">
               <img src="img/tutor-4.png" alt="">
               <div class="share">
                  <a href="#" class="fab fa-facebook-f"></a>
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-instagram"></a>
                  <a href="#" class="fab fa-linkedin"></a>
               </div>
               <h3>Aline</h3>
            </div>

            <div class="swiper-slide slide">
               <img src="img/tutor-5.png" alt="">
               <div class="share">
                  <a href="#" class="fab fa-facebook-f"></a>
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-instagram"></a>
                  <a href="#" class="fab fa-linkedin"></a>
               </div>
               <h3>Bruno</h3>
            </div>

            <div class="swiper-slide slide">
               <img src="img/tutor-6.png" alt="">
               <div class="share">
                  <a href="#" class="fab fa-facebook-f"></a>
                  <a href="#" class="fab fa-twitter"></a>
                  <a href="#" class="fab fa-instagram"></a>
                  <a href="#" class="fab fa-linkedin"></a>
               </div>
               <h3>Geralda</h3>
            </div>

         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>

   <!-- teachers section ends -->

   <!-- reviews section starts  -->

   <section class="reviews" id="reviews">

      <h1 class="heading"> Opnião de <span>estudantes</span></h1>

      <div class="swiper reviews-slider">

         <div class="swiper-wrapper">

            <div class="swiper-slide slide">
               <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum necessitatibus atque fuga delectus
                  numquam consequatur velit autem distinctio possimus culpa!</p>
               <div class="user">
                  <img src="img/pic-1.png" alt="">
                  <div class="user-info">
                     <h3>Leo</h3>
                     <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                     </div>
                  </div>
               </div>
            </div>

            <div class="swiper-slide slide">
               <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum necessitatibus atque fuga delectus
                  numquam consequatur velit autem distinctio possimus culpa!</p>
               <div class="user">
                  <img src="img/pic-2.png" alt="">
                  <div class="user-info">
                     <h3>Maria</h3>
                     <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                     </div>
                  </div>
               </div>
            </div>

            <div class="swiper-slide slide">
               <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum necessitatibus atque fuga delectus
                  numquam consequatur velit autem distinctio possimus culpa!</p>
               <div class="user">
                  <img src="img/pic-3.png" alt="">
                  <div class="user-info">
                     <h3>João</h3>
                     <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                     </div>
                  </div>
               </div>
            </div>

            <div class="swiper-slide slide">
               <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum necessitatibus atque fuga delectus
                  numquam consequatur velit autem distinctio possimus culpa!</p>
               <div class="user">
                  <img src="img/pic-4.png" alt="">
                  <div class="user-info">
                     <h3>Bia</h3>
                     <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                     </div>
                  </div>
               </div>
            </div>

            <div class="swiper-slide slide">
               <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum necessitatibus atque fuga delectus
                  numquam consequatur velit autem distinctio possimus culpa!</p>
               <div class="user">
                  <img src="img/pic-5.png" alt="">
                  <div class="user-info">
                     <h3>Osvaldo</h3>
                     <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                     </div>
                  </div>
               </div>
            </div>

            <div class="swiper-slide slide">
               <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illum necessitatibus atque fuga delectus
                  numquam consequatur velit autem distinctio possimus culpa!</p>
               <div class="user">
                  <img src="img/pic-6.png" alt="">
                  <div class="user-info">
                     <h3>Joaninha</h3>
                     <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>

   <section class="contact" id="contact">
      <h1 class="heading"><span>Informe seus dados de contato para receber por email noticias do mundo financeiro.</span></h1>
      <div class="row">
         <div class="image">
            <img src="img/contact-img.svg" alt="">
         </div>
         <form action="enviar_email.php" method="post" id="formContatoNewsletter">
            <span>nome</span>
            <input type="text" required placeholder="Seu nome completo" maxlength="50" name="nome" class="box">
            <span>email</span>
            <input type="email" required placeholder="seuemail@exemplo.com" maxlength="50" name="email" class="box">
            <span>telefone</span>
            <input type="number" placeholder="Seu telefone (opcional)" max="9999999999999" min="0" name="telefone" class="box" onkeypress="if(this.value.length == 11) return false;">
            <span>profissão</span>
            <select name="profissao" class="box">
               <option value="" disabled selected>Selecione sua profissão (opcional)</option>
               <option value="estudante">Estudante</option>
               <option value="desenvolvedor">Desenvolvedor Web</option>
               <option value="ciencia_biologia">Ciência e Biologia</option>
               <option value="engenharia">Engenharia</option>
               <option value="marketing_digital">Marketing Digital</option>
               <option value="design_grafico">Design Gráfico</option>
               <option value="ensino">Ensino</option>
               <option value="estudos_sociais">Estudos Sociais</option>
               <option value="analise_dados">Análise de Dados</option>
               <option value="ia">Inteligência Artificial</option>
               <option value="outra">Outra</option>
            </select>
            <span>Selecione o seu Gênero:</span>
            <div class="radio">
               <input type="radio" name="gender" value="male" id="male">
               <label for="male" style="margin-right: 5px; color: var(--light-bg);">Masculino</label>
               <input type="radio" name="gender" value="female" id="female">
               <label for="female" style="margin-right: 20px; color: var(--light-bg);">Feminino</label>
               <input type="radio" name="gender" value="other" id="other">
               <label for="other" style="color: var(--light-bg);">Outro</label>
            </div>
            <input type="submit" value="Quero Receber!" class="btn" name="send">
         </form>
      </div>
   </section>

   <footer class="footer">
      <section>
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-linkedin"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-youtube"></a>
         </div>
         <div class="credit" style="color: var(--green);">&copy; <span id="currentYear"></span> YoungCash. Todos os direitos reservados.</div>
      </section>
   </footer>

   <div id="newsletterFeedbackPopup" class="yc-feedback-popup-overlay" style="display:none;">
      <div class="yc-feedback-popup-content">
         <button type="button" class="yc-popup-close" onclick="closeNewsletterFeedbackPopup()" aria-label="Fechar">&times;</button>
         <h3 id="newsletterPopupTitle"><i class="fas"></i> <span></span></h3>
         <p id="newsletterPopupMessage"></p>
         <button onclick="closeNewsletterFeedbackPopup()" class="btn-popup-ok">OK</button>
      </div>
   </div>

   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
   <script src="js/script-home.js"></script>

   <script>
      // Script para o formulário de newsletter/contato com popup
      const newsletterForm = document.getElementById('formContatoNewsletter');
      const newsletterFeedbackPopup = document.getElementById('newsletterFeedbackPopup');
      const newsletterPopupTitleEl = document.getElementById('newsletterPopupTitle');
      const newsletterPopupMessageEl = document.getElementById('newsletterPopupMessage');
      const newsletterPopupOverlay = document.querySelector('#newsletterFeedbackPopup.yc-feedback-popup-overlay');

      if (newsletterForm) {
         newsletterForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(newsletterForm);
            const submitButton = newsletterForm.querySelector('input[type="submit"]');
            const originalButtonText = submitButton.value;
            submitButton.value = 'Enviando...';
            submitButton.disabled = true;

            fetch('enviar_email.php', {
                  method: 'POST',
                  body: formData
               })
               .then(response => {
                  if (!response.ok) { // Verifica se a resposta HTTP foi bem-sucedida
                     throw new Error(`Erro de rede: ${response.status} ${response.statusText}`);
                  }
                  return response.json();
               })
               .then(data => {
                  if (data.status === 'success') {
                     if (newsletterPopupTitleEl) newsletterPopupTitleEl.innerHTML = '<i class="fas fa-check-circle" style="color: green;"></i> Sucesso!';
                     if (newsletterPopupMessageEl) newsletterPopupMessageEl.textContent = data.message;
                     newsletterForm.reset();
                  } else {
                     if (newsletterPopupTitleEl) newsletterPopupTitleEl.innerHTML = '<i class="fas fa-times-circle" style="color: red;"></i> Erro!';
                     if (newsletterPopupMessageEl) newsletterPopupMessageEl.textContent = data.message || 'Ocorreu um erro ao processar sua solicitação.';
                  }
                  if (newsletterFeedbackPopup) newsletterFeedbackPopup.style.display = 'flex';
               })
               .catch(error => {
                  console.error('Erro na requisição AJAX:', error);
                  if (newsletterPopupTitleEl) newsletterPopupTitleEl.innerHTML = '<i class="fas fa-exclamation-triangle" style="color: orange;"></i> Erro de Conexão!';
                  if (newsletterPopupMessageEl) newsletterPopupMessageEl.textContent = 'Não foi possível conectar ao servidor. Tente novamente mais tarde.';
                  if (newsletterFeedbackPopup) newsletterFeedbackPopup.style.display = 'flex';
               })
               .finally(() => {
                  submitButton.value = originalButtonText;
                  submitButton.disabled = false;
               });
         });
      }

      function closeNewsletterFeedbackPopup() {
         if (newsletterFeedbackPopup) newsletterFeedbackPopup.style.display = 'none';
      }

      if (newsletterPopupOverlay) {
         newsletterPopupOverlay.addEventListener('click', function(event) {
            if (event.target === newsletterPopupOverlay) {
               closeNewsletterFeedbackPopup();
            }
         });
      }

      // Manter o script do currentYear se ele não estiver no script_home.js
      if (document.getElementById('currentYear')) {
         document.getElementById('currentYear').textContent = new Date().getFullYear();
      }
   </script>

</body>

</html>