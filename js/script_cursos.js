// js/script_cursos.js

const CHAVE_LOCALSTORAGE_CARRINHO = "cursosNoCarrinho";

// Função para escapar caracteres HTML (útil ao construir HTML dinamicamente)
function htmlspecialchars(str) {
  if (typeof str !== "string") return str;
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return str.replace(/[&<>"']/g, function (m) {
    return map[m];
  });
}

// Elementos do popup de feedback (definidos globalmente no script se o popup está no HTML principal)
const popupFeedbackCarrinho = document.getElementById("popupFeedbackCarrinho");
const feedbackPopupTituloEl = document.getElementById("feedbackPopupTitulo");
const feedbackPopupMensagemEl = document.getElementById(
  "feedbackPopupMensagem"
);

function mostrarPopupFeedback(titulo, mensagem, tipoIcone = "info") {
  if (
    popupFeedbackCarrinho &&
    feedbackPopupTituloEl &&
    feedbackPopupMensagemEl
  ) {
    let iconeClasse = "fas fa-info-circle"; // Ícone padrão
    let tituloEscapado = htmlspecialchars(titulo); // Escapa o título para segurança no innerHTML

    if (tipoIcone === "success") {
      iconeClasse = "fas fa-check-circle";
    } else if (tipoIcone === "warning") {
      iconeClasse = "fas fa-exclamation-triangle";
    } else if (tipoIcone === "error") {
      iconeClasse = "fas fa-times-circle";
    }

    feedbackPopupTituloEl.innerHTML = `<i class="${iconeClasse}"></i> <span>${tituloEscapado}</span>`;

    // A mensagem já está formatada como queremos (com aspas literais ao redor do título do curso).
    // Como estamos usando textContent para a mensagem principal, não precisamos escapar a string inteira novamente.
    // O curso.titulo dentro da 'mensagem' já foi tratado pelo json_encode no PHP
    // e as aspas literais são intencionais na string template.
    feedbackPopupMensagemEl.textContent = mensagem;

    popupFeedbackCarrinho.style.display = "flex";
  } else {
    // Fallback para alert (o alert já trata texto como texto)
    alert(titulo + "\n" + mensagem);
  }
}

function fecharPopupFeedbackCarrinho() {
  if (popupFeedbackCarrinho) popupFeedbackCarrinho.style.display = "none";
}

