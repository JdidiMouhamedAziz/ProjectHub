<?php
//-----------------------------------------------------------------------

    // Create Task ---> createTask(title,description,status,complexity,project_id,assigned_to)
    // Get All Task ---> findAllTask()
    // Get Tasks By Project Id ---> findTasksByProjectId(project_id)
    // Get Tasks By User Id ---> findTasksByUser(user_id)
    // Get Tasks By Status ---> findTasksByStatus(status)
    // Get Tasks By Status and user ---> findTasksByStatusAndUser(status,user_id)
    // Get Tasks By Status and project ---> findTasksByStatusAndProject(status,project_id)
    // Get Task By Id ---> findTasksById(id)
    // Update task ---> updateTask(id)
    // Update task status ---> updateTaskStatus(id,status)
    // Update user Task ---> updateUserTask(id,user_id)
    // Delete Task ---> deleteTask(id)


//-----------------------------------------------------------------------

    class Task{
        private $pdo;
        public function __construct(PDO $pdo) {
            $this->pdo = $pdo;
        }

        // Create Task
        public function createTask($title,$description,$status,$complexity,$project_id,$assigned_to){
            $stmt=$this->pdo->prepare("INSERT INTO tasks (title,description,status,complexity,project_id,assigned_to) VALUES (?,?,?,?,?,?)");
            return $stmt->execute([$title,$description,$status,$complexity,$project_id,$assigned_to]);
        }

        // find All Tasks
        public function findAllTasks(){
            $stmt=$this->pdo->prepare("SELECT * FROM tasks ORDER BY complexity DESC ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Find Tasks By Project Id
        public function findTasksByProjectId($project_id){
            $stmt=$this->pdo->prepare("SELECT * From tasks WHERE project_id=?");
            $stmt->execute([$project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);   
        }

        //Find Tasks By User Id
        public function findTasksByUser($user_id){
            $stmt=$this->pdo->prepare("SELECT * FROM tasks WHERE assigned_to = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Find Tasks by Status
        public function findTasksByStatus($status){
            $stmt=$this->pdo->prepare("SELECT * FROM tasks WHERE status=?");
            $stmt->execute([$status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Find Tasks by Status and user
        public function findTasksByStatusAndUser($status,$user_id){
            $stmt=$this->pdo->prepare("SELECT * FROM tasks WHERE status=? AND assigned_to=?");
            $stmt->execute([$status,$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Find Tasks by Status and project
        public function findTasksByStatusAndProject($status,$project_id){
            $stmt=$this->pdo->prepare("SELECT * FROM tasks WHERE status=? AND project_id=?");
            $stmt->execute([$status,$project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Find Task By Id
        public function findTaskById($id){
            $stmt=$this->pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Update Task
        public function updateTask($id,$title,$description,$status,$complexity,$project_id,$assigned_to){
            $stmt=$this->pdo->prepare("UPDATE tasks SET title=?, description=?, status=?, complexity=?, project_id=?, assigned_to=? WHERE id=?");
            return $stmt->execute([$title,$description,$status,$complexity,$project_id,$assigned_to, $id]);
        }

        //update Task Satus
        public function updateTaskStatus($id,$status){
            $stmt=$this->pdo->prepare("UPDATE tasks SET status = ? WHERE id=?");
            return $stmt->execute([$status,$id]);
        }

        //update User Task
        public function updateUserTask($id,$user_id){
            $stmt=$this->pdo->prepare("UPDATE tasks SET assigned_to=? WHERE id=?");
            return $stmt->execute([$user_id, $id]);
        }

        // delete Task
        public function deleteTask($id){
            $stmt=$this->pdo->prepare("DELETE FROM tasks WHERE id=?");
            return $stmt->execute([$id]);
        }
    

    }

?>