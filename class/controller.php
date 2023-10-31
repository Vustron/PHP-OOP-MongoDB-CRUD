<?php 

require 'model.php';

$collections = new Collections();
$userCollection = $collections->getUserCollection();

$controller = new Controller($userCollection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Call the handleAction method to handle the 'register' action
    $controller->handleAction();
}

class Controller {
    private $userCollection; 

    public function __construct($userCollection) {
        $this->userCollection = $userCollection;
    }

    public function handleAction() {
        // Switch action handler
        $functionToCall = isset($_GET['action']) ? $_GET['action'] : '';

        switch ($functionToCall) {
            case 'register':
                $this->handleSignup(); 
                break;
            case 'update':
                $this->handleUpdate();
                break;
            case 'delete':
                $userId = isset($_POST['UserId']) ? $_POST['UserId'] : '';
                $this->handleDelete($userId);
                break;
            default:
                // Handle invalid action
                echo "Invalid action";
                break;
        }
    }

    public function handleSignup() {
        try {
            // Get form data from POST request
            $first_name = $_POST['FirstName'];
            $last_name = $_POST['LastName'];
            $email = $_POST['Email'];
            $password = $_POST['Password'];
            $created_at = date('Y-m-d H:i:s');

            // Handle image upload
            if ($_FILES['ProfilePicture']['name']) {
                $uploadDirectory = '../uploads/';
                // Generate a unique filename using the user's _id
                $user_id = new MongoDB\BSON\ObjectId();
                $uploadedFile = $uploadDirectory . $user_id . '_' . basename($_FILES['ProfilePicture']['name']);
                if (move_uploaded_file($_FILES['ProfilePicture']['tmp_name'], $uploadedFile)) {
                    // Image uploaded successfully

                    // Convert the image to binary data
                    $imageData = new MongoDB\BSON\Binary(file_get_contents($uploadedFile), MongoDB\BSON\Binary::TYPE_GENERIC);

                    // Create a document to insert into MongoDB
                    $create = [
                        '_id' => $user_id,  // Assign the unique _id to the document
                        'FirstName' => $first_name,
                        'LastName' => $last_name,
                        'Email' => $email,
                        'Password' => $password,
                        'ProfilePicture' => $imageData,
                        'CreatedAt' => $created_at
                    ];

                    // Insert the document into the MongoDB collection
                    $result = $this->userCollection->insertOne($create);

                    if ($result->getInsertedCount() > 0) {
                        echo json_encode(['success' => true, 'message' => 'Signup successful!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Signup failed.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Profile picture is required.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function handleUpdate() {
        try {
            // Get user information to update from POST request
            $user_id = $_POST['UserId'];
            $first_name = $_POST['FirstName'];
            $last_name = $_POST['LastName'];
            $email = $_POST['Email'];
            $password = $_POST['Password'];

            // Convert the user_id string to a MongoDB ObjectId
            $user_id = new MongoDB\BSON\ObjectId($user_id);

            // Create a filter to identify the user to update
            $filter = ['_id' => $user_id];

            // Check if a new profile picture is uploaded via AJAX
            if (isset($_FILES['ProfilePicture']['name']) && $_FILES['ProfilePicture']['name']) {
                // Handle image upload for the new profile picture
                $uploadDirectory = '../uploads/';
                $uploadedFile = $uploadDirectory . $user_id . '_' . basename($_FILES['ProfilePicture']['name']);
                if (move_uploaded_file($_FILES['ProfilePicture']['tmp_name'], $uploadedFile)) {
                    // Image uploaded successfully

                    // Convert the image to binary data
                    $imageData = new MongoDB\BSON\Binary(file_get_contents($uploadedFile), MongoDB\BSON\Binary::TYPE_GENERIC);
                    $updatedAt = date('Y-m-d H:i:s');

                    // Remove the existing ProfilePicture field
                    $unset = ['$unset' => ['ProfilePicture' => '']];
                    $this->userCollection->updateOne($filter, $unset);

                    // Update other user information including the new profile picture
                    $update = [
                        '$set' => [
                            'FirstName' => $first_name,
                            'LastName' => $last_name,
                            'Email' => $email,
                            'Password' => $password,
                            'ProfilePicture' => $imageData,
                            'UpdateAt' => $updatedAt
                        ]
                    ];
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload the new profile picture']);
                    return;
                }
            } else {
                // No new profile picture uploaded via AJAX, update other user information only
                $updatedAt = date('Y-m-d H:i:s');
                $update = [
                    '$set' => [
                        'FirstName' => $first_name,
                        'LastName' => $last_name,
                        'Email' => $email,
                        'Password' => $password,
                        'UpdateAt' => $updatedAt
                    ]
                ];
            }

            // Update the document in the MongoDB collection
            $result = $this->userCollection->updateOne($filter, $update);

            // Send a JSON response
            if ($result->getModifiedCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'User info updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'User info update failed']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function handleDelete($userId) {
        try {
            // Convert the user ID to MongoDB ObjectId
            $objectId = new MongoDB\BSON\ObjectId($userId);

            // Delete the user based on their _id field
            $result = $this->userCollection->deleteOne(['_id' => $objectId]);

            if ($result->getDeletedCount() > 0) {
                // User deletion was successful
                $response = ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                // No user found with the provided ID
                $response = ['success' => false, 'message' => 'User not found or deletion failed'];
            }
            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }   
    }   
}


?>