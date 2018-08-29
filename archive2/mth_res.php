<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2017 Walter Pachlinger (walter.pachlinger@gmx.at)
//    
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, softwaredistributed under 
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF 
//  ANY KIND, either express or implied. See the License for the specific language 
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------
session_start();
include 'model/mdl_dbs.php';
include 'model/mdl_ssn.php';
include 'model/mdl_jnl.php';
include 'model/mdl_mth.php';
include_once 'model/mdl_par.php';
include 'frm_gen.php';

// Check for valid user session
$usr_is_authenticated = false;
if (empty($_SESSION) || empty($_SESSION['user']))
{
    die('Client user is not authenticated (0)');
}

$db_conn = db_connect();
$usr_sess = new UserSession($db_conn);
$usr_sess->load_by_id($_SESSION['user']);

if (! $usr_sess->valid())
{
    $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'SESSION.VALIDATE');
    $jnl_entry->set_jnl_result(301, 'Invalid User Session');
    $jnl_entry->set_jnl_data(json_encode($usr_sess));
    $jnl_entry->store();
    die('Client user is not authenticated (1)');
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
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/bootstrap-theme.css">
        <link rel="stylesheet" href="/css/project.css">
    </head>
    <body>
        <?php create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$title . ': Ergebnis der Suche'; ?></h1></div>
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
                            <th>Zeit Vorbereitung</th>
                            <th>Zeit Durchf&uuml;hrung</th>
                            <th>Jahrgang</th>
                            <th>Unterrichtsphase</th>
                            <th>Typ</th>
                            <th>Sozialform</th>
                            <th>Fachbereich</th>
                            <th>AutorIn</th>
                            <th>Beschreibung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $last_id = -1;
                            $last_fguid = '';
                            $last_fname = '';
                            
                            foreach($method_list as $method)
                            {
                                if ($last_id != $method['mth_id'])
                                {
                                    if ($last_id != -1)
                                    {
                                        echo '</td><td>';
                                        echo '<a href="/php/mth_dnl.php?fid=' . $last_id . '&fguid=' . $last_fguid . '" download="' . $last_fname . '" class="btn btn-primary btn-sm role="button">';
                                        echo '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Datei Laden</a>';
                                        echo '</td></tr>';
                                    }
                                    $last_id = $method['mth_id'];
                                    $last_fguid = $method['mth_att_guid'];
                                    $last_fname = $method['mth_att_name'];
                                
                                    echo '<tr>';
                                    // echo '<td>' . $method['mth_name'] . '</td>';
                                    
                                    echo '<td><button type="button" class="btn btn-light" data-toggle="popover" data-placement="right" title="Kurzbeschreibung" data-content="' . $method['mth_summary'] . '">';
                                    echo $method['mth_name'];
                                    echo '</button></td>';

                                    if ($method['mth_prep_min'] == $method['mth_prep_max'])
                                    {
                                        echo '<td>' . $method['mth_prep_min'] . '</td>';
                                    }
                                    else 
                                    {
                                        echo '<td>' . $method['mth_prep_min'] . ' - ' . $method['mth_prep_max'] . '</td>';
                                    }
                                    if ($method['mth_exec_min'] == $method['mth_exec_max'])
                                    {
                                        echo '<td>' . $method['mth_exec_min'] . '</td>';
                                    }
                                    else
                                    {
                                        echo '<td>' . $method['mth_exec_min'] . ' - ' . $method['mth_exec_max'] . '</td>';
                                    }
                                    if ($method['mth_age_grp'] == 0)
                                    {
                                        echo '<td></td>';
                                    }
                                    else
                                    {
                                        echo '<td>' . $method['mth_age_grp'] . '</td>';
                                    }
                                    // Handle enumerations for mth_phase
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
                            
                                    // Handle "Unterrichtstyp"                
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
                            
                                    // Handle "Sozialform"                
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
                            
                                    echo '<td>' . $method['mth_topic'] . '</td>';
                                    echo '<td>' . $method['mth_auth_name'];
                                }
                                else
                                {
                                    echo '<br>' . $method['mth_auth_name'];
                                }
                            }
                            
                            if ($last_id != -1)
                            {
                                echo '</td><td>';
                                echo '<a href="/php/mth_dnl.php?fid=' . $last_id . '&fguid=' . $last_fguid . '" download="' . $last_fname . '" class="btn btn-primary btn-sm role="button">';
                                echo '<span class="glyphicon glyphicon-file" aria-hidden="true"></span> Datei Laden</a>';
                                echo '</td></tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div> <!-- row -->    
        </div> <!-- container -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/validator.js"></script>
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
