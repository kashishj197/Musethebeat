<?php
    include("includes/config.php");
    include("includes/classes/Account.php");
    include("includes/classes/constants.php");
    $account = new Account($con);
    include("includes/handlers/register-handler.php");
    include("includes/handlers/login-handler.php");
    
    function getInputValue($name){
        if(isset($_POST[$name])){
            echo $_POST[$name];
        }
    }
?>
<html>
<head>
    <title>Register yourself or Try logging in</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>
<body>
    <?php
        if(isset($_POST['registerButton'])){
            echo '<script>$(document).ready(function() {
    	    	$("#loginForm").hide();
    	    	$("#registerForm").show();
    	        });</script>';
        }
        else{
            echo '<script>$(document).ready(function() {
    	    	$("#loginForm").show();
    	    	$("#registerForm").hide();
    	        });</script>';
        }
    ?>
    <div id = "background">
        <div id = "loginContainer">
            <div id = "inputContainer">
                <form id="loginForm" action="register.php" method="POST">
                    <h2>Login to your account</h2>
                    
                    <p>
                    <?php echo $account->getError(constants::$loginFailed); ?>
                    <label for="loginUsername">Username</label>
                    <input id="loginUsername" name="loginUsername" type="text" placeholder="e.g killerBee" value = "<?php getInputValue('loginUsername')?>" required>
                    </p>
                    
                    <p>
                    <label for="loginPassword">Password</label>
                    <input id="loginPassword" name="loginPassword" type="Password" required>
                    </p>
                    
                    <button type="submit" name="loginButton">LOG IN</button>
                    <div class="hasAccountText">
                        <span id="hideLogin">Don't have an account? SignUp here.</span>
                    </div>
                </form>
                <form id="registerForm" action="register.php" method="POST">
                    <h2>Create your new account</h2>
                    
                    <p>
                    <?php echo $account->getError(constants::$emailDoNotMatch);?>
                    <?php echo $account->getError(constants::$emailInvalid);?>
                    <?php echo $account->getError(constants::$emailTaken);?>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="text" placeholder="e.g something@gmail.com" value = "<?php getInputValue('email')?>" required>
                    </p>
                    
                    <p>
                    <label for="email2">Confirm Email</label>
                    <input id="email2" name="email2" type="email" value = "<?php getInputValue('email2')?>" required>
                    </p>
                    
                    <p>
                    <?php echo $account->getError(constants::$usernameLimit);?>
                    <?php echo $account->getError(constants::$usernameTaken);?>
                    <label for="username">User Name</label>
                    <input id="username" name="username" type="text" placeholder="What should we call you?" value = "<?php getInputValue('username')?>" required>
                    </p>
                    
                    <p>
                    <?php echo $account->getError(constants::$passwordDoNotMatch);?>
                    <?php echo $account->getError(constants::$passwordAlphaNumeric);?>
                    <?php echo $account->getError(constants::$passwordLimit);?>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="Password" required>
                    </p>
            
                    <p>
                    <label for="password2">Confirm Password</label>
                    <input id="password2" name="password2" type="Password" required>
                    </p>
                    
                    <button type="submit" name="registerButton">SIGN UP</button>
                    <div class="hasAccountText">
                        <span id="hideRegister">Already have account? LogIn here.</span>
                    </div>
                </form>
            </div>
            <div id="loginText">
                <h1>Get great music, Right now</h1>
                <h2>Listen to loads of songs for free</h2>
                <ul>
                    <li>Discover music you will fall in love with</li>
                    <li>Create your own playlists</li>
                    <li>Follow artists to keep up to date</li>
                </ul>
            </div>
        </div>
    </div>
</body>    
</html>