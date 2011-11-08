if (typeof(FoxyDomain) == 'undefined') {
var FoxyDomain = "carpetbeggers.foxycart.com";
}

function showTab(elem) {
  $('#' + elem + '_form').toggle();
  $('.tabs .tab').not('#' + elem + '_form').hide();
}

$(document).ready(function() {
  var ajaxManager = $.manageAjax.create('ajaxManager', {
      queue: 'clear',
      cacheResponse: true,
      abortOld: true,
      maxRequests: 1
  });
  
  
  $('#item, #notes, #category_edit, #import').live('click', function() {
    var id = $(this).attr('id');

    showTab(id);

    $(this).addClass('active');
    $(this).siblings().removeClass('active');
    
    if (!$('.tabs .tab').is(':visible'))
    {
      $.cookie('admin_menu', id + '_none');
    }
    else
    {
      $.cookie('admin_menu', id, { path: '/', expires: 10 });
    }  
  });
  
  checkCookie();

  if ($('#sidebar').height() > $('#showcase').height()) {
    $('#showcase').height($('#sidebar').height());
  };
  
  var cookie = $.cookie('admin_menu');

  if (cookie != 'none')
  {
    $('#' + cookie).addClass('active');
    $('#' + cookie + '_form').toggle();
  }
  else {
    $('#item').addClass('active');
  }
});
