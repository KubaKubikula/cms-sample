<?php
namespace Components\NewSubcategory;

class Popup extends \Nette\Application\UI\Control
{
    private $show = false;

    public $categoryId = null;

    public function render()
    {
        
        $template = $this->template;
        $template->setFile(__DIR__ . '/Popup.latte');
        
        $template->show = $this->getShow();

        if($this->categoryId) {
            $this["newSubcategoryForm"]->setDefaults(
                array(
                    "categoryId" => $this->categoryId,
                )
            );
        }
        // a vykreslíme ji
        $template->render();
    }

    public function createComponentNewSubcategoryForm() {
        $form = new \Nette\Application\UI\Form;
        
        $form->addText('name', 'Název subkategorie:')
            ->setRequired('Vyplňte název.');

        $form->addHidden("categoryId");    
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = array($this, 'onNewSubcategoryForm');

        return $form;
    }

    public function onNewSubcategoryForm($form) {
        $values = $form->values;

        $category = \CategoryManager::category(array(
            "activeYn" => false,
            "parent" => $values["categoryId"]
        ))->save();

        \CategoryDescriptionManager::category(array(
            "language" => DEFAULT_LANGUAGE,
            "title" => $values["name"],
            "category" => $category
        ))->save();

        $this->presenter->flashMessage("Kategorie bylo vložena","success");
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