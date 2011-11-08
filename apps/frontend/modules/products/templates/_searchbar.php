<div id="searchbar">
  <div class="tools">
    <div id="tabs">

    <?php $rugs = 'left' ?>
    <?php $furniture = 'right' ?>

    <?php 
    if ($sf_params->get('pt') == "furniture" || $sf_params->get('type') == 'furniture')
    {
      $furniture .= ' active';
      $furnitureTab = true;
     } 
     else 
     {
      $rugs .= ' active';
      $rugsTab = true;
     } ?>

      <?php echo jq_link_to_function('Persian Rugs', 'toggleTab("rugs")', array('id' => 'rugs', 'class' => $rugs)) ?>
      <?php echo jq_link_to_function('Furniture', 'toggleTab("furniture")', array('id' => 'furniture', 'class' => $furniture)) ?>
      <div class="clear"></div>
    </div>

    <div class="wrapper">
      <div id="rugs_tab" class="tab">
        <div class="wrapper" style="<?php echo isset($rugsTab) ? '': 'display: none;' ?>">
        <?php echo include_partial('products/search_form', array('type' => 'rugs',
                                                                 'categories' => $categories['rugs'], 
                                                                 'limits' => $limits['rugs'])); ?>
        </div>
      </div>
      <div id="furniture_tab" class="tab">
        <div class="wrapper" style="<?php echo isset($furnitureTab) ? '': 'display: none;' ?>">
          <?php echo include_partial('products/search_form', array('type' => 'furniture',
                                                                   'categories' => $categories['furniture'], 
                                                                   'limits' => $limits['furniture'])); ?>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
function toggleTab(active)
{
  if ($('#tabs a#' + active).hasClass('active')) { return false }; 
  
  $('#tabs a').removeClass('active');
  $('#tabs a#' + active).addClass('active');

  $('.tab .wrapper').toggle();
}
</script>