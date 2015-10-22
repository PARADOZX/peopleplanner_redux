<?php


class User_Auth {
     private $email;
     private $password;
     private $DB;

     function __construct($DB, $email, $pass){
          if (!empty($email) && !empty($pass)) {
               if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                   $this->DB = $DB;
                   $this->email = $email;
                   $this->password = $pass;
               } else {
                    throw new Exception('Please enter a valid email.');
               }
          } else {
               throw new Exception('Please enter email and password.');
          }
          
     }

     public function loginUser() {
          $errors = array();
          if (empty($this->email)) {
               $errors[] = 'You forgot to enter your email address.';
          } else {
               $email = trim($this->email);
          }
         
          if (empty($this->password)) {
               $errors[] = 'You forgot to enter your password.';
          } else {
               $pass = trim($this->password);
          }
         
          if (empty($errors)) { 
               $q = "SELECT firstName, userID, password FROM user WHERE email = ?";
               try {
                    $stmt = $this->DB->prepare($q);
                    $stmt->execute(array($email));
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    $result = $stmt->fetch();
               } catch (PDOException $e) {
                    echo $e->getMessage();
               }

               // if user found return user details in JSON
               if (password_verify($pass, $result['password'])) {
               // if ($p == $result['password']) {     //bluehost

                    //start session
                    session_start();
                    $_SESSION['user'] = $result['userID'];

                    $resultArr = array();
                    foreach ($result as $key => $value){
                         $resultArr[$key] = $value;
                    }                 
                    echo json_encode($resultArr);
                    
               } else {
                    throw new Exception('The email address and password entered do not match those on file.');
               }
          } else {
               print_r($errors);
          }
     }

     static function logOut(){
          session_unset();     // unset $_SESSION variable for the run-time 
          session_destroy();   // destroy session data in storage
          setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);

          echo "<h2>You have logged out.</h2><br /><p>Click <a href=''>here</a> to login again.</p>";
     }
}
?>