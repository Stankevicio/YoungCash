function showPayment(id) {
  document
    .querySelectorAll(".payment-info")
    .forEach((div) => (div.style.display = "none"));
  document.getElementById(id).style.display = "block";
}

// Recuperar carrinho
const carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];

const resumo = document.getElementById("resumo-pedido");

if (carrinho.length === 0) {
  resumo.innerHTML = "<p>Carrinho vazio!</p>";
} else {
  let total = 0;
  carrinho.forEach((curso) => {
    total += curso.preco;
  });

  resumo.innerHTML = `
        <p>Preço dos cursos: <strong>R$ ${total.toFixed(2)}</strong></p>
        <p>Desconto aplicado: <strong style="color: green;">- R$0,00</strong></p>
        <p><strong>Total a pagar: R$ ${total.toFixed(2)}</strong></p>
      `;
}
