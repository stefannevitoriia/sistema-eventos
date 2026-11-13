<?php
require_once 'funcoes.php';

if (usuario_logado()) {
    header('Location: inicio.php');
    exit;
}

$erros = [];
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    // Validações no servidor (repetem as do JavaScript)
    if ($nome === '') {
        $erros[] = 'Informe o nome.';
    }
    if ($email === '') {
        $erros[] = 'Informe o e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'O formato do e-mail é inválido.';
    }
    if ($senha === '') {
        $erros[] = 'Informe a senha.';
    } elseif (strlen($senha) < 6) {
        $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
    }
    if ($senha !== $confirmar) {
        $erros[] = 'A confirmação não é igual à senha.';
    }
    if (empty($erros) && buscar_usuario_por_email($email) !== null) {
        $erros[] = 'Já existe um usuário cadastrado com este e-mail.';
    }

    if (empty($erros)) {
        $usuarios = ler_json('usuarios.json');

        $ultimo_id = 0;
        foreach ($usuarios as $u) {
            if ($u['id'] > $ultimo_id) {
                $ultimo_id = $u['id'];
            }
        }

        $usuarios[] = [
            'id' => $ultimo_id + 1,
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
        ];

        if (salvar_json('usuarios.json', $usuarios)) {
            definir_mensagem('sucesso', 'Cadastro realizado com sucesso! Faça login.');
            header('Location: login.php');
            exit;
        }
        $erros[] = 'Não foi possível salvar o cadastro. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro - Sistema de Eventos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <main class="container-auth">
        <h1>Criar conta</h1>

        <?php if (!empty($erros)): ?>
            <div class="mensagem erro" role="alert">
                <?php foreach ($erros as $erro): ?>
                    <p><?= e($erro) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div id="erros-js" class="mensagem erro" role="alert" hidden></div>

        <form id="form-cadastro" method="post" action="cadastro.php" novalidate>
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" value="<?= e($nome) ?>" required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <label for="senha">Senha</label>
            <div class="campo-senha">
                <input type="password" id="senha" name="senha" minlength="6" required>
                <button type="button" class="btn-mostrar-senha" data-alvo="senha">Mostrar</button>
            </div>

            <label for="confirmar_senha">Confirmar senha</label>
            <div class="campo-senha">
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                <button type="button" class="btn-mostrar-senha" data-alvo="confirmar_senha">Mostrar</button>
            </div>

            <button type="submit">Cadastrar</button>
        </form>

        <p>Já tem conta? <a href="login.php">Entrar</a></p>
    </main>
    <script src="js/auth.js"></script>
</body>
</html>
