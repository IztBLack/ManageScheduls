<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Editar Docente</h2>
            <?php if (isset($data['teacher'])) : ?>
                <?php
                // Construir la URL completa para el atributo action
                $action = URLROOT . '/teachers/edit/' . $data['id'];
                ?>
                <form action="<?php echo $action; ?>" method="post" class="mt-4" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="name" class="form-control form-control-lg" id="name" value="<?php echo $data['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Primer Apellido:</label>
                        <input type="text" name="lastName1" class="form-control form-control-lg" id="lastName1" value="<?php echo $data['lastName1']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Segundo Apellido:</label>
                        <input type="text" name="lastName2" class="form-control form-control-lg" id="lastName2" value="<?php echo $data['lastName2']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>CURP:</label>
                        <input type="text" name="curp" class="form-control form-control-lg" id="curp" value="<?php echo $data['curp']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>RFC:</label>
                        <input type="text" name="rfc" class="form-control form-control-lg" id="rfc" value="<?php echo $data['rfc']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Clave:</label>
                        <input type="text" name="clave" class="form-control form-control-lg" id="clave" value="<?php echo $data['clave']; ?>" required>
                    </div>
                    <input type="submit" class="btn btn-success btn-block" value="Guardar Cambios">
                </form>
            <?php else : ?>
                <p>No se encontró el ID del docente.</p>
            <?php endif; ?>
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
        if (!curp.match(curpPattern)) {
            alert("El CURP no es válido.");
            return false;
        }

        // Validación de RFC
        var rfcPattern = /^[A-Z]{4}\d{6}[A-Z0-9]{3}$/;
        if (!rfc.match(rfcPattern)) {
            alert("El RFC no es válido.");
            return false;
        }

        return true;
    }
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>
