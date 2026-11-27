<?php
require_once 'funcoes.php';

// Verifica se o usuário está conectado
exigir_login();

// Recebe o ID do evento pela URL
$id = $_GET['id'] ?? '';

// Busca os eventos cadastrados
$eventos = ler_json('eventos.json');

// Procura o evento solicitado
$eventoEncontrado = null;

foreach ($eventos as $evento) {

    if (
        isset($evento['id']) &&
        (string)$evento['id'] === (string)$id
    ) {
        $eventoEncontrado = $evento;
        break;
    }
}

// Verifica se o evento foi encontrado
if ($eventoEncontrado === null) {
    http_response_code(404);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes do Evento - Sistema de Eventos</title>

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <?php require 'menu.php'; ?>

    <main class="container">

    <?php mostrar_mensagem(); ?> 

        <?php if ($eventoEncontrado === null): ?>

            <h1>Evento não encontrado</h1>

            <p>O evento solicitado não existe ou não está disponível.</p>

        <?php else: ?>

            <h1>
                <?= e($eventoEncontrado['nome'] ?? '') ?>
            </h1>

            <section class="card-evento">

                <h2>Descrição</h2>

                <p>
                    <?= nl2br(e($eventoEncontrado['descricao'] ?? '')) ?>
                </p>

                <p>
                    <strong>Data:</strong>

                    <?php
                    $data = $eventoEncontrado['data'] ?? '';
                    $dataFormatada = date_create_from_format('!Y-m-d', $data);

                    echo $dataFormatada
                        ? $dataFormatada->format('d/m/Y')
                        : e($data);
                    ?>
                </p>

                <p>
                    <strong>Horário:</strong>
                    <?= e($eventoEncontrado['horario'] ?? '') ?>
                </p>

                <p>
                    <strong>Local:</strong>
                    <?= e($eventoEncontrado['local'] ?? '') ?>
                </p>

                <?php if (usuario_ja_agendou($_SESSION['usuario_id'], $eventoEncontrado['id'])): ?>
                    <p class="mensagem sucesso">Você já está agendado neste evento.</p>
                <?php else: ?>
                    <form method="post" action="agendar.php">
                        <?= campo_csrf() ?>
                        <input type="hidden" name="evento_id" value="<?= e($eventoEncontrado['id']) ?>">
                        <button type="submit">Agendar</button>
                    </form>
                <?php endif; ?>
            </section>

        <?php endif; ?>

        <p>
            <a href="eventos.php">Voltar para eventos</a>
        </p>

    </main>
<script src="js/scripts.js"></script>
</body>
</html>
