
<h3>Edit Category</h3>

<?php

echo $this->Form->create('Category', array('action' => 'add'));
echo $this->Form->input('name');
echo $this->Form->end('save');


?>

