<div class="users index">
	<h2><?php echo __('Files'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>Id</th>
			<th>Name</th>
            <th>Mime Type</th>
			<th>Created</th>
			<th>Modified</th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($files as $file): ?>
	<tr>
		<td><?php echo $file['id']; ?>&nbsp;</td>
		<td><?php echo h($file['title']); ?>&nbsp;</td>
        <td><?php echo h($file['mimeType']); ?>&nbsp;</td>
		<td><?php echo date("Y/m/d", strtotime($file['createdDate'])); ?>&nbsp;</td>
		<td><?php echo date("Y/m/d", strtotime($file['modifiedDate'])); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $file['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $file['id']), null, __('Are you sure you want to delete # %s?', $file['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
	
</div>
<div class="actions">
	<h3><?php echo __('Folders'); ?></h3>
	<ul>
        <li><a href="/google_api/drives/">All Files</a></li>
        <?php
        if(!empty($folders)){
            foreach($folders as $folder){
            ?>
        <li><a href="/google_api/drives/index/<?php echo $folder['id']; ?>"><?php echo $folder['title'];?></a></li>
            <?php
            }
        }
        ?>
	</ul>
</div>
