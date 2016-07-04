<?php
namespace Components\Invoice;

class Popup extends \Nette\Application\UI\Control
{
    private $show = false;

    public $orderId = null;

    public function render()
    {
        
        $template = $this->template;
        $template->setFile(__DIR__ . '/Popup.latte');
        
        $template->show = $this->getShow();

        if($this->orderId) {

            $template->order = $order = \OrderManager::order($this->orderId);
            $template->products = $order->getProducts();
            
        }

        // a vykreslíme ji
        $template->render();
    }

    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }


   

    public function setContent($form) {
        $this->formContent = $form;
    }


    public function setShow($show) {
        $this->show = $show;
    }

    public function getShow() {
        return $this->show;
    }

}