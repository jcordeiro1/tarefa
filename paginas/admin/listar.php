<?php
require '../../conexao.php';

// Função para formatar telefone
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

// Paginação
$pagina = (int)($_GET['pagina'] ?? 1);
$limite = 10;
$offset = ($pagina - 1) * $limite;

// Buscar usuários
$stmt = $pdo->prepare("SELECT * FROM usuarios ORDER BY nome ASC LIMIT :limite OFFSET :offset");
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($usuarios) === 0) {
    echo '<p class="text-muted">Nenhum usuário encontrado.</p>';
    exit();
}

echo '<div class="accordion" id="accordionUsuarios">';

foreach ($usuarios as $usuario) {
    $tarefasStmt = $pdo->prepare("SELECT * FROM tarefas WHERE usuario_id = ? ORDER BY data ASC, hora ASC");
    $tarefasStmt->execute([$usuario['id']]);
    $tarefas = $tarefasStmt->fetchAll(PDO::FETCH_ASSOC);

    $usuarioId = (int)$usuario['id'];
    $usuarioNome = htmlspecialchars($usuario['nome']);
    $telefoneFormatado = formatarTelefone($usuario['telefone']);
    $qtdTarefas = count($tarefas);

    echo '
    <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading' . $usuarioId . '">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $usuarioId . '" aria-expanded="false" aria-controls="collapse' . $usuarioId . '">
                ' . $usuarioNome . ' (' . $qtdTarefas . ' Tarefa(s))
            </button>
        </h2>
        <div id="collapse' . $usuarioId . '" class="accordion-collapse collapse" aria-labelledby="heading' . $usuarioId . '" data-bs-parent="#accordionUsuarios">
            <div class="accordion-body">
                <div class="mb-2">
                    <strong>Telefone:</strong> ' . $telefoneFormatado . '<br>
                    <button class="btn btn-success btn-sm mt-2 me-1" onclick="resetarSenha(' . $usuarioId . ')">Resetar Senha</button>
                   <button class="btn btn-danger btn-sm mt-2" onclick="excluirUsuario(' . $usuario['id'] . ')">Excluir Usuário</button>
    </div>';

    if ($qtdTarefas > 0) {
        echo '<div class="table-responsive mt-3">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($tarefas as $tarefa) {
            $dataF = date('d/m/Y', strtotime($tarefa['data']));
            $horaF = date('H:i', strtotime($tarefa['hora']));
            $statusBadge = $tarefa['status'] === 'Concluída' ? 'success' : 'primary';

            echo '
                <tr>
                    <td>' . htmlspecialchars($tarefa['descricao']) . '</td>
                    <td>' . $dataF . '</td>
                    <td>' . $horaF . '</td>
                    <td><span class="badge bg-' . $statusBadge . '">' . htmlspecialchars($tarefa['status']) . '</span></td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="excluirTarefaIndividual(' . (int)$tarefa['id'] . ')">Excluir</button>
                    </td>
                </tr>';
        }

        echo '</tbody></table></div>';
    } else {
        echo '<p class="text-muted mt-3">Nenhuma tarefa cadastrada para este usuário.</p>';
    }

    echo '</div>
        </div>
    </div>';
}

echo '</div>';

// Paginação total
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPaginas = ceil($totalUsuarios / $limite);

if ($totalPaginas > 1) {
    echo '<nav class="mt-4">
        <ul class="pagination justify-content-center">';

    if ($pagina > 1) {
        echo '<li class="page-item">
                <button class="page-link" onclick="carregarAdmin(' . ($pagina - 1) . ')">&laquo; Anterior</button>
              </li>';
    }

    $intervalo = 2; // Quantidade de páginas para mostrar antes/depois da atual

    $start = max(1, $pagina - $intervalo);
    $end = min($totalPaginas, $pagina + $intervalo);

    if ($start > 1) {
        echo '<li class="page-item"><button class="page-link" onclick="carregarAdmin(1)">1</button></li>';
        if ($start > 2) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $ativo = ($i == $pagina) ? ' active' : '';
        echo '<li class="page-item' . $ativo . '">
                <button class="page-link" onclick="carregarAdmin(' . $i . ')">' . $i . '</button>
              </li>';
    }

    if ($end < $totalPaginas) {
        if ($end < $totalPaginas - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        echo '<li class="page-item"><button class="page-link" onclick="carregarAdmin(' . $totalPaginas . ')">' . $totalPaginas . '</button></li>';
    }

    if ($pagina < $totalPaginas) {
        echo '<li class="page-item">
                <button class="page-link" onclick="carregarAdmin(' . ($pagina + 1) . ')">Próximo &raquo;</button>
              </li>';
    }

    echo '</ul>
    </nav>';
}
?>
