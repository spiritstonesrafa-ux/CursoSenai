<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Análise de Crédito - SPC/Serasa</title>
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
            <a href="index.php" class="nav-link active">Consultar CPF</a>
            <a href="cadastro.php" class="nav-link">Cadastrar Cliente</a>
            <a href="clientes.php" class="nav-link">Ver Clientes</a>
            <a href="solicitacoes.php" class="nav-link">Solicitações</a>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Consulta de Crédito</h2>
        <p>Verifique a situação do cliente no SPC/Serasa e solicite um empréstimo</p>
    </div>

    <!-- Formulario de consulta -->
    <div class="card">
        <h3 class="card-title">🔍 Consultar CPF do Cliente</h3>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="cpf">CPF do Cliente:</label>
                <input 
                    type="text" 
                    id="cpf" 
                    name="cpf" 
                    placeholder="000.000.000-00" 
                    maxlength="14"
                    required
                    oninput="formatarCPF(this)"
                >
            </div>
            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>
    </div>

    <?php
    // Carrega a funcao conectar(), usada para falar com o MySQL.
    require_once 'conexao.php';

    // Verifica se o formulario foi enviado.
    // REQUEST_METHOD indica se a pagina foi aberta por GET ou enviada por POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cpf'])) {
        // Pega o CPF digitado e remove espacos extras.
        $cpf = trim($_POST['cpf']);
        $conn = conectar();

        // Busca o cliente pelo CPF.
        // A consulta preparada evita SQL injection, pois o valor do usuario nao entra direto no SQL.
        $stmt = $conn->prepare("SELECT * FROM clientes WHERE cpf = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            // Cliente nao encontrado
            echo '<div class="alert alert-warning">
                    <span class="alert-icon">⚠️</span>
                    <div>
                        <strong>CPF não encontrado!</strong>
                        <p>Nenhum cliente cadastrado com o CPF informado.</p>
                        <a href="cadastro.php" class="link">Cadastrar novo cliente</a>
                    </div>
                  </div>';
        } else {
            // fetch_assoc() transforma a linha encontrada em um array com nomes das colunas.
            $cliente = $resultado->fetch_assoc();
            $status  = $cliente['status_credito'];

            // htmlspecialchars() protege a tela contra HTML/JavaScript digitado nos cadastros.
            $nome    = htmlspecialchars($cliente['nome']);
            $clienteId = $cliente['id'];

            // Define a aparencia do status antes de montar o HTML do resultado.
            $classeStatus = ($status === 'Perfil confiavel') ? 'status-ok' : 'status-block';
            $iconeStatus  = ($status === 'Perfil confiavel') ? '✅' : '🚫';

            echo '<div class="card resultado-card">
                    <h3 class="card-title">📋 Resultado da Consulta</h3>
                    <div class="cliente-info">
                        <div class="info-row">
                            <span class="info-label">Nome:</span>
                            <span class="info-value">' . $nome . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">CPF:</span>
                            <span class="info-value">' . htmlspecialchars($cliente['cpf']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">E-mail:</span>
                            <span class="info-value">' . htmlspecialchars($cliente['email']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Telefone:</span>
                            <span class="info-value">' . htmlspecialchars($cliente['telefone']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status SPC/Serasa:</span>
                            <span class="badge ' . $classeStatus . '">' . $iconeStatus . ' ' . htmlspecialchars($status) . '</span>
                        </div>
                    </div>';

            // Regra de negocio da tela:
            // somente clientes com "Perfil confiavel" podem solicitar emprestimo.
            if ($status === 'Perfil confiavel') {
                echo '<div class="alert alert-success">
                        <span class="alert-icon">✅</span>
                        <div>
                            <strong>Cliente AUTORIZADO a solicitar empréstimo</strong>
                            <p>Este cliente não possui pendências no SPC/Serasa.</p>
                        </div>
                      </div>';

                // Formulario de solicitacao de emprestimo.
                // O cliente_id fica escondido porque o usuario nao precisa digitar esse dado.
                echo '<form method="POST" action="solicitar.php" class="emprestimo-form">
                        <input type="hidden" name="cliente_id" value="' . $clienteId . '">
                        <h4>💰 Solicitar Empréstimo</h4>
                        <div class="form-group">
                            <label for="valor">Valor desejado (R$):</label>
                            <input type="number" id="valor" name="valor" min="100" max="50000" step="100" placeholder="Ex: 5000.00" required>
                        </div>
                        <button type="submit" class="btn btn-success">Enviar Solicitação</button>
                      </form>';

            } else {
                // Cliente com pendencia: a tela informa a restricao e nao mostra o formulario.
                echo '<div class="alert alert-danger">
                        <span class="alert-icon">🚫</span>
                        <div>
                            <strong>Cliente RESTRITO — Empréstimo NEGADO</strong>
                            <p>Este cliente possui pendências no SPC/Serasa e não pode solicitar empréstimo.</p>
                        </div>
                      </div>';
            }

            echo '</div>'; // fecha resultado-card
        }

        // Fecha statement e conexao depois que a consulta terminou.
        $stmt->close();
        $conn->close();
    }
    ?>

</main>

<footer>
    <p>Sistema de Análise de Crédito Rafael Alves &copy; 2026 — CréditoCheck &nbsp;|&nbsp; Exercício de curso PHP + MySQL</p>
</footer>

<script>
function formatarCPF(input) {
    // Mantem apenas numeros e adiciona pontos/traco conforme o usuario digita.
    let v = input.value.replace(/\D/g, '');
    if (v.length <= 11) {
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d)/, '$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }
    input.value = v;
}
</script>

</body>
</html>
