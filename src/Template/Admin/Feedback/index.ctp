<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\Feedbackstore[] $feedbacks
 */

foreach ($feedbacks as $feedback): ?>
  <div class="media">
    <a class="pull-left" href="<?= $this->Url->build(["plugin"=>"Feedback","controller"=>"Feedback","action"=>"viewimage", $feedback['filename']], true); ?>" target="_blank">
      <img class="media-object feedbackit-small-img" src="data:image/png;base64,<?= $feedback['screenshot']; ?>">
    </a>
    <div class="media-body">
		<div class="pull-right">
			<?= $this->Form->postLink('Remove', ['action' => 'remove', $feedback['filename']], ['confirm' => 'Sure?']); ?>
		</div>

		<h4 class="media-heading"><?= $feedback['subject'] . ' <i>(' . date('d-m-Y H:i:s', $feedback['time']) . ')</i>';?></h4>
		<b><?= $feedback['feedback']; ?></b>

		<?php
		//Unset the already displayed vars and loop throught the next. Saves us some coding when a new var is added to the feedback
		unset($feedback['subject']);
		unset($feedback['feedback']);
		unset($feedback['screenshot']);
		unset($feedback['time']);
		unset($feedback['filename']);
		unset($feedback['copyme']);

		foreach ($feedback as $fieldname => $fieldvalue){
			echo '<br/>';
			echo "<b>" . ucfirst($fieldname) . ":</b> $fieldvalue";
		}
?>
    </div>
  </div>
<?php endforeach; ?>
