let categorias = [];
let itensCategoria = [];
let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

function categoriasMenu(){
        $.ajax({
            url: '/getCategorias',
            method: 'GET',
            dataType: 'json',
            async: false,
            success: function (response) {
                categorias = response.data;
            },
            error: function (xhr, status, error) {
                console.error('Erro ao obter itens:', error);
                alert('Erro ao obter itens. Tente novamente mais tarde.');
            }
        });
    
}

function carregarCategorias() {
    categoriasMenu();
    if (!Array.isArray(categorias)) {
        categorias = [categorias];
    }

    const sidebar = $('#sidebar-menu');
    let html = '';
    categorias.forEach((categoria, index) => {
        html += `<a href="javascript:void(0)" value="${categoria.nome}" style="text-decoration:none; color:black;">
                    <li class="${index === 0 ? "active" : ""}">${categoria.nome}</li>
                 </a>`;
        if(index === 0){
            carregarItens(categoria.nome);
        }
    });
    sidebar.empty();
    sidebar.append(html);

    // Adicionar evento ao #sidebar-menu
    document.getElementById('sidebar-menu').addEventListener('click', function (event) {
        debugger;
        const link = event.target.tagName === 'LI' ? event.target.parentElement : event.target;
        if (link.tagName === 'A') {
             const itens = sidebar.find('li');
             itens.removeClass('active');
 
             const li = link.querySelector('li');
             li.classList.add('active');
 
             const valor = link.getAttribute('value');
             carregarItens(valor);
        }
    });
}

function ItensMenu(){
    $.ajax({
        url: '/getItens',
        method: 'GET',
        dataType: 'json',
        async: false,
        success: function (response) {
            itensCategoria = response.data;
        },
        error: function (xhr, status, error) {
            console.error('Erro ao obter itens:', error);
            alert('Erro ao obter itens. Tente novamente mais tarde.');
        }
    });

}

function carregarItens(categoriaSelecionada) {

    ItensMenu();
    const menuGrid = document.getElementById('menu-grid');
    menuGrid.innerHTML = '';

    itensCategoria.forEach(item => {
        if(item.categoria === categoriaSelecionada){
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('menu-card');
            itemDiv.innerHTML = `
                <img src="https://www.imagensempng.com.br/wp-content/uploads/2021/06/02-25.png" alt="${item.nome}">
                <div class="card-details">
                    <h3>${item.nome}</h3>
                    <p>${item.preco}</p>
                </div>
            `;
            // Chama a função para exibir os ingredientes ao clicar no card
            itemDiv.onclick = () => mostrarModal(item);
            menuGrid.appendChild(itemDiv);
        }
    });

    
}

function mostrarModal(item) {
    const modal = document.getElementById('modalIngredientes');
    const ingredientesLista = document.getElementById('ingredientes-lista');
    ingredientesLista.innerHTML = ''; // Limpa os ingredientes anteriores

    if (typeof item.ingredientes === 'string') {
        item.ingredientes = JSON.parse(item.ingredientes);
    }

    if (item && Array.isArray(item.ingredientes)) {
        item.ingredientes.forEach(ingrediente => {
            const ingredienteLi = document.createElement('li');
            ingredienteLi.classList.add('ingrediente-item');
            ingredienteLi.innerHTML = `
                <span class="ingrediente-nome">${ingrediente.nome}</span>
                <div class="quantidade-container">
                    <button class="icon-btn" onclick="alterarQuantidadeIngrediente('${item.id}', '${ingrediente.nome}', 'decrease')">-</button>
                    <span id="quantidade-${item.id}-${ingrediente.nome}" class="quantidade">${ingrediente.quantidade || 0}</span>
                    <button class="icon-btn" onclick="alterarQuantidadeIngrediente('${item.id}', '${ingrediente.nome}', 'increase')">+</button>
                </div>
            `;
            ingredientesLista.appendChild(ingredienteLi);
        });
    } else {
        console.log("Ingredientes não encontrados ou não são um array válido");
    }

    modal.style.display = "block";
    modal.setAttribute('data-item-id', item.id); // Salva o id do item na modal
}

