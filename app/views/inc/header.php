<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/app.css">
  <title><?php echo SITENAME; ?></title>
  <script>
      // Global JS Constants
      const URLROOT = '<?php echo URLROOT; ?>';
  </script>
</head>
<body>
  <?php require APPROOT . '/views/inc/navbar.php'; ?>
  <div class="container"> 

<?php if (isset($_SESSION['register_success'])) : ?>
  <div class="alert alert-success" role="alert">
    <?php echo $_SESSION['register_success']; ?>
  </div>
  <?php unset($_SESSION['register_success']); ?>
<?php endif; ?>