<?php
    //zform emails

    $formEmailModel = ZFactory::createModel('zform_email');
    $formEmailRows = $formEmailModel->fetchAll();

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zformemail";
    $addUrl = $pageUrl . "&amp;zform_action=add";
    
    //handy block function 
    function getRowHtml($row,$pageUrl) {

        $pageUrl .= "&amp;form_email_id=" . $row->id ;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";
        $rowTitle = $row->title;
        
        $to = 'dynamic';
        $from = 'dynamic';
        if (!empty($row->to)) {
            $to = $row->to;
        }
        if (!empty($row->from)) {
            $form = $row->from;
        }

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"post-{$row->id}\">
    		<td>
                <strong><a title=\"Edit “{$row->title}”\" href=\"{$editUrl}\" class=\"row-title\">{$row->title}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"{$editUrl}\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"{$trashUrl}\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a></span>
                </div>
            </td>
            <td>{$from}</td>
            <td>{$to}</td>
            <td>{$row->subject}</td>
         </tr>
        ";
        return $html;
    }
?>
<div>
<p><a href="<?php echo $adminUrl; ?>">Forms</a></p>
</div>
<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Form Emails <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Form Email</a> </h2>
  <table cellspacing="0" class="widefat">
	<thead>
	<tr>
	<th style="" class="manage-column">Title</th>
	<th style="" class="manage-column">From</th>
	<th style="" class="manage-column">To</th>
    <th style="" class="manage-column">Subject</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column">Title</th>
	<th style="" class="manage-column">From</th>
	<th style="" class="manage-column">To</th>
    <th style="" class="manage-column">Subject</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($formEmailRows)) {
        foreach($formEmailRows as $row) {
            echo getRowHtml($row,$pageUrl);
        }
    }
?>
    </tbody>
    
   </table>
</div>
