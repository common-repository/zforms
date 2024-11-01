<?php

//frontend api

class zform {

    //handle completed/validated form
    public static function handle($key,$formData) {

        $formRow = self::getFormRow($key);
        if (empty($formRow)) {
            return false;
        }
        
        $handlerId = $formRow->handler_id;
        $successEmailId = $formRow->success_email_id;
        $errorEmailId = $formRow->error_email_id;   
        $emailDisabled = $formRow->email_disabled;        

        if ($handlerId > 0) {
            $handlerModel = ZFactory::createModel('zform_handler');
            $handlerRow = $handlerModel->fetchRow($handlerModel->select()->where('id = ?', $handlerId));
            if (!empty($handlerRow)) {
                $handlerFile = $handlerRow->filename;
                if (!empty($handlerFile)) {
                    $handlerPath = 'handlers/' . $handlerFile;

                    include_once($handlerPath);

                    if (function_exists('zfHandle') && 
                        function_exists('zfSuccess') && 
                        function_exists('zfError')) {

                        $success = zfHandle($formData,$key);
                        //good place to debug
                        //die("success: $success");
                        if ($success) {
                            $_SESSION['zforms']['handled'][$key] = 1;
                            if (zfSuccess($formData,$key)) {
                                //send success mail
                                if ($successEmailId > 0 && !$emailDisabled) {
                                    $emailObj = ZFactory::createEmail($successEmailId, $formData);
                                    $emailObj->send();
                                }
                            }
                        } else {
                            $_SESSION['zforms']['error'][$key] = 1; //might be useful
                            if (zfError($formData,$key)) {
                                //send error email
                                if ($errorEmailId > 0 && !$emailDisabled) {
                                    $emailObj = ZFactory::createEmail($errorEmailId, $formData);
                                    $emailObj->send();
                                }
                            }
                        }

                    } else {
                        die("your zforms handler must have these functions: zfHandle(data), zfSuccess(data), zfError(data)");
                    }
                }
            }
        } else if ($successEmailId > 0) {
            //they just want to email
            if ($successEmailId > 0) {
                $emailObj = ZFactory::createEmail($successEmailId, $formData);
                $emailObj->send();
            }
        }
    }

