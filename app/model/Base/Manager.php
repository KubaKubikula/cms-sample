<?php

/** 
 * This file is part of demo application for example of using framework Obo beta 2 version (http://www.obophp.org/)
 * Created under supervision of company as CreatApps (http://www.creatapps.cz/)
 * @link http://www.obophp.org/
 * @author Adam Suba, http://www.adamsuba.cz/
 * @copyright (c) 2011, 2012 Adam Suba
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Base;

/**
 * Base entity manager class for my improvements
 */
class Manager extends \obo\EntityManager{
    
    /**
     * Universal method for creating and processing data from the form
     * @param \Nette\Forms\Form $form
     * @return \Nette\Forms\Form 
     */
    public static function newEntityFromForm(\Nette\Forms\Form $form){
        $form->addSubmit("add", "Insert");
        
        $form->setDefaults(self::entity()->propertiesAsArray($form->values));
        $primaryPropertyName = self::entity()->entityInformation()->primaryPropertyName;
        $form[$primaryPropertyName]->setValue(self::entity()->$primaryPropertyName);
        
        if ($form->isSuccess()) return self::saveEntityFromForm($form);
        
        return $form;
    }
    
    /**
     * Universal method for creating and processing data from the form
     * @param \Nette\Forms\Form $form
     * @param \Base\Entity $entity
     * @return \Nette\Forms\Form 
     */
    public static function editEntityFromForm(\Nette\Forms\Form $form, \Base\Entity $entity = null){
        $form->addSubmit("save", "Save");
        
        if(!\is_null($entity)){
            $form->setDefaults($entity->propertiesAsArray($form->values));
            $primaryPropertyName = $entity->entityInformation()->primaryPropertyName;
            $form[$primaryPropertyName]->setValue($entity->$primaryPropertyName);
        }
        
        if ($form->isSuccess()) return self::saveEntityFromForm($form);
        
        return $form;
    }
    
    /**
     * Universal method to retrieve entities from the form data and store it
     * @param \Nette\Forms\Form $form
     * @return \Base\Entity 
     */
    private static function saveEntityFromForm(\Nette\Forms\Form $form){
        return self::entity($form->values)->save();
    }
}