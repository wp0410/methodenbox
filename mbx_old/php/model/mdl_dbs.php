<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------

/**
 * DatabaseConnection        Encapsulates the connection to the MySQL database
 * 
 * @package        DatabaseConnection
 * @author         Walter Pachlinger (walter.pachlinger@gmx.at)
 * @version        $Revision: 1.0 $
 * @access         public
 */
class DatabaseConnection
{
    private static $db_connection = null;
    private static $db_error = null;
    
    /**
     * Establishes a connection to the MySQL database
     * 
     * @access     private
     * @return     Database connection (mysqli)
     * @return     In case of error: null
     */
    private static function connect()
    {
        if (self::$db_error != null)
        {
            return null;
        }
        
        if (GlobalParam::$app_config['deploy_zone'] == 'DEMO')
        {
            $db_host = 'sql113.byethost.com';
            $db_username = 'b8_22634095';
            $db_password = 'UthmaifKocyoHu1';
            $db_name = 'b8_22634095_mthbox';
            $db_port = 3306;
        }
        
        if (GlobalParam::$app_config['deploy_zone'] == 'DEV')
        {
            $db_host = getenv('IP');
            $db_username = getenv('C9_USER');
            $db_password = null;
            $db_name = 'c9';
            $db_port = null;
        }
    
        $db_conn = new mysqli($db_host, $db_username, $db_password, $db_name, $db_port);
        if ($db_conn->connect_error)
        {
            self::$db_error = new ErrorInfo();
            self::$db_error->err_last_action = 'Connect to MySQL DB (' . $db_host . ', ' . $db_username . ', *****, ' . $db_name . ')';
            self::$db_error->err_number = $db_conn->connect_errno;
            self::$db_error->err_text = $db_conn->connect_error;
            return null;
        }
        else 
        {
            return $db_conn;
        }
    }
    
    /**
     * Getter: retrieves the mysqli connection to the database. Establishes the
     * connection in case it is not yet established
     * 
     * @access     public
     * @return     Database connection (mysqli)
     * @return     In case of error: null
     */
    public static function get_connection()
    {
        if (self::$db_connection == null)
        {
            self::$db_connection = self::connect();
        }
        return self::$db_connection;
    }
    
    /**
     * Getter: retrieves the description of the error that occurred when
     * tying to establish the database connection
     * 
     * @access     public
     * @return     ErrorInfo bject describing the database error
     * @return     null in case no error occurred
     */
    public static function get_error()
    {
        return self::$db_error;
    }
}

/**
 * Executes a non-select SQL statement with one parameter
 * 
 * @param          mysqli    $db_conn       Database connection
 * @param          string    $stmt          SQL statement to be executed
 * @param          string    $par_type      Type of the single parameter
 * @param          object    $par_value     Value of the single parameter
 * 
 * @return         TRUE      Success
 * @return         FALSE     Error executing the statement
 */
function db_execute_stmt_one_param($db_conn, $stmt, $par_type, $par_value)
{
    $sql_stmt = $db_conn->prepare($stmt);
    $sql_stmt->bind_param($par_type, $par_value);
    $result = $sql_stmt->execute();
    $sql_stmt->free_result();
    $sql_stmt->close();
    
    return $result;
}
?>