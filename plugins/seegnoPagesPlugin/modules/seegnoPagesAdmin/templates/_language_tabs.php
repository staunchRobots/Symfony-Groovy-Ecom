<ul id="admin-pages-menu">
  <li id="pages-general" class="page-tab"><a id="general"><span><?php echo __("General") ?></span></a></li>
  <?php foreach(seegnoI18N::getLabels() as $label => $name): ?>
    <li id="pages-culture-<?php echo $label ?>" class="page-tab"><a id="culture-<?php echo $label ?>"><span><?php echo $name ?></span></a></li>
  <?php endforeach ?>
</ul>