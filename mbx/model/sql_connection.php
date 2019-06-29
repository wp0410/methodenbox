<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018, 2019 Walter Pachlinger (walter.pachlinger@gmail.com)
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
	private static $rep_connection = null;
	
	/**
	 * Retrieves the connection parameters for the deployment zone
	 * 
	 * @access     private
	 * @return     Database connection parameters as array
	 */
	private static function get_connection_params($deploy_zone)
	{
		if ($deploy_zone == 'DEMO')
		{
			return array(
				'db_host' => 'localhost',
				'db_username' => 'u294174670_mbx',
				'db_password' => '4U5N1NQrPezQ',
				'db_name' => 'u294174670_mbx',
				'db_port' => 3306
			);
		}
		
		if ($deploy_zone == 'DEV')
		{
			return array(
				'db_host' => getenv('IP'),
				'db_username' => 'mthbox',
				'db_password' => 'AcIw35926+MB',
				'db_name' => 'mthbox',
				'db_port' => 3306
			);
		}
	}
    
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
		
		$conn_par = self::get_connection_params(GlobalParameter::$applicationConfig['deploymentZone']);
        
        $db_conn = new mysqli($conn_par['db_host'], $conn_par['db_username'], $conn_par['db_password'], $conn_par['db_name'], $conn_par['db_port']);
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
	 * Getter: retrieves the connection parameters as "KoolReport" database 
	 * connection array
	 *
	 * @access     public
	 * @return     KoolReport database connection array
	 */
	public static function get_report_connection()
	{
		if (self::$rep_connection == null)
		{
			$conn_par = self::get_connection_params(GlobalParameter::$applicationConfig['deploymentZone']);
			
			self::$rep_connection = array(
				'connectionString' => 'mysql:host=' . $conn_par['db_host'] . ';port=' . $conn_par['db_port'] . ';dbname=' . $conn_par['db_name'],
				'username' => $conn_par['db_username'],
				'password' => $conn_par['db_password'],
				'charset' => 'utf8'
			);
		}
		
		return self::$rep_connection;
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