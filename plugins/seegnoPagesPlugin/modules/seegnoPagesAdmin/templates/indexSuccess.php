<?php use_helper("seegnoPages", "seegnoI18N") ?>

<div id="sf_admin_container">
  <div class="admin-left for-pages">
    <?php echo get_nested_set_manager("Page", "title") ?>
  </div>
  
  <div class="admin-right for-pages">
    <div class="row bluebar">
      
      <?php if(class_exists('seegnoI18N')): ?>
        <?php echo include_partial('language_tabs') ?>
      <?php endif?>    
      
      <div id="loading" style="display: none;"></div>
    </div>
    
    <div class="page">
      <div id="embedForm"></div>
    </div>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  equalHeights('.admin-left', '.admin-right', 350);
  
  $('#admin-pages-menu a').click(function() {equalHeights('.admin-left', '.admin-right', 350);});
});
</script>