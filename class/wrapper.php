<?php 

require 'app.php';

class AppWrapper {
    private $app;

    public function __construct() {
        $this->app = new App();
    }
    
    public function getDashboard() {
        return $this->app->Dashboard();
    }

    public function getSignIn() {
        return $this->app->SignIn();
    }

    
}























?>