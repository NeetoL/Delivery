<?php
include('App/DataBase/Database.php');

class BaseModel {
    protected $conn;

    public function __construct() {
        $database = new ConexaoBD();
        $this->conn = $database->getConnection();
    }
}

class GetModel extends BaseModel {
    public function getCategorias() {
        $categorias = [];
        try {
            $query = "SELECT * FROM categorias";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Erro ao obter categorias: ' . $e->getMessage();
        }
        return $categorias;
    }

    public function getUsuarios() {
        $usuarios = [];
        try {
            $query = "SELECT * FROM usuarios";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Erro ao obter usuários: ' . $e->getMessage();
        }
        return $usuarios;
    }

    public function getItens() {
        $itens = [];
        try {
            $query = "CALL GetItensComIngredientes()";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Erro ao obter itens: ' . $e->getMessage();
        }
        return $itens;
    }
    
    public function getPedidos(){
         $pedidos = [];
        try {
            $query = "SELECT * FROM pedidos order by id desc";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
        echo 'Erro ao obter itens: ' . $e->getMessage();
        }
        return $pedidos;
    }
    
    public function getTotais() {
        $totais = [];
        try {
            // Prepara a chamada da stored procedure
            $query = "CALL get_totals()";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    
            // Retorna os dados da execução da stored procedure
            $totais = $stmt->fetch(PDO::FETCH_ASSOC); // Usando PDO::FETCH_ASSOC para pegar os resultados como um array associativo
        } catch (PDOException $e) {
            echo 'Erro ao obter totais: ' . $e->getMessage();
        }
    
        return $totais;
    }

}

class DeleteModel extends BaseModel {
    public function deletarItem($id) {
        try {
            // Verifica se o ID é válido
            if (empty($id) || !is_numeric($id)) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'ID inválido ou não fornecido.'
                ]);
            }
    
            // Log para depuração (opcional)
            error_log("Tentando deletar o item com ID: " . $id);
    
            // Prepara a query para exclusão
            $query = "DELETE FROM itens WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
    
            // Verifica se o item foi deletado
            if ($stmt->rowCount() > 0) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Item deletado com sucesso.',
                    'id' => $id
                ]);
            } else {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Item não encontrado.',
                    'id' => $id
                ]);
            }
        } catch (PDOException $e) {
            // Retorna erro em formato JSON
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao deletar item: ' . $e->getMessage()
            ]);
        }
    }


    public function deletarCategoria($id) {
        try {
            // Verifica se o ID é válido
            if (empty($id) || !is_numeric($id)) {
                return json_encode([
                    'erro' => 'ID inválido ou não fornecido.'
                ]);
            }
    
            // Inicia uma transação
            $this->conn->beginTransaction();
    
            // Exclui a categoria
            $queryCategoria = "DELETE FROM categorias WHERE id =".$id;
            $stmtCategoria = $this->conn->prepare($queryCategoria);
            $stmtCategoria->bindParam(':id', $id, PDO::PARAM_INT);
            header('Content-Type: application/json');
            $stmtCategoria->execute();
    
            // Verifica se a categoria foi excluída
            if ($stmtCategoria->rowCount() > 0) {
                // Confirma a transação
                $this->conn->commit();
                error_log("Categoria com ID: " . $id . " foi deletada com sucesso.");
                return json_encode([
                    'status' => 0,
                    'data' => [
                        'codigo' => 0,
                        'mensagem' => 'Categoria deletada com sucesso.'
                    ]
                ]);
            } else {
                // Reverte a transação em caso de falha
                $this->conn->rollBack();
                error_log("Categoria com ID: " . $id . " não encontrada.");
                return json_encode([
                    'status' => 1,
                    'data' => [
                        'codigo' => 1,
                        'mensagem' => 'Categoria não encontrada.'
                    ]
                ]);
            }
        } catch (PDOException $e) {
            // Reverte a transação em caso de erro
            $this->conn->rollBack();
            error_log("Erro ao tentar deletar categoria: " . $e->getMessage());
            return json_encode([
                'status' => 1,
                'data' => [
                    'codigo' => 1,
                    'mensagem' => 'Erro ao tentar deletar categoria.',
                    'detalhes' => $e->getMessage()
                ]
            ]);
        }
    }


    public function deletarUsuario($id) {
        try {
            $query = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Verifica se algum usuário foi deletado
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao deletar usuário: ' . $e->getMessage()]);
            return false;
        }
    }
}

