<?php

class Users
{


    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }


    /*public function addUser()
    {

 
      //Checkar om användaren skrivit i alla input-fält
      if(!empty($_POST['username']) && !empty($_POST['email'])){
    
         $result = $this->pdo->prepare("SELECT * FROM users WHERE username = :username AND email = :email");
         $result->execute(':username' => $_POST['username'], ':email' => $_POST['email']);
         $result->fetchAll();

          }
      }*/
     
      //
      public function addUser(){
      //För att checka att användaren skrivit in i båda input-fält
      if(!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])){

        $_POST['msg_adduser'] = '';
        $_POST['msg_user_reg'] = '';

        if($this->userExists()){
          return $_POST['msg_adduser'] = 'Användaren finns redan';
        } else {
          $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
          $statement = $this->pdo->prepare("
            INSERT INTO users (username, email, password)
            VALUES (:username, :email, :password)
          ");
          
          $statement->execute([
              ':username' => $_POST['username'],
              ':email' => $_POST['email'],
              ':password' => $new_password
          ]);
          return $_POST['msg_user_reg'] = 'Du är nu registrerad!';
        }     
      }
      //Exekverar om användaren inte skrivit i alla input-fält
      else{
        return $_POST['msg_adduser'] = 'Vänligen ange användarnamn, email och lösenord.';
      }
      }

      //Funktion som checkar så att användaren inte redan finns i databasen
      private function userExists(){
        
          $statement = $this->pdo->prepare("
            SELECT username, email FROM users
            WHERE username = :username
            OR email = :email 
          ");

          $statement->execute([
              ':username' => $_POST['username'],
              ':email' => $_POST['email']
          ]);

          //Saves and return the data from the statement
         return $statement->fetch();
    
      }


    public function login()
    {
     //För att checka att användaren skrivit in i båda input-fält
     if(!empty($_POST['username']) && !empty($_POST['password'])){

       $statement = $this->pdo->prepare('SELECT id, username, email, password, role FROM users WHERE username = :username');
       $statement->bindParam(':username', $_POST['username']);
       $statement->execute();
       //Fetch what is matched with the input
       $results = $statement->fetch(PDO::FETCH_ASSOC);

       $msg_log = '';
         if(password_verify($_POST['password'], $results['password'])){

           $_SESSION['user_id'] = $results['id'];

           $_SESSION['loggedin'] = true;

           $_SESSION['username'] = $results['username'];

           $_SESSION['admin'] = $results['role'];

           $_SESSION['success'] = 'Hej ' . '<strong>' .$results['username'] .'</strong>' . ', du är nu inloggad!';
  

           header('Location: /');

         } elseif(!password_verify($_POST['password'], $results['password'])) {
           return $_POST['error'] = 'Fel användarnamn eller lösenord.';
 
         }

       } else {
         return $_POST['error'] = 'Vänligen ange användarnamn och lösenord.';
 
       }
   }


}



