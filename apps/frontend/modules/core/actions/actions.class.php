<?php

class coreActions extends sfActions
{
  public function executeAboutUs(sfWebRequest $request)
  {
    $form = new ContactForm();
    
    return $this->renderPartial('core/about_us', array('form' => $form));
  }
  
  public function executeContact(sfWebRequest $request)
  {
    $type = $request->getParameter('type');
    $sale = false;
    $name = 'contact';
    
    $product = $request->hasParameter('id') ? ProductTable::getInstance()->find($request->getParameter('id')) : null;
    
    if ($type == 'sale')
    {
      $this->form = new OnSaleForm;
      $sale = true;
      $name = 'on_sale';
    }
    else 
    {
      $this->form = new ContactForm;
    }

    if ($request->isMethod('post'))
    {    
       $data = $request->getParameter($name);
       $this->form->bind($data);   
       
       if ($this->form->isValid())
       {
           $v = $this->form->getValues();
           
           $email = Swift_Message::newInstance();
 
           $to = 'kp.sergio@gmail.com';
           $from = $v['email'];
           $name = isset($v['name']) ? $v['name'] : null;
           $message = isset($v['message']) ? $v['message'] : null;
           $phone = isset($v['phone']) ? $v['phone'] : null;
           
           $email->setFrom($from);
           $email->setTo($to);
           
           if ($product && !$sale)
           {
             $v['product'] = $product;
             
             $subject = '[Baltimore Persian Rug] New contact message for product #' . $product->getId(); 
           }
           elseif ($sale)
           {
             $subject = '[Baltimore Persian Rug] New subscription for on-sale items'; 
           }
           else
           {
            $subject = '[Baltimore Persian Rug] New contact request'; 
           }
           
           $email->setSubject($subject);
           
           $body = $this->getPartial('core/email', array('data' => $v, 'sale' => $sale)); 

           $email->setBody($body, 'text/html');
          
           $this->getMailer()->send($email);

           $this->getUser()->setFlash('notice', sfContext::getInstance()->getI18N()->__('Thank you for getting in touch with us!'));
        }
    }
    
    if ($type == 'sale')
    {
      return $this->renderPartial('core/on_sale_form', array('form' => $this->form));
    }
    else
    {
      $this->redirect('@homepage');
    }

    return $this->renderPartial('core/contact_form', array('form' => $this->form, 'sidebar' => $this->getPartial(sprintf('core/%s_sidebar', $type))));
  }
}
?>