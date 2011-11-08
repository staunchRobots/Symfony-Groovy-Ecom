<div id="tags">
  <div class="categories">
    <div class="top"></div>
    <div class="middle">
      <h2>Categories</h2>
        <?php echo include_partial('products/addedCategories', array('product' => $form->getObject())); ?>
      <div class="clear"></div>

      <div class="add_category">
        <h2>Add Category</h2>
        <?php echo $form['categories_options']->render() ?>

        <?php echo jq_link_to_function('Add', 'addTag()'); ?>
      </div>
    </div>
    <div class="bottom"></div>
  </div>
</div>