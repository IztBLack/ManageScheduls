<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Editar Materia</h2>
            <form action="<?php echo URLROOT . '/subjects/edit/' . $data['id']; ?>" method="post" class="mt-4">
                <div class="form-group">
                    <label>Nombre:</label>
                    <input type="text" name="name" class="form-control form-control-lg" id="name" value="<?php echo $data['name']; ?>">
                </div>
                <div class="form-group">
                    <label>Materia:</label>
                    <input type="text" name="subject" class="form-control form-control-lg" id="subject" value="<?php echo $data['subject']; ?>">
                </div>
                <div>
                    <label>Profesores:</label>
                    <?php foreach ($data['teachers'] as $teacher) : ?>
                        <div>
                            <input type="checkbox" name="teachers[]" value="<?php echo $teacher->id; ?>" <?php echo in_array($teacher->id, $data['selectedTeachers']) ? 'checked' : ''; ?>>
                            <?php echo $teacher->name . ' ' . $teacher->lastName1 . ' ' . $teacher->lastName2; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="submit" class="btn btn-success btn-block" value="Guardar Cambios">
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>