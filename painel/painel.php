<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Captura o nome e nível do usuário
$nome_usuario = $_SESSION['usuario_nome'] ?? 'Usuário';
$nivel_usuario = $_SESSION['usuario_nivel'] ?? 'usuario'; // Default
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Tarefas</title>
  <meta name="description" content="Transforme a eficiência do seu negócio com nosso sistema de gestão inovador. Automatize processos, gerencie equipes, controle financeiro e muito mais, tudo em uma única plataforma. Ideal para empresas que buscam otimizar operações e aumentar a produtividade. Experimente agora e veja a diferença!">
  <meta name="Author" content="JacyCordeiro">
  <meta name="keywords" content="sistema de gestão, automação de processos, controle financeiro, gestão de equipe, software de gestão, produtividade empresarial, agendamento, relatórios, gerenciamento de projetos, eficiência organizacional" />
   <link rel="icon" href="../img/icone.png" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body style="background: #e3f2fd;">
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h2 class="text-primary mb-0">Painel de Tarefas</h2>
  <div class="d-flex gap-2 flex-wrap">
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCadastroUsuario">
        Cadastrar Usuário
    </button>

    <!-- Botão ADMIN Só para Nível Admin -->
    <?php if ($_SESSION['usuario_nivel'] == 'admin'): ?>
    <a href="../paginas/admin.php" class="btn btn-warning">Área do Administrador</a>
    <?php endif; ?>

    <a href="logout.php" class="btn btn-danger">Sair</a>
  </div>
</div>


  <div class="d-flex flex-column flex-md-row justify-content-start align-items-stretch mb-4 gap-3">
    <button class="btn btn-outline-primary w-100 w-md-auto px-4 py-2 fw-bold rounded-pill shadow" onclick="abrirModalNovaTarefa()">
      <i class="bi bi-plus-circle"></i> Nova Tarefa
    </button>
    <input type="date" id="filtroDataInicio" class="form-control w-100 w-md-auto" onchange="carregarPainel()">
    <input type="date" id="filtroDataFim" class="form-control w-100 w-md-auto" onchange="carregarPainel()">
  </div>
  
  <div id="listar-tarefas" class="mt-4"></div>
  
  <h4 class="mt-5">Usuários Cadastrados</h4>
  <div id="listar-usuarios" class="table-responsive"></div>


  <canvas id="graficoTarefas" height="100" class="mt-4"></canvas>

</div>

<!-- Modal Detalhes da Tarefa -->
<div class="modal fade" id="modalTarefa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalhes da Tarefa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="conteudo-modal"></div>
    </div>
  </div>
</div>

<!-- Modal Nova Tarefa -->
<div class="modal fade" id="modalNovaTarefa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nova Tarefa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formNovaTarefa">
          <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <input type="text" class="form-control" id="descricao" required>
          </div>
          <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" class="form-control" id="data" required>
          </div>
          <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" class="form-control" id="hora" required>
          </div>
          <div class="mb-3">
            <label for="hora_alerta" class="form-label">Hora Alerta</label>
            <input type="time" class="form-control" id="hora_alerta">
          </div>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Tarefa -->
<div class="modal fade" id="modalEditarTarefa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditarTarefa">
        <div class="modal-header">
          <h5 class="modal-title">Editar Tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editar-id">
          <div class="mb-3">
            <label class="form-label">Descrição</label>
            <input type="text" class="form-control" name="descricao" id="editar-descricao" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="data" id="editar-data" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Hora</label>
            <input type="time" class="form-control" name="hora" id="editar-hora" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Hora Alerta</label>
            <input type="time" class="form-control" name="hora_alerta" id="editar-hora-alerta">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Cadastro -->
<div class="modal fade" id="modalCadastroUsuario" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCadastro">
        <div class="modal-header">
          <h5 class="modal-title">Cadastrar Usuário</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Telefone (WhatsApp)</label>
            <input type="text" class="form-control" name="telefone" id="telefone" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Cadastro/Edição de Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formUsuario">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalUsuarioLabel">Cadastrar Usuário</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
          </div>
          <div class="mb-3">
            <label for="telefone" class="form-label">Telefone (WhatsApp)</label>
            <input type="text" class="form-control" id="telefone" name="telefone" required>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" id="senha" name="senha">
            <button class="btn btn-outline-secondary" type="button" id="toggleSenha">
              <i class="bi bi-eye-slash" id="iconSenha"></i>
            </button>
          </div>
          <small class="text-muted">Deixe em branco para não alterar.</small>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// Máscara Telefone
