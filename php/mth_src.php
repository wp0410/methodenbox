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
include_once 'model/mdl_par.php';

// Check for valid user session
/*
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
*/
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
        <link href="/css/bootstrap-toggle.min.css" rel="stylesheet">
    </head>

    <body>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?php echo GlobalParam::$title; ?></a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="#">Methode Suchen</a></li>
                        <li><a href="/php/mth_new.php">Methode Erstellen</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Registrieren</a></li>
                        <li><a href="/php/usr_out.php">Abmelden</a></li>
                        <li><a href="/php/aux_hlp.php">Hilfe</a></li>
                        <li><a href="/php/aux_ctc.php">Kontakt</a></li>
                    </ul>
                </div>
            </div>
        </nav>
      
        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$title . ': Suchen'; ?></h1></div>
            <div class="row">
                <form id="s_method_form" method="post" action="/php/mth_res.php" data-toggle="validator" role="form">
                    <div class="messages"></div>
                    <div class="controls">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Methodenname</th>
                                    <th>Zeit Vorbereitung</th>
                                    <th>Zeit Durchführung</th>
                                    <th>Unterrichtsphase</th>
                                    <th>Typ</th>
                                    <th>Sozialform</th>
                                    <th>Fachbereich</th>
                                    <th>AutorIn</th>
                                    </tr>
                            </thead>
                            <tbody><!-- /tbody -->
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Methodenname">
                                        </div> <!-- form-group -->
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input id="mth_prep" type="text" name="mth_prep" class="form-control" pattern="^[0-9]{1,}$">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input id="mth_exec" type="text" name="mth_exec" class="form-control" pattern="^[0-9]{1,}$">
                                        </div>
                                    </td>
                                    <td>
                                        <!--
                                        <div class="btn-group-vertical" data-toggle="buttons">
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_phase_entry" name="mth_phase[]" value="E" type="checkbox" autocomplete="off">Einstieg
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_phase_info" name="mth_phase[]" value="I" type="checkbox" autocomplete="off"> Information
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_phase_assert" name="mth_phase[]" value="S" type="checkbox" autocomplete="off"> Sicherung
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_phase_activate" name="mth_phase[]" value="A" type="checkbox" autocomplete="off"> Aktivierung
                                            </label>
                                        </div>
                                        -->
                                        <!-- div class="btn-group-vertical"> -->
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                            <label>
                                                <input type="checkbox" data-toggle="toggle" id="mth_phase_entry" name="mth_phase[]" value="E" data-on="Einstieg" data-off="Einstieg"
                                                    data-onstyle="success" data-offstyle="default" data-size="small">
                                            </label>
                                            </li>
                                            <li class="list-group-item">
                                            <label>
                                                <input type="checkbox" data-toggle="toggle" id="mth_phase_info" name="mth_phase[]" value="I" data-on="Information" data-off="Information"
                                                    data-onstyle="success" data-offstyle="default" data-size="small">
                                            </label>
                                            </li>
                                            <li class="list-group-item">
                                            <label>
                                                <input type="checkbox" data-toggle="toggle" id="mth_phase_assert" name="mth_phase[]" value="S" data-on="Sicherung" data-off="Sicherung"
                                                    data-onstyle="success" data-offstyle="default" data-size="small">
                                            </label>
                                            </li>
                                            <li class="list-group-item">
                                            <label>
                                                <input type="checkbox" data-toggle="toggle" id="mth_phase_activate" name="mth_phase[]" value="A" data-on="Aktivierung" data-off="Aktivierung"
                                                    data-onstyle="success" data-offstyle="default" data-size="small">
                                                
                                            </label>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" data-toggle="buttons">
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_type_exp" name="mth_type[]" value="E" type="checkbox" autocomplete="off">Erkl&auml;rung
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_type_instr" name="mth_type[]" value="I" type="checkbox" autocomplete="off"> Instruktion
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_type_example" name="mth_type[]" value="B" type="checkbox" autocomplete="off"> Beispiel
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical" data-toggle="buttons">
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_soc_E" name="mth_soc[]" value="E" type="checkbox" autocomplete="off">Einzelarbeit
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_soc_G" name="mth_soc[]" value="G" type="checkbox" autocomplete="off"> Gruppenarbeit
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_soc_K" name="mth_soc[]" value="K" type="checkbox" autocomplete="off"> Klasse
                                            </label>
                                            <label class="btn btn-primary btn-xs">
                                                <input id="mth_soc_P" name="mth_soc[]" value="P" type="checkbox" autocomplete="off"> Partnerarbeit
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input id="mth_topic" type="text" name="mth_topic" class="form-control" placeholder="Fachbereich">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input id="mth_author" type="text" name="mth_author" class="form-control" placeholder="AutorIn">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-send" value="Suche Starten">
                                        </div> <!-- form-group -->
                                    </td>
                                </tr>
                            </tbody>
                        </table> <!-- table -->
                    </div> <!-- controls -->
                </form>
            </div> <!-- row -->    
        </div> <!-- container -->
    
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/bootstrap-toggle.min.js"></script>
        <script src="/js/validator.js"></script>
    </body>
</html>