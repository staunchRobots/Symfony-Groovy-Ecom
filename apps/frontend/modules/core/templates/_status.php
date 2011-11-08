<?php echo include_component('core', 'checkImportStatus'); ?>

<?php if (isset($url)): ?>
<script type="text/javascript" charset="utf-8">
  window.top.location = <?php echo url_for($url) ?>
</script>
<?php endif ?>