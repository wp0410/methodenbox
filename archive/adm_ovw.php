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

include 'model/mdl_ssn.php';
include 'model/mdl_dbs.php';
include 'model/mdl_par.php';
include 'model/mdl_usr.php';

// Check for valid user session with role 'ADMIN'
/*--
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
if (! $usr_sess->validate_admin())
{
    $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'SESSION.VALIDATE');
    $jnl_entry->set_jnl_result(301, 'User Account does not have role "ADMIN"');
    $jnl_entry->set_jnl_data(json_encode($usr_sess));
    $jnl_entry->store();
    die('Client user is not authenticated (2)');
}
$usr_sess->extend();
--*/

$usr_list = NULL;
if (!empty($_POST))
{
    $db_conn = db_connect();
    $usr_src = new UserAccountSearcher($db_conn);
    $usr_src->set_fstname($_POST['usr_fst_name']);
    $usr_src->set_lstname($_POST['usr_lst_name']);
    $usr_src->set_email($_POST['usr_email']);
    $usr_src->set_locked($_POST['usr_state_lck']);
    
    $usr_list = $usr_src->get_result();
}
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
                </div> <!-- navbar-header -->
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="#">M1</a></li>
                        <li><a href="#">M2</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Registrieren</a></li>
                        <li><a href="/php/mth_ovw.php">Administration Beenden</a></li>
                        <li><a href="#">Hilfe</a></li>
                        <li><a href="#">Kontakt</a></li>
                    </ul>
                </div> <!-- navbar -->
            </div> <!-- container-fluid -->
        </nav>
        
        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$title . ': Registrierte Benutzer Suchen'; ?></h1></div>
            <div class="row">
                <form id="s_method_form" method="post" action="/php/mth_res.php" data-toggle="validator" role="form">
                    <div class="messages"></div>
                    <div class="controls">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Vorname</th>
                                    <th>Familienname</th>
                                    <th>E-Mail Adresse</th>
                                    <th>Gesperrt</th>
                                    <th>Letzte Anmeldung</th>
                                    <th>Anzahl ungültig</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input id="usr_fst_name" type="text" name="usr_fst_name" class="form-control" placeholder="Vorname">
                                        </div> <!-- form-group -->
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input id="usr_lst_name" type="text" name="usr_lst_name" class="form-control" placeholder="Familienname">
                                        </div> <!-- form-group -->
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input id="usr_email" type="text" name="usr_email" class="form-control" placeholder="E-Mail Adresse">
                                        </div> <!-- form-group -->
                                    </td>
                                    <td>
                                        <!-- div class="btn-group-vertical" data-toggle="buttons"> -->
                                        <div class="checkbox-inline">
                                            <!-- label class="btn btn-primary btn-sm"> -->
                                            <label>
                                                <!-- input id="usr_state_lck" name="usr_state[]" value="LOCKED" type="checkbox" autocomplete="off"> -->
                                                <input type="checkbox" data-toggle="toggle" id="usr_state_lck" name="usr_state" value="LOCKED" data-on="Ja" data-off="Nein"
                                                    data-onstyle="success" data-offstyle="warning">
                                                <!-- Gesperrt -->
                                            </label>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-send" value="Suche Starten">
                                        </div> <!-- form-group -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> <!-- controls -->
                </form>
            </div> <!-- row -->
            <div class="row">
                
            </div> <!-- row -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/bootstrap-toggle.min.js"></script>
    </body>
</html>