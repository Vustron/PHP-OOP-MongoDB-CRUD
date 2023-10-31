<?php 

require '../vendor/autoload.php';

class Model {
    public function DatabaseConnection() {
        // MongoDB connection settings
        $uri = 'mongodb://localhost:27017';
        $databaseName = 'phpDB';
        // Initialize MongoDB Client
        $client = new MongoDB\Client($uri);
        // Connect to MongoDB database
        $ConnectDB = $client->selectDatabase($databaseName);
        return $ConnectDB;
    }
}

class Collections {
    private $model;
    private $userCollection;

    public function __construct() {
        $this->model = new Model();
        $this->userCollection = $this->model->DatabaseConnection()->selectCollection('users');
    }

    public function getUserCollection() {
        return $this->userCollection;
    }

    public function getUsers() {
        return iterator_to_array($this->userCollection->find());
    }
}




























?>