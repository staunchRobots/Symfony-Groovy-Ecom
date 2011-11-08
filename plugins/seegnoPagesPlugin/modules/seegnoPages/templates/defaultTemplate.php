<?php echo use_helper('seegnoPages') ?>

<div id="page">
  <div class="title"><?php echo seegno_slot($page, 'title', 'Text') ?></div>

  <div class="text">
    <?php echo seegno_slot($page, 'description', 'Textarea') ?>
  </div>
</div>
