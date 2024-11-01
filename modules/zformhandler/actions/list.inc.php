<?php

    //get rows
    $rowModel = ZFactory::createModel('zform_handler');
    $select = $rowModel->select();
    $select->order("title asc");
    $rows = $rowModel->fetchAll($select);

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=zformhandler";
    $addUrl = $pageUrl . "&amp;zform_action=add";

    //handy block function
    function getRowHtml($row,$pageUrl) {

        $pageUrl .= "&amp;handler_id=" . $row->id;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";
        $adminUrl = "admin.php?page=" . zform_controller_file;

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"handler-{$row->id}\">
    		<td>
                <strong><a title=\"Edit “{$row->title}”\" href=\"$editUrl\" class=\"row-title\">{$row->title}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"$editUrl\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"$trashUrl\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a></span>
                </div>
            </td>
            <td>{$row->filename}</td>
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
 <h2>Form Handlers <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Form Handler</a> </h2>
 <form>


  <table cellspacing="0" class="widefat post fixed">
	<thead>
	<tr>
	<th style="" class="manage-column">Title</th>
	<th style="" class="manage-column">Filename</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column">Title</th>
	<th style="" class="manage-column">Filename</th>
	</tr>
	</tfoot>

	<tbody>
<?php
    //output rows
    if (!empty($rows)) {
        foreach($rows as $row) {
            echo getRowHtml($row,$pageUrl);
        }
    }
?>
	</tbody>
   </table>
  </form>
</div>
