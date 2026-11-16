# Cadastro de eventos (Livia)

Parte do Sistema de Eventos responsável por cadastrar novos eventos.

## Arquivos

| Arquivo | Função |
|---|---|
| `criar-evento.php` | Tela com o formulário, validação no servidor e gravação do evento |
| `js/criar-evento.js` | Validação no navegador (resposta rápida, antes de enviar) |
| `dados/eventos.json` | Onde os eventos são salvos (usa o `funcoes.php` do projeto) |

## Como funciona

1. O usuário precisa estar logado. Quem não está é redirecionado para `login.php`.
   Qualquer usuário logado pode cadastrar eventos.
2. O formulário tem: **nome, descrição, data, horário e local**.
3. Regras de validação (servidor e JavaScript):
   - todos os campos são obrigatórios;
   - nome até 100 caracteres, local até 150 e descrição até 1000;
   - data real (ex.: 31/02 é recusada) e horário no formato HH:MM;
   - não aceita data nem horário no passado.
4. Se estiver tudo certo, o evento é salvo em `dados/eventos.json` e a página mostra
   **"Evento cadastrado com sucesso!"**. Se houver problemas, os erros aparecem em vermelho
   e os campos preenchidos são mantidos.

## Formato salvo em `eventos.json`

```json
{
    "id": 1,
    "nome": "Festa de formatura",
    "descricao": "Comemoração da turma.",
    "data": "2027-03-10",
    "horario": "19:30",
    "local": "Salão principal",
    "criado_por": 1
}
```

`id` é incremental e `criado_por` é o id do usuário que cadastrou.

## Como testar

1. Rode o projeto (XAMPP em `htdocs/sistema-eventos` ou `php -S localhost:8000` na pasta do projeto).
2. Crie uma conta em `cadastro.php` e entre em `login.php`.
3. Abra `criar-evento.php` e cadastre um evento.
4. Confira em `eventos.php` (lista) e em `dados/eventos.json`.

## Observações

- A pasta `dados/` precisa de permissão de escrita pelo servidor.
- Depende do `funcoes.php` (sessão, `ler_json`, `salvar_json`, `e`, mensagens) e do `css/estilo.css`
  (classes `container`, `mensagem`, `erro`, `sucesso`).
- O link "Voltar ao início" aponta para `inicio.php` (parte do Fernando). Falta incluir um link
  para `criar-evento.php` no menu.
