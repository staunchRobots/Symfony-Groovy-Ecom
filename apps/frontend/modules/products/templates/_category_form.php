<?php echo use_javascripts_for_form($form) ?>
<?php echo use_stylesheets_for_form($form) ?>

<?php echo $form->renderHiddenFields() ?>

<?php if ($form->hasGlobalErrors()): ?>
  <?php echo $form->renderGlobalErrors() ?>
<?php endif ?>

<div class="row form-row">
  <?php echo $form['parent']->render() ?>
</div>

<h2>Categories</h2>
<div class="row form-row manage_categories">
  <?php echo $form['categories']->render() ?>
</div>

<div class="add_category">
  <div class="row form-row">
    <h2>Add New</h2>
    <?php echo $form['new']->render() ?>
    <?php echo jq_link_to_function('Add', 'addCategory()', array('' => '', )); ?>
  </div>
  
  <div class="clear"></div>
</div>


<script type="text/javascript" charset="utf-8">
function addCategory()
{
  $.ajax({
      url:'<?php echo url_for("@categories?action=new")?>',
      type:'GET',
      dataType:'html',
      data: { parent_id: $('#category_parent :selected').val(), name: $('#category_new').val() },
      success: function(data){
        $('#category_edit_form').html(data);
        $("p.flash.notice").delay(2000).fadeOut("slow");
      },
      });
}

$(document).ready(function() {
  $('#category_parent').bind('change', function() {
    $.ajax({
        url:'<?php echo url_for("@categories?action=list")?>',
        type:'GET',
        dataType:'html',
        data: { parent_id: $('#category_parent :selected').val() },
        success: function(data){
          $('#category_edit_form').html(data);
          $("p.flash.notice").delay(2000).fadeOut("slow");
        },
      });
  });
});

</script>
