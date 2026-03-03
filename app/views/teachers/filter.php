<?php require APPROOT . '/views/inc/header.php'; ?>
<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>CURP</th>
            <th>Clave</th>
            <th>RFC</th>
            <th>
                <form action="<?php echo URLROOT; ?>/teachers/filter" method="POST" style="display: inline;">
                    <input type="text" name="filter" class="" value="<?php echo (isset($_POST['filter']) ? $_POST['filter'] : ''); ?>">
                    <input type="submit" class="" value="Filtrar">
                </form>
            </th>
            <th>
                <a href="<?php echo URLROOT; ?>/teachers/add" class="btn btn-success btn-block">Nuevo</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['filteredTeachers'] as $teacher) : ?>
            <tr>
                <td><?php echo $teacher->name; ?></td>
                <td><?php echo $teacher->lastName1; ?></td>
                <td><?php echo $teacher->lastName2; ?></td>
                <td><?php echo $teacher->curp; ?></td>
                <td><?php echo $teacher->clave; ?></td>
                <td><?php echo $teacher->rfc; ?></td>
                <td>
                    <a href="<?php echo URLROOT . '/teachers/edit/' . $teacher->id; ?>" class="btn btn-success btn-block">Editar</a>
                    <form action="<?php echo URLROOT . '/teachers/delete/' . $teacher->id; ?>" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este docente?');">
                        <input type="submit" class="btn btn-danger btn-block" value="Eliminar">
                    </form>
                </td>
            </tr>

        <?php endforeach; ?>
    </tbody>
</table>

<?php require APPROOT . '/views/inc/footer.php'; ?>