
<!--
<div style="background:gray; width:700px;"><h2 class="white">Edit Calendar Entry</h2></div>
-->
<div style='padding:20px;'>

<?php

// pass in variable "formTitle" from controller as form is used in more than one place
echo $this->element("entryForm", array('formTitle' => 'Edit Calendar Entry'));
	
?>
</div>

