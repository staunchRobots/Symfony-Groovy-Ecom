<div class="tabs">
  <div id="item_form" class="tab" style="display: none;">
    <?php echo include_partial('products/item', array('form' => $forms['item'])); ?>
  </div>

  <div id="notes_form" class="tab" style="display: none;">
    <?php echo include_partial('products/notes', array('form' => $forms['notes'])); ?>
  </div>

  <div id="category_edit_form" class="tab" style="display: none;">
    <?php echo include_partial('products/category_edit', array('form' => $forms['category'])); ?>
  </div>

  <div id="import_form" class="tab" style="display: none;">
    <?php echo include_partial('products/import', array('form' => $forms['import'])); ?>
  </div>
</div>