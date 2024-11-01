<?php

    $formEmailId = ZRequest::getParam('form_email_id'); 
    $formEmailModel = ZFactory::createModel('zform_email');
    $form = ZFormEmail::getForm('edit',$formEmailId);
    $formData = ZRequest::getPost();

    if (isset($formData['zformemail_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData = ZFormEmail::hydrateRowData($formData);
            $updateData['body'] = stripslashes($updateData['body']);
            $updateData['body'] = addslashes($updateData['body']);
            //update row
            $res = 0;
            if ($formEmailId >0) {
                $res = $formEmailModel->update($updateData,array('id = ?'=>$formEmailId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Edit Form Email</h2>";
            $formData['body'] = stripslashes($formData['body']);
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        if ($formEmailId >0) {
            $row = $formEmailModel->find($formEmailId);
            if (!empty($row)) {
                //fill the form and show it
                $dataRow = $row[0]; //collection/array of objects
                $data = $dataRow->toArray(); //convert objects to arrays. using naming scheme.
                $data['body'] = stripslashes($data['body']);
                $form->populate($data);
                echo "<h2>Edit Form Email</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            //not much to edit if we don't have the rowId
            include('list.inc.php');
        }
    }