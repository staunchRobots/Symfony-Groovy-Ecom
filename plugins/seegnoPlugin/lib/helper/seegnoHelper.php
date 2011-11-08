<?php

$no_toggleMenu = sfConfig::get('app_seegnoPlugin_noToggleMenu');

if (!isset($no_toggleMenu) || !$no_toggleMenu)
{
  sfContext::getInstance()->getResponse()->addJavascript('/seegnoPlugin/js/togglemenu.js', 'last');
}

if (in_array('seegnoModal', sfConfig::get('sf_enabled_modules')))
{
  if (sfConfig::get('app_seegnoModal_stylesheet') != 'none')
  {
    $stylesheet = sfConfig::get('app_seegnoModal_stylesheet') ? sfConfig::get('app_seegnoModal_stylesheet') : '/seegnoPlugin/css/modal.css';
    use_stylesheet($stylesheet);
  }

  $javascript = sfConfig::get('app_seegnoModal_javascript');

  if (!isset($javascript) || $javascript)
  {
    sfContext::getInstance()->getResponse()->addJavascript('/js/custom/modal.js', 'last');
  }

}

function h2hm($hours)
{
  $t = explode(".", $hours); 
  $h = $t[0]; 
  if (isset($t[1])) { 
    $m = $t[1]; 
  } else { 
    $m = "00"; 
  } 
  $mm = $h . ':' . round(($m*60)/100) . 'h';

  return $mm;
}

function remove_bbcode($text)
{
  $text = preg_replace('/\[center\](.*?)\[\/center\]/i', '\\1', $text);
  $text = preg_replace('/\[justify\](.*?)\[\/justify\]/i', '\\1', $text);
  $text = preg_replace('/\[small\](.*?)\[\/small\]/i', '\\1', $text);
  $text = preg_replace('/\[url=(.*?)\](\w+)/i', '<a target="_blank" href="\\1">\\2</a>', $text); 
  $text = preg_replace('/(\[url=)(.+)(\])(.+)(\[\/url\])/i', '<a target="_blank" href="\\2">\\4</a>', $text);
  $text = preg_replace('/(\[color=(.+)\])/i', '', $text);
  $text = preg_replace('/(\[url\])(.+)/i', '<a target="_blank" href="\\2">\\2</a>', $text);

  return $text;
}

function digitalize($number, $pad = false)
{
  $digits = array();

  $position = (string) ($number);

  $length = strlen($position);

  if ($pad && $length == 1)
  {
    $digits[] = 0;
  }

  for ($i=0; $i < $length; $i++)
  {
    $digits[] = $position[$i];
  }

  return $digits;
}

function ucfirstHTMLentity($matches)
{
  return "&".ucfirst(strtolower($matches[1])).";";
}

function uppercase($str)
{
  $subject = strtoupper(htmlentities($str, null, 'UTF-8'));
  $pattern = '/&([A-Z]+);/';
  return preg_replace_callback($pattern, "ucfirstHTMLentity", $subject);
}


function _load_menu($menu)
{
  $items = sfConfig::get($menu);
  $items  = $items['items'];

  return $items;
}

function _load_typeface( $version )
{
  use_javascript('/seegnoPlugin/js/typeface/typeface.' . $version . '.js');
}

function seegno_typeface($name)
{
  _load_typeface(sfConfig::get('app_typeface_version', 'dev'));

  use_javascript('/seegnoPlugin/js/typeface/' . $name . '.typeface.js');
}

function seegno_menu_item($menu_)
{
  sfContext::getInstance()->getConfiguration()->loadHelpers('I18N'); 

  // load data for the chosen menu
  $data = sfConfig::get( 'app_seegnoMenu_' . $menu );

  if (!$data)
  {
    return false;
  }

  $module = sfContext::getInstance()->getRequest()->getParameter('module');
  $action = sfContext::getInstance()->getRequest()->getParameter('action');

  if (sfContext::getInstance()->getRequest()->hasParameter('menu_' . $menu))
  {
    $active = sfContext::getInstance()->getRequest()->getParameter('menu_' . $menu);
  }
  elseif (isset($items[$module . "_" . $action]))
  {
    $active = $module . "_" . $action;
  }
  elseif (isset($items[$action]))
  {
    $active = $action;
  }
  else
  {
    $active = $module;
  }

  if (!isset($data['items'][$active]))
  {
    return false;
  }

  return $data['items'][$active];
}