function adicionarAoCarrinhoModal(curso) {
  let carrinho =
    JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];

  const cursoExistente = carrinho.find((item) => item.id === curso.id);
  if (cursoExistente) {
    // O título do curso (curso.titulo) já é uma string JS segura vinda do json_encode.
    // As aspas literais são adicionadas aqui para a mensagem.
    mostrarPopupFeedback(
      "Atenção!",
      `O curso "${curso.titulo}" já está no seu carrinho.`,
      "warning"
    );
    return;
  }

  carrinho.push({
    id: curso.id,
    nome: curso.titulo,
    imagem: curso.img,
    preco: curso.preco_atual,
  });
  localStorage.setItem(CHAVE_LOCALSTORAGE_CARRINHO, JSON.stringify(carrinho));

  mostrarPopupFeedback(
    "Sucesso!",
    `"${curso.titulo}" foi adicionado ao carrinho!`,
    "success"
  );

  const contadorCarrinhoNav = document.getElementById("contador-carrinho-nav");
  if (contadorCarrinhoNav) {
    contadorCarrinhoNav.textContent = carrinho.length.toString();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  if (
    typeof window.youngCashCursosData === "undefined" ||
    typeof window.youngCashCursosData.cursos === "undefined"
  ) {
    console.error(
      "Dados dos cursos (window.youngCashCursosData.cursos) não foram encontrados ou não foram passados pelo PHP."
    );
    return;
  }
  const todosCursos = window.youngCashCursosData.cursos;

  const cursoCards = document.querySelectorAll(".curso-card-clicavel");
  const modalElement = document.getElementById("cursoModal");

  const modalTitulo = document.getElementById("modal-curso-titulo");
  const modalSubtitulo = document.getElementById("modal-curso-subtitulo");
  const modalAutor = document.getElementById("modal-curso-autor");
  const modalAvaliacao = document.getElementById("modal-curso-avaliacao");
  const modalImg = document.getElementById("modal-curso-img");
  const modalPrecoAtual = document.getElementById("modal-curso-preco-atual");
  const modalPrecoAntigo = document.getElementById("modal-curso-preco-antigo");
  const modalDescontoInfo = document.getElementById("modal-curso-desconto");
  const modalAprenderaLista = document.getElementById(
    "modal-curso-aprendera-lista"
  );
  const modalBtnCarrinho = document.getElementById("modal-btn-add-carrinho");
  const modalLinkDetalhes = document.getElementById("modal-link-detalhes");

  cursoCards.forEach((card) => {
    card.addEventListener("click", function () {
      const cursoId = this.dataset.cursoId;
      const cursoData = todosCursos[cursoId];

      if (cursoData && modalElement) {
        if (modalTitulo)
          modalTitulo.textContent = htmlspecialchars(
            cursoData.titulo || "Detalhes do Curso"
          );
        if (modalSubtitulo)
          modalSubtitulo.textContent = htmlspecialchars(
            cursoData.subtitulo || ""
          );
        if (modalAutor)
          modalAutor.textContent = htmlspecialchars(cursoData.autor || "N/A");
        if (modalAvaliacao)
          modalAvaliacao.textContent = htmlspecialchars(
            cursoData.avaliacao || "N/A"
          );
        if (modalImg) {
          modalImg.src = htmlspecialchars(
            cursoData.img || "img/placeholder.jpg"
          );
          modalImg.alt = htmlspecialchars(
            cursoData.titulo || "Thumbnail do Curso"
          );
        }
        if (modalPrecoAtual)
          modalPrecoAtual.textContent = htmlspecialchars(
            cursoData.preco_atual || ""
          );

        if (modalPrecoAntigo) {
          if (cursoData.preco_antigo) {
            modalPrecoAntigo.textContent = htmlspecialchars(
              cursoData.preco_antigo
            );
            modalPrecoAntigo.style.display = "inline";
          } else {
            modalPrecoAntigo.style.display = "none";
          }
        }
        if (modalDescontoInfo)
          modalDescontoInfo.textContent = htmlspecialchars(
            cursoData.desconto_info || ""
          );

        if (modalAprenderaLista) {
          modalAprenderaLista.innerHTML = "";
          if (
            cursoData.o_que_aprendera_lista &&
            Array.isArray(cursoData.o_que_aprendera_lista)
          ) {
            cursoData.o_que_aprendera_lista.forEach((item) => {
              const li = document.createElement("li");
              const icon = document.createElement("i");
              icon.className = "fas fa-check";
              li.appendChild(icon);
              // O item da lista também deve ser escapado se for para textNode
              li.appendChild(
                document.createTextNode(" " + htmlspecialchars(item))
              );
              modalAprenderaLista.appendChild(li);
            });
          }
        }
        if (modalLinkDetalhes)
          modalLinkDetalhes.href = htmlspecialchars(
            cursoData.link_detalhes || "#"
          );
        if (modalBtnCarrinho) {
          modalBtnCarrinho.onclick = function () {
            adicionarAoCarrinhoModal(cursoData);
          };
        }
        if (typeof $ !== "undefined" && $.fn.modal) {
          $("#cursoModal").modal("show");
        } else {
          console.error(
            "jQuery ou Bootstrap Modal não estão carregados para #cursoModal."
          );
        }
      } else {
        if (!cursoData)
          console.error("Dados do curso não encontrados para o ID: " + cursoId);
        if (!modalElement)
          console.error("Elemento do modal #cursoModal não encontrado no DOM.");
      }
    });
  });

  const currentYearElem = document.getElementById("currentYear");
  if (currentYearElem) {
    currentYearElem.textContent = new Date().getFullYear();
  }

  const contadorCarrinhoNav = document.getElementById("contador-carrinho-nav");
  if (contadorCarrinhoNav) {
    const carrinho =
      JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];
    contadorCarrinhoNav.textContent = carrinho.length.toString();
  }
});
