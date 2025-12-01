(function () {
  var i18n = window.wp && window.wp.i18n ? window.wp.i18n : null;
  var messages = {
    'Add coupons': ['Adicionar cupom'],
    'Add coupon': ['Adicionar cupom'],
    'Add a coupon': ['Adicionar cupom'],
    'Apply': ['Aplicar'],
    'Apply coupon': ['Aplicar cupom'],
    'Coupon': ['Cupom'],
    'Coupon code': ['Código do cupom'],
    'Enter code': ['Digite o código'],
    'Remove': ['Remover'],
    'Remove item': ['Remover item'],
    'Update cart': ['Atualizar carrinho'],
    'Order summary': ['Resumo do pedido'],
    'Product': ['Produto'],
    'Products': ['Produtos'],
    'Quantity': ['Quantidade'],
    'Price': ['Preço'],
    'Subtotal': ['Subtotal'],
    'Total': ['Total'],
    'Shipping': ['Frete'],
    'Free shipping': ['Frete grátis'],
    'Local pickup': ['Retirada no local'],
    'Proceed to checkout': ['Prosseguir para o checkout'],
    'Checkout': ['Checkout'],
    'Place order': ['Finalizar pedido'],
    'Return to cart': ['Retornar ao carrinho'],
    'Your cart': ['Seu carrinho'],
    'Your cart is currently empty': ['Seu carrinho está vazio'],
    'Your cart is currently empty.': ['Seu carrinho está vazio.'],
    'Continue shopping': ['Continuar comprando'],
    'View cart': ['Ver carrinho'],
    'Add to cart': ['Adicionar ao carrinho'],
    'Select options': ['Selecionar opções'],
    'Read more': ['Saiba mais'],
    'Out of stock': ['Fora de estoque'],
    'In stock': ['Em estoque'],
    'Billing details': ['Dados de cobrança'],
    'Shipping address': ['Endereço de entrega'],
    'Same as billing address': ['Igual ao endereço de cobrança'],
    'Payment': ['Pagamento'],
    'Payment methods': ['Métodos de pagamento'],
    'Select a payment method': ['Selecione um método de pagamento'],
    'Credit card': ['Cartão de crédito'],
    'Cash on delivery': ['Pagamento na entrega'],
    'PIX': ['PIX'],
    'Discount': ['Desconto'],
    'Order notes': ['Observações do pedido'],
    'I have read and agree to the website terms and conditions': ['Li e concordo com os termos e condições do site'],
    'Privacy policy': ['Política de privacidade'],
    'Email': ['E-mail'],
    'Phone': ['Telefone'],
    'Address': ['Endereço'],
    'City': ['Cidade'],
    'Postcode': ['CEP'],
    'ZIP code': ['CEP'],
    'State': ['Estado'],
    'Country': ['País'],
    'Company': ['Empresa'],
    'First name': ['Nome'],
    'Last name': ['Sobrenome'],
    'Apartment, suite, etc.': ['Apartamento, sala, etc.'],
    'Optional': ['Opcional'],
    'Required': ['Obrigatório'],
    'Select': ['Selecionar'],
    'Search': ['Buscar'],
    'Shipping will be calculated at checkout': ['O frete será calculado na finalização da compra'],
    'Shipping will be calculated at checkout.': ['O frete será calculado na finalização da compra'],
    'Thank you': ['Obrigado'],
    'Your order has been received': ['Seu pedido foi recebido'],
    'Order number': ['Número do pedido'],
    'Date': ['Data'],
    'Payment method': ['Método de pagamento'],
    'Download': ['Download'],
    'View': ['Ver'],
    'Pay': ['Pagar'],
    'Cancel': ['Cancelar'],
    'My account': ['Minha conta'],
    'Orders': ['Pedidos'],
    'Addresses': ['Endereços'],
    'Account details': ['Detalhes da conta'],
    'Login': ['Entrar'],
    'Register': ['Cadastrar'],
    'Username or email address': ['Usuário ou e-mail'],
    'Password': ['Senha'],
    'Remember me': ['Lembrar-me'],
    'Lost your password?': ['Perdeu sua senha?'],
    'Logout': ['Sair']
  };
  if (i18n && i18n.setLocaleData) {
    var jedWoo = { '': { domain: 'woocommerce', lang: 'pt_BR' } };
    var jedBlocks = { '': { domain: 'woocommerce-blocks', lang: 'pt_BR' } };
    for (var key in messages) {
      if (Object.prototype.hasOwnProperty.call(messages, key)) {
        jedWoo[key] = messages[key];
        jedBlocks[key] = messages[key];
      }
    }
    i18n.setLocaleData(jedWoo, 'woocommerce');
    i18n.setLocaleData(jedBlocks, 'woocommerce-blocks');
  }
  var map = {};
  for (var k in messages) { if (Object.prototype.hasOwnProperty.call(messages, k)) { map[k] = messages[k][0]; } }
  map['apply'] = 'Aplicar';
  map['coupon code'] = 'Código do cupom';
  map['proceed to checkout'] = 'Prosseguir para o checkout';
  map['place order'] = 'Finalizar pedido';
  function replaceTextNodes(el) {
    if (!el) return;
    var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);
    var node = walker.nextNode();
    while (node) {
      var t = node.nodeValue.trim();
      var tl = t.toLowerCase();
      if (map[t]) { node.nodeValue = map[t]; }
      else if (map[tl]) { node.nodeValue = map[tl]; }
      node = walker.nextNode();
    }
    var placeholder = el.getAttribute && el.getAttribute('placeholder');
    if (placeholder) {
      var pl = placeholder.toLowerCase();
      if (map[placeholder]) { el.setAttribute('placeholder', map[placeholder]); }
      else if (map[pl]) { el.setAttribute('placeholder', map[pl]); }
    }
    var aria = el.getAttribute && el.getAttribute('aria-label');
    if (aria) {
      var al = aria.toLowerCase();
      if (map[aria]) { el.setAttribute('aria-label', map[aria]); }
      else if (map[al]) { el.setAttribute('aria-label', map[al]); }
    }
  }
  function translateRoot(root) {
    replaceTextNodes(root);
    var targets = root.querySelectorAll([
      '.wc-block-components-panel__button',
      '.wc-block-components-totals-coupon',
      '.wc-block-components-button',
      '.wc-block-cart__submit-button',
      '.wc-block-checkout__actions',
      '.wc-block-components-shipping-address',
      '.wc-block-components-billing-address',
      '.wc-block-components-order-summary',
      '.wc-block-components-payment-methods'
    ].join(','));
    targets.forEach(function (t) { replaceTextNodes(t); });
    var shipElems = root.querySelectorAll('.wc-block-components-totals-footer-item-shipping');
    shipElems.forEach(function (el) {
      var t = (el.textContent || '').trim();
      var tl = t.toLowerCase();
      if (map[t]) { el.textContent = map[t]; }
      else if (map[tl]) { el.textContent = map[tl]; }
    });
  }
  function init() {
    translateRoot(document.body);
    var obs = new MutationObserver(function (muts) {
      for (var i = 0; i < muts.length; i++) {
        var m = muts[i];
        if (m.type === 'childList') {
          m.addedNodes.forEach(function (an) {
            if (an.nodeType === 1) { translateRoot(an); }
            else if (an.nodeType === 3 && an.parentNode) { replaceTextNodes(an.parentNode); }
          });
        } else if (m.type === 'attributes') {
          replaceTextNodes(m.target);
        }
      }
    });
    obs.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['placeholder', 'aria-label'] });
  }
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
