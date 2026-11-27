// scripts.js - JavaScript compartilhado por todas as páginas.
// Inclui: mostrar/ocultar senha e validações visuais de login e cadastro.
// (As validações do servidor continuam valendo; as daqui são só para ajudar o usuário.)

document.addEventListener('DOMContentLoaded', function () {
    configurarMostrarSenha();

    var formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', validarLogin);
    }

    var formCadastro = document.getElementById('form-cadastro');
    if (formCadastro) {
        formCadastro.addEventListener('submit', validarCadastro);
    }
});

/* ---------- Mostrar / ocultar senha ---------- */
function configurarMostrarSenha() {
    document.querySelectorAll('.btn-mostrar-senha').forEach(function (botao) {
        botao.addEventListener('click', function () {
            var campo = document.getElementById(botao.dataset.alvo);
            if (!campo) return;
            var mostrando = campo.type === 'text';
            campo.type = mostrando ? 'password' : 'text';
            botao.textContent = mostrando ? 'Mostrar' : 'Ocultar';
            botao.setAttribute('aria-pressed', mostrando ? 'false' : 'true');
        });
    });
}

/* ---------- Utilitários de erro ---------- */
function limparErros(form) {
    var caixa = document.getElementById('erros-js');
    if (caixa) {
        caixa.hidden = true;
        caixa.innerHTML = '';
    }
    form.querySelectorAll('.invalido').forEach(function (campo) {
        campo.classList.remove('invalido');
        campo.removeAttribute('aria-invalid');
    });
}

function marcarInvalido(campo) {
    campo.classList.add('invalido');
    campo.setAttribute('aria-invalid', 'true');
}

function mostrarErros(erros) {
    var caixa = document.getElementById('erros-js');
    if (!caixa) return;
    caixa.innerHTML = '';
    erros.forEach(function (texto) {
        var p = document.createElement('p');
        p.textContent = texto; // textContent evita injeção de HTML
        caixa.appendChild(p);
    });
    caixa.hidden = false;
    caixa.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function emailValido(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/* ---------- Validação do login ---------- */
function validarLogin(evento) {
    var form = evento.target;
    limparErros(form);
    var erros = [];

    var email = form.elements['email'];
    var senha = form.elements['senha'];

    if (email.value.trim() === '') {
        erros.push('Informe o e-mail.');
        marcarInvalido(email);
    } else if (!emailValido(email.value.trim())) {
        erros.push('O formato do e-mail é inválido.');
        marcarInvalido(email);
    }
    if (senha.value === '') {
        erros.push('Informe a senha.');
        marcarInvalido(senha);
    }

    if (erros.length > 0) {
        evento.preventDefault();
        mostrarErros(erros);
    }
}

/* ---------- Validação do cadastro ---------- */
function validarCadastro(evento) {
    var form = evento.target;
    limparErros(form);
    var erros = [];

    var nome = form.elements['nome'];
    var email = form.elements['email'];
    var senha = form.elements['senha'];
    var confirmar = form.elements['confirmar_senha'];

    if (nome.value.trim() === '') {
        erros.push('Informe o nome.');
        marcarInvalido(nome);
    }
    if (email.value.trim() === '') {
        erros.push('Informe o e-mail.');
        marcarInvalido(email);
    } else if (!emailValido(email.value.trim())) {
        erros.push('O formato do e-mail é inválido.');
        marcarInvalido(email);
    }
    if (senha.value === '') {
        erros.push('Informe a senha.');
        marcarInvalido(senha);
    } else if (senha.value.length < 6) {
        erros.push('A senha deve ter pelo menos 6 caracteres.');
        marcarInvalido(senha);
    }
    if (senha.value !== confirmar.value) {
        erros.push('A confirmação não é igual à senha.');
        marcarInvalido(confirmar);
    }

    if (erros.length > 0) {
        evento.preventDefault();
        mostrarErros(erros);
    }
}
