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
include_once '../model/aux_parameter.php';

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
        
        if (GlobalParameter::$applicationConfig['deploymentZone'] == 'DEMO')
        {
            $db_host = 'sql113.byethost.com';
            $db_username = 'b8_22634095';
            $db_password = 'UthmaifKocyoHu1';
            $db_name = 'b8_22634095_mthbox';
            $db_port = 3306;
        }
        
        if (GlobalParameter::$applicationConfig['deploymentZone'] == 'DEV_C9')
        {
            $db_host = getenv('IP');
            $db_username = getenv('C9_USER');
            $db_password = null;
            $db_name = 'c9';
            $db_port = null;
        }

        if (GlobalParameter::$applicationConfig['deploymentZone'] == 'DEV')
        {
            $db_host = getenv('IP');
            $db_username = 'mthbox';
            $db_password = 'AcIw35926+MB';
            $db_name = 'mthbox';
            $db_port = 3306;
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

?>