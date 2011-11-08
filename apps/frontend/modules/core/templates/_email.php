<p><strong>Hi!</strong></p>

<?php if (isset($sale) && $sale): ?>
  <p>You have received a new subscription for on-sale items. The email provided was: <?php echo $data['email'] ?></p>
<?php else: ?>
  <p>You have received an appointment request from <?php echo $data['name'] ?> (<?php echo $data['email'] ?> / <?php echo $data['phone'] ?>) with the following message:</p>
<?php endif ?>

<?php if (isset($data['message'])): ?>
 <p>----------------------</p>
 <p><?php echo $data['message'] ?></p>
 <p>----------------------</p>
<?php endif ?>

<?php if (isset($data['product'])): ?>
<p>Product ID: #<?php echo $data['product']['id'] ?></p>
<?php endif ?>