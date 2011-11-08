<?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>

<div class="row form-row left">
  <?php echo $form['name']->renderLabel() ?>
  <?php echo $form['name']->render() ?>
</div>

<div class="row form-row left">
  <?php echo $form['quality']->renderLabel() ?>
  <?php echo $form['quality']->render() ?>
</div>

<div class="clear"></div>

<div class="row form-row left">
  <?php echo $form['price']->renderLabel() ?>
  <?php echo $form['price']->render() ?>
</div>

<div class="row form-row left">
  <?php echo $form['status']->renderLabel() ?>
  <?php echo $form['status']->render() ?>
</div>

<div class="clear"></div>

<?php if (!$form->getObject()->isFurniture()): ?>
<div class="row form-row left">
  <?php echo $form['length']->renderLabel() ?>
  <?php echo $form['length']->render() ?>
</div>

<div class="row form-row left">
  <?php echo $form['width']->renderLabel() ?>
  <?php echo $form['width']->render() ?>
</div>  
<?php endif ?>

<div class="clear"></div>

<div class="row form-row left">
  <?php echo $form['pile']->renderLabel() ?>
  <?php echo $form['pile']->render() ?>
</div>

<div class="row form-row left">
  <?php echo $form['floor']->renderLabel() ?>
  <?php echo $form['floor']->render() ?>
</div>

<div class="clear"></div>

<div class="row form-row checkbox">
  <?php echo $form['is_published']->render() ?>
  <?php echo $form['is_published']->renderLabel() ?>
</div>

<?php echo include_partial('products/item_tags', array('form' => $form)); ?>

<div class="clear"></div>

<script type="text/javascript" charset="utf-8">
function addTag()
{
  $.ajax({
      url:'<?php echo url_for("products/toggleTag")?>',
      type:'GET',
      dataType:'html',
      data: { product_id: <?php echo $form->getObject()->getId() ?>, category_id: $('#item_categories_options :selected').val() },
      success: function(data, textStatus){
        $('#tags').html(data);
        $('p.flash.notice').delay(2000).fadeOut('slow');
      },
    });
}
</script>