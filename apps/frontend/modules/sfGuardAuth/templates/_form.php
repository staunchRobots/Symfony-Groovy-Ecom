<?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>

<div class="lite box">
  <div class="top"></div>
  <div class="wrapper">
    <div class="left form-row<?php if ($form['username']->hasError() or $form->hasGlobalErrors()) { echo ' error'; } ?>">
      <?php echo $form['username']->renderLabel() ?>
      <?php echo $form['username']->render(array('class' => 'username')) ?>
    </div>

    <div class="left form-row<?php if ($form['password']->hasError()) { echo ' error'; } ?>">
      <?php echo $form['password']->renderLabel() ?>
      <?php echo $form['password']->render(array('class' => 'password')) ?>
    </div>
  </div>

  <div class="bottom"></div>
</div>

<div class="clear"></div>

<div class="form-row checkbox">
  <?php echo $form['remember']->render() ?>
  <?php echo $form['remember']->renderLabel() ?>
</div>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
if (typeof Cufon != 'undefined')
{
  Cufon.replace('#modal h1', { fontFamily: '007', textShadow: '1px 1px #fff'});
  Cufon.now();
  
  $('#modal .username').watermark('Username');
  $('#modal .password').watermark('Password');  
}
});
</script>