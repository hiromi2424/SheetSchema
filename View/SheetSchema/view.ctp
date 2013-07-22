<?php $this->extend('menu'); ?>
<h2><?php echo h($worksheets->title); ?></h2>

<p><?php echo $this->Html->link(__d('sheet_schema', 'Open this spreadsheet'), $worksheets->link[0]['href']); ?></p>

<h3><?php echo __d('sheet_schema', 'Following tables will be created:'); ?></h3>

<ul>
	<?php foreach ($worksheets->entry as $worksheet): ?>
		<?php if (empty($settings['ignored_worksheet']) || $worksheet->title != $settings['ignored_worksheet']): ?>
			<li><?php echo h($worksheet->title); ?></li>
		<?php endif; ?>
	<?php endforeach;  ?>
</ul>

<?php if (!empty($errors)): ?>
	<?php echo $this->element('errors'); ?>
<?php endif; ?>

<?php if (!empty($sql)): ?>
	<h3><?php echo __d('cake_schema', 'Following sqls will be processed:'); ?></h3>
	<?php echo $this->element('sql') ?>
<?php endif; ?>

<?php $this->start('menu'); ?>
<li><a href="<?php echo $this->Html->url(array('action' => $this->request->action, $key, !$showSql)); ?>"><?php echo $showSql ? __d('cake_schema', 'Hide SQL') : __d('cake_schema', 'Show SQL'); ?></a></li>
<li><?php echo $this->Form->postLink(__d('cake_schema', 'Import to DB'), array('action' => 'process', $key)); ?></li>
<?php $this->end('menu'); ?>

<?php echo $this->element('syntax_highlight'); ?>
