let categorias = [];
let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
let informacoesUsuario = [];
        var idPedido = "";

        function fecharModalEscolherMetodo() {
            document.getElementById("modalEscolherMetodo").style.display = "none";
        }        

        function createPayment() {
            informacoesUsuario = JSON.parse(localStorage.getItem('informacoesUsuario'));
            const totalPedido = informacoesUsuario.total.replace("R$ ", "");
            debugger;
            gravarPedido(carrinho, informacoesUsuario,totalPedido);
            // Exibe o carregamento e esconde o restante
            document.getElementById("loadingMessage").style.display = "block";
            document.querySelector(".user-info").hidden = true;
            document.querySelector(".payment-options").hidden = true;
            document.getElementById("countdown").style.display = "none";
            document.querySelector(".paymentData").hidden = true;
            const body = {
                name: informacoesUsuario.name,
                email: informacoesUsuario.email,
                value: "0.01"//informacoesUsuario.total
            };
            $.post("/criar", body, (data, status) => {
                debugger;
                data = JSON.parse(data);
                if (data.id != undefined) {
                    // Atualiza as informações do QR Code
                    document.getElementById("qr_code_img").src = "data:image/png;base64," + data.qr_code_base64;
                    document.getElementById("qr_code_cp").value = data.qr_code;
        
                    // Exibe os dados de pagamento e esconde o carregamento
                    document.getElementById("loadingMessage").style.display = "none";
                    document.querySelector(".paymentData").hidden = false;
                    document.querySelector("#cardPayment").hidden = true;
        
                    // Configura a contagem regressiva e verifica o pagamento
                    const paymentReference = data.id;
                    startCountdown(5 * 60);
                    checkPaymentInterval(paymentReference);
        
                    // Exibe a contagem regressiva
                    document.getElementById('countdown').style.display = 'block';
                } else {
                    alert("Erro ao gerar o pagamento. Tente novamente.");
                    // Restaura a interface caso ocorra um erro
                    document.getElementById("loadingMessage").style.display = "none";
                    document.querySelector(".user-info").hidden = false;
                    document.querySelector(".payment-options").hidden = false;
                }
            });

            function checkPaymentInterval(ref) {
                setTimeout(() => {
                    const notificationData = {
                        action: "payment.updated",
                        api_version: "v1",
                        data: { id: ref },
                        date_created: new Date().toISOString(),
                        id: ref,
                        live_mode: true,
                        type: "payment",
                        user_id: 583963771
                    };
                    $.ajax({
                        url: "/notificacao", 
                        method: "POST",
                        contentType: "application/json",
                        data: JSON.stringify(notificationData),
                        success: (data, status) => {
                            data = JSON.parse(data);
                            // Se o status for aprovado
                            if (data.status === "approved") {
                                document.querySelector(".paymentData").hidden = true;
                                document.querySelector(".paymentApproved").hidden = false;
                                document.getElementById('countdown').style.display = 'none';
                                document.getElementById('total-compra').style.display = 'none';
                                localStorage.removeItem('informacoesUsuario');
                                localStorage.removeItem('carrinho');
                                statusPedido(idPedido, 'Aprovado');
                            } else {
                                checkPaymentInterval(ref);
                            }
                        },
                        error: (xhr, status, error) => {
                            console.error('Erro na requisição: ', error);
                        }
                    });
                }, 10e3); // Intervalo de 10 segundos
            }
        }
        
        function statusPedido(id, status) {
            $.ajax({
                url: '/updatePedido',
                type: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify({
                    id: id,
                    status: status
                }),
                success: function(data, status) {
                },
                error: function(xhr, status, error) {
                    // Aqui você pode tratar o erro da requisição
                    console.error("Erro ao atualizar o pedido:", error);
                }
            });
        }

        function gravarPedido(itensCarrinho, InformacoesUsuario,total){
            $.post("/gravarPedido",
                { 
                    itensCarrinho: JSON.stringify(itensCarrinho), 
                    InformacoesUsuario: JSON.stringify(InformacoesUsuario), 
                    total:total
                }, 
                (data, status) => {
                    var dados = JSON.parse(data);
                    idPedido = dados.id;
                }
            );

        }

        function startCountdown(seconds) {
            let remainingTime = seconds;
            const timerElement = document.getElementById('timer');
            
            const interval = setInterval(() => {
                let minutes = Math.floor(remainingTime / 60);
                let seconds = remainingTime % 60;
                if (seconds < 10) seconds = '0' + seconds;
                timerElement.textContent = `${minutes}:${seconds}`;
                
                if (remainingTime <= 0) {
                    clearInterval(interval);
                    timerElement.textContent = "00:00";
        
                    // Exibe a mensagem de tempo expirado
                    alert("Tempo expirado!");
        
                    // Atualiza a tela
                    location.reload();
                } else {
                    remainingTime--;
                }
            }, 1000);
        }

        function copiarQRCode() {
            const textToCopy = document.getElementById('qr_code_cp');
            
            textToCopy.select();
            document.execCommand('copy');
            alert("QrCode copiado!");
        }

        function encerrarCompra(){
            location.reload();
        }
        
        function fecharModalPagamento() {
            document.getElementById('modalPagamento').style.display = "none";
            window.location.reload();
        }
        
        var escolha = '';
        function escolhaModal(escolha){
            const divReferencia = document.getElementById('divReferencia');
            const divEndereco = document.getElementById('divEndereco');
            
            escolha = escolha;
            
            if(escolha === "mesa"){
                divEndereco.style.display="none";
                divReferencia.style.display="none";
            }
            
            if(escolha === "delivery"){
                divEndereco.style.display="block";
                divReferencia.style.display="block";
            }

            
            const total = document.getElementById('total-compraFinal').textContent;
            document.getElementById('total-compra').textContent = total;
            document.getElementById('modalPagamento').style.display = "block";
            document.getElementById('modalEscolherMetodo').style.display = "none";
            
        }

        document.getElementById('cardPayment').addEventListener('click', () => {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const address = document.getElementById('address').value;
            const number = document.getElementById('number').value;
            const complement = document.getElementById('complement').value;
            const reference = document.getElementById('reference').value;
            const total = document.getElementById('total-compra').textContent;
            
            if(escolha === "mesa"){
                if (!name || !email) {
                    alert('Por favor, preencha todos os campos!');
                    return;
                }    
            }
            
            if(escolha === "delivery"){
                if (!name || !email || !address || !number || !complement) {
                    alert('Por favor, preencha todos os campos!');
                    return;
                }    
            }
            
        
            const informacoesUsuario = {
                name: name,
                email: email,
                address: address,
                number: number,
                complement: complement,
                reference: reference,
                total: total
            };
        
            localStorage.setItem('informacoesUsuario', JSON.stringify(informacoesUsuario));
            createPayment();
        });
    
        function abrirModal() {
            document.getElementById("modalIngredientes").style.display = "block";
        }

        function fecharModal() {
            document.getElementById("modalIngredientes").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("modalIngredientes");
            if (event.target === modal) {
                fecharModal();
            }
        }


