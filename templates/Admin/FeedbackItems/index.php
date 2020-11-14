<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\FeedbackItem[]|\Cake\Collection\CollectionInterface $feedbackItems
 */

use Cake\Core\Plugin;

?>
<nav class="actions large-3 medium-4 columns col-sm-4 col-xs-12" id="actions-sidebar">
	<ul class="side-nav nav nav-pills flex-column">
		<li class="nav-item heading"><?= __('Actions') ?></li>
		<li class="nav-item">
			<?= $this->Html->link(__('Back'), ['controller' => 'Feedback', 'action' => 'index'], ['class' => 'nav-link']) ?>
		</li>
	</ul>
</nav>
<div class="feedbackItems index content large-9 medium-8 columns col-sm-8 col-12">

	<h2><?= __('Feedback Items') ?></h2>

	<div class="">
		<table class="table table-sm table-striped">
			<thead>
				<tr>
					<th><?= $this->Paginator->sort('sid') ?></th>
					<th><?= $this->Paginator->sort('url') ?></th>
					<th><?= $this->Paginator->sort('subject') ?></th>
					<th><?= $this->Paginator->sort('created', null, ['direction' => 'desc']) ?></th>
					<th class="actions"><?= __('Actions') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($feedbackItems as $feedbackItem): ?>
				<tr>
					<td><?= h($feedbackItem->sid) ?></td>
					<td><?= h($feedbackItem->url) ?></td>
					<td><?= h($feedbackItem->name) ?></td>
					<td><?= h($feedbackItem->email) ?></td>
					<td><?= h($feedbackItem->subject) ?></td>
					<td><?= $this->Number->format($feedbackItem->status) ?></td>
					<td><?= $this->Time->nice($feedbackItem->created) ?></td>
					<td class="actions">
						<?php echo $this->Html->link(isset($this->Format) ? $this->Format->icon('view') : __('View'), ['action' => 'view', $feedbackItem->id], ['escapeTitle' => false]); ?>
						<?php echo $this->Html->link(isset($this->Format) ? $this->Format->icon('edit') : __('Edit'), ['action' => 'edit', $feedbackItem->id], ['escapeTitle' => false]); ?>
						<?php echo $this->Form->postLink(isset($this->Format) ? $this->Format->icon('delete') : __('Delete'), ['action' => 'delete', $feedbackItem->id], ['escapeTitle' => false, 'confirm' => __('Are you sure you want to delete # {0}?', $feedbackItem->id)]); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php
	if (Plugin::isLoaded('Tools')) {
		echo $this->element('Tools.pagination');
	} else {
		echo $this->element('Feedback.pagination');
	}
	?>
</div>
