<?php
/*
Quick example of tmp file storage index.
 */

/**
 * @var $this view
 * @var $feedbacks mixed
 */
foreach ($feedbacks as $feedback) {
  ?>

  <div class="media">
    <a class="pull-left" href="<?php echo $this->Url->build(array("plugin"=>"Feedback","controller"=>"Feedback","action"=>"viewimage", $feedback['filename']),true); ?>" target="_blank">
      <img class="media-object feedbackit-small-img" src="data:image/png;base64,<?php echo $feedback['screenshot']; ?>">
    </a>
    <div class="media-body">
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

      foreach ($feedback as $fieldname => $fieldvalue){
          echo '<br/>';
          echo "<b>".ucfirst($fieldname).":</b> $fieldvalue";
      }
      ?>
    </div>
  </div>

  <?php
}
?>
