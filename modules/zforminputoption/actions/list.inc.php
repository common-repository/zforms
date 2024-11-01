<?php
    //zform input options

     //parentId basically
    $foreignId = ZRequest::getParam('form_input_id');
    $formId = ZRequest::getParam('form_id');
    $formStepId = ZRequest::getParam('form_step_id');

    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $stepModel = ZFactory::createModel('zform_step');
    $stepRow = $stepModel->fetchRow($stepModel->select()->where('id = ?',$formStepId));
    $step = '';
    if (!empty($stepRow->step)) {
        $step = $stepRow->step;
    }
    
    $formInputModel = ZFactory::createModel('zform_input');
    $formInputRow = $formInputModel->fetchRow($formInputModel->select()->where('id = ?', $foreignId));
    $inputTitle = '';
    if (!empty($formInputRow->label)) {
        $inputTitle = $formInputRow->label;
    }

    $inputOptionModel = ZFactory::createModel('zform_input_option');
    $inputOptionRows = $inputOptionModel->fetchAll($inputOptionModel->select()->where("input_id = ?",$foreignId));

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zforminputoption&amp;form_id=$formId&amp;form_step_id=$formStepId&amp;form_input_id=$foreignId";
    $addUrl = $pageUrl . "&amp;zform_action=add";

    $backUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zforminput&amp;form_step_id=$formStepId&amp;form_id=$formId";
    $stepsUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformstep&amp;form_id=$formId";

    //handy block function. this type of stuff may evolve within this api
    function getRowHtml($row,$pageUrl) {

        $rowId = $row->id;
        $rowName = $row->label;
        
        $pageUrl .= "&amp;form_input_option_id=" . $rowId;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"input-$rowId\">
    		<td>
                <strong><a title=\"Edit “$rowName”\" href=\"$editUrl\" class=\"row-title\">$rowName</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"$editUrl\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"$trashUrl\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a></span>
                </div>
            </td>
            <td>{$row->order}</td>
            <td>{$row->value}</td>
         </tr>
        ";
        return $html;
    }
?>
<div>
 <p>
  <a href="<?php echo $adminUrl; ?>">Forms</a> &nbsp;&gt;
  <a href="<?php echo $stepsUrl; ?>">Steps</a> &nbsp;&gt;
  <a href="<?php echo $backUrl; ?>">Inputs</a> &nbsp;&gt;
  Options
 </p>
</div>
<div><p>Input: &quot;<?php echo $inputTitle;?>&quot; in Step <?php echo $step;?> of &quot;<?php echo $formTitle;?>&quot;</p></div>
<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Input Options <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Option</a> </h2>

  <table cellspacing="0" class="widefat">

	<thead>
	<tr>
	<th style="" class="manage-column">Label</th>
	<th style="" class="manage-column">Order</th>
	<th style="" class="manage-column">Value</th>

	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column">Label</th>
	<th style="" class="manage-column">Order</th>
	<th style="" class="manage-column">Value</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($inputOptionRows)) {
        foreach($inputOptionRows as $row) {
            echo getRowHtml($row,$pageUrl);
        }
    }
?>
	</tbody>
   </table>
</div>