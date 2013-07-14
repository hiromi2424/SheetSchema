<h2><?php echo __d('sheet_schema', 'Settings'); ?></h2>

<?php echo $this->Form->create(); ?>

<fieldset>
<legend><?php echo __d('sheet_schema', 'Google OAuth Settings'); ?></legend>

<p><?php echo __d('sheet_schema', 'Visit %s and choose a project then copy and paste values.', $this->Html->link(__d('sheet_schema', 'Google APIs Console'), 'https://code.google.com/apis/console')); ?></p>

<?php echo $this->Form->input('client_id', array('type' => 'text', 'label' => __d('sheet_schema', 'Client ID'))); ?>
<?php echo $this->Form->input('client_secret', array('type' => 'text', 'label' => __d('sheet_schema', 'Client secret'))); ?>
<?php echo $this->Form->input('redirect_uri', array('type' => 'url', 'label' => __d('sheet_schema', 'Redirect URI'), 'default' => Router::url(array(
	'controller' => 'sheet_schema',
	'action' => 'oauth2callback'
), true))); ?>

<p><?php echo __d(
	'sheet_schema',
	'You need to specify redirect url as like WEBROOT%s',
	preg_replace(
		sprintf('/^%s/', preg_quote($this->request->webroot, '/')),
		'',
		$this->Html->url(array(
		'controller' => 'sheet_schema',
		'action' => 'oauth2callback'
		))
	)
); ?></p>

</fieldset>

<fieldset>
<legend><?php echo __d('sheet_schema', 'Behavior Settings') ?></legend>

<?php echo $this->Form->input('ignored_worksheet', array('type' => 'text', 'label' => __d('sheet_schema', 'Workseet name to be ignored. (e.g. "cover", "index" or "note page".)'))); ?>
<?php echo $this->Form->input('database', array('label' => __d('sheet_schema', 'Default database settings in database.php'), 'default' => 'default')); ?>

</fieldset>

<fieldset>
<legend><?php echo __d('sheet_schema', 'Field Names') ?></legend>

<p><?php echo __d('sheet_schema', 'First column of rows(or row of columns with reverted sheet) is treated as field name. SheetSchema discovers it to understand what is a type of the rows(cols).'); ?></p>

<?php echo $this->Form->input('name_type', array('type' => 'text', 'label' => __d('sheet_schema', 'Type'), 'default' => 'type')); ?>
<?php echo $this->Form->input('name_length', array('type' => 'text', 'label' => __d('sheet_schema', 'Length'), 'default' => 'length')); ?>
<?php echo $this->Form->input('name_index', array('type' => 'text', 'label' => __d('sheet_schema', 'Index'), 'default' => 'index')); ?>
<?php echo $this->Form->input('name_null', array('type' => 'text', 'label' => __d('sheet_schema', 'Null'), 'default' => 'null')); ?>
<?php echo $this->Form->input('name_default', array('type' => 'text', 'label' => __d('sheet_schema', 'Default'), 'default' => 'default')); ?>
<?php echo $this->Form->input('name_comment', array('type' => 'text', 'label' => __d('sheet_schema', 'Comment'), 'default' => 'comment')); ?>
<?php echo $this->Form->input('name_initial_records', array('type' => 'text', 'label' => __d('sheet_schema', 'Initial Records'), 'default' => 'initial_records')); ?>


</fieldset>

<?php echo $this->Form->end(__d('sheet_schema', 'Save')); ?>

