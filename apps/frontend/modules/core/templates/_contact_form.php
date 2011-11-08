<?php $url = ($sf_params->has('id')) ? sprintf('@contact?id=%s&type=%s', $sf_params->get('id'), $sf_params->get('type')) : sprintf('@contact?type=%s', $sf_params->get('type')) ?>

<?php echo jq_form_remote_tag(array('url' => url_for($url), 'update' => 'contact')) ?>
<div id="contact">
  <div class="form">
    <?php echo $form ?>
  </div>

  <div class="sidebar">
    <?php echo $sf_data->getRaw('sidebar') ?>
  </div>

  <input class="submit" type="submit" value="<?php echo ($sf_params->has('id')) ? 'Send #' . $sf_params->get('id') : 'Send' ; ?>" />  
</div>

</form>