function seegno_menu_get_active($menu, $parameters = array())
{
  if (isset($parameters['active']))
  {
    $active = $parameters['active'];
  }
  else
  {
    $active = null;
    $module = sfContext::getInstance()->getRequest()->getParameter('module');
    $action = sfContext::getInstance()->getRequest()->getParameter('action');

    if (sfContext::getInstance()->getRequest()->hasParameter('cms') && ($slug = sfContext::getInstance()->getRequest()->getParameter('slug')))
    {
      $parent = Doctrine::getTable('Page')->createQuery('p')->where("p.menu = ? AND ? LIKE CONCAT(p.slug, '%')", array($menu, $slug))->orderBy('p.level DESC, LENGTH(p.slug) DESC')->fetchOne();

      if ($parent)
      {
        $active = $parent->getSlug();
      }
    }

    $items = $parameters['items'];
    if ($active)
    {
    }
    elseif (sfContext::getInstance()->getRequest()->hasParameter('menu_' . $menu))
    {
      $active = sfContext::getInstance()->getRequest()->getParameter('menu_' . $menu);
    }
    elseif (isset($items[$module . "_" . $action]))
    {
      $active = $module . "_" . $action;
    }
    elseif (isset($items[$action]))
    {
      $active = $action;
    }
    else
    {
      $active = $module;
    }
  }

  return $active;
}

function seegno_menu_pages($menu, $parameters)
{
  // Get all pages under this menu
  if (!class_exists('Page'))
  {
    return;
  }

  $pages = null;
  $slug = isset($parameters['active']) ? $parameters['active'] : sfContext::getInstance()->getRequest()->getParameter('slug');

  if ($slug == 'homepage')
  {
    $parent = Doctrine_Query::create()
            ->from('Page x, Page p')
            ->select('x.*')
            ->where('(p.slug = ? OR p.slug IS NULL) AND x.menu <> ?', array($slug, 'none'))
            ->addWhere('p.is_published = true')
            ->addWhere('p.lft > x.lft AND p.rgt < x.rgt AND x.menu <> p.menu')
            ->orderby('x.level DESC')
            ->fetchOne();
  }
  else
  {
    $parent = Doctrine_Query::create()
            ->from('Page x, Page p')
            ->select('x.*')
            ->where('(p.slug = ? OR p.slug IS NULL) AND x.menu <> ?', array($slug, 'none'))
            ->addWhere('p.is_published = true')
            ->addWhere('p.lft > x.lft AND p.rgt < x.rgt AND x.menu <> p.menu')
            ->orderby('x.level DESC')
            ->fetchOne();
  }

  if ($parent)
  {
    $pages = Doctrine_Query::create()->from('Page p')->where('p.lft BETWEEN ? AND ? AND p.menu = ? AND level = ?', array($parent->getLft(), $parent->getRgt(), $menu, $parent->getLevel() + 1))->addWhere('is_published = true')->orderBy('p.lft ASC')->execute();

    if (!count($pages))
    {
      $pages = null;
    }
  }

  if ($pages === null)
  {
    $pages = Doctrine::getTable('Page')->createQuery('p')->where("p.menu = ?", array($menu))->addWhere('p.is_published = true')->orderBy('p.lft ASC')->execute();
  }

  $items = array();

  if (isset($pages))
  {
    foreach ($pages as $page)
    {
      if ($page->getLink() && ($page->getLink() != null) && ($page->getLink() != ""))
      {
        $href = $page->getLink();
      }
      else
      {
        $href = $page->getRoute();
      }
      
      $items[$page->getSlug()] = array('name' => $page, 'link' => $href, 'cms' => true, 'slug' => $page->getSlug(), 'id' => $page->getSlug());
    }
  }
  
  // Fetching Children
  foreach ($pages as $page)
  {
    if (method_exists($page, 'getChildren'))
    {
      $children = $page->getChildren();
    }
    else
    {
      $children = $page->getNode()->getChildren();
    }

    if ($children)
    {
      foreach ($children as $child)
      {
        if (isset($child['menu']) && (($child['menu'] != $menu)))
        {
          if (!isset($parameters['children']['include']) || (!$child['is_published']))
          {
            continue;
          }
        }
        
        if (isset($child['link']) && $child['link'] != null)
        {
          $link = $child['link'];
        }
        else
        {
          $link = '@page?slug=' . $child['slug'];
        }
        
        $items[$page->getSlug()]['children'][$child['slug']] = array('name' => $child['title'], 'link' => $link, 'cms' => true, 'slug' => $child['slug']);
      }
    }
  }

  return $items;
}

