<ul class="admin_actions">
<li><?php echo link_to('Show sold', url_for('@homepage_options?type=all&filter=sold'), array('class' => 'link')) ?></li>
<li><?php echo link_to('Show pending sales', url_for('@homepage_options?type=all&filter=pending'), array('class' => 'link')) ?></li>
<li><?php echo link_to('Show incomplete', url_for('@homepage_options?type=all&filter=incomplete'), array('class' => 'link')) ?></li>
<li><?php echo link_to('Show complete', url_for('@homepage_options?type=all&filter=complete'), array('class' => 'link')) ?></li>
<li><?php echo link_to('Show unpublished', url_for('@homepage_options?type=all&filter=unpublished'), array('class' => 'link')) ?></li>
</ul>