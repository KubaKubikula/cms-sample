<?php
namespace Components;

class Popup extends \Nette\Application\UI\Control
{
    private $show = false;

    public $userId = null;

    public function render()
    {
        
        $template = $this->template;
        $template->setFile(__DIR__ . '/Popup.latte');
        
        $template->show = $this->getShow();
        if($this->userId){
            $user = \UsersManager::user($this->userId);

            $this["editUserForm"]->setDefaults(
                array(
                    "name" => $user->name,
                    "id" => $this->userId
                )
            );
        }

        // a vykreslíme ji
        $template->render();
    }

    public function createComponentEditUserForm() {
        
        $form = new \Nette\Application\UI\Form;
        $form->addHidden("id");
        $form->addText('name', 'Jméno:');
        $form->addPassword('newPassword', 'Heslo:');
        $form->addPassword('newPasswordAgain', 'Heslo znovu:');           
        $form->addSubmit('save', 'Uložit');
        $form->onSuccess[] = array($this, 'onEditUserForm');

        return $form;
    }

    public function onEditUserForm($form) {
        $values = $form->values;

        if($values["newPassword"] AND $values["newPasswordAgain"]) {
            if($values["newPassword"] != $values["newPasswordAgain"]) {

                $this->presenter->flashMessage("Hesla se neshodují","error");

            } else {

                $authenticator = $this->presenter->user->authenticator;
                $password = $authenticator::calculateHash($values["newPassword"]);

                \UsersManager::user(array(
                        "id" => $values["id"],
                        "password" => $password
                    )
                )->save();

                $this->presenter->flashMessage("Heslo bylo změněno", "success");
            }
        }

        \UsersManager::user(array(
                "id" => $values["id"],
                "name" => $values["name"]
            )
        )->save();
        
        $this->presenter->flashMessage("Uživatel byl editován","success");
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