
<?php
    include("./api/FetchData.php");

    $sqlHost = "mysql";
    $port = "3306";
    $sqlUser = "phpdemo_mysql_user";
    $sqlPassword = "phpdemo_mysql_pass";
    $dbName = "phpdemo_mysql_db";

    try{
        $conn = new PDO("mysql:host=$sqlHost;dbname=$dbName;port=$port",$sqlUser,$sqlPassword);

        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";
    }catch(PDOException $e){
        echo "Connection failed: " . $e->getMessage();
    }

    //Comment this out after running, the db will retain the data.
    //$sqlSetupQueries = file_get_contents("./data/data.sql");
    //$conn->exec($sqlSetupQueries);

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    //Controls what is displayed based on the URI. Default is the html list of emlpoyees by their department.
    if($uri =="/"){



    }else{

        //Initializes FetchData with the current db connection and calls the function with the name matching the first '/' seperated word.
        $uri = explode('/', $uri);
        $apiCall = new FetchData($conn);
        $apiCall->getFunc($uri[1]);
        $conn = null;

        //Prevents the rest of the script from executing so the json output can be safely consumed.
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>   

    <meta charset="UTF-8">
    <title>Title</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">  
    
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <?php 

        $departments = $conn->query("SELECT * FROM departments;")->fetchAll();

   
            
        //Seperate supervisors and non-supervisors into two separate arrays.
        foreach($departments as $dept){

            echo "<div><h1>${dept['department']}</h1><br><br>";

            //Seperate supervisors and non-supervisors into two separate arrays.
            $supervisors = $conn->query("SELECT * FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept['id']};")->fetchAll();
            $employees = $conn->query("SELECT * FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept['id']};")->fetchAll();

            foreach($supervisors as $row){
                
                echo "${row['first_name']} ${row['last_name']}, ${row['title']}<br>";

            }
            foreach($employees as $row){
                
                echo "${row['first_name']} ${row['last_name']}, ${row['title']}<br>";

            }

            echo "<br></div>";
        }
     
        $conn = null;
    ?> 
</body>
</html>
