<?php

    $formData = ZRequest::getPost();
    $formId = ZRequest::getParam('form_id');
    $form = ZForm::getForm('trash',$formId);
    $formModel = ZFactory::createModel('zform');

    //common action logic
    if (isset($formData['zform_trash_submit'])) {
        //delete row
        $res = 0;
        if ($formId >0) {
            
            $formStepModel = ZFactory::createModel('zform_step');
            $stepRows = $formStepModel->fetchAll($formStepModel->select()->where('form_id = ?',$formId));
            $inputModel = ZFactory::createModel('zform_input');
            
            if (!empty($stepRows)) {
                foreach($stepRows as $stepRow) {
                    //remove inputs
                    $inputModel->delete(array('form_step_id = ?' => $stepRow->id));
                }
                //remove steps
                $formStepModel->delete(array('form_id = ?' => $formId));
            }
            //remove form
            $formModel->delete(array('id = ?' => $formId));
        }
        include('list.inc.php');
    } else {
        //initial view of form
        if ($formId >0) {
            $row = $formModel->find($formId);
            if (!empty($row)) {
                //get the row we're deleting
                $dataRow = $row[0];
                $data = $dataRow->toArray();
                //fill the form with rowData
                $form->populate($data);
                echo "<h2>Trash Form</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            include('list.inc.php');
        }
    }