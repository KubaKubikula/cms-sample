<?php
namespace Components\Supplier;

class Popup extends \Nette\Application\UI\Control
{
    private $show = false;

    public $supplierId = null;

    public function render()
    {
        
        $template = $this->template;
        $template->setFile(__DIR__ . '/Popup.latte');
        
        $template->show = $this->getShow();

        if($this->supplierId) {

            $supplier = \SupplierManager::supplier($this->supplierId);

            $this["newSupplierForm"]->setDefaults(
                array(
                    "name" => $supplier->name,
                    "id" => $supplier->id
                )
            );
        }

        // a vykreslíme ji
        $template->render();
    }

    public function setSupplierId($supplierId) {
        $this->supplierId = $supplierId;
    }

    public function createComponentNewSupplierForm() {
        $form = new \Nette\Application\UI\Form;
        
        $form->addText('name', 'Název výrobce')
            ->setRequired('Vyplňte název.');
        $form->addHidden("id");
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = array($this, 'onNewsupplierForm');

        return $form;
    }

    public function onNewSupplierForm($form) {
        $values = $form->values;
        
        \SupplierManager::supplier(array(
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