function aplicarMascaraTelefone(input) {
    input.addEventListener('input', function (e) {
        let numero = e.target.value.replace(/\D/g, '');
        numero = numero.replace(/^(\d{2})(\d)/g, '($1) $2');
        numero = numero.replace(/(\d{5})(\d)/, '$1-$2');
        numero = numero.replace(/(\d{4})-(\d{5})/, '$1$2');
        e.target.value = numero;
    });
}
aplicarMascaraTelefone(document.getElementById('telefone'));

// Toggle senha
document.getElementById('toggleSenha').addEventListener('click', function () {
    const senhaInput = document.getElementById('senha');
    const iconSenha = document.getElementById('iconSenha');
    if (senhaInput.type === 'password') {
        senhaInput.type = 'text';
        iconSenha.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        senhaInput.type = 'password';
        iconSenha.classList.replace('bi-eye', 'bi-eye-slash');
    }
});


// Salvar usuário
document.getElementById('formUsuario').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../paginas/usuarios/salvar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert('Usuário salvo com sucesso!');
        carregarUsuarios();
        bootstrap.Modal.getInstance(document.getElementById('modalUsuario')).hide();
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar o usuário.');
    });
});

//cadastro usuário
document.getElementById("formCadastro").addEventListener("submit", function(e) {
  e.preventDefault();

  const btnCadastrar = this.querySelector('button[type="submit"]');
  btnCadastrar.disabled = true;
  btnCadastrar.textContent = "Salvando...";

  const formData = new FormData(this);

  fetch('../../paginas/cadastrar_usuario.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(msg => {
    alert(msg);
    bootstrap.Modal.getInstance(document.getElementById('modalCadastroUsuario')).hide();
    document.getElementById('formCadastro').reset();
    btnCadastrar.disabled = false;
    btnCadastrar.textContent = "Cadastrar";
  })
  .catch(err => {
    console.error(err);
    alert("Erro ao cadastrar usuário.");
    btnCadastrar.disabled = false;
    btnCadastrar.textContent = "Cadastrar";
  });
});


// Editar usuário
function editarUsuario(id) {
    fetch('../paginas/usuarios/editar.php?id=' + id)
    .then(response => response.json())
    .then(data => {
        document.getElementById('id').value = data.id;
        document.getElementById('nome').value = data.nome;
        document.getElementById('telefone').value = data.telefone;
        document.getElementById('senha').value = '';
        document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuário';
        var modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
        modal.show();
    });
}

// Excluir usuário
function excluirUsuario(id) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        fetch('../paginas/usuarios/excluir.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            alert(data);
            carregarUsuarios();
        });
    }
}

// Carregar painel de tarefas
function carregarPainel() {
    const inicio = document.getElementById('filtroDataInicio').value;
    const fim = document.getElementById('filtroDataFim').value;
    const params = new URLSearchParams({ inicio, fim });

    fetch('../paginas/listar_tarefas.php?' + params)
    .then(res => res.text())
    .then(html => document.getElementById('listar-tarefas').innerHTML = html);

    fetch('../paginas/grafico_tarefas.php')
    .then(res => res.json())
    .then(data => {
        grafico.data.datasets[0].data = data.valores;
        grafico.update();
    });
    

//Carregar Usuários
  fetch('../paginas/usuarios/listar.php')
    .then(res => res.text())
    .then(html => document.getElementById('listar-usuarios').innerHTML = html);
}


// Nova tarefa
document.getElementById('formNovaTarefa').addEventListener('submit', function(e) {
    e.preventDefault();

    const btnSalvar = this.querySelector('button[type="submit"]');
    btnSalvar.disabled = true;
    btnSalvar.textContent = "Salvando...";

    const descricao = document.getElementById('descricao').value;
    const data = document.getElementById('data').value;
    const hora = document.getElementById('hora').value;
    const hora_alerta = document.getElementById('hora_alerta').value;

    // Validação da Hora Alerta antes de enviar
    if (hora_alerta && hora) {
        const [hA, mA] = hora_alerta.split(':').map(Number);
        const [hG, mG] = hora.split(':').map(Number);

        const alertaDate = new Date(0, 0, 0, hA, mA);
        const agendadaDate = new Date(0, 0, 0, hG, mG);

        if (alertaDate >= agendadaDate) {
            alert('Hora Alerta deve ser ANTES da Hora da tarefa.');
            btnSalvar.disabled = false;
            btnSalvar.textContent = "Salvar";
            return;
        }
    }

    const formData = new FormData();
    formData.append('descricao', descricao);
    formData.append('data', data);
    formData.append('hora', hora);
    formData.append('hora_alerta', hora_alerta);

    fetch('../paginas/inserir_tarefa.php', { method: 'POST', body: formData })
    .then(response => response.text())
    .then(result => {
        alert(result);
        carregarPainel();
        bootstrap.Modal.getInstance(document.getElementById('modalNovaTarefa')).hide();
        btnSalvar.disabled = false;
        btnSalvar.textContent = "Salvar";
    })
    .catch(error => {
        alert("Erro ao salvar tarefa.");
        btnSalvar.disabled = false;
        btnSalvar.textContent = "Salvar";
        console.error(error);
    });
});



