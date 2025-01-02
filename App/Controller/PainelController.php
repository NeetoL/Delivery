<?php
include('App/Models/getModel.php');

class PainelController {
    
    public function checkLogin() {
        $authController = new AuthController();
        
        if (!$authController->checkLogin()) {
            header('Content-Type: application/json', true, 401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Você precisa estar logado para acessar esta página.'
            ]);
            exit();
        }
    }
    
    public function getCategorias() {
        try {
            //$this->checkLogin();
            
            $model = new GetModel();
            $categorias = $model->getCategorias();
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $categorias
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getUsuarios() {
        try {
            $this->checkLogin();
            
            $model = new GetModel();
            $usuarios = $model->getUsuarios();
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $usuarios
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getItens() {
        try {
            //$this->checkLogin();
            
            $model = new GetModel();
            $itens = $model->getItens();
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $itens
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function getPedidos(){
        $model = new GetModel();
        $pedidos = $model->getPedidos();
        
        header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $pedidos
            ]);
    }
    
    public function getTotais(){
        $model = new GetModel();
        $pedidos = $model->getTotais();
        
        header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $pedidos
            ]);
    }
    
    public function gravarPedido(){
       if (isset($_POST['InformacoesUsuario'], $_POST['itensCarrinho'],$_POST['total'])) {
            $informacoesUsuario = json_decode($_POST['InformacoesUsuario'], true);
            $itensCarrinho = json_decode($_POST['itensCarrinho'], true);
            $total = $_POST['total'];
            
            // Valida os JSONs decodificados
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode([
                    'codigo' => 1,
                    'mensagem' => 'Erro: JSON inválido enviado.'
                ]);
                return;
            }
    
            $model = new SaveModel();
            
            // Chama o método de salvar pedido com os JSONs originais
            $resultado = $model->salvarPedido(json_encode($informacoesUsuario), json_encode($itensCarrinho),$total);
            echo $resultado;
        } else {
            echo json_encode([
                'codigo' => 1,
                'mensagem' => 'Parâmetros ausentes: InformacoesUsuario e itensCarrinho são obrigatórios.'
            ]);
        }
    }
    
    public function updatePedido() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        $id = isset($data['id']) ? $data['id'] : null;
        $status = isset($data['status']) ? $data['status'] : null;
        if ($id !== null && $status !== null) {
            $model = new UpdateModel();
            $success = $model->updatePedido($id, $status);
            
            if ($success) {
                echo json_encode(['codigo' => 0, 'mensagem' => 'Pedido atualizado com sucesso']);
            } else {
                echo json_encode(['codigo' => 1, 'mensagem' => 'Erro ao atualizar pedido']);
            }
        } else {
            echo json_encode(['codigo' => 1, 'mensagem' => 'Dados inválidos']);
        }
    }

    public function updateItem($id, $dados) {
        try {
            $this->checkLogin();
    
            $model = new UpdateModel();
            $success = $model->atualizarItem($id, $dados);
    
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Item atualizado com sucesso!' : 'Falha ao atualizar o item.'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function updateCategoria() {
        try {

            $this->checkLogin();
            
            $dados = json_decode(file_get_contents('php://input'), true);
            if (!isset($dados['id'])) {
                throw new Exception('ID da categoria não fornecido.');
            }
            
            $updateModel = new UpdateModel();
            $success = $updateModel->atualizarCategoria($dados['id'],$dados['nome']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Categoria atualizada com sucesso!' : 'Falha ao atualizar a categoria.',
                'dados' => $dados
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function updateUsuario($id, $dados) {
        try {
            $this->checkLogin();
    
            $model = new UpdateModel();
            $success = $model->atualizarUsuario($id, $dados);
    
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Usuário atualizado com sucesso!' : 'Falha ao atualizar o usuário.'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteItem() {
        try {
            $this->checkLogin();
    
            $model = new DeleteModel();
            
            $dados = json_decode(file_get_contents('php://input'), true);
    
            $id_item = isset($dados['id_item']) ? $dados['id_item'] : null;
            $success = $model->deletarItem($id_item);
            
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function deleteCategoria() {
        try {
    
            $this->checkLogin();
    
            $model = new DeleteModel();
    
            // Pega os dados enviados na requisição
            $dados = json_decode(file_get_contents('php://input'), true);
    
            // Pega o id_categoria que está dentro de $dados
            $id_categoria = isset($dados['id']) ? $dados['id'] : null;
            
            if ($id_categoria === null) {
                throw new Exception("ID da categoria não fornecido.");
            }
    
            // Passando o id_categoria para o método de deletar
            $success = $model->deletarCategoria($id_categoria);
    
            // Retorna o JSON de sucesso ou erro
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $success ? 'success' : null,
                'data' => [
                    'codigo' => $success ? 0 : 1,
                    'mensagem' => $success ? 'realizada' : 'falha'
                ]
            ]);
        } catch (Exception $e) {
            ob_clean(); // Limpa qualquer saída anterior
            flush(); // Garante que o buffer de saída seja limpo
    
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function deleteUsuario($id) {
        try {
            $this->checkLogin();
    
            $model = new DeleteModel();
            $success = $model->deletarUsuario($id);
    
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $success ? 'success' : 'error',
                'message' => $success ? 'Usuário deletado com sucesso!' : 'Falha ao deletar o usuário.'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function geraString($length) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function generateUUID(){
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), // 8 caracteres
            mt_rand(0, 0xffff), // 4 caracteres
            mt_rand(0, 0x0fff) | 0x4000, // 4 caracteres para a versão 4
            mt_rand(0, 0x3fff) | 0x8000, // 4 caracteres para a variante
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff) // 12 caracteres
        );
    }
    
    public function Pagamento() {
        // Gerar a referência externa única
        $externalReference = $this->geraString(24);
    
        // Receber dados do formulário
        $name = strtolower($_POST['name']);
        $email = strtolower($_POST['email']);
        $value = $_POST['value'];
    
        // Limpar o valor de R$ e espaços, e formatar para float
        $value = str_replace(['R$', ' '], '', $value);
        $value = str_replace(",", ".", $value); 
        $value = floatval($value);
    
        // Descrição do produto
        $description = "PRODUTO #1";
    
        // Dados do pagador
        $pagador = [
            "first_name" => $name,
            "last_name" => "",
            "email" => $email
        ];
    
        // Dados da transação
        $infos = [
            "notification_url" => 'https://frangonacaixaoficial.online/notificacao',
            "description" => $description,
            "external_reference" => $externalReference,
            "transaction_amount" => $value,
            "payment_method_id" => "pix"
        ];
    
        // Mescla as informações do pagador com os dados da transação
        $payment = array_merge(["payer" => $pagador], $infos);
        $payment = json_encode($payment);
    
        // Geração do UUID para idempotência
        $uuid = $this->generateUUID();
    
        // Iniciar a requisição cURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.mercadopago.com/v1/payments/",
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $payment,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer APP_USR-1233514292355788-122815-c2abe9fc7d9b1b702441dd0a2f478bb3-583963771',
                'X-Idempotency-Key: ' . $uuid,
                'Content-Type: application/json'
            ]
        ]);
    
        // Execução da requisição
        $response = curl_exec($curl);
        curl_close($curl);
    
        // Decodificando a resposta
        $data = json_decode($response, true);
        
        // Caminho para os dados da transação
        $transactionData = $data['point_of_interaction']['transaction_data'];
    
        // Criando um array com as informações necessárias
        $arr = [
            'qr_code' => $transactionData['qr_code'],
            'qr_code_base64' => $transactionData['qr_code_base64'],
            'payment_url' => $transactionData['ticket_url'],
            'id' => $data['id'],
            'ref' => $externalReference,
            'full_info_for_developer' => $data
        ];
    
        // Definindo o diretório onde as transações serão salvas
        $transactionDir = "../App/transactions/";
    
        // Verificar se o diretório existe, se não, criar
        if (!file_exists($transactionDir)) {
            mkdir($transactionDir, 0777, true);
        }
    
        // Criação do arquivo de transação com o status inicial
        $paymentId = $data['id'];
        file_put_contents($transactionDir . $externalReference, "pending;$paymentId");
    
        // Exibe a resposta como JSON
        echo json_encode($arr);
    }
    
    public function Notificacao() {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        if (isset($data['data']['id'])) {
            $paymentId = $data['data']['id'];

            
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.mercadopago.com/v1/payments/' . $paymentId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer APP_USR-1233514292355788-122815-c2abe9fc7d9b1b702441dd0a2f478bb3-583963771',
            ]
        ]);
        
        $response = curl_exec($curl);
        $response = json_decode($response, true);
        
        curl_close($curl);
        
        $externalReference = $response['external_reference'];
        $status = $response['status'];
        $valuePayment = (float) $response['transaction_amount'];
        
        if($status=="approved"){
            file_put_contents("../transactions/$externalReference", "approved;$paymentId");
        }
        
        echo json_encode($response);
        } else {
            // Caso não tenha o id esperado
            http_response_code(200);
            echo json_encode([
                'status' => 'pendente',
                'message' => 'ID do pagamento não encontrado na notificação.'
            ]);
        }
    }

}
?>
