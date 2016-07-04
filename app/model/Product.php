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

class ProductProperties extends \Base\Properties{
    public $id = 0;
    public $activeYn = 0;
    public $recomendYn = 0;
    public $name = "";
    public $shortDescription = "";
    public $description = "";
    public $count = 1;
    public $metaTitle = "";
    public $metaDescription = "";
    public $metaKeywords = "";
    public $price = null;
    public $smallPrice = null;
    public $buyPrice = null;
    public $vat = null;
    public $decreaseStockYn = 0;
    public $ProductAvailability = 0;
    public $manufacturer = null;
    public $supplier = null;
    public $model = null;
    public $sku = null;
    public $ean = null;
    public $dateTimeInserted = null;
    public $dateTimeUpdated	= null;
    public $language = null;
}

# definition entity

/**
 * @repositoryName(Product)
 * @property string $activeYn
 * @property string $Name
 * @property string $shortDescription
 * @property string $Description
 * @property string $count
 * @property string $metaTitle
 * @property string $metaDescription
 * @property string $metaKeywords
 * @property string $price
 * @property string $vat
 * @property string $decreaseStockYn
 * @property string $ProductAvailability
 * @property string $manufacturer
 * @property string $supplier
 * @property string $model
 * @property string $sku
 * @property string $ean
 * @property string $dateTimeInserted
 * @property string $dateTimeUpdated
 */

class Product extends \Base\Entity{

    public function getPrice() {
        return $this->price . " Kč";
    }

    public function getManufacturer() {
        try{
        return \ManufacturerManager::manufacturer($this->manufacturer);
        }catch(\Exception $e){
            return null;
        }
    }

    public function getAvailability() {
        if($this->count > 0) {
            return "Skladem";
        } else {
            return "Není skladem";
        }
    }


    public function getDescription($language) {

        if(!$language || !$this->id) {
            return null;
        }
        try{
            return ProductDescriptionManager::productDescriptionForProduct($language, $this->id);
        } catch(\Exception $e){
            return null;
        }
    }

    public function getCategories() {

        if(!$this->id) {
            return null;
        }
        try{
            return ProductCategoryManager::productCategoriesForProduct($this->id);
        } catch(\Exception $e){
            return null;
        }
    }

    public function getMainCategory() {

        if(!$this->id) {
            return null;
        }
        try{
            $category =  ProductCategoryManager::productMainCategoryForProduct($this->id);

            return $category;

        } catch(\Exception $e){
            return null;
        }
    }
    
    public function getImages() {

        if(!$this->id) {
            return null;
        }
        try{
            return ProductImageManager::imagesForProduct($this->id);
        } catch(\Exception $e){
            return null;
        }
    }  

    public function getMainImage(){
       if(!$this->id) {
            return null;
        }
        try{
            return ProductImageManager::imageForProduct($this->id);
        } catch(\Exception $e){
            return null;
        } 
    }


}

# definition entity manager

class ProductManager extends \Base\Manager{
        
    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function product($specification = null){
        # vrati jednu entitu
        return self::entity($specification);
    }
    
    /**
     * @param iPaginator $paginatorComponet
     * @param iFilter $filterComponent
     * @return \Users\User[]
     */
    public static function products(\obo\Interfaces\IPaginator $paginator = null, \obo\Interfaces\IFilter $filter = null){
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