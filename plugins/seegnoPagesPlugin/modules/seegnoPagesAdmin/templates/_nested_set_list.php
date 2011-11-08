<?php use_javascript('/seegnoPagesPlugin/js/typewatch.js') ?>
<?php use_javascript('/seegnoPagesPlugin/js/ajaxqueue.js') ?>
<?php use_javascript('/seegnoPagesPlugin/js/equalheights.admin.js') ?>
 
<?php if(isset($records) && is_object($records) && count($records) > 0 ): ?>
  <div id="<?php echo strtolower($model);?>-nested-set">
      <ul class="nested_set_list">
      <?php $prevLevel = 0; ?>
      <?php foreach ($records as $record): ?>
        <?php if ($prevLevel > 0 && $record['level'] == $prevLevel): ?>
          <?php echo '</li>' ?>
        <?php endif ?>
        <?php if ($record['level'] > $prevLevel): ?>
          <?php echo '<ul>' ?>
          <?php else: ?>
            <?php if ($record['level'] < $prevLevel): ?>
              <?php echo str_repeat('</ul></li>', $prevLevel - $record['level']) ?>
            <?php endif ?>
        <?php endif ?>
  
          <li id ="phtml_<?php echo $record->id ?>" <?php echo ($record['level'] == 0) ? 'class="root"' : '' ?>>
              <a href="#"><ins>&nbsp;</ins><?php echo $record->$field;?></a> 
          <?php $prevLevel = $record['level'] ?>
        <?php endforeach ?>
      </ul>
  </div>
<?php endif ?>


<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
  var ajaxManager = $.manageAjax.create('treeQueue', { queue: 'clear', cacheResponse: false, preventDoubbleRequests: true });
  
  $(".sf_admin_action_save").live("click", function(e) {
   e.preventDefault();
   $.manageAjax.add('treeQueue', {
      type: "POST",
      url : '<?php echo url_for("seegnoPagesAdmin/update") ?>',
      dataType : 'html',
      data : $('#page_form').serialize(),
      complete: function(){ 
        $('.waiting').remove();
        equalHeights('.admin-left', '.admin-right', 350);
      },
      success: function (data) {
        $('#embedForm').html(data);
        equalHeights('.admin-left', '.admin-right', 350);
      }
    });
  });  
});

