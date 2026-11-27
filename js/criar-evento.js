// criar-evento.js - validação do formulário de cadastro de eventos.
// (O servidor valida tudo de novo em criar-evento.php; isto só dá resposta rápida.)

(function () {
    const form = document.getElementById('form-evento');
    if (!form) {
        return;
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

    function dataReal(valor) {
        const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(valor);
        if (!m) {
            return null;
        }
        const d = new Date(+m[1], +m[2] - 1, +m[3]);
        const ok = d.getFullYear() === +m[1] && d.getMonth() === +m[2] - 1 && d.getDate() === +m[3];
        return ok ? d : null;
    }

    form.addEventListener('submit', function (evento) {
        const erros = [];
        const nome = form.nome.value.trim();
        const descricao = form.descricao.value.trim();
        const data = form.data.value.trim();
        const horario = form.horario.value.trim();
        const local = form.local.value.trim();

        if (nome === '') {
            erros.push('Informe o nome do evento.');
        } else if (nome.length > 100) {
            erros.push('O nome deve ter no máximo 100 caracteres.');
        }

        if (descricao === '') {
            erros.push('Informe a descrição do evento.');
        } else if (descricao.length > 1000) {
            erros.push('A descrição deve ter no máximo 1000 caracteres.');
        }

        let dataObj = null;
        if (data === '') {
            erros.push('Informe a data do evento.');
        } else {
            dataObj = dataReal(data);
            if (!dataObj) {
                erros.push('A data informada é inválida.');
            }
        }

        let horarioValido = false;
        if (horario === '') {
            erros.push('Informe o horário do evento.');
        } else if (!/^([01]\d|2[0-3]):[0-5]\d$/.test(horario)) {
            erros.push('O horário informado é inválido.');
        } else {
            horarioValido = true;
        }

        if (dataObj) {
            const agora = new Date();
            const hoje = new Date(agora.getFullYear(), agora.getMonth(), agora.getDate());
            if (dataObj < hoje) {
                erros.push('A data do evento não pode estar no passado.');
            } else if (horarioValido && dataObj.getTime() === hoje.getTime()) {
                const hh = String(agora.getHours()).padStart(2, '0');
                const mm = String(agora.getMinutes()).padStart(2, '0');
                if (horario < hh + ':' + mm) {
                    erros.push('O horário do evento já passou.');
                }
            }
        }

        if (local === '') {
            erros.push('Informe o local do evento.');
        } else if (local.length > 150) {
            erros.push('O local deve ter no máximo 150 caracteres.');
        }

        mostrarErros(erros);
        if (erros.length > 0) {
            evento.preventDefault();
        }
    });
})();
