let metaTrancada = false;
let investmentChartInstance;

function formatCurrency(value) {
  return parseFloat(value).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

function calcularInvestimento() {
  const valorInvestidoInput = document.getElementById("valorInvestido");
  const taxaJurosInput = document.getElementById("taxaJuros");
  const periodoInput = document.getElementById("periodo");

  if (
    !valorInvestidoInput.value ||
    !taxaJurosInput.value ||
    !periodoInput.value
  ) {
    alert("Por favor, preencha todos os campos do simulador.");
    return;
  }

  const valorInvestido = parseFloat(valorInvestidoInput.value);
  const taxaJurosAnual = parseFloat(taxaJurosInput.value) / 100;
  const periodoAnos = parseInt(periodoInput.value);

  const taxaJurosMensal = Math.pow(1 + taxaJurosAnual, 1 / 12) - 1;
  const periodoMeses = periodoAnos * 12;

  const montantePorMes = [];
  const mesesParaGrafico = [];
  let valorAtual = valorInvestido;

  for (let i = 0; i <= periodoMeses; i++) {
    if (i === 0) {
      montantePorMes.push(valorAtual.toFixed(2));
    } else {
      valorAtual = valorAtual * (1 + taxaJurosMensal);
      montantePorMes.push(valorAtual.toFixed(2));
    }
    mesesParaGrafico.push(i === 0 ? "Inicial" : `Mês ${i}`);
  }

  mostrarGrafico(mesesParaGrafico, montantePorMes);

  const valorFinal = parseFloat(montantePorMes[periodoMeses]);
  document.getElementById("valorFinal").textContent =
    formatCurrency(valorFinal);
  document.getElementById("resultadoInvestimento").style.display = "block";
}

function mostrarGrafico(labels, data) {
  const ctx = document.getElementById("investmentChart").getContext("2d");
  if (investmentChartInstance) {
    investmentChartInstance.destroy();
  }
  investmentChartInstance = new Chart(ctx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Valor do Investimento (R$)",
          data: data,
          borderColor: "var(--yc-dark)",
          backgroundColor: "rgba(var(--yc-primary-rgb), 0.2)",
          borderWidth: 2,
          tension: 0.1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      scales: {
        y: {
          beginAtZero: false,
          title: {
            display: true,
            text: "Valor (R$)",
          },
          ticks: {
            callback: function (value, index, values) {
              return formatCurrency(value);
            },
          },
        },
        x: {
          title: {
            display: true,
            text: "Tempo (Meses)",
          },
        },
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function (context) {
              let label = context.dataset.label || "";
              if (label) {
                label += ": ";
              }
              if (context.parsed.y !== null) {
                label += formatCurrency(context.parsed.y);
              }
              return label;
            },
          },
        },
      },
    },
  });
}

function travarMeta() {
  if (!metaTrancada) {
    const valorInvestido = document.getElementById("valorInvestido").value;
    const taxaJuros = document.getElementById("taxaJuros").value;
    const periodo = document.getElementById("periodo").value;

    if (!valorInvestido || !taxaJuros || !periodo) {
      alert(
        "Por favor, preencha todos os campos antes de calcular e travar a meta."
      );
      return;
    }

    metaTrancada = true;
    document.getElementById("valorInvestido").disabled = true;
    document.getElementById("taxaJuros").disabled = true;
    document.getElementById("periodo").disabled = true;
    document.getElementById("travarMetaBtn").classList.add("disabled");
    document.getElementById("travarMetaBtn").disabled = true;
    document.getElementById("mudarMetaBtn").style.display = "inline-block";

    calcularInvestimento();
  }
}

function mudarMeta() {
  metaTrancada = false;
  document.getElementById("valorInvestido").disabled = false;
  document.getElementById("taxaJuros").disabled = false;
  document.getElementById("periodo").disabled = false;
  document.getElementById("travarMetaBtn").classList.remove("disabled");
  document.getElementById("travarMetaBtn").disabled = false;
  document.getElementById("mudarMetaBtn").style.display = "none";
  document.getElementById("valorInvestido").value = "";
  document.getElementById("taxaJuros").value = "";
  document.getElementById("periodo").value = "";
  if (investmentChartInstance) {
    investmentChartInstance.destroy();
    investmentChartInstance = null;
  }
  document.getElementById("resultadoInvestimento").style.display = "none";
  document.getElementById("valorFinal").textContent = "";
}

if (document.getElementById("currentYear")) {
  document.getElementById("currentYear").textContent = new Date().getFullYear();
}
