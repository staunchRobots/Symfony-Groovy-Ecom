<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@seegnoPagesAdmin', array('id' => 'page_form')) ?>
    <?php echo $form->renderHiddenFields() ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif ?>
        
    <div class="general container">
      <div class="title"><?php echo __(' Page Data') ?></div>
      <div class="body">
        <?php include_partial('seegnoPagesAdmin/path', array('form' => $form)) ?>
        
        <div class="row form-row">
          <label><?php echo __('Template') ?></label>
          <?php echo $form['template'] ?>
        </div>
        
        <div class="row form-row">
          <label><?php echo __('Menu') ?></label>
          <?php echo $form['menu'] ?>
        </div>
        
        <div class="row form-row">
          <label><?php echo __('Published?') ?></label>
          <?php echo $form['is_published'] ?>
        </div>
      </div>
    </div>
    
    <div id="cultures">
      <?php foreach ($sf_user->getLanguages() as $culture => $params): ?>
        <div class="culture-<?php echo $culture ?> container" style="display: none">
          <div class="title"><?php echo $culture . __(' version') ?></div>
          <div class="body"><?php echo $form[$culture] ?></div>
        </div>
      <?php endforeach ?>
    </div>
    
  <div class="row form-row actions ajax">
      <?php include_partial('seegnoPagesAdmin/form_actions', array('page' => $page, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
    </form>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  $('#admin-pages-menu li a').click(function() {
    var page_menu_option_name = $(this).attr('id');
    
    if (page_menu_option_name == 'general')
    {
      $('.page-tab').removeClass('active');
      $('#pages-general').addClass('active');
      $('#cultures').children().hide();
      $('.general').show();
    } 
    else {
      $('.page-tab').removeClass('active');
      $('#pages-' + page_menu_option_name).addClass('active');
      $('.general').hide();
      $('#cultures').children().hide();
      $('#cultures .' + page_menu_option_name).show();      
    }
  });
  
  
  $(document).ajaxStart(function() {
    $("#loading").show();
  });
  $(document).ajaxComplete(function() {
    $("#loading").hide();
  });
});
</script>