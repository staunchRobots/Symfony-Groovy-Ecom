<?php

/**
 * An extension of sfWidgetFormTime.
 * 
 * @package     seegnoWidgetFormTime
 * @subpackage  widget
 * @author      Kris Wallsmith - http://svn.symfony-project.com/plugins/sfFormtasticPlugin/trunk/lib/widget/sfWidgetasticFormTime.class.php
 * @version     SVN: $Id$
 */
class seegnoWidgetFormTime extends sfWidgetFormTime
{
  /**
   * Adds the following options to sfWidgetFormTime:
   * 
   *  * format
   *  * format_without_seconds
   *  * format_without_ampm
   *  * format_without_seconds_or_ampm
   * 
   *  * hours_interval:     Interval between hours presented
   *  * minutes_interval:   Interval between minutes presented
   *  * seconds_interval:   Interval between seconds presented
   *  * 24_hour_clock:      Use a 24-hour clock
   * 
   * @see sfWidgetFormTime
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    // rename parent options
    $this->addOption('format_without_ampm', $this->getOption('format'));
    $this->addOption('format_without_seconds_or_ampm', $this->getOption('format_without_seconds'));

    // new defaults for parent options
    $this->setOption('format', '%hour%:%minute%:%second% %ampm%');
    $this->setOption('format_without_seconds', '%hour%:%minute% %ampm%');

    $this->addOption('hours_interval', 1);
    $this->addOption('minutes_interval', 1);
    $this->addOption('seconds_interval', 1);

    $this->addOption('24_hour_clock', false);

    // new default empty values
    $emptyValues = $this->getOption('empty_values');
    $emptyValues['ampm'] = null;
    $this->setOption('empty_values', $emptyValues);
  }

  /**
   * @see sfWidgetFormTime
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // 24_hour_clock
    if ($this->getOption('24_hour_clock'))
    {
      $lowHour  = 0;
      $highHour = 23;
    }
    else
    {
      $lowHour  = 1;
      $highHour = 12;
    }

    // hours_interval
    $hours = array();
    foreach (range($lowHour, $highHour, $this->getOption('hours_interval')) as $hour)
    {
      $formattedHour = strftime('%H', strtotime(sprintf('2000-01-01 %02d:00:00', $hour)));
      $hours[(int) $formattedHour] = $formattedHour;
    }
    $this->setOption('hours', $hours);

    // minutes_interval
    $minutes = array();
    foreach (range(0, 59, $this->getOption('minutes_interval')) as $minute)
    {
      $twoChar = sprintf('%02d', $minute);
      $minutes[(int) $twoChar] = $twoChar;
    }
    $this->setOption('minutes', $minutes);

    // seconds_interval
    $seconds = array();
    foreach (range(0, 59, $this->getOption('seconds_interval')) as $second)
    {
      $twoChar = sprintf('%02d', $second);
      $seconds[(int) $twoChar] = $twoChar;
    }
    $this->setOption('seconds', $seconds);

    // ampm
    if (!$this->getOption('24_hour_clock'))
    {
      $choices = array(
        strftime('%p', strtotime('2000-01-01 06:00:00')),
        strftime('%p', strtotime('2000-01-01 18:00:00')),
      );
      $choices = array_combine($choices, $choices);

      $default = array('ampm' => null);
      $emptyValues = $this->getOption('empty_values');

      if (is_array($value))
      {
        $time = array_merge($default, $value);
      }
      else
      {
        $time = ctype_digit($value) ? (integer) $value : strtotime($value);

        if (false === $time)
        {
          $time = $default;
        }
        else
        {
          $time = array('ampm' => strftime('%p', $time));
        }
      }

      $widget = new sfWidgetFormSelect(array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['ampm']) + $choices : $choices), array_merge($this->attributes, $attributes));
      $renderedAmPm = $widget->render($name.'[ampm]', $time['ampm']);
    }
    else
    {
      // replace options renamed in ->configure()
      $this->setOption('format', $this->getOption('format_without_ampm'));
      $this->setOption('format_without_seconds', $this->getOption('format_without_seconds_or_ampm'));
    }

    $retval = parent::render($name, date("g:i", strtotime($value)), $attributes, $errors);

    if (isset($renderedAmPm))
    {
      $retval = strtr($retval, array('%ampm%' => $renderedAmPm));
    }

    return $retval;
  }

  /**
   * Get an option value to be used with {@link strftime()}.
   * 
   * @param   string $name
   * 
   * @return  string
   */
  protected function getFormatOption($name)
  {
    $value = $this->getOption($name);

    if ($value && '%' != $value{0})
    {
      $value = '%'.$value;
    }

    return $value;
  }
}
