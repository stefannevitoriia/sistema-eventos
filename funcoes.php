<?php
// funcoes.php - funções compartilhadas por todas as páginas do sistema.
// Inclua no início de cada página com: require_once 'funcoes.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DIR_DADOS', __DIR__ . '/dados/');

/** Lê um arquivo JSON da pasta dados/ e devolve um array. */
function ler_json($arquivo) {
    $caminho = DIR_DADOS . $arquivo;
    if (!file_exists($caminho)) {
        return [];
    }
    $conteudo = file_get_contents($caminho);
    $dados = json_decode($conteudo, true);
    return is_array($dados) ? $dados : [];
}

/** Grava um array em um arquivo JSON da pasta dados/. */
function salvar_json($arquivo, $dados) {
    $caminho = DIR_DADOS . $arquivo;
    $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($caminho, $json, LOCK_EX) !== false;
}

/** Escapa texto para exibir com segurança no HTML. */
function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

/** Guarda uma mensagem (tipo: 'sucesso' ou 'erro') para a próxima página. */
function definir_mensagem($tipo, $texto) {
    $_SESSION['mensagem'] = ['tipo' => $tipo, 'texto' => $texto];
}

/** Retorna e apaga a mensagem guardada (ou null). */
function obter_mensagem() {
    if (isset($_SESSION['mensagem'])) {
        $m = $_SESSION['mensagem'];
        unset($_SESSION['mensagem']);
        return $m;
    }
    return null;
}

/** Procura um usuário pelo e-mail (sem diferenciar maiúsculas). */
function buscar_usuario_por_email($email) {
    $email = strtolower(trim($email));
    foreach (ler_json('usuarios.json') as $usuario) {
        if (strtolower($usuario['email']) === $email) {
            return $usuario;
        }
    }
    return null;
}

/** Verifica se há usuário conectado. */
function usuario_logado() {
    return isset($_SESSION['usuario_id']);
}

/** Use no topo das páginas internas: redireciona para o login se não estiver conectado. */
function exigir_login() {
    if (!usuario_logado()) {
        definir_mensagem('erro', 'Faça login para acessar esta página.');
        header('Location: login.php');
        exit;
    }
}
