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

  carrinhoContainer.innerHTML = "";
  let totalValor = 0;

  if (carrinho.length === 0) {
    carrinhoContainer.innerHTML =
      "<div class='carrinho-vazio'><i class='fas fa-cart-plus'></i><p>Seu carrinho está vazio. Que tal adicionar alguns cursos?</p></div>";
    if (sumarioContainer) sumarioContainer.style.display = "none";
    if (contadorCarrinhoNav) contadorCarrinhoNav.textContent = "0";
  } else {
    carrinho.forEach((curso, indice) => {
      let precoNumerico = 0;
      if (curso.preco) {
        // Ajustado para 'preco' como na função adicionarAoCarrinhoModal
        const precoLimpo = curso.preco
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
                            )}" alt="${htmlspecialchars(
        curso.nome || "Curso"
      )}" style="width: 100px; height: auto; margin-right: 15px;">
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
      carrinhoContainer.innerHTML += itemHTML;
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

function finalizarCompraEConfirmar() {
  const carrinho =
    JSON.parse(localStorage.getItem(CHAVE_LOCALSTORAGE_CARRINHO)) || [];
  if (carrinho.length === 0) {
    alert(
      "Seu carrinho está vazio! Adicione cursos antes de finalizar a compra."
    );
    return false;
  }

  // Simulação de confirmação - em um sistema real, você processaria o pagamento
  if (confirm("Deseja realmente finalizar a compra?")) {
    alert(
      "Compra finalizada com sucesso (simulação)! Seu carrinho será limpo."
    );
    localStorage.removeItem(CHAVE_LOCALSTORAGE_CARRINHO);
    carregarCarrinho();
    // window.location.href = 'pagamento.php'; // Redirecionar para a página de pagamento real
    return true;
  }
  return false; // Impede o redirecionamento do link <a> se o usuário cancelar
}

function limparCarrinhoParaNovoLoginOuLogout() {
  localStorage.removeItem(CHAVE_LOCALSTORAGE_CARRINHO);
  console.log("Carrinho (localStorage) limpo.");
  if (document.getElementById("contador-carrinho-nav")) {
    document.getElementById("contador-carrinho-nav").textContent = "0";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  carregarCarrinho();

  if (document.getElementById("currentYear")) {
    document.getElementById("currentYear").textContent =
      new Date().getFullYear();
  }

  const linkSair = document.getElementById("link-sair"); // ID adicionado ao link de sair no header
  if (linkSair) {
    linkSair.addEventListener("click", function (event) {
      // Não precisa de event.preventDefault() se o href já é para sair.php
      limparCarrinhoParaNovoLoginOuLogout();
    });
  }
});
