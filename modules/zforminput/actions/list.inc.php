<?php
    //zform inputs

    $formId = ZRequest::getParam('form_id');
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $formStepId = ZRequest::getParam('form_step_id');
    $stepModel = ZFactory::createModel('zform_step');
    $stepRow = $stepModel->fetchRow($stepModel->select()->where('id = ?',$formStepId));
    $step = '';
    if (!empty($stepRow->step)) {
        $step = $stepRow->step;
    }
    
    $formInputModel = ZFactory::createModel('zform_input');
    $formInputSelect = $formInputModel->select()->where("form_step_id = ?",$formStepId)->order('order');
    $formInputRows = $formInputModel->fetchAll($formInputSelect);

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zforminput&amp;form_id=$formId&amp;form_step_id=$formStepId";
    $addUrl = $pageUrl . "&amp;zform_action=add";

    $backUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformstep&amp;form_id=$formId";

    //handy block function. this type of stuff may evolve within this api
    function getRowHtml($row,$pageUrl,$formId,$formStepId) {

        $rowName = $row->label;
        $rowId = $row->id;

        $optionTypes = array('radio','select');
        
        $isRequired = 'no';
        if ($row->required) {
            $isRequired = 'yes';
        }
        
        $pageUrl .= "&amp;form_input_id=" . $rowId;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";

        $optionsUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zforminputoption&amp;form_id=$formId&amp;form_step_id=$formStepId&amp;form_input_id=$rowId";
        $validatorsUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zforminputvalidator&amp;form_id=$formId&amp;form_step_id=$formStepId&amp;input_id=$rowId";

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"input-$rowId\">
    		<td>
                <strong><a title=\"Edit “{$rowName}”\" href=\"{$editUrl}\" class=\"row-title\">{$rowName}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"{$editUrl}\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"{$trashUrl}\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a></span>";

        if (in_array(strtolower($row->type),$optionTypes)) {
            $html .= "\n | <span class=\"edit\"><a title=\"View options\" href=\"{$optionsUrl}\">Options</a></span>\n";
        }

        $html .= "\n | <span class=\"edit\"><a title=\"View validators\" href=\"{$validatorsUrl}\">Validators</a></span>\n";

        $html .="
                </div>
            </td>
            <td>{$row->order}</td>
            <td>{$row->name}</td>
            <td>{$row->type}</td>
            <td>{$isRequired}</td>
            <td>{$row->default_value}</td>
         </tr>
        ";
        return $html;
    }
?>
<div>
 <p>
  <a href="<?php echo $adminUrl; ?>">Forms</a> &nbsp;&gt;
  <a href="<?php echo $backUrl; ?>">Steps</a> &nbsp;&gt;
  Inputs
 </p>
</div>
<div><p>Step <?php echo $step; ?> in &quot;<?php echo $formTitle; ?>&quot;</p></div>
<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Form Inputs <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Input</a> </h2>
 
  <table cellspacing="0" class="widefat">
	<thead>
	<tr>
	<th style="" class="manage-column" width="16%">Label</th>
	<th style="" class="manage-column" width="16%">Order</th>
	<th style="" class="manage-column" width="16%">Name</th>
	<th style="" class="manage-column" width="16%">Type</th>
    <th style="" class="manage-column" width="16%">Required</th>
    <th style="" class="manage-column" width="16%">Default</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column" width="16%">Label</th>
	<th style="" class="manage-column" width="16%">Order</th>
	<th style="" class="manage-column" width="16%">Name</th>
	<th style="" class="manage-column" width="16%">Type</th>
    <th style="" class="manage-column" width="16%">Required</th>
    <th style="" class="manage-column" width="16%">Default</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($formInputRows)) {
        foreach($formInputRows as $row) {
            echo getRowHtml($row,$pageUrl,$formId,$formStepId);
        }
    }
?>
	</tbody>
   </table>
</div>
