<?php if ($sf_user->hasFlash('notice')): ?>
  <p class="flash notice"><?php echo html_entity_decode(__($sf_user->getFlash('notice'))) ?></p>
<?php elseif ($sf_user->hasFlash('error')): ?>
  <p class="flash error"><?php echo html_entity_decode(__($sf_user->getFlash('error'))) ?></p>
<?php elseif ($sf_user->hasFlash('info')): ?>
  <p class="flash info"><?php echo html_entity_decode(__($sf_user->getFlash('info'))) ?></p>
<?php endif ?>