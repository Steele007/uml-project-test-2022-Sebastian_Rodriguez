<?php
class FetchData{

    private $conn;

    function __construct($conn){
        $this->conn= $conn;
    }

    //Called when getFunc calls a function that doesn't exist.
    public function __call($method, $args){
        echo $method." is not a valid command.";
    }

    function getEmployees(){

        $departments = $this->conn->query("SELECT * FROM departments;")->fetchAll();

        //Set up an associative array indexed by department name 
        $employeeHierarchy = array();

        foreach($departments as $department){

            $employeeHierarchy[$department['department']] = [];

        }

        //For each department, fill it with the names and titles of its employees.
        foreach($departments as $department){

            //Seperate supervisors and non-supervisors into two separate arrays.
            $supervisors =$this->conn->query("SELECT * FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${department['id']};")->fetchAll();
            $employees = $this->conn->query("SELECT * FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${department['id']};")->fetchAll();

            foreach($supervisors as $row){
               
                array_push($employeeHierarchy[$department['department']], array("first_name"=>$row['first_name'], "last_name"=>$row['last_name'], "title"=>$row['title']));

            }
            foreach($employees as $row){
               
                array_push($employeeHierarchy[$department['department']], array("first_name"=>$row['first_name'], "last_name"=>$row['last_name'], "title"=>$row['title']));

            }

            
            
        }
        echo json_encode($employeeHierarchy);
    }

    //Calls the function matching $funcName along with any of the variables passed.
    function getFunc($funcName, ...$args){
        $funcToCall = $funcName;
        $this->$funcToCall(...$args);
    }

}
?>