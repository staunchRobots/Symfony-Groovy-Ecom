<div id="sidebar">
  <?php echo include_partial('core/company', array()); ?>

  <?php echo include_partial('products/searchbar', array('categories' => $categories, 'limits' => $limits)); ?>
</div>