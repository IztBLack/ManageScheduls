<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h1>Lista de Docentes</h1>
            <div class="docentes-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido Paterno</th>
                                <th>Apellido Materno</th>
                                <th>Curp</th>
                                <th>Clave</th>
                                <th>RFC</th>
                                <th colspan="2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form action="<?php echo URLROOT; ?>/teachers/filter" method="POST" class="form-inline">
                                            <input type="text" name="filter" class="form-control mr-2 btn-custom" value="<?php echo (isset($_POST['filter']) ? $_POST['filter'] : ''); ?>" placeholder="Filtrar">
                                        </form>
                                        <a href="<?php echo URLROOT; ?>/teachers/add" class="btn btn-success btn-custom">Nuevo</a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['teachers'] as $teacher) : ?>
                                <tr>
                                    <td><?php echo $teacher->name; ?></td>
                                    <td><?php echo $teacher->lastName1; ?></td>
                                    <td><?php echo $teacher->lastName2; ?></td>
                                    <td><?php echo $teacher->curp; ?></td>
                                    <td><?php echo $teacher->clave; ?></td>
                                    <td><?php echo $teacher->rfc; ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo URLROOT . '/teachers/edit/' . $teacher->id; ?>" class="btn btn-warning btn-sm">Editar</a>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?php echo URLROOT . '/teachers/delete/' . $teacher->id; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este docente?');">
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
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>
