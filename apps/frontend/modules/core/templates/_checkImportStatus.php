<div id="progress"></div>

<?php if ($poll): ?>
  <?php echo jq_periodically_call_remote(array('url' => url_for('products/checkImportStatus'), 'update' => 'progress', 'frequency' => 1)) ?>
<?php endif ?>