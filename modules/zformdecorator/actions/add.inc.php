<?php

    $form = ZFormDecorator::getForm('add');
    $formData = ZRequest::getPost();

    if (isset($formData['zformdecorator_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $decoratorModel = ZFactory::createModel('zform_decorator');
            $insertData = ZFormDecorator::hydrateRowData($formData);
            //save row
            $newId = $decoratorModel->insert($insertData);
            //cannot redirect, show listing page
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Form Decorator</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Form Decorator</h2>";
        echo $form;
    }
