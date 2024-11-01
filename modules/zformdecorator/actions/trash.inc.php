<?php

    $formData = ZRequest::getPost();
    $decoratorId = ZRequest::getParam('decorator_id');
    $form = ZFormDecorator::getForm('trash',$decoratorId);
    $decoratorModel = ZFactory::createModel('zform_decorator');

    //common action logic
    if (isset($formData['zformdecorator_trash_submit'])) {
        //delete row
        $res = 0;
        if ($decoratorId >0) {
            $res = $decoratorModel->delete(array('id = ?'=>$decoratorId));
        }
        include('list.inc.php');
    } else {
        //initial view of form
        if ($decoratorId >0) {
            $row = $decoratorModel->find($decoratorId);
            if (!empty($row)) {
                //get the row we're deleting
                $dataRow = $row[0];
                $data = $dataRow->toArray();
                //fill the form with rowData
                $form->populate($data);
                echo "<h2>Trash Form Decorator</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            include('list.inc.php');
        }
    }