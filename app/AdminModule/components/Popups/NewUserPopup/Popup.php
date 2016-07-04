<?php
namespace Components\NewUser;

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

    public function createComponentNewUserForm() {
        $form = new \Nette\Application\UI\Form;
        
        $form->addText('name', 'Jméno:')
            ->setRequired('Vyplňte jméno.');
            
        $newPass = $form->addPassword('newPassword', 'Heslo:')
            ->setRequired('Vyplňte heslo.');
            
        $form->addPassword('newPasswordAgain', 'Heslo znovu:')
            ->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
            ->addRule(\Nette\Application\UI\Form::EQUAL, 'Hesla se neshodují', $newPass );
            
        $form->addSubmit('save', 'Uložit');

        $form->onSuccess[] = array($this, 'onNewUserForm');

        return $form;
    }

    public function onNewUserForm($form) {
        $values = $form->values;

        if($values["newPassword"] AND $values["newPasswordAgain"]) {
            if($values["newPassword"] != $values["newPasswordAgain"]) {

                $this->presenter->flashMessage("Hesla se neshodují","error");

            } else {

                $authenticator = $this->presenter->user->authenticator;
                $password = $authenticator::calculateHash($values["newPassword"]);

                 \UsersManager::user(array(
                        "name" => $values["name"],
                        "password" => $password
                    )
                )->save();

                 $this->presenter->flashMessage("Uživatel byl vložen","success");
                 $this->presenter->redirect("this");

            }
        }
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