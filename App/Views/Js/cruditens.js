        async function salvarItem(button) {
        
            const itemRow = button.closest('tr');
            const id = itemRow.querySelector('.item-id').value.trim();
            const itemNameCell = itemRow.querySelector('.item-name');
            const itemPriceCell = itemRow.querySelector('.item-price');
            const itemImageCell = itemRow.querySelector('.item-image-preview');
            const itemNameInput = itemRow.querySelector('.item-edit-input');
            const itemPriceInput = itemRow.querySelector('.item-edit-price');
            const itemImageInput = itemRow.querySelector('.item-edit-image');
        
            const newName = itemNameInput.value.trim();
            const newPrice = parseFloat(itemPriceInput.value || 0).toFixed(2);
            itemNameCell.textContent = newName;
            itemPriceCell.textContent = `R$ ${newPrice}`;
        
            if (itemImageInput.files.length > 0) {
                const fileName = itemImageInput.files[0].name;
                itemImageCell.textContent = fileName;
            }
        
            // Exibe os valores no console
            console.log("Valores atualizados:");
            console.log("Nome:", newName);
            console.log("Preço:", `R$ ${newPrice}`);
            console.log("Imagem:", itemImageInput.files.length > 0 ? itemImageInput.files[0].name : "Nenhum arquivo selecionado");
            
            const updatedData = [{id:id,name:newName, preco:newPrice, imagem:itemImageInput.files.length > 0 ? itemImageInput.files[0].name : itemImageCell.textContent}];
            
            var retorno = await atualizarItem(updatedData)
            
            cancelarEdicao(button);
        }
    
        function cancelarEdicao(button) {
            const itemRow = button.closest('tr');
            if (!itemRow) {
                console.error("Linha correspondente ao botão não encontrada.");
                return;
            }
        
            const itemNameCell = itemRow.querySelector('.item-name');
            const itemPriceCell = itemRow.querySelector('.item-price');
            const SelectCell = itemRow.querySelector('.select2-hidden-accessible');
            const itemImageCell = itemRow.querySelector('.item-image-preview');
            const itemNameInput = itemRow.querySelector('.item-edit-input');
            const itemPriceInput = itemRow.querySelector('.item-edit-price');
            const itemImageInput = itemRow.querySelector('.item-edit-image');
            const actionsCell = itemRow.querySelector('.actions');
            const boxIcon = itemRow.querySelector('.fa-box');
            
            SelectCell.setAttribute('disabled', 'disabled');
        
            itemNameInput.style.display = 'none';
            itemPriceInput.style.display = 'none';
            itemImageInput.style.display = 'none';
            itemNameCell.style.display = 'inline';
            itemPriceCell.style.display = 'inline';
            itemImageCell.style.display = 'inline';
        
            actionsCell.innerHTML = `
                <button class="btn btn-sm btn-primary" onclick="editarItem(this)">
                            <i class="fas fa-edit"></i>
                        </button>
                            <button class="btn btn-sm btn-danger delete-item-btn">
                                <i class="fas fa-trash"></i>
                            </button>
            `;
        
            boxIcon.style.display = 'inline';
        }
        
        function editarItem(button) {
            const itemRow = button.closest('tr'); // Obtém a linha do botão clicado
            const itemNameCell = itemRow.querySelector('.item-name');
            const itemPriceCell = itemRow.querySelector('.item-price');
            const SelectCell = itemRow.querySelector('.select2-hidden-accessible');
            const itemImageCell = itemRow.querySelector('.item-image-preview');
            const itemNameInput = itemRow.querySelector('.item-edit-input');
            const itemPriceInput = itemRow.querySelector('.item-edit-price');
            const itemImageInput = itemRow.querySelector('.item-edit-image');
            const actionsCell = itemRow.querySelector('.actions');
            const boxIcon = itemRow.querySelector('.fa-box');
        
            const isEditing = itemNameInput.style.display === 'block';
        
            if (isEditing) {
                salvarItem(itemRow, itemNameCell, itemPriceCell, itemImageCell, itemNameInput, itemPriceInput, itemImageInput, actionsCell, boxIcon);
            } else {
                // Preenche os campos de edição com os valores atuais
                itemNameInput.value = itemNameCell.textContent.trim();
                itemPriceInput.value = itemPriceCell.textContent.replace('R$', '').trim();
        
                // Alterna a visibilidade entre elementos de visualização e edição
                itemNameCell.style.display = 'none';
                itemPriceCell.style.display = 'none';
                itemImageCell.style.display = 'none';
                itemNameInput.style.display = 'block';
                itemPriceInput.style.display = 'block';
                itemImageInput.style.display = 'block';
                SelectCell.removeAttribute('disabled');
        
                boxIcon.style.display = 'none';
        
                // Atualiza os botões de ações
                actionsCell.innerHTML = `
                    <button 
                        class="btn btn-sm btn-success save-item-btn" 
                        onclick="salvarItem(this)"
                        title="Salvar Alterações">
                        <i class="fas fa-check"></i>
                    </button>
                    <button 
                        class="btn btn-sm btn-secondary cancel-item-btn" 
                        onclick="cancelarEdicao(this)"
                        title="Cancelar Edição">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }
        }
        
        window.salvarItem = salvarItem;
        window.cancelarEdicao = cancelarEdicao;
        window.editarItem = editarItem;