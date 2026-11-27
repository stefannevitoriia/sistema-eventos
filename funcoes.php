<?php
// funcoes.php - funções compartilhadas por todas as páginas do sistema.
// Inclua no início de cada página com: require_once 'funcoes.php';

date_default_timezone_set('America/Sao_Paulo');

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

/** Exibe (e apaga) a mensagem guardada, se houver. */
function mostrar_mensagem() {
    $m = obter_mensagem();
    if ($m) {
        echo '<div class="mensagem ' . e($m['tipo']) . '" role="alert"><p>' . e($m['texto']) . '</p></div>';
    }
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

/* ---------- Segurança (CSRF) ---------- */

/** Token de proteção para formulários POST. */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Campo hidden para colocar dentro de formulários POST. */
function campo_csrf() {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Confere o token enviado no POST. */
function csrf_valido() {
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}

/* ---------- Eventos e agendamentos ---------- */

/** Formata 'Y-m-d' como 'd/m/Y' (devolve o texto original se for inválido). */
function formatar_data($data) {
    $d = date_create_from_format('!Y-m-d', (string)$data);
    return $d ? $d->format('d/m/Y') : (string)$data;
}

/** Busca um evento pelo id (ou null). */
function buscar_evento_por_id($id) {
    foreach (ler_json('eventos.json') as $evento) {
        if (isset($evento['id']) && (string)$evento['id'] === (string)$id) {
            return $evento;
        }
    }
    return null;
}

/** Agendamentos do usuário conectado (somente os dele). */
function agendamentos_do_usuario($usuario_id) {
    $lista = [];
    foreach (ler_json('agendamentos.json') as $ag) {
        if ((string)($ag['usuario_id'] ?? '') === (string)$usuario_id) {
            $lista[] = $ag;
        }
    }
    return $lista;
}

/** Verifica se o usuário já agendou o evento. */
function usuario_ja_agendou($usuario_id, $evento_id) {
    foreach (agendamentos_do_usuario($usuario_id) as $ag) {
        if ((string)($ag['evento_id'] ?? '') === (string)$evento_id) {
            return true;
        }
    }
    return false;
}

/** Eventos futuros (a partir de hoje), ordenados por data e horário. */
function proximos_eventos($limite = null) {
    $hoje = date('Y-m-d');
    $eventos = array_filter(ler_json('eventos.json'), function ($ev) use ($hoje) {
        return ($ev['data'] ?? '') >= $hoje;
    });
    usort($eventos, function ($a, $b) {
        return strcmp(($a['data'] ?? '') . ($a['horario'] ?? ''), ($b['data'] ?? '') . ($b['horario'] ?? ''));
    });
    return $limite ? array_slice($eventos, 0, $limite) : array_values($eventos);
}
