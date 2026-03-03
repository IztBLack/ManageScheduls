<?php


class Profile extends Controller
{
    private $profileModel;

    public function __construct() {
        $this->profileModel = $this->model('User');
    }

    public function index()
    {
        $this->view("profile/index");
    }
}

?>