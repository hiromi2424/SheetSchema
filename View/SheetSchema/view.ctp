<?php $this->extend('menu'); ?>
<h2><?php echo h($worksheets->title); ?></h2>

<p>
	<?php echo $this->Html->link(__d('sheet_schema', 'Open this spreadsheet'), $worksheets->link[0]['href']); ?>
</p>

<p>
	<?php echo __d('sheet_schema', 'Following tables will be created'); ?>:
</p>

<ul>
	<?php foreach ($worksheets->entry as $worksheet): ?>
		<?php if (empty($settings['ignored_worksheet']) || $worksheet->title != $settings['ignored_worksheet']): ?>
			<li><?php echo h($worksheet->title); ?></li>
		<?php endif; ?>
	<?php endforeach;  ?>
</ul>

<?php if (!empty($errors)): ?>
	<p class="warning"><?php echo __d('cake_schema', 'Worksheets have some errors:'); ?></p>
<ul class="error">
	<?php foreach ($errors as $error): ?>
		<li><?php echo h($error); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($sql)): ?>
	<h3><?php echo __d('cake_schema', 'Following sqls will be processed:'); ?></h3>
<ul>
	<?php foreach ($sql as $s): ?>
		<li><?php echo h($s); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php $this->start('menu'); ?>
<li><a href="<?php echo $this->Html->url(array('action' => $this->request->action, $key, !$showSql)); ?>"><?php echo $showSql ? __d('cake_schema', 'Hide SQL') : __d('cake_schema', 'Show SQL'); ?></a></li>
<li><?php echo $this->Form->postLink(__d('cake_schema', 'Import to DB'), array('action' => 'process', $key)); ?></li>
<?php $this->end('menu'); ?>