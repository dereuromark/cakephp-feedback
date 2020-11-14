<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\FeedbackItem $feedbackItem
 */
?>
<div class="row">
	<aside class="column large-3 medium-4 columns col-sm-4 col-12">
		<ul class="side-nav nav nav-pills flex-column">
			<li class="nav-item heading"><?= __('Actions') ?></li>
			<li class="nav-item"><?= $this->Form->postLink(
				__('Delete'),
				['action' => 'delete', $feedbackItem->id],
				['confirm' => __('Are you sure you want to delete # {0}?', $feedbackItem->id), 'class' => 'side-nav-item']
				) ?></li>
			<li class="nav-item"><?= $this->Html->link(__('List Feedback Items'), ['action' => 'index'], ['class' => 'side-nav-item']) ?></li>
		</ul>
	</aside>
	<div class="column-responsive column-80 form large-9 medium-8 columns col-sm-8 col-12">
		<div class="feedbackItems form content">
			<h1><?= __('Feedback Items') ?></h1>

			<h2><?php echo h($feedbackItem->url_short); ?></h2>
			<?= $this->Form->create($feedbackItem) ?>
			<fieldset>
				<legend><?= __('Edit Feedback Item') ?></legend>
				<?php
					echo $this->Form->control('subject');
					echo $this->Form->control('feedback');
					echo $this->Form->control('name');
					echo $this->Form->control('email');
					echo $this->Form->control('status');
				?>
			</fieldset>
			<?= $this->Form->button(__('Submit')) ?>
			<?= $this->Form->end() ?>
		</div>
	</div>
</div>
