<div class="message">
  <p>Carpetbegger's furniture warehouse, offers beautiful Persian rugs and Antique Chinese furniture at very low prices.</p>
  <p>We keep our prices low, by only opening our Glyndon, Maryland Warehouse by appointment only on weekends!</p>
  <p>Browse our selection of rugs and furniture on this site, and sign up to make an appointment by clicking the make an appointment button or calling us!</p>
  <p>Learn a little more about us by <?php echo link_to('reading this article about us in Style Magazine!', public_path('uploads/Carpetqueen.pdf')) ?></p>
  
  <form id="contact" action="<?php echo url_for('@contact') ?>" method="post">
    <?php echo $form ?>
    
    <input type="submit" value="Send" />
  </form>
</div>

<?php /*
<div class="movie">
  <object width="521" height="222">
    <param name="allowfullscreen" value="true" />
    <param name="allowscriptaccess" value="always" />
    <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=13710508&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ff9933&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" />
    <embed src="http://vimeo.com/moogaloop.swf?clip_id=13710508&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ff9933&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="521" height="222">
    </embed>
  </object>
</div>

*/ ?>