<?php use_helper('carpetbeggers')?>

<div id="product_<?php echo $product->getId() ?>" class="product">
  <div class="image">
    <?php echo $product->getImageTag('photo', 'main', array('alt' => $product->getName()), ESC_RAW) ?>
  </div>

  <div class="product-info">
    <div class="description">
      <p><?php echo $product->getName() ?>, Item <?php echo $product->getId() ?></p>
      <p><?php if (!$product->isFurniture()): ?>
            Size: <?php echo convertInchesToFeet($product->getWidth()) ?> wide, <?php echo convertInchesToFeet($product->getLength()) ?> long
          <?php endif ?>
          $<?php echo $product->getPrice() ?>
      </p>
    </div>

    <div class="buttons right">
      <?php echo jq_link_to_function('More Info', 'showModal("' . url_for(sprintf('@contact?id=%s&type=%s', $product->getId(), 'product')) . '")', array('class' => 'button margin_right_10 right')); ?>

      <?php if ($sf_params->get('category') != 'on-sale'): ?>
        <?php $more = 'More Info' ?>

        <?php echo link_to('Checkout', 'http://carpetbeggers.foxycart.com/cart?cart=checkout', array('class' => 'button right margin_right_10 foxycart')); ?>
        <?php echo link_to('Add to Cart', FoxyCart::getHashQuery(sprintf('name=%s&code=%s&price=%s', $product->getName(), $product->getId(), $product->getPrice())), array('class' => 'button right foxycart')); ?>
      <?php else: ?>
        <?php echo jq_link_to_function('About "On-Sale" Items', 'showModal("' . url_for(sprintf('@contact?id=%s&type=%s', $product->getId(), 'sale')) . '","sale")', array('class' => 'button on-sale margin_right_10 right')); ?>
      <?php endif ?>


      <iframe src="http://www.facebook.com/plugins/like.php?href=http://www.facebook.com/pages/Baltimore-Persian-Rugs-and-Antique-Furniture/147051318670942&amp;layout=button_count&amp;show_faces=true&amp;width=60&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:76px; height:21px;" allowTransparency="true"></iframe>
    </div>
  </div>
</div>