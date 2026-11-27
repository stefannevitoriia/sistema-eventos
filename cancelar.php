<?php
require_once 'funcoes.php';
exigir_login();

$usuario_id = $_SESSION['usuario_id'];

/** Procura o agendamento pelo id, desde que pertença ao usuário conectado. */
function buscar_agendamento_do_usuario($id, $usuario_id) {
    foreach (ler_json('agendamentos.json') as $ag) {
        if ((string)($ag['id'] ?? '') === (string)$id
            && (string)($ag['usuario_id'] ?? '') === (string)$usuario_id) {
            return $ag;
        }
    }
    return null;
}

// ---------- POST: executa o cancelamento (depois da confirmação) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        definir_mensagem('erro', 'Requisição inválida. Tente novamente.');
        header('Location: meus-agendamentos.php');
        exit;
    }

    $id = $_POST['id'] ?? '';
    if (buscar_agendamento_do_usuario($id, $usuario_id) === null) {
        definir_mensagem('erro', 'Agendamento não encontrado.');
        header('Location: meus-agendamentos.php');
        exit;
    }

    $restantes = array_values(array_filter(ler_json('agendamentos.json'), function ($ag) use ($id) {
        return (string)($ag['id'] ?? '') !== (string)$id;
    }));

    if (salvar_json('agendamentos.json', $restantes)) {
        definir_mensagem('sucesso', 'Agendamento cancelado.');
    } else {
        definir_mensagem('erro', 'Não foi possível cancelar. Tente novamente.');
    }
    header('Location: meus-agendamentos.php');
    exit;
}

// ---------- GET: página de confirmação ----------
$id = $_GET['id'] ?? '';
$agendamento = buscar_agendamento_do_usuario($id, $usuario_id);

if ($agendamento === null) {
    definir_mensagem('erro', 'Agendamento não encontrado.');
    header('Location: meus-agendamentos.php');
    exit;
}

$evento = buscar_evento_por_id($agendamento['evento_id'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar agendamento - Sistema de Eventos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <?php require 'menu.php'; ?>

    <main class="container container-estreito">
        <h1>Cancelar agendamento</h1>

        <div class="mensagem erro">
            <p>Tem certeza de que deseja cancelar o agendamento no evento
                <strong><?= e($evento['nome'] ?? 'Evento removido') ?></strong>?</p>
        </div>

        <?php if ($evento): ?>
            <p>
                <?= e(formatar_data($evento['data'] ?? '')) ?> às <?= e($evento['horario'] ?? '') ?>
                — <?= e($evento['local'] ?? '') ?>
            </p>
        <?php endif; ?>

        <form method="post" action="cancelar.php" class="acoes">
            <?= campo_csrf() ?>
            <input type="hidden" name="id" value="<?= e($agendamento['id']) ?>">
            <button type="submit" class="btn-perigo">Sim, cancelar</button>
            <a class="botao-link secundario" href="meus-agendamentos.php">Não, voltar</a>
        </form>
    </main>
    <script src="js/scripts.js"></script>
</body>
</html>
