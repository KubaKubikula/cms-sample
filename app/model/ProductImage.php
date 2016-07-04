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

class ProductImageProperties extends \Base\Properties{
    public $id = null;
    public $product = null;
    public $image = null;
    public $mainYn = null;
    public $sort = null;
    public $dateTimeInserted = null;
}

# definition entity

/**
 * @repositoryName(ProductImage)
 * @property string $product
 * @property string $image
 * @property string $mainYn
 * @property string $sort
 * @property string $dateTimeInserted
 */
class ProductImage extends \Base\Entity{

        public function getSource() {

            if(!$this->id) {
                return null;
            }
            try{
                return ImageManager::imageForProductImage($this->image);
            } catch(\Exception $e){
                return null;
            }
        } 
}

# definition entity manager

class ProductImageManager extends \Base\Manager{
        
    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productImage($specification = null){
        # vrati jednu entitu
        return self::entity($specification);
    }
    
    /**
     * @param iPaginator $paginatorComponet
     * @param iFilter $filterComponent
     * @return \Users\User[]
     */
    public static function productImages(\obo\Interfaces\IPaginator $paginator = null, \obo\Interfaces\IFilter $filter = null){
        #najde entity v repositari dle danych specifikaci, lze pridat strankovadlo a filter
        return self::findEntities(\obo\Carriers\QueryCarrier::instance(), $paginator, $filter);
        //return self::findEntities(new \obo\Carriers\QueryCarrier(), $paginator, $filter);
    }

    public static function imagesForProduct($product) {
        #najde entity v repositari dle danych specifikaci, lze pridat strankovadlo a filter
        return self::findEntities(\obo\Carriers\QueryCarrier::instance()->where("AND [product] = %i", $product));
        //return self::findEntities(new \obo\Carriers\QueryCarrier(), $paginator, $filter);
    }

    public static function imageForProduct($product) {
        #najde entity v repositari dle danych specifikaci, lze pridat strankovadlo a filter
        return self::findEntity(\obo\Carriers\QueryCarrier::instance()->where("AND [product] = %i", $product));
        //return self::findEntities(new \obo\Carriers\QueryCarrier(), $paginator, $filter);
    }
    
    /**
     * @param \Nette\Forms\Form $form
     * @return \Nette\Forms\For|\Users\User 
     */
    public static function newProductImage(\Nette\Forms\Form $form){
        return self::newEntityFromForm(\Users\User::constructForm($form));
    }
    
    /**
     * @param \Nette\Forms\Form $form
     * @param \Users\User $user
     * @return \Nette\Forms\For|\Users\User
     */
    public static function editProductImage(\Nette\Forms\Form $form, \Users\User $user = null){
        return self::editEntityFromForm(\Users\User::constructForm($form), $user);
    }
        
}