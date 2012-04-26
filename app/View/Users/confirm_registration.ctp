

<?php if($userFound == 1): ?>
<b>Activation is complete!</b>  Redirecting to calendar...

<script type=text/javascript>
	setTimeout(f, 10000);
	function f(){
		location = '/';
	}
</script>


<?php else: ?>

Activation failed. please contact the webmaster at ----.

<?php endif; ?>

