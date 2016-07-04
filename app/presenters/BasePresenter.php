<?php

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public $db = null;
    public $cache ;
    public $mailer;

    public function startup() {

        parent::startup();

        $options = array(
            'driver'   => 'mysql',
            'host'     => 'localhost',
            'username' => 'root',
            'password' => 'root',
            'database' => 'someDatabase',
        );

        $this->db = new \DibiConnection($options);

        $this->mailer = new \Nette\Mail\SmtpMailer(array(
                'host' => 'smtp.seznam.cz',
                'username' => 'someemail@seznam.cz',
                'password' => 'somepassword',
        ));
        
    }

    public function getCategories($parent) {
        $categories = $this->db->fetchAll("
            SELECT C.[id],CD.[title] FROM [category] C 
            INNER JOIN [categoryDescription] CD ON CD.category = C.id AND CD.[language] = 'cz'
            WHERE C.[parent] = %i
        ", $parent);
        
        $cat = array();
        foreach($categories as $category) {
            
            $cat[$category->id] = array(
                "id" => $category->id,
                "title" => $category->title,
                "childs" => $this->getCategories($category->id)
            );

        }

        return $cat;
    }

}
