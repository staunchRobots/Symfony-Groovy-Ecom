<?php use_helper('carpetbeggers') ?>

<form id="<?php echo $type?>_search_form" action="<?php echo url_for('@search') ?>" method="get" accept-charset="utf-8">
  <div class="categories">
    <?php foreach ($categories as $name => $items): ?>
      <div class="<?php echo $name ?>">
        <p>Browse By <?php echo ucfirst($name) ?></p>
        <?php foreach ($items as $category): ?>
          <div class="category<?php echo $category['slug'] == 'on-sale' ? ' on-sale' : '' ?>">
            <?php echo link_to($category['name'], url_for(sprintf('@products?category=%s&pt=%s', $category['slug'], $category['parent'])), array('class' => (in_multiarray($category['slug'], explode(',', $sf_request->getGetParameter('category'))) || ($sf_params->get('category') == $category['slug']) || !$sf_params->has('category')) ? 'selected' : '')) ?>
          </div>
        <?php endforeach ?>
      </div>
    <?php endforeach ?>
  </div>

  <input type="hidden" name="pt" value="<?php echo $type?>">

  <input type="submit" value="Browse all <?php echo $type ?>" class="button">
</form>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  $('#rugs_search_form').submit(function(e){
    e.preventDefault();
    window.location = "<?php echo url_for(sprintf('@homepage_options?type=%s&filter=%s', 'rugs', 'published')) ?>";
  });
  $('#furniture_search_form').submit(function(e){
    e.preventDefault();
    window.location = "<?php echo url_for(sprintf('@homepage_options?type=%s&filter=%s', 'furniture', 'published')) ?>";
  });
});  
</script>