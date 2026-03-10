<?php
  class Pages extends Controller{
    public function __construct(){
    
    }

    // Load Homepage
    public function index(){
      // If logged in, load dashboard metrics instead of static welcome
      if(isset($_SESSION['user_id'])){
        $data = [
            'title' => 'Bienvenido, ' . $_SESSION['user_name'],
            'description' => 'Este es tu panel de control de acceso rápido.'
        ];
        
        // Si quisieras inyectar conteos, aquí invocarías a los modelos (ej: Schedule, User)
        // Por ahora lo dejaremos visual a nivel UI en la vista.
        
        $this->view('pages/index', $data);
        return; // Salir para no ejecutar lo de abajo
      }

      //Set Data for guests
      $data = [
        'title' => 'School Control',
        'description' => 'El sistema integral para organizar tus clases, pase de lista y esquema de evaluación de manera profesional.'
      ];

      // Load homepage/index view
      $this->view('pages/index', $data);
    }

    // public function about(){
    //   //Set Data
    //   $data = [
    //     'version' => '1.0.0'
    //   ];

    //   // Load about view
    //   $this->view('pages/about', $data);
    // }
  }