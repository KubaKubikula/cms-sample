<?php
namespace Components\Tag;

class Popup extends \Nette\Application\UI\Control
{
    private $show = false;

    public $tagId = null;

    public function render()
    {
        
        $template = $this->template;
        $template->setFile(__DIR__ . '/Popup.latte');
        
        $template->show = $this->getShow();

        if($this->tagId) {

            $tag = \TagManager::tag($this->tagId);

            $this["newtagForm"]->setDefaults(
                array(
                    "name" => $tag->name,
                    "id" => $tag->id
                )
            );
        }

        // a vykreslíme ji
        $template->render();
    }

    public function settagId($tagId) {
        $this->tagId = $tagId;
    }

    public function createComponentNewtagForm() {
        $form = new \Nette\Application\UI\Form;
        
        $form->addText('name', 'Název výrobce')
            ->setRequired('Vyplňte název.');
        $form->addHidden("id");
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = array($this, 'onNewtagForm');

        return $form;
    }

    public function onNewtagForm($form) {
        $values = $form->values;
        \tagManager::tag(array(
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