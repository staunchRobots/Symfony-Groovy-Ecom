<?php echo include_component('products', 'sidebar'); ?>

<div id="showcase">
  <div class="wrapper">
    <?php echo include_partial('products/showcase', array('product' => $products->getFirst())); ?>
  </div>
</div>

<div class="clear"></div>

<div id="carousel">
  <?php echo include_partial('products/gallery', array('products' => $products)); ?>
</div>