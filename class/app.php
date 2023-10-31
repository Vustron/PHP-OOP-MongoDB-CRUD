<?php 

require '../vendor/autoload.php';
require '../class/model.php';

class App {
    
// Dashboard--------------------------------------------------
    public function Dashboard() {

        $collections = new Collections();
        $users = $collections->getUserCollection();
        $users = $collections->getUsers();
        
        ob_start();
?>
<section class="hidden">
    <div class="container m-5">
        <table class="table table-dark table-striped table-hover table-bordered border-primary">
            <thead>
                <tr style="text-align: center;">
                    <th scope="col">ID</th>
                    <th scope="col">Profile Picture</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Password</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <?php if (empty($users)) { // Check if $users is empty
            echo '<tr><td colspan="7" class="text-center">No users</td></tr>';
        } else {
            foreach ($users as $user): ?>
                <tr>
                    <th scope="row"><?php echo $user['_id']; ?></th>
                    <th scope="row" class="figure-img img-fluid rounded" style="text-align: center;">
                        <?php
                    // Check if the user has a profile picture
                    if (isset($user['ProfilePicture']) && $user['ProfilePicture'] instanceof MongoDB\BSON\Binary) {
                        // Get the image data and convert it to base64
                        $imageData = base64_encode($user['ProfilePicture']->getData());
                        echo '<img class="img-thumbnail rounded-circle" src="data:image/png;base64,' . $imageData . '" alt="Profile Picture" style="width: 100px; margin: 0 auto; height: 100px;">';
                    } else {
                        echo '<img class="img-thumbnail rounded-circle" src="" alt="Profile Picture" style="width: 100px; margin: 0 auto;">'; // Provide a default image
                    }
                    ?>
                    </th>
                    <td scope="row"><?php echo $user['FirstName']; ?></td>
                    <td scope="row"><?php echo $user['LastName']; ?></td>
                    <td scope="row"><?php echo $user['Email']; ?></td>
                    <td scope="row"><?php echo $user['Password']; ?></td>
                    <td class="p-2 m-auto justify-content-center">
                        <button class="btn btn-warning text-white editUser">Edit</button>
                        <button class="btn btn-danger eraseUser">Erase</button>
                    </td>
                </tr>
                <?php endforeach;
        } ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Update Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background: transparent; border: none;">
            <div class="modal-body">
                <div class="container border rounded bg-warning">
                    <div class="row">
                        <div class="col p-2 m-3">
                            <div class="row justify-content-center">
                                <!-- Center the row -->
                                <div class="col">
                                    <h2 class="text-white text-center">Update</h2>
                                    <form name="update-form" method="POST" id="update-form"
                                        enctype="multipart/form-data">

                                        <input type="hidden" name="UserId" value="<?php echo $userId ?>">
                                        <div class="form-group mt-2 mb-2">
                                            <input type="text" class="form-control" placeholder="First Name"
                                                name="FirstName" id="FirstName" value="<?php echo $firstName ?>"
                                                required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="text" class="form-control" placeholder="Last Name"
                                                name="LastName" id="LastName" value="<?php echo $lastName ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="email" class="form-control" placeholder="Email" name="Email"
                                                id="Email" value="<?php echo $email ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <input type="password" class="form-control" placeholder="Password"
                                                name="Password" id="Password" value="<?php echo $password ?>" required>
                                        </div>
                                        <div class="form-group mb-2 text-center figure-img img-fluid rounded">
                                            <label for="ProfilePicture" class="text-white"><strong>Update Profile
                                                    Picture</strong></label>
                                            <img id="image-preview" src="" style="display: none"
                                                class="img-fluid rounded">
                                            <input type="file" class="form-control-file" name="ProfilePicture"
                                                id="ProfilePicture" accept="image/*">
                                        </div>
                                        <button class="btn btn-success mt-2" name="update" id="update"
                                            type="submit">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Update User -->
<script>
$(document).ready(function() {
    // Attach a click event handler to the "Edit" button
    $(".editUser").click(function() {
        // Find the parent <tr> element to get user data
        var $row = $(this).closest('tr');
        var userId = $row.find('th').text().trim();
        var firstName = $row.find('td:eq(0)').text();
        var lastName = $row.find('td:eq(1)').text();
        var email = $row.find('td:eq(2)').text();
        var password = $row.find('td:eq(3)').text();

        // Set the values of the form fields in the modal
        $("#update-form input[name='UserId']").val(userId);
        $("#update-form input[name='FirstName']").val(firstName);
        $("#update-form input[name='LastName']").val(lastName);
        $("#update-form input[name='Email']").val(email);
        $("#update-form input[name='Password']").val(password);

        // Show the Bootstrap modal
        $('#editUserModal').modal('show');
    });

    // Clear the input field when the page loads
    window.addEventListener('load', function() {
        var input = document.getElementById('ProfilePicture');
        input.value = ''; // Clear the input value
    });
});

$(document).ready(function() {
    $("#update-form").submit(function(event) {
        event.preventDefault();

        var userId = $("#update-form input[name='UserId']").val().trim();

        // Check if the user ID is in a valid format
        if (userId.length === 24 && /^[0-9a-fA-F]+$/.test(userId)) {

            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: "../class/controller.php?action=update",
                data: formData,
                contentType: false,
                processData: false, // Prevent jQuery from processing the data
                success: function(response) {
                    // Handle the response from the server
                    handleResponse(response);
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    console.error("AJAX error: " + error);
                    Swal.fire({
                        icon: 'error',
                        title: 'AJAX Error',
                        text: 'An error occurred during the request.',
                    });
                }
            });
        } else {
            // Invalid user ID format
            Swal.fire({
                icon: 'error',
                title: 'Invalid User ID',
                text: 'The User ID format is invalid.',
            });
        }
    });

    function handleResponse(response) {
        try {
            var data = JSON.parse(response);

            if (data.success) {
                // Success handling here (e.g., display a success message)
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timerProgressBar: true,
                }).then(() => {
                    location.reload();
                });
            } else {
                // Error handling here (e.g., display an error message)
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    timerProgressBar: true,
                });
            }
        } catch (error) {
            console.error("Invalid response format.");
            Swal.fire({
                icon: 'error',
                title: 'Response Error',
                text: 'An error occurred while processing the response.',
            });
        }
    }
});
</script>

