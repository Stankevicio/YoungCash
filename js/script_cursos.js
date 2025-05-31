// No início do script-cursos.js, esperamos que window.youngCashCursosData.todosCursos exista

function adicionarAoCarrinhoModal(curso) {
  const CHAVE_LOCALSTORAGE_CARRINHO = "cursosNoCarrinho"; // Definindo a chave aqui também
  let carrinho =
    JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];

  const cursoExistente = carrinho.find((item) => item.id === curso.id);
  if (cursoExistente) {
    alert("Este curso já está no seu carrinho!");
    return;
  }

  carrinho.push({
    id: curso.id,
    nome: curso.titulo,
    imagem: curso.img,
    preco: curso.preco_atual,
  });
  localStorage.setItem(CHAVE_LOCALSTORAGE_CARRINHO, JSON.stringify(carrinho));
  alert('"' + curso.titulo + '" adicionado ao carrinho!');

  // Atualizar contador na navbar (se existir)
  const contadorCarrinhoNav = document.getElementById("contador-carrinho-nav");
  if (contadorCarrinhoNav) {
    contadorCarrinhoNav.textContent = carrinho.length.toString();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Verifica se os dados dos cursos foram passados corretamente
  if (
    typeof window.youngCashCursosData === "undefined" ||
    typeof window.youngCashCursosData.cursos === "undefined"
  ) {
    console.error(
      "Dados dos cursos (window.youngCashCursosData.cursos) não foram encontrados ou não foram passados pelo PHP."
    );
    return; // Interrompe a execução se os dados não estiverem disponíveis
  }
  const todosCursos = window.youngCashCursosData.cursos;

  const cursoCards = document.querySelectorAll(".curso-card-clicavel");
  const modalElement = document.getElementById("cursoModal");

  // Elementos do Modal para popular (verifique se todos existem no seu HTML do modal)
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
      const cursoData = todosCursos[cursoId]; // Acessa o curso pelo ID/chave

      if (cursoData && modalElement) {
        if (modalTitulo)
          modalTitulo.textContent = cursoData.titulo || "Detalhes do Curso";
        if (modalSubtitulo)
          modalSubtitulo.textContent = cursoData.subtitulo || "";
        if (modalAutor) modalAutor.textContent = cursoData.autor || "N/A";
        if (modalAvaliacao)
          modalAvaliacao.textContent = cursoData.avaliacao || "N/A";
        if (modalImg) {
          modalImg.src = cursoData.img || "img/placeholder.jpg";
          modalImg.alt = cursoData.titulo || "Thumbnail do Curso";
        }
        if (modalPrecoAtual)
          modalPrecoAtual.textContent = cursoData.preco_atual || "";

        if (modalPrecoAntigo) {
          if (cursoData.preco_antigo) {
            modalPrecoAntigo.textContent = cursoData.preco_antigo;
            modalPrecoAntigo.style.display = "inline";
          } else {
            modalPrecoAntigo.style.display = "none";
          }
        }
        if (modalDescontoInfo)
          modalDescontoInfo.textContent = cursoData.desconto_info || "";

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
              li.appendChild(document.createTextNode(" " + item));
              modalAprenderaLista.appendChild(li);
            });
          }
        }

        if (modalLinkDetalhes)
          modalLinkDetalhes.href = cursoData.link_detalhes || "#";

        if (modalBtnCarrinho) {
          modalBtnCarrinho.onclick = function () {
            // Redefine o onclick para o curso atual
            adicionarAoCarrinhoModal(cursoData);
          };
        }

        // Para Bootstrap 4, usamos jQuery para mostrar o modal (se jQuery estiver carregado)
        if (typeof $ !== "undefined" && $.fn.modal) {
          $("#cursoModal").modal("show");
        } else {
          console.error(
            "jQuery ou Bootstrap Modal não estão carregados corretamente para exibir #cursoModal."
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

  // Atualizar ano no rodapé
  const currentYearElem = document.getElementById("currentYear");
  if (currentYearElem) {
    currentYearElem.textContent = new Date().getFullYear();
  }
});
