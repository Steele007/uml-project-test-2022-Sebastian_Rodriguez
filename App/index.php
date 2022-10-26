
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

    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    //Controls what is displayed based on the URI. Default is the html list of emlpoyees by their department.
    if($uri =="/"){



    }else{

        //Initializes FetchData with the current db connection and calls the function with the name matching the first '/' seperated word.
        $uri = explode('/', $uri);

        //Get arguments if any.
        $arguments = [];

        for($i = 2; $i < count($uri); $i++){
            
            array_push($arguments, $uri[$i]);
        }

        $apiCall = new FetchData($conn);
        if($arguments){
            $apiCall->getFunc($uri[1], $arguments);
        }else{
            $apiCall->getFunc($uri[1]);
        }
        
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
           
        createAccrodion($departments, $conn);
        //Seperate supervisors and non-supervisors into two separate arrays.
        /*foreach($departments as $dept){

            echo "<div><h1>${dept['department']}</h1><br><br>";

            //Seperate supervisors and non-supervisors into two separate arrays.
            $supervisors = $conn->query("SELECT first_name, last_name, title FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept['id']};")->fetchAll();
            $employees = $conn->query("SELECT first_name, last_name, title FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept['id']};")->fetchAll();

            foreach($supervisors as $row){
                
                echo "${row['first_name']} ${row['last_name']}, ${row['title']}<br>";

            }
            foreach($employees as $row){
                
                echo "${row['first_name']} ${row['last_name']}, ${row['title']}<br>";

            }

            echo "<br></div>";
        }
        */
        

        function createAccrodion($departments, $conn){

            echo "<div class='accordion' id='accordionExample'>";
            $i = 0;
            foreach($departments as $dept){

                $supervisors = $conn->query("SELECT first_name, last_name, title FROM staff WHERE id IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept['id']};")->fetchAll();
                $employees = $conn->query("SELECT first_name, last_name, title FROM staff WHERE id NOT IN (SELECT DISTINCT supervisor_id FROM staff) AND department_id = ${dept['id']};")->fetchAll();

                echo "<div class='accordion-item'>
                <h1 class='accordion-header' id='heading$i'>
                  <button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$i' aria-expanded='true' aria-controls='collapse$i'>
                  ${dept['department']}
                  </button>
                </h1>
                <div id='collapse$i' class='accordion-collapse collapse' aria-labelledby='heading$i'>
                  <div class='accordion-body'>
                    <ul class='list-group list-group-flush'>";
                     foreach($supervisors as $row){
                    
                        echo "<li class='list-group-item'>${row['first_name']} ${row['last_name']}, ${row['title']}</li>";
    
                    }
                    foreach($employees as $row){
                    
                        echo "<li class='list-group-item'>${row['first_name']} ${row['last_name']}, ${row['title']}</li>";;
    
                    }
    
                    echo "</ul>
                  </div>
                </div>
              </div>";        
                $i++;
            }
            
            echo "</div>";
        }
        $conn = null;
    ?> 
</body>
</html>
