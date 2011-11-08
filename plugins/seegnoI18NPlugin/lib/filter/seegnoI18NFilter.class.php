<?php
class seegnoI18NFilter extends sfFilter
{
  public function execute($filterChain)
  {
    if ($this->getContext()->getRequest()->hasParameter('sf_culture'))
    {
      $this->getContext()->getUser()->setCulture($this->getContext()->getRequest()->getParameter('sf_culture', 'en'));
    }

    if ($this->getContext()->getUser()->getCulture())
    {
      $this->getContext()->getRequest()->setParameter('sf_culture', $this->getContext()->getUser()->getCulture());
    }

    // execute next filter
    $filterChain->execute();
  }
}