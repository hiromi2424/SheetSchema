<?php $this->extend('menu'); ?>
<h2><?php echo h($worksheets->title); ?></h2>

<?php if (!empty($errors)): ?>
	<?php echo $this->element('errors'); ?>
<?php endif; ?>

<?php if (!empty($sql)): ?>
	<h3><?php echo __d('cake_schema', 'Following sqls were processed:'); ?></h3>
	<?php echo $this->element('sql') ?>
<?php endif; ?>

<?php echo $this->element('syntax_highlight'); ?>
