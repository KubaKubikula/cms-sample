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

class ProductCategoryProperties extends \Base\Properties{
    public $id = 0;
    public $product = null;
    public $category = null ;
    public $primaryYn = 0;
    
}

# definition entity

/**
 * @repositoryName(ProductCategory)
 * @property string $product
 * @property string $category
 * @property string $primaryYn
 */
class ProductCategory extends \Base\Entity{

    public function getProductEntity() {

        
        if(!$this->product) {
            return null;
        }
        try{
            return \ProductManager::product($this->product);
        } catch(\Exception $e){
            return null;
        }
    }
        
}

# definition entity manager

class ProductCategoryManager extends \Base\Manager{
        
    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productCategory($specification = null){
        # vrati jednu entitu
        return self::entity($specification);
    }
    
    /**
     * @param iPaginator $paginatorComponet
     * @param iFilter $filterComponent
     * @return \Users\User[]
     */
    public static function productCategories(\obo\Interfaces\IPaginator $paginator = null, \obo\Interfaces\IFilter $filter = null){
        #najde entity v repositari dle danych specifikaci, lze pridat strankovadlo a filter
        return self::findEntities(\obo\Carriers\QueryCarrier::instance(), $paginator, $filter);
        //return self::findEntities(new \obo\Carriers\QueryCarrier(), $paginator, $filter);
    }

    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productCategoriesForProduct($product){
        # vrati jednu entitu
        return self::findEntities(\obo\Carriers\QueryCarrier::instance()->where("AND [product] = %i",$product));
    }

    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productMainCategoryForProduct($product){
        # vrati jednu entitu
        return self::findEntity(\obo\Carriers\QueryCarrier::instance()->where("AND [product] = %i",$product));
    }

    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productCategoriesForCategory($category, $sort = "[id] DESC", $limit = "1,15"){
        # vrati jednu entitu
        return self::findEntities(
            \obo\Carriers\QueryCarrier::instance()
            ->join("INNER JOIN [Product] ON [Product].[id] = [ProductCategory].[product] ")
            ->where("AND [category] = %i",$category)            
            ->orderBy($sort)
            ->limit($limit)
        );
    }

    /**
     * @param int|array|null $specification
     * @return \Users\User  
     */
    public static function productCategoryForProduct($product, $category) {
        # vrati jednu entitu
        try{
            return self::findEntity(\obo\Carriers\QueryCarrier::instance()->where("AND [product] = %i AND [category] = %i", $product, $category));
        } catch(\Exception $e) {
            return null;
        }   
    }
    
    /**
     * @param \Nette\Forms\Form $form
     * @return \Nette\Forms\For|\Users\User 
     */
    public static function newProductCategory(\Nette\Forms\Form $form){
        return self::newEntityFromForm(\Users\User::constructForm($form));
    }
    
    /**
     * @param \Nette\Forms\Form $form
     * @param \Users\User $user
     * @return \Nette\Forms\For|\Users\User
     */
    public static function editProductCategory(\Nette\Forms\Form $form, \Users\User $user = null){
        return self::editEntityFromForm(\Users\User::constructForm($form), $user);
    }
        
}