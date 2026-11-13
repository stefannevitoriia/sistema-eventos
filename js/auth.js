// auth.js - validações e interações das telas de login e cadastro.
// (Se o Fernando juntar tudo em scripts.js, é só copiar este conteúdo para lá.)

function emailValido(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function mostrarErros(erros) {
    const caixa = document.getElementById('erros-js');
    caixa.innerHTML = '';
    erros.forEach(function (texto) {
        const p = document.createElement('p');
        p.textContent = texto;
        caixa.appendChild(p);
    });
    caixa.hidden = erros.length === 0;
}

// Mostrar / ocultar senha
document.querySelectorAll('.btn-mostrar-senha').forEach(function (botao) {
    botao.addEventListener('click', function () {
        const campo = document.getElementById(botao.dataset.alvo);
        const oculto = campo.type === 'password';
        campo.type = oculto ? 'text' : 'password';
        botao.textContent = oculto ? 'Ocultar' : 'Mostrar';
    });
});

// Validação do login
const formLogin = document.getElementById('form-login');
if (formLogin) {
    formLogin.addEventListener('submit', function (evento) {
        const erros = [];
        const email = formLogin.email.value.trim();
        if (email === '') {
            erros.push('Informe o e-mail.');
        } else if (!emailValido(email)) {
            erros.push('O formato do e-mail é inválido.');
        }
        if (formLogin.senha.value === '') {
            erros.push('Informe a senha.');
        }
        mostrarErros(erros);
        if (erros.length > 0) {
            evento.preventDefault();
        }
    });
}

// Validação do cadastro
const formCadastro = document.getElementById('form-cadastro');
if (formCadastro) {
    formCadastro.addEventListener('submit', function (evento) {
        const erros = [];
        const email = formCadastro.email.value.trim();
        const senha = formCadastro.senha.value;

        if (formCadastro.nome.value.trim() === '') {
            erros.push('Informe o nome.');
        }
        if (email === '') {
            erros.push('Informe o e-mail.');
        } else if (!emailValido(email)) {
            erros.push('O formato do e-mail é inválido.');
        }
        if (senha === '') {
            erros.push('Informe a senha.');
        } else if (senha.length < 6) {
            erros.push('A senha deve ter pelo menos 6 caracteres.');
        }
        if (formCadastro.confirmar_senha.value !== senha) {
            erros.push('As senhas não são iguais.');
        }
        mostrarErros(erros);
        if (erros.length > 0) {
            evento.preventDefault();
        }
    });
}
