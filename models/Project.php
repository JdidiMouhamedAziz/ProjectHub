<?php


//-----------------------------------------------------------------------
    // Create Project --> createProject(title,description)
    // Get Project By Title --> FindProjectByTitle(title)
    // Get Project By Id --> FindProjectById(id)
    // Get All Project  --> FindAllProjects()
    // Update Project  --> UpdateProject(id,title,description)
    // Delete Project  --> Deleteroject(id)
//-----------------------------------------------------------------------


    class Project{
        private $pdo;
        public function __construct($pdo){
            $this->pdo=$pdo;
        }

        // Create Project
        public function createProject($title, $description){
            $stmt = $this->pdo->prepare("INSERT INTO projects (title,description) VALUES (?,?)");
            return $stmt->execute([$title, $description]);
        }

        // Find Project By Title
        public function findProjectByTitle($title){
            $stmt= $this->pdo->prepare("SELECT * FROM projects WHERE title LIKE ?");
            $stmt->execute(["%".$title."%"]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Find project By Id
        public function findProjectById($id){
            $stmt= $this->pdo->prepare("SELECT * FROM projects WHERE id=?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        //Find All
        public function findAllProjects(){
            $stmt= $this->pdo->prepare("SELECT * FROM projects");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Update Project
        public function updateProject($id, $title, $description){
            $stmt= $this->pdo->prepare("UPDATE projects SET title=? , description=? WHERE id=?");
            return $stmt->execute([$title, $description, $id]);
        }

        // Delete Project
        public function deleteProject($id){
            $stmt= $this->pdo->prepare("DELETE FROM projects WHERE id=?");
            return $stmt->execute([$id]);
        }
    }

?>