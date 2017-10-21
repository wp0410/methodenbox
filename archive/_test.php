<?php
    require 'int_mailjet.php';
    
    $result = mailjet_mail('walter.pachlinger@gmx.at', 'Mailjet E-Mail Test', 'Das ist ein Test. Bitte nicht antworten');
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p>
            <?php
                echo 'Email Sent: ' . $result . '<br>';
            ?>
        </p>
    </body>
</html>