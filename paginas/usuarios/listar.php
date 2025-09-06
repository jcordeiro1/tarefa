<?php
session_start();
require_once '../../conexao.php';

function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone) === 11) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7);
    } elseif (strlen($telefone) === 10) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    } else {
        return $telefone;
    }
}

$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$usuario_id) {
    echo '<p class="text-danger">Usuário não autenticado.</p>';
    exit();
}

$query = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$query->bindParam(':id', $usuario_id, PDO::PARAM_INT);
$query->execute();
$usuario = $query->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover">';
    echo '<thead class="table-primary">';
    echo '<tr>';
    echo '<th>Nome</th>';
    echo '<th>Telefone</th>';
    echo '<th>Ações</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    echo '<tr>';
    echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';
    echo '<td>' . formatarTelefone($usuario['telefone']) . '</td>';
    echo '<td>';
    echo '<button class="btn btn-sm btn-primary me-2" onclick="editarUsuario(' . (int)$usuario['id'] . ')">Editar</button>';
    echo '<button class="btn btn-sm btn-danger" onclick="excluirUsuario(' . (int)$usuario['id'] . ')">Excluir</button>';
    echo '</td>';
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<p class="text-muted">Usuário não encontrado.</p>';
}
?>
<script>
function excluirUsuario(id) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        fetch('../paginas/usuarios/excluir.php?id=' + id, { method: 'GET' })
            .then(response => response.text())
            .then(data => {
                alert('Usuário excluído com sucesso!');
                location.reload();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao excluir o usuário.');
            });
    }
}

// Função editarUsuario que abre o modal e carrega dados
function editarUsuario(id) {
    fetch('../paginas/usuarios/editar.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        // Preenche os campos da modal
        document.getElementById('id').value = data.id;
        document.getElementById('nome').value = data.nome;
        document.getElementById('telefone').value = data.telefone;
        document.getElementById('senha').value = ''; // Limpa o campo senha
        document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuário';
        // Abre o modal
        var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        modal.show();
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao carregar dados do usuário.');
    });
}
</script>
