<?php require APPROOT . '/views/inc/header.php'; ?>
<h2>Perfil</h2>
<?php if (!empty($_SESSION['user_id'])): ?>
   <ul>
        <li><strong>Nombre:</strong> <?php echo $_SESSION['user_name']; ?></li>
        <li><strong>Correo electrónico:</strong> <?php echo $_SESSION['user_email']; ?></li>
    </ul>
<?php else: ?>
    <p>No se encontraron datos de sesión.</p>
<?php endif; ?>

<?php require APPROOT . '/views/inc/footer.php'; ?>
