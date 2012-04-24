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

	<table id=mainTable>
		<tr>
			<td id="header">				
				<h3 style="font:bold 24px arial; color:black; text-shadow: 4px 4px 0px white; float:left"> 
					<a href='/calendar'  style='font-size:15px;'>. . . <span style='font-size:35px;'>C</span>alendar</a>
				</h3>
				<div style='float:right;'>
				<?php 
					if($this->action != 'login'){
						$logout = $this->Html->link('logout', array('controller'=>'users', 'action'=>'logout'));
						$profile = $this->Html->link('user profile', array('controller'=>'users', 'action'=>'edit'));
						$help = $this->Html->link('help', '/pages/help.html');
						echo "$logout | $profile | $help";	
					}
				?>
				</div>
			
			</td>
		</tr>
		<tr><td>	<span style="font-size:5px;color:white;" >. . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . </span>
			</td></tr>
		<tr>
			<td style="width:900px; height:600px;">
			<div id="content">
				<?php echo $content_for_layout; ?>
			</div>
			
			</td>
		</tr>
		
		<tr>
			<td>			
		
				<div id="footer">
				
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

			</td>
		</tr>
	</table>
		
</body>
</html>






	<?php //echo $this->element('sql_dump'); ?>
