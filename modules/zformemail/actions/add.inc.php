<?php
    //email/add
    
    $form = ZFormEmail::getForm('add');
    $formData = ZRequest::getPost();

    if (isset($formData['zformemail_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $formEmailModel = ZFactory::createModel('zform_email');
            $insertData = ZFormEmail::hydrateRowData($formData);
            $insertData['body'] = stripslashes($insertData['body']);  
            $insertData['body'] = addslashes($insertData['body']);            
            //save row
            $newId = $formEmailModel->insert($insertData);
            //cannot redirect, show listing page
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Form Email</h2>";
            $insertData['body'] = stripslashes($insertData['body']);
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Form Email</h2>";
        echo $form;
    }
