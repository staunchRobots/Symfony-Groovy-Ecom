<?php echo jq_form_remote_tag(array('url' => url_for(sprintf('@contact?id=%s&type=%s', $sf_params->get('id'), $sf_params->get('type'))), 'update' => 'contact')) ?>
  <div id="sale">
    <h1>About "On Sale" items</h1>

    <div class="message">
      <p>Once a month we offer 1-3 rugs at SUPER DEEP discount and they sell quick! Sign up to get email updates so you can be the first to see and buy our deals!</p>
  
      <p>"ON SALE" Items are available for pickup only, please call to set up an appointment - 410-329-3181</p>
    </div>

    <?php echo $form->renderHiddenFields() ?>
    <?php echo $form->renderGlobalErrors() ?>

    <div class="row form-row">
      <?php echo $form['email']->render() ?>
    </div>
  
    <input class="submit" class="button" type="submit" value="Subscribe" />  
    <div class="clear"></div>
  </div>
</form>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  $('#on_sale_email').watermark('enter email here', {top: 2, color: '#0071A1'});
});

</script>