function seegno_menu($menu = 'main', $parameters = array())
{  
  sfContext::getInstance()->getConfiguration()->loadHelpers('I18N'); 

  $options = sfConfig::get('app_seegnoMenu_options');

  $use_stylesheets = (isset($options['use_stylesheets']) and (!$options['use_stylesheets'])) ? false : true;

  // check for a stylesheet
  $sf_web = sfConfig::get( 'sf_web_dir' );
  $sf_css = '/seegnoPlugin/css/' . sfConfig::get( 'sf_app' ) . '.css';
  if (file_exists($sf_web . $sf_css) and $use_stylesheets)
  {
    use_stylesheet($sf_css);
  }

  // load data for the chosen menu
  $data = sfConfig::get('app_seegnoMenu_' . $menu);

  if (!$data)
  {
    $data = sfConfig::get('mod_' . $menu . '_seegnoMenu_' . $menu);

    if (!$data)
    {
      return false;
    }
  }

  // setup default parameters
  $defaults = array(
    'descriptions'  => false,
    'active_class'  => 'active',
    'normal_class'  => 'normal',
    'first_class'   => 'first',
    'last_class'    => 'last',
    'class'         => '',
    'id'            => '',
    'prefix'        => '',
    'suffix'        => '',
    'items'         => array(),
    'tag'           => 'ul',
    'itemtag'       => 'li',
    'pages'         => false,
    );

  $is_child = false;

  foreach ($defaults as $def_key => $def_val)
  {
    if ( isset($parameters[$def_key] ) )
      ;
    else if ( isset($data[$def_key] ) )
      $parameters[$def_key] = $data[$def_key];
    else
      $parameters[$def_key] = $def_val;
  }

  $items = $parameters['items'];

  if ($parameters['pages'] == 'append')
  {
    $items = array_merge($items, seegno_menu_pages($menu, $parameters));
  }
  elseif ($parameters['pages'] == 'prepend')
  {
    $items = array_merge(seegno_menu_pages($menu, $parameters), $items);
  }

  // Enable stuff
  foreach ($items as $item => $options)
  {
    if (isset($options['desc']))
    {
      $parameters['descriptions'] = true;
    }
  }

  $html = "";
  $class = "first ";

  end($items);
  $last = key($items);

  foreach ($items as $item => $options)
  {
    if (isset($options['show']))
    {
      if (($options['show'] == 'authenticated') and !sfContext::getInstance()->getUser()->isAuthenticated())
        continue;

      if (($options['show'] == 'anonymous') and sfContext::getInstance()->getUser()->isAuthenticated())
        continue;
    }

    if (isset($options['credentials']) and !sfContext::getInstance()->getUser()->hasCredential($options['credentials']))
    {
      continue;
    }

    $key = $item;
    if (isset($options['key']))
      $key=$options['key'];

    if (!isset($options['link']))
      $options['link'] = '@' . $key;

    if (isset($options['include_culture']))
      $options['link'] .= '?sf_culture=' . sfContext::getInstance()->getUser()->getCulture();

    if (!isset($options['desc']))
      $options['desc']  = '';

    if (!isset($options['name']))
      $options['name']  = '';

    if (isset($options['class']))
      $class  .= $options['class'] . " ";

    // If link option is an external URL
    if (preg_match("/http:\/\//", $options['link']))
    {
    }
    elseif (stripos($options['link'], 'mailto') !== false)
    {
    }
    elseif (preg_match_all( "/:(\w*)/", $options['link'], $link_vars ))
    {
      $link_vars  = $link_vars[1];
      foreach ( $link_vars as $var )
      {
        $val  = sfContext::getInstance()->getRequest()->getParameter($var);
        $options['link']  = str_replace( ":$var", $val, $options['link'] );
      }
    }
  

    if (isset($parameters['replace']))
    {
      foreach ( $parameters['replace'] as $var => $value )
      {
        $options['link']  = str_replace( ":$var", $value, $options['link'] );
      }
    }

    if (!empty($options['name']))
      $options['name']  = __($options['name']);

    if (isset($options['case']))
    {
      switch($options['case'])
      {
        case 'upper':  $options['name']  = strtoupper($options['name']); break;
        case 'lower': $options['name']  = strtolower($options['name']); break;
      }
    }

    $parameters['active'] = seegno_menu_get_active($menu, $parameters);

    $class .= $parameters[$key == $parameters['active'] ? 'active_class' : 'normal_class'];

    if ( $item == $last )
      $class  .= " last";

    $name = "";
    if ( $parameters['descriptions'] )
    {
      if (!empty($options['name']))
        $name .= '<span class="name">' . $options['name'] . '</span>';
      if (!empty($options['desc']))
        $name .= '<span class="description">' . __($options['desc']) . '</span>';
    }
    else if (!empty($options['name']))
      $name .= $options['name'];

    if ( isset($options['icon']) )
    {
      $name = image_tag($options['icon'], array( 'class' => 'icon' )) . $name;
    }

    $linkoptions = array();
    if ( isset($options['linkclass']) )
      $linkoptions['class'] = $options['linkclass'];
    if ( isset($options['linktitle']) )
      $linkoptions['title'] = $options['linktitle'];
    if ( isset($options['confirm']) )
      $linkoptions['confirm'] = $options['confirm'];

    $itemoptions  = array( 'class' => $class );
    if (isset($options['id']))
      $itemoptions['id']  = $options['id'];

    $url  = "";
    if (isset($options['link']) and ($options['link'] != 'none'))
    {
      if (stripos($options['link'], 'mailto') !== false)
      {
        $url .= $options['link'];
      }
      else
      {
       $url .= str_ireplace('%2F', '/', url_for($options['link'])); 
      }
    }

    if (isset($options['anchor']))
      $url .= "#" . $options['anchor'];

    if (empty($name))
    {
      if ($options['link'] != 'none')
      {
        if (isset($options['target']))
        {
          $itemoptions['onclick'] = "window.open('" . $url . "')";
        }
        else
        {
          $itemoptions['onclick'] = "window.location = '" . $url . "'";
        }
      }

      $link = "";
    }
    elseif($options['link'] == 'none')
    {
      $link = content_tag('span', $name, $linkoptions);
    }
    else
    {
      if (isset($options['target']))
      {
        $linkoptions['target'] = $options['target'];
      }

      if (isset($data['use_span']) and $data['use_span'])
      {
        $element = content_tag('span', $name);
      }
      else
      {
        $element = $name;
      }

      if (isset($linkoptions['confirm']))
      {
        $link = link_to($element, $options['link'], $linkoptions );
      }
      else
      {
        $linkoptions['href']  = $url;

        $link = content_tag('a', $element, $linkoptions);
      }
    }

    $content  = $parameters['prefix'] . $link . $parameters['suffix'];
    if (isset($options['partial']))
    {
      $parameters['item'] = $item;

      $content  .= get_partial($options['partial'], $parameters);
    }

    if (isset($parameters['is_child']) && $parameters['is_child'])
    {
      if (isset($parameters['display']) && !$parameters['display'])
      {
        foreach ($parameters['items'] as $item)
        {
          $show = false;

          if ($item['slug'] == sfContext::getInstance()->getRequest()->getParameter('slug'))
          {
            $show = true;
            break;
          }
        }

        if ($show === false)
        {
          $parameters['style'] = 'display: none;';
        }
      }

      // <Else> for future use, we can customize children here
    }

    if (isset($parameters['children']) && $parameters['children'])
    {
      if (isset($parameters['children']['include']) && $parameters['children']['include'])
      {
        if ((isset($options['slug']) && ($menu != $options['slug'])) && isset($items[$options['slug']]['children']))
        {
          $content .= seegno_menu($menu, array('id' => false, 'active' => sfContext::getInstance()->getRequest()->getParameter('slug'), 'is_child' => true, 'display' => false, 'items' => $items[$options['slug']]['children'], 'pages' => false));
          
          $itemoptions['class'] .= ' parent';
        }
      }
    }

    $html .= content_tag( $parameters['itemtag'], $content, $itemoptions );
    $class  = "";
  }

  // create UL with right params
  $params = array();
  if (!empty($parameters['class']))  $params['class']  = $parameters['class'];
  if (!empty($parameters['id']))  $params['id']  = $parameters['id'];
  if (!empty($parameters['style']))  $params['style']  = $parameters['style'];

  return content_tag( $parameters['tag'], $html, $params );
}

