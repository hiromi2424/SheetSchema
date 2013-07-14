<h2><?php echo __d('sheet_schema', 'Welcome'); ?></h2>

<p>
	<?php echo __d('sheet_schema', 'Welcome sheet schema.'); ?><br>
	<?php echo __d('sheet_schema', 'First, %s to setup google api credentials.', $this->Html->link(__d('sheet_schema', 'configure settings'), array('controller' => 'SheetSchemaSettings', 'action' => 'index'))); ?>
</p>