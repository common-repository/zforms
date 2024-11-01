<?php

    $formData = ZRequest::getPost();
    $form = ZFormRecaptcha::getForm('add');

    if (isset($formData['zformrecaptcha_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $rowModel = ZFactory::createModel('zform_recaptcha');
            $insertData = ZFormRecaptcha::hydrateRowData($formData);
            //save row
            $newId = $rowModel->insert($insertData);
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Re-Captcha</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Re-Captcha</h2>";
        echo $form;
    }