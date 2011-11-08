<?php if (!$records) :?>
  <?php include_partial('seegnoPagesAdmin/list_actions_no_root', array('model' => $model, 'field' => $field)) ?>
<?php else : ?>
  <?php include_partial('seegnoPagesAdmin/list_actions_tree', array('model' => $model, 'field' => $field)) ?>
<?php endif; ?>