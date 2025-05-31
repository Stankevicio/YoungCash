<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Finalizar Compra</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_pagamento.css">
</head>

<body>
  <div class="container">
    <div class="left">
      <h2>Finalizar compra</h2>
      <label>País</label>
      <select>
        <option>Brasil</option>
        <option>Portugal</option>
        <option>Outros</option>
      </select>

      <h3 style="margin-top: 30px;">Forma de pagamento</h3>

      <!-- Pix -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('pix')"> <i class="fas fa-qrcode"></i> Pix</label>
        <div id="pix" class="payment-info">
          <input type="text" placeholder="CPF/CNPJ">
          <p>Para concluir sua transação, redirecionaremos você para os servidores seguros do dLocal.</p>
        </div>
      </div>

      <!-- Cartões -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('cartao')"> <i class="fas fa-credit-card"></i> Cards <img src="https://img.icons8.com/color/32/visa.png" /><img src="https://img.icons8.com/color/32/mastercard.png" /><img src="https://img.icons8.com/color/32/amex.png" /></label>
        <div id="cartao" class="payment-info">
          <input type="text" placeholder="Número do Cartão">
          <input type="text" placeholder="Validade (MM/AA)">
          <input type="text" placeholder="CVV">
        </div>
      </div>

      <!-- Apple Pay -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('apple')"> <i class="fab fa-apple-pay"></i> Apple Pay</label>
        <div id="apple" class="payment-info">
          <p>Você será redirecionado para a carteira Apple.</p>
        </div>
      </div>

      <!-- Google Pay -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('google')"> <i class="fab fa-google-pay"></i> Google Pay</label>
        <div id="google" class="payment-info">
          <p>Você será redirecionado para a carteira Google.</p>
        </div>
      </div>

      <!-- Boleto -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('boleto')"> <i class="fas fa-barcode"></i> Boleto bancário</label>
        <div id="boleto" class="payment-info">
          <p>O boleto será gerado após a finalização.</p>
        </div>
      </div>

      <!-- PayPal -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('paypal')"> <i class="fab fa-cc-paypal"></i> PayPal</label>
        <div id="paypal" class="payment-info">
          <input type="email" placeholder="E-mail PayPal">
        </div>
      </div>

      <!-- Mercado Pago -->
      <div class="payment-option">
        <label><input type="radio" name="pay" onclick="showPayment('mp')"> <img src="https://logodownload.org/wp-content/uploads/2021/03/mercado-pago-logo-0.png" width="20"> Mercado Pago</label>
        <div id="mp" class="payment-info">
          <p>Você será redirecionado para o Mercado Pago.</p>
        </div>
      </div>
    </div>

    <div class="right">
      <div id="resumo-pedido"></div>

      <button class="btn">🔒 Prosseguir</button>

      <a href="cursos.php"><button class="btn"> Cancelar</button></a>

      <p style="font-size: 14px; margin-top: 20px;">Garantia de devolução do dinheiro em 30 dias.</p>
      <div class="box-info">
        <strong>🚀 Comece a correr atrás do sucesso agora</strong>
        <p>Junte-se a 10+ pessoas que se inscreveram neste curso nas últimas 24 horas.</p>
      </div>
    </div>
  </div>

  <script src="js/script_pagamento.js"></script>
</body>

</html>