// carrinho.test.js

// Mock para localStorage
let store = {};
const mockLocalStorage = {
  getItem: jest.fn((key) => store[key] || null),
  setItem: jest.fn((key, value) => {
    store[key] = String(value);
  }),
  removeItem: jest.fn((key) => {
    delete store[key];
  }),
  clear: jest.fn(() => {
    store = {};
  }),
};
Object.defineProperty(window, 'localStorage', { value: mockLocalStorage });

window.alert = jest.fn();
window.confirm = jest.fn();


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


const setupDOM = () => {
  document.body.innerHTML = `
    <div id="lista-carrinho"></div>
    <span id="total-carrinho"></span>
    <div id="sumario-carrinho-container"></div>
    <span id="contador-carrinho-nav"></span>
    <a id="link-sair">Sair</a>
    <span id="currentYear"></span>
    <button onclick="removerDoCarrinho(0)"></button> `;
};

describe('Testes para Funções Utilitárias', () => {
  describe('htmlspecialchars', () => {
    test('deve escapar caracteres HTML especiais', () => {
      expect(htmlspecialchars('<div class="foo">Hello & "World"\'s</div>')).toBe('&lt;div class=&quot;foo&quot;&gt;Hello &amp; &quot;World&quot;&#039;s&lt;/div&gt;');
    });

    test('deve retornar string vazia para entrada não string', () => {
      expect(htmlspecialchars(null)).toBe('');
      expect(htmlspecialchars(undefined)).toBe('');
      expect(htmlspecialchars(123)).toBe('');
    });

    test('deve retornar a string original se não houver caracteres especiais', () => {
      expect(htmlspecialchars('Hello World')).toBe('Hello World');
    });
  });

  describe('formatarMoeda', () => {
    test('deve formatar número para moeda BRL corretamente', () => {
      expect(formatarMoeda(10.5)).toBe('R$\xa010,50'); // \xa0 é o espaço não quebrável
      expect(formatarMoeda(12345.67)).toBe('R$\xa012.345,67');
    });

    test('deve formatar string numérica para moeda BRL', () => {
      expect(formatarMoeda('99.90')).toBe('R$\xa099,90');
    });

    test('deve lidar com valor zero', () => {
      expect(formatarMoeda(0)).toBe('R$\xa00,00');
    });
  });
});