    //construct a form and add elements for the step
    public static function build($key, $step=1, $populate=1) {
        $debug = '';
        //a flag for whether to add a submit input
        $hasSubmit = false;
        //if we populate, we want to know if it is valid
        $isValid = 0;
        
        $hasCaptcha = 0;
        if ($step > 1 && !isset($_SESSION['zforms']['valid'][$key][$step-1])) {
            return "<p>You must complete the previous step/form first.</p>";
        }

        //get form row
        $formRow = self::getFormRow($key);
        if (empty($formRow)) {
            return "<p>ZForm key: $key does not exist.</p>";
        }
        $formId = $formRow->id;

        //get step row
        $stepRow = self::getFormStepRow($formId, $step);
        if (empty($stepRow)) {
            return "<p>ZForm step: $step , for key: $key does not exist.</p>";
        }
        $stepId = $stepRow->id;
        $step = $stepRow->step;
        
        if (!empty($stepRow->requires_key) && 
            !isset($_SESSION[$stepRow->requires_key])) {
            return "<p>Your session is not authorized for this form.</p>";
        }

        $isLastStep = self::isLastStep($formId, $step);

        //set aside decorator model
        $decoratorModel = ZFactory::createModel('zform_decorator');
        //get decorators
        $decoratorId = $stepRow->decorator_id;
        $decoratorRow = $decoratorModel->fetchRow($decoratorModel->select()->where('id = ?',$decoratorId));

        //get input rows
        $inputModel = ZFactory::createModel('zform_input');
        $select = $inputModel->select();
        $select->where('form_step_id = ?', $stepId);
        $select->order('order asc');
        $inputRows = $inputModel->fetchAll($select);
        if (empty($inputRows)) {
              return false;
        }

        //set aside option model, stuff
        $optionModel = ZFactory::createModel('zform_input_option');
        $optionTypes = array('radio','select');

        //start building the form object
        $form = ZFactory::createForm('zform',1);
        if (!empty($decoratorRow)) {
            $decorator = ZFactory::createFormDecorator($decoratorRow->container_tag);
            $form->setDecorators($decorator);
        }

        //can go wherever the user wants
        if (!empty($stepRow->custom_action)) {
            $form->setAction($stepRow->custom_action);
        }

        //let the form handler know what form we're submitting
        $formKeyInput = ZFactory::createHiddenElement('zforms_key');
        $formKeyInput->setValue($key);
        $form->addElement($formKeyInput);

        //let the form handler know what step we're submitting
        $stepInput = ZFactory::createHiddenElement('zforms_step');
        $stepInput->setValue($step);
        $form->addElement($stepInput);

        //let the form handler know what post we're submitting from

        foreach($inputRows as $formInput) {
            
            $isRecaptcha = 0;
            $isSubmit = 0;
            
            //watch for a submit input
            if (strtolower($formInput->type) == 'submit') {
                $hasSubmit = true;
                $isSubmit = 1;
            }
            
            //create element with factory
            if (strpos($formInput->type,'recaptcha') === false) {
                $input = ZFactory::createElement($formInput->type,$formInput->name,1);
            } else {
                $isRecaptcha = 1;
                $hasCaptcha = 1;
                $recaptchaIdStr = $formInput->type;
                $colon = strpos($recaptchaIdStr,':')+1;
                $idStr = substr($recaptchaIdStr,$colon,strlen($recaptchaIdStr)-$colon);
                $recaptchaId = (int) $idStr;
                $recaptchaRow = self::getRecaptchaRow($recaptchaId);
                $input = ZFactory::createRecaptchaElement($recaptchaRow->pub_key,$recaptchaRow->priv_key,$formInput->name);
            }
            
            //add css
            $cssClasses = $formInput->css_classes;
            $cssId = $formInput->css_id;
            if (!empty($cssClasses)) {
                $input->setAttrib('class',$cssClasses);
            }
            if (!empty($cssId)) {
                $input->setAttrib('id',$cssId);
            }
            
            //skip the rest of the loop with recaptcha's and continue
            if ($isRecaptcha) {
                $form->addElement($input);
                continue;
            }
            
            $input->setLabel($formInput->label);

            //set required
            if ($formInput->required) {
                $input->setRequired(true);
            }

            //set default - gets over-written if populated with form values
            if (!empty($formInput->default_value)) {
                $input->setValue($formInput->default_value);
            }

            //add validators
            $inputValidatorModel = ZFactory::createModel('zform_input_validator');
            $inputValidatorRows = $inputValidatorModel->fetchAll($inputValidatorModel->select()->where('input_id = ?',$formInput->id));
            if (!empty($inputValidatorRows)) {
                foreach($inputValidatorRows as $validatorRow) {
                    $validator = ZFactory::createValidator($validatorRow->type,$validatorRow->message);
                    $input->addValidator($validator);
                }
            }

            //get option rows if input is a multi-type
            if (in_array($formInput->type,$optionTypes)) {
                $select = $optionModel->select();
                $select->where('input_id = ?',$formInput->id);
                $select->order('order asc');
                //add options to input
                $optionRows = $optionModel->fetchAll($select);
                if (!empty($optionRows)) {
                    foreach($optionRows as $optionRow) {
                        $input->addMultiOption($optionRow->value,$optionRow->label);
                    }
                }
            }

            //set decorators
            if (empty($decoratorRow)) {
                $input->setDecorators(ZFactory::createInputDecorator());
            } else {
                $rowTag = $decoratorRow->row_tag;
                $labelTag = $decoratorRow->label_tag;
                $inputTag = $decoratorRow->input_tag;
                $showLabel = true;
                if ($isSubmit) {
                    $showLabel = false;
                }
                $input->setDecorators(ZFactory::createInputDecorator($labelTag,$inputTag,$rowTag, $showLabel));
            }

            //add input to form
            $form->addElement($input);
        }

        //set method
        $form->setMethod($stepRow->method);

        //only add submit if they didn't
        if (!$hasSubmit) {
            //$input = ZFactory::createSubmitElement('zforms_submit',1);
            $input = ZFactory::createElement('submit','zforms_submit',1);
            $input->setLabel('Submit');
            
            //set decorators
            if (empty($decoratorRow)) {
                $input->setDecorators(ZFactory::createInputDecorator());
            } else {
                $rowTag = $decoratorRow->row_tag;
                $labelTag = $decoratorRow->label_tag;
                $inputTag = $decoratorRow->input_tag;
                $showLabel = true;
                $input->setDecorators(ZFactory::createInputDecorator($labelTag, $inputTag, $rowTag, false));
            }
            
            $form->addElement($input);
        }

        //strip stuff we don't need eg submit inputs
        $emptyRowData = self::getEmptyFormKeyStepData($key,$step);
        
        if ($populate) {
            if (isset($_SESSION['zforms']['valid'][$key][$step])) {
                $formData = $_SESSION['zforms']['valid'][$key][$step];
                $formData = ZRequest::hydrateRowData($emptyRowData,$formData);
                //already know it's valid - calling isValid here will only mess with recaptcha
                $form->populate($formData);
                //don't unset session key                
            } else if (isset($_SESSION['zforms']['invalid'][$key][$step])) {
                $formData = $_SESSION['zforms']['invalid'][$key][$step];
                $formData = ZRequest::hydrateRowData($emptyRowData,$formData);
                $isValid = $form->isValid($formData); //need to do this in order to show error msgs
                $form->populate($formData);
                //don't unset session key                                
            } else if (ZRequest::isSubmitted($key, $step)) {
                //something went wrong if we get here
                $formData = ZRequest::getRequestParams();
                $formData = ZRequest::hydrateRowData($emptyRowData, $formData);
                $isValid = $form->isValid($formData); //show error msgs
                $form->populate($formData);
            }
        }
        
        //just to make sure
        $form->getElement('zforms_key')->setValue($key);
        $form->getElement('zforms_step')->setValue($step);
        
        return $form;
    }

