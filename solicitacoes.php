<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações de Empréstimo — CréditoCheck</title>
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
            <a href="clientes.php" class="nav-link">Ver Clientes</a>
            <a href="solicitacoes.php" class="nav-link active">Solicitações</a>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Solicitações de Empréstimo</h2>
        <p>Histórico de todas as solicitações de empréstimo</p>
    </div>

    <?php
    // Carrega a conexao com o banco de dados.
    require_once 'conexao.php';
    $conn = conectar();

    // Esta consulta junta duas tabelas:
    // solicitacoes guarda o pedido, clientes guarda nome e CPF do cliente.
    $sql = "SELECT s.id, c.nome, c.cpf, s.valor_solicitado, s.status_solicitacao, s.data_solicitacao
            FROM solicitacoes s
            JOIN clientes c ON s.cliente_id = c.id
            ORDER BY s.data_solicitacao DESC";

    $resultado = $conn->query($sql);

    // Total de linhas retornadas pela consulta, usado no resumo e para saber se a tabela aparece.
    $total     = $resultado->num_rows;
    ?>

    <div class="stats-bar">
        <span>Total de solicitações: <strong><?= $total ?></strong></span>
        <a href="index.php" class="btn btn-primary btn-sm">+ Nova Consulta</a>
    </div>

    <div class="card">
        <?php if ($total === 0): ?>
            <p class="sem-dados">Nenhuma solicitação registrada. <a href="index.php">Fazer uma consulta</a></p>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>CPF</th>
                        <th>Valor Solicitado</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($s = $resultado->fetch_assoc()): ?>
                    <?php // Cada repeticao do while monta uma linha da tabela HTML. ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><strong><?= htmlspecialchars($s['nome']) ?></strong></td>
                        <td><?= htmlspecialchars($s['cpf']) ?></td>
                        <?php // number_format() mostra o valor no padrao brasileiro: 1.234,56. ?>
                        <td class="valor">R$ <?= number_format($s['valor_solicitado'], 2, ',', '.') ?></td>
                        <td>
                            <?php if ($s['status_solicitacao'] === 'Aprovada'): ?>
                                <span class="badge status-ok">✅ Aprovada</span>
                            <?php else: ?>
                                <span class="badge status-block">🚫 Negada</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($s['data_solicitacao'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <?php
    // Fecha a conexao com o banco ao final da pagina.
    $conn->close();
    ?>
</main>

<footer>
    <p>Sistema de Análise de Crédito Rafael Alves &copy; 2026 — CréditoCheck &nbsp;|&nbsp; Exercício de curso PHP + MySQL</p>
</footer>

</body>
</html>
