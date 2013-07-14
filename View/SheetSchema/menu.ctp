<div class="<?php echo Inflector::underscore($this->request->controller); ?> <?php echo strtolower($this->request->action); ?>">
	<?php echo $this->fetch('content');  ?>
</div>

<div class="actions">
	<h3><?php echo __d('sheet_schema', 'Contents'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('sheet_schema', 'SheetSchema Home'), array('controller' => 'sheet_schema', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__d('sheet_schema', 'Settings'), array('controller' => 'sheet_schema_settings', 'action' => 'index')); ?></li>
	</ul>
</div>