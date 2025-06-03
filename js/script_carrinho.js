const CHAVE_LOCALSTORAGE_CARRINHO = "cursosNoCarrinho";

function htmlspecialchars(str) {
  if (typeof str !== "string") return "";
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

function formatarMoeda(valor) {
  if (isNaN(parseFloat(valor))) return "R$ 0,00"; // Fallback se o valor não for um número
  return parseFloat(valor).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

function carregarCarrinho() {
  const carrinhoContainer = document.getElementById("lista-carrinho");
  const totalCarrinhoEl = document.getElementById("total-carrinho");
  const sumarioContainer = document.getElementById(
    "sumario-carrinho-container"
  );
  const contadorCarrinhoNav = document.getElementById("contador-carrinho-nav");

  let carrinho =
    JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];

  if (carrinhoContainer) carrinhoContainer.innerHTML = "";
  let totalValor = 0;

  if (carrinho.length === 0) {
    if (carrinhoContainer)
      carrinhoContainer.innerHTML =
        "<div class='carrinho-vazio'><i class='fas fa-cart-plus'></i><p>Seu carrinho está vazio. Que tal adicionar alguns cursos?</p></div>";
    if (sumarioContainer) sumarioContainer.style.display = "none";
    if (contadorCarrinhoNav) contadorCarrinhoNav.textContent = "0";
  } else {
    carrinho.forEach((curso, indice) => {
      let precoNumerico = 0;
      if (curso.preco) {
        const precoLimpo = String(curso.preco)
          .replace("R$", "")
          .replace(/\./g, "")
          .replace(",", ".")
          .trim();
        precoNumerico = parseFloat(precoLimpo);
      }
      if (!isNaN(precoNumerico)) {
        totalValor += precoNumerico;
      }

      const itemHTML = `
                <div class="item-carrinho" data-indice="${indice}">
                    <img src="${htmlspecialchars(
                      curso.imagem || "img/placeholder.jpg"
                    )}" alt="${htmlspecialchars(curso.nome || "Curso")}">
                    <div class="info">
                        <h3>${htmlspecialchars(
                          curso.nome || "Curso sem nome"
                        )}</h3>
                        ${
                          curso.autor
                            ? `<p class="autor-carrinho">Por: ${htmlspecialchars(
                                curso.autor
                              )}</p>`
                            : ""
                        }
                        ${
                          curso.precoAntigo
                            ? `<span class="preco-original">${htmlspecialchars(
                                curso.precoAntigo
                              )}</span>`
                            : ""
                        }
                    </div>
                    <span class="preco-item">${htmlspecialchars(
                      curso.preco || "N/A"
                    )}</span>
                    <button class="btn-remover" onclick="removerDoCarrinho(${indice})" title="Remover item">
                        <i class="fas fa-trash-alt"></i> <span class="d-none d-md-inline">Remover</span>
                    </button>
                </div>
            `;
      if (carrinhoContainer) carrinhoContainer.innerHTML += itemHTML;
    });
    if (sumarioContainer) sumarioContainer.style.display = "block";
    if (contadorCarrinhoNav)
      contadorCarrinhoNav.textContent = carrinho.length.toString();
  }

  if (totalCarrinhoEl)
    totalCarrinhoEl.textContent = `Total: ${formatarMoeda(totalValor)}`;
}

function removerDoCarrinho(indice) {
  let carrinho =
    JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];
  if (indice >= 0 && indice < carrinho.length) {
    carrinho.splice(indice, 1);
    localStorage.setItem(CHAVE_LOCALSTORAGE_CARRINHO, JSON.stringify(carrinho));
    carregarCarrinho();
  }
}

// --- NOVAS FUNÇÕES E LÓGICA PARA POPUP DE CONFIRMAÇÃO ---
const popupConfirmacao = document.getElementById("popupConfirmacaoCompra");
const popupSucesso = document.getElementById("popupSucessoCompra");

function mostrarPopupConfirmacao() {
  if (popupConfirmacao) popupConfirmacao.style.display = "flex";
}

function fecharPopupConfirmacao() {
  if (popupConfirmacao) popupConfirmacao.style.display = "none";
}

function mostrarPopupSucesso() {
  if (popupSucesso) popupSucesso.style.display = "flex";
}

function fecharPopupSucessoEContinuar() {
  if (popupSucesso) popupSucesso.style.display = "none";
  // Redireciona para a página de pagamento ou de "meus pedidos" após o sucesso
  // window.location.href = 'pagamento.php';
  // Ou apenas recarrega a página do carrinho, que agora estará vazio
  window.location.href = "carrinho.php";
}

function finalizarCompraEConfirmar() {
  const carrinho =
    JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];
  if (carrinho.length === 0) {
    // Poderia usar um popup de erro aqui também
    alert(
      "Seu carrinho está vazio! Adicione cursos antes de finalizar a compra."
    );
    return false; // Impede o redirecionamento do link <a> se houver
  }
  mostrarPopupConfirmacao();
  return false; // Impede o comportamento padrão do link <a>, pois o popup cuidará da ação
}

function cancelarFinalizacaoCompra() {
  fecharPopupConfirmacao();
}

function processarFinalizacaoCompra() {
  fecharPopupConfirmacao(); // Fecha o popup de confirmação

  // Lógica real de finalização de compra iria aqui (ex: chamada AJAX para o backend)
  // Para esta simulação:
  console.log("Compra processada (simulação)!");
  localStorage.removeItem(CHAVE_LOCALSTORAGE_CARRINHO);
  carregarCarrinho(); // Atualiza a exibição do carrinho (deve mostrar vazio)

  // Mostra o popup de sucesso
  mostrarPopupSucesso();
}
// --- FIM DAS NOVAS FUNÇÕES ---

function limparCarrinhoParaNovoLoginOuLogout() {
  localStorage.removeItem(CHAVE_LOCALSTORAGE_CARRINHO);
  console.log("Carrinho (localStorage) limpo.");
  const contadorCarrinhoNav = document.getElementById("contador-carrinho-nav");
  if (contadorCarrinhoNav) {
    contadorCarrinhoNav.textContent = "0";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  carregarCarrinho();

  if (document.getElementById("currentYear")) {
    document.getElementById("currentYear").textContent =
      new Date().getFullYear();
  }

  const linkSair = document.getElementById("link-sair");
  if (linkSair) {
    linkSair.addEventListener("click", function (event) {
      limparCarrinhoParaNovoLoginOuLogout();
      // O redirecionamento para sair.php ocorrerá normalmente pelo href do link
    });
  }
});
