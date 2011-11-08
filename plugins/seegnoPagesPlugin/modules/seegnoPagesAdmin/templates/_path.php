<?php use_javascript('/seegnoPagesPlugin/js/typewatch.js'); ?>

<div class="sf_admin_form_row sf_admin_text sf_admin_form_field_slug<?php $form['slug']->hasError() and print ' errors' ?>">
  <?php echo $form['slug']->renderError() ?>
  <div>
    <?php echo $form['slug']->renderLabel() ?>

    <div class="content"><?php echo $form['slug']->render() ?></div>
  </div>
</div>

<div class="url">
  <div class="host">
    <p>http://<?php echo sfContext::getInstance()->getRequest()->getHost() ?>/<span id="page_url"><?php echo $form->getObject()->getNode()->getParent() && $form->getObject()->getNode()->getParent()->getSlug() && ($form->getObject()->getNode()->getParent()->getSlug() != $form->getObject()->getSlug()) ? $form->getObject()->getNode()->getParent()->getSlug() . '/' . $form->getObject()->getSlug() : $form->getObject()->getSlug() ?></span></p>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  $("#page_slug").typeWatch({ highlight: true, wait: 500, captureLength: -1, callback: checkUrl });
});

function checkUrl() {
  $slugInput = $('input#page_slug');
  $urlInput = $('span#page_url');

  if ($slugInput.val() != "") {
    $.getJSON('<?php echo url_for("@urlify") ?>', { key: $slugInput.val()<?php echo ($sf_params->has("id") ? ", id: '" . $sf_params->get("id") . "'" : "") ?> },
     function(data) {
       $urlInput.html(data.slug);
       
       if (!data.success) {
         $urlInput.addClass('error');
       } else if ($urlInput.hasClass('error')) {
         $urlInput.removeClass('error');
       }
    });
  } else {
    $urlInput.val('<?php echo __("Invalid Path") ?>');
  }
}
</script>