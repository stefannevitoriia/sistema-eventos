<?php
require_once 'funcoes.php';

if (usuario_logado()) {
    header('Location: inicio.php');
    exit;
}

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Informe o e-mail e a senha.';
    } else {
        $usuario = buscar_usuario_por_email($email);

        if ($usuario !== null && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: inicio.php');
            exit;
        }
        $erro = 'E-mail ou senha incorretos.';
    }
}

$mensagem = obter_mensagem(); // sucesso do cadastro, logout, acesso negado...
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistema de Eventos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <main class="container-auth">
        <h1>Entrar</h1>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= e($mensagem['tipo']) ?>" role="alert">
                <p><?= e($mensagem['texto']) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
            <div class="mensagem erro" role="alert"><p><?= e($erro) ?></p></div>
        <?php endif; ?>

        <div id="erros-js" class="mensagem erro" role="alert" hidden></div>

        <form id="form-login" method="post" action="login.php" novalidate>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <label for="senha">Senha</label>
            <div class="campo-senha">
                <input type="password" id="senha" name="senha" required>
                <button type="button" class="btn-mostrar-senha" data-alvo="senha">Mostrar</button>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </main>
    <script src="js/auth.js"></script>
</body>
</html>
