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
include_once '../model/app_warning.php';
include_once '../model/sql_connection.php';
include_once '../model/usr_permission.php';

set_private_warning_handler();
session_start();

if (! empty($_POST) && ! empty($_POST['usr_role']) && ! empty($_POST['usr_id']))
{
    $current_role = $_POST['usr_role'];
    $usr_id = $_POST['usr_id'];
    
    echo '<div class="row form-row"><div class="col">';
    echo '   <div id="usrPermChangeMsg" name="usrPermChangeMsg"></div>';
    echo '</div></div>';
    
    echo '<div class="row form-row">';
	echo '   <div class="col">';
	echo '   	<table class="table">';
	echo '		   <thead class="thead-dark"><tr>';
	echo '            <th></th>';
	echo '		      <th scope="col">Rolle</th>';
	echo '            <th scope="col">Berechtigungen</th>';
	echo '		   </tr></thead>';
	echo '         <tbody>';
	
	$perm_schema = new UserPermissionSchema(DatabaseConnection::get_connection());
	$role_list = $perm_schema->getAllRoles();
	
	$cnt = 1;
	foreach($role_list as $role)
	{
		echo '<tr><td><div class="form-check">';
		echo '<input class="form-check-input" type="radio" name="roles[]" id="role_' . $cnt++ . '" value="' . $role['role_name'] . '" ';
		if ($role['role_name'] == $_POST['usr_role'])
		{
			echo ' disabled';
		}
		echo '></td><td>';
		
		echo '<h2><span class="badge badge-light"><i class="fa ' . $role['role_symbol'] . '" aria-hidden="true"></i>&nbsp;&nbsp;';
		echo $role['role_name'] . ' (' . $role['role_description'] . ')</span></h2>';
		echo '</td><td>';
		
		$perm_list = $perm_schema->getRolePrivileges($role['role_name']);
		
		foreach($perm_list as $perm)
		{
			echo '<h6><span class="badge badge-primary">' . $perm['perm_description'] . '</span></h6>';
		}
		
		echo '</td></tr>';
	}
	
	echo '         </tbody>';
	echo '		</table>'; // table
	echo '   </div>';      // col
    echo '</div>';         // row
}
?>