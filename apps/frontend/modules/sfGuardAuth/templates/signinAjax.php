<div id="signin">
  <h1><?php echo __('Login') ?></h1>

  <?php echo jq_form_remote_tag(array('url' => '@sf_guard_signin', 'update' => 'signin')) ?>
    
    <?php echo include_partial('sfGuardAuth/form', array('form' => $form)); ?>

    <div class="clear"></div>

    <div class="actions">
      <input type="submit" class="green" value="<?php echo __('Login') ?>" />
    </div>

    <div class="clear"></div>
  </form>
</div>