if (typeof CKEDITOR !== "undefined")
{
  CKEDITOR.config.format_tags = 'p;h1;h2;h3;h4;h5;h6;div';	
}


(function($) {
    $.fn.editable = function(target, options) {
        if ('disable' == target) {
            $(this).data('disabled.editable', true);
            return;
        }
        if ('enable' == target) {
            $(this).data('disabled.editable', false);
            return;
        }
        if ('destroy' == target) {
            $(this).unbind($(this).data('event.editable')).removeData('disabled.editable').removeData('event.editable');
            return;
        }
        var settings = $.extend({},
        $.fn.editable.defaults, {
            target: target
        },
        options);
        var cufon = false;
        var plugin = $.editable.types[settings.type].plugin ||
        function() {};
        var submit = $.editable.types[settings.type].submit ||
        function() {};
        var buttons = $.editable.types[settings.type].buttons || $.editable.types['defaults'].buttons;
        var content = $.editable.types[settings.type].content || $.editable.types['defaults'].content;
        var element = $.editable.types[settings.type].element || $.editable.types['defaults'].element;
        var reset = $.editable.types[settings.type].reset || $.editable.types['defaults'].reset;
        var callback = settings.callback ||
        function() {};
        var onedit = settings.onedit ||
        function() {};
        var onsubmit = settings.onsubmit ||
        function() {};
        var onreset = settings.onreset ||
        function() {};
        var onerror = settings.onerror || reset;
        if (settings.tooltip) {
            $(this).attr('title', settings.tooltip);
        }
        settings.autowidth = 'auto' == settings.width;
        settings.autoheight = 'auto' == settings.height;
        return this.each(function() {
            var self = this;
            var savedwidth = $(self).width();
            var savedheight = $(self).height();
            $(this).data('event.editable', settings.event);
            if (!$.trim($(this).html())) {
                $(this).html(settings.placeholder);
            }
            $(this).bind(settings.event,
            function(e) {
                if (true === $(this).data('disabled.editable')) {
                    return;
                }
                if (self.editing) {
                    return;
                }
                if (false === onedit.apply(this, [settings, self])) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                
                // Cufon
                var cufonTag = $(self).find('cufon');

                if (cufonTag.length != 0)
                {
                  cufon = true;

                  $(self).html($(self).html().replace(/<cufon.*?<cufontext>(.*?)<\/cufon>/g, '$1'));
                }

                // Remove "click to edit bla" text
                $(self).html($(self).html().replace(/\[click to edit .+\]/i, ''));

                if (settings.tooltip) {
                    $(self).removeAttr('title');
                }
                if ((0 == $(self).width()) || (0 == $(self).height())) {
                    settings.width = savedwidth;
                    settings.height = savedheight;
                } else {
                    if (settings.width != 'none') {
                        settings.width = settings.autowidth ? $(self).width() : settings.width;
                    }
                    if (settings.height != 'none') {
                        settings.height = settings.autoheight ? $(self).height() : settings.height;
                    }
                }
                if ($(this).html().toLowerCase().replace(/(;|")/g, '') == settings.placeholder.toLowerCase().replace(/(;|")/g, '')) {
                    $(this).html('');
                }
                self.editing = true;
                self.revert = $(self).html();
                $(self).html('');
                var form = $('<form />');
                if (settings.cssclass) {
                    if ('inherit' == settings.cssclass) {
                        form.attr('class', $(self).attr('class'));
                    } else {
                        form.attr('class', settings.cssclass);
                    }
                }
                if (settings.style) {
                    if ('inherit' == settings.style) {
                        form.attr('style', $(self).attr('style'));
                        form.css('display', $(self).css('display'));
                    } else {
                        form.attr('style', settings.style);
                    }
                }
                var input = element.apply(form, [settings, self]);
                var input_content;
                if (settings.loadurl) {
                    var t = setTimeout(function() {
                        input.disabled = true;
                        content.apply(form, [settings.loadtext, settings, self]);
                    },
                    100);
                    var loaddata = {};
                    loaddata[settings.id] = self.id;
                    if ($.isFunction(settings.loaddata)) {
                        $.extend(loaddata, settings.loaddata.apply(self, [self.revert, settings]));
                    } else {
                        $.extend(loaddata, settings.loaddata);
                    }
                    $.ajax({
                        type: settings.loadtype,
                        url: settings.loadurl,
                        data: loaddata,
                        async: false,
                        success: function(result) {
                            window.clearTimeout(t);
                            input_content = result;
                            input.disabled = false;
                        }
                    });
                } else if (settings.data) {
                    input_content = settings.data;
                    if ($.isFunction(settings.data)) {
                        input_content = settings.data.apply(self, [self.revert, settings]);
                    }
                } else {
                    input_content = self.revert;
                }
                content.apply(form, [input_content, settings, self]);
                input.attr('name', $(this).attr('id'));
                buttons.apply(form, [settings, self]);

                if (input.hasClass('modal')) {
                  var wrapper = $('<div class="wrapper"></div>').append(form);
                  var blocker = $('<div class="blocker" />');
                  var modal = $('<div id="modal" class="ckeditor" />').append(blocker).append(wrapper);

                  $('body').prepend(modal);
                  $('html, body').animate({scrollTop:0}, 'slow');
                } else {
                  $(self).append(form);
                }
                if ((typeof settings.config == "string") && (settings.config != "")) {
                  url = settings.config.replace(/%5C/gi, '/');
                  url = settings.config.replace(/%2F/gi, '/');
                  
                  $('textarea.richtext', form).ckeditor(function() {}, { customConfig : url });
                
                  if ($('textarea.richtext.ckfinder', form))
                  {
                      var editor = $($('textarea.richtext', form)).ckeditorGet();
                      CKFinder.SetupCKEditor(editor, "/seegnoPlugin/ckfinder");
                  }
                } else {
                  if ($('textarea.richtext', form).length > 0)
                  {
                    if ($('textarea.richtext.ckfinder', form).length > 0)
                    {
                      $('textarea.richtext', form).ckeditor(function() {}, { toolbar: [["Bold", "Italic", "-", "NumberedList", "BulletedList", "-", "Link", "Unlink", "Image", "Format"], ["UIColor"]] });
                      
                      var editor = $($('textarea.richtext', form)).ckeditorGet();
                      CKFinder.SetupCKEditor(editor, "/seegnoPlugin/ckfinder");
                    } else {
                      $(form).find('textarea.richtext').ckeditor(settings.default_config);
                    }
                  }                
                }
                plugin.apply(form, [settings, self]);
                $(':input:visible:enabled:first', form).focus();
                if (settings.select) {
                    input.select();
                }
                input.keydown(function(e) {
                   if (($(input).attr('type') == 'text') && (e.keyCode == 13)) {
                        e.preventDefault();
                        form.submit();
                    }
                    if (e.keyCode == 27) {
                        e.preventDefault();
                        reset.apply(form, [settings, self]);
                    }
                });
                var t;
                if ('cancel' == settings.onblur) {
                    input.blur(function(e) {
                        t = setTimeout(function() {
                            reset.apply(form, [settings, self]);
                        },
                        500);
                    });
                } else if ('submit' == settings.onblur) {
                    input.blur(function(e) {
                        t = setTimeout(function() {
                            form.submit();
                        },
                        200);
                    });
                } else if ($.isFunction(settings.onblur)) {
                    input.blur(function(e) {
                        settings.onblur.apply(self, [input.val(), settings]);
                    });
                } else {
                    input.blur(function(e) {});
                }
                form.submit(function(e) {
                    if (t) {
                        clearTimeout(t);
                    }
                    e.preventDefault();
                    if (false !== onsubmit.apply(form, [settings, self])) {
                        if (false !== submit.apply(form, [settings, self])) {
                            if ($.isFunction(settings.target)) {
                                var str = settings.target.apply(self, [input.val(), settings]);
                                $(self).html(str);
                                self.editing = false;
                                callback.apply(self, [self.innerHTML, settings]);
                                if (!$.trim($(self).html())) {
                                    $(self).html(settings.placeholder);
                                }
                            } else {
                                var submitdata = {};
                                submitdata[settings.id] = self.id;
                                submitdata['value'] = input.val();

                                var o = {};
                                
                                if (input && input.val()) {
                                  $.each($('#' + input.val()).find('input'), function() {
                                    if (o[this.name]) {
                                        if (!o[this.name].push) {
                                            o[this.name] = [o[this.name]];
                                        }
                                        o[this.name].push(this.value || '');
                                    } else {
                                        o[this.name] = this.value || '';
                                    }
                                  });
                                };

                                submitdata['params'] = o;
                                if ($.isFunction(settings.submitdata)) {
                                    $.extend(submitdata, settings.submitdata.apply(self, [self.revert, settings]));
                                } else {
                                    $.extend(submitdata, settings.submitdata);
                                }

                                if ('PUT' == settings.method) {
                                    submitdata['_method'] = 'put';
                                }

                                $(self).html(settings.indicator);
                                var ajaxoptions = {
                                    type: 'POST',
                                    data: submitdata,
                                    dataType: 'html',
                                    url: settings.target,
                                    success: function(result, status) {
                                        if ($(form).find('textarea.richtext').attr('name')) {
                                          CKEDITOR.instances[$(form).find('textarea.richtext').attr('name')].destroy();
                                        };
                                        
                                        if ($(form).find('textarea.modal').attr('name')) {
                                          $('#modal').remove();
                                        };

                                        if (ajaxoptions.dataType == 'html') {
                                            $(self).html(result);
                                        }
                                        self.editing = false;
                                        callback.apply(self, [result, settings]);
                                        if (!$.trim($(self).html())) {
                                            $(self).html(settings.placeholder);
                                        }

                                        if (cufon) { Cufon.refresh(); }
                                    },
                                    error: function(xhr, status, error) {
                                        onerror.apply(form, [settings, self, xhr]);
                                        if (cufon) { Cufon.refresh(); }
                                    }
                                };
                                $.extend(ajaxoptions, settings.ajaxoptions);
                                $.ajax(ajaxoptions);
                            }
                        }
                    }
                    $(self).attr('title', settings.tooltip);
                    return false;
                });
            });
            this.reset = function(form) {
                if ($(form).find('textarea.richtext').attr('name')) {
                  CKEDITOR.instances[$(form).find('textarea.richtext').attr('name')].destroy();
                };

                if ($(form).find('textarea.modal').attr('name')) {
                  $('#modal').remove();
                };

                if (this.editing) {
                    if (false !== onreset.apply(form, [settings, self])) {
                        $(self).html(self.revert);
                        self.editing = false;
                        if (!$.trim($(self).html())) {
                            $(self).html(settings.placeholder);
                        }
                        if (settings.tooltip) {
                            $(self).attr('title', settings.tooltip);
                        }
                    }
                }
                
                if (cufon) { Cufon.refresh(); }
            };
        });
    };
    $.editable = {
        types: {
            defaults: {
                element: function(settings, original) {
                    var input = $('<input type="hidden"></input>');
                    $(this).append(input);
                    return (input);
                },
                content: function(string, settings, original) {
                    $(':input:first', this).val(string);
                },
                reset: function(settings, original) {
                    original.reset(this);
                },
                buttons: function(settings, original) {
                    var form = this;
                    if (settings.submit) {
                        if (settings.submit.match(/>$/)) {
                            var submit = $(settings.submit).click(function() {
                                if (submit.attr("type") != "submit") {
                                    form.submit();
                                }
                            });
                        } else {
                            var submit = $('<button type="submit" />');
                            submit.html(settings.submit);
                        }
                        $(this).append(submit);
                    }
                    if (settings.cancel) {
                        if (settings.cancel.match(/>$/)) {
                            var cancel = $(settings.cancel);
                        } else {
                            var cancel = $('<button type="cancel" />');
                            cancel.html(settings.cancel);
                        }
                        $(this).append(cancel);
                        $(cancel).click(function(event) {
                            if ($.isFunction($.editable.types[settings.type].reset)) {
                                var reset = $.editable.types[settings.type].reset;
                            } else {
                                var reset = $.editable.types['defaults'].reset;
                            }
                            reset.apply(form, [settings, original]);
                            return false;
                        });
                    }
                }
            },
            spinner: {
                element: function(settings, original) {
                    var input = $('<input />');
                    if (settings.width != 'none') {
                        input.width(settings.width);
                    }
                    if (settings.height != 'none') {
                        input.height(settings.height);
                    }
                    input.attr('autocomplete', 'off');
                    input.attr('type', 'text');
                    $(this).append(input);
                    input.attr('readonly', true);
                    input.spin({min: 0, imageBasePath: '/seegnoPlugin/css/images/spinner/', lock: true});  
                    return (input);
                }
            },
            text: {
                element: function(settings, original) {
                    var input = $('<input />');
                    if (settings.width != 'none') {
                        input.width(settings.width);
                    }
                    if (settings.height != 'none') {
                        input.height(settings.height);
                    }
                    input.attr('autocomplete', 'off');
                    $(this).append(input);
                    return (input);
                }
            },
            richtext: {
                element: function(settings, original) {
                    if ($(original).hasClass('modal')) {
                      var textarea = $('<textarea class="richtext modal" />');
                    } else {
                      var textarea = $('<textarea class="richtext" />');
                    }

                    if (settings.rows) {
                        textarea.attr('rows', settings.rows);
                    } else if (settings.height != "none") {
                        textarea.height(settings.height);
                    }
                    if (settings.cols) {
                        textarea.attr('cols', settings.cols);
                    } else if (settings.width != "none") {
                        textarea.width(settings.width);
                    }

                    $(this).append(textarea);

                    return textarea;
                }
            },
            textarea: {
                element: function(settings, original) {
                    var textarea = $('<textarea />');
                    if (settings.rows) {
                        textarea.attr('rows', settings.rows);
                    } else if (settings.height != "none") {
                        textarea.height(settings.height);
                    }
                    if (settings.cols) {
                        textarea.attr('cols', settings.cols);
                    } else if (settings.width != "none") {
                        textarea.width(settings.width);
                    }

                    $(this).append(textarea);

                    return textarea;
                }
            },
            select: {
                element: function(settings, original) {
                    var select = $('<select />');
                    $(this).append(select);
                    $form = $(this);
                    return (select);
                  },
                  content: function(data, settings, original) {
                      if (String == data.constructor) {
                        eval('var json = ' + data);
                    } else {
                        var json = data;
                    }
                    for (var key in json) {
                        if (!json.hasOwnProperty(key)) {
                            continue;
                        }
                        if ('selected' == key) {
                            continue;
                        }
                        var option = $('<option />').val(key).append(json[key]['name']);
                        $('select', this).append(option);

                        $.each(json[key], function(i, item) {
                          
                          if (typeof item === 'string')
                          {
                            $form.append('<div id="' + item + '"></div>');
                          }

                          if (typeof item === 'object')
                          {
                            var paramId = i;
                            var divId = key;
                            
                            $.each(item, function(key, value) {
                              if (key == 'name')
                              {
                                $form.find('"#' + divId + '"').append('<label for="' + paramId  + '">' + value + '</label>');
                              }
                              if (key == 'type')
                              {
                                if (value == 'integer')
                                {
                                  $form.find('"#' + divId + '"').append('<input name="' + paramId + '" type="text" value="" id="' + paramId + '" />');
                                }
                              }
                            });
                          }
                        });
                      }
                      
                    $form.find('label').hide();
                    $form.find('input[type=text]').hide();
                        
                    $('select', this).children().each(function() {
                        if ($(this).val() == json['selected'] || $(this).text() == $.trim(original.revert)) {
                            $(this).attr('selected', 'selected');
                            $form.find('"#' + $(this).val() + '"').children().show();
                            $form.find('div').not('"#' + $(this).val() + '"').children().hide();
                        }
                    });
                    
                    $form.delegate('select', 'click change', function() { 
                       $form.find('"#' + $(this).val() + '"').children().show();
                       $form.find('div').not('"#' + $(this).val() + '"').children().hide();
                    });
                }
            }
        },
        addInputType: function(name, input) {
            $.editable.types[name] = input;
        }
    };
    $.fn.editable.defaults = {
        id: 'id',
        type: 'text',
        width: 'auto',
        height: 'auto',
        event: 'click.editable',
        onblur: '',
        loadtype: 'GET',
        loadtext: 'Loading...',
        placeholder: '[click to edit]',
        loaddata: {},
        submitdata: {},
        submit : 'Save',
        cancel : 'Cancel',
        ajaxoptions: {},
        default_config: {
          toolbar:
          [
            ["Bold", "Italic", "-", "NumberedList", "BulletedList", "-", "HorizontalRule", "-", "Link", "Unlink", "Format"],
            ["UIColor"]
          ]
        }
    };
})(jQuery);