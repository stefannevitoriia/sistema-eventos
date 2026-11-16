<?php
require_once 'funcoes.php';

// Verifica se o usuário está conectado
exigir_login();

// Busca os eventos cadastrados no JSON
$eventos = ler_json('eventos.json');

// Organiza os eventos por data
usort($eventos, function ($a, $b) {
    return strcmp($a['data'] ?? '', $b['data'] ?? '');
});

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Eventos - Sistema de Eventos</title>

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <main class="container">

        <h1>Eventos disponíveis</h1>

        <p>Confira os eventos cadastrados no sistema.</p>

        <p>
            <a href="inicio.php">Voltar ao início</a>
        </p>

        <?php if (empty($eventos)): ?>

            <div class="mensagem">
                <p>Nenhum evento cadastrado no momento.</p>
            </div>

        <?php else: ?>

            <section class="lista-eventos">

                <?php foreach ($eventos as $evento): ?>

                    <article class="card-evento">

                        <h2>
                            <?= e($evento['nome'] ?? 'Evento sem nome') ?>
                        </h2>

                        <p>
                            <strong>Data:</strong>
                            <?php
                            $data = $evento['data'] ?? '';
                            $dataFormatada = date_create_from_format('!Y-m-d', $data);
                            echo $dataFormatada
                                ? $dataFormatada->format('d/m/Y')
                                : e($data);
                            ?>
                        </p>

                        <p>
                            <strong>Horário:</strong>
                            <?= e($evento['horario'] ?? '') ?>
                        </p>

                        <p>
                            <strong>Local:</strong>
                            <?= e($evento['local'] ?? '') ?>
                        </p>

                        <?php if (isset($evento['id'])): ?>

                            <a href="detalhes.php?id=<?= urlencode((string)$evento['id']) ?>">
                                Ver detalhes
                            </a>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </main>

</body>
</html>
