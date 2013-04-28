<h2><?php echo __d('sheet_schema', 'Settings'); ?></h2>

<p><?php echo __d('sheet_schema', 'Visit %s and create project, then copy and paste values.', $this->Html->link(__d('sheet_schema', 'Google APIs Console'), 'https://code.google.com/apis/console')); ?></p>

<?php echo $this->Form->create(); ?>
<?php $this->Form->inputDefaults(array('type' => 'text')) ?>

<?php echo $this->Form->input('client_id', array('label' => __d('sheet_schema', 'Client ID'))); ?>
<?php echo $this->Form->input('client_secret', array('label' => __d('sheet_schema', 'Client secret'))); ?>
<?php echo $this->Form->input('redirect_uri', array('label' => __d('sheet_schema', 'Redirect URI'))); ?>

<?php echo $this->Form->end(__d('sheet_schema', 'Save')); ?>

