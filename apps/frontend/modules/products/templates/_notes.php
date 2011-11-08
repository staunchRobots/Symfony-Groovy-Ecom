<?php echo jq_form_remote_tag(array('url' => url_for('products/updateNotes?id=' . $form->getObject()->getId()), 'update' => 'notes_form',  'complete' => '$("p.flash.notice").delay(2000).fadeOut("slow");')) ?>

  <?php echo include_partial('core/flash'); ?>
  
  <?php echo include_partial('products/notes_form', array('form' => $form)); ?>
  
  <input class="submit" type="submit" value="Save" />
</form>