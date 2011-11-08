<?php

require_once dirname(__FILE__).'/../../plugins/csDoctrineActAsSortablePlugin/lib/template/Sortable.php';

class seegnoWidgetSortable extends sfWidgetForm
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('choices');
    $this->addRequiredOption('promote_url');
    $this->addRequiredOption('demote_url');
    $this->addRequiredOption('remove_url');
    $this->addRequiredOption('model');

    $this->addOption('save_url');
    $this->addOption('class', 'sortable');
    $this->addOption('label_promote', 'Promote');
    $this->addOption('label_demote', 'Demote');
    $this->addOption('label_remove', 'Remove');
    $this->addOption('update', 'sortable');
    $this->addOption('promote', '<img src="/csDoctrineActAsSortablePlugin/images/sortable/icons/promote.png" alt="promote" />');
    $this->addOption('demote', '<img src="/csDoctrineActAsSortablePlugin/images/sortable/icons/demote.png" alt="demote" />');
    $this->addOption('remove', '<img src="/csDoctrineActAsSortablePlugin/images/sortable/icons/remove.png" alt="remove" />');
    $this->addOption('template', <<<EOF
<div>
  <div class="sortable_label">%label_promote%</div>
  <div>%promote%</div>
  <div class="sortable_label">%label_demote%</div>
  <div>%demote%</div>
  <div class="sortable_label">%label_remove%</div>
  <div>%remove%</div>
  <div>
    %choice%
  </div>
</div>
EOF
);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $choices = $this->getOption('choices');
    
    foreach ($choices as $choice) 
    {
      $widget = new sfWidgetFormInput();
      
      $widgets .= strtr($this->getOption('template'), array(
                  '%class%'         => $this->getOption('class'),
                  '%id%'            => $this->generateId($name.$choice->getId()),
                  '%label_promote%' => $this->getOption('label_promote'),
                  '%label_demote%'  => $this->getOption('label_demote'),
                  '%label_remove%'  => $this->getOption('label_remove'),
                  '%promote%'       => sprintf('<a href="#" onclick="%s">%s</a>', 'promote(' . $choice->getId() . '); return false;', $this->getOption('promote')),
                  '%demote%'        => sprintf('<a href="#" onclick="%s">%s</a>', 'demote(' . $choice->getId() . '); return false;', $this->getOption('demote')),
                  '%remove%'        => sprintf('<a href="#" onclick="%s">%s</a>', 'remove(' . $choice->getId() . '); return false;', $this->getOption('remove')),
                  '%choice%'        => $widget->render(strtolower($this->getOption('model')).'_'.$choice->getId(), $choice->getName(), array('class' => $this->getOption('class'))),
              ));
    }
    
    $js = sprintf(<<<EOF
<script type="text/javascript" charset="utf-8">

function promote(id)
{
  $.ajax({
    url: '%s',
    type: 'GET',
    dataType: 'html',
    data: { id: id },
    success: function(data) {
     $('#%s').html(data);
   },
  });

}

function demote(id)
{
 $.ajax({
   url: '%s',
   type: 'GET',
   dataType: 'html',
   data: { id: id },
   success: function(data) {
     $('#%s').html(data);
  },
 });
}

function remove(id)
{
  var answer = confirm("%s");
  
  if (answer) {
    $.ajax({
      url: '%s',
      type: 'GET',
      dataType: 'html',
      data: { id: id },
      success: function(data) {
        $('#%s').html(data);
     }
    });
  }
  else {
    return false;
  }  
}

$(function() {
  $('.sortable').keyup(function() {
    var val = $(this).val();
    if (val.length == 0) return false;
    var id = $(this).attr('id');
    var el = $(this);
    id = id.split('_')[1];
    clearTimeout($.data(this, 'timer'));
    var wait = setTimeout(function() {
      $.getJSON('%s', { id: id, value: val },
       function(json){
          if (json.success == 'true')
          {
            el.css('background-color', '#DCC761').animate({'backgroundColor': 'white'}, 1500);
          }
          else 
          {
            el.css('background-color', '#E9594D').animate({'backgroundColor': 'white'}, 1500);
          }
      });
      
    }, 1000);
    $(this).data('timer', wait);
  });
});
</script>
EOF
, sfContext::getInstance()->getController()->genUrl($this->getOption('promote_url')), $this->getOption('update'), sfContext::getInstance()->getController()->genUrl($this->getOption('demote_url')), $this->getOption('update'), sfContext::getInstance()->getI18N()->__('Are you sure?'), sfContext::getInstance()->getController()->genUrl($this->getOption('remove_url')), $this->getOption('update'), sfContext::getInstance()->getController()->genUrl($this->getOption('save_url')));

    return content_tag('div', $widgets.$js, array('id' => $this->getOption('update')));
  }
}