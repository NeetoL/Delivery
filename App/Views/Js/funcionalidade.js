    const menuLinks = document.querySelectorAll('.nav-link');
    const dashboardContent = document.getElementById('dashboardContent');
    const contasContent = document.getElementById('contasContent');
    const itensContent = document.getElementById('itensContent');
    const historicoPedidosContent = document.getElementById('historicoPedidosContent');

    const contasTable = document.getElementById('contasTable');
    const itensTable = document.getElementById('itensTable');

    function clearTables() {
        contasTable.innerHTML = '';
        itensTable.innerHTML = '';
    }
    window.clearTables = clearTables;

    function setActiveLink(screen) {
        menuLinks.forEach(link => link.classList.remove('active'));
        const activeLink = document.querySelector(`[data-screen="${screen}"]`);
        if (activeLink) activeLink.classList.add('active');
    }
    window.setActiveLink = setActiveLink 

    function saveActiveScreen(screen) {
        localStorage.setItem('activeScreen', screen);
    }
    window.saveActiveScreen = saveActiveScreen;

    function getActiveScreen() {
        return localStorage.getItem('activeScreen') || 'dashboard';
    }
    window.getActiveScreen=getActiveScreen;

    var cancelarEdicaoCategoria = false;
    async function showCategoriasItens() {
        saveActiveScreen('itens');
        document.getElementById('contentTitle').textContent = 'Gerenciar Menu';
        setActiveLink('itens');
        dashboardContent.style.display = 'none';
        contasContent.style.display = 'none';
        itensContent.style.display = 'block';
        historicoPedidosContent.style.display = 'none';
    
        clearTables();
        const itensData = await obterItens();
        debugger;
        const categoriasData = await obterCategorias();
    
        const itensTable = document.getElementById('itensTable');
        itensTable.innerHTML = '';
    
        const categoriasAgrupadas = itensData.reduce((agrupados, item) => {
            if (!agrupados[item.categoria]) {
                agrupados[item.categoria] = [];
            }
            agrupados[item.categoria].push(item);
            return agrupados;
        }, {});
    
        Object.keys(categoriasAgrupadas).forEach(categoriaNome => {
          // Pegando a categoria completa com id e nome, se 'categoriasAgrupadas[categoriaNome]' for um objeto
            const categoria = categoriasAgrupadas[categoriaNome][0]; // Supondo que o id é o mesmo para todos os itens da mesma categoria
            const categoriaId = categoria.id; // Use o id da categoria (ajuste conforme seu modelo de dados)
            const categoriaRow = document.createElement('tr');
            categoriaRow.classList.add('treeview');
            categoriaRow.setAttribute('data-id', categoriaId);
        
            categoriaRow.innerHTML = `
                <td>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw toggle-arrow"></i> 
                    <span class="category-name">${categoriaNome}</span>
                    <input type="text" class="form-control category-edit-input" value="${categoriaNome}" style="display: none;">
                    <input type="text" class="form-control category-id" value="${categoriaId}" style="display: none;">
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="actionsCat">
                    <button class="btn btn-sm btn-primary edit-category-btn" onclick="editarCategoria(this)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="removerCategoria(${categoriaId})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="modal('item')">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
            `;
            
            itensTable.appendChild(categoriaRow);
    
            categoriasAgrupadas[categoriaNome].forEach(item => {
                const itemRow = document.createElement('tr');
                itemRow.classList.add('treeview-item');
                itemRow.setAttribute('data-parent-id', categoriaId);
                itemRow.style.display = 'none';
                
                const categoriaSelect = categoriasData
                    .map(cat => 
                        `<option value="${cat.id}" ${cat.nome === item.categoria ? 'selected' : ''}>
                            ${cat.nome}
                        </option>`
                    )
                    .join('');
    
                itemRow.innerHTML = `
                    <td class="col-lg-4 align-items-center" style="width:25%">
                        <i class="fas fa-box ml-4"></i>
                        <span class="item-name">${item.nome}</span>
                        <input type="text" class="item-id" value="${item.id}" style="display: none;">
                        <input type="text" class="form-control ml-2 item-edit-input" value="${item.nome}" style="display: none;">
                    </td>
                    <td class="col-lg-4 align-items-center" style="width:10%">
                        <span class="item-price">R$ ${parseFloat(item.preco).toFixed(2)}</span>
                        <input type="number" class="form-control ml-2 item-edit-price" value="${item.preco}" style="display: none;">
                    </td>
                    <td style="width:20%"><select class="form-control select2 select2-hidden-accessible" disabled>${categoriaSelect}</select></td>
                    <td class="col-lg-4 align-items-center" style="width:25%">
                        <img src="${item.imagem}" class="item-image-preview" alt="Imagem" style="width: 50px; height: 50px; display: inline;">
                        <input type="file" class="form-control ml-2 item-edit-image" style="display: none;">
                    </td>
                    <td class="align-items-center actions" style="width:20%">
                        <button class="btn btn-sm btn-primary" onclick="editarItem(this)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-item-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
    
                itensTable.appendChild(itemRow);
            });
        });
        
        
        
        
        document.querySelectorAll('.treeview').forEach(row => {
            row.addEventListener('click', function (event) {
                if (event.target.closest('.edit-category-btn') || event.target.closest('.delete-category-btn')) {
                    return;
                }
                
                if (event.target.closest('.category-edit-input')) {
                    event.stopPropagation();
                    return;
                }
                

                if(cancelarEdicaoCategoria){
                    cancelarEdicaoCategoria = false;
                    return;
                }
                
                const arrow = this.querySelector('.toggle-arrow');
                const parentId = this.getAttribute('data-id');
                const children = document.querySelectorAll(`.treeview-item[data-parent-id="${parentId}"]`);
        
                let isExpanded = false;

                children.forEach(child => {
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
    
    async function showHistoricoPedidos(){
        saveActiveScreen('historicopedidos');
        document.getElementById('contentTitle').textContent = 'Histórico de Pedidos';
        setActiveLink('historicopedidos');
        dashboardContent.style.display = 'none';
        contasContent.style.display = 'none';
        itensContent.style.display = 'none';
        historicoPedidosContent.style.display = 'block';
    
        clearTables();
        $.ajax({
            url: '/getPedidos', // URL da rota
            method: 'GET', // Método HTTP
            success: function (response) {
                if (response.data && response.data.length > 0) {
                    const pedidosContainer = $('#historicoPedidoTable');
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
                                </tr>
                            `);
                    });
                     $('.ver-pedido').on('click', function () {
                        const pedidoId = $(this).data('id');
                        exibirPedidoModal(pedidoId);
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
}
    
    async function showContas() {
    saveActiveScreen('contas');
    setActiveLink('contas');
    document.getElementById('contentTitle').textContent = 'Gerenciar Usuários';
    dashboardContent.style.display = 'none';
    contasContent.style.display = 'block';
    itensContent.style.display = 'none';
    historicoPedidosContent.style.display = 'none';

    clearTables();
    const contasData = await obterUsuarios();
    contasData.forEach(conta => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${conta.id}</td>
            <td>${conta.nome}</td>
            <td>${conta.email}</td>
            <td>
                <button class="btn btn-warning btn-sm">Editar</button>
                <button class="btn btn-danger btn-sm">Deletar</button>
            </td>
        `;
        contasTable.appendChild(row);
    });
}
    
    function showDashboard() {
        saveActiveScreen('dashboard');
        setActiveLink('dashboard');
        document.getElementById('contentTitle').textContent = 'Lista de Pedidos';
        dashboardContent.style.display = 'block';
        contasContent.style.display = 'none';
        itensContent.style.display = 'none';
        historicoPedidosContent.style.display = 'none';
        
        obterDashboardData();
    }

    function showScreen(screen) {
        switch (screen) {
            case 'dashboard':
                showDashboard();
                break;
            case 'contas':
                showContas();
                break;
            case 'itens':
                showCategoriasItens();
                break;
            case 'historicopedidos':
                showHistoricoPedidos();
                break;
            default:
                showDashboard();
        }
    }

    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            const screen = link.dataset.screen;
            showScreen(screen);
        });
    });

    showScreen(getActiveScreen());
