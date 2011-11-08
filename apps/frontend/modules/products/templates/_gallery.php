<div id="gallery" class="jcarousel-skin-gallery">
  <ul>
  </ul>
</div>

<script type="text/javascript" charset="utf-8">
var items = [
  <?php foreach ($products as $product): ?>
    <?php echo '{id: "' . $product->getId() . '",
                 name: "' . $product->getName() . '",
                 href: "#/' . $product->getId() . '/'. $product->getSlug() . '",
                 src: "' . $product->getImageSrc('photo', 'thumb') . '",
                 alt: "' . $product->getName() . '",
                 },'; ?>
  <?php endforeach ?>
];
</script>