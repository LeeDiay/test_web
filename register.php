<?php
    @include './inc/config.php';
    if (isset($_POST['submit'])){
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $pass = md5($_POST['password']);    
        $cpass = md5($_POST['cpassword']);
        $type = $_POST['type'];

        $select = "SELECT * FROM user WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $select); 

        if(mysqli_num_rows($result) > 0){
            $select = "SELECT * FROM user WHERE username = '$username'";
            $result = mysqli_query($conn, $select);
            if(mysqli_num_rows($result) > 0){
                $error[] = 'This username has already been used!';
            }
            else{
                $error[] = 'This email has already already been registered!';
            }
        }else{
            if ($pass != $cpass){
                $error[] = "Password doesn't match!";
            } else {
                // Upload ảnh avatar
                if(isset($_FILES['avatar'])){
                    $file_name = $_FILES['avatar']['name'];
                    $file_tmp = $_FILES['avatar']['tmp_name'];
                    $file_type = $_FILES['avatar']['type'];
                    $file_size = $_FILES['avatar']['size'];
                    $file_error = $_FILES['avatar']['error'];

                    if($file_error === 0){
                        if($file_size > 5242880){ // 5MB
                            $error[] = 'File size must be less than 5 MB';
                        } else {
                            $file_destination = './avatar_user/' . $file_name;
                            move_uploaded_file($file_tmp, $file_destination);
                            $avatar_path = $file_name;
                        }
                    } else {
                        $error[] = 'Error uploading file';
                    }
                }

                // Thêm thông tin người dùng vào cơ sở dữ liệu
                $insert = "INSERT INTO user (username, name, email, phone, password, type, avatar) VALUES ('$username','$name', '$email' , '$phone', '$pass','$type', '$avatar_path')";
                mysqli_query($conn, $insert);
                $success[] = "Successfully registered!";
            }
        }
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Register form</title>
</head>
<body>
    <section class="p-5">
        <div class="container">
            <div>
                <div class="d-flex col-md justify-content-center">
                    <div class="card bg-light text-dark" style="width: 50rem;">
                        <div class="card-body text-center">
                            <form class="text-start" method='POST' enctype="multipart/form-data">
                                <h3 class="text-center">Register form</h3>
                                <?php
                                    if (isset($error)){
                                        foreach($error as $error){
                                            echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        }
                                    }
                                    if (isset($success)){
                                        foreach($success as $success){
                                            echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
                                        }
                                    }
                                ?>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Enter Username:</label>
                                    <input type="text" name='username' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Enter Name:</label>
                                    <input type="text" name='name' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Enter email:</label>
                                    <input type="email" name='email' required class="form-control" id="email" aria-describedby="emailHelp">
                                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Enter Phone number:</label>
                                    <input type="text" name='phone' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Enter password:</label>
                                    <input type="password" name='password' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Re-enter password:</label>
                                    <input type="password" name='cpassword' required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Choose Avatar:</label>
                                    <input type="file" name="avatar" accept="image/*" class="form-control" id="avatar">
                                </div>
                                <label for="utype" class="form-label">Select user type:</label>
                                <select class="form-select" name='type'>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <div class="d-flex justify-content-between align-items-center" style="margin-top: 1rem;">
                                    <p style="margin-bottom: 0rem;">Already have an account? <a href="login.php">Sign in now</a></p>
                                    <button type="submit" name='submit' class="btn btn-primary">Sign up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>