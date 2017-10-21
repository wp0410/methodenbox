<?php
  session_start();
  
  $ajaxRequest = ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
  $success = false;
  $err_msg = '';
  
  require 'database.php';
  include 'common.php';
  include 'session.php';
  
  // Check for valid user session
  if (empty($_SESSION) || empty($_SESSION['user']))
  {
    die('Client user not authenticated (0)');
  }

  $result = validate_user_session($db_conn, $_SESSION['user']);
  if (empty($result) || empty($result['status']) || !$result['status'])
  {
    die('Current user is not authenticated');
  }
  $usr_id = $result['user_id'];
  $usr_name = $result['user_name'];
  
  // If we reach this position, the user session is 
  // valid and was successfully refreshed

  if (! empty($_POST))
  {
    $mth_name = $_POST['mth_name'];
    $mth_topic = $_POST['mth_topic'];
    $mth_phase = '';
    $mth_type = '';
    $mth_soc = '';
    $mth_prep_min = 0;
    $mth_prep_max = 0;
    $mth_exec_min = 0;
    $mth_exec_max = 0;
    $fname = '';
    $fguid = '';
    $ftype = '';
    
    // We assume that all mandatory fields are set
    $success = true;
    
    if (! empty($_POST['mth_phase']))
    {
      $ph = $_POST['mth_phase'];
      $cnt = count($ph);
      for ($i = 0; $i < $cnt; $i++)
      {
        $mth_phase = $mth_phase . $ph[$i];
      }
    }
    
    if (! empty($_POST['mth_type']))
    {
      $ty = $_POST['mth_type'];
      $cnt = count($ty);
      for ($i = 0; $i < $cnt; $i++)
      {
        $mth_type = $mth_type . $ty[$i];
      }
    }
    
    if (! empty($_POST['mth_soc_E'])) { $mth_soc = 'E'; }
    if (! empty($_POST['mth_soc_P'])) { $mth_soc = 'P'; }
    if (! empty($_POST['mth_soc_K'])) { $mth_soc = 'K'; }
    if (! empty($_POST['mth_soc_G'])) { $mth_soc = 'G'; }
    
    $mth_prep_min = (int)$_POST['mth_prep_min'];
    $mth_prep_max = $mth_prep_min;
    if (! empty($_POST['mth_prep_max']))
    {
      $mth_prep_max = (int)$_POST['mth_prep_max'];
    }
    
    $mth_exec_min = (int)$_POST['mth_exec_min'];
    $mth_exec_max = $mth_exec_min;
    if (! empty($_POST['mth_exec_max']))
    {
      $mth_exec_max = (int)$_POST['mth_exec_max'];
    }

    // Split list of authors into an array    
    if (! empty($_POST['mth_author_list']))
    {
      $authors = explode(';', $_POST['mth_author_list']);
    }
    else
    {
      $err_msg = 'Die Liste der AutorInnen darf nicht leer sein';
      $success = false;
    }
    
    // Handle transferred "Methodenbeschreibung" file
    if ((! empty($_FILES)) && (! empty($_FILES['mth_file'])))
    {
      $mth_file = $_FILES['mth_file'];
      
      if ($mth_file['error'] === UPLOAD_ERR_OK)
      {
        $ftype = mime_content_type($mth_file['tmp_name']);
        $mime_types = array(
          'application/pdf' => '.pdf',
          'application/msword' => '.doc',
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
          'application/vnd.ms-excel' => '.xls',
          'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
          'application/vnd.ms-powerpoint' => '.ppt',
          'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx');
        if (! array_key_exists($ftype, $mime_types))
        {
          $err_msg = 'Die hochgeladene Datei hat den falschen Typ (' . $ftype . '). Erlaubte Typen sind .pdf, .docx, .doc, .pptx, .ppt, .xls und .xlsx';
          $success = false;
        }
        else
        {
          $fname = $mth_file['name'];
          if (strpos($fname, '/'))
          {
            $fparts = explode('/', $fname);
          }
          else
          {
            $fparts = explode('\\', $fname);
          }
          $fname = end($fparts);
          $fguid = rnd_string(32);
          $ftype = $mime_types[$ftype];
          $ftemp = $mth_file['tmp_name'];
        }
      }
      else
      {
        $err_msg = 'Hochladen der Datei (Methodenbeschreibung) fehlgeschlagen (1)';
        $success = false;
      }
    }
    else
    {
      $err_msg = 'Hochladen der Datei (Methodenbeschreibung) fehlgeschlagen (2)';
      $success = false;
    }

    /* ----- For Testing Purposes Only ------------------------------------------------
    if ($success)
    {
      $err_msg = 
        'result: {file: {name:' . $fname . '; type:' . $ftype . '}; authors: {';
      for ($cnt = 0; $cnt < count($authors); $cnt++)
      {
        $err_msg = $err_msg . $cnt . ':' . $authors[$cnt];
        if ($cnt < (count($authors) - 1))
        {
          $err_msg = $err_msg . ', ';
        }
      }
      $err_msg = $err_msg . '}; mth_author_list: {' . $_POST['mth_author_list'] . '}}';
      $success = false;
    }
    */

    if ($success)
    {
      $mth_id = -1;
      
      $mth_ins_stmt =
        'insert into ta_mth_method( ' .
          'mth_name, mth_phase, mth_prep_min, mth_prep_max, mth_exec_min, mth_exec_max, ' .
          'mth_topic, mth_type, mth_soc_form ) ' .
        'values( ?, ?, ?, ?, ?, ?, ?, ?, ? );';
      $stm3 = $db_conn->prepare($mth_ins_stmt);
      $stm3->bind_param('ssiiiisss', $mth_name, $mth_phase, $mth_prep_min, $mth_prep_max, $mth_exec_min, $mth_exec_max, $mth_topic, $mth_type, $mth_soc);
      $stm3->execute();
      $mth_id = $stm3->insert_id;
      $stm3->close();
      
      $mth_auth_ins_stmt =
        'insert into ta_mth_method_author( mth_id, mth_seq, mth_auth_name ) ' .
        'values( ?, ?, ? );';
      $stm4 = $db_conn->prepare($mth_auth_ins_stmt);
      $num_auth = count($authors);
      for ($i = 0; $i < $num_auth; $i++)
      {
        if (! empty($authors[$i]))
        {
          $stm4->bind_param('iis', $mth_id, $i, $authors[$i]);
          $stm4->execute();
        }
      }
      $stm4->close();
      
      $att_ins_stmt =
        'insert into ta_mth_method_attachment( att_mth_id, att_name, att_type, att_guid, att_data ) ' .
        'values( ?, ?, ?, ?, ? );';
      $stm5 = $db_conn->prepare($att_ins_stmt);
      $null = NULL;
      $seq = 0;
      $stm5->bind_param('isssb', $mth_id, $fname, $ftype, $fguid, $null);
      $stm5->send_long_data(4, file_get_contents($ftemp));
      $stm5->execute();
      $stm5->close();

      header('Location: /php/create_method.php');
      exit;
    }
  }
  else
  {
    $success = true;
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
          <a class="navbar-brand" href="#"><?php echo $global_title; ?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/php/search_method.php">Methode Suchen</a></li>
            <li><a href="#">Methode Erstellen</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Registrieren</a></li>
            <li><a href="#">Anmelden</a></li>
            <li><a href="/php/help.php">Hilfe</a></li>
            <li><a href="/php/contact.php">Kontakt</a></li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div class="container" role="main">
      <div class="page-header"><h1><?php echo $global_title . '     Neue Methode Erstellen'; ?></h1></div>

      <div class="row">
        <form id="c_method_form" method="post" action="/php/create_method.php" data-toggle="validator" role="form" enctype="multipart/form-data">
<?php
  if ($success)
  {
    echo '<div class="messages"></div>';
  }
  else 
  {
    echo '<div class="messages">';
    echo '<div class="alert alert-danger" role="alert">' . $err_msg . '</div>';
    echo '</div>';
  }
?>
          
          <div class="controls">
            <!-- Submit Button -->
            <div class="col-md-12">
              <ul class="list-inline">
                <li>
                  <div class="form-group" id="c_method_submit">
                    <input type="submit" class="btn btn-primary btn-send" value="Eingabe abschließen und neue Methode anlegen">
                  </div>
                </li>
                <li>
                  <div class="form-group">
                    <p class="text-muted"><strong>*</strong>Pflichtfelder</p>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Methodenname -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="mth_name">Methodenname *</label>
                <input id="mth_name" type="text" name="mth_name" class="form-control" 
                <?php if ($success || empty($mth_name)) { echo ' placeholder="Name der Methode"'; } else { echo ' value="' . $mth_name . '"'; } ?> required>
                <!-- div class="help-block with-errors"></div> -->
              </div>
            </div> <!-- col-md-6 -->
            
            <!-- Fachbereich -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="mth_topic">Fachbereich</label>
                <input id="mth_topic" type="text" name="mth_topic" class="form-control"
                <?php if ($success || empty($mth_topic)) { echo ' placeholder="Fachbereich"'; } else { echo ' value="' . $mth_topic . '"'; } ?> >
                <!-- div class="help-block with-errors"></div> -->
              </div>
            </div> <!-- col-md-6 -->
            
            <!-- Preparation Time -->
            <div class="col-md-6">
              <ul class="list-unstyled">
                <li><label>Zeit Vorbereitung Lehrperson</label></li>
                <li>
                  <ul class="list-inline">
                    <li>
                      <div class = "form-group">
                        <label for="mth_prep_min">Von *</label>
                        <input id="mth_prep_min" type="text" name="mth_prep_min" class="form-control" pattern="^[0-9]{1,}$" required
                        <?php if (! $success && ! empty($mth_prep_min)) { echo ' value="' . $mth_prep_min . '"'; } ?>
                        >
                        <!-- div class="help-block with-errors"></div> -->
                      </div>
                    </li>
                    <li>
                      <div class = "form-group">
                        <label for="mth_prep_max">Bis</label>
                        <input id="mth_prep_max" type="text" name="mth_prep_max" pattern="^[0-9]{0,}$" class="form-control"
                        <?php if (! $success && ! empty($mth_prep_max)) { echo ' value="' . $mth_prep_max . '"'; } ?>
                        >
                        <!-- div class="help-block with-errors"></div> -->
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </div> <!-- col-md-6 -->
            
            <!-- Execution Time -->
            <div class="col-md-6">
              <ul class="list-unstyled">
                <li><label>Zeit Durchführung im Unterricht</label></li>
                <li>
                  <ul class="list-inline">
                    <li>
                      <div class = "form-group">
                        <label for="mth_exec_min">Von *</label>
                        <input id="mth_exec_min" type="text" name="mth_exec_min" class="form-control" pattern="^[0-9]{1,}$" required
                        <?php if (! $success && ! empty($mth_exec_min)) { echo ' value="' . $mth_exec_min . '"'; } ?>
                        >
                        <!-- div class="help-block with-errors"></div> -->
                      </div>
                    </li>
                    <li>
                      <div class = "form-group">
                        <label for="mth_exec_max">Bis</label>
                        <input id="mth_exec_max" type="text" name="mth_exec_max" pattern="^[0-9]{0,}$" class="form-control"
                        <?php if (! $success && ! empty($mth_exec_max)) { echo ' value="' . $mth_exec_max . '"'; } ?>
                        >
                        <!-- div class="help-block with-errors"></div> -->
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </div> <!-- col-md-6 -->
            
            <!-- Unterrichtsphase -->
            <div class="col-md-2"> 
              <div class="form-group">
                <ul class="list-group">
                  <li class="list-group-item active">Unterrichtsphase</li>
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
            </div> <!-- col-md-2 -->

            <!-- Typ -->
            <div class="col-md-2"> 
              <div class="form-group">
                <ul class="list-group">
                  <li class="list-group-item active">Typ</li>
                  <li class="list-group-item">
                    <label for="mth_type_exp">Erklärung</label>
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
            </div> <!-- col-md-2 -->

            <!-- Sozialform -->
            <div class="col-md-2">
              <div class="form-group">
                <ul class="list-group">
                  <li class="list-group-item active">Sozialform *</li>
                  <li class="list-group-item">
                    <label for="mth_soc_E">Einzelarbeit</label>
                    <input type="radio" id="mth_soc_E" name="mth_soc_E" value="Einzelarbeit" checked>
                  </li>
                  <li class="list-group-item">
                    <label for="mth_soc_G">Gruppenarbeit</label>
                    <input type="radio" id="mth_soc_G" name="mth_soc_G" value="Gruppenarbeit">
                  </li>
                  <li class="list-group-item">
                    <label for="mth_soc_K">Klasse</label>
                    <input type="radio" id="mth_soc_K" name="mth_soc_K" value="Klasse">
                  </li>
                  <li class="list-group-item">
                    <label for="mth_soc_P">Partnerarbeit</label>
                    <input type="radio" id="mth_soc_P" name="mth_soc_P" value="Partnerarbeit">
                  </li>
                </ul>
              </div> <!-- form-group -->
            </div><!-- col-md-2 -->
            
            <!-- File Selection -->
            <!-- div class="col-lg-6 col-sm-6 col-12"> -->
            <div class="col-md-6">
              <div class="form-group">
                <label>Methodenbeschreibung *</label>
                <div class="input-group">
                  <label class="input-group-btn" required>
                    <span class="btn btn-primary">
                      Datei auswählen &hellip; 
                      <input type="file" style="display: none;" id = "mth_file" name="mth_file" multiple accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx">
                    </span>
                  </label>
                  <input type="text" class="form-control" id="mth_file_name", name="mth_file_name" readonly aria-describedby="mth_file">
                </div> <!-- input-group -->
              </div> <!-- form-group -->
            </div> <!-- col-lg-6 col-sm-6 col-12 -->

            <input type="hidden" name="mth_author_list" id="mth_author_list" value="">
            
          </div> <!-- controls -->
        </form> <!-- form -->

        <!-- Author List -->
        <div class="col-md-4">
          <div class="form-group">
            <ul class="list-inline">
              <li>
                <label for="mth_auth">AutorInnen</label>
                <input id="mth_auth" type="text" name="mth_auth" class="form-control" onfocus="clearField(this)">
              </li>
              <li>
                <button id="author-add" class="btn btn-default" formnovalidate onclick="addAuthor()">
                  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Hinzufügen
                </button>
              </li>
            </ul> <!-- list-inline -->
            <ul class="list-group" id="author-list">
              <li class="list-group-item active">Liste der AutorInnen *</li>
            </ul> <!-- list-group -->
          </div> <!-- form-group -->
        </div> <!-- col-md-4 -->

      </div> <!-- row -->
    </div> <!-- container -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/validator.js"></script>
    <script>
      /* global $ */
      function addAuthor() {
        var author = $('#mth_auth').val();
        var author_list = $('#mth_author_list').val();

        if (author.length > 0) {
          author_list = author_list + author + ';';
          $('#author-list').append("<li class='list-group-item'>".concat(author, "</li>"));
          $('#mth_author_list').val(author_list);
        }
        //alert(author_list + ' *** ' + $('#mth_author_list').val());
      }
    </script>
    <script>
      /* global $ */
      function clearField(target) {
        target.value = "";
      }
    </script>
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
                log = numFiles > 1 ? numFiles + ' Dateien ausgewählt' : label;
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