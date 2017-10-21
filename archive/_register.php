<?php
  session_start();
  
  $fst_name = '';
  $lst_name = '';
  $emailaddr = '';
  $password = '';
  $pwdconf = '';
  
  require './database.php';
  include './common.php';
  
  $success = false;
  
  if (! empty($_POST))
  {
    if (!empty($_POST['vorname']) && !empty($_POST['nachname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['pwdconf']))
    {
      $fst_name = $_POST['vorname'];
      $lst_name = $_POST['nachname'];
      $emailaddr = $_POST['email'];
      $password = $_POST['password'];
      $pwdconf = $_POST['pwdconf'];
      
      if($password == $pwdconf)
      {
        $usr_id = -1;
        $stmt = $db_conn->prepare('SELECT usr_id FROM ta_sec_user WHERE usr_email = ?;');
        $stmt->bind_param("s", $emailaddr);
        $stmt->execute();
        $stmt->bind_result($usr_id);
        if (! $stmt->fetch())
        {
          $usr_id = -1;
        }
        $stmt->free_result();
        $stmt->close();
        
        $log_ins_stmt =
          'INSERT INTO ta_jnl_sec_user( jnl_created, jnl_usr_id, jnl_usr_name, jnl_action, jnl_text ) ' .
          'VALUES( ?, ?, ?, ?, ? );';
        $logstm = $db_conn->prepare($log_ins_stmt);
        
        if ($usr_id > 0)
        {
          // We have to handle the situation that a user with the given e-mail address already exists. 
          // We lock this account and re-send the "confirm registration" e-mail.
          $pin = str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
          $lck = 1;
          
          $upd_usr_stmt = 
            'update ta_sec_user set usr_locked = ?, usr_pin = ? where usr_id = ?;';
          $stm3 = $db_conn->prepare($upd_usr_stmt);
          $stm3->bind_param('isi', $lck, $pin, $usr_id);
          $stm3->execute();
          $stm3->close();
          
          $log_tm  = strftime('%Y-%m-%d %H:%M:%S', time());
          $jnl_act = 'REGISTER.LOCK';
          $jnl_txt = 'Reason: new registration for account that already existed';
          
          $logstm->bind_param('sisss', $log_tm, $usr_id, $emailaddr, $jnl_act, $jnl_txt);
          $logstm->execute();
          $logstm->free_result();
          $logstm->close();
          
          $success = true;
        }
        else
        {
          $salt = str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
          $pwd = hash_pwd($password, $salt);
        
          $pin = str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
          $lck = 1;
          $log_tm  = strftime('%Y-%m-%d %H:%M:%S', time());
          
          $ins_usr_stmt = 
            'insert into ta_sec_user( usr_fst_name, usr_lst_name, usr_email, usr_pwd, usr_salt, usr_registered, usr_locked, usr_pin ) ' .
            'values( ?, ?, ?, ?, ?, ?, ?, ? );';
          $stm2 = $db_conn->prepare($ins_usr_stmt);
          $stm2->bind_param('ssssssis', $fst_name, $lst_name, $emailaddr, $pwd, $salt, $log_tm, $lck, $pin);
          $stm2->execute();
          $usr_id = $stm2->insert_id;
          $stm2->close();
  
          $jnl_act = 'REGISTER';
          $jnl_txt = '';

          $logstm->bind_param('sisss', $log_tm, $usr_id, $emailaddr, $jnl_act, $jnl_txt);
          $logstm->execute();
          $logstm->free_result();
          $logstm->close();
          
          $success = true;          
        }
      }
    }
  }
  
  if ($success)
  {
    // Send E-Mail to newly registered user here ...
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
        </div>
      </div>
    </nav>
    
    <div class="container" role="main">
      <div class="page-header">
        <h1>Ilses Methodenbox</h1>
      </div>
      
      <div class="row">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h2 class="panel-title">Benutzerregistrierung</h2>
          </div>
          <div class="panel-body">

            <div class="col-md-5">
              <div id="message"></div>
              
              <form id="frmRegister" method="post" action="/php/register.php">
                <div class="form-group" id="frmGrpVorname">
                  <label for="vorname" class="control-label">Vorname</label>
                  <input type="text" id="vorname" class="form-control" placeholder="Vorname"
                  <?php if (! empty($fst_name)) { echo 'value="' . $fst_name . '"'; } ?>
                  >
                </div>
                <div class="form-group" id="frmGrpNachname">
                  <label for="nachname" class="control-label">Nachname</label>
                  <input type="text" id="nachname" class="form-control" placeholder="Nachname"
                  <?php if (! empty($lst_name)) { echo 'value="' . $lst_name . '"'; } ?>
                  >
                </div>
  
                <div class="form-group" id="frmGrpEmail">
                  <label for="email" class="control-label">E-Mail Adresse</label>
                  <input type="text" id="email" class="form-control"  placeholder="E-Mail Adresse">
                </div>
  
                <div class="form-group" id="frmGrpPassword">
                  <label for="password" class="control-label">Passwort</label>
                  <input type="password" id="password" class="form-control">
                </div>
  
                <div class="form-group" id="frmGrpPwdConf">
                  <label for="pwdconf" class="control-label">Wiederholung des Passworts</label>
                  <input type="password" id="pwdconf" class="form-control">
                </div>
  
                <div class="form-group text-right">
                  <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" active>Bestätigen</button>
                  <a href="/html/index.html" id="continueBtn" class="btn btn-primary btn-lg disabled" role="button" aria-disabled="true">Weiter</a>
                </div>
              </form>

            </div>

          </div>
        </div>
      </div>
    </div>    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="/js/bootstrap.min.js"></script>
    
    <script>
      function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
      }
      
      $( '#frmRegister').submit( function() {
          var formControl = true;

          $( '#frmGrpVorname' ).removeClass( 'has-error' );
          $( '#frmGrpNachname' ).removeClass( 'has-error' );
          $( '#frmGrpEmail' ).removeClass( 'has-error' );
          $( '#frmGrpPassword').removeClass( 'has-error');
          $( '#frmGrpPwdConf').removeClass( 'has-error');

  
          var vorname = $( '#vorname' );
          var nachname = $( '#nachname' );
          var email = $( '#email' );
          var password = $( '#password' );
          var pwdconf = $( '#pwdconf' );
  
          if(vorname.val() == '') {
            formControl = false;
            $( '#frmGrpVorname' ).addClass( 'has-error' );
          }
  
          if(nachname.val() == '') {
            formControl = false;
            $( '#frmGrpNachname' ).addClass( 'has-error' );
          }
  
          if(validateEmail(email.val()) == false) {
            formControl = false;
            $( '#frmGrpEmail' ).addClass( 'has-error' );
          }
  
          if(password.val() == '') {
            formControl = false;
            $( '#frmGrpPassword' ).addClass( 'has-error' );
          }
  
          if(pwdconf.val() == '') {
            formControl = false;
            $( '#frmGrpPwdConf' ).addClass( 'has-error' );
          }
          
          if (password.val() != pwdconf.val()) {
            formControl = false;
            $( '#frmGrpPassword' ).addClass( 'has-error' );
            $( '#frmGrpPwdConf' ).addClass( 'has-error' );
          }

          if(formControl) {
            $.ajax({
                type: "POST",
                url: "/php/register.php",
                data: { 
                  vorname: vorname.val(), 
                  nachname: nachname.val(),
                  email: email.val(),
                  password: password.val(),
                  pwdconf: pwdconf.val()
                }
            }).done(function(msg) {
                $( '#message' ).addClass( 'alert' );
                $( '#message' ).addClass( 'alert-success' );
                $( '#message').html( "Benutzerregistrierung erfolgreich durchgeführt" );
                $( '#continueBtn').removeClass('disabled');
                $( '#continueBtn').removeAttr('aria-disabled');
                $( '#submitBtn').removeAttr('active');
                $( '#submitBtn').addClass('disabled');
            });          
          }
          
          return false;
      } );
      
    </script>
  </body>
</html>