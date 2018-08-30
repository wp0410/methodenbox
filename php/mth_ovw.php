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

include 'model/mdl_ssn.php';
include 'model/mdl_dbs.php';
include 'model/mdl_jnl.php';
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
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
        <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">
        
        <!-- link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/bootstrap-theme.css" -->
        <?php style_sheet_refs(); ?>
    </head>

    <body>
        <?php create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>

        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . '     &Uumlbersicht'; ?></h1></div>
        
            <div class="row">
                <p class="lead">W&auml;hlen Sie "Methode Suchen", um Unterrichtsmethoden auszuw&auml;hlen.</p>
                <p class="lead">W&auml;hlen Sie "Methode Erstellen", um eine neue Unterrichtsmethode anzulegen.</p>
                <p class="lead">W&auml;hlen Sie "Methode Bewerten", um eine Bewertung f&uuml;r eine Unterrichtsmethode abzugeben.</p>
                <p class="lead">W&auml;hlen Sie "Methoden Verwalten", um die von Ihnen hochgeladenen Unterrichtsmethoden zu bearbeiten.</p>
            </div> <!-- row -->
        </div> <!-- container -->
    
        <!-- script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script> -->
        <?php script_refs(); ?>
    </body>
</html>