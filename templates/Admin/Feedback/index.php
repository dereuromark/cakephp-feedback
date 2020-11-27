<?php
/**
 * @var \App\View\AppView $this
 * @var string[] $stores
 * @var \Feedback\Model\Entity\FeedbackItem[]|null $feedbackItems
 */
?>

<h1>Feedback</h1>

<?php
$map = [
	\Feedback\Store\DatabaseStore::class => ['controller' => 'FeedbackItems'],
	\Feedback\Store\FilesystemStore::class => ['action' => 'listing'],
];
?>

<h2>Active Stores</h2>
<ul>
<?php
foreach ($stores as $store => $storeName) {
?>
	<li>
		<?php
		if (isset($map[$store])) {
			echo $this->Html->link($storeName, $map[$store]);
		} else {
			echo h($storeName);
		}
		?>
	</li>
  <?php
}
?>
</ul>

<?php if (!empty($feedbackItems)) { ?>
	<h3>New feedback</h3>
	<ul>
		<?php foreach ($feedbackItems as $feedbackItem) { ?>
		<li>
			<?php
			if ($feedbackItem->priority !== null) {
				echo '[' . $feedbackItem::priorities($feedbackItem->priority) . '] ';
			}
			echo $this->Html->link($feedbackItem->subject, ['controller' => 'FeedbackItems', 'action' => 'view', $feedbackItem->id]);
			?>
			<?php if (!empty($feedbackItem->name)) {
				echo ' by ' . h($feedbackItem->name);
			} ?>

			<div><small><?= $this->Time->nice($feedbackItem->created) ?></small></div>
		</li>
		<?php } ?>
	</ul>

<?php } ?>
