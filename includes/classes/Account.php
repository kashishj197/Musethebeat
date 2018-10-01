<?php
    class Account{
        
        private $con;
        private $errorArray; 
        public function __construct($con){
            $this->con = $con;
            $this->errorArray = Array();
        }
        
         public function login($un, $pw) {

            $pw2 = md5($pw);
            echo $pw2;
            $query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$un' AND password='$pw2'");

            if(mysqli_num_rows($query)==1) {
                return true;
            }
            else {
                array_push($this->errorArray, constants::$loginFailed);
                return false;
            }

        }
        
        public function register($username,$email,$email2,$password,$password2){
            $this->validateuserName($username);
            $this->validatepassWord($password,$password2);
            $this->validateEmail($email,$email2);
            
            if(empty($this->errorArray) == true){
                return $this->insertUserDetails($username,$email,$password);
            }
            else{
                return false;
            }
        }
        
        private function insertUserDetails($un, $em, $pw) {
            $encryptedPw = md5($pw);
            $profilePic = "assets/images/profile-pic/default.jpg";
            $date = date("Y-m-d");
            $result = mysqli_query($this->con, "INSERT INTO users VALUES ('', '$un', '$em', '$encryptedPw', '$date', '$profilePic')");
            return $result;
        }
        
        private function validateuserName($un){
            if(strlen($un) > 25 || strlen($un) < 5){
                array_push($this->errorArray,constants::$usernameLimit);
                return;
            }
            $checkUsernameTaken = mysqli_query($this->con,"SELECT username FROM users WHERE username = '$un' ");
            if(mysqli_num_rows($checkUsernameTaken) != 0){
                array_push($this->errorArray,constants::$usernameTaken);
                return;
            }
        }
        
        private function validatepassWord($pw,$pw2){
            if($pw!=$pw2){
                array_push($this->errorArray,constants::$passwordDoNotMatch);
                return;
            }
            if(!preg_match('/[A-Za-z0-9]/',$pw)){
                array_push($this->errorArray,constants::$passwordAlphaNumeric);
                return;
            }
            if(strlen($pw) > 30 || strlen($pw) < 5){
                array_push($this->errorArray,constants::$passwordLimit);
                return;
            }
        }
        
        private function validateEmail($em,$em2){
            if($em!=$em2){
                array_push($this->errorArray,constants::$emailDoNotMatch);
                return;
            }
            if(!filter_var($em,FILTER_VALIDATE_EMAIL)){
                array_push($this->errorArray,constants::$emailInvalid);
                return;
            }
            $checkEmailTaken = mysqli_query($this->con,"SELECT email FROM users WHERE email = '$em' ");
            if(mysqli_num_rows($checkEmailTaken) != 0){
                array_push($this->errorArray,constants::$emailTaken);
                return;
            }
        }

        public function getError($error){
            if(!in_array($error, $this->errorArray)){
                $error = ""; 
            }
            return "<span class = errorMessage>$error</span>";
        }
    }
?>