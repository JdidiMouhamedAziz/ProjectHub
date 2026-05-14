
<?php

//------------------------------------------------------------------------
    // Create User--->createUser(username,password,email,role)
    // Get user By username--->findUserByUsername(username)
    // Get user By Email--->findUserByEmail(email)
    // Get user By Role--->findUserByRole(role)
    // Get user By Id--->findUserById(id)
    // Get All Users--->findAllUsers()
    // Delete User--->deleteUser(id)
    // Update User--->updateUser(id,username,email,role,password)
//------------------------------------------------------------------------


    class User{
        private $pdo;
        public function __construct($pdo){
            $this->pdo = $pdo;
        }

        // create user
        public function createUser($username,$password,$email,$role){
            // hash password
            $hashedpassword=password_hash($password,PASSWORD_DEFAULT);
            // insert into database
            $stmt=$this->pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?) 
            ");
            return $stmt->execute([$username,$email,$hashedpassword,$role]);
        }

        // Find User By Username
        public function findUserByUsername($username){
            $stmt=$this->pdo->prepare("SELECT * FROM users WHERE username LIKE ?
            ");
            $stmt->execute(["%".$username."%"]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        // Find User By Username for login
        public function findUserByUsernameLogin($username){
            $stmt=$this->pdo->prepare("SELECT * FROM users WHERE username = ?
            ");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Find User By Email
        public function findUserByEmail($email){
            $stmt=$this->pdo->prepare("SELECT * FROM users WHERE email LIKE ?
            ");
            $stmt->execute(["%".$email."%"]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Find Users By Role
        public function findUsersByRole($role){
            $stmt=$this->pdo->prepare("SELECT * FROM users WHERE role = ?
            ");
            $stmt->execute([$role]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //Find User By Id
        public function findUserById($id){
            $stmt=$this->pdo->prepare("SELECT * FROM users WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Find All
        public function findAllUsers() {
          $stmt = $this->pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Update User
        public function updateUser($id, $username, $email, $role, $password = null) {
            // if the password is not null update it 
          if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, email=?,role=?, password=? WHERE id=?");
            return $stmt->execute([$username, $email, $role, $hash, $id]);
          } else {
            // else do not update the password
            $stmt = $this->pdo->prepare("UPDATE users SET username=?, email=?,role=? WHERE id=?");
            return $stmt->execute([$username, $email, $role, $id]);
          }
        }

        //Delete User
        public function deleteUser($id) {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        }
    }
?>