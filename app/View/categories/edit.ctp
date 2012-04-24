
<h3>Edit Category</h3>

<?php

echo $this->Form->create('Category', array('action' => 'edit'));
echo $this->Form->input('name');
echo $this->Form->end('save');

echo $this->Html->link('cancel', array('action' => 'index'));

?>


