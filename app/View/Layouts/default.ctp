<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('main');
		echo $scripts_for_layout;
	?>
	
</head>

<body>
		<div id="header">
			
			<div class=wrapper>				
				
				<a href='<?php echo $this->base; ?>'  style='font:bold 17px Courier;'>. . . <span style='font-size:30px;'>C</span>alendar</a>
				
				<div style='float:right;'>
				<?php 
					if($this->action != 'login'){
						$logout = $this->Html->link('logout', array('controller'=>'users', 'action'=>'logout'));
						$profile = $this->Html->link('account', array('controller'=>'users', 'action'=>'edit'));
						$help = $this->Html->link('help', '/pages/help.html');
						echo "$logout | $profile | $help";
					}
				?>
				</div>
			</div>
		
		</div>
		
		<div>
			<div class=wrapper style="padding: 20px 10px 2px 25px;">
				<?php echo $content_for_layout; ?>	
			</div>
		</div>
		
		<div id="footer">
		
				<div class=wrapper>
				
				<?php
					// if(!($this->params['controller'] == 'entries' && $this->params['action'] == 'index'))
						// echo $this->Session->flash(); 
				?>
				
				<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt'=> __('CakePHP: the rapid development php framework', true), 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false));
				?>
				</div>

		</div>
	
		
</body>
</html>