// Concluir tarefa
function concluirTarefa(id) {
    fetch('../paginas/concluir_tarefa.php?id=' + id)
    .then(response => response.text())
    .then(result => carregarPainel());
}

// Excluir tarefa
function excluirTarefa(id) {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        fetch('../paginas/excluir_tarefa.php?id=' + id)
        .then(response => response.text())
        .then(result => carregarPainel());
    }
}


// Função para abrir o modal e preencher os dados

function abrirModalEditar(id, descricao, data, hora, hora_alerta) {
  document.getElementById('editar-id').value = id;
  document.getElementById('editar-descricao').value = descricao;
  document.getElementById('editar-data').value = data;
  document.getElementById('editar-hora').value = hora;
  document.getElementById('editar-hora-alerta').value = hora_alerta || '';

  new bootstrap.Modal(document.getElementById('modalEditarTarefa')).show();
}

//EditarTarefa
document.getElementById("formEditarTarefa").addEventListener("submit", function(e) {
  e.preventDefault();

  const hora = document.getElementById('editar-hora').value;
  const horaAlerta = document.getElementById('editar-hora-alerta').value;

  // Validação de Hora Alerta
  if (horaAlerta && horaAlerta >= hora) {
    alert('Hora Alerta deve ser ANTES da Hora da tarefa.');
    return; // Não envia o formulário se inválido
  }

  const formData = new FormData(this);

  fetch('../paginas/editar_tarefa.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    if (data.includes('sucesso')) {
      // Fechar Modal
      bootstrap.Modal.getInstance(document.getElementById('modalEditarTarefa')).hide();

      // Mostrar Toast de sucesso
      const toast = new bootstrap.Toast(document.getElementById('toastSucesso'));
      toast.show();

      // Recarregar a página depois de 2 segundos
      setTimeout(() => {
        location.reload();
      }, 2000);
    } else {
      alert(data);
    }
  });
});

// Modal detalhe tarefa
function abrirModal(id) {
    fetch('../modal/detalhe_tarefa.php?id=' + id)
    .then(res => res.text())
    .then(html => {
        document.getElementById('conteudo-modal').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalTarefa')).show();
    });
}

// Modal nova tarefa
function abrirModalNovaTarefa() {
    new bootstrap.Modal(document.getElementById('modalNovaTarefa')).show();
}

// Carregar usuários
function carregarUsuarios() {
    fetch('../paginas/usuarios/listar.php')
    .then(res => res.text())
    .then(html => document.getElementById('listar-usuarios').innerHTML = html);
}

const ctx = document.getElementById('graficoTarefas');
const grafico = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Agendadas', 'Concluídas', 'Notificadas'],
    datasets: [{
      label: 'Tarefas',
      data: [0, 0, 0],
      backgroundColor: ['#42a5f5', '#66bb6a', '#ffca28']
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});

carregarPainel();
carregarUsuarios();
</script>

<script>
// Quando o usuário mudar a Hora ou Hora Alerta, tira o foco automaticamente
document.getElementById('editar-hora').addEventListener('change', function() {
  this.blur();
});
document.getElementById('editar-hora-alerta').addEventListener('change', function() {
  this.blur();
});

document.getElementById('hora-cadastro').addEventListener('change', function() {
  this.blur();
});
document.getElementById('hora-alerta-cadastro').addEventListener('change', function() {
  this.blur();
});

</script>

<script>
// Espera o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
  const telefoneInput = document.getElementById('telefone'); // ID do campo telefone
  const formCadastro = document.getElementById('formCadastro'); // ID do seu form de cadastro

  formCadastro.addEventListener('submit', function(e) {
    e.preventDefault(); // Para o envio normal
    const telefone = telefoneInput.value.trim();

    // Remove máscara
    const telefoneNumerico = telefone.replace(/\D/g, '');

    // Faz requisição AJAX para checar se telefone existe
    fetch('../paginas/usuarios/validar_telefone.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'telefone=' + telefoneNumerico
    })
    .then(response => response.text())
    .then(data => {
      if (data === 'EXISTE') {
        alert('Este WhatsApp já está cadastrado!');
      } else {
        // Se não existir, envia o formulário
        formCadastro.submit();
      }
    })
    .catch(error => {
      console.error('Erro na validação do telefone:', error);
      alert('Erro ao validar telefone.');
    });
  });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Outros modais ou conteúdo aqui -->

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
  <div id="toastSucesso" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Tarefa salva com sucesso!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

</body>
</html>