<!-- Delete User -->
<script>
$(document).ready(function() {
    // Attach a click event handler to the "Erase" button
    $(".eraseUser").click(function() {
        var $row = $(this).closest('tr');
        var userId = $row.find('th').text();

        // Show a confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this user!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                // Convert the user ID to a valid ObjectId string
                userId = userId.trim(); // Remove any leading/trailing whitespace
                if (userId.length === 24 && /^[0-9a-fA-F]+$/.test(userId)) {
                    // Valid ObjectId format
                    $.ajax({
                        type: "POST",
                        url: "../class/controller.php?action=delete",
                        data: {
                            UserId: userId
                        },
                        success: function(response) {
                            // Handle the response from the server
                            handleResponse(response);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid User ID',
                        text: 'The User ID format is invalid.',
                    });
                }
            }
        });
    });

    function handleResponse(response) {
        try {
            var data = JSON.parse(response);

            if (data.success) {
                // Success handling here (e.g., display a success message)
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: data.message,
                }).then(() => {
                    location.reload();
                });
            } else {
                // Error handling here (e.g., display an error message)
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                });
            }
        } catch (error) {
            console.error(error);
        }
    }
});
</script>


<?php
        return ob_get_clean();
    }
    
// LoginForm-----------------------------------------------------
    public function SignIn() {
        ob_start();
?>
<section class="hidden">
    <div class="container border rounded bg-secondary m-5 p-4">
        <h1 class="text-center text-white"><b>PHP-OOP-MongoDB-CRUD</b></h1>
        <hr>
        <div class="row p-4">
            <div class="col p-2 m-3">
                <div class="row justify-content-center">
                    <!-- Center the row -->
                    <div class="col-md-6">
                        <h2 class="text-white text-center">Register</h2>
                        <form name="register" method="POST" id="signup-form" enctype="multipart/form-data">
                            <div class="form-group mt-2 mb-2">
                                <input type="text" class="form-control" placeholder="First Name" name="FirstName"
                                    id="FirstName" required>
                            </div>
                            <div class="form-group mb-2">
                                <input type="text" class="form-control" placeholder="Last Name" name="LastName"
                                    id="LastName" required>
                            </div>
                            <div class="form-group mb-2">
                                <input type="email" class="form-control" placeholder="Email" name="Email" id="Email"
                                    required>
                            </div>
                            <div class="form-group mb-2">
                                <input type="password" class="form-control" placeholder="Password" name="Password"
                                    id="Password" required>
                            </div>
                            <div class="form-group mb-2 text-center figure-img img-fluid rounded">
                                <label for="ProfilePicture" class="text-white">
                                    <strong>
                                        Add Profile Picture
                                    </strong>
                                </label>
                                <input type="file" class="form-control-file mt-2" name="ProfilePicture"
                                    id="ProfilePicture" accept="image/*">
                                <img id="image-preview" src="" alt="Preview"
                                    style="max-width: 100px; max-height: 100px; display: none; margin: 0 auto;">
                            </div>
                            <div class="form-group mb-2 text-center">
                                <button class="btn btn-success w-50" name="signup" id="signup"
                                    type="submit">Signup</button>
                                <a type="button" href="../../Home/index.php" class="btn btn-primary w-50 m-2">Home</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Clear the input field when the page loads
window.addEventListener('load', function() {
    var input = document.getElementById('ProfilePicture');
    input.value = ''; // Clear the input value
});

// Function to display the preview image
document.getElementById('ProfilePicture').addEventListener('change', function(e) {
    var preview = document.getElementById('image-preview');
    if (e.target.files.length > 0) {
        preview.style.display = 'block';
        preview.src = URL.createObjectURL(e.target.files[0]);
    } else {
        preview.style.display = 'none';
        preview.src = '';
    }
});
</script>

<script>
$(document).ready(function() {
    // Function to display the preview image
    $("#ProfilePicture").change(function() {
        var fileInput = $(this)[0];
        var preview = $("#image-preview")[0];

        if (fileInput.files.length > 0) {
            var file = fileInput.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.style.display = 'block';
                preview.src = e.target.result;
            };

            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            preview.src = '';
        }
    });

    $("#signup-form").submit(function(event) {
        event.preventDefault(); // Prevent the default form submission

        var formData = new FormData(this);
        console.log(formData);

        $.ajax({
            type: "POST",
            url: "../class/controller.php?action=register", // Set your PHP endpoint
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Handle the response from the server
                handleResponse(response);
            }
        });
    });

    function handleResponse(response) {
        try {
            var data = JSON.parse(response);

            if (data.success) {
                // Success handling here (e.g., display a success message)
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timerProgressBar: true,
                }).then(() => {
                    location.reload();
                });
            } else {
                // Error handling here (e.g., display an error message)
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    timerProgressBar: true,
                });
            }
        } catch (error) {
            console.error("Invalid response format.");
        }
    }
});
</script>

<?php
        return ob_get_clean();
    }
// ------------------------------------------------------------=


    
}





















?>