async function carregarCategorias() {
    categorias = await fetchData('/getItens', 'Erro ao obter itens');

    if (!Array.isArray(categorias)) {
        categorias = [categorias];
    }

    const sidebar = document.querySelector('#sidebar-menu');
    let primeiroCarregado = false; // Controle para o primeiro item

    categorias.forEach((categoria, index) => {
        const categoriaLi = document.createElement('li');
        categoriaLi.textContent = categoria.categoria;
        categoriaLi.onclick = () => {
            document.querySelectorAll('#sidebar-menu li').forEach(li => li.classList.remove('active')); // Remove classe de ativo de outros itens
            categoriaLi.classList.add('active'); // Adiciona classe de ativo ao item clicado
            carregarItens(categoria.categoria);
        };

        sidebar.appendChild(categoriaLi);

        // Carrega o primeiro item por padrão
        if (!primeiroCarregado && index === 0) {
            categoriaLi.classList.add('active'); // Marca como ativo
            carregarItens(categoria.categoria); // Carrega os itens da primeira categoria
            primeiroCarregado = true;
        }
    });
}

function carregarItens(categoriaSelecionada) {
    const menuGrid = document.getElementById('menu-grid');
    menuGrid.innerHTML = '';

    const itensCategoria = categorias.filter(item => item.categoria === categoriaSelecionada);

    itensCategoria.forEach(item => {
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
    const item = categorias.find(i => i.id == itemId);
    
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
