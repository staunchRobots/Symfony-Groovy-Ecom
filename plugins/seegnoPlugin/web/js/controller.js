$(function() {
  $('a[data-seegno]').click(function(e) {
    e.preventDefault();
    var data = $(this).data('seegno');

    eval(data.method + '("' + data.params.join('","') + '")');
  });
});
