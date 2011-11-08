<?php if ($sf_user->isSuperAdmin() || $sf_user->hasCredential('edit_pages')): ?>
  <?php $route = isset($route) ? url_for($sf_data->getRaw('route')) : $sf_request->getUri() . '/edit' ?>

  <div class="cms">
    <?php if ($sf_params->get('edit')): ?>
      <?php if ($sf_params->get('cms') && $sf_params->get('slug')): ?>
        <?php echo link_to_with_path(__('Done'), '@page?slug=' . $sf_params->get('slug'), array('class' => 'cms_done')) ?>
      <?php else: ?>
        <?php echo link_to_with_path(__('Done'), str_replace('/edit', '', $route), array('class' => 'cms_done')) ?>
      <?php endif ?>
    <?php else: ?>
      <?php if ($sf_params->get('cms') && $sf_params->get('slug')): ?>
        <?php echo link_to_with_path(__('Edit Page'), '@page_edit?slug=' . $sf_params->get('slug'), array('class' => 'cms_edit')) ?>
      <?php else: ?>
        <?php echo link_to_with_path(__('Edit Page'), $route, array('class' => 'cms_edit')) ?>
      <?php endif ?>
    <?php endif ?>
  </div>
<?php endif ?>