    //retrieve all inputs (all steps) - only called after completing all form steps
    //get an associative array of inputs except submit and recaptcha
    public static function getEmptyFormData($key) {
        $formData = array();            
        //ugh. 
        $formRow = self::getFormRow($key);
        $formId = (int) $formRow->id;
        if (!$formId) {
            return array();
        }
        $stepModel = ZFactory::createModel('zform_step');
        $stepRows = $stepModel->fetchAll($stepModel->select()->where('form_id = ?',$formId));
        if (empty($stepRows)) {
            return array();
        }
        $inputModel = ZFactory::createModel('zform_input');
        foreach($stepRows as $stepRow) {
            $stepId = $stepRow->id;
            $inputRows = $inputModel->fetchAll($inputModel->select()->where('form_step_id = ?', $stepId));
            if (!empty($inputRows)) {
                foreach($inputRows as $inputRow) {
                    $inputId = $inputRow->id;
                    //submit and recaptcha are not part of the row data
                    if ($inputRow->type != 'submit' && 
                        strpos($inputRow->type,'recaptcha') === false) {

                        $formData[$inputRow->name] = '';
                    }
                }
            }
        }
        return $formData;
    }

    //the name says it all
    public static function getFormRow($key) {
        $formModel = ZFactory::createModel('zform');
        $select = $formModel->select();
        $select->where('code = ?', $key);
        $formRow = $formModel->fetchRow($select);
        if (empty($formRow)) {
              return false;
        }
        return $formRow;
    }
    
    //get empty array for a step, by formKey
    //mostly called by catchZForm while progressing/validating steps
    //could probably include captcha stuff here, but still exclude submits
    public static function getEmptyFormKeyStepData($formKey,$step) {
        $formModel = ZFactory::createModel('zform');
        
        $formRow = $formModel->fetchRow($formModel->select()->where('code = ?',$formKey));
        if (empty($formRow->id)) {
            return false;
        }
        $formId = $formRow->id;
        
        $stepModel = ZFactory::createModel('zform_step');
        $select = $stepModel->select();
        $select->where('form_id = ?',$formId);
        $select->where('step = ?',$step);
        $stepRow = $stepModel->fetchRow($select);
        if (empty($stepRow)) {
            return false;
        }
        $stepId = $stepRow->id;
        
        $inputModel = ZFactory::createModel('zform_input');
        $inputSelect = $inputModel->select()->where('type != \'submit\'')->where('form_step_id = ?', $stepId);
        $inputRows = $inputModel->fetchAll($inputSelect);
        
        $inputRowData = array();
        if (!empty($inputRows)) {
            foreach($inputRows as $inputRow) {
                $inputRowData[$inputRow->name] = '';
            }
        }      
        return $inputRowData;
    }

    //get a step within a form
    public static function getFormStepRow($formId,$step) {
        $stepModel = ZFactory::createModel('zform_step');
        $select = $stepModel->select();
        $select->where('form_id = ?',$formId);
        $select->where('step = ?',$step);
        $stepRow = $stepModel->fetchRow($select);
        if (empty($stepRow)) {
            return false;
        }
        return $stepRow;
    }

    //retrieve a validator for an input
    public static function getValidatorRow($validatorId) {
        $validatorModel = ZFactory::createModel('zform_validator');
        return $validatorModel->fetchRow($validatorModel->select()->where('id = ?',$validatorId));
    }

    //retrieve a recaptcha
    public static function getRecaptchaRow($recaptchaId) {
        $recaptchaModel = ZFactory::createModel('zform_recaptcha');
        return $recaptchaModel->fetchRow($recaptchaModel->select()->where('id = ?',$recaptchaId));
    }

    //see if a form step is the last one
    public static function isLastStep($formId,$step) {
        $stepModel = ZFactory::createModel('zform_step');
        $select = $stepModel->select();
        $select->where('form_id = ?',$formId);
        $select->where('step > ?',$step);
        $stepRow = $stepModel->fetchRow($select);
        if (empty($stepRow)) {
            return true;
        }
        return false;
    }

    //see if a form has a recaptcha
    public static function hasRecaptcha($key, $step = 0) {
        $hasRecaptcha = false;
        $formRow = self::getFormRow($key);
        if (empty($formRow->id)) {
            return false;
        }
        $formId = $formRow->id;
        $stepModel = ZFactory::createModel('zform_step');
        $stepSelect = $stepModel->select();
        $stepSelect->where('form_id = ?', $formId);
        if ($step > 0) {
            $stepSelect->where('step = ?', $step);
        }
        $stepRows = $stepModel->fetchAll($stepSelect);
        if (empty($stepRows)) {
            return false;
        }
        $inputModel = ZFactory::createModel('zform_input');
        foreach($stepRows as $stepRow) {
            $stepId = $stepRow->id;
            $select = $inputModel->select();
            $select->where('form_step_id = ?', $stepId);
            $select->where("type like 'recaptcha%'");
            $inputRows = $inputModel->fetchAll($select);
            if (!empty($inputRows)) {
                $hasRecaptcha = true;
                break;
            }
        }
        return $hasRecaptcha;
    }
}
