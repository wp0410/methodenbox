<?php
//---------------------------------------------------------------------------------------
//  Copyright (c) 2018, 2019 Walter Pachlinger (walter.pachlinger@gmail.com)
//
//  Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
//  file except in compliance with the License. You may obtain a copy of the License at
//      http://www.apache.org/licenses/LICENSE-2.0
//  Unless required by applicable law or agreed to in writing, software distributed under
//  the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
//  ANY KIND, either express or implied. See the License for the specific language
//  governing permissions and limitations under the License.
//----------------------------------------------------------------------------------------

class GlobalResultText
{
    public static $resultText = array (
            // Error Messages
            'E_100' => 'E_100 Interner Verarbeitungsfehler. Bitte kontaktieren Sie einen Administrator',
            
            // Section: User Account
            'E_400' => 'E_400 Anmeldung fehlgeschlagen',
            'E_401' => 'E_401 Benutzername (E-Mail Adresse oder Vor- und Nachname) sind bereits in Verwendeung. Registrierung ist nicht m&ouml;glich',
            'E_402' => 'E_402 Fehler beim Speichern der Benutzer Registrierung in der Datenbank',
            'E_403' => 'E_403 Benutzer Registrierung konnte nicht gefunden werden',
            'E_404' => 'E_404 Fehler beim Zugriff auf die Benutzer Registrierung in der Datenbank',
            'E_405' => 'E_405 Die Benutzersitzung ist ung&uuml;ltig.',
            'E_406' => 'E_406 Die Identifizierung und Authentifizierung ist fehlgeschlagen',
            'E_407' => 'E_407 Sie sind nicht berechtigt, die gew&uuml;nschte Funktion auszuf&uuml;hren. Bitte kontaktieren Sie einen Administrator',
            'E_410' => 'E_410 Benutzer Anmeldung fehlgeschlagen (Ung&uuml;ltiges Benutzerkonto oder falsches Passwort)',
            'E_411' => 'E_411 Benutzer Anmeldung fehlgeschlagen (die Registrierung ist noch nicht best&auml;tigt)',
            'E_412' => 'E_412 Benutzer Anmeldung fehlgeschlagen (das Benutzerkonto ist gesperrt). Bitte kontaktieren Sie einen Administrator',
            'E_413' => 'E_413 Das alte Passwort ist ung&uuml;ltig. Das Passwort kann daher nicht ge&auml;ndert werden',
            'E_414' => 'E_414 Die eingegebenen Passw&ouml;rter stimmen nicht überein',
            'E_420' => 'E_420 Die Registrierung konnte nicht korrekt best&auml;tigt werden',
			'E_430' => 'E_430 Schwerer interner Fehler beim Laden der Benutzerberechtigungen, Bitte kontaktieren Sie einen Administrator',
            
            // Section: User Session
            'E_501' => 'E_501 Fehler beim Starten der Benutzersitzung. Bitte kontaktieren Sie einen Administrator',
            'E_502' => 'E_502 Ihre Benutzersitzung ist nicht g&uuml;ltig. Neuerliche Anmeldung erforderlich',
            
            // Section: Teaching Method
            'E_601' => 'E_601 Fehler beim Speichern der Daten f&uuml;r die Unterrichtsmethode. Bitte kontaktieren Sie einen Administrator',
            'E_602' => 'E_602 Die Unterrichtsmethode wurde nicht gefunden. Bitte kontaktieren Sie einen Administrator',
            'E_603' => 'E_603 Fehler beim Laden der Unterrichtsmethode. Bitte kontaktieren Sie einen Administrator',
            'E_604' => 'E_604 Fehler beim L&ouml;schen der Unterrichtsmethode. Bitte kontaktieren Sie einen Administrator',
            'E_651' => 'E_651 Der Dateityp ist ung&uuml;ltig oder nicht zul&auml;ssig. Es d&uuml;rfen nur komprimierte Archivdateien hochgeladen werden.',
            'E_652' => 'E_652 Die Datei konnte leider nicht erfolgreich hochgeladen werden. Bitte kontaktieren Sie einen Administrator',
            'E_653' => 'E_653 Fehler beim Speichern der hochgeladenen Datei im File Store. Bitte konnte Sie einen Administrator',
            'E_654' => 'E_654 F&uuml;r die Unterrichtsmethode wurde keine Datei gefunden. Bitte kontaktieren Sie einen Administrator',
            'E_655' => 'E_655 Fehler beim Laden der Datei f&uuml;r die Unterrichtsmethode. Bitte kontaktieren Sie einen Administrator',
            'E_671' => 'E_671 Fehler beim Speichern der Bewertung. Bitte kontaktieren Sie einen Administrator',
            'E_681' => 'E_681 Fehler beim Speichern des Downloads. Bitte kontaktieren Sie einen Administrator',
        
            // SEction: Contact Request
            'E_701' => 'E_701 Fehler beim Zugriff auf die Datenbank. Bitte kontaktieren Sie einen Administrator',
            'E_702' => 'E_702 Fehler: die Kontaktanfrage kann in der Datenbank nicht gefunden werden. Bitte kontaktieren Sie einen Administrator',
			'E_703' => 'E_703 Fehler: die Antwort kann nicht gespeichert werden. Bitte kontaktieren Sie einen Administrator',
			'E_704' => 'E_704 Fehler: die Kontaktanfrage kann nicht geschlossen werden. Bitte kontaktieren Sie einen Administrator',

            // Info Messages
            'E_901' => 'Sie werden in K&uuml;rze ein E-Mail and die angegebene E-Mail Adresse erhalten. Bitte folgen Sie den Anweisungen in dieser E-Mail, um die Registrierung abzuschlie&szlig;en',
            'E_902' => 'Sie werden in K&uuml;rze ein E-Mail and die angegebene E-Mail Adresse erhalten. Bitte folgen Sie den Anweisungen in dieser E-Mail, um die &Auml;nderung des Passworts abzuschlie&szlig;en',
            'E_949' => 'Diese Funktion ist noch nicht implementiert. Die Aktion hatte keine Auswirkung',
            
            // Success Messages
            'E_951' => 'Die Unterrichtsmethode "[%M]" wurde erfolgreich hochgeladen.',
            'E_952' => 'Das Passwort wurde erfolgreich ge&auml;ndert',
        
            // End of Array
            'E_999' => 'VOID'
        );
        
}
?>