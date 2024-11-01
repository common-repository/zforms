<?php

    //parentId basically
    $foreignId = ZRequest::getParam('input_id');
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

    //get validator rows
    $rowModel = ZFactory::createModel('zform_input_validator');
    $select = $rowModel->select();
    $select->where('input_id = ?',$foreignId);
    $rows = $rowModel->fetchAll($select);

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zforminputvalidator&amp;input_id=$foreignId&form_id=$formId&amp;form_step_id=$formStepId";
    $addUrl = $pageUrl . "&amp;zform_action=add";

    $backUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zforminput&amp;form_step_id=$formStepId&amp;form_id=$formId";
    $stepsUrl = "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformstep&amp;form_id=$formId";

    //handy block function
    function getRowHtml($row,$pageUrl,$foreignId) {

        $pageUrl .= "&amp;input_validator_id=" . $row->id;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";
        $adminUrl = "admin.php?page=" . zform_controller_file;
        $rowTitle = $row->type;

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"zforminputvalidator-{$row->id}\">
    		<td class=\"post-title column-title\">
                <strong><a title=\"Edit “{$rowTitle}”\" href=\"$editUrl\" class=\"row-title\">{$rowTitle}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"$editUrl\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"$trashUrl\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a></span>
                </div>
            </td>
            <td>{$row->message}</td>
            <td>{$row->extra}</td>
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
  Validators
 </p>
</div>
<div><p>Input: &quot;<?php echo $inputTitle;?>&quot; in Step <?php echo $step;?> of &quot;<?php echo $formTitle;?>&quot;</p></div>
<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Input Validators <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Input Validator</a> </h2>

  <table cellspacing="0" class="widefat post fixed">
	<thead>
	<tr>
	<th style="" class="manage-column column-title">Type</th>
    <th style="" class="manage-column column-title">Message</th>
	<th style="" class="manage-column column-author">Extra</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column column-title">Type</th>
    <th style="" class="manage-column column-title">Message</th>
	<th style="" class="manage-column column-author">Extra</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($rows)) {
        foreach($rows as $row) {
            echo getRowHtml($row,$pageUrl,$foreignId);
        }
    }
?>
	</tbody>
   </table>
</div>
