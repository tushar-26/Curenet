<?php
session_start();
include "includes/functions.php";
singUp();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" href="images/favicon.png">
    <link rel="stylesheet" href="css/register.css">
    <title>Sign Up</title>

</head>

<body>
    <div class="wrapper">
        <nav class="nav">
            <div class="nav-logo">
                <img style="width: 120px;" src="images/favicon.png" alt="logo">
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#" class="link active">Home</a></li>
                    <li><a href="#" class="link">About</a></li>
                    <li><a href="members.html" class="link">Members</a></li>
                    <li><a href="#" class="link">Customers</a></li>
                </ul>
            </div>
            <div class="nav-btns">

                <a href="login.php"><button class="l-btn" id="loginBtn">Sign In</button></a>
                <button class="s-btn" id="registerBtn">Sign Up</button>

            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>

        <div class="form-box">
            <?php

            message();
            ?>

            <form class="register-container" id="register" action="signUp.php" method="post">
                <div class="top">
                    <span>Already have an account? <a href="login.php">Login</a></span>
                    <header>Sign Up to Curenet</header>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" placeholder="Firstname" name="Fname">
                        <i class="bx bx-user"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" placeholder="Lastname" name="Lname">
                        <i class="bx bx-user"></i>
                    </div>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" placeholder="Email" name="email">
                    <i class="bx bx-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" placeholder="Password" name="passwd">
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" placeholder="Address..." name="address">
                    <i class="bx bx-lock-alt"></i>
                </div>
                <div class="input-box">

                    <input type="submit" class="submit" value="Register" name="singUp">

                </div>
            </form>
        </div>
    </div>
    <script>
        function myMenuFunction() {
            var i = document.getElementById("navMenu");

            if (i.className === "nav-menu") {
                i.className += " responsive";
            } else {
                i.className = "nav-menu";
            }
        }
    </script>
</body>

</html>