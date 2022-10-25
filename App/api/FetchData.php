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

    private function getEmployeesAt($dept=[]){
       
        if(count($dept) != 1){
            $error = array("error" => true, "message"=>"Must have a single arg.");
            echo json_encode($error);
            exit();
        }

        $employeeList = [];

        if(intval($dept[0])){

            $supervisors =$this->conn->query("SELECT * FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept[0]};")->fetchAll(PDO::FETCH_ASSOC);
            $employees = $this->conn->query("SELECT * FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept[0]};")->fetchAll(PDO::FETCH_ASSOC);

        }elseif(is_string($dept[0])){

            $supervisors =$this->conn->query("SELECT * FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = (SELECT id FROM departments WHERE department = '${dept[0]}');")->fetchAll(PDO::FETCH_ASSOC);
            $employees = $this->conn->query("SELECT * FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = (SELECT id FROM departments WHERE department = '${dept[0]}');")->fetchAll(PDO::FETCH_ASSOC);

        }else{

            $error = array("error" => true, "message"=>"Argument must either be an integer Id or the name of the department");
            echo json_encode($error);
            exit();

        }

        
        if($supervisors){

            foreach($supervisors as $row){
           
                array_push($employeeList, $row);

            }
            foreach($employees as $row){
            
                array_push($employeeList, $row);

            }

            echo json_encode($employeeList);

        }else{
            $error = array("error" => true, "message"=>"Department doesn't exist.");
            echo json_encode($error);
        }
        
    }

    private function getDepartments(){

        $departments = $this->conn->query("SELECT * FROM departments;")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($departments);
    }

    private function getEmployees(){

        $departments = $this->conn->query("SELECT * FROM departments;")->fetchAll(PDO::FETCH_ASSOC);

        //Set up an associative array indexed by department name 
        $employeeHierarchy = array();

        foreach($departments as $department){

            $employeeHierarchy[$department['department']] = [];

        }

        //For each department, fill it with the names and titles of its employees.
        foreach($departments as $department){

            //Seperate supervisors and non-supervisors into two separate arrays.
            $supervisors =$this->conn->query("SELECT * FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${department['id']};")->fetchAll(PDO::FETCH_ASSOC);
            $employees = $this->conn->query("SELECT * FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${department['id']};")->fetchAll(PDO::FETCH_ASSOC);

            foreach($supervisors as $row){
               
                array_push($employeeHierarchy[$department['department']], $row);

            }
            foreach($employees as $row){
               
                array_push($employeeHierarchy[$department['department']], $row);

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