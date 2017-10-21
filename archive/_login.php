<?php
  session_start();
  
  require './database.php';
  include './common.php';
  
  $error_id = 0;
  $error_jnltxt = "";
  $error_txt = "";
  
  if (! empty($_POST))
  {
    $usr_id = null;
    $usr_email = null;
    $usr_pwd = null;
    $usr_salt = 0;
    $usr_locked = -1;
    $usr_invlogin = 0;
    
    $jnl_time = strftime('%Y-%m-%d %H:%M:%S', time());
    $jnl_act = 'LOGIN';
    $jnl_txt = 'Login Successful';
    
    if (! empty($_POST['email']))
    {
      $usr_email = $_POST['email'];
      
      if (! empty($_POST['password']))
      {
        $login_sel_stmt = 
            'SELECT usr_id, usr_email, usr_pwd, usr_salt, usr_locked, usr_numinvlogin ' .
            'FROM   ta_sec_user ' .
            'WHERE  usr_email = ?;';
        $stmt = $db_conn->prepare($login_sel_stmt);
        $stmt->bind_param('s', $usr_email);
        $stmt->execute();
        $stmt->bind_result($usr_id, $usr_email, $usr_pwd, $usr_salt, $usr_locked, $usr_invlogin);
        
        if ($stmt->fetch())
        {
          $stmt->free_result();
          $stmt->close();
          
          if ($usr_locked != 0)
          {
            $pwd = hash_pwd($_POST['password']);
            if ($pwd == $usr_pwd)
            {
              $usr_invlogin = 0;
              
              $_SESSION['user'] = array('ID' => $usr_id, 'NAME' => $_POST['email']);
              $error_id = 0;
              $jnl_txt = 'Successful Login';
            }
            else
            {
              $jnl_txt = 'Invalid Login: Invalid password for account [' . $usr_email . ']';
              $usr_invlogin += 1;
              if ($usr_invlogin >= 3)
              {
                $usr_locked = 1;
                $jnl_txt = $jnl_txt . '; Locked Account';
              }
              
              $error_id = 5;
              $error_txt = 'Anmeldung fehlgeschlagen. E-Mail Adresse oder Passwort falsch eingegeben';
            }
            
            $login_usr_update =
              'UPDATE ta_sec_user ' .
              '   SET usr_lastlogin = ?, ' .
              '       usr_numinvlogin = ? ' .
              ' WHERE usr_id = ? ';
            $stm2 = $db_conn->prepare($login_usr_update);
            $stm2->bind_param('sii', $jnl_time, $usr_invlogin, $usr_id);
            $stm2->execute();
            $stm2->close();
          }
          else
          {
            $error_id = 4;
            $error_txt = 'Anmeldung fehlgeschlagen. E-Mail Adresse oder Passwort falsch eingegeben';
            $jnl_txt = 'Invalid Login: Account [' . $usr_email . '] is locked';
          }
        }
        else 
        {
          $error_id = 3;
          $error_txt = 'Anmeldung fehlgeschlagen. E-Mail Adresse oder Passwort falsch eingegeben';
          $jnl_txt = 'Invalid Login: Account [' . $usr_email . '] does not exist';
        }
      }
      else
      {
        $error_id = 2;
        $error_txt = 'Sie haben ein leeres Passwort eingegeben. Bitte geben Sie Ihr Passwort ein';
        $jnl_txt = 'Invalid Login: Password is empty';
      }
    }
    else
    {
      $error_id = 1;
      $error_txt = 'Sie haben keine E-Mail Adresse angegeben. Bitte geben Sie die E-Mail Adresse an, mit der Sie die Registrierung durchgeführt haben';
      $jnl_txt = 'Invalid Login: E-Mail address is empty';
    }
    
    $log_ins_stmt =
      'INSERT INTO ta_jnl_sec_user( jnl_created, jnl_usr_id, jnl_usr_name, jnl_action, jnl_result, jnl_text ) ' .
      'VALUES( ?, ?, ?, ?, ? );';
    $logstm = $db_conn->prepare($log_ins_stmt);
    $logstm->bind_param('sissis', $jnl_time, $usr_id, $emailaddr, $jnl_act, $error_id, $jnl_txt);
    $logstm->execute();
    $logstm->close();
  }
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="description" content="Ilse Pachlinger: Sammlung von Unterrichtsmethoden">
    <meta name="author" content="Walter Pachlinger (walter.pachlinger@gmx.at)">

    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/bootstrap-theme.css">
  </head>

  <body>
    <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Ilses Methodenbox</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/php/help.php">Hilfe</a></li>
            <li><a href="/php/contact.php">Kontakt</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    
    <div class="container" role="main">
      <div class="page-header">
        <h1>Ilses Methodenbox</h1>
      </div>

      <div class="row">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h2 class="panel-title">Anmelden</h2>
          </div>
          <div class="panel-body">
            <div class="col-md-5">
              <?php if ($error_id > 0) { echo '<h2><span class="label label-danger">' . $error_txt . '</span></h2>'; } ?>
              <form id="frmLogin" method="post" action="/php/login.php">
                 <div class="form-group" id="frmGrpEmail">
                   <label for="email" class="control-label">E-Mail Adresse</label>
                   <input type="text" id="email" class="form-control"  placeholder="E-Mail Adresse">
                 </div>
                <div class="form-group" id="frmGrpPassword">
                  <label for="password" class="control-label">Passwort</label>
                  <input type="password" id="password" class="form-control">
                </div>
                <div class="form-group text-right">
                  <button type="submit" id="loginBtn" class="btn btn-primary btn-lg" active>Anmelden</button>
                  <!-- <a href="/html/index.html" id="continueBtn" class="btn btn-primary btn-lg disabled" role="button" aria-disabled="true">Weiter</a> -->
                </div>
                
              </form>
              
            </div><!-- col-md-5 -->
          </div><!-- panel-body -->          
        </div><!-- panel -->
      </div><!-- row -->
    </div><!-- container -->
    
  </body>  
</html>

  
