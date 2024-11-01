<?php

    $formEmailId = ZRequest::getParam('form_email_id'); //could use either post or get
    $formEmailModel = ZFactory::createModel('zform_email');
    $form = ZFormEmail::getForm('trash',$formEmailId);
    $formData = ZRequest::getPost();

    //common action logic
    if (isset($formData['zformemail_trash_submit'])) {
        //delete row
        $res = 0;
        if ($formEmailId >0) {
            $res = $formEmailModel->delete(array('id = ?'=>$formEmailId));
        }
        include('list.inc.php');
    } else {
        //initial view of form
        if ($formEmailId >0) {
            $row = $formEmailModel->find($formEmailId);
            if (!empty($row)) {
                //get the row we're deleting
                $dataRow = $row[0];
                $data = $dataRow->toArray();
                //fill the form with rowData
                $form->populate($data);
                echo "<h2>Trash Form Email</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            include('list.inc.php');
        }
    }