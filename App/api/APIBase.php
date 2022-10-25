<?php
class APIBase{

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

    //Calls the function matching $funcName along with any of the variables passed.
    function getFunc($funcName, ...$args){
        $funcToCall = $funcName;
        $this->$funcToCall(...$args);
    }

    //Prints out an error in JSON. Takes a string error message as param. 
    private function showError($errorMessage, ...$errorParams){

        $error = array("error" => true, "message"=>$errorMessage);
        echo json_encode($error);
        exit();

    }
}

?>