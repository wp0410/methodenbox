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
    
    // die('Client user is not authenticated (1)');
    $app_err = new ErrorInfo();
    $app_err->err_last_action = 'Validate User Authentication';
    $app_err->err_number = 301;
    $app_err->err_text = 'Invalid User Session';
    $app_err->handle_fatal();
}

$usr_sess->extend();
$usr_is_authenticated = true;

$mth_search = new TeachingMethodSearcher($db_conn);

if (! empty($_POST))
{
    $mth_search->set_mth_name($_POST['mth_name']);
    $mth_search->set_mth_prep($_POST['mth_prep']);
    $mth_search->set_mth_exec($_POST['mth_exec']);
    $mth_search->set_mth_age_grp($_POST['mth_age_grp']);
    $mth_search->set_mth_topic($_POST['mth_topic']);
    $mth_search->set_mth_phase($_POST['mth_phase']);
    $mth_search->set_mth_type($_POST['mth_type']);
    $mth_search->set_mth_socform($_POST['mth_soc']);
    $mth_search->set_mth_author($_POST['mth_author']);
}
$method_list = $mth_search->get_result();
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">

        <!-- link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/bootstrap-theme.css">
        <link rel="stylesheet" href="/css/project.css" -->
        <?php FormatHelper::style_sheet_refs(); ?>
    </head>
    <body>
        <?php FormatHelper::create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ': Ergebnis der Suche'; ?></h1></div>
            <div class="row">
                <?php
                    if (count($method_list) == 0)
                    {
                        echo '<div class="alert alert-warning" role="alert">';
                        echo 'Es gibt keine Datens&auml;tze f&uuml;r die eingegebenen Suchkriterien';
                        echo '</div>';
                    }
                ?>
                <form id="r_method_form" method="post" action="/php/mth_src.php" data-toggle="validator" role="form">
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-send" value="Neue Suche">
                    </div> <!-- form-group -->
                </form>
            
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Methodenname</th>
                            <th>Zeit<br>Vorb.</th>
                            <th>Zeit<br>Durchf.</th>
                            <th>Jahr-<br>gang</th>
                            <th>Phase</th>
                            <th>Typ</th>
                            <th>Sozialform</th>
                            <th>Fachbereich</th>
                            <th>AutorIn</th>
                            <th>Beliebtheit</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($method_list as $method)
                            {
                                echo '<tr>';

                                // Column: Methodenname
                                echo '<td><button type="button" class="btn btn-light" data-toggle="popover" data-placement="right" title="Kurzbeschreibung" data-content="' . $method['mth_summary'] . '">';
                                echo $method['mth_name'];
                                echo '</button></td>';

                                // Column: Zeit Vorbereitung
                                if ($method['mth_prep_min'] == $method['mth_prep_max'])
                                {
                                    echo '<td>' . $method['mth_prep_min'] . '</td>';
                                }
                                else 
                                {
                                    echo '<td>' . $method['mth_prep_min'] . ' - ' . $method['mth_prep_max'] . '</td>';
                                }
                                
                                // Column: Zeit Durchfuehrung
                                if ($method['mth_exec_min'] == $method['mth_exec_max'])
                                {
                                    echo '<td>' . $method['mth_exec_min'] . '</td>';
                                }
                                else
                                {
                                    echo '<td>' . $method['mth_exec_min'] . ' - ' . $method['mth_exec_max'] . '</td>';
                                }
                                
                                // Column: Jahrgang
                                if ($method['mth_age_grp'] == 0)
                                {
                                    echo '<td></td>';
                                }
                                else
                                {
                                    echo '<td>' . $method['mth_age_grp'] . '</td>';
                                }
                                
                                // Column: Unterrichtsphase
                                echo '<td>';
                                $count = 0;
                                if (stristr($method['mth_phase'], 'E'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Einstieg</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_phase'], 'I'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Information</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_phase'], 'S'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Sicherung</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_phase'], 'A'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Aktivierung</span>';
                                    $count += 1;
                                }
                                echo '</td>';
                        
                                // Column: Unterrichtstyp              
                                echo '<td>';
                                $count = 0;
                                if (stristr($method['mth_type'], 'E'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Erkl&auml;rung</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_type'], 'I'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Instruktion</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_type'], 'B'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Beispiel</span>';
                                    $count += 1;
                                }
                                echo '</td>';
                        
                                // Column: Sozialform
                                echo '<td>';
                                $count = 0;
                                if (stristr($method['mth_soc_form'], 'E'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Einzelarbeit</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_soc_form'], 'G'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Gruppenarbeit</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_soc_form'], 'P'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Partnerarbeit</span>';
                                    $count += 1;
                                }
                                if (stristr($method['mth_soc_form'], 'K'))
                                {
                                    if ($count > 0) { echo '<br>'; }
                                    echo '<span class="label label-primary">Klasse</span>';
                                    $count += 1;
                                }
                                echo '</td>';
                        
                                // Column: Fachbereich
                                echo '<td>' . $method['mth_topic'] . '</td>';
                                
                                // Column: Autoring
                                echo '<td>' . $method['mth_auth_name'] . '</td>';
                                
                                // Column: Bewertung
                                echo '<td>';
                                if (($method['rtg_count'] != 0) || ($method['dld_count'] != 0))
                                {
                                    if ($method['rtg_count'] != 0)
                                    {
                                        $rtg_avg = round($method['rtg_sum'] / $method['rtg_count'], 1, PHP_ROUND_HALF_UP);
                                        //echo '    <span class="badge badge-primary">Bewertung: ' . $rtg_avg . '</span>';
                                        
                                        if ($method['rtg_count'] == 1)
                                        {
                                            echo '<span class="badge badge-primary">' . $method['rtg_count'] . ' Bewertung: ' . $rtg_avg . '</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="badge badge-primary">' . $method['rtg_count'] . ' Bewertungen: ' . $rtg_avg . '</span>';
                                        }
                                    }
                                    if ($method['dld_count'] != 0)
                                    {
                                        if ($method['rtg_count'] != 0)
                                        {
                                            echo '<br>';
                                        }
                                        
                                        if ($method['dld_count'] == 1)
                                        {
                                            echo '<span class="badge badge-primary">' . $method['dld_count'] . ' Download</span>';
                                        }
                                        else
                                        {
                                            echo '<span class="badge badge-primary">' . $method['dld_count'] . ' Downloads</span>';
                                        }
                                    }
                                }
                                echo '</td>';

                                // Column: Beschreibung
                                echo '<td>';
                                echo '<a href="/php/mth_dnl.php?fid=' . $method['mth_id'] . '&fguid=' . $method['mth_att_guid'] . '" download="' . $method['mth_att_name'] . '" class="btn btn-primary btn-sm role="button">';
                                echo '<span class="glyphicon glyphicon-file" aria-hidden="true"></span>Datei</a>';
                                echo '</td></tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div> <!-- row -->    
        </div> <!-- container -->

        <?php FormatHelper::script_refs(); ?>
        <script>
            /* global $ */
            $(function () {
                $('[data-toggle="popover"]').popover()
            })

            $('.popover-dismiss').popover({
                trigger: 'focus'
            })
        </script>
    </body>
</html>
