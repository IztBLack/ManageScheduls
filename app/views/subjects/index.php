<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-5">
    <h1>Lista de Asignaturas</h1>
    <div class="asignaturas-container">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Materia</th>
                        <th colspan="2">
                            <div class="d-flex justify-content-between align-items-center">
                                <form action="<?php echo URLROOT; ?>/subjects/filter" method="POST" class="form-inline">
                                    <input type="text" name="filter" class="form-control mr-2" value="<?php echo (isset($_POST['filter']) ? $_POST['filter'] : ''); ?>" placeholder="Filtrar">
                                </form>
                                <a href="<?php echo URLROOT; ?>/subjects/add" class="btn btn-success btn-custom">Nuevo</a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['subjects'] as $subject) : ?>
                        <tr>
                            <td><?php echo $subject->name; ?></td>
                            <td><?php echo $subject->subject_name; ?></td>
                            <td class="text-center">
                                <a href="<?php echo URLROOT . '/subjects/edit/' . $subject->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                            </td>
                            <td class="text-center">
                                <form action="<?php echo URLROOT . '/subjects/delete/' . $subject->id; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta asignatura?');">
                                    <input type="submit" class="btn btn-danger btn-sm" value="Eliminar">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
