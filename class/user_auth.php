<?php


class User_Auth {
     private $email;
     private $password;

     function __construct($email, $pass){
          if (!empty($email) && !empty($pass)) {
               if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                   $this->email = $email;
                   $this->password = $pass;
                   User_Auth::check_login($this->email, $this->password);
               } else {
                    echo 'Please enter a valid email address.';
               }
          } else {
               echo 'Please enter email and password to log in.';
          }
          
     }

     static function check_login($email = '', $pass = '') {
          $errors = array();
          if (empty($email)) {
               $errors[] = 'You forgot to enter your email address.';
          } else {
               $e = trim($email);
          }
         
          if (empty($pass)) {
               $errors[] = 'You forgot to enter your password.';
          } else {
               $p = trim($pass);
          }
         
          if (empty($errors)) { 
               $dbconnection = new dbconnect();
               $DB = $dbconnection->connect();
               $q = "SELECT firstName, userID FROM user WHERE email=? AND password=SHA1(?)";

               try {
                    $stmt = $DB->prepare($q);
                    $stmt->execute(array($e, $p));
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $result = $stmt->fetch();
               } catch (PDOException $e) {
                    echo $e->getMessage();
               }
               //if user found return user details in JSON
               if ($result) {

                    //start session
                    session_start();
                    $_SESSION['user'] = $result['userID'];

                    $resultArr = array();
                    foreach ($result as $key => $value){
                         $resultArr[$key] = $value;
                    }                 
                    echo json_encode($resultArr);
               } else {
                    echo 'The email address and password entered do not match those on file.';
               }
          } else {
               print_r($errors);
          }
     }

     static function register(){

     }

     static function logOut(){
          session_unset();     // unset $_SESSION variable for the run-time 
          session_destroy();   // destroy session data in storage
          setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);

          echo "<h2>You have logged out.</h2>";
     }
}
?>