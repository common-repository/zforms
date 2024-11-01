<?php
    //zforms
    $formModel = ZFactory::createModel('zform');
    $dbPrefix = ZFactory::getDbPrefix();
    
    //because of prefix
    $formTableStr = $dbPrefix . 'zform';
    $handlerTableStr = $dbPrefix . 'zform_handler';
    $emailTableStr = $dbPrefix . 'zform_email';

    //pull rows of forms and left join on a few optional settings
    $formSelect = $formModel->select();
    $formSelect->from(
        array('zform' => $formTableStr)
    )->joinLeft(
        array('zform_handler' => $handlerTableStr),
        'zform.handler_id = zform_handler.id',
        array('handler_title' => 'title')
    )->joinLeft(
        array('email_success' => $emailTableStr),
        'zform.success_email_id = email_success.id',
        array('success_email_title' => 'title')
    )->joinLeft(
        array('email_error' => $emailTableStr),
        'zform.error_email_id = email_error.id',
        array('error_email_title' => 'title')
    );
    $formSelect->order('title');
    $formSelect->setIntegrityCheck(false);
    $formRows = $formModel->fetchAll($formSelect);

    $pageUrl = "admin.php?page=" . zform_controller_file;
    $addUrl = $pageUrl . "&amp;zform_action=add";
    
    //handy block function 
    function getRowHtml($row,$pageUrl) {

        $pageUrl .= "&amp;form_id=" . $row->id;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";
        $stepsUrl = $pageUrl . "&amp;zform_module=zformstep&amp;zform_action=list";
        
        $handlerTitle = 'none';
        if ($row->handler_id > 0) {
            $handlerTitle = '(blank)';
            if (!empty($row->handler_title)) {
                $handlerTitle = $row->handler_title;
            }
        }
        $rowTitle = $row->title;
        
        $successEmailTitle = 'none';
        if ($row->success_email_id > 0) {
            $successEmailTitle = '(blank)';
            if (!empty($row->success_email_title)) {
                $successEmailTitle = $row->success_email_title;
            }
        }
        
        $errorEmailTitle = 'none';
        if ($row->error_email_id > 0) {
            $errorEmailTitle = '(blank)';
            if (!empty($row->error_email_title)) {
                $errorEmailTitle = $row->error_email_title;
            }
        }
        
        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"form-{$row->id}\">
            <td>
                <strong><a title=\"Edit “{$rowTitle}”\" href=\"{$editUrl}\" class=\"row-title\">{$rowTitle}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"{$editUrl}\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"{$trashUrl}\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a> | </span>
                    <span class=\"view\"><a title=\"View “{$rowTitle}” Steps\" href=\"{$stepsUrl}\">View Steps</a></span>
                </div>
            </td>
            <td>{$row->code}</td>
            <td>{$handlerTitle}</td>
            <td>{$successEmailTitle}</td>
            <td>{$errorEmailTitle}</td>
         </tr>
        ";
        return $html;
    }
?>
<div>
<p>Forms</p>
</div>
<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Forms <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Form</a> </h2>
  <table cellspacing="0" class="widefat">
	<thead>
	<tr>
	<th style="" class="manage-column" width="20%">Title</th>
    <th style="" class="manage-column" width="20%">Key</th>
    <th style="" class="manage-column" width="20%">Handler</th>
	<th style="" class="manage-column" width="20%">Success Email</th>
    <th style="" class="manage-column" width="20%">Error Email</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
    <th style="" class="manage-column" width="20%">Title</th>
    <th style="" class="manage-column" width="20%">Key</th>
	<th style="" class="manage-column" width="20%">Handler</th>
    <th style="" class="manage-column" width="20%">Success Email</th>
    <th style="" class="manage-column" width="20%">Error Email</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($formRows)) {
        foreach($formRows as $row) {
            echo getRowHtml($row,$pageUrl);
        }
    }
?>
	</tbody>
   </table>
</div>
