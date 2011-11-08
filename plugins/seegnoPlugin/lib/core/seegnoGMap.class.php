<?php

class seegnoGMap extends GMap
{
  private $has_tooltips = false;

  private function  initTooltips()
  {
    if ($this->has_tooltips) return;

    $this->has_tooltips   = true;

    $this->addAfterInitJs('var tooltip = document.createElement("div"); tooltip.className="tooltip"; document.getElementById("map").appendChild(tooltip); tooltip.style.visibility="hidden";');
    $this->addAfterInitJs('function showTooltip(marker) { 
              tooltip.innerHTML = marker.tooltip;
              tooltip.style.visibility="visible";
              var point=map.getCurrentMapType().getProjection().fromLatLngToPixel(map.getBounds().getSouthWest(),map.getZoom());
              var offset=map.getCurrentMapType().getProjection().fromLatLngToPixel(marker.getPoint(),map.getZoom());
              var anchor=marker.getIcon().iconAnchor;
              var width=marker.getIcon().iconSize.width;
              var size = new GSize(offset.x - point.x - anchor.x + width,- offset.y + point.y +anchor.y);
              var pos = new GControlPosition(G_ANCHOR_BOTTOM_LEFT, size); 
              pos.apply(tooltip);
            }');

  }


  public function addMarker($marker)
  {
    $properties = $marker->getCustomProperties();

    if (isset($properties['tooltip']))
    {
      $this->initTooltips();
      $marker->addEvent(new GMapEvent('mouseover', 'showTooltip(this)'));
      $marker->addEvent(new GMapEvent('mouseout', 'tooltip.style.visibility="hidden"'));
      // $marker->addEvent(new GMapEvent('mouseover', 'this.openInfoWindowHtml(this.tooltip)'));
      // $marker->addEvent(new GMapEvent('mouseout', 'this.closeInfoWindow()'));
    }

    if (isset($properties['location']))
    {
       $marker->addEvent(new GMapEvent('click', 'window.location = this.location'));
    }

    parent::addMarker($marker);
  }
}
