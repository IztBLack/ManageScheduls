<?php
  class Pages extends Controller{
    public function __construct(){
    
    }

    // Load Homepage
    public function index(){
      // If logged in, redirect to posts
      if(isset($_SESSION['user_id'])){
        $this->view('pages/index');

      }

      //Set Data
      $data = [
        'title' => 'Welcome To School control',
        'description' => 'Simple social network built on the TraversyMVC PHP framework'
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