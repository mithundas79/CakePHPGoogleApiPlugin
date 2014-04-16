<div class="users view">
<h2><?php echo h($file->title); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo $file->id; ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($file->title); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo date("Y/m/d", strtotime($file->createdDate)); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo date("Y/m/d", strtotime($file->modifiedDate)); ?>
			&nbsp;
		</dd>
	</dl>
<div style="width: 90%; height: 90%;">
    <iframe width="100%" height="100%" frameborder="0" scrolling="yes" allowtransparency="true" src="<?php echo $file->getWebContentLink(); ?>"></iframe>
    </div>

</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php //echo $this->Html->link(__('Edit User'), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete File'), array('action' => 'delete', $file->id), null, __('Are you sure you want to delete # %s?', $file->id)); ?> </li>
		<li><?php echo $this->Html->link(__('List Files'), array('action' => 'index')); ?> </li>
		<li><?php //echo $this->Html->link(__('New User'), array('action' => 'add')); ?> </li>
	</ul>
</div>
