<?php
    session_start();
    if(!isset($_SESSION['user_id'])){
        header('Location: /logout');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://printjs.crabbly.com/css/print.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f4f6f9;
        }
        .content-wrapper {
            min-height: calc(100vh - 120px);
        }

        /* Ajuste de tamanho e responsividade para tabelas */
        .table {
            width: 100%;
            table-layout: fixed; /* Força a tabela a usar um layout fixo para manter o controle sobre o tamanho das células */
        }

        /* Para dispositivos menores (móveis) */
        @media (max-width: 768px) {
            .table-responsive {
                -webkit-overflow-scrolling: touch;
                overflow-x: auto;
            }
        
            .table th, .table td {
                white-space: normal; /* Permite a quebra de linha no conteúdo */
            }
        
            .card-body {
                padding: 0.5rem;
            }
        }

        /* Ajuste do conteúdo das células da tabela para que os textos não saiam do limite */
        .table td, .table th {
            word-wrap: break-word; /* Quebra a linha quando o conteúdo for muito longo */
            word-break: break-word; /* Quebra as palavras longas */
            padding: 8px; /* Ajuste de espaçamento */
            white-space: normal; /* Permite a quebra de linha no conteúdo */
        }
        
        /* Adiciona um scroll horizontal nas tabelas se o conteúdo for grande */
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
        
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column">
                        <li class="nav-item">
                            <a href="#" class="nav-link" id="dashboardLink" data-screen="dashboard">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Pedidos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" id="contasLink" data-screen="contas">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Gerenciar Contas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" id="itensLink" data-screen="itens">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Gerenciar Cardápio</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" id="itensLink" data-screen="historicopedidos">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Histórico de Pedidos</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0 text-dark" id="contentTitle">Lista de Pedidos</h1>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <!-- Dashboard Content -->
                    <div id="dashboardContent">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title">Pedidos Pendentes</h5>
                                                <p id="pedidosPendentes" class="card-text">Carregando...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-white bg-success">
                                            <div class="card-body">
                                                <h5 class="card-title">Total no Caixa</h5>
                                                <p id="totalCaixa" class="card-text">R$ 0,00</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-white bg-warning">
                                            <div class="card-body">
                                                <h5 class="card-title">Pedidos no Dia</h5>
                                                <p id="pedidosDia" class="card-text">Carregando...</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-white bg-danger">
                                            <div class="card-body">
                                                <h5 class="card-title">Pedidos Cancelados</h5>
                                                <p id="pedidosCancelados" class="card-text">Carregando...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabela de Pedidos Pendentes -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Pedidos Pendentes</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered" id="pedidosPendenteTable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Pedido</th>
                                            <th>Data do Pedido</th>
                                            <th>Status</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pedidosPendenteBody">
                                        <!-- As linhas da tabela serão inseridas via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Contas Content -->
                    <div id="contasContent" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Gerenciar Contas</h3>
                                <button type="button" class="btn btn-primary float-end" onclick="modal('user')">
                                    <i class="fas fa-user-plus"></i> Adicionar Usuário
                                </button>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contasTable"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Wrapper -->
                    <div id="itensContent" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Árvore de Categorias e Itens</h3>
                                <button type="button" class="btn btn-success float-end" onclick="modal('category')">
                                    <i class="fas fa-plus"></i> Adicionar Categoria
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <tbody id="itensTable">
                                            <!-- Linhas de dados serão inseridas aqui -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Historico de pedidos Content -->
                    <div id="historicoPedidosContent" style="display: none;">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Histórico de Pedidos</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Pedido</th>
                                            <th>Data do pedido</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historicoPedidoTable"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pedidoModalLabel">Detalhes do Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <!-- Main Content Wrapper -->
                        <div id="PedidosContent">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Árvore de Categorias e Itens</h3>
                                    <button type="button" class="btn btn-secondary float-end" onclick="imprimirNota()">
                                        <i class="fas fa-print"></i> Imprimir Nota
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tbody id="PedidosTable">
                                                <!-- Linhas de dados serão inseridas aqui -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div id="confirmModal" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Excluindo essa categoria, você também excluirá os itens dela. Deseja realmente prosseguir?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Sim</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Adicionar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="dynamicForm"></form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="saveButton">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; 2024 <a href="https://www.linkedin.com/in/luizrodriguescastroneto/">LN Sistemas</a>.</strong> Todos os direitos reservados.
        </footer>
    </div>
    <!-- Scripts necessários para o funcionamento do site -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1.0/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="App/Views/Js/fetch.js"></script>
    <script src="App/Views/Js/cruditens.js"></script>
    <script src="App/Views/Js/crudcategorias.js"></script>
    <script src="App/Views/Js/funcionalidade.js"></script>
    <!-- Via CDN -->
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
    <script>
        setInterval(buscarPedidos, 5000); // Busca a cada 5 segundos
        var todosPedidos = [];        
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        let isPlaying = false;
        let lastPedidoId = null;
        
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: "3000",
            extendedTimeOut: "1000",
            positionClass: "toast-top-right",
            onclick: function () {
                window.location.reload();
            }
        };
        
        $.ajax({
            url: '/getPedidos', // URL da rota
            method: 'GET', // Método HTTP
            success: function (response) {
                if (response.data && response.data.length > 0) {
                    const pedidosContainer = $('#pedidosPendenteBody');
                    pedidosContainer.empty(); // Limpa o container
                    var infoUsuario = [];
                    var infoPedido = [];
                    todosPedidos = response;
                    response.data.forEach(pedido => {
                        infoUsuario = JSON.parse(pedido.json_usuario);
                        infoPedido = JSON.parse(pedido.json_pedido);
                        const date = new Date(pedido.data_criacao);
                        const formattedDate = date.toLocaleString('pt-BR', {
                                                                                day: '2-digit',
                                                                                month: '2-digit',
                                                                                year: 'numeric',
                                                                                hour: '2-digit',
                                                                                minute: '2-digit',
                                                                                second: '2-digit'
                                                                            });
                        if(pedido.status_pagamento !== "cancelado" && pedido.status_pagamento !== "despachado"){
                            pedidosContainer.append(`
                                <tr class="text-center">
                                    <td>${pedido.id}</td>
                                    <td>${infoUsuario.name}</td>
                                    <td>
                                    <button class="btn btn-primary ver-pedido" data-id="${pedido.id}">
                                        Ver Pedido
                                    </button>
                                    </td>
                                    <td>${formattedDate}</td>
                                    <td>${pedido.status_pagamento}</td>
                                    <td class="actionsCat">
                                        <button class="btn btn-sm btn-success edit-category-btn" onclick="statusPedido(${pedido.id},'despachado')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="statusPedido(${pedido.id},'cancelado')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        }
                    });
                     $('.ver-pedido').on('click', function () {
                        const pedidoId = $(this).data('id');
                        exibirPedidoModal(pedidoId); // Chama a função para exibir a modal com o pedido
                     });
                } else {
                    $('#pedidos-container').html('<p>Sem pedidos disponíveis.</p>');
                }
            },
            error: function (xhr, status, error) {
                // Manipula erros na requisição
                console.error('Erro ao obter pedidos:', error);
                $('#pedidos-container').html('<p>Erro ao carregar os pedidos. Tente novamente mais tarde.</p>');
            }
        });
            
        function exibirPedidoModal(pedidoId) {
            // Encontra o pedido pelo ID na lista armazenada
            const pedido = todosPedidos.data.find(p => p.id == pedidoId);
            const infoPedido = JSON.parse(pedido.json_pedido);
            const infoUsuario = JSON.parse(pedido.json_usuario);
            const itensContainer = $('#itensPedidoContainer');
            var html = '';
            
            if (!pedido) {
                alert('Pedido não encontrado!');
                return;
            }
        
            itensContainer.empty(); // Limpa os itens antigos
        
            if (infoPedido && Array.isArray(infoPedido) && infoPedido.length > 0) {
                infoPedido.forEach(item => {
                    html += `<tr class="treeview" data-id="${item.id}">`;
                    
                    html += `
                        <td>
                            <i class="expandable-table-caret fas fa-caret-right fa-fw toggle-arrow"></i> 
                            <span class="category-name">${item.id}</span>
                        </td>
                        <td>${item.nome}</td>
                        <td>R$${item.preco}</td>
                    `;
        
                    html += `</tr>`;
        
                    // Criação dos ingredientes ou item "sem ingredientes"
                    if (item.ingredientes && Array.isArray(item.ingredientes) && item.ingredientes.length > 0) {
                        item.ingredientes.forEach(ingrediente => {
                            html += `<tr class="treeview-item" style="display:none">`;
                            html += `<td><i class="fas fa-box ml-4"></i></td>`;
                            html += `<td>${ingrediente.nome}</td>`;
                            html += `<td>Quantidade: ${ingrediente.quantidade}</td>`;
                            html += `</tr>`;
                        });
                    } else {
                        html += `<tr class="treeview-item" style="display:none">`;
                        html += `<td colspan="3">Não possui ingredientes</td>`;
                        html += `</tr>`;
                    }
                });
        
                // Adiciona os itens gerados à tabela
                $("#PedidosTable").empty();
                $("#PedidosTable").append(html);
            } else {
                itensContainer.append('<p>Este pedido não contém itens.</p>');
            }
        
            // Exibe a modal com os detalhes do pedido
            $('#pedidoModal').modal('show');
        
            // Ativa o acordeão para cada categoria (com base no evento de clique nas linhas)
            document.querySelectorAll('.treeview').forEach(row => {
                row.addEventListener('click', function (event) {
                    if (event.target.closest('.edit-category-btn') || event.target.closest('.delete-category-btn')) {
                        return;  // Evita que o clique nos botões de edição e exclusão seja tratado
                    }
        
                    if (event.target.closest('.category-edit-input')) {
                        event.stopPropagation();  // Impede propagação do clique no campo de edição
                        return;
                    }
        
                    const arrow = this.querySelector('.toggle-arrow');
                    const children = this.nextElementSibling ? this.nextElementSibling.classList.contains('treeview-item') : false;
        
                    let isExpanded = false;
                    
                    // Expande ou colapsa os itens diretamente abaixo da categoria clicada
                    const nextRows = [];
                    let currentRow = this.nextElementSibling;
        
                    while (currentRow && !currentRow.classList.contains('treeview')) {
                        nextRows.push(currentRow);
                        currentRow = currentRow.nextElementSibling;
                    }
        
                    nextRows.forEach(child => {
                        if (child.style.display === 'none' || !child.style.display) {
                            child.style.display = 'table-row';  // Expande
                            isExpanded = true;
                        } else {
                            child.style.display = 'none';  // Colapsa
                        }
                    });
        
                    // Alterna o ícone da seta
                    if (isExpanded) {
                        arrow.classList.remove('fa-caret-right');
                        arrow.classList.add('fa-caret-down');
                    } else {
                        arrow.classList.remove('fa-caret-down');
                        arrow.classList.add('fa-caret-right');
                    }
                });
            });
        }

        function modal(tipo) {
            const formContainer = document.getElementById('dynamicForm');
            formContainer.innerHTML = ''; // Limpa o formulário antes de adicionar os campos
        
            const modalTitle = document.getElementById('addModalLabel');
            
            // Gerar os campos do formulário de acordo com o tipo
            if (tipo === 'user') {
                modalTitle.innerText = 'Adicionar Usuário';
                formContainer.appendChild(createInput('Nome', 'text', 'userName'));
                formContainer.appendChild(createInput('Email', 'email', 'userEmail'));
                formContainer.appendChild(createInput('Senha', 'password', 'userPassword'));
            } else if (tipo === 'category') {
                modalTitle.innerText = 'Adicionar Categoria';
                formContainer.appendChild(createInput('Nome da Categoria', 'text', 'categoryName'));
            } else if (tipo === 'item') {
                modalTitle.innerText = 'Adicionar Item';
                formContainer.appendChild(createInput('Nome do Item', 'text', 'itemName'));
                formContainer.appendChild(createInput('Preço', 'text', 'itemPrice'));
                formContainer.appendChild(createInput('Categoria', 'text', 'itemCategory'));
                formContainer.appendChild(createFileInput('Imagem', 'itemImage'));
            }
        
            // Exibe a modal
            $('#addModal').modal('show');
        }
        
        function createInput(label, type, name) {
            const div = document.createElement('div');
            div.classList.add('mb-3');
            
            const inputLabel = document.createElement('label');
            inputLabel.setAttribute('for', name);
            inputLabel.classList.add('form-label');
            inputLabel.innerText = label;
            
            const input = document.createElement('input');
            input.type = type;
            input.classList.add('form-control');
            input.id = name;
            input.name = name;
            input.required = true;
            
            div.appendChild(inputLabel);
            div.appendChild(input);
            
            return div;
        }
        
        function createFileInput(label, name) {
            const div = document.createElement('div');
            div.classList.add('mb-3');
            
            const inputLabel = document.createElement('label');
            inputLabel.setAttribute('for', name);
            inputLabel.classList.add('form-label');
            inputLabel.innerText = label;
            
            const input = document.createElement('input');
            input.type = 'file';
            input.classList.add('form-control');
            input.id = name;
            input.name = name;
            
            div.appendChild(inputLabel);
            div.appendChild(input);
            
            return div;
        }
        
        function playNotificationSound() {
            if (isPlaying) return; // Evita múltiplas reproduções simultâneas
        
            isPlaying = true;
            fetch('/App/Views/audio/campainha.mp3')
                .then(response => response.arrayBuffer())
                .then(data => audioContext.decodeAudioData(data))
                .then(buffer => {
                    const source = audioContext.createBufferSource();
                    source.buffer = buffer;
                    source.connect(audioContext.destination);
                    source.start(0);
        
                    let playCount = 1; // Inicia o contador de toques
                    const maxPlays = 3; // Número máximo de execuções
        
                    source.onended = () => {
                        if (playCount < maxPlays) {
                            playCount++;
                            source.start(0); // Reproduz novamente
                        } else {
                            isPlaying = false; // Libera para novas reproduções
                        }
                    };
                })
                .catch(error => {
                    console.error('Erro ao reproduzir som:', error);
                    toastr.error("Erro ao reproduzir áudio.", "Erro!");
                    isPlaying = false; // Libera para novas reproduções mesmo em erro
                });
        }

        function buscarPedidos() {
            fetch('/getPedidos')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao buscar pedidos: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(todosPedidoss => {
                    const pedidosTabela = getTableAsJson();
                    debugger;
                    if(pedidosTabela.length === 0){
                        if(todosPedidoss.data[0].status_pagamento === 'pendente' || todosPedidoss.data[0].status_pagamento === 'Aprovado'){
                            playNotificationSound();
                            toastr.success("Novo pedido recebido!", "Notificação");
                        }
                    }
                    if(pedidosTabela.length > 0){
                        if (todosPedidoss.data[0].id !== parseInt(pedidosTabela[0].column1, 10)) {
                            playNotificationSound();
                            toastr.success("Novo pedido recebido!", "Notificação");
                        }
                    }
                })
                .catch(error => {
                    console.error("Erro na busca de pedidos:", error);
                });
        }
        
        function getTableAsJson() {
            const table = $('#pedidosPendenteTable');
            const rows = table.find('tr');
            const data = [];
        
            rows.each(function () {
                const row = $(this);
                const columns = row.find('td');
                if (columns.length > 0) { // Ignorar cabeçalho ou linhas vazias
                    const rowData = {};
                    columns.each(function (index) {
                        rowData[`column${index + 1}`] = $(this).text().trim();
                    });
                    data.push(rowData);
                }
            });
        
            return data;
        }
        
        function adicionarPedidoNaTabela(pedido) {
            const tabela = document.getElementById("PedidosTable");
        
            let html = `
                <tr class="treeview" data-id="${pedido.id}" data-datetime="${pedido.data_hora}">
                    <td>
                        <i class="expandable-table-caret fas fa-caret-right fa-fw toggle-arrow"></i>
                        <span class="category-name">${pedido.id}</span>
                    </td>
                    <td>${pedido.nome}</td>
                    <td>${pedido.preco}</td>
                    <td class="actionsCat">
                        <button class="btn btn-sm btn-success edit-category-btn" onclick="editarCategoria(this)">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="removerCategoria(${pedido.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        
            if (pedido.ingredientes && Array.isArray(pedido.ingredientes) && pedido.ingredientes.length > 0) {
                pedido.ingredientes.forEach(ingrediente => {
                    html += `
                        <tr class="treeview-item" style="display:none">
                            <td><i class="fas fa-box ml-4"></i>${ingrediente.id}</td>
                            <td>${ingrediente.nome}</td>
                            <td>${ingrediente.preco}</td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr class="treeview-item" style="display:none">
                        <td colspan="3">Não possui ingredientes</td>
                    </tr>
                `;
            }
        
            tabela.insertAdjacentHTML("beforeend", html);
        
            // Reaplica o evento de clique para os novos elementos
            document.querySelectorAll('.treeview').forEach(row => {
                row.addEventListener('click', function (event) {
                    const arrow = this.querySelector('.toggle-arrow');
                    const nextRows = [];
                    let currentRow = this.nextElementSibling;
        
                    while (currentRow && !currentRow.classList.contains('treeview')) {
                        nextRows.push(currentRow);
                        currentRow = currentRow.nextElementSibling;
                    }
        
                    let isExpanded = false;
                    nextRows.forEach(child => {
                        if (child.style.display === 'none' || !child.style.display) {
                            child.style.display = 'table-row';
                            isExpanded = true;
                        } else {
                            child.style.display = 'none';
                        }
                    });
        
                    if (isExpanded) {
                        arrow.classList.remove('fa-caret-right');
                        arrow.classList.add('fa-caret-down');
                    } else {
                        arrow.classList.remove('fa-caret-down');
                        arrow.classList.add('fa-caret-right');
                    }
                });
            });
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
                    debugger;
                    if(status === 'success'){
                    toastr.success("Pedido foi despachado!", "Notificação");
                    }else if(status === 'cancelado'){
                        toastr.success("Pedido foi cancelado!", "Notificação");
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    
                },
                error: function(xhr, status, error) {
                    // Aqui você pode tratar o erro da requisição
                    console.error("Erro ao atualizar o pedido:", error);
                }
            });
        }
        
        function imprimirNota(){
            toastr.warning("Isso ainda não funciona. Aguarde!", "Notificação");
        }
        
        document.getElementById('saveButton').addEventListener('click', async () => {
            const formContainer = document.getElementById('dynamicForm');
            const formData = new FormData(formContainer);
            
            let data = {};
            
            // Coletar os dados de acordo com o formulário
            formData.forEach((value, key) => {
                data[key] = value;
            });
        
            try {
                const response = await fetch('/api/endpoint', {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
        
                const result = await response.json();
                if (response.ok) {
                    alert('Dados salvos com sucesso!');
                    $('#addModal').modal('hide');
                } else {
                    alert('Erro ao salvar!');
                }
            } catch (error) {
                alert('Erro na comunicação com o servidor');
            }
        });
    </script>
    