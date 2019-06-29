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

if (empty($_POST) || empty($_POST['hlp_topic']))
{
	$help_contents = '<div class="alert alert-primary mt-4 pl-5 pr-5"><h3>Das Hilfethema konnte nicht gefunden werden</h3></div>';
}

$static_file = '../static/aux_help_' . $_POST['hlp_topic'] . '.html';

if (file_exists($static_file))
{
	$help_contents = file_get_contents($static_file);
	if ($help_contents === false)
	{
		$help_contents = '<div class="alert alert-primary mt-4 pl-5 pr-5"><h3>Das Hilfethema "' . $_POST['hlp_topic'] . '" konnte nicht geladen werden</h3></div>';
	}
}
else
{
	$help_contents = '<div class="alert alert-primary mt-4 pl-5 pr-5"><h3>Das Hilfethema "' . $_POST['hlp_topic'] . '" konnte nicht gefunden werden</h3></div>';
}

echo $help_contents;
?>