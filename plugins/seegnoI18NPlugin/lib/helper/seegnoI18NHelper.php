<?php

function flags($showImages = false, $parameters = array())
{
  $content = '';

  if (!isset($parameters['active_class']))
  {
    $parameters['active_class'] = 'active';
  }

  if (!isset($parameters['normal_class']))
  {
    $parameters['normal_class'] = 'normal';
  }

  foreach (sfContext::getInstance()->getUser()->getLanguages() as $culture => $language)
  {
    $class = (sfContext::getInstance()->getUser()->getCulture() == $culture) ? $parameters['active_class'] : $parameters['normal_class'];

    if ($showImages)
    {
      if (isset($parameters['disabled_images']) and ($parameters['disabled_images'] == true) and (sfContext::getInstance()->getUser()->getCulture() != $culture))
      {
        $flag = 'flags/' . $language['slug'] . '_disabled';
      }
      else
      {
        $flag = 'flags/' . $language['slug'];
      }

      $content .= content_tag('div', link_to(image_tag($flag, array('alt' => $language['name'], 'class' => $class, 'title' => $language['name'])), '@seegno_i18n_culture?culture=' . $language['slug']), array('class' => 'flag'));

    }
    else
    {
      $content .= content_tag('div', link_to($language['culture'], '@seegno_i18n_culture?culture=' . $language['slug']), array('class' => 'language'));
    }
  }

  return content_tag('div', $content, array('id' => 'languages'));
}

function languages($parameters = array())
{
  $content = '';
  $languages = sfContext::getInstance()->getUser()->getLanguages();
  $i = 1;

  if (!isset($parameters['active_class']))
  {
    $parameters['active_class'] = 'active';
  }

  if (!isset($parameters['normal_class']))
  {
    $parameters['normal_class'] = 'normal';
  }

  foreach ($languages as $culture => $language)
  {
    $name = (isset($parameters['acronym']) and $parameters['acronym']) ? $language['acronym'] : $language['name'];

    $class = (sfContext::getInstance()->getUser()->getCulture() == $culture) ? $parameters['active_class'] : $parameters['normal_class'];

    if (!isset($parameters['element']))
    {
      $content .= content_tag('span', link_to($name, '@seegno_i18n_culture?culture=' . $language['slug'], array('class' => $class)));
    }
    else
    {
	     $content .= content_tag($parameters['element'], link_to($name, '@seegno_i18n_culture?culture=' . $language['slug'], array('class' => $class)), (isset($parameters['labels']) ? array('class' => $culture) : ''));
    }
    
    if (isset($parameters['separator']) and ($i < count($languages)))
    {
      $content .= ' | ';
      $i++;
    }
  }

  return $content;
}