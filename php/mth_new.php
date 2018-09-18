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

include_once 'model/mdl_dbs.php';
include_once 'model/mdl_mth.php';
include_once 'model/mdl_ssn.php';
include_once 'model/mdl_jnl.php';
include_once 'model/mdl_par.php';
include_once 'model/mdl_err.php';
include_once 'model/mdl_bs3.php';

// Check for valid user session
$usr_is_authenticated = false;
$success = true;
$err_msg = array();

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

if (! empty($_POST))
{
    /*
    $err_msg[] = 'usr_id      = ' . $usr_sess->usr_id;
    $err_msg[] = 'mth_name    = ' . $_POST['mth_name'];
    $err_msg[] = 'mth_topic   = ' . $_POST['mth_topic'];
    $err_msg[] = 'mth_summary = ' . $_POST['mth_summary'];
    $err_msg[] = 'mth_age_grp = ' . $_POST['mth_age_grp'];
    $err_msg[] = 'mth_phase   = ' . $_POST['mth_phase'];
    $err_msg[] = 'mth_type    = ' . $_POST['mth_type'];
    $err_msg[] = 'mth_soc     = ' . $_POST['mth_soc'];
    $err_msg[] = 'mth_prep    = ' . $_POST['mth_prep_min'] . ' --- ' . $_POST['mth_prep_max'];
    $err_msg[] = 'mth_exec    = ' . $_POST['mth_exec_min'] . ' --- ' . $_POST['mth_exec_max'];
    $err_msg[] = 'mth_authors = ' . $_POST['mth_authors'];
    */
    
    $method = new TeachingMethod($db_conn, $usr_sess->usr_id);
    $method->mth_name = $_POST['mth_name'];
    $method->mth_topic = $_POST['mth_topic'];
    $method->mth_summary = $_POST['mth_summary'];
    if (! empty($_POST['mth_age_grp']))
    {
      $method->mth_age_grp = $_POST['mth_age_grp'];
    }
    
    $method->set_phase($_POST['mth_phase']);
    $method->set_type($_POST['mth_type']);
    $method->set_soc_form($_POST['mth_soc']);

    $method->mth_prep_min = (int)$_POST['mth_prep_min'];
    $method->mth_prep_max = $method->mth_prep_min;
    if (! empty($_POST['mth_prep_max']))
    {
      $method->mth_prep_max = (int)$_POST['mth_prep_max'];
    }
    
    $method->mth_exec_min = (int)$_POST['mth_exec_min'];
    $method->mth_exec_max = $method->mth_exec_min;
    if (! empty($_POST['mth_exec_max']))
    {
      $method->mth_exec_max = (int)$_POST['mth_exec_max'];
    }

    // Split list of authors into an array    
    if (! empty($_POST['mth_authors']))
    {
        $method->mth_authors = explode("<br>", nl2br($_POST['mth_authors'], false));
    }

    // Handle transferred "Methodenbeschreibung" file
    if ((! empty($_FILES)) && (! empty($_FILES['mth_file'])))
    {
        $res = $method->set_file($_FILES['mth_file']);
        if ($res['code'] != 0)
        {
            $success = false;
            $err_msg[] = $res['text'];
        }
    }
    
    /*
    $err_msg[] = 'File GUID = ' . $method->mth_description->file_guid;
    $err_msg[] = 'File NAME = ' . $method->mth_description->file_name;
    $err_msg[] = 'File TYPE = ' . $method->mth_description->file_type;
    $err_msg[] = 'File PATH = ' . $method->mth_description->file_temp_path;
    */
    
    if ($success)
    {
        $res = $method->store();
        
        if ($res['code'] != 0)
        {
            $err_msg[] = 'Fehler beim Speichern der Unterrichtsmethode';
        }
        
        $jnl_entry = new JournalEntry($db_conn, $usr_sess->usr_id, $usr_sess->usr_name, 'METHOD.CREATE');
        $jnl_entry->set_jnl_result($res['code'], $res['text']);
        $jnl_entry->set_jnl_data(json_encode($method));
        $jnl_entry->store();

        header('Location: /php/mth_new.php');
        exit;
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
        
        <?php FormatHelper::style_sheet_refs(); ?>
    </head>

    <body>
        <?php FormatHelper::create_menu($usr_is_authenticated, basename($_SERVER['PHP_SELF'])); ?>
            
        <div class="container" role="main">
            <div class="row">
                <div class="col">
                    <div class="page-header"><h1><?php echo GlobalParam::$app_config['app_title'] . ':   Neue Methode Erstellen'; ?></h1></div>
                </div>
            </div>
            <form id="c_method_form" method="post" action="/php/mth_new.php" data-toggle="validator" role="form" enctype="multipart/form-data">
                <?php
                    if ($success)
                    {
                        echo '<div class="messages"></div>';
                    }
                    else 
                    {
                        echo '<div class="messages"><div class="alert alert-danger" role="alert">';
                        foreach($err_msg as $msg)
                        {
                            echo '<p>' . $msg . '</p>';
                        }
                        echo '</div></div>';
                    }
                ?>
                
                <div class="row form-row">
                    <div class="col-md-2">
                        <h4><span class="label label-primary">Name der Methode *</span></h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group has-feedback">
                            <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Name der Methode" required />
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <h4><span class="label label-primary">Fachbereich *</span></h4>
                    </div>    
                    <div class="col-md-4">
                        <div class="form-group has-feedback">
                            <input id="mth_topic" type="text" name="mth_topic" class="form-control" placeholder="Fachbereich" required/>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                </div> <!-- class="row" -->

                <div class="row">
                    <div class="col-md-2">
                        <h4><span class="label label-primary">Beschreibung *</span></h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group has-feedback">
                            <textarea id="mth_summary" name="mth_summary" form="c_method_form" required cols="48" rows="3" maxlength="300" placeholder="Kurzbeschreibung"></textarea>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h4><span class="label label-primary">AutorInnen *</span></h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group has-feedback">
                            <textarea id="mth_authors" name="mth_authors" form="c_method_form" required cols="48" rows="3"></textarea>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                </div> <!-- class="row" -->
                
                <div class="row">
                    <div class="col-md-2">
                        <h4><span class="label label-primary">Jahrgang</span></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group has-feedback">
                            <input type="number" id="mth_age_grp" name="mth_age_grp" class="form-control" min="1" max="5">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h4> <span class="label label-primary">Vorbereitungszeit</span></h4>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group has-feedback">
                            <label for="mth_prep_min">Von *</label>
                            <input id="mth_prep_min" type="text" name="mth_prep_min" class="form-control" pattern="^[0-9]{1,}$" required />
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group has-feedback">
                            <label for="mth_prep_max">Bis</label>
                            <input id="mth_prep_max" type="text" name="mth_prep_max" pattern="^[0-9]{0,}$" class="form-control" />
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h4><span class="label label-primary">Dauer im Unterricht</span></h4>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group has-feedback">
                            <label for="mth_exec_min">Von *</label>
                            <input id="mth_exec_min" type="text" name="mth_exec_min" class="form-control" pattern="^[0-9]{1,}$" required />
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group has-feedback">
                            <label for="mth_exec_max">Bis</label>
                            <input id="mth_exec_max" type="text" name="mth_exec_max" pattern="^[0-9]{0,}$" class="form-control" />
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                </div> <!-- class="row" -->
                
                <div class="row">
                    <div class="col-md-2">
                        <h4><span class="label label-primary">Unterrichtsphase</span></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <label for="mth_phase_entry">Einstieg</label>
                                    <input id="mth_phase_entry" type="checkbox" name="mth_phase[]" value="E">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_phase_info">Information</label>
                                    <input id="mth_phase_info" type="checkbox" name="mth_phase[]" value="I">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_phase_assert">Sicherung</label>
                                    <input id="mth_phase_assert" type="checkbox" name="mth_phase[]" value="S">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_phase_activate">Aktivierung</label>
                                    <input id="mth_phase_activate" type="checkbox" name="mth_phase[]" value="A">
                                </li>
                            </ul> <!-- list-group -->
                        </div> <!-- form-group> -->
                    </div>

                    <div class="col-md-1">
                        <h4><span class="label label-primary">Typ</span></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <label for="mth_type_exp">Erkl&auml;rung</label>
                                    <input type="checkbox" id="mth_type_exp" name="mth_type[]" value="E">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_type_instr">Instruktion</label>
                                    <input type="checkbox" id="mth_type_instr" name="mth_type[]" value="I">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_type_example">Beispiel</label>
                                    <input type="checkbox" id="mth_type_example" name="mth_type[]" value="B">
                                </li>
                            </ul> <!-- list-group -->
                        </div> <!-- form-group -->
                    </div>

                    <div class="col-md-1">
                        <h4><span class="label label-primary">Sozialform</span></h4>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <label for="mth_soc_E">Einzeln</label>
                                    <input type="checkbox" id="mth_soc_E" name="mth_soc[]" value="E">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_soc_G">Gruppe</label>
                                    <input type="checkbox" id="mth_soc_G" name="mth_soc[]" value="G">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_soc_K">Klasse</label>
                                    <input type="checkbox" id="mth_soc_K" name="mth_soc[]" value="K">
                                </li>
                                <li class="list-group-item">
                                    <label for="mth_soc_P">Partner</label>
                                    <input type="checkbox" id="mth_soc_P" name="mth_soc[]" value="P">
                                </li>
                            </ul>
                        </div> <!-- form-group -->
                    </div>
                </div> <!-- class="row" -->
                
                <div class="row">
                    <div class="col-md-2">
                        <h4> <span class="label label-primary">Methodendatei *</span></h4>
                    </div>
                    
                    <div class="col-md-10">
                       <div class="form-group has-feedback">
                            <div class="input-group">
                                <label class="input-group-btn" required>
                                    <span class="btn btn-primary">
                                        Datei ausw&auml;hlen &hellip; 
                                        <input type="file" style="display: none;" id = "mth_file" name="mth_file" multiple accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx">
                                    </span>
                                </label>
                                <input type="text" class="form-control" id="mth_file_name", name="mth_file_name" required aria-describedby="mth_file">
                            </div> <!-- input-group -->
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div> <!-- form-group -->
                    </div>
                </div> <!-- class="row" -->
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group has-feedback">
                            <ul class="list-group list-inline">
                                <li class="list-group-item">
                                    <div class="checkbox">
                                        <label>
                                            <input id="usr_accept" type="checkbox" name="usr_accept" value="YES" required> 
                                            * Richtlinie f&uuml;r die Bereitstellung von Unterrichtsmaterial gelesen und akzeptiert
                                        </label>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#conditionModal">Richtlinie anzeigen</button>
                                </li>
                            </ul>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <h4><span class="text-primary">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden</span></h4>
                    </div>
                </div> <!-- class="row" -->
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group" id="c_method_submit">
                            <input type="submit" class="btn btn-primary btn-send" value="Eingabe abschlie&szlig;en und neue Unterrichtsmethode anlegen">
                        </div>
                    </div>
                </div> <!-- class="row" -->
            </form> <!-- id="c_method_form" -->            

            <div class="modal fade" id="conditionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="exampleModalLabel">Richtlinie f&uuml;r die Bereitstellung von Unterrichtsmaterial</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Hier kommt der Text f&uuml;r die Richtlinien hin ...</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Schlie&szlig;en</button>
                        </div>
                    </div> <!-- modal-content -->
                </div> <!-- modal-dialog -->
            </div> <!-- modal fade -->
        </div> <!-- class="container" -->
        <?php  FormatHelper::script_refs(); ?>
        <script>
            /* global $ */
            $(function() {
                $(document).on('change', ':file', function() {
                    var input = $(this),
                    numFiles = 1, // input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                    
                    input.trigger('fileselect', [numFiles, label]);
                });
                    
                $(document).ready( function() {
                    $(':file').on('fileselect', function(event, numFiles, label) {
                        var input = $(this).parents('.input-group').find(':text'),
                            log = numFiles > 1 ? numFiles + ' Dateien ausgew&auml;hlt' : label;
                        if (input.length) {
                            input.val(log);
                        } else {
                            if( log ) alert(log);
                        }
                    });
                });
            });
        </script>
    </body>
</html>