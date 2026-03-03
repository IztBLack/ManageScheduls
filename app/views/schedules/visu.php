<?php require APPROOT . '/views/inc/header.php'; ?>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        text-align: left;
        padding: 8px;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }
</style>
<h1>Visualizar Horario</h1>

<?php if (isset($horario['id'])) : ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Periodo Escolar</th>
            <th>Turno</th>
            <th>Tutor</th>
            <th>Grupo</th>
            <th>Especialidad</th>
            <th>Nivel</th>
            <th>Salón</th>
        </tr>
        <tr>
            <td><?php echo $horario['id']; ?></td>
            <td><?php echo $horario['periodo_escolar']; ?></td>
            <td><?php echo $horario['turno']; ?></td>
            <td><?php echo $horario['tutor']; ?></td>
            <td><?php echo $horario['grupo']; ?></td>
            <td><?php echo $horario['especialidad']; ?></td>
            <td><?php echo $horario['nivel']; ?></td>
            <td><?php echo $horario['salon']; ?></td>
        </tr>
    </table>

    <h2>Horas</h2>
    <table>
        <tr>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Lunes</th>
            <th>Martes</th>
            <th>Miércoles</th>
            <th>Jueves</th>
            <th>Viernes</th>
        </tr>
        <?php foreach ($horario['horas'] as $hora) : ?>
            <tr>
                <td><?php echo $hora['hora_inicio']; ?></td>
                <td><?php echo $hora['hora_fin']; ?></td>
                <td><?php echo $hora['lunes']; ?></td>
                <td><?php echo $hora['martes']; ?></td>
                <td><?php echo $hora['miercoles']; ?></td>
                <td><?php echo $hora['jueves']; ?></td>
                <td><?php echo $hora['viernes']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Asignaturas</h2>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Docente</th>
        </tr>
        <?php foreach ($horario['asignaturas'] as $asignaturas) : ?>
            <tr>
                <td><?php echo $asignaturas['nombre']; ?></td>
                <td><?php echo $asignaturas['docente']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else : ?>
    <p>No se encontró el horario con el ID proporcionado.</p>
<?php endif; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>
