<?php $this->extend('menu'); ?>
<h2><?php echo h($worksheets->title); ?></h2>

<?php if (!empty($errors)): ?>
	<p class="warning"><?php echo __d('cake_schema', 'Worksheets have some errors:'); ?></p>
<ul class="error">
	<?php foreach ($errors as $error): ?>
		<li><?php echo h($error); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($sql)): ?>
	<h3><?php echo __d('cake_schema', 'Following sqls were processed:'); ?></h3>
<ul>
	<?php foreach ($sql as $s): ?>
		<li><?php echo h($s); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
