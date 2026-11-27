<?php
require_once 'funcoes.php';
exigir_login();

// Só aceita POST (vindo do botão "Agendar" da página de detalhes)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: eventos.php');
    exit;
}

if (!csrf_valido()) {
    definir_mensagem('erro', 'Requisição inválida. Tente novamente.');
    header('Location: eventos.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$evento_id = $_POST['evento_id'] ?? '';
$voltar = 'detalhes.php?id=' . urlencode((string)$evento_id);

$evento = buscar_evento_por_id($evento_id);
if ($evento === null) {
    definir_mensagem('erro', 'O evento solicitado não existe.');
    header('Location: eventos.php');
    exit;
}

if (($evento['data'] ?? '') < date('Y-m-d')) {
    definir_mensagem('erro', 'Este evento já aconteceu e não aceita agendamentos.');
    header('Location: ' . $voltar);
    exit;
}

// Impede o mesmo usuário de agendar o mesmo evento duas vezes
if (usuario_ja_agendou($usuario_id, $evento_id)) {
    definir_mensagem('erro', 'Você já está agendado neste evento.');
    header('Location: ' . $voltar);
    exit;
}

$agendamentos = ler_json('agendamentos.json');

$ultimo_id = 0;
foreach ($agendamentos as $ag) {
    if (($ag['id'] ?? 0) > $ultimo_id) {
        $ultimo_id = $ag['id'];
    }
}

$agendamentos[] = [
    'id'            => $ultimo_id + 1,
    'usuario_id'    => $usuario_id,
    'evento_id'     => $evento['id'],
    'agendado_em'   => date('Y-m-d H:i:s'),
];

if (salvar_json('agendamentos.json', $agendamentos)) {
    definir_mensagem('sucesso', 'Agendamento realizado com sucesso!');
    header('Location: meus-agendamentos.php');
} else {
    definir_mensagem('erro', 'Não foi possível salvar o agendamento. Tente novamente.');
    header('Location: ' . $voltar);
}
exit;
