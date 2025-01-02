<?php
// Página de erro
if (isset($_GET['payment_status']) && $_GET['payment_status'] == 'failure') {
    $message = "O pagamento falhou. Por favor, tente novamente.";
} else {
    // Se não encontrar o parâmetro ou ele não for "failure", define uma mensagem de erro
    $message = "Erro: Status do pagamento não encontrado.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro no Pagamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .error-message {
            color: red;
            font-size: 24px;
        }
        .button {
            background-color: #4CAF50; /* Cor verde */
            color: white;
            padding: 15px 32px;
            text-align: center;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Erro no Pagamento</h1>
    <p class="error-message"><?php echo $message; ?></p>
    <p>Se o problema persistir, entre em contato com o suporte.</p>
    
    <!-- Botão de retorno -->
    <a href="/" class="button">Voltar à Página Inicial</a>

</body>
</html>
