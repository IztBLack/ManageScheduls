<?php require APPROOT . '/views/inc/header.php'; ?>
<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Materia</th>
            <th>
                <form action="<?php echo URLROOT; ?>/subjects/filter" method="POST" style="display: inline;">
                    <input type="text" name="filter" class="" value="<?php echo (isset($_POST['filter']) ? $_POST['filter'] : ''); ?>">
                    <input type="submit" class="" value="Filtrar">
                </form>
            </th>
            <th>
                <a href="<?php echo URLROOT; ?>/subjects/add" class="btn btn-success btn-block">Nuevo</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data['subjects'] as $subject) : ?>
            <tr>
                <td><?php echo $subject->name; ?></td>
                <td><?php echo $subject->subject_name; ?></td>
                <td>
                    <a href="<?php echo URLROOT . '/subjects/edit/' . $subject->id; ?>" class="btn btn-success btn-block">Editar</a>
                </td>
                <td>
                    <form action="<?php echo URLROOT . '/subjects/delete/' . $subject->id; ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta asignatura?');">
                        <input type="submit" class="btn btn-danger btn-block" value="Eliminar">
                    </form>
                </td>
            </tr>

        <?php endforeach; ?>
    </tbody>
</table>

<?php require APPROOT . '/views/inc/footer.php'; ?>