function seegno_menu_admin($menu = 'main', $active = '')
{
  return seegno_menu( $menu, array( 'active' => $active ) );
}

function javascriptify($javascript)
{
  $javascript = str_replace('\r', '', $javascript);
  $javascript = str_replace('\n', '', $javascript);

  return addslashes($javascript);
}

function url_for_with_path($args) 
{
  return str_replace('%2F', '/', url_for($args));
}

function link_to_with_path($text, $uri, $options = array()) 
{ 
  return str_replace('%2F', '/', link_to($text, $uri, $options)); 
}

function br2p($text)
{
  if (!stripos('<br', $text) && !stripos('&lt;br', $text))
  {
    return $text;
  }

  $text = '<p>' . $text . '</p>';

  // replace all break tags with paragraph
  // ending and starting tags
  $text = str_replace(array('&lt;br&gt;', '<br>', '<br />', '<BR>', '<BR />'), "</p>\n<p>", $text);

  // remove empty paragraph tags
  $text = str_replace(array('<p></p>', '<p></p>', "<p>\n</p>"), '', $text);

  return $text;
}

function fmt_currency($value, $options = array())
{
  if (!isset($options['currency']))   $options['currency']  = '&euro;';
  if (!isset($options['scale']))      $options['scale']     = 0;
  if (!isset($options['preffix']))    $options['preffix']   = false;
  if (!isset($options['negative']))   $options['negative']  = false;
  if (!isset($options['positive']))   $options['positive']  = false;

  $str  = number_format($value, $options['scale']);
  if ($options['preffix'])
    $str  = $options['currency'] . number_format($value, $options['scale']);
  else
    $str  = number_format($value, $options['scale']) . $options['currency'];

  if ($options['negative'] && $value < 0)
  {
    $str  = "<span class=\"" . $options['negative'] . "\">" . $value . "</span>";
  }
  else if ($options['positive'])
  {
    $str  = "<span class=\"" . $options['positive'] . "\">" . $value . "</span>";
  }

  return $str;
}

function seegno_modal($label, $route, $class = false, $options = array())
{
  if ($class) {
    $params = array(url_for($route), $class);
  } else {
    $params = array(url_for($route));
  }
  echo content_tag('a', $label, array_merge($options, array('href' => url_for($route), 'data-seegno' => json_encode(array('method' => 'showModal', 'params' => $params)))));
}
