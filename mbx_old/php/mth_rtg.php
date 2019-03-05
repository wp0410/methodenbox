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
session_start();

include_once 'model/mdl_par.php';
include_once 'model/mdl_dbs.php';
include_once 'model/mdl_ssn.php';
include_once 'model/mdl_jnl.php';
include_once 'model/mdl_mth.php';
include_once 'model/mdl_sta.php';
include_once 'model/mdl_err.php';
include_once 'model/mdl_bs3.php';

// Check for valid user session
$usr_is_authenticated = false;
if (empty($_SESSION) || empty($_SESSION['user']))
{
    // die('Client user is not authenticated (0)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 300;
    $app_err->err_text = 'No User Session';
    $app_err->handle_fatal();
}

// $db_conn = db_connect();
$db_conn = DatabaseConnection::get_connection();
if ($db_conn == null)
{
    $app_err = DatabaseConnection::get_error();
    $app_err->handle_fatal();
}

$usr_sess = new UserSession($db_conn);
$usr_sess->load_by_id($_SESSION['user']);

if (! $usr_sess->valid())
{
    $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'SESSION.VALIDATE');
    $jnl_entry->set_jnl_result(301, 'Invalid User Session');
    $jnl_entry->set_jnl_data(json_encode($usr_sess));
    $jnl_entry->store();
    
    //die('Client user is not authenticated (1)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 301;
    $app_err->err_text = 'Invalid User Session';
    $app_err->handle_fatal();
}

$usr_sess->extend();
$usr_is_authenticated = true;

if (! empty($_POST))
{
    foreach($_POST['method'] as $mth_list)
    {
        // echo 'mth_id = ' . $mth_list['mth_id'] . '; rate_val = ' . $mth_list['rate_val'] . ';';
        
        if ($mth_list['rate_val'] != 0)
        {
            $rating = new RatingStatistics($db_conn, $usr_sess->usr_id, $mth_list['mth_id'], $mth_list['rate_val']);
            $rating->save();
        }
    }
}

$mth_search = new MethodListByDownload($db_conn, $usr_sess->usr_id);
$method_list = $mth_search->get_result();
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">
        
        <?php FormatHelper::style_sheet_refs(); ?>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="/css/star-rating.css" media="all" type="text/css">
    </head>
    <body>
        <?php FormatHelper::create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="row">
                <div class="col">
                    <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ': Unterrichtsmethoden Bewertung'; ?></h1></div>
                </div>
            </div>
            
            <form id="rating_form" method="post" action="/php/mth_rtg.php" role="form">
                <?php
                    if (count($method_list) == 0)
                    {
                        echo '<div class="row"><div class="col">';
                        echo '<div class="alert alert-warning" role="alert">';
                        echo 'Es gibt keine Unterrichtsmethoden, die sie geladen aber noch nicht bewertet haben';
                        echo '</div></div></div>';
                    }
                ?>

                <div class="row form-row">
                    <div class="col">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Methodenname</th>
                                    <th>Kurzbeschreibung</th>
                                    <th>Jahrgang</th>
                                    <th>Fachbereich</th>
                                    <th>Zuletzt geladen</th>
                                    <th>Bewertung</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $mth_index = 0;
                                    
                                    foreach($method_list as $method)
                                    {
                                        if ($method['rtg_count'] == 0)
                                        {
                                            echo '<tr>';
                                            
                                            echo '<td>' . $method['mth_name'] . '</td>';
                                            echo '<td>' . $method['mth_summary'] . '</td>';
                                            
                                            if ( $method['mth_age_grp'] == "0")
                                            {
                                                echo '<td></td>';
                                            }
                                            else
                                            {
                                                echo '<td>' . $method['mth_age_grp'] . '</td>';
                                            }
                                            echo '<td>' . $method['mth_topic'] . '</td>';
                                            echo '<td>' . substr($method['dld_last_date'], 0, 10) . '</td>';
                                            
                                            echo '<td><div class="form-group" id="mth_rate_' . $method['mth_id'] . '">';
                                            echo '<input id="rate_val_' . $mth_index . 
                                                 '" name="method[' . $mth_index . '][rate_val]" type="text" class="rating" data-min="0" data-max="5" data-step="1" data-size="xs">';
                                            echo '<input id="mth_id_' . $mth_index . 
                                                 '" name="method[' . $mth_index . '][mth_id]" type="hidden" value="' . $method['mth_id'] . '" class="form-control">';
                                            
                                            echo '</div></td>';
        
                                            echo '</tr>';
                                            
                                            $mth_index += 1;
                                        }
                                    }
                                    
                                    if ($mth_index > 0)
                                    {
                                        echo '<tr><td></td><td></td><td></td><td></td><td></td><td>';
                                        echo '<input type="submit" class="btn btn-primary btn-send" value="Bewertung abschicken">';
                                        echo '</td></tr>';
                                    }
                                    else
                                    {
                                        echo '<tr><td colspan="6"><div class="alert alert-warning" role="alert">';
                                        echo 'Es gibt keine Unterrichtsmethoden, die sie geladen aber noch nicht bewertet haben';
                                        echo '</div></td></tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div> <!-- container -->
        
        <?php FormatHelper::script_refs(); ?>
    </body>
</html>
