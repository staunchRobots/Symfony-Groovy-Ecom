<div class="sf_admin_actions">
  <?php include_partial('seegnoPagesAdmin/list_batch_actions') ?>
  <?php include_partial('seegnoPagesAdmin/list_actions', array('model' => $model, 'field' => $field, 'records' => $records)) ?>
</div>

<div class="nested_set_manager_holder" id="<?php echo strtolower($model) ?>_nested_set_manager_holder">
    <?php echo get_partial('seegnoPagesAdmin/nested_set_list', array('model' => $model, 'field' => $field, 'records' => $records)) ?>
    <div style="clear:both">&nbsp;</div>
</div>

