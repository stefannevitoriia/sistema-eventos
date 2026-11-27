<?php
require_once 'funcoes.php';
exigir_login();

$nome = $_SESSION['usuario_nome'] ?? 'usuário';
$proximos = proximos_eventos(3);
$totalAgendamentos = count(agendamentos_do_usuario($_SESSION['usuario_id']));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Sistema de Eventos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <?php require 'menu.php'; ?>

    <main class="container">
        <?php mostrar_mensagem(); ?>

        <section class="boas-vindas">
            <h1>Olá, <?= e($nome) ?>! </h1>
            <p>Bem-vindo(a) ao Sistema de Eventos. Veja o que está por vir e reserve o seu lugar.</p>
            <p>
                Você tem <strong><?= $totalAgendamentos ?></strong>
                agendamento<?= $totalAgendamentos === 1 ? '' : 's' ?>.
                <a href="meus-agendamentos.php">Ver meus agendamentos</a>
            </p>
        </section>

        <h2>Próximos eventos</h2>

        <?php if (empty($proximos)): ?>
            <div class="mensagem"><p>Nenhum evento futuro cadastrado no momento.</p></div>
        <?php else: ?>
            <section class="lista-eventos">
                <?php foreach ($proximos as $evento): ?>
                    <article class="card-evento">
                        <h3><?= e($evento['nome'] ?? 'Evento sem nome') ?></h3>
                        <p><strong>Data:</strong> <?= e(formatar_data($evento['data'] ?? '')) ?></p>
                        <p><strong>Horário:</strong> <?= e($evento['horario'] ?? '') ?></p>
                        <p><strong>Local:</strong> <?= e($evento['local'] ?? '') ?></p>
                        <?php if (isset($evento['id'])): ?>
                            <a class="botao-link" href="detalhes.php?id=<?= urlencode((string)$evento['id']) ?>">Ver detalhes</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <p><a href="eventos.php">Ver todos os eventos →</a></p>
    </main>
    <script src="js/scripts.js"></script>
</body>
</html>
