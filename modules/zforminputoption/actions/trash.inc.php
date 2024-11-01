<?php
    //get rowId
    $inputOptionId = ZRequest::getParam('form_input_option_id'); 
    //get parentId
    $inputId = ZRequest::getParam('form_input_id');
    $form = ZFormInputOption::getForm('trash',$inputId,$inputOptionId);
    $formData = ZRequest::getPost();
    $inputOptionModel=ZFactory::createModel('zform_input_option');

    //common action logic
    if (isset($formData['zforminputoption_trash_submit'])) {
        //delete row
        $res = 0;
        if ($inputOptionId >0) {
            $res = $inputOptionModel->delete(array('id = ?'=>$inputOptionId));
        }
        include('list.inc.php');
    } else {
        //initial view of form
        if ($inputOptionId >0) {
            $row = $inputOptionModel->find($inputOptionId);
            if (!empty($row)) {
                //get the row we're deleting
                $dataRow = $row[0]; //first row in a collection/array
                $data = $dataRow->toArray(); //change the array elements to arrays
                //fill the form with rowData
                $form->populate($data);
                echo "<h2>Trash Form Input Option</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            include('list.inc.php');
        }
    }