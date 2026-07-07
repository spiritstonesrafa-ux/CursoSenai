<?php
// ============================================
// Configuracao de conexao com o banco de dados
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_credito');

function conectar() {
    // Cria uma conexao com o MySQL usando as constantes definidas acima.
    // Assim, se usuario, senha ou banco mudarem, basta alterar em um lugar.
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Se a conexao falhar, interrompe a pagina e mostra uma mensagem amigavel.
    // Em sistemas reais, normalmente o erro tecnico ficaria em um log.
    if ($conn->connect_error) {
        die("
            <div style='font-family:sans-serif; padding:20px; background:#fff0f0; border:2px solid #e53e3e; border-radius:8px; margin:20px;'>
                <h3 style='color:#e53e3e;'>❌ Erro de Conexão</h3>
                <p>Não foi possível conectar ao banco de dados.</p>
                <p><strong>Erro:</strong> " . $conn->connect_error . "</p>
                <p>Verifique se o XAMPP está rodando e o banco foi criado.</p>
            </div>
        ");
    }

    // Define a codificacao usada na conversa com o banco.
    // Isso ajuda a gravar e ler textos com acentos corretamente.
    $conn->set_charset('utf8');

    // Retorna a conexao pronta para ser usada pelas outras paginas.
    return $conn;
}
?>
