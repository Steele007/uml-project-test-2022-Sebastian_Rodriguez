<?php

class FetchData{

    private $conn;

    //Takes a PDO connection for the rest of the class to function.
    function __construct($conn){
        $this->conn= $conn;
    }

    //Called when getFunc calls a function that doesn't exist.
    public function __call($method, $args){
        $error = array("error" => true, "message"=>$method." is not a valid command.");
        echo json_encode($error);
        exit();
    }
    

    //Gets a JSON object representing the department that matches the search criteria. 
    private function getDepartment($searchParams=[]){

        if(count($searchParams) != 1){
            $error = array("error" => true, "message"=>"Must have a single arg.");
            echo json_encode($error);
            exit();
        }

        if(intval($searchParams[0])){

            $department = $this->conn->query("SELECT * FROM departments WHERE id = ${searchParams[0]};")->fetch(PDO::FETCH_ASSOC);

        }elseif(is_string($searchParams[0])){

            $department = $this->conn->query("SELECT * FROM departments WHERE department = '${searchParams[0]}';")->fetch(PDO::FETCH_ASSOC);

        }else{
            $error = array("error" => true, "message"=>"Argument value not supported.");
            echo json_encode($error);
            exit();
        }

        if($department){
            echo json_encode($department);
        }else{
            $error = array("error" => true, "message"=>"Department doesn't exist.");
            echo json_encode($error);
            exit();
        }
    }

    //Gets a JSON list of all employees that match the search criteria. Input is either a single int (for id)
    //or string name (either one word or two for first name/last name). May return multiple employees if they share the name.
    private function getEmployee($searchParams=[]){

        $employeeResult = [];
        if(count($searchParams) != 1){
            $error = array("error" => true, "message"=>"Must have a single arg.");
            echo json_encode($error);
            exit();
        }
            
        if(intval($searchParams[0])){

            $employeeResult = $this->conn->query("SELECT * FROM staff WHERE id = ${searchParams[0]};")->fetch(PDO::FETCH_ASSOC);

        }elseif(is_string($searchParams[0])){

            $name = explode(' ', $searchParams[0]);

            if(count($name)==1){

                if(strpos($name[0], "first_name=") === 0){

                    $name = substr($name[0],11);
                    $employeeResult = $this->conn->query("SELECT * FROM staff WHERE first_name = '".$name."';")->fetchAll(PDO::FETCH_ASSOC);

                }elseif(strpos($name[0], "last_name=") === 0){
                    $name = substr($name[0],10);
                    $employeeResult = $this->conn->query("SELECT * FROM staff WHERE last_name = '".$name."';")->fetchAll(PDO::FETCH_ASSOC);
                }else{
                    $employeeResult = $this->conn->query("SELECT * FROM staff WHERE first_name = '${name[0]}';")->fetchAll(PDO::FETCH_ASSOC);
                    $employeeResult = array_merge($employeeResult, $this->conn->query("SELECT * FROM staff WHERE last_name = '${name[0]}';")->fetchAll(PDO::FETCH_ASSOC));
                }

            }elseif(count($name)==2){

                $employeeResult = $this->conn->query("SELECT * FROM staff WHERE first_name = '${name[0]}' AND last_name ='${name[1]}';")->fetchAll(PDO::FETCH_ASSOC);

            }else{

                $error = array("error" => true, "message"=>"Name must either be one or two words (one name or first and last name).");
                echo json_encode($error);
                exit();

            }

        }else{

            $error = array("error" => true, "message"=>"Argument value not supported.");
            echo json_encode($error);
            exit();

        }

        

        if($employeeResult){
            echo json_encode($employeeResult);
        }else{
            $error = array("error" => true, "message"=>"Employee doesn't exist.");
            echo json_encode($error);
            exit();
        }

    }

    //Gets a JSON list of all employees that work for the supervisor that matches the search criteria. Input is either an int Id
    //or string name (either one word or two for first name/last name). May return employees from different supervisors if they share the name.
    private function getSupervisorEmployees($searchParams=[]){
        if(count($searchParams) != 1){
            $error = array("error" => true, "message"=>"Must have a single arg.");
            echo json_encode($error);
            exit();
        }

        if(intval($searchParams[0]) && intval($searchParams[0])!=0){

            $employees = $this->conn->query("SELECT * FROM staff WHERE supervisor_id = ${searchParams[0]};")->fetchAll(PDO::FETCH_ASSOC);

        }elseif(is_string($searchParams[0])){

            $name = explode(' ', $searchParams[0]);

            if(count($name)==1){

                if(strpos($name[0], "first_name=") === 0){

                    $name = substr($name[0],11);
                    $employees = $this->conn->query("SELECT * FROM staff WHERE supervisor_id = (SELECT id FROM staff WHERE first_name = '".$name."');")->fetchAll(PDO::FETCH_ASSOC);

                }elseif(strpos($name[0], "last_name=") === 0){
                    $name = substr($name[0],10);
                    $employees = $this->conn->query("SELECT * FROM staff WHERE supervisor_id = (SELECT id FROM staff WHERE last_name = '".$name."');")->fetchAll(PDO::FETCH_ASSOC);
                }else{
                    $employees = $this->conn->query("SELECT * FROM staff WHERE supervisor_id = (SELECT id FROM staff WHERE first_name = '${name[0]}');")->fetchAll(PDO::FETCH_ASSOC);
                    $employees = array_merge($employees, $this->conn->query("SELECT * FROM staff WHERE supervisor_id = (SELECT id FROM staff WHERE last_name = '${name[0]}');")->fetchAll(PDO::FETCH_ASSOC));
                }

            }elseif(count($name)==2){

                $employees = $this->conn->query("SELECT * FROM staff WHERE supervisor_id = (SELECT supervisor_id FROM staff WHERE first_name = '${name[0]}' AND last_name = '${name[1]}');")->fetchAll(PDO::FETCH_ASSOC);

            }else{

                $error = array("error" => true, "message"=>"Name must either be one or two words (one name or first and last name).");
                echo json_encode($error);
                exit();

            }

        }else{

            $error = array("error" => true, "message"=>"Argument value not supported.");
            echo json_encode($error);
            exit();
        }

        if($employees){
            echo json_encode($employees);
        }else{
            $error = array("error" => true, "message"=>"Supervisor doesn't exist.");
            echo json_encode($error);
            exit();
        }

    }

    //Gets a JSON list of all employees at the specified department (either by int id or string name).
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
            exit();
        }
        
    }

    //Gets a JSON list of all departments.
    private function getDepartments(){

        $departments = $this->conn->query("SELECT * FROM departments;")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($departments);
    }

    //Returns the JSON list of employees sorted by department, with supervisors first.
    private function getEmployeesByDept(){

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

    //Gets a JSON list of all employees.
    private function getEmployees(){

        $employees = $this->conn->query("SELECT * FROM staff;")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($employees);
    }

    //Calls the function matching $funcName along with any of the variables passed.
    function getFunc($funcName, ...$args){
        $funcToCall = $funcName;
        $this->$funcToCall(...$args);
    }


}
?>