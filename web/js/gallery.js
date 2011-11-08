function loadThumbnails(carousel, state)
{
  for (var i = carousel.first; i <= carousel.last; i++) {
       if ((i == 1) && !carousel.has(i))
       {
         $('.jcarousel-item-1').addClass('active');
       }
      
       if (carousel.has(i)) {
           continue;
       }

       if (i > items.length) {
           break;
       }

       carousel.add(i, getHtml(items[i-1]));
   }
};

function getHtml(item)
{ 
   return '<a href="' + item.href + '" id="thumbnail_' + item.id + '" onclick=javascript:switchImage(' + item.id + ')><img src="' + item.src + '" alt="' + item.name + '" /></a>';
};

function switchImage(id)
{
  var thumb = $('a#thumbnail_' + id);
  var parent = thumb.parent();
  var alt = thumb.find('img').attr('alt');

  $.manageAjax.add('ajaxManager', {
     type: "GET",
     url : '/products/showcase',
     dataType : 'html',
     data : { id: id },
     success: function (data) {
       if (!parent.hasClass('active'))
       {
        parent.addClass('active');
       }

       $('#gallery a').not('a#thumbnail_' + id).parent().removeClass('active');

       $('#showcase').hide().html(data).show();

       checkCookie();

       $(document).attr('title', 'Baltimore Persian Rug ∙ ' + alt);
       $(document).attr('meta[name="title"]', 'Baltimore Persian Rug ∙ ' + alt);

       $('a.foxycart').unbind('click');

       fc_tb_init('a.foxycart');
     }
   });
}

function checkCookie() {
  var cookie = $.cookie('admin_menu');
  
  if (cookie) 
  {
    if (cookie.match("none$") != "none") {
      $('#' + $.cookie('admin_menu')).addClass('active');
      $('#' + $.cookie('admin_menu')+'_form').show();      
    }
    else {
      $('#' + cookie.replace('_none', '')).addClass('active');
    }
  };
};

function bindKeyboard() {
$('body').keydown(function(e) {
  if (e.keyCode == 37) {
    prevItem();
  } else if (e.keyCode == 39) {
    nextItem();
  }
    $('.jcarousel-prev, .jcarousel-next').qtip("disable");
 });
}

function unbindKeyboard() {
  $('body').unbind();
};

function positionTips()
{
  var shared = {
     position: {
        my: 'bottom center', 
        at: 'center',
     },
     style: {
        tip: true,
        classes: 'ui-tooltip-youtube'
     }
  };
  
  $('.jcarousel-prev').qtip( $.extend({}, shared, { 
     content: 'Use the Left arrow keyboard key to move items left',
  }));

  $('.jcarousel-next').qtip( $.extend({}, shared, { 
     content: 'Use the Right arrow keyboard key to move items right',
  }));
}

function nextItem()
{
  var el = $('#gallery').find('.active').next().children();
  var id = el.attr('id');
  
  if (id == null) {
    return false;
  }
  
  id = id.split('_')[1];  

  window.location.hash = el.attr('href');
}

function prevItem()
{
  var el = $('#gallery').find('.active').prev().children();
  var id = el.attr('id');
  
  if (id == null) {
    return false;
  }

  id = id.split('_')[1];
  
  window.location.hash = el.attr('href');
}

function switchAndScrollToImage(id)
{
  id = id.split('_')[1];
  
  $thumb = $('a#thumbnail_' + id).parent();
  
  var gallery = $('#gallery').data('jcarousel')
  
  switchImage(id);
  
  var first = gallery.first;
  var prevFirst = gallery.prevFirst;
  var last = gallery.last;
  var index = $thumb.attr('jcarouselindex');

  if (index > (((last-first)/2)+prevFirst))
  {
    gallery.scroll(parseInt(first+1), true);
  }
  else
  {
    gallery.scroll(parseInt(first-1), true);
  }
}

$(document).ready(function() {
  var id = null;
  
    $('#gallery').jcarousel({
        size: items.length,
        scroll: 6,
        itemLoadCallback: { onBeforeAnimation: loadThumbnails },
    });

    positionTips();

    $('#administration').draggable({ cursor: 'move', snap: '#showcase' });

    $(window).bind('load.jcarousel', function() {
      bindKeyboard();
    
      $(window).bind('hashchange', function(e) {
        var url = e.fragment;        
        
        if (url == '')
        {
          return false;
        }
        var matches = url.match(/\d+/);
        
        $(items).each(function(i){
          if (this.id == matches[0])
          {
            id = i-2;
          }
        });
        
        $('#gallery').data('jcarousel').scroll(id);

        var el = $( 'a[href="#' + url + '"]' );
    
        url && el.addClass( 'bbq-current' );
          switchImage(el.attr('id').split('_')[1]);
      });
    
      $(window).trigger('hashchange');
    });
});