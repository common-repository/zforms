<?php
    
    $formData = ZRequest::getPost();
    $decoratorId = ZRequest::getParam('decorator_id'); 
    $form = ZFormDecorator::getForm('edit',$decoratorId);
    $decoratorModel = ZFactory::createModel('zform_decorator');

    if (isset($formData['zformdecorator_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData = ZFormDecorator::hydrateRowData($formData);
            //update row
            $res = 0;
            if ($decoratorId >0) {
                $res = $decoratorModel->update($updateData,array('id = ?'=>$decoratorId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Edit Form Decorator</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        if ($decoratorId >0) {
            $row = $decoratorModel->find($decoratorId);
            if (!empty($row)) {
                //fill the form and show it
                $dataRow = $row[0]; //collection/array of objects
                $data = $dataRow->toArray(); //convert objects to arrays. using naming scheme.
                $form->populate($data);
                echo "<h2>Edit Form Decorator</h2>";
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            //not much to edit if we don't have the rowId
            include('list.inc.php');
        }
    }