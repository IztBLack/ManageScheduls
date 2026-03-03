<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="jumbotron jumbotron-fluid">

  <?php if (isset($_SESSION['is_logged_in'])) : ?>
    <div class="container">
    <h1 lass="display-3"><?php echo SITENAME; ?></h1>

    </div>
  <?php else : ?>
    <div class="container">
      <h1 class="display-3"><?php echo $data['title']; ?></h1>
    </div>
  <?php endif; ?>

  <?php require APPROOT . '/views/inc/footer.php'; ?>