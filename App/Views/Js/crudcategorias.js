function editarCategoria(button) {
    const itemRow = button.closest('tr');
    const nome = itemRow.querySelector('.category-name');
    const icon = itemRow.querySelector('.expandable-table-caret');
    const actionsCell = itemRow.querySelector('.actionsCat');
    const categoriaEditInput = itemRow.querySelector('.category-edit-input');
    const isEditing = categoriaEditInput.style.display === 'block';
    
    if (isEditing) {
        // Ação ao salvar a edição
        alert('teste');
    } else {
        // Inicia o modo de edição
        nome.style.display = 'none';
        categoriaEditInput.style.display = 'block';
        icon.style.display = 'none';
        
        itemRow.classList.add('editing');
        
        actionsCell.innerHTML = `
            <button 
                class="btn btn-sm btn-success save-item-btn" 
                onclick="SalvarEdicaoCategoria(this)"
                title="Salvar Alterações">
                <i class="fas fa-check"></i>
            </button>
            <button 
                class="btn btn-sm btn-secondary cancel-item-btn" 
                onclick="cancelaredicaoCategoria(this)"
                title="Cancelar Edição">
                <i class="fas fa-times"></i>
            </button>
        `;
    }
}
window.editarCategoria=editarCategoria;

function cancelaredicaoCategoria(button) {
    cancelarEdicaoCategoria = true;
    const categoriaRow = button.closest('tr');  // Encontra a linha da categoria
    const nomeCategoria = categoriaRow.querySelector('.category-name');
    const categoriaEditInput = categoriaRow.querySelector('.category-edit-input');
    
    const actionsCell = categoriaRow.querySelector('.actionsCat');
    const icon = categoriaRow.querySelector('.expandable-table-caret');
    icon.style.display = '';
    // Restaura o nome da categoria para o valor original
    nomeCategoria.textContent = categoriaEditInput.value.trim();
    
    // Restaura a visibilidade dos campos
    nomeCategoria.style.display = '';
    categoriaEditInput.style.display = 'none';
    
    // Restaura os botões de ação para os de edição
    actionsCell.innerHTML = `
        <button class="btn btn-sm btn-primary edit-category-btn" onclick="editarCategoria(this)">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-danger delete-category-btn">
            <i class="fas fa-trash"></i>
        </button>
    `;

}
window.cancelaredicaoCategoria=cancelaredicaoCategoria;

async function SalvarEdicaoCategoria(button) {
    cancelarEdicaoCategoria = true;
    const itemRow = button.closest('tr');
    
    const nome = itemRow.querySelector('.category-name').textContent;
    
    const novoNome = itemRow.querySelector('.category-edit-input').value;
    
    let id = 0;
    
    const categorias = await obterCategorias();
    
    $.each(categorias, function(index, obj) {
        if (nome === obj.nome) {
            id = obj.id;
        }
    });

    let resultado = await atualizarCategoria({ id: id, nome: novoNome });
    
    showCategoriasItens();

}
window.SalvarEdicaoCategoria=SalvarEdicaoCategoria;

function removerCategoria(id) {
    var myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    myModal.show();
    document.getElementById('confirmDelete').onclick = async function() {
        var restultado = await excluirCategoria(id);
        debugger;
        myModal.hide(); // Fecha a modal após confirmação
    };

    // Ações quando o usuário cancela
    document.querySelector('.btn-secondary').onclick = function() {
        alert("Não vamos excluir");
        myModal.hide(); // Fecha a modal após cancelamento
    };
}
window.removerCategoria=removerCategoria;