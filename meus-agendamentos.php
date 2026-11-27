<?php
require_once 'funcoes.php';
exigir_login();

// Somente os agendamentos do usuário conectado
$meus = [];
foreach (agendamentos_do_usuario($_SESSION['usuario_id']) as $ag) {
    $evento = buscar_evento_por_id($ag['evento_id'] ?? '');
    $meus[] = ['agendamento' => $ag, 'evento' => $evento];
}

// Ordena pela data do evento (eventos removidos ficam no fim)
usort($meus, function ($a, $b) {
    $da = ($a['evento']['data'] ?? '9999-12-31') . ($a['evento']['horario'] ?? '');
    $db = ($b['evento']['data'] ?? '9999-12-31') . ($b['evento']['horario'] ?? '');
    return strcmp($da, $db);
});
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus agendamentos - Sistema de Eventos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <?php require 'menu.php'; ?>

    <main class="container">
        <h1>Meus agendamentos</h1>

        <?php mostrar_mensagem(); ?>

        <?php if (empty($meus)): ?>
            <div class="mensagem">
                <p>Você ainda não agendou nenhum evento. <a href="eventos.php">Ver eventos disponíveis</a></p>
            </div>
        <?php else: ?>
            <section class="lista-eventos">
                <?php foreach ($meus as $item): ?>
                    <?php $ag = $item['agendamento']; $ev = $item['evento']; ?>
                    <article class="card-evento">
                        <?php if ($ev === null): ?>
                            <h2>Evento indisponível</h2>
                            <p>Este evento não existe mais.</p>
                        <?php else: ?>
                            <h2><?= e($ev['nome'] ?? 'Evento sem nome') ?></h2>
                            <p><strong>Data:</strong> <?= e(formatar_data($ev['data'] ?? '')) ?></p>
                            <p><strong>Horário:</strong> <?= e($ev['horario'] ?? '') ?></p>
                            <p><strong>Local:</strong> <?= e($ev['local'] ?? '') ?></p>
                        <?php endif; ?>

                        <div class="acoes">
                            <?php if ($ev !== null): ?>
                                <a class="botao-link secundario" href="detalhes.php?id=<?= urlencode((string)$ev['id']) ?>">Ver detalhes</a>
                            <?php endif; ?>
                            <a class="botao-link perigo" href="cancelar.php?id=<?= urlencode((string)$ag['id']) ?>">Cancelar</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
    <script src="js/scripts.js"></script>
</body>
</html>
