<?php

function sanitizingUsername($inputString){
    $inputString = strip_tags($inputString);
    $inputString = ucfirst(strtolower($inputString));
    return $inputString;
}

function sanitizingString($inputString){
    $inputString = strip_tags($inputString);
    $inputString = strtolower($inputString);
    $inputString = str_replace(" ","",$inputString);
    return $inputString;
}

if(isset($_POST['registerButton'])){
    //if register button was pressed do something
    $username = sanitizingUsername($_POST['username']);
    $email = sanitizingString($_POST['email']);
    $email2 = sanitizingString($_POST['email2']);
    $password = sanitizingString($_POST['password']);
    $password2 = sanitizingString($_POST['password2']);
    $wasSuccessful = $account->register($username,$email,$email2,$password,$password2);
    
    if($wasSuccessful == true){
        $_SESSION['userLoggedIn'] = $username;
         header("Location: index.php"); 
    }
    
}

?>