class UpdateModel extends BaseModel {
    public function atualizarItem($id, $dados) {
        try {
            $query = "UPDATE itens SET nome = :nome, preco = :preco, categoria_id = :categoria_id WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':preco', $dados['preco']);
            $stmt->bindParam(':categoria_id', $dados['categoria_id'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo 'Erro ao atualizar item: ' . $e->getMessage();
            return false;
        }
    }
    
    public function updatePedido($id, $status) {
        try {
            // Query SQL para atualizar apenas o status do pedido
            $query = "UPDATE pedidos SET status_pagamento = :status WHERE id = :id";
            
            // Preparando a consulta
            $stmt = $this->conn->prepare($query);
    
            // Ligando os parâmetros da consulta
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            // Executando a consulta
            $stmt->execute();
            
            // Verificando se a linha foi afetada (indicando sucesso)
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Tratamento de erro
            echo 'Erro ao atualizar status do pedido: ' . $e->getMessage();
            return false;
        }
    }



    public function atualizarCategoria($id, $nome) {
    try {
        // Depuração: Exibir os valores recebidos
        error_log("Recebido para atualização: ID=$id, Nome=$nome");

        // Validação básica dos parâmetros
        if ($id <= 0 || empty($nome)) {
            throw new InvalidArgumentException("Dados inválidos: ID deve ser maior que zero e Nome não pode ser vazio.");
        }

        // Consulta SQL
        $query = "UPDATE categorias SET nome = :nome WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Bind de parâmetros
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execução da consulta
        $stmt->execute();

        // Depuração: Linhas afetadas
        $rowCount = $stmt->rowCount();
        error_log("Consulta executada com sucesso. Linhas afetadas: $rowCount");

        // Retorna verdadeiro se alguma linha foi alterada
        return $rowCount > 0;

    } catch (PDOException $e) {
        // Depuração: Capturar erros do banco de dados
        error_log('Erro ao atualizar categoria: ' . $e->getMessage());
        return false;
    } catch (InvalidArgumentException $e) {
        // Depuração: Capturar erros de validação
        error_log('Erro de validação: ' . $e->getMessage());
        return false;
    }
}


    public function atualizarUsuario($id, $dados) {
        try {
            $query = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':email', $dados['email']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo 'Erro ao atualizar usuário: ' . $e->getMessage();
            return false;
        }
    }
}

class SaveModel extends BaseModel{
    public function salvarPedido($json_usuario, $json_pedido,$total) {
        try {
            $query = "INSERT INTO pedidos (json_usuario, json_pedido, status_pagamento, data_criacao,total) 
                      VALUES (:json_usuario, :json_pedido, 'pendente', :data_criacao,:total)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind dos parâmetros
            $stmt->bindParam(':json_usuario', $json_usuario);
            $stmt->bindParam(':json_pedido', $json_pedido);
            $data_criacao = date('Y-m-d H:i:s'); // Data atual
            $stmt->bindParam(':data_criacao', $data_criacao);
            $stmt->bindParam(':total', $total);
            
            $stmt->execute();
            
            // Retorna o JSON de sucesso
            return json_encode([
                'codigo' => 0,
                'mensagem' => 'Pedido salvo com sucesso!',
                'id' => $this->conn->lastInsertId()
            ]);
        } catch (PDOException $e) {
            // Retorna o JSON de erro
            return json_encode([
                'codigo' => 1,
                'mensagem' => 'Erro ao salvar pedido: ' . $e->getMessage()
            ]);
        }
    }

}

?>