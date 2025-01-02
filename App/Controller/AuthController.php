<?php
class AutorizacaoController {
    public function checkLogin() {
        session_start();
        // Verifica se a variável de sessão 'user_id' existe
        return isset($_SESSION['user_id']);
    }

    public function login($username, $password) {
        // Configurar o cabeçalho para JSON
        header('Content-Type: application/json');
    
        // Verificar credenciais
        if ($username === 'admin' && $password === 'senha') {
            session_start(); // Inicia a sessão, se necessário
            $_SESSION['user_id'] = 1;
    
            // Retornar resposta JSON de sucesso
            echo json_encode([
                'status' => 'success',
                'message' => 'Login bem-sucedido!',
                'user_id' => $_SESSION['user_id']
            ]);
        } else {
            // Retornar resposta JSON de erro
            echo json_encode([
                'status' => 'error',
                'message' => 'Credenciais inválidas.'
            ]);
        }
    
        exit(); // Garantir que a execução do script termina aqui
    }


    public function logout() {
        session_start();
        session_destroy();
        header("Location: /login");
    }
}
?>
