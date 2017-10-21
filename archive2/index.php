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

  include 'model/mdl_par.php';
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
            <li><a href="#">Methode Suchen</a></li>
            <li><a href="#">Methode Erstellen</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/php/usr_new.php">Registrieren</a></li>
            <li><a href="/php/usr_lin.php">Anmelden</a></li>
            <li><a href="/php/aux_hlp.php">Hilfe</a></li>
            <li><a href="/php/aux_ctc.php">Kontakt</a></li>
          </ul>
        </div>
      </div>
    </nav>
    
    <div class="container" role="main">
      <div class="page-header"><h1><?php echo GobalParam::$title; ?></h1></div>
      <div class="page-header"><h2>Übersicht</h2></div>
      
      <div class="row">
        <p class="lead">
          Sie müssen sich anmelden, um auf den Inhalt der Methodenbox zugreifen zu können. 
          Wenn Sie noch keine Anmeldedaten haben, müssen Sie die Registrierung durchführen.
        </p>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
  </body>
</html>