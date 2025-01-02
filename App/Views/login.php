<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Link do Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link do Font Awesome para o ícone de cadeado -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5; /* Cor de fundo suave */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff; /* Card completamente branco */
        }
        .card-header {
            background-color: #fff; /* Header do card branco */
            text-align: center;
            font-size: 1.25rem;
            border: none;
            padding-top: 10px;
        }
        .card-header i {
            font-size: 2rem; /* Tamanho do ícone */
            color: #007bff;
            margin-bottom: 10px;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 100%;
            padding: 0.75rem;
            font-size: 1.1rem;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-body">
            <div class="text-center">
            <h1><i class="fas fa-lock"></i></h1>
            </div>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            <div id="message" class="message"></div>
        </div>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            try {
                const response = await fetch('/AutorizarLogin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`,
                });

                const result = await response.json();
                const messageDiv = document.getElementById('message');
                if (result.status === 'success') {
                    messageDiv.innerHTML = `<p style="color: green;">${result.message}</p>`;
                    setTimeout(() => {
                        window.location.href = '/painelAdmin';
                    }, 2000);
                } else {
                    messageDiv.innerHTML = `<p style="color: red;">${result.message}</p>`;
                }

            } catch (error) {
                console.error('Erro ao enviar os dados:', error);
            }
        });
    </script>
</body>
</html>
