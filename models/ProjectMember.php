<?php

//-----------------------------------------------------------------------

    // create project member ---> createProjectMember(project_id,user_id,role)
    // Get Projects By Member ---> findProjectByMember(user_id)
    // Get Members By Project ---> findMembersByProject(project_id)
    // Get Members By Role ---> findMembersByRole(role)
    // Get Members By Project And Role ---> findMembersByRoleAndProject(role)
    // Get Role By Project And User ---> findRoleByProjectAndUser(project_id,user_id)
    // update role in a project ---> updateProjectMember(project_id,user_id,role)
    // delete project Member ---> updateProjectMember(project_id,user_id)

//-----------------------------------------------------------------------

    class ProjectMember{
        private $pdo;
        public function __construct($pdo){
            $this->pdo = $pdo;
        }

        // Create project member
        public function createProjectMember($project_id, $user_id,$role){
            $stmt=$this->pdo->prepare("INSERT INTO project_members (project_id,user_id,role) VALUES (?,?,?)");
            return $stmt->execute([$project_id,$user_id,$role]);
        }

        // Find Projects By Member
        public function findProjectsByMember($user_id){
            $stmt=$this->pdo->prepare("SELECT * From project_members WHERE user_id= ?");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //find members By Project
        public function findMembersByProject($project_id){
            $stmt=$this->pdo->prepare("SELECT * FROM project_members WHERE project_id=?");
            $stmt->execute([$project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // find member By Role and project
        public function findMembersByRoleAndProject($role,$project_id){
            $stmt=$this->pdo->prepare("SELECT * FROM project_members WHERE role =? AND project_id =?");
            $stmt->execute([$role,$project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // find member By Role
        public function findMembersByRole($role){
            $stmt=$this->pdo->prepare("SELECT * FROM project_members WHERE role =?");
            $stmt->execute([$role]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // find Role By Project And User
        public function findRoleByProjectAndUser($project_id, $user_id){
            $stmt=$this->pdo->prepare("SELECT role FROM project_members WHERE project_id=? and user_id=?");
            $stmt->execute([$project_id, $user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // update role in project members
        public function updateProjectMember($project_id, $user_id, $role){
            $stmt=$this->pdo->prepare("UPDATE project_members SET role=? WHERE project_id=? and user_id=?");
            return $stmt->execute([$role,$project_id, $user_id]);
        }
        

        // delete project Member
        public function deleteProjectMember($project_id, $user_id){
            $stmt=$this->pdo->prepare("DELETE FROM project_members WHERE project_id=? and user_id=?");
            return $stmt->execute([$project_id, $user_id]);
        }
    }

?>