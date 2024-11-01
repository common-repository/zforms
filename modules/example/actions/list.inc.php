<?php

    //parentId basically
    $foreignId = ZRequest::getParam('foreign_id');

    //get rows
    $rowModel = ZFactory::createModel('example');
    $select = $rowModel->select();
    $select->where('foreign_id = ?',$foreignId);
    $select->order('first_name asc');
    $rows = $rowModel->fetchAll($select);

    $adminUrl = "admin.php?page=" . zform_controller_file;
    $pageUrl = $adminUrl . "&amp;zform_module=example&amp;foreign_id=$foreignId";
    $addUrl = $pageUrl . "&amp;zform_action=add";

    //handy block function
    function getRowHtml($row,$pageUrl,$foreignId) {

        $pageUrl .= "&amp;example_id=" . $row->id;
        $editUrl = $pageUrl . "&amp;zform_action=edit";
        $trashUrl = $pageUrl . "&amp;zform_action=trash";
        $adminUrl = "admin.php?page=" . zform_controller_file;
        $childrenUrl = $adminUrl .= "&amp;example_id={$row->id}&amp;foreign_id=$foreignId&amp;zform_module=foreignexample&amp;action=list";
        $rowTitle = $row->title;

        $html ="
        <tr valign=\"top\" class=\"alternate author-self status-publish iedit\" id=\"example-{$row->id}\">
    		<td class=\"post-title column-title\">
                <strong><a title=\"Edit “{$rowTitle}”\" href=\"$childrenUrl\" class=\"row-title\">{$rowTitle}</a></strong>
                <div class=\"row-actions\">
                    <span class=\"edit\"><a title=\"Edit this item\" href=\"$editUrl\">Edit</a> | </span>
                    <span class=\"trash\"><a href=\"$trashUrl\" title=\"Move this item to the Trash\" class=\"submitdelete\">Trash</a> | </span>
                    <span class=\"view\"><a title=\"View “{$rowTitle}”\" href=\"$childrenUrl\">View Child Rows</a></span>
                </div>
            </td>
            <td>{$row->first_name}</td>
            <td>{$row->last_name}</td>
         </tr>
        ";
        return $html;
    }
?>

<div class="wrap">
 <div class="icon32" id="icon-edit"><br></div>
 <h2>Examples <a class="button add-new-h2" href="<?php echo $addUrl;?>">Add Example</a> </h2>

  <table cellspacing="0" class="widefat post fixed">
	<thead>
	<tr>
	<th style="" class="manage-column column-title">First Name</th>
	<th style="" class="manage-column column-author">Last Name</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th style="" class="manage-column column-title">First Name</th>
	<th style="" class="manage-column column-author">Last Name</th>
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
