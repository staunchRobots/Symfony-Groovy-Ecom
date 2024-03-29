<div class="sf_admin_list">
  <?php if (!$roots->count()): ?>
    <p><?php echo __('No root', array(), 'sf_admin') ?></p>
  <?php else: ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <th class="sf_admin_text sf_admin_list_th_title"><?php echo ucfirst($field);?> </th>
          <th class="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>

      <tfoot>
        <tr>
          <th colspan="2"> <?php echo $roots->count() ;?> <?php echo __('Roots') ?></th>
        </tr>
      </tfoot>

      <tbody>
        <?php foreach ($roots as $i => $root): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
        <tr class="sf_admin_row <?php echo $odd ?>">
          <td class="sf_admin_text sf_admin_list_td_<?php echo $field ?>"><?php echo link_to($root->getTitle(), $sf_request->getParameter('module') . '/' . $sf_request->getParameter('action') . '?root=' . $root->id) ?></td>
          <td>
            <ul class="sf_admin_td_actions">
              <li class="sf_admin_action_edit"><?php echo link_to( __('Manage Pages Tree') ,$sf_request->getParameter('module') . '/' . $sf_request->getParameter('action') . '?root=' . $root->id);?></li>
            </ul>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="sf_admin_actions">
  <?php include_partial('seegnoPagesAdmin/list_batch_actions') ?>
  <?php include_partial('seegnoPagesAdmin/list_actions_no_root', array('model' => $model, 'field' => $field, 'root' => $root)) ?>
</div>