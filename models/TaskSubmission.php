<?php

//------------------------------------------------------------------------

    // create task submission ---> createTaskSubmission(task_id,git_link,message,status)
    // Get ALL task Submission ---> findAllTaskSubmissions()
    // Get task Submission By task id ---> findTaskSubmissionsByTask(task_id)
    // Get task Submission By status ---> findTaskSubmissionsByStatus(status)
    // update task Submission  status ---> updateTaskSubmissionStatus(id,status)
    // update task Submission ---> updateTaskSubmission(id,task_id,message,git_link,status)
    // delete task Submission --->deleteTaskSubmission(id)



//------------------------------------------------------------------------


    class TaskSubmission{
        private $pdo;
        public function __construct($pdo){
            $this->pdo = $pdo;
        }

        // Create Task Submission
        public function createTaskSubmission($task_id,$git_link,$message,$status){
            $stmt = $this->pdo->prepare("INSERT INTO task_submissions (task_id,git_link,message,status) VALUES (?,?,?,?)");
            return $stmt->execute([$task_id,$git_link,$message,$status]);
        }

        // get All Task Submission
        public function findAllTaskSubmissions(){
            $stmt = $this->pdo->prepare("SELECT * FROM task_submissions");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // get Task Submission By Task_Id
        public function findTaskSubmissionsByTask($task_id){
            $stmt = $this->pdo->prepare("SELECT * FROM task_submissions WHERE task_id=? ORDER BY created_at DESC");
            $stmt->execute([$task_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // //get Task Submissions BY User 
        // public function findTaskSubmissionsByUser($user_id){
        //     $stmt = $this->pdo->prepare("SELECT * FROM task_submissions WHERE user_id=?");
        //     $stmt->execute([$user_id]);
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }

        // get task submissions by status
        public function findTaskSubmissionsByStatus($status){
            $stmt = $this->pdo->prepare("SELECT * FROM task_submissions WHERE status=?");
            $stmt->execute([$status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // get task submissions by project id
        public function findTaskSubmissionsByProject($project_id){
            $stmt = $this->pdo->prepare("
                SELECT ts.* FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                WHERE t.project_id = ?
                ORDER BY ts.created_at DESC
            ");
            $stmt->execute([$project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // update task submission status
        public function updateTaskSubmissionStatus($id,$status){
            $stmt = $this->pdo->prepare("UPDATE task_submissions SET status=? WHERE id=?");
            return $stmt->execute([$status,$id]);
        }

        // update task submission
        public function updateTaskSubmission($id,$task_id,$message,$git_link,$status){
            $stmt = $this->pdo->prepare("UPDATE task_submissions SET task_id=?, message=?, git_link=?, status=? WHERE id=?");
            return $stmt->execute([$task_id,$message,$git_link,$status,$id]);
        }

        // delete task submission
        public function deleteTaskSubmission($id){
            $stmt = $this->pdo->prepare("DELETE FROM task_submissions WHERE id=?");
            return $stmt->execute([$id]);
            
        }
    }

?>