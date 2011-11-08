$(document).keydown(function(e) {
  if (e.which == 27) {
    hideModal();

    return false;
  }
});

function showModal(url, name, tag) {
  $('#modal').remove();

  if (!($('#modal').length)) {
    var selector = $('body');

    if (tag)  {
      selector = $(tag);
    }

    selector.prepend('<?php echo str_replace("\n", "", get_partial("seegnoModal/modal")) ?>');
  }

  $('#modal').removeClass();

  $.get(url, function(data) {
    if (name !== "undefined") {
      $('#modal').addClass(name);
    }

    $('#modal').fadeIn();
    $('#modal #ajax').html(data).parent().show();
    $('#modal #ajax :input:visible:enabled:first').focus();
  });

  if ($.scrollTo) {
    $.scrollTo($('body'), 800);
  }

  return false;
}

function showMessage(data, name) {
  if (!($('#modal').length)) {
    $('body').prepend('<?php echo str_replace("\n", "", get_partial("seegnoModal/modal")) ?>');
  }

  if (name !== "undefined") {
    $('#modal').addClass(name);
  }

  $('#modal').show();
  $('#modal #ajax').html(data).parent().show();
  $('#modal #ajax :input:visible:enabled:first').focus();

  if ($.scrollTo) {
    $.scrollTo($('body'), 800);
  }
}

function hideModal() {
  if (!$('#modal').hasClass('ckeditor')) {
    $('#modal').fadeOut('slow');

    return false;
  }
}

function refreshPage() {
  window.setTimeout('location.reload(true)', 200);
}