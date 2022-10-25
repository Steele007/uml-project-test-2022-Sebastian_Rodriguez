# Programmer Demonstration Project, UM Libraries

## Overview:  

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

* hostname/getEmployeesAt/{param}
   * Gets all employees that work in the specified department, with supervisors on top. {param} can either be the name the department or its id number.

* hostname/getEmployee/{param}
   * Gets the employee(s) that match the {param} search criteria. {param} can be the full or parital name of the employee or the employee's id number.

* hostname/getDepartment/{param}
   * Gets the department that matches the {param} search criteria. {param} can either be the name the department or its id number.

Errors will return a JSON object in the following form:

{error : bool, message : string}

