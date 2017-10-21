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
include 'model/mdl_mth.php';
include 'model/mdl_ssn.php';
include 'model/mdl_jnl.php';
include_once 'model/mdl_par.php';

// Check for valid user session
if (empty($_SESSION) || empty($_SESSION['user']))
{
    die('Client user is not authenticated (0)');
}

$success = true;
$err_msg = array();
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

if (! empty($_POST))
{
    $method = new TeachingMethod($db_conn);
    $method->mth_name = $_POST['mth_name'];
    $method->mth_topic = $_POST['mth_topic'];
    
    $method->set_phase($_POST['mth_phase']);
    $method->set_type($_POST['mth_type']);
    $method->mth_socform = $_POST['mth_soc'];

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

    if ($success)
    {
        $res = $method->store();
        
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
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/bootstrap-theme.css">
        <link rel="stylesheet" href="/css/project.css">
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
                        <li><a href="/php/mth_src.php">Methode Suchen</a></li>
                        <li><a href="#">Methode Erstellen</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">Registrieren</a></li>
                        <li><a href="/php/usr_out.php">Abmelden</a></li>
                        <li><a href="/php/aux_hlp.php">Hilfe</a></li>
                        <li><a href="/php/aux_ctc.php">Kontakt</a></li>
                    </ul>
                </div>
            </div> <!-- container-fluid -->
        </nav>
    
        <div class="container" role="main">
            <div class="page-header"><h1><?php echo GlobalParam::$title . ':   Neue Methode Erstellen'; ?></h1></div>
        
            <div class="row">
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

                    <div class="controls">
                        <div class="col-md-12">
                        
                            <!-- Methodenname -->
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">Methodenname *</span></h4></li>    
                                    <li class="col-md-5">
                                        <input id="mth_name" type="text" name="mth_name" class="form-control" placeholder="Name der Methode" required />
                                    </li>
                                    <li>
                                        <div class="help-block with-errors"></div>
                                    </li>
                                </ul>
                            </div>
                        
                            <!-- Fachbereich -->
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">Fachbereich</span></h4></li>    
                                    <li class="col-md-5">
                                        <input id="mth_topic" type="text" name="mth_topic" class="form-control" placeholder="Fachbereich" />
                                    </li>
                                    <li>
                                        <div class="help-block with-errors"></div>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Preparation Time -->
                            <ul class="list-group list-inline">
                                <li class="col-md-3"><h4><span class="label label-primary">Zeit Vorbereitung Lehrperson</span></h4></li>
                                <li>
                                    <div class="form-group">
                                        <label for="mth_prep_min">Von *</label>
                                        <input id="mth_prep_min" type="text" name="mth_prep_min" class="form-control" pattern="^[0-9]{1,}$" required />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-group">
                                        <label for="mth_prep_max">Bis</label>
                                        <input id="mth_prep_max" type="text" name="mth_prep_max" pattern="^[0-9]{0,}$" class="form-control" />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </li>
                            </ul>
                            
                            <!-- Execution Time -->
                            <ul class="list-group list-inline">
                                <li class="col-md-3"><h4><span class="label label-primary">Zeit Durchf&uuml;hrung im Unterricht</span></h4></li>
                                <li>
                                    <div class = "form-group">
                                        <label for="mth_exec_min">Von *</label>
                                        <input id="mth_exec_min" type="text" name="mth_exec_min" class="form-control" pattern="^[0-9]{1,}$" required />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </li>
                                <li>
                                    <div class = "form-group">
                                        <label for="mth_exec_max">Bis</label>
                                        <input id="mth_exec_max" type="text" name="mth_exec_max" pattern="^[0-9]{0,}$" class="form-control" />
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </li>
                            </ul>
                            
                            <!-- Unterrichtsphase -->
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">Unterrichtsphase</span></h4></li>
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
                            
                            <!-- Typ -->
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">Typ</span></h4></li>
                                    <li class="list-group-item">
                                        <label for="mth_type_exp">Erkl&auml;rung</label>
                                        <input id="mth_type_exp" type="checkbox" name="mth_type[]" value="E">
                                    </li>
                                    <li class="list-group-item">
                                        <label for="mth_type_instr">Instruktion</label>
                                        <input id="mth_type_instr" type="checkbox" name="mth_type[]" value="I">
                                    </li>
                                    <li class="list-group-item">
                                        <label for="mth_type_example">Beispiel</label>
                                        <input id="mth_type_example" type="checkbox" name="mth_type[]" value="B">
                                    </li>
                                </ul> <!-- list-group -->
                            </div> <!-- form-group -->
                            
                            <!-- Sozialform -->
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">Sozialform*</span></h4></li>
                                    <li class="list-group-item">
                                        <label for="mth_soc_E">Einzelarbeit</label>
                                        <input type="radio" id="mth_soc_E" name="mth_soc" value="E" checked>
                                    </li>
                                    <li class="list-group-item">
                                        <label for="mth_soc_G">Gruppenarbeit</label>
                                        <input type="radio" id="mth_soc_G" name="mth_soc" value="G">
                                    </li>
                                    <li class="list-group-item">
                                        <label for="mth_soc_K">Klasse</label>
                                        <input type="radio" id="mth_soc_K" name="mth_soc" value="K">
                                    </li>
                                    <li class="list-group-item">
                                        <label for="mth_soc_P">Partnerarbeit</label>
                                        <input type="radio" id="mth_soc_P" name="mth_soc" value="P">
                                    </li>
                                </ul>
                            </div> <!-- form-group -->
                            
                            <!-- File Selection -->
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">Methodenbeschreibung *</span></h4></li>
                                    <li>
                                        <div class="input-group col-md-8">
                                            <label class="input-group-btn" required>
                                                <span class="btn btn-primary">
                                                    Datei ausw&auml;hlen &hellip; 
                                                    <input type="file" style="display: none;" id = "mth_file" name="mth_file" multiple accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx">
                                                </span>
                                            </label>
                                            <input type="text" class="form-control" id="mth_file_name", name="mth_file_name" required aria-describedby="mth_file">
                                        </div> <!-- input-group -->
                                    </li>
                                    <li>
                                        <div class="help-block with-errors"></div>
                                    </li>
                                </ul>
                            </div> <!-- form-group -->
                            
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li class="col-md-3"><h4><span class="label label-primary">AutorInnen *</span></h4></li>
                                    <li>
                                        <textarea id="mth_authors" name="mth_authors" form="c_method_form" required cols="64"></textarea>
                                    </li>
                                    <li>
                                        <div class="help-block with-errors"></div>
                                    </li>
                                </ul>
                            </div> <!-- form-group -->
                            
                            <div class="form-group">
                                <ul class="list-group list-inline">
                                    <li>
                                        <div class="checkbox">
                                            <label>
                                                <input id="usr_accept" type="checkbox" name="usr_accept" value="YES" required> 
                                                * Richtlinie f&uuml;r die Bereitstellung von Unterrichtsmaterial gelesen und akzeptiert
                                            </label>
                                        </div>
                                    </li>
                                    <li>
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#conditionModal">Richtlinie anzeigen</button>
                                    </li>
                                </ul>
                            </div> <!-- form-group -->
                            
                            <!-- Submit Button -->
                            <ul class="list-inline">
                                <li>
                                    <div class="form-group" id="c_method_submit">
                                        <input type="submit" class="btn btn-primary btn-send" value="Eingabe abschlie&szlig;en und neue Methode anlegen">
                                    </div>
                                </li>
                                <li>
                                    <div class="form-group">
                                        <p class="text-muted"><strong>*</strong>Pflichtfelder</p>
                                    </div>
                                </li>
                            </ul>
                        </div> <!-- col-md12 -->
                    </div> <!-- controls -->
                </form> <!-- form -->
            </div> <!-- row -->
        
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
        </div> <!-- container -->
    
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/validator.js"></script>
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
                            log = numFiles > 1 ? numFiles + ' Dateien ausgew&aumlhlt' : label;
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