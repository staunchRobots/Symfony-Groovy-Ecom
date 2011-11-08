<?php echo jq_form_remote_tag(array('url' => url_for('products/updateItem?id=' . $form->getObject()->getId()), 'update' => 'item_form', 'complete' => '$("p.flash.notice").delay(2000).fadeOut("slow");')) ?>

  <?php echo use_javascripts_for_form($form) ?>
  <?php echo use_stylesheets_for_form($form) ?>

  <?php echo include_partial('core/flash'); ?>

  <?php echo include_partial('products/item_form', array('form' => $form)); ?>

  <div class="actions">
    <?php echo jq_link_to_function('Delete', 'deleteItem(' . $form->getObject()->getId() . ')', array('class' => 'delete', 'confirm' => 'Are you sure?')) ?>

    <?php echo jq_link_to_function('< Previous', 'prevItem()', array('class' => 'previous')) ?>
    <?php echo jq_link_to_function('Next >', 'nextItem()', array('class' => 'next')) ?>

    <input class="submit" type="submit" value="Save" class="save" />
  </div>
</form>

<script type="text/javascript" charset="utf-8">
function deleteItem(id)
{
  $.getJSON('<?php echo url_for("products/delete") ?>', { id: id }, function(data) {
      window.top.location = data.url;
  });
}
</script>