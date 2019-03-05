<?php
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" href="/mbx/css/mbx.css">
    </head>
    <body>
        <form id="test_form" method="post" action="#" novalidate>
            <div class="row form-row">
                <div class="col col-md-4">
                    <div class="form-group" id="login_email">
                        <label for="user_email">E-Mail Adresse *</label>
                        <input id="user_email" type="email" name="user_email" class="form-control" placeholder="E-Mail Adresse" required>
                        <label for="user_email" class="error"></label>
                    </div>
                </div>
            </div>
            
            <div class="row form-row">
                <div class="col col-md-4">
                    <div class="form-group" id="login_email_conf">
                        <label for="user_conf">E-Mail Adresse *</label>
                        <input id="user_conf" type="email" name="user_conf" class="form-control" placeholder="E-Mail Best&auml;tigung" required>
                    </div>
                </div>
            </div>
            
            <div class="row form-row">
                <div class="col col-md-2">
                    <div class="form-group" id="login_submit">
                        <input type="submit" class="btn btn-primary btn-send" value="Anmelden">
                    </div>
                </div>
            </div>
        </form>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>
        <script>
            /* global $ */
            $('#test_form').validate({
                rules: {
                    user_email: {
                        required: true
                    },
                    user_conf: {
                        required: true,
                        equalTo: "#user_email"
                    }
                },
                messages: {
                    user_email: {
                        required: "E-Mail Adresse eingeben",
                        email: "Gültige E-Mail Adrsse eingeben"
                    },
                    user_conf: {
                        required: "E-Mail Adresse eingeben",
                        equalTo: "Die E-Mail Adressen müssen übereinstimmen"
                    }
                }
            });
        </script>
    </body>
</html>