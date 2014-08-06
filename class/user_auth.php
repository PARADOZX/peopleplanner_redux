<?php


class User_Auth {
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
}
?>