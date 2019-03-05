<?php
$result = '';

if (!empty($_POST))
{
    if ($_POST['val_type'] == 'mth_area')
    {
        if (empty($_POST['mth_topic']) || ($_POST['mth_topic'] == ''))
        {
            $result = '<option>MTH_TOPIC is empty</option>';
        }
        else
        {
            $result = '<option>' . $_POST['mth_topic'] . ' 1' . '</option>';
            $result = $result . '<option>' . $_POST['mth_topic'] . ' 2' . '</option>';
            $result = $result . '<option>' . $_POST['mth_topic'] . ' 3' . '</option>';
            $result = $result . '<option>Sonstiges</option>';
        }
    }
    else
    {
        if ($_POST['val_type'] == 'mth_result')
        {                    
            $result = '<table class="table table-striped"><thead><tr><th>Methodenname</th><th>Jahrgang</th><th>Phase</th><th>Sozialform</th><th>Fachbereich</th></tr></thead>';
            $result = $result . '<tbody>';
            $result = $result . '<tr><td>Test Methode 1</td><td>JG 1</td><td>Einf&uuml;hrung</td><td>Gruppenarbeit</td><td>BW 3</td></tr>';
            $result = $result . '<tr><td>Test Methode 2</td><td>JG 2</td><td>Sicherung</td><td>Einzelarbeit</td><td>BW 2</td></tr>';
            $result = $result . '<tr><td>Test Methode 3</td><td>JG 3</td><td>Aktivierung</td><td>Klassenplenum</td><td>WINF 1</td></tr>';
            $result = $result . '</tbody></table>';
        }
    }
}

echo $result;
?>