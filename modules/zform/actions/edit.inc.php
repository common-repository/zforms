<?php
    
    $formData = ZRequest::getPost();
    $formId = ZRequest::getParam('form_id'); 
    $form = ZForm::getForm('edit',$formId);
    $formModel = ZFactory::createModel('zform');

    if (isset($formData['zform_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData=ZForm::hydrateRowData($formData);
            //update row
            $res = 0;
            if ($formId >0) {
                $res = $formModel->update($updateData,array('id = ?'=>$formId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Edit Form</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        if ($formId >0) {
            $row = $formModel->find($formId);
            if (!empty($row)) {
                //fill the form and show it
                $dataRow = $row[0]; //collection/array of objects
                $data = $dataRow->toArray(); //convert objects to arrays. using naming scheme.
                $form->populate($data);
                echo "<h2>Edit Form</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            //not much to edit if we don't have the rowId
            include('list.inc.php');
        }
    }