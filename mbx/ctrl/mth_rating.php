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
include_once '../model/sql_connection.php';
include_once '../model/aux_parameter.php';
include_once '../model/aux_helpers.php';
include_once '../model/app_result.php';
include_once '../model/mth_stat_rating.php';
include_once '../model/app_warning.php';
include_once '../model/aux_static.php';

set_private_warning_handler();

session_start();

function outputPostParams()
{
	$result_text = '<div class="alert alert-info" role="alert"><h5>';
	$line_no = 0;
	foreach($_POST as $param_name => $param_value)
	{
		if ($line_no++ == 0)
		{
			$result_text = $result_text . '$_POST["' . $param_name . '"]: "' . $param_value . '"';
		}
		else
		{
			$result_text = $result_text . '<br><hr>$_POST["' . $param_name . '"]: "' . $param_value . '"';
		}
	}
	$result_text = $result_text . '</h5></div>';
	
	return $result_text;
}

function outputRatingModalBody()
{
	//return StaticHtmlBlock::retrieveBlockById('mth_rating.rtg_modal_bd.0');
	$stat_block = new StaticHtmlBlock();
	$stat_block->retrieveBlockById('mth_rating.rtg_modal_bd.0');
	if ($stat_block->app_result->isOK())
	{
		return $stat_block->static_text;
	}
	else
	{
		return '<div class="alert alert-danger mt-3" role="alert"><h5>' . $stat_block->$app_result->text . '</h5></div>';
	}
}

function outputRatingModalFooter()
{
	if (empty($_SESSION) || empty($_SESSION['user']) || empty($_POST['mth_id']))
	{
		$result_text = '<div class="alert alert-warning"><h5>Fehler: Notwendige Parameter nicht angegeben</h5></div>';
		$result_text = $result_text . outputPostParams();
	}
	else
	{
		$mth_id = $_POST['mth_id'];
		$usr_id = $_SESSION['user']['uid'];
		
		$result_text = 
			'<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Abbrechen</button>' .
			'<button type="button" id="rtg_save" name="rtg_save" class="btn btn-primary btn-sm" onclick="saveRating(' .
				$mth_id . ',' . $usr_id . ')">Bewertung abschicken</button>';
	}
	
	return $result_text;
}

function saveRating()
{
	if (empty($_POST['mth_id']) || empty($_POST['usr_id']) || empty($_POST['mth_rtg_val']))
	{
		$result_text = '<div class="alert alert-warning"><h5>Fehler: Notwendige Parameter nicht angegeben</h5></div>';
		$result_text = $result_text . outputPostParams();
	}
	else
	{
		if (empty($_POST['mth_rtg_comm']))
		{
			$rtg_comment = '';
		}
		else
		{	
			$rtg_comment = htmlentities($_POST['mth_rtg_comm']);
		}
		
		$mth_rtg = new MethodRating(DatabaseConnection::get_connection());
		$mth_rtg->initializeCreate($_POST['mth_id'], $_POST['usr_id']);
		$mth_rtg->rtg_date = Helpers::dateTimeString(time());
		$mth_rtg->rtg_value = $_POST['mth_rtg_val'];
		$mth_rtg->rtg_summary = $rtg_comment;
		$mth_rtg->createRating();
		
		$result_text = 
			'<div class="alert alert-success" role="alert"><h5>Vielen Dank f&uuml;r Ihre Bewertung. Sie wurde erfolgreich gespeichert.</h5></div>';
	}
	
	return $result_text;
}

if (empty($_POST))
{
	echo '<div class="alert alert-warning"><h5>Fehler: keine Parameter angegeben</h5></div>';
}
else
{
	$result_text = '';
	
	if (empty($_POST['rtg_action']))
	{
		$result_text = '<div class="alert alert-warning"><h5>Fehler: Notwendige Parameter nicht angegeben ("rtg_action")</h5></div>';
	}
	else
	{
		switch($_POST['rtg_action'])
		{
			case 'RTG_SUBMIT':
				$result_text = saveRating();
				break;
			case 'RTG_MODAL_BDY':
				$result_text = outputRatingModalBody();
				break;
			case 'RTG_MODAL_FOOTER':
				$result_text = outputRatingModalFooter();
				break;
			default:
				$result_text = outputPostParams();
				break;
		}
	}
	
	echo $result_text;
}
/*
if (empty($_POST) || empty($_POST['mth_id']) || empty($_POST['mth_rtg_val']) || ($usr_id < 0))
{
    if (empty($_POST)) echo '$_POST: EMPTY --';
    if (empty($_POST['mth_id'])) echo 'MTH_ID: EMPTY --';
    if (empty($_POST['mth_rtg_val'])) echo 'MTH_RTG_VAL: EMPTY --';
    if (empty($_POST['mth_rtg_comm'])) echo 'MTH_RTG_COMMENT: EMPTY -- ';
    if ($usr_id < 0) echo 'USR_ID: EMPTY --';
}
else
{
    $mth_rtg = new MethodRating($db_conn);
    $mth_rtg->initializeCreate($_POST['mth_id'], $usr_id);
    $mth_rtg->rtg_date = Helpers::dateTimeString(time());
    $mth_rtg->rtg_value = $_POST['mth_rtg_val'];
    $mth_rtg->rtg_summary = $_POST['mth_rtg_comm'];
    $mth_rtg->createRating();
	
	echo '<div class="alert alert-success"><h5>Vielen Dank f&uuml; Ihre Bewertung</h5></div>';
}
*/
?>