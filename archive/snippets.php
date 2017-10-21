<?php
  $ajaxRequest = ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'));
  if ($ajaxRequest)
  {
    $encoded = json_encode($responseArray);
    
    header('Content-Type: application/json');
    echo $encoded;
  }
  else
  {
    if ($success)
    {
      if ($two_phase_reg)
      {
        header('Location: /php/usr_cnf.php');
        exit;
      }
      else
      {
        header('Location: /php/index.php');
        exit;
      }
    }
  }
?>