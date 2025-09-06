<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistema de Tarefas</title>
  <meta name="description" content="Transforme a eficiência do seu negócio com nosso sistema de gestão inovador...">
  <meta name="author" content="JacyCordeiro">
  <meta name="keywords" content="sistema de gestão, automação, produtividade, tarefas">
  <link rel="icon" href="../img/icone.png" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/imask"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background: #f1f5f9;">

<div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
  <h4 class="text-center text-primary mb-4">Login</h4>
  <form id="form-login">
    <div class="mb-3">
      <label for="telefone" class="form-label">WhatsApp (Telefone)</label>
      <input type="text" class="form-control" name="telefone" id="telefone-login" required placeholder="(00) 00000-0000">
    </div>
    <div class="mb-3 position-relative">
      <label for="senha" class="form-label">Senha</label>
      <input type="password" class="form-control" name="senha" id="senha" required>
      <span id="toggleSenha" style="position:absolute; right:10px; top:38px; cursor:pointer;">👁️</span>
    </div>
    <button type="submit" class="btn btn-primary w-100">Entrar</button>
    <div id="msg-erro" class="text-danger text-center mt-3"></div>
    <div class="text-center mt-3">
      <a href="#" onclick="abrirRecuperarSenha()">Esqueceu a senha?</a>
    </div>
  </form>
  <div class="text-center mt-3">
    <a href="#" onclick="new bootstrap.Modal(document.getElementById('modalCadastroUsuario')).show()">Cadastrar-se</a>
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
            <input type="text" class="form-control" name="telefone" id="telefone-cadastro" required placeholder="(00) 00000-0000">
          </div>
          <div class="mb-3 position-relative">
            <label class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha" id="senha-cadastro" required>
            <span id="toggleSenhaCadastro" style="position:absolute; right:10px; top:38px; cursor:pointer;">👁️</span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Recuperar Senha -->
<div class="modal fade" id="modalRecuperarSenha" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formRecuperarSenha">
        <div class="modal-header">
          <h5 class="modal-title">Recuperar Senha</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">WhatsApp (Telefone)</label>
            <input type="text" class="form-control" name="telefone" id="telefone-recuperar" required placeholder="(00) 00000-0000">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Recuperar Senha</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Máscara de telefone
IMask(document.getElementById('telefone-login'), { mask: '(00)00000-0000' });
IMask(document.getElementById('telefone-cadastro'), { mask: '(00)00000-0000' });
IMask(document.getElementById('telefone-recuperar'), { mask: '(00)00000-0000' });

// Mostrar/ocultar senha
document.getElementById('toggleSenha').addEventListener('click', function() {
  const senha = document.getElementById('senha');
  senha.type = senha.type === 'password' ? 'text' : 'password';
});

document.getElementById('toggleSenhaCadastro').addEventListener('click', function() {
  const senhaCadastro = document.getElementById('senha-cadastro');
  senhaCadastro.type = senhaCadastro.type === 'password' ? 'text' : 'password';
});

// LOGIN
document.getElementById("form-login").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('../paginas/login_ajax.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    if (data === 'ok') {
      window.location.href = 'painel.php';
    } else {
      document.getElementById("msg-erro").textContent = data;
    }
  });
});

// CADASTRAR USUÁRIO
document.addEventListener('DOMContentLoaded', function() {
  const formCadastro = document.getElementById('formCadastro');
  const telefoneInput = document.getElementById('telefone-cadastro');

  formCadastro.addEventListener('submit', function(e) {
    e.preventDefault();
    const telefone = telefoneInput.value.trim();
    const telefoneNumerico = telefone.replace(/\D/g, '');

    fetch('../paginas/usuarios/validar_telefone.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'telefone=' + telefoneNumerico
    })
    .then(response => response.text())
    .then(data => {
      if (data === 'EXISTE') {
        Swal.fire({
          icon: 'warning',
          title: 'Atenção!',
          text: 'Este WhatsApp já está cadastrado!',
          confirmButtonColor: '#3085d6'
        });
      } else {
        const formData = new FormData(formCadastro);
        fetch('../paginas/cadastrar_usuario.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(msg => {
          Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Cadastro realizado com sucesso!',
            confirmButtonColor: '#28a745'
          });
          bootstrap.Modal.getInstance(document.getElementById('modalCadastroUsuario')).hide();
          formCadastro.reset();
        });
      }
    })
    .catch(error => {
      console.error('Erro na validação do telefone:', error);
      Swal.fire({
        icon: 'error',
        title: 'Erro!',
        text: 'Erro ao validar telefone.',
        confirmButtonColor: '#dc3545'
      });
    });
  });
});

// RECUPERAR SENHA
document.getElementById("formRecuperarSenha").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('../paginas/recuperar_senha.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(msg => {
    Swal.fire({
      icon: 'info',
      title: 'Recuperação de Senha',
      text: msg,
      confirmButtonColor: '#ffc107'
    });
    bootstrap.Modal.getInstance(document.getElementById('modalRecuperarSenha')).hide();
    document.getElementById('formRecuperarSenha').reset();
  });
});

function abrirRecuperarSenha() {
  new bootstrap.Modal(document.getElementById('modalRecuperarSenha')).show();
}
</script>

</body>
</html>
