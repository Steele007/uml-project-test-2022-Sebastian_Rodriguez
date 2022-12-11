# Programmer Demonstration Project, UM Libraries

## Overview: 

### What is This?
My solution to a take-home project given by the University of Miami Libraries as part of their interview process. [Given here](https://github.com/UMiamiLibraries/uml-project-test-2022).

Instructions were as follows:

   a. Using PHP, output in HTML a hierarchical list of staff arranged by department, with the supervisor as the first person showing in each department

   b. Generate an API that outputs the data in JSON suitable for external applications to consume.

### Running the App:
1. Clone the github repository.
2. Run 'docker compose up' in the command line.
3. Once you have run the application once, feel free to comment out the code that executes the contents of data.sql in index.php (lines 23 and 24).

### Main Page:  
The home page uses HTML to output a list of employees ordered by their department, with supervisors first.

### API:  
The API is accessed by using the format hostname/{function name}/{arg1}/{arg2}/...etc.
Current API calls include:

* hostname/getEmployees
   * Prints out the full database information for every employee.

* hostname/getEmployeesByDept
   * Prints out the full database information for every employee, sorted by department, with supervisors at the top.

* hostname/getDepartments
   * Prints out the full database information for every department.

* hostname/getEmployeesAt/{arg1}
   * Gets all employees that work in the specified department, with supervisors on top. {arg1} can either be the name the department or its id number.

* hostname/getSupervisorEmployees/{arg1}
   * Gets all employees that work for the specified supervisor. {arg1} can be the full or parital name of the supervisor or the supervisor's id number. User can specify whether the partial name is the first or last name by typing 'first_name={name}' or 'last_name={name}' respectively.

* hostname/getEmployee/{arg1}
   * Gets the employee(s) that match the {arg1} search criteria. {arg1} can be the full or parital name of the employee or the employee's id number. User can specify whether the partial name is the first or last name by typing 'first_name={name}' or 'last_name={name}' respectively.

* hostname/getDepartment/{arg1}
   * Gets the department that matches the {arg1} search criteria. {arg1} can either be the name of the department or its id number.

* hostname/help
   * Outputs a list of all API commands in plaintext.

Errors will return a JSON object in the following form:

{error : bool, message : string}

