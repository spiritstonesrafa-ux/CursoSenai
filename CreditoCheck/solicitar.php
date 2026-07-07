<?php
// ============================================
// solicitar.php - Processa a solicitacao de emprestimo
// ============================================
require_once 'conexao.php';

// Esta pagina deve receber dados por POST vindos do formulario de emprestimo.
// Se alguem tentar abri-la diretamente no navegador, volta para a tela inicial.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Converte os valores recebidos para os tipos esperados.
// intval() transforma em numero inteiro; floatval() transforma em numero decimal.
$clienteId = intval($_POST['cliente_id']);
$valor      = floatval($_POST['valor']);

$conn = conectar();

// Verifica novamente o status do cliente (seguranca).
// Mesmo que o formulario so apareca para clientes confiaveis, o servidor valida de novo.
$stmt = $conn->prepare("SELECT nome, status_credito FROM clientes WHERE id = ?");
$stmt->bind_param("i", $clienteId);
$stmt->execute();
$res = $stmt->get_result();

// Se o ID recebido nao existir no banco, nao ha como processar a solicitacao.
if ($res->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$cliente = $res->fetch_assoc();
$stmt->close();

// Define o status da solicitacao conforme a regra do sistema:
// perfil confiavel e valor positivo = aprovada; qualquer outro caso = negada.
if ($cliente['status_credito'] === 'Perfil confiavel' && $valor > 0) {
    $statusSolicitacao = 'Aprovada';
} else {
    $statusSolicitacao = 'Negada';
}

// Salva a solicitacao no banco, inclusive quando ela e negada.
// Isso permite manter um historico na pagina solicitacoes.php.
$stmt2 = $conn->prepare("INSERT INTO solicitacoes (cliente_id, valor_solicitado, status_solicitacao) VALUES (?, ?, ?)");
$stmt2->bind_param("ids", $clienteId, $valor, $statusSolicitacao);
$stmt2->execute();
$stmt2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Solicitação — CréditoCheck</title>
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
            <a href="solicitacoes.php" class="nav-link">Solicitações</a>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Resultado da Solicitação</h2>
        <p>Confira o resultado do pedido de empréstimo</p>
    </div>

    <div class="card resultado-card text-center">
        <?php if ($statusSolicitacao === 'Aprovada'): ?>
            <div class="result-icon result-ok">✅</div>
            <h3 class="result-title ok">Solicitação APROVADA!</h3>
            <p>O empréstimo foi aprovado para <strong><?= htmlspecialchars($cliente['nome']) ?></strong>.</p>
            <p class="valor-destaque">Valor: <strong>R$ <?= number_format($valor, 2, ',', '.') ?></strong></p>
            <p class="subtexto">A solicitação foi registrada com sucesso.</p>
        <?php else: ?>
            <div class="result-icon result-block">🚫</div>
            <h3 class="result-title block">Solicitação NEGADA!</h3>
            <p>O empréstimo foi <strong>recusado</strong> para <strong><?= htmlspecialchars($cliente['nome']) ?></strong>.</p>
            <p class="subtexto">O cliente possui pendências no SPC/Serasa.</p>
        <?php endif; ?>

        <div class="botoes-resultado">
            <a href="index.php" class="btn btn-primary">🔍 Nova Consulta</a>
            <a href="solicitacoes.php" class="btn btn-outline">📋 Ver Solicitações</a>
        </div>
    </div>
</main>

<footer>
    <p>Sistema de Análise de Crédito Rafael Alves &copy; 2026 — CréditoCheck &nbsp;|&nbsp; Exercício de curso PHP + MySQL</p>
</footer>

</body>
</html>
