<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes Cadastrados — CréditoCheck</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="header-content">
        <div class="logo">
            <span class="logo-icon">🏦</span>
            <div>
                <h1>CréditoCheck</h1>
                <p>Sistema de Análise de Crédito</p>
            </div>
        </div>
        <nav>
            <a href="index.php" class="nav-link">Consultar CPF</a>
            <a href="cadastro.php" class="nav-link">Cadastrar Cliente</a>
            <a href="clientes.php" class="nav-link active">Ver Clientes</a>
            <a href="solicitacoes.php" class="nav-link">Solicitações</a>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Clientes Cadastrados</h2>
        <p>Lista de todos os clientes no sistema</p>
    </div>

    <?php
    // Carrega a funcao de conexao e abre uma conexao com o banco.
    require_once 'conexao.php';
    $conn = conectar();

    // Busca todos os clientes, ordenando pelo nome para facilitar a leitura da tabela.
    $resultado = $conn->query("SELECT * FROM clientes ORDER BY nome ASC");

    // num_rows informa quantos registros voltaram da consulta.
    $total = $resultado->num_rows;
    ?>

    <div class="stats-bar">
        <span>Total de clientes: <strong><?= $total ?></strong></span>
        <a href="cadastro.php" class="btn btn-primary btn-sm">+ Novo Cliente</a>
    </div>

    <div class="card">
        <?php if ($total === 0): ?>
            <p class="sem-dados">Nenhum cliente cadastrado ainda. <a href="cadastro.php">Cadastrar agora</a></p>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Status SPC/Serasa</th>
                        <th>Cadastrado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $resultado->fetch_assoc()): ?>
                    <?php // fetch_assoc() entrega uma linha da consulta como array associativo. ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <?php // htmlspecialchars() evita executar HTML digitado por usuarios. ?>
                        <td><strong><?= htmlspecialchars($c['nome']) ?></strong></td>
                        <td><?= htmlspecialchars($c['cpf']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['telefone']) ?></td>
                        <td>
                            <?php if ($c['status_credito'] === 'Perfil confiavel'): ?>
                                <span class="badge status-ok">✅ Perfil confiável</span>
                            <?php else: ?>
                                <span class="badge status-block">🚫 Pendência</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($c['data_cadastro'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <?php
    // Fecha a conexao depois que a pagina terminou de usar os dados.
    $conn->close();
    ?>
</main>

<footer>
    <p>Sistema de Análise de Crédito Rafael Alves &copy; 2026 — CréditoCheck &nbsp;|&nbsp; Exercício de curso PHP + MySQL</p>
</footer>

</body>
</html>