$(function () {  
  $("#page-nested-set").tree({
    opened : ['phtml_<?php echo $records->getFirst()->getId() ?>'],
    plugins : { 
      cookie : { prefix : "<?php echo strtolower($model);?>_jstree_" }
    },
    ui : { theme_name : "seegno" },
    callback: {
      onselect: function (NODE, TREE_OBJ, RB){
        $.manageAjax.add('treeQueue', {
              type: "POST",
              url : '<?php echo url_for('seegnoPagesAdmin/embedForm');?>',
              dataType : 'html',
              data : { id: NODE.id.replace('phtml_','') },
              complete : function(){ 
                $('.waiting').remove();
              },
              success : function (data) {
                $('#embedForm').html(data);
                equalHeights('.admin-left', '.admin-right', 350);
              },
            });
       },
      onchange: function() { $('.nodeinteraction').attr('disabled',''); },
      
      onrename: function (NODE, TREE_OBJ, RB) {
        $('.error').remove();
        $('.notice').remove();
        $('.nested_set_manager_holder').before('<div class="waiting"><?php echo __("Sending data to server.");?></div>');
        equalHeights('.admin-left', '.admin-right', 350);
        if (TREE_OBJ.get_text(NODE) == 'New page' || TREE_OBJ.get_text(NODE) == 'New folder' ){
          $('.nested_set_manager_holder').before('<div class="error">"'+TREE_OBJ.get_text(NODE)+'" <?php echo __("is not a valid name");?></div>');
          $.tree.focused().rename();
        }
        else {
          if (NODE.id == '') {
            equalHeights('.admin-left', '.admin-right', 350);
            $.manageAjax.add('treeQueue', {
              type: "POST",
              url : '<?php echo url_for("seegnoPagesAdmin/Add_child");?>',
              dataType : 'json',
              data : 'model=<?php echo $model ?>&field=<?php echo $field ?>&value='+escape(TREE_OBJ.get_text(NODE))+'&parent_id=' + TREE_OBJ.parent(NODE).attr('id').replace('phtml_',''),
              complete : function(){ 
                $('.waiting').remove();
                equalHeights('.admin-left', '.admin-right', 350);
              },
              success : function (data, textStatus) {
                $('.nested_set_manager_holder').before('<div class="notice"><?php echo __('The item was created successfully.');?></div>');
                $(NODE).attr('id','phtml_'+data.id);
                
                $.manageAjax.add('treeQueue', {
                  type: "POST",
                  url: '<?php echo url_for("seegnoPagesAdmin/embedForm") ?>',
                  data: { id: data.id },
                  dataType : 'html',
                  complete: function(){ 
                    $('.waiting').remove();
                    equalHeights('.admin-left', '.admin-right', 350);
                  },
                  success: function (data) {
                    $('#embedForm').html(data);
                    equalHeights('.admin-left', '.admin-right', 350);
                  }
                });
              },
              error : function (data, textStatus) {
                $('.nested_set_manager_holder').before('<div class="error"><?php echo __('Error while creating the item.');?></div>');
                $.tree.rollback(RB);
                equalHeights('.admin-left', '.admin-right', 350);
              }
            });
          }
          else { // happen when renaming an existing node
            equalHeights('.admin-left', '.admin-right', 350);
            $.manageAjax.add('treeQueue', {
              type: "POST",
              url : '<?php echo url_for('seegnoPagesAdmin/Edit_field');?>',
              dataType : 'json',
              data : 'model=<?php echo $model;?>&field=<?php echo $field;?>&value='+TREE_OBJ.get_text(NODE)+'&id=' + NODE.id.replace('phtml_',''),
              complete : function(){ 
                $('.waiting').remove();
                equalHeights('.admin-left', '.admin-right', 350);
              },
              success : function (data, textStatus) {
                $('.nested_set_manager_holder').before('<div class="notice"><?php echo __('The item was renamed successfully.');?></div>');
                equalHeights('.admin-left', '.admin-right', 350);
              },
              error : function (data, textStatus) {
                $('.nested_set_manager_holder').before('<div class="error"><?php echo __('Error while renaming the item.');?></div>');
                $.tree.rollback(RB);
                equalHeights('.admin-left', '.admin-right', 350);
              }
            });
          }
        }
      },
      
      onmove: function(NODE, REF_NODE, TYPE, TREE_OBJ, RB){
        $('.error').remove();
        $('.notice').remove();
        $('.nested_set_manager_holder').before('<div class="waiting"><?php echo __('Sending data to server...');?></div>');
        equalHeights('.admin-left', '.admin-right', 350);
        $.manageAjax.add('treeQueue', {
          type: "POST",
          url : '<?php echo url_for('seegnoPagesAdmin/Move');?>',
          dataType : 'json',
          data : 'model=<?php echo $model;?>&id=' + NODE.id.replace('phtml_','') +'&to_id=' + REF_NODE.id.replace('phtml_','') + '&movetype=' + TYPE,
          complete : function(){
            $('.waiting').remove();
            equalHeights('.admin-left', '.admin-right', 350);
          },
          success : function (data, textStatus) {
            $('.nested_set_manager_holder').before('<div class="notice"><?php echo __('The item was moved successfully.');?></div>');
            equalHeights('.admin-left', '.admin-right', 350);
          },
          error : function (data, textStatus) {
            $('.nested_set_manager_holder').before('<div class="error"><?php echo __('Error while moving the item.');?></div>');
            $.tree.rollback(RB);
            equalHeights('.admin-left', '.admin-right', 350);
          }
        });
      },
      onopen: function(NODE, TREE_OBJ, RB){ equalHeights('.admin-left', '.admin-right', 350); },
      onopen_all:  function(NODE, TREE_OBJ, RB){ equalHeights('.admin-left', '.admin-right', 350); },
      onclose: function(NODE, TREE_OBJ, RB){ equalHeights('.admin-left', '.admin-right', 350); },
      onclose_all:  function(NODE, TREE_OBJ, RB){ equalHeights('.admin-left', '.admin-right', 350); },
      ondelete: function(NODE, TREE_OBJ, RB){
        $('.error').remove();
        $('.notice').remove();
        $('.nested_set_manager_holder').before('<div class="waiting"><?php echo __('Sending data to server.');?></div>');
        equalHeights('.admin-left', '.admin-right', 350);
        $.manageAjax.add('treeQueue', {
          type: "POST",
          url : '<?php echo url_for('seegnoPagesAdmin/Delete');?>',
          dataType : 'json',
          data : 'model=<?php echo $model;?>&id=' + NODE.id.replace('phtml_',''),
          complete : function(){ 
            $('.waiting').remove();
            equalHeights('.admin-left', '.admin-right', 350);
          },
          success : function (data, textStatus) {
            $('.nested_set_manager_holder').before('<div class="notice"><?php echo __('The item was deleted successfully.');?></div>');
            equalHeights('.admin-left', '.admin-right', 350);
          },
          error : function (data, textStatus) {
            $('.nested_set_manager_holder').before('<div class="error"><?php echo __('Error while deleting the item.');?></div>');
            $.tree.rollback(RB);
            equalHeights('.admin-left', '.admin-right', 350);
          }
        });
      }
    }
  });
});
</script>