<h2><?php echo __d('sheet_schema', 'OAuth Login via Google'); ?></h2>

<p>
	<?php echo __d('sheet_schema', 'Please click a link below to authenticate with Google.'); ?><br>
	<?php echo $this->Html->link(__d('sheet_schema', 'Authenticate'), array('action' => 'login')); ?>
</p>