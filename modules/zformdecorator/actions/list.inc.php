<?php
    //zforms

    $decoratorModel = ZFactory::createModel('zform_decorator');
    $decoratorRows = $decoratorModel->fetchAll($decoratorModel->select()->order("title"));

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zformdecorator";
    $addUrl = $pageUrl . "&amp;zform_action=add";
    
    //handy block function 
    function getRowHtml($row,$pageUrl) {

        $pageUrl .= "&amp;decorator_id=" . $row->id;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"post-{$row->id}\">
    		<td>
                <strong><a title=\"Edit “{$row->title}”\" href=\"$stepsUrl\" class=\"row-title\">{$row->title}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"$editUrl\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"$trashUrl\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a> </span>   
                </div>
            </td>
            <td>{$row->container_tag}</td>
            <td>{$row->row_tag}</td>
            <td>{$row->label_tag}</td>
            <td>{$row->input_tag}</td>
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
 <h2>Form Decorators <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Decorator</a> </h2>
  <table cellspacing="0" class="widefat">
	<thead>
	<tr>
	<th style="" class="manage-column" width="20%">Title</th>
    <th style="" class="manage-column" width="20%">Container</th>
	<th style="" class="manage-column" width="20%">Row</th>
    <th style="" class="manage-column" width="20%">Label</th>
    <th style="" class="manage-column" width="20%">Input</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column" width="20%">Title</th>
    <th style="" class="manage-column" width="20%">Container</th>
	<th style="" class="manage-column" width="20%">Row</th>
    <th style="" class="manage-column" width="20%">Label</th>
    <th style="" class="manage-column" width="20%">Input</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($decoratorRows)) {
        foreach($decoratorRows as $row) {
            echo getRowHtml($row,$pageUrl);
        }
    }
?>
	</tbody>
   </table>
</div>
