<div>
 <p>
 <a href="<?php echo "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformemail"; ?>">Emails</a> &nbsp;
 <a href="<?php echo "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformhandler"; ?>">Handlers</a> &nbsp; 
 <a href="<?php echo "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformdecorator"; ?>">Decorators</a> &nbsp;
 <a href="<?php echo "admin.php?page=" . zform_controller_file . "&amp;zform_module=zformrecaptcha"; ?>">ReCaptchas</a> &nbsp;
 </p>
</div>
<?php
    //design decision
    if (in_array($action, array('add','edit','trash')) && empty($_POST)) {
        echo "<div><p><a href=\"javascript:;\" onclick=\"history.back();\">Back</a></p></div>";
    }