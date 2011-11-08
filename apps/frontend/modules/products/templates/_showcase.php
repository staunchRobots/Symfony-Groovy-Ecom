<?php if ($sf_user->isSuperAdmin()): ?>
  <div id="administration">
    <?php echo seegno_menu('admin') ?>

    <?php echo include_component('products', 'adminTabs', array('product' => $product)); ?>
  </div>
<?php endif ?>

<?php echo include_partial('products/info', array('product' => $product)); ?>