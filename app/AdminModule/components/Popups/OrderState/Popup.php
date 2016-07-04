<?php
namespace Components\OrderState;

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

            //$manufacturer = \ManufacturerManager::manufacturer($this->manufacturerId);

            $this["newStateForm"]->setDefaults(
                array(
                    "id" => $this->orderId
                )
            );
        }

        // a vykreslíme ji
        $template->render();
    }

    public function setOrderId($orderId) {
        $this->orderId = $orderId;
    }

    public function createComponentNewStateForm() {
        $form = new \Nette\Application\UI\Form;
        
        $statuses = \OrderStatusManager::orderStatuses();

        $statusesArray = array();

        foreach($statuses as $status) {
            $statusesArray[$status->id] = $status->name;
        }

        $form->addSelect('name', 'Stav', $statusesArray );
        $form->addHidden("id");
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = array($this, 'onNewStateForm');

        return $form;
    }

    public function onNewStateForm($form) {
        $val = $form->values;
        
        \OrderHistoryManager::orderHistory(array(  
            "order" => $val["id"],
            "orderStatus"=> $val["name"]
        ))->save();

        $this->presenter->flashMessage("Stav byl uložen","success");
        $this->presenter->redirect("this");

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