<!-- /modal/tarefa_modal.php -->
<div class="modal fade" id="modalCadastroTarefa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cadastrar Nova Tarefa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formTarefa">
          <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" name="titulo" id="titulo" required>
          </div>
          <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" id="descricao" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="data" class="form-label">Data</label>
            <input type="date" class="form-control" name="data" id="data" required>
          </div>
          <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="text" class="form-control" name="hora" id="hora" required>
          </div>
          <button type="submit" class="btn btn-success">Salvar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const horaMask = IMask(
  document.getElementById('hora'),
  { mask: '00:00' }
);

document.getElementById("formTarefa").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('../paginas/cadastrar_tarefa.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(msg => {
    alert(msg);
    bootstrap.Modal.getInstance(document.getElementById('modalCadastroTarefa')).hide();
    document.getElementById('formTarefa').reset();
    carregarPainel();
  });
});
</script>
