<div id="footer">
  <a id="morrillwebsites">morrill websites</a>
  <?php echo seegno_menu('auth'); ?>
  <div id="search_form">
    <form>
      <input type="hidden" name="product[id]" value="" id="product_id">
      <input type="hidden" name="product[slug]" value="" id="product_slug">
      <input type="text" value="" id="search" autocomplete="off">
      <input type="submit" value="Go" id="go">
    </form>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
$(function()
{
  $('#search_form form').submit(function(e) {
    e.preventDefault();
    window.location = 'http://<?php echo $sf_request->getHost() ?>'+'#/' + $('#product_id').val() + '/' + $('#product_slug').val();
  })
  
  $('#search').awesomecomplete({
    dontMatch: ['slug'],
    dataMethod: function(keyword, search, onData) {
      $.getJSON('<?php echo url_for("@search") ?>', { q: keyword }, function(json){
        onData(json);
      });
    },
    valueFunction: function(dataItem) {
      return dataItem['name'] + ' (#' + dataItem['id'] + ')';
    },
    onComplete: function(dataItem) {
      $('#product_id').val(dataItem['id']);
      $('#product_slug').val(dataItem['slug']);
    }
  });
});
</script>