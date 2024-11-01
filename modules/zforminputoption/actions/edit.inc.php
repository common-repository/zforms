<?php
    //get rowId
    $inputOptionId = ZRequest::getParam('form_input_option_id'); //could use either post or get
    //get parentId and set to form
    $inputId = ZRequest::getParam('form_input_id'); 
    $form = ZFormInputOption::getForm('edit',$inputId,$inputOptionId);
    $formData = ZRequest::getPost();
    $inputOptionModel = ZFactory::createModel('zform_input_option');

    if (isset($formData['zforminputoption_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData = ZFormInputOption::hydrateRowData($formData);
            //update row
            $res = 0;
            if ($inputOptionId >0) {
                $res = $inputOptionModel->update($updateData,array('id = ?'=>$inputOptionId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            $form->populate($formData);
            echo "<h2>Edit Form Input Option</h2>";
            echo $form;
        }
    } else {
        //initial view of form
        if ($inputOptionId >0) {
            $row = $inputOptionModel->find($inputOptionId);
            if (!empty($row)) {
                //fill the form and show it
                $dataRow = $row[0]; //collection/array of objects
                $data = $dataRow->toArray(); //convert objects to arrays. using naming scheme.
                $form->populate($data);
                echo "<h2>Edit Form Input Option</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            //not much to edit if we don't have the rowId
            include('list.inc.php');
        }
    }