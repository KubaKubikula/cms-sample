<?php

/** 
 * This file is part of demo application for example of using framework Obo beta 2 version (http://www.obophp.org/)
 * Created under supervision of company as CreatApps (http://www.creatapps.cz/)
 * @link http://www.obophp.org/
 * @author Adam Suba, http://www.adamsuba.cz/
 * @copyright (c) 2011, 2012 Adam Suba
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */


# A class defining the entity is usually better to split into its own file. Here are clarity placed in one file

# definition of properties

class ProductDescriptionProperties extends \Base\Properties{
    public $id = 0;
    public $name = "";
    public $language = "";
    public $product = "";
    public $shortDescription = "";
    public $description = "";
    public $metaTitle = "";
    public $metaDescription = "";
    public $metaKeywords = "";
    public $seoUrl = "";
    /** @timeStamp(beforeInsert)*/
    public $dateTimeInserted = "";
    /** @timeStamp(beforeUpdate) */
    public $dateTimeUpdated = "";
}

# definition entity

/**
 * @repositoryName(ProductDescription)
 * @property string $name
 * @property string $language
 * @property string $shortDescription
 * @property string $description
 * @property string $metaTitle
 * @property string $metaDescription
 * @property string $metaKeywords
 * @property string $dateTimeInserted
 * @property string $dateTimeUpdated
 */
class ProductDescription extends \Base\Entity{

        
}

# definition entity manager

class ProductDescriptionManager extends \Base\Manager{
        
    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function product($specification = null){
        # vrati jednu entitu
        return self::entity($specification);
    }

    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productDescriptionForProduct($language, $product){
        # vrati jednu entitu
        return self::findEntity(\obo\Carriers\QueryCarrier::instance()->where("AND [language] = %s AND [product] = %i", $language, $product));
    }
    
    /**
     * @param iPaginator $paginatorComponet
     * @param iFilter $filterComponent
     * @return \Users\User[]
     */
    public static function products($language, \obo\Interfaces\IPaginator $paginator = null, \obo\Interfaces\IFilter $filter = null){
        #najde entity v repositari dle danych specifikaci, lze pridat strankovadlo a filter
        return self::findEntities(\obo\Carriers\QueryCarrier::instance(), $paginator, $filter);
        //return self::findEntities(new \obo\Carriers\QueryCarrier(), $paginator, $filter);
    }
    
    /**
     * @param \Nette\Forms\Form $form
     * @return \Nette\Forms\For|\Users\User 
     */
    public static function newProduct(\Nette\Forms\Form $form){
        return self::newEntityFromForm(\Users\User::constructForm($form));
    }
    
    /**
     * @param \Nette\Forms\Form $form
     * @param \Users\User $user
     * @return \Nette\Forms\For|\Users\User
     */
    public static function editProduct(\Nette\Forms\Form $form, \Users\User $user = null){
        return self::editEntityFromForm(\Users\User::constructForm($form), $user);
    }
        
}