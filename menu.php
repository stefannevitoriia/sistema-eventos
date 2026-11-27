<?php
// menu.php - menu de navegação. Para usar, inclua com require logo depois da tag body.
$paginaAtual = basename($_SERVER['SCRIPT_NAME']);
$itensMenu = [
    'inicio.php'             => 'Início',
    'eventos.php'            => 'Eventos',
    'meus-agendamentos.php'  => 'Meus agendamentos',
    'criar-evento.php'       => 'Cadastrar evento',
];
?>
<header class="topo">
    <div class="topo-conteudo">
        <a href="inicio.php" class="logo">Sistema de Eventos</a>
        <nav class="menu" aria-label="Navegação principal">
            <?php foreach ($itensMenu as $arquivo => $rotulo): ?>
                <a href="<?= e($arquivo) ?>"
                   <?= $paginaAtual === $arquivo ? 'class="ativo" aria-current="page"' : '' ?>>
                    <?= e($rotulo) ?>
                </a>
            <?php endforeach; ?>
            <a href="logout.php" class="menu-sair">Sair</a>
        </nav>
    </div>
</header>
