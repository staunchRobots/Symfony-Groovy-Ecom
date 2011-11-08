<div class="added_categories">
  <?php foreach ($product->getCategories() as $category): ?>
    <div class="added_category">
      <p><?php echo $category->getName() ?></p>
      <?php echo jq_link_to_function('Remove Category', 'removeTag(' .  $category->getId() .')', array('class' => 'remove_icon')); ?>
    </div>
  <?php endforeach ?>
</div>

<script type="text/javascript" charset="utf-8">
function removeTag(id)
{
  $.ajax({
      url:'<?php echo url_for("products/toggleTag")?>',
      type:'GET',
      dataType:'html',
      data: { product_id: <?php echo $product->getId() ?>, category_id: id },
      success: function(data, textStatus){
        $('#tags').html(data);
        $('p.flash.notice').delay(2000).fadeOut('slow');
      },
      });
}
</script>
