  <?php require APPROOT . '/views/inc/header.php'; ?>
<div class="row">
  <div class="col-md-6 mx-auto">
    <div class="card card-body bg-light mt-5">
      <h2>Agregar Docente</h2>
      <form action="<?php echo URLROOT; ?>/teachers/add" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
          <label>Nombre:</label>
          <input type="text" name="name" class="form-control form-control-lg" id="name" required>
        </div>
        <div class="form-group">
          <label>Primer Apellido:</label>
          <input type="text" name="lastName1" class="form-control form-control-lg" id="lastName1" required>
        </div>
        <div class="form-group">
          <label>Segundo Apellido:</label>
          <input type="text" name="lastName2" class="form-control form-control-lg" id="lastName2" required>
        </div>
        <div class="form-group">
          <label>CURP:</label>
          <input type="text" name="curp" class="form-control form-control-lg" id="curp" required>
        </div>
        <div class="form-group">
          <label>RFC:</label>
          <input type="text" name="rfc" class="form-control form-control-lg" id="rfc" required>
        </div>
        <div class="form-group">
          <label>Clave:</label>
          <input type="text" name="clave" class="form-control form-control-lg" id="clave" required>
        </div>
        <input type="submit" class="btn btn-success btn-block" value="New">
      </form>
    </div>
  </div>
</div>

<script>
  function validateForm() {
    var name = document.getElementById("name").value;
    var lastName1 = document.getElementById("lastName1").value;
    var lastName2 = document.getElementById("lastName2").value;
    var curp = document.getElementById("curp").value;
    var rfc = document.getElementById("rfc").value;
    var clave = document.getElementById("clave").value;

    if (name.trim() == "" || lastName1.trim() == "" || lastName2.trim() == "" || curp.trim() == "" || rfc.trim() == "" || clave.trim() == "") {
      alert("Por favor, completa todos los campos.");
      return false;
    }

    // Validación de CURP
    var curpPattern = /^[A-Z]{4}\d{6}[HM][A-Z]{2}[A-Z]{3}[A-Z0-9][0-9]$/;
    if (!curpPattern.test(curp)) {
      alert("CURP inválida. Por favor, introduce una CURP válida.");
      return false;
    }

    // Validación de RFC
    var rfcPattern = /^[A-Z]{4}\d{6}[A-Z0-9]{3}$/;
    if (!rfcPattern.test(rfc)) {
      alert("RFC inválido. Por favor, introduce un RFC válido.");
      return false;
    }

    return true;
  }
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
