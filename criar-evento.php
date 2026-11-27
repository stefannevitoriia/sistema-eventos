<?php
require_once 'funcoes.php';

// Qualquer usuário logado pode cadastrar eventos (não há checagem de perfil/admin).
exigir_login();

date_default_timezone_set('America/Sao_Paulo');

const NOME_MAX = 100;
const LOCAL_MAX = 150;
const DESCRICAO_MAX = 1000;

$erros = [];
$nome = '';
$descricao = '';
$data = '';
$horario = '';
$local = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $data = trim($_POST['data'] ?? '');
    $horario = trim($_POST['horario'] ?? '');
    $local = trim($_POST['local'] ?? '');

    if (!csrf_valido()) {
        $erros[] = 'Requisição inválida. Recarregue a página e tente novamente.';
    }

    // Validações no servidor (repetem as do JavaScript)
    if ($nome === '') {
        $erros[] = 'Informe o nome do evento.';
    } elseif (mb_strlen($nome) > NOME_MAX) {
        $erros[] = 'O nome deve ter no máximo ' . NOME_MAX . ' caracteres.';
    }

    if ($descricao === '') {
        $erros[] = 'Informe a descrição do evento.';
    } elseif (mb_strlen($descricao) > DESCRICAO_MAX) {
        $erros[] = 'A descrição deve ter no máximo ' . DESCRICAO_MAX . ' caracteres.';
    }

    // Data: formato AAAA-MM-DD e data real (rejeita, por exemplo, 2026-02-31)
    $dataObj = null;
    if ($data === '') {
        $erros[] = 'Informe a data do evento.';
    } else {
        $dataObj = DateTime::createFromFormat('!Y-m-d', $data);
        $dataErros = DateTime::getLastErrors();
        if (!$dataObj || ($dataErros && ($dataErros['warning_count'] > 0 || $dataErros['error_count'] > 0))) {
            $dataObj = null;
            $erros[] = 'A data informada é inválida.';
        }
    }

    // Horário: formato HH:MM
    $horarioValido = false;
    if ($horario === '') {
        $erros[] = 'Informe o horário do evento.';
    } elseif (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $horario)) {
        $erros[] = 'O horário informado é inválido.';
    } else {
        $horarioValido = true;
    }

    // Não permite cadastrar evento no passado
    if ($dataObj !== null) {
        $hoje = new DateTime('today');
        if ($dataObj < $hoje) {
            $erros[] = 'A data do evento não pode estar no passado.';
        } elseif ($horarioValido && $dataObj == $hoje && $horario < date('H:i')) {
            $erros[] = 'O horário do evento já passou.';
        }
    }

    if ($local === '') {
        $erros[] = 'Informe o local do evento.';
    } elseif (mb_strlen($local) > LOCAL_MAX) {
        $erros[] = 'O local deve ter no máximo ' . LOCAL_MAX . ' caracteres.';
    }

    if (empty($erros)) {
        $eventos = ler_json('eventos.json');

        $ultimo_id = 0;
        foreach ($eventos as $ev) {
            if (isset($ev['id']) && $ev['id'] > $ultimo_id) {
                $ultimo_id = $ev['id'];
            }
        }

        $eventos[] = [
            'id' => $ultimo_id + 1,
            'nome' => $nome,
            'descricao' => $descricao,
            'data' => $data,
            'horario' => $horario,
            'local' => $local,
            'criado_por' => $_SESSION['usuario_id'],
        ];

        if (salvar_json('eventos.json', $eventos)) {
            // Redireciona (evita reenvio do formulário ao atualizar a página)
            definir_mensagem('sucesso', 'Evento cadastrado com sucesso!');
            header('Location: criar-evento.php');
            exit;
        }
        $erros[] = 'Não foi possível salvar o evento. Tente novamente.';
    }
}

$mensagem = obter_mensagem(); // sucesso após o cadastro
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar evento - Sistema de Eventos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <?php require 'menu.php'; ?>

    <main class="container container-estreito">
        <h1>Cadastrar evento</h1>

        <?php if ($mensagem): ?>
            <div class="mensagem <?= e($mensagem['tipo']) ?>" role="alert">
                <p><?= e($mensagem['texto']) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($erros)): ?>
            <div class="mensagem erro" role="alert">
                <?php foreach ($erros as $erro): ?>
                    <p><?= e($erro) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div id="erros-js" class="mensagem erro" role="alert" hidden></div>

        <form id="form-evento" method="post" action="criar-evento.php" novalidate>
            <?= campo_csrf() ?>

            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" maxlength="<?= NOME_MAX ?>"
                   value="<?= e($nome) ?>" required>

            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="5" maxlength="<?= DESCRICAO_MAX ?>"
                      required><?= e($descricao) ?></textarea>

            <label for="data">Data</label>
            <input type="date" id="data" name="data" min="<?= date('Y-m-d') ?>"
                   value="<?= e($data) ?>" required>

            <label for="horario">Horário</label>
            <input type="time" id="horario" name="horario" value="<?= e($horario) ?>" required>

            <label for="local">Local</label>
            <input type="text" id="local" name="local" maxlength="<?= LOCAL_MAX ?>"
                   value="<?= e($local) ?>" required>

            <button type="submit">Cadastrar evento</button>
        </form>
    </main>
    <script src="js/criar-evento.js"></script>
</body>
</html>
