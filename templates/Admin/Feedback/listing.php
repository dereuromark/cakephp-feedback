<?php
/**
 * @var \App\View\AppView $this
 * @var \Feedback\Model\Entity\Feedbackstore[] $feedbacks
 */

use Cake\Core\Configure;
?>

<div class="feedback index content large-9 medium-8 columns col-sm-8 col-12">

<h1><?php echo count($feedbacks); ?> Feedback records</h1>

<?php
foreach ($feedbacks as $feedback) {
?>

  <div class="media">
	<a class="pull-left float-left" href="<?php echo $this->Url->build(['plugin'=>'Feedback','controller'=>'Feedback','action'=>'viewimage', $feedback['filename']]); ?>" target="_blank">
	  <img class="media-object feedbackit-small-img" src="data:image/png;base64,<?php echo $feedback['screenshot']; ?>">
	</a>
	<div class="media-body">
		<div class="pull-right float-right">
			<?php echo $this->Form->postLink('Remove', ['action' => 'remove', $feedback['filename']], ['confirm' => 'Sure?']); ?>
		</div>

		<h4 class="media-heading"><?php echo $feedback['subject'] . ' <i>(' . date('d-m-Y H:i:s',$feedback['time']) . ')</i>';?></h4>
		<b><?php echo $feedback['feedback'];?></b>

		<?php
		//Unset the already displayed vars and loop throught the next. Saves us some coding when a new var is added to the feedback
		unset($feedback['subject']);
		unset($feedback['feedback']);
		unset($feedback['screenshot']);
		unset($feedback['time']);
		unset($feedback['filename']);
		unset($feedback['copyme']);

		foreach ($feedback as $fieldname => $fieldvalue) {
			if ($fieldname === 'url' && Configure::read('Feedback.autoLink')) {
				$fieldvalue = '<a href="' . $fieldvalue . '" target="_blank">' . $fieldvalue . '</a>';
			} else {
				$fieldvalue = h($fieldvalue);
			}

			echo '<br/>';
			echo '<b>' . ucfirst($fieldname) . ":</b> $fieldvalue";
		}
?>
	</div>
  </div>

  <?php
}
?>

</div>
