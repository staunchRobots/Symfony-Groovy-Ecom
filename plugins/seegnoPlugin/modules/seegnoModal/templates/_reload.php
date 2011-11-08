<?php echo include_partial('seegnoModal/indicator', array()) ?>

<script type="text/javascript">
<?php if (isset($url)): ?>
  window.setTimeout('jump()', 200);

  function jump() {
    window.location = "<?php echo url_for($sf_data->getRaw('url')) ?>";
  }
<?php else: ?>
  var hash = location.hash;

  if (hash){
    hash = hash.replace('#', '');

    window.setTimeout('location.href = hash', 200);
  } else {
    window.setTimeout('location.reload(true)', 200);
  }
<?php endif ?>
</script>