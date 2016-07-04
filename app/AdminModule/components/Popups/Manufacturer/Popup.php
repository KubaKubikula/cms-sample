<?php
namespace Components\Manufacturer;

class Popup extends \Nette\Application\UI\Control
{
    private $show = false;

    public $manufacturerId = null;

    public function render()
    {
        
        $template = $this->template;
        $template->setFile(__DIR__ . '/Popup.latte');
        
        $template->show = $this->getShow();

        if($this->manufacturerId) {

            $manufacturer = \ManufacturerManager::manufacturer($this->manufacturerId);

            $this["newManufacturerForm"]->setDefaults(
                array(
                    "name" => $manufacturer->name,
                    "id" => $manufacturer->id
                )
            );
        }

        // a vykreslíme ji
        $template->render();
    }

    public function setManufacturerId($manufacturerId) {
        $this->manufacturerId = $manufacturerId;
    }

    public function createComponentNewManufacturerForm() {
        $form = new \Nette\Application\UI\Form;
        
        $form->addText('name', 'Název výrobce')
            ->setRequired('Vyplňte název.');
        $form->addHidden("id");
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = array($this, 'onNewManufacturerForm');

        return $form;
    }

    public function onNewManufacturerForm($form) {
        $values = $form->values;
        \ManufacturerManager::manufacturer(array(
            "id" => $values["id"],
            "name" => $values["name"]
        ))->save();

        $this->presenter->flashMessage("Výrobce byl uložen","success");
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