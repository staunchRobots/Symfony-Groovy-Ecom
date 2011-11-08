<div id="company">
  <div class="top">
    <?php echo link_to('Baltimore Persian Rug', '@homepage', array('id' => 'logo')) ?>

    <div class="clear"></div>

    <div class="contact">
      <?php echo jq_link_to_function('Set an appointment', 'showModal("' . url_for(sprintf('@contact?type=%s', 'appointment')) . '")', array('class' => 'button margin_right_5 left')); ?>

      <?php echo jq_link_to_function('Learn About Us', 'showModal("' . url_for('@about_us') . '", "about-us")',  array('class' => 'button left')); ?>
      <div class="clear"></div>
      <p>Call Us: 410-329-3181</p>
    </div>
  </div>
</div>

<?php if (($sf_request->getParameter('module') == 'products') && ($sf_request->getParameter('action') == 'index') && !$sf_user->isSuperAdmin()): ?>
  <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
      $('#about-us').click();
    });
  </script>
<?php endif ?>