<?php

class Users
{


    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function addUser()
    {
        //För att checka att det inte redan finns en användare med samma email
      $result = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
      $result->bindParam(':email', $_POST['email']);

      if(count($result) > 0){
        $_SESSION['message'] = 'This user already exists';

      }
      //Try connect to the database
      try
      {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $statement = $this->pdo->prepare("
        INSERT INTO users(username, email, password)
        VALUES (:username, :email, :password)");

        $statement->execute([
          ':username' => $_POST['username'],
          ':email' => $_POST['email'],
          ':password' => $new_password
        ]);
          return $statement;
        }
        //If we don't connect: throw an exception
        catch(PDOException $e)
        {
          echo $e->getMessage();
        }

    }

    public function login()
    {

     if(!empty($_POST['username']) && !empty($_POST['password'])){

            //Try connect to the database
      try
      {
       $statement = $this->pdo->prepare('SELECT id, username, email, password, role FROM users WHERE username = :username');
       $statement->bindParam(':username', $_POST['username']);
       $statement->execute();
       //Fetch what is matched with the input
       $results = $statement->fetch(PDO::FETCH_ASSOC);

      //  var_dump($results);
      //  var_dump(password_verify($_POST['password'], $results['password']));
       $msg_log = '';
         if(password_verify($_POST['password'], $results['password'])){

           $_SESSION['user_id'] = $results['id'];

           $_SESSION['loggedin'] = true;

           $_SESSION['username'] = $results['username'];

           $_SESSION['admin'] = $results['role'];

           $msg_log = 'Hello' . $results['username'] . 'You successfully logged in';

           header('Location: /');

         } elseif(!password_verify($_POST['password'], $results['password'])) {
           header('Location: /login_user.php');
         }

         }
         //If we don't connect: throw an exception
        catch(PDOException $e)
        {
          echo $e->getMessage();
        }

       }


   }


}