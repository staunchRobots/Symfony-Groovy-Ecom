<?php

class NotesForm extends BaseProductForm
{
  public function configure()
  {
    $this->useFields(array('notes'));
    
    $this->widgetSchema->setNameFormat('notes[%s]');
  }
}
