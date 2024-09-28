<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "cimatec";
$dbname = "php";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$dadosCadastrados = null; // Variável para armazenar os dados cadastrados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nascimento = trim($_POST['data_nascimento'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $biografia = trim($_POST['biografia'] ?? '');

    // Inicializa uma variável para armazenar mensagens de erro
    $erros = [];

    // Valida se todos os campos foram preenchidos
    if (empty($nome)) {
        $erros[] = "O campo 'Nome completo' é obrigatório.";
    }
    if (empty($email)) {
        $erros[] = "O campo 'E-mail' é obrigatório.";
    }
    if (empty($data_nascimento)) {
        $erros[] = "O campo 'Data de Nascimento' é obrigatório.";
    }
    if (empty($genero)) {
        $erros[] = "O campo 'Gênero' é obrigatório.";
    }
    if (empty($biografia)) {
        $erros[] = "O campo 'Biografia' é obrigatório.";
    }

    // Exibe mensagens de erro ou de sucesso
    if (count($erros) > 0) {
        foreach ($erros as $erro) {
            echo "<script>alert('$erro');</script>";
        }
    } else {
        // Prepara a instrução SQL para inserir os dados
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, data_nascimento, genero, biografia) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $email, $data_nascimento, $genero, $biografia);

        // Executa a instrução SQL e verifica se foi bem-sucedida
        if ($stmt->execute()) {
            $dadosCadastrados = [
                'nome' => $nome,
                'email' => $email,
                'data_nascimento' => $data_nascimento,
                'genero' => $genero,
                'biografia' => $biografia
            ];
        } else {
            echo "<script>alert('Erro ao salvar os dados.');</script>";
        }

        // Fecha a instrução
        $stmt->close();
    }
}

// Lida com a exclusão dos dados
if (isset($_POST['deletar'])) {
    // Coleta o email do usuário que será deletado
    $emailParaDeletar = trim($_POST['email']);
    
    // Prepara a instrução SQL para deletar os dados
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $emailParaDeletar); // Usando o email como referência para deletar

    // Executa a instrução SQL e verifica se foi bem-sucedida
    if ($stmt->execute()) {
        echo "<script>alert('Dados deletados com sucesso.');</script>";
        $dadosCadastrados = null; // Limpa os dados cadastrados
    } else {
        echo "<script>alert('Erro ao deletar os dados.');</script>";
    }

    // Fecha a instrução
    $stmt->close();
}

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários Cadastrados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Dados do Cadastro</h2>
        <?php if ($dadosCadastrados): ?>
            <p><strong>Nome completo:</strong> <?php echo htmlspecialchars($dadosCadastrados['nome']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($dadosCadastrados['email']); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($dadosCadastrados['data_nascimento']); ?></p>
            <p><strong>Gênero:</strong> <?php echo htmlspecialchars($dadosCadastrados['genero']); ?></p>
            <p><strong>Biografia:</strong> <?php echo nl2br(htmlspecialchars($dadosCadastrados['biografia'])); ?></p>
            <form method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($dadosCadastrados['email']); ?>">
                <button type="submit" name="deletar">Deletar Dados</button>
            </form>
        <?php else: ?>
            <p>Nenhum dado foi cadastrado.</p>
        <?php endif; ?>
        <a href="forms.html">Voltar ao formulário</a>
    </div>
</body>
</html>
