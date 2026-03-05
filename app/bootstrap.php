<?php

require_once 'libraries/Controller.php';

// Load Config
require_once 'config/config.php';
// Load Helpers
require_once 'helpers/session_helper.php';
require_once 'helpers/url_helper.php';


// Autoload Core Classes
spl_autoload_register(function ($className) {
    require_once 'libraries/' . $className . '.php';
});

?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

