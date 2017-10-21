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
include 'model/mdl_jnl.php';
include_once 'model/mdl_par.php';

$authenticated = false;
$auth_admin = false;
$route_prev = array();

if (empty($_SESSION))
{
    $authenticated = false;
    $route_prev = '';
}
else
{
    if (empty($_SESSION['user']))
    {
        $authenticated = false;
    }
    else
    {
        $db_conn = db_connect();
        $usr_sess = new UserSession($db_conn);
        $usr_sess->load_by_id($_SESSION['user']);
        if (! $usr_sess->valid())
        {
            $authenticated = false;
            
            $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'SESSION.VALIDATE');
            $jnl_entry->set_jnl_result(301, 'Invalid User Session');
            $jnl_entry->set_jnl_data(json_encode($usr_sess));
            $jnl_entry->store();
        }
        else
        {
            $usr_id = $usr_sess->usr_id;
            $usr_name = $usr_sess->usr_name;
            $authenticated = true;
            $auth_admin = $usr_sess->validate_admin();
            $usr_sess->extend();
        }
    }
    
    if (! empty($_SESSION['route_prev']))
    {
        $route_prev = $_SESSION['route_prev'];
        unset($_SESSION['route_prev']);
    }
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
                        <?php
                            if ($authenticated)
                            {
                                echo '<li><a href="/php/mth_src.php">Methode Suchen</a></li>';
                                echo '<li><a href="/php/mth_new.php">Methode Erstellen</a></li>';
                                
                                if ($auth_admin)
                                {
                                    echo '<li><a href="/php/adm_ovw.php">Administration</a></li>';
                                }
                            }
                            else 
                            {
                                echo '<li><a href="#">Methode Suchen</a></li>';
                                echo '<li><a href="#">Methode Erstellen</a></li>';
                            }
                        ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?php
                            if ($authenticated) 
                            {
                                echo '<li><a href="#">Registrieren</a></li>';
                                echo '<li><a href="/php/usr_out.php">Abmelden</a></li>';
                            }
                            else 
                            {
                                echo '<li><a href="/php/usr_new.php">Registrieren</a></li>';
                                echo '<li><a href="/php/usr_lin.php">Anmelden</a></li>';
                            }
                        ?>
                        <li><a href="/php/aux_hlp.php">Hilfe</a></li>
                        <li><a href="/php/aux_ctc.php">Kontakt</a></li>
                    </ul>
                </div> <!-- navbar -->
            </div> <!-- container-fluid -->
        </nav>
    
        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$title . '     &Uumlbersicht'; ?></h1></div>
        
            <div class="row">
                <?php
                    if (! empty($route_prev))
                    {
                        switch ($route_prev['result']) 
                        {
                        case 0:
                            echo '<div class="alert alert-success" role="alert">' . $route_prev['msg'] . '</div>';
                            break;
                        case 1:
                            echo '<div class="alert alert-warning" role="alert">' . $route_prev['msg'] . '</div>';
                            break;
                        case 2:
                            echo '<div class="alert alert-danger" role="alert">' . $route_prev['msg'] . '</div>';
                            break;
                        default:
                            break;
                        } 
                    }
                ?>
                <hr>
                <?php
                    if ($authenticated)
                    {
                        echo '<p class="lead">W&auml;hlen Sie "Methode Suchen", um Unterrichtsmethoden auszuw&auml;hlen.</p>';
                        echo '<p class="lead">W&auml;hlen Sie "Methode Erstellen", um eine neue Unterrichtsmethode anzulegen</p>';
                        
                        if ($auth_admin)
                        {
                            echo '<p class="lead">W&auml;hlen Sie "Administration", um Verwaltungst&auml;tigkeiten auszuf&uuml;hren</p>';
                        }
                    }
                    else
                    {
                        echo '<p class="lead">';
                        echo 'Sie m&uuml;ssen sich anmelden, um auf den Inhalt der Methodenbox zugreifen zu k&ouml;nnen. ' .
                             'Wenn Sie noch keine Anmeldedaten haben, m&uuml;ssen Sie die Registrierung durchf&uuml;hren.';
                        echo '</p>';
                    }
                ?>
            </div> <!-- row -->
        </div> <!-- container -->
    
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
    </body>
</html>