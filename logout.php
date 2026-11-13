<?php
require_once 'funcoes.php';

// Encerra a sessão
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Nova sessão só para mostrar a mensagem de saída
session_start();
definir_mensagem('sucesso', 'Você saiu do sistema.');
header('Location: login.php');
exit;
