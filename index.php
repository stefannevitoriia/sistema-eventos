<?php
require_once 'funcoes.php';
header('Location: ' . (usuario_logado() ? 'inicio.php' : 'login.php'));
exit;
