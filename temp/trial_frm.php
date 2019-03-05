<?php
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="/temp/trial_frm.php">Methodenbox</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNavbar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarMethodeLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Methode
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarMethodeLink">
                            <a class="dropdown-item" href="#">Suchen</a>
                            <a class="dropdown-item" href="#">Anlegen</a>
                            <a class="dropdown-item" href="#">Bewerten</a>
                            <a class="dropdown-item" href="#">Verwalten</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarBenutzerLink" role="button" data-toggle="dropdown" aria-haspopup="true" area-expanded="false">
                            Benutzer
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarBenutzerLink">
                            <a class="dropdown-item" href="#">Registrieren</a>
                            <a class="dropdown-item" href="#">Anmelden</a>
                            <a class="dropdown-item" href="#">Abmelden</a>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Hilfe</a>
                    </li>
                </ul>
            </div>
        </nav>
        <nav class="navbar fixed-bottom navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bottomNavbar" aria-controls="bottomNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="bottomNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Impressum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kontakt</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">AGB</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row row-fluid">
                <!-- div class="span8" -->
                    
                <div class="col-md-8 col-xl-9">
                    <div class="card-header">
                        Ergebnis der Filterung
                    </div>
                    <div class="card-body">
                        <div id="mth_result">
                            
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-3">
                    <div class="card">
                        <div class="card-header">
                            Filtern nach ...
                        </div>
                        <div class="card-body">
                            <form id="abcdefg" method="post" action="/temp/trial_frm.php" role="form">
                                <div class="form-group">
                                    <label for="mth_topic">Unterrichtsfach</label>
                                    <select class="form-control" id="mth_topic">
                                        <option></option>
                                        <option>Betriebswirtschaft</option>
                                        <option>Rechnungswesen</option>
                                        <option>Wirtschaftsinformatik</option>
                                    </select>
                                </div> 
                                <div class="form-group">
                                    <label for="selectFachbereich">Fachbereich</label>
                                    <select class="form-control" id="mth_area">
                                        <option></option>
                                    </select>
                                </div>
                            </form>
                            
                            <button type="button" class="btn btn-primary" id="test_exec">Ergebnis Anzeigen</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>    
        
        <script type="text/javascript">
            /* global $ */
            $(document).ready(function() {
                $('#mth_topic').click(function() {
                    $.post(
                        "pov_val.php",
                        {
                            val_type: "mth_area",
                            mth_topic: $('#mth_topic').val()
                        },
                        function(data,status) {
                            $('#mth_area').html(data);
                        }
                    )
                })
            })
        </script>
        <script type="text/javascript">
            /* global $ */
            $(document).ready(function() {
                $('#test_exec').click(function() {
                    $.post(
                        "pov_val.php",
                        {
                            val_type: "mth_result",
                            mth_topic: $('#mth_topic').val(),
                            mth_area: $('#mth_area').val()
                        },
                        function(data,status) {
                            $('#mth_result').html(data);
                        }
                    )
                })
            })
        </script>
    </body>
</html>