function fecharModal() {
    const modal = document.getElementById('modalIngredientes');
    modal.style.display = "none";
}

function alterarQuantidadeIngrediente(itemId, ingredienteNome, operacao) {
    const item = categorias.find(i => i.id == itemId);
    if (item) {
        const ingrediente = item.ingredientes.find(i => i.nome === ingredienteNome);
        if (ingrediente) {
            if (operacao === 'increase') {
                ingrediente.quantidade = (ingrediente.quantidade || 0) + 1;
            } else if (operacao === 'decrease' && ingrediente.quantidade > 0) {
                ingrediente.quantidade--;
            }

            const quantidadeElement = document.getElementById(`quantidade-${itemId}-${ingredienteNome}`);
            if (quantidadeElement) {
                quantidadeElement.textContent = ingrediente.quantidade;
            }
        }
    }
}

function adicionarAoCarrinho() {
    const modal = document.getElementById('modalIngredientes');
    const itemId = modal.getAttribute('data-item-id');
    const item = itensCategoria.find(i => i.id == parseInt(itemId));
    
    if (!item) {
        console.error("Item não encontrado");
        return;
    }

    // Se o item não tiver ingredientes, adiciona no carrinho com ingredientes vazios
    const ingredientesSelecionados = item.ingredientes && item.ingredientes.length > 0
        ? item.ingredientes.filter(ingrediente => ingrediente.quantidade > 0)
        : [];

    const itemCarrinho = {
        id: item.id,
        nome: item.nome,
        preco: item.preco,
        ingredientes: ingredientesSelecionados,
        quantidade: 1
    };

    // Se não houver ingredientes selecionados, ainda adiciona o item com uma lista vazia
    if (ingredientesSelecionados.length === 0) {
        itemCarrinho.ingredientes = []; // Garantir que ingredientes seja um array vazio
    }

    carrinho.push(itemCarrinho);
    localStorage.setItem('carrinho', JSON.stringify(carrinho)); // Salva o carrinho atualizado
    atualizarCarrinho();
    fecharModal();
}

function atualizarCarrinho() {
    const cartItemsContainer = document.getElementById('cart-items');
    const finalizarPedidoBtn = document.getElementById('finalizarPedido');

    if (carrinho.length === 0) {
        finalizarPedidoBtn.disabled = true;
    } else {
        finalizarPedidoBtn.disabled = false;
    }

    atualizarTotal();
}

function atualizarTotal() {
    const totalElement = document.getElementById('total-price');
    let total = 0;
    carrinho.forEach(item => {
        total += parseFloat(item.preco.replace('R$', '').replace(',', '.')) * item.quantidade;
    });
    totalElement.textContent = `R$ ${total.toFixed(2)}`;
}

function removerDoCarrinho(index, atualizarResumo = false) {
    carrinho.splice(index, 1);
    localStorage.setItem('carrinho', JSON.stringify(carrinho)); // Atualiza o carrinho no localStorage
    atualizarCarrinho();

    // Atualiza o resumo da compra, se necessário
    if (atualizarResumo) {
        exibirResumoCompra();
    }
}

