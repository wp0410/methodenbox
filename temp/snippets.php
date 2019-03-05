
    
    if ($_POST['frm_type'] == 'MTH_CHANGE')
    {
        $mth = new TeachingMethod($db_conn, 0);
        $mth->loadMethod($_POST['mth_id']);
        
        echo '<form id="frm_change" role="form" onsubmit="submitChange()">';
        echo '  <div class="controls">';
        echo '    <input id="mth_id" type="hidden" name="mth_id" value="' . $mth->getId() . '">';
        echo '    <div class="row form-row">';

        // Method Name
        echo '      <div class="col col-md-6 col-xl-6"><div class="form-group">';
        echo '        <label for="mth_name">Name der Methode *</label><input id="mth_name" type="text" name="mth_name" class="form-control" value="' . 
                        htmlentities($mth->mth_name) . '">';
        echo '      </div></div>';

        // Method Subject
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group">';
        echo '        <label for="mth_subject">Unterrichtsfach *</label>';
        echo '        <select class="form-control" id="mth_subject" name="mth_subject">';
        foreach(MethodSelectionFactory::getSubjects() as $sub)
        {
            if ($sub['VAL'] == $mth->mth_subject)
            {
                echo '<option value="' . $sub['VAL'] . '" selected>' . htmlentities($sub['NAME']) . '</option>';
            }
            else
            {
                echo '<option value="' . $sub['VAL'] . '">' . htmlentities($sub['NAME']) . '</option>';
            }
        }
        echo '        </select>';
        echo '      </div></div>';
        
        // Method Subject Area
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group">';
        echo '        <label for="mth_area">Fachbereich *</label>';
        echo '        <select class="form-control" id="mth_area" name="mth_area">';
        foreach(MethodSelectionFactory::getSubjectAreas($mth->mth_subject) as $area)
        {
            if ($area['VAL'] == $mth->mth_subject_area)
            {
                echo '<option value="' . $area['VAL'] .'" selected>' . htmlentities($area['NAME']) . '</option>';
            }
            else
            {
                echo '<option value="' . $area['VAL'] .'">' . htmlentities($area['NAME']) . '</option>';
            }
        }
        echo '        </select>';
        echo '      </div></div>';
        echo '    </div>'; // row form-row

        echo '    <div class="row form-row">';
        
        // Method Summary
        echo '      <div class="col-md-6 col-xl-6"><div class="form-group">';
        echo '        <label for="mth_summary">Beschreibung *</label>';
        echo '        <textarea id="mth_summary" class="form-control" name="mth_summary" form="mth_upload" rows="5">' . htmlentities($mth->mth_summary) . '</textarea>';
        echo '      </div></div>';
        
        // Method Preparation Time
        echo '      <div class="col-md-3 col-xl-3"><div class="form-group">';
        echo '        <label for="mth_prep_tm">Vorbereitungszeit *</label>';
        echo '        <select class="form-control" id="mth_prep_tm" name="mth_prep_tm">';
        foreach(MethodSelectionFactory::getPrepTime() as $prep)
        {
            if ($prep['VAL'] == $mth->mth_prep_time)
            {
                echo '<option value="' . $prep['VAL'] . '" selected>' . htmlentities($prep['NAME']) . '</option>';
            }
            else 
            {
                echo '<option value="' . $prep['VAL'] . '">' . htmlentities($prep['NAME']) . '</option>';
            }
        }
        echo '        </select>';
        echo '      </div></div>';
        
        // Method Execution Time && Age Group
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group">';
        echo '        <label for="mth_exec_tm">Durchf&uuml;hrungszeit *</label>';
        echo '        <select class="form-control" id="mth_exec_tm" name="mth_exec_tm">';
        foreach(MethodSelectionFactory::getExecTime() as $exec)
        {
            if ($exec['VAL'] == $mth->mth_exec_time)
            {
                echo '<option value="' . $exec['VAL'] . '" selected>' . htmlentities($exec['NAME']) . '</option>';
            }
            else 
            {
                echo '<option value="' . $exec['VAL'] . '">' . htmlentities($exec['NAME']) . '</option>';
            }
        }
        echo '        </select>';
        
        // Method Age Group
        echo '      </div><div class="form-group">';
        echo '        <label for="mth_class">Jahrgang *</label>';
        echo '        <select class="form-control" id="mth_age_grp" name="mth_age_grp">';
        foreach(MethodSelectionFactory::getAgeGroups() as $cls)
        {
            if ($cls['VAL'] == $mth->mth_age_grp)
            {
                echo '<option value="' . $cls['VAL'] . '" selected>' . htmlentities($cls['NAME']) . '</option>';
            }
            else
            {
                echo '<option value="' . $cls['VAL'] . '">' . htmlentities($cls['NAME']) . '</option>';
            }
        }
        echo '        </select>';
        echo '      </div></div>';
        echo '    </div>'; // row

        // Method Teaching Phase
        echo '    <div class="row form-row">';
        echo '      <div class="col col-md-3 col-xl-3"><label>Unterrichtsphase *</label>';
        echo '        <div class="card"><div class="card-body">';
        echo '          <div class="form-check">';
        if (in_array('E', $mth->mth_phase))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_E" name="mth_phase[]" value="E" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_E" name="mth_phase[]" value="E">';
        }
        echo '            <label class="form-check-label" for="mth_phase_E">Einstieg</label>';
        echo '          </div><div class="form-check">';
        if (in_array('I', $mth->mth_phase))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_I" name="mth_phase[]" value="I" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_I" name="mth_phase[]" value="I">';
        }
        echo '            <label class="form-check-label" for="mth_phase_I">Information</label>';
        echo '          </div><div class="form-check">';
        if (in_array('S', $mth->mth_phase))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_S" name="mth_phase[]" value="S" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_S" name="mth_phase[]" value="S">';
        }
        echo '            <label class="form-check-label" for="mth_phase_S">Sicherung</label>';
        echo '          </div><div class="form-check">';
        if (in_array('A', $mth->mth_phase))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_A" name="mth_phase[]" value="A" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_phase_A" name="mth_phase[]" value="A">';
        }
        echo '            <label class="form-check-label" for="mth_phase_A">Aktivierung</label>';
        echo '      </div></div></div></div>';

        // Method Social Form
        echo '      <div class="col col-md-3 col-xl-3"><label>Unterrichtsphase *</label>';
        echo '        <div class="card"><div class="card-body">';
        echo '          <div class="form-check">';
        if (in_array('E', $mth->mth_soc_form))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_E" name="mth_soc[]" value="E" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_E" name="mth_soc[]" value="E">';
        }
        echo '            <label class="form-check-label" for="mth_soc_E">Einzelarbeit</label>';
        echo '          </div><div class="form-check">';
        if (in_array('P', $mth->mth_soc_form))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_P" name="mth_soc[]" value="P" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_P" name="mth_soc[]" value="P">';
        }
        echo '            <label class="form-check-label" for="mth_soc_P">Partnerarbeit</label>';
        echo '          </div><div class="form-check">';
        if (in_array('G', $mth->mth_soc_form))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_G" name="mth_soc[]" value="G" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_G" name="mth_soc[]" value="G">';
        }
        echo '            <label class="form-check-label" for="mth_soc_G">Gruppenarbeit</label>';
        echo '          </div><div class="form-check">';
        if (in_array('K', $mth->mth_soc_form))
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_K" name="mth_soc[]" value="K" checked>';
        }
        else
        {
            echo '        <input class="form-check-input" type="checkbox" id="mth_soc_K" name="mth_soc[]" value="K">';
        }
        echo '            <label class="form-check-label" for="mth_soc_K">Klassenplenum</label>';
        echo '      </div></div></div></div>';
        
        // Method Author and Confirm Checkbox
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group">';
        echo '        <label for="mth_author">AutorIn</label>';
        echo '        <input id="mth_author" type="text" name="mth_author" class="form-control" value="' . htmlentities($mth->getAuthors()[0]) . '" disabled>';
        echo '      </div>';
        echo '      <div class="form-group"><div class="form-check">';
        echo '        <input class="form-check-input" type="checkbox" id="confirm_author" name="confirm_author" value="auth_confirm">';
        echo '        <label class="form-check-label" for="confirm_author">Ich habe die Erlaubnis der zus&auml;tzlichen AutorInnen für die Eintragung eingeholt</label>';
        echo '      </div></div></div>';
        
        // Additional Authors
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group">';
        echo '        <label for="mth_add_author">Zus&auml;tzliche AutorInnen</label>';
        echo '        <textarea id="mth_add_author" class="form-control" name="mth_add_author" form="mth_upload" rows="5" placeholder="Name">';
        for($cnt = 1; $cnt < count($mth->getAuthors()); $cnt++)
        {
            echo htmlentities($mth->getAuthors()[$cnt]) . '<br>';
        }
        echo '</textarea></div>';
        echo '      </div>';
        echo '    </div>';
        echo '    <div class="row form-row"><div class="col"><br></div></div>';

        // Close and Submit Button
        echo '    <div class="row form-row">';
        echo '      <div class="col col-md-6 col-xl-6"><label class="form-check-label">Mit * gekennzeichnete Felder m&uuml;ssen eingegeben werden.</label></div>';
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group float-right">';
        echo '        <button type="button" class="btn btn-secondary" onclick="refreshResult()">Schlie&szlig;en</button>';
        echo '      </div></div>';
        echo '      <div class="col col-md-3 col-xl-3"><div class="form-group float-right">';
        // echo '        <button type="button" class="btn btn-primary btn-send">&Auml;nderungen Speichern</button>';
        echo '        <input type="submit" class="btn btn-primary btn-send" value="&Auml;nderungen Speichern">';
        echo '      </div></div>';
        echo '    </div>';
        
        echo '  </div>'; // controls
        echo '</form>';
    }
