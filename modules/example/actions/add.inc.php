<?php

    $formData = ZRequest::getPost();
    $foreignId = ZRequest::getParam('foreign_id');
    $form = example_action_helper::getForm('add',$foreignId);

    if (isset($formData['example_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $rowModel = ZFactory::createModel('example');
            $insertData = example_action_helper::hydrateRowData($formData);
            //save row
            $newId = $rowModel->insert($insertData);
            //cannot redirect, show listing page
            $foreignId = $insertData['foreign_id'];
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Example</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Example</h2>";
        echo $form;
    }