<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente — CréditoCheck</title>
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
            <a href="cadastro.php" class="nav-link active">Cadastrar Cliente</a>
            <a href="clientes.php" class="nav-link">Ver Clientes</a>
            <a href="solicitacoes.php" class="nav-link">Solicitações</a>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Cadastrar Cliente</h2>
        <p>Adicione um novo cliente ao sistema SPC/Serasa</p>
    </div>

    <?php
    // Importa o arquivo que contem a funcao conectar().
    // require_once evita carregar o mesmo arquivo mais de uma vez.
    require_once 'conexao.php';

    // Esta variavel guardara a mensagem exibida ao usuario
    // depois de tentar cadastrar um cliente.
    $mensagem = '';

    // O bloco abaixo so roda quando o formulario e enviado por POST.
    // Quando a pagina abre pela primeira vez, ele e ignorado.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // trim() remove espacos extras no inicio e no fim dos campos digitados.
        $nome   = trim($_POST['nome']);
        $cpf    = trim($_POST['cpf']);
        $email  = trim($_POST['email']);
        $tel    = trim($_POST['telefone']);
        $status = $_POST['status_credito'];

        // Validacao simples: nome, CPF e status sao obrigatorios.
        if (empty($nome) || empty($cpf) || empty($status)) {
            $mensagem = '<div class="alert alert-warning"><span class="alert-icon">⚠️</span><div><strong>Preencha os campos obrigatórios!</strong></div></div>';
        } else {
            // Abre a conexao somente quando sera necessario acessar o banco.
            $conn = conectar();

            // Verifica se o CPF ja existe.
            // O prepare() cria uma consulta segura, separando o SQL dos dados digitados.
            $check = $conn->prepare("SELECT id FROM clientes WHERE cpf = ?");
            // "s" significa string. O CPF sera colocado no lugar do ? da consulta.
            $check->bind_param("s", $cpf);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $mensagem = '<div class="alert alert-warning"><span class="alert-icon">⚠️</span><div><strong>CPF já cadastrado!</strong><p>Este CPF já existe no sistema.</p></div></div>';
            } else {
                // Se o CPF ainda nao existe, insere o novo cliente na tabela clientes.
                $stmt = $conn->prepare("INSERT INTO clientes (nome, cpf, email, telefone, status_credito) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $nome, $cpf, $email, $tel, $status);

                if ($stmt->execute()) {
                    $mensagem = '<div class="alert alert-success"><span class="alert-icon">✅</span><div><strong>Cliente cadastrado com sucesso!</strong></div></div>';
                } else {
                    $mensagem = '<div class="alert alert-danger"><span class="alert-icon">❌</span><div><strong>Erro ao cadastrar!</strong><p>' . $stmt->error . '</p></div></div>';
                }
                $stmt->close();
            }

            // Fecha os recursos usados no banco. Isso libera memoria e conexoes.
            $check->close();
            $conn->close();
        }
    }

    // Mostra na tela a mensagem definida durante o processamento do formulario.
    echo $mensagem;
    ?>

    <div class="card">
        <h3 class="card-title">➕ Novo Cliente</h3>
        <form method="POST" action="cadastro.php">

            <div class="form-group">
                <label for="nome">Nome Completo: <span class="obrigatorio">*</span></label>
                <input type="text" id="nome" name="nome" placeholder="Ex: João da Silva" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cpf">CPF: <span class="obrigatorio">*</span></label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required oninput="formatarCPF(this)">
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="15" oninput="formatarTel(this)">
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="email@exemplo.com">
            </div>

            <div class="form-group">
                <label for="status_credito">Status no SPC/Serasa: <span class="obrigatorio">*</span></label>
                <select id="status_credito" name="status_credito" required>
                    <option value="">— Selecione —</option>
                    <option value="Perfil confiavel">✅ Perfil confiável</option>
                    <option value="Pendencia">🚫 Pendência</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar Cliente</button>
            <a href="clientes.php" class="btn btn-outline">Ver Clientes</a>
        </form>
    </div>
</main>

<footer>
    <p>Sistema de Análise de Crédito Rafael Alves &copy; 2026 — CréditoCheck &nbsp;|&nbsp; Exercício de curso PHP + MySQL</p>
</footer>

<script>
function formatarCPF(input) {
    // Remove tudo que nao for numero e depois aplica a mascara 000.000.000-00.
    let v = input.value.replace(/\D/g, '');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    input.value = v;
}
function formatarTel(input) {
    // Remove caracteres nao numericos e aplica uma mascara simples de telefone.
    let v = input.value.replace(/\D/g, '');
    v = v.replace(/^(\d{2})(\d)/, '($1) $2');
    v = v.replace(/(\d{5})(\d)/, '$1-$2');
    input.value = v;
}
</script>

</body>
</html>
