# Curso SENAI - CreditoCheck

Projeto desenvolvido em PHP e MySQL para simular um sistema simples de analise de credito, com consulta de CPF, cadastro de clientes e registro de solicitacoes de emprestimo.

## Funcionalidades

- Cadastro de clientes com nome, CPF, e-mail, telefone e status de credito.
- Consulta de cliente por CPF.
- Analise simples de credito com base no status SPC/Serasa.
- Solicitacao de emprestimo para clientes com perfil confiavel.
- Historico de solicitacoes aprovadas e negadas.
- Listagem de clientes cadastrados.

## Tecnologias usadas

- PHP
- MySQL
- HTML
- CSS
- XAMPP

## Requisitos

- XAMPP instalado
- Apache ativo
- MySQL ativo
- Navegador web

## Como executar o projeto

1. Clone ou baixe este repositorio dentro da pasta `htdocs` do XAMPP:

   ```bash
   C:\xampp\htdocs\CursoSenai
   ```

2. Abra o XAMPP e inicie:

   - Apache
   - MySQL

3. Importe o banco de dados:

   - Abra o phpMyAdmin ou HeidiSQL.
   - Execute o arquivo `CreditoCheck/banco.sql`.
   - O script cria o banco `sistema_credito`, as tabelas e alguns dados de exemplo.

4. Acesse o sistema no navegador:

   ```text
   http://localhost/CursoSenai/CreditoCheck/
   ```

## Configuracao do banco

A conexao com o banco esta no arquivo `conexao.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_credito');
```

Essas configuracoes seguem o padrao do XAMPP. Se o seu MySQL usar outro usuario, senha ou nome de banco, altere esses valores.

## Estrutura do projeto

```text
.
|-- banco.sql          # Script de criacao do banco e dados de exemplo
|-- cadastro.php       # Tela de cadastro de clientes
|-- clientes.php       # Listagem de clientes cadastrados
|-- conexao.php        # Configuracao e funcao de conexao com o MySQL
|-- index.php          # Tela principal de consulta por CPF
|-- solicitacoes.php   # Historico de solicitacoes de emprestimo
|-- solicitar.php      # Processamento da solicitacao de emprestimo
`-- style.css          # Estilos do sistema
```

## Regra de negocio

- Clientes com status `Perfil confiavel` podem solicitar emprestimo.
- Clientes com status `Pendencia` tem a solicitacao negada.
- Todas as solicitacoes ficam registradas na tabela `solicitacoes`.

## Autor

Rafael Alves
