<?php
    
    $formData = ZRequest::getPost();
    $rowId = ZRequest::getParam('recaptcha_id');
    $form = ZFormRecaptcha::getForm('edit',$rowId);
    $rowModel = ZFactory::createModel('zform_recaptcha');

    if (isset($formData['zformrecaptcha_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData = ZFormRecaptcha::hydrateRowData($formData);
            //update row
            $res = 0;
            if ($rowId >0) {
                $res = $rowModel->update($updateData,array('id = ?'=>$rowId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Edit Re-Captcha</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        if ($rowId >0) {
            $row = $rowModel->find($rowId);
            if (!empty($row)) {
                //fill the form and show it
                $dataRow = $row[0]; //collection/array of objects
                $data = $dataRow->toArray(); //convert objects to arrays. using naming scheme.
                $form->populate($data); //quick if you name db fields and form fields the same
                echo "<h2>Edit Re-Captcha</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            //not much to edit if we don't have the rowId
            include('list.inc.php');
        }
    }