function exibirResumoCompra() {
    debugger;
    const resumoContainer = document.getElementById('resumo-compra');
    resumoContainer.innerHTML = ''; // Limpa os itens anteriores

    carrinho.forEach((item, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('resumo-item');

        // Criação da parte de ingredientes com cada um em uma linha
        const ingredientesDiv = document.createElement('div');
        ingredientesDiv.classList.add('ingredientes-lista'); // Adicione uma classe para estilizar se necessário

        if (item.ingredientes.length > 0) {
            item.ingredientes.forEach(ingrediente => {
                const ingredienteParagrafo = document.createElement('p');
                ingredienteParagrafo.textContent = `${ingrediente.nome} (${ingrediente.quantidade})`;
                ingredientesDiv.appendChild(ingredienteParagrafo);
            });
        } else {
            const noIngredients = document.createElement('p');
            noIngredients.textContent = 'Nenhum ingrediente selecionado';
            ingredientesDiv.appendChild(noIngredients);
        }

        itemDiv.innerHTML = `
            <div class="item-info">
                <p><strong>${item.nome}</strong></p>
            </div>
            <div class="remove-container">
                <button class="remove-btn" onclick="removerDoCarrinho(${index}, true)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        // Adiciona a lista de ingredientes no item do carrinho
        itemDiv.querySelector('.item-info').appendChild(ingredientesDiv);

        resumoContainer.appendChild(itemDiv);
    });

    const total = document.getElementById('total-compraFinal');
    total.textContent = `R$ ${calcularTotalCompra()}`;
    document.getElementById('modalFinalizarCompra').style.display = "block"; // Exibe a modal
}

function calcularTotalCompra() {
    let total = 0;
    carrinho.forEach(item => {
        total += parseFloat(item.preco.replace('R$', '').replace(',', '.')) * item.quantidade;
    });
    return total.toFixed(2);
}

function casaDelivery(){
    document.getElementById("modalFinalizarCompra").style.display = "none";
    document.getElementById("modalEscolherMetodo").style.display = "block";
}

function confirmarCompra() {
    document.getElementById('modalPagamento').style.display = "block";
    document.getElementById('modalFinalizarCompra').style.display = "none"; // Fecha a modal de resumo de compra
}

function finalizarCompra() {
    alert("Pagamento realizado com sucesso!");
    carrinho = [];  // Limpa o carrinho
    localStorage.removeItem('carrinho'); // Remove os itens do carrinho do localStorage
    atualizarCarrinho(); // Atualiza a exibição do carrinho
    fecharModalPagamento(); // Fecha a modal de pagamento
}

function fecharModalFinalizar() {
    document.getElementById('modalFinalizarCompra').style.display = "none";
}

let metodoPagamentoSelecionado = '';

function selecionarPagamento(metodo) {
    metodoPagamentoSelecionado = metodo;
    
    // Esconde os formulários
    document.getElementById('cartao-form').style.display = 'none';
    document.getElementById('pix-qr').style.display = 'none';
    
    // Mostra o formulário apropriado com base na escolha
    if (metodo === 'pix') {
        document.getElementById('pix-qr').style.display = 'block'; // Exibe o QR Code do PIX
    } else if (metodo === 'debito' || metodo === 'credito') {
        document.getElementById('cartao-form').style.display = 'block'; // Exibe os dados do cartão
    }
}

function finalizarPagamento() {
    if (metodoPagamentoSelecionado === 'pix') {
        alert("Pagamento via PIX está aguardando confirmação.");
    } else if (metodoPagamentoSelecionado === 'debito' || metodoPagamentoSelecionado === 'credito') {
        // Aqui você pode pegar os dados do cartão para enviar ao backend, mas por enquanto, só exibe um alerta.
        const numeroCartao = document.getElementById('numero-cartao').value;
        const dataVencimento = document.getElementById('data-vencimento').value;
        const cvv = document.getElementById('cvv').value;

        if (numeroCartao && dataVencimento && cvv) {
            alert("Pagamento realizado com sucesso via " + metodoPagamentoSelecionado);
        } else {
            alert("Preencha todos os campos do cartão.");
        }
    }
    fecharModalPagamento(); // Fecha a modal após o pagamento
}

function fecharModalPagamento() {
    document.getElementById('modalPagamento').style.display = 'none';
}

document.querySelector('.pay-btn').addEventListener('click', exibirResumoCompra);
carregarCategorias();
atualizarCarrinho();