<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área Administrativa</title>
  <meta name="description" content="Transforme a eficiência do seu negócio com nosso sistema de gestão inovador...">
  <meta name="author" content="JacyCordeiro">
  <meta name="keywords" content="sistema de gestão, automação, produtividade, tarefas">
  <link rel="icon" href="../../img/icone.png" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .custom-btn {
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .custom-btn:hover {
      transform: translateY(-2px);
    }

    .custom-input {
      transition: box-shadow 0.3s ease, transform 0.3s ease;
    }

    .custom-input:focus {
      box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
      transform: scale(1.03);
    }
  </style>
</head>
<body style="background: #f8f9fa;">

<div class="container mt-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-center gap-2">
      <h2 class="text-primary mb-0">Área Administrativa</h2>
      <a href="../painel/painel.php" class="btn btn-primary rounded-pill shadow-sm custom-btn">
        <i class="bi bi-arrow-left-circle me-1"></i> Voltar ao Painel
      </a>
    </div>
    <input type="text" id="busca" class="form-control shadow-sm custom-input" placeholder="🔍 Pesquisar usuários..." style="max-width: 300px;">
  </div>

  <!-- Conteúdo Principal -->
  <div id="listar-admin"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Carregar dados admin
function carregarAdmin(pagina = 1) {
  fetch('admin/listar.php?pagina=' + pagina)
    .then(response => response.text())
    .then(data => {
      document.getElementById('listar-admin').innerHTML = data;
    });
}

// Filtro de busca instantânea
function setupBusca() {
  const inputBusca = document.getElementById('busca');
  inputBusca.addEventListener('input', function () {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('.accordion-item').forEach(item => {
      const texto = item.querySelector('.accordion-button').textContent.toLowerCase();
      item.style.display = texto.includes(filtro) ? '' : 'none';
    });
  });
}

// Resetar senha
function resetarSenha(id) {
  if (!id) {
    alert('ID inválido.');
    return;
  }

  if (confirm('Deseja resetar a senha deste usuário?')) {
    fetch('admin/resetar_senha.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.text())
    .then(data => {
      alert(data);
      carregarAdmin();
    })
    .catch(error => {
      console.error('Erro:', error);
      alert('Erro ao resetar a senha.');
    });
  }
}

// Excluir usuário
function excluirUsuario(id) {
  if (confirm('Deseja realmente excluir este usuário?')) {
    fetch('admin/excluir_usuario_admin.php?id=' + id)
      .then(response => response.text())
      .then(msg => {
        alert(msg);
        carregarAdmin();
      })
      .catch(error => console.error('Erro ao excluir usuário:', error));
  }
}


// Excluir Tarefa Individual
function excluirTarefaIndividual(id) {
  if (confirm('Deseja realmente excluir esta tarefa?')) {
    fetch('admin/excluir_tarefa_individual.php?id=' + id)
      .then(response => response.text())
      .then(msg => {
        alert(msg);
        carregarAdmin(); // Recarrega a lista
      })
      .catch(error => console.error('Erro ao excluir tarefa:', error));
  }
}

// Inicializa
carregarAdmin();
setupBusca();
</script>

</body>
</html>
