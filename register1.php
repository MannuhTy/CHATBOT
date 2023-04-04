<?php

session_start();
include('db.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Validate input fields
    if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
        header('Location: register.php?message=Please fill all the fields!');
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: register.php?message=Invalid email address!');
        exit();
    } elseif ($password != $cpassword) {
        header('Location: register.php?message=Password fields do not match!');
        exit();
    } elseif ($_FILES["dp"]["error"] == UPLOAD_ERR_NO_FILE) {
        header('Location: register.php?message=Please upload a profile picture!');
        exit();
    } elseif ($_FILES["dp"]["error"] != UPLOAD_ERR_OK) {
        header('Location: register.php?message=There was an error uploading your file!');
        exit();
    } elseif ($_FILES["dp"]["size"] > 500000) {
        header('Location: register.php?message=Sorry, your file is too large!');
        exit();
    } elseif (exif_imagetype($_FILES["dp"]["tmp_name"]) === false) {
        header('Location: register.php?message=Sorry, only JPG, JPEG, PNG & GIF files are allowed!');
        exit();
    }

    // Hash the password and generate a salt
    $salt = uniqid();
    $newPassword = md5(md5($password) . $salt);

    // Upload the profile picture to the server
    $targetDir = "dp/";
    $targetFile = $targetDir . basename($_FILES["dp"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $newTargetFile = $targetDir . $newFileName;
    if (!move_uploaded_file($_FILES["dp"]["tmp_name"], $newTargetFile)) {
        header('Location: register.php?message=There was an error uploading your file!');
        exit();
    }

    // Check if user already exists
    $checkUser = "SELECT * FROM users WHERE email = '$email'";
    $checkUserStatus = mysqli_query($conn, $checkUser) or die(mysqli_error($conn));
    if (mysqli_num_rows($checkUserStatus) > 0) {
        header('Location: register.php?message=You have already registered!');
        exit();
    }
        } else {

            if($password == $cpassword) { 
            
                $image = basename($_FILES["dp"]["name"]);
                $insertUser = "INSERT INTO users(name,email,password,dp,salt) VALUES('$name','$email','$newPassword','$image','$salt')";
                $insertUserStatus = mysqli_query($conn,$insertUser) or die(mysqli_error($conn));
    
                if($insertUserStatus) { 
      
                    header('Location: inbox.php?message=You have registered successfully! Please Login');
    
                }  else { 
    
                    header('Location: register_U.php?message=Unable to register!');
    
                }
    
            } else { 
    
                header('Location: register_U.php?message=Password fields do not match!');
    
            }

        }


       // else { 

       // header('Location:register_U.php?message=Please fill the fields properly!');  

   // }
?>