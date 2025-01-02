<?php
function renderPage($page) {
    $path = "App/Views/{$page}";
    
    if (file_exists($path)) {
        include($path);
    } else {
        http_response_code(404);
        echo "<h1>Erro 404</h1>";
        echo "<p>Página não encontrada.</p>";
    }
}

include 'App/Controller/AuthController.php';
$authController = new AutorizacaoController();

include 'App/Controller/PainelController.php';
$PainelController = new PainelController();

// Captura a URI solicitada
$request = $_SERVER['REQUEST_URI'];

// Separar a URL em caminho e parâmetros de consulta
$parsedUrl = parse_url($request);
$queryParams = [];

if (isset($parsedUrl['query'])) {
    parse_str($parsedUrl['query'], $queryParams); // Extrai os parâmetros de consulta
}

// Extrair apenas o caminho (sem os parâmetros)
$path = $parsedUrl['path'];

switch ($path) {
    case '/':
        renderPage('inicio.html');
        break;
    case '/painelAdmin':
        renderPage('painel.php');
        break;
    case '/loginAdmin':
        renderPage('login.php');
        break;
    // GET
    case '/getUsuarios':
        $PainelController->getUsuarios();
        break;
    case '/getItens':
        $PainelController->getItens();
        break;

    case '/getCategorias':
        $PainelController->getCategorias();
        break;
        
    case '/getPedidos':
        $PainelController->getPedidos();
        break;
    case '/getTotais':
        $PainelController->getTotais();
        break;
        
    //POST
    case '/gravarPedido':
        $PainelController->gravarPedido();
        break;

    // DELETE
    case '/deleteCategoria':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $PainelController->deleteCategoria();
        }
        break;

    case '/deleteUsuario':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $PainelController->deleteUsuario();
        }
        break;

    case '/deleteItem':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $PainelController->deleteItem();
        }
        break;

    // UPDATE
    case '/updateCategoria':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $PainelController->updateCategoria();
        }
        break;
    case '/updatePedido':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $PainelController->updatePedido();
        }
        break;

    case '/updateUsuario':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id_usuario'] ?? '';
            $nome = $input['nome'] ?? '';
            $email = $input['email'] ?? '';
            $PainelController->updateUsuario($id, $input);
        }
        break;

    case '/updateItem':
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? '';
            $nome = $input['nome'] ?? '';
            $preco = $input['preco'] ?? '';
            $categoria_id = $input['categoria_id'] ?? '';
            $PainelController->updateItem($id, $input);
        }
        break;

    case '/logout':
        session_start();
        session_unset();
        session_destroy();
        header('Location: /loginAdmin');
        break;

    case '/AutorizarLogin':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $authController->login($username, $password);
        }
        break;
    //PAGAMENTO
     case '/criar':
        $PainelController->Pagamento();
        break;
    case '/notificacao':
        $PainelController->Notificacao();
        break;
    default:
        // Caso a URL não corresponda a nenhuma rota, retorna um erro 404
        http_response_code(404);
        echo "<h1>Erro 404</h1>";
        echo "<p>Página não encontrada.</p>";
        break;
}
?>
