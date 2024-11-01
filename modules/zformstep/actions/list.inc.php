<?php

    //zform steps

    //parentId
    $formId = ZRequest::getParam('form_id');
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }

    //get rows
    $formStepModel = ZFactory::createModel('zform_step');
    $select = $formStepModel->select();
    $select->where('form_id = ?',$formId);
    $select->order('step asc');
    $formStepRows = $formStepModel->fetchAll($select);

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zformstep&amp;form_id=$formId";
    $addUrl = $pageUrl . "&amp;zform_action=add";

    //handy block function
    function getRowHtml($row,$pageUrl,$formId) {

        $pageUrl .= "&amp;form_step_id=" . $row->id;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";
        $adminUrl = "admin.php?page=" . zform_controller_file;
        $inputsUrl = $adminUrl .= "&amp;form_step_id={$row->id}&amp;form_id=$formId&amp;zform_module=zforminput&amp;action=list";

        $decorator = 'none';
        $decoratorModel = ZFactory::createModel('zform_decorator');
        $decoratorRow = $decoratorModel->fetchRow($decoratorModel->select()->where('id = ?',$row->decorator_id));
        if (!empty($decoratorRow->title)) {
            $decorator = $decoratorRow->title;
        }
        
        $rowTitle = "Step {$row->step}";

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"post-{$row->id}\">
    		<td>
                <strong><a title=\"Edit “{$rowTitle}”\" href=\"{$editUrl}\" class=\"row-title\">{$rowTitle}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"{$editUrl}\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"{$trashUrl}\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a> | </span>
                    <span class=\"view\"><a title=\"View “{$rowTitle} Inputs”\" href=\"{$inputsUrl}\">View Inputs</a></span>
                </div>
            </td>
            <td>{$row->method}</td>
            <td><a href=\"post.php?post={$row->next_post_id}&amp;action=edit\">{$row->next_post_id}</a></td>
            <td>{$decorator}</td>
            <td>{$row->requires_key}</td>
         </tr>
        ";
        return $html;
    }
?>
<div>
 <p>
  <a href="<?php echo $adminUrl; ?>">Forms</a> &nbsp;&gt;
  Steps
 </p>
</div>
<div><p>Steps in &quot;<?php echo $formTitle; ?>&quot;</p></div>
<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Steps <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Step</a></h2>

  <table cellspacing="0" class="widefat post fixed">
	<thead>
	<tr>
	<th style="" class="manage-column">Step</th>
	<th style="" class="manage-column">Method</th>
    <th style="" class="manage-column">Next Post</th>
    <th style="" class="manage-column">Decorator</th>
    <th style="" class="manage-column">Reqd. Session Key</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column">Step</th>
	<th style="" class="manage-column">Method</th>
    <th style="" class="manage-column">Next Post</th>
    <th style="" class="manage-column">Decorator</th>
    <th style="" class="manage-column">Reqd. Session Key</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($formStepRows)) {
        foreach($formStepRows as $row) {
            echo getRowHtml($row,$pageUrl,$formId);
        }
    }
?>
	</tbody>
   </table>
  </div>
