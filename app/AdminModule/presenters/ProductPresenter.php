<?php
namespace AdminModule;
/**
 * Homepage presenter.
 */
class ProductPresenter extends BasePresenter
{


    public function actionDefault() {
        $this->template->products = \ProductManager::products($this["paginator"], $this["filter"]);   
    }

    public function actionDetail($id) {
        
        $this->template->categories = \CategoryManager::categories();
        $this->template->manufacturers = \ManufacturerManager::manufacturers();
        $this->template->suppliers = \SupplierManager::suppliers();

        $defaults = array();

        $breadcrumbTitle = "";
        $this->template->product = null;
        if($id) {
            $product = $this->template->product = \ProductManager::product($id);
            foreach($this->languages as $lang) {
                $descLang = $product->getDescription($lang->symbol);
                if($descLang){
                    $defaults[$lang->symbol] = array(
                        "name" => $descLang->name,
                        "metaTitle" => $descLang->metaTitle,               
                        "metaKeywords" => $descLang->metaKeywords,
                        "metaDescription" => $descLang->metaDescription,
                        "seoUrl" => $descLang->seoUrl,
                        "shortDescription" => $descLang->shortDescription,
                        "description" => $descLang->description
                    );

                    if(!$breadcrumbTitle) {
                        $breadcrumbTitle = $descLang->name;
                    }
                }
            }

            $catArray = array();
            $categories = $product->getCategories();

            foreach($categories as $cat) {
                $catArray[$cat->category] = $cat->category;
            }

            $defaults["categories"] = $catArray;
            $defaults["suppliers"] = $product->supplier;
            $defaults["manufacturers"] = $product->manufacturer;
            $defaults["count"] = $product->count;
            $defaults["price"] = $product->price;
            $defaults["smallPrice"] = $product->smallPrice;
            $defaults["buyPrice"] = $product->buyPrice;
            $defaults["activeYn"] = $product->activeYn;

            $this["productForm"]->setDefaults($defaults);
        }
        $this->template->title = $breadcrumbTitle;
    }

    public function handleDeleteProduct($id) {
        $product = \ProductManager::product($id);
        $product->delete();

        $this->flashMessage("Produkt byl smazán","success");
        $this->redirect("this");
    }

    public function handleMassCopy($ids) {

        $ids = explode(",", $ids);

        foreach($ids as $id) {
            
            $product = \ProductManager::product($ids);
            
            $productCopy = $product->propertiesAsArray();
            $productCopy["id"] = null;
            
            $newProduct = \ProductManager::product($productCopy)->save();

            $productDescription = $product->getDescription("cz")->propertiesAsArray();
            $productCategories = $product->getCategories();
            $productImages = $product->getImages();

            $productDescription["id"] = null;
            $productDescription["name"] .= " (Kopie)"; 
            $productDescription["product"] = $newProduct->id;
            $newProductDescription = \ProductDescriptionManager::product($productDescription)->save();

            foreach($productCategories as $productCategory) {
                $productCat = $productCategory->propertiesAsArray();
                $productCat["id"] = null;
                $productCat["product"] = $newProduct->id;
                \ProductCategoryManager::productCategory($productCat)->save();
            }

            foreach($productImages as $productImage) {
                $productImg = $productImage->propertiesAsArray();
                $productImg["id"] = null;
                $productImg["product"] = $newProduct->id;
                \ProductImageManager::productImage($productImg);
            }

            
        }

    }

    public function createComponentPaginator() {
        return new \Components\VisualPaginator();
    }

    public function createComponentFilter() {
        $filter = new \Components\Filter();
        $filter->setHeader(
            array(
                array(
                    "name" => "",
                    "type" => "masscheck",
                    "filter" => "name"
                ),
                array(
                    "name" => "Obr."
                ),
                array(
                    "name" => "Název produktu",
                    "filter" => "name",
                    "sort" => "name"
                ),
                array(
                    "name" => "Množství",
                    "filter" => "count",
                    "sort" => "count"
                ),
                array(
                    "name" => "Model",
                    "filter" => "model"
                ),
                array(
                    "name" => "Kategorie",
                    "filter" => "category"
                ),
                array("name" => "Cena","filter" => "price", "sort" => "sort"),
            )
        );


        return $filter;
    }

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentProductForm()
    {   

        $form = new \Nette\Application\UI\Form;
        
        $sex = array(
            '1' => 'Aktivní',
            '0' => 'Neaktivní',
        );

        

        $form->addRadioList('activeYn', '', $sex)
            ->getSeparatorPrototype()->setName(NULL);

        $form->addText("count","Množství skladem");
        
        $form->addText("price","Cena");

        $form->addText("smallPrice","Maloobchodní cena"); 

        $form->addText("buyPrice","Nákupní cena"); 

        $form->addHidden("upload_images");

        $categories = \CategoryDescriptionManager::categoryDescriptionForLanguage($this->language);
        $product_category = array();
        foreach($categories as $category) {
            $product_category[$category->category] = $category->title;  
        }

        $manufacturers = \ManufacturerManager::manufacturers();
        $man = array();
        foreach($manufacturers as $manufacturer) {
            $man[$manufacturer->id] = $manufacturer->name;  
        }

        $suppliers = \SupplierManager::suppliers();
        $sup = array();
        foreach($suppliers as $supplier) {
            $sup[$supplier->id] = $supplier->name;  
        }

        $form->addMultiSelect('categories', 'Kategorie', $product_category);
        $form->addMultiSelect('manufacturers', 'Výrobci', $man);
        $form->addMultiSelect('suppliers', 'Dodavatelé', $sup);

        foreach($this->languages as $lang) {
            
            $container = $form->addContainer($lang->symbol);

            $container->addText('name', 'Název produktu: (h1)');
            /*->setRequired('Vyplňte Název produktu');*/
            $container->addText('metaTitle', 'Meta Tag Title');
            $container->addText('metaKeywords', 'Meta Tag Keywords');
            $container->addText('metaDescription', 'Meta Tag Description');
            $container->addText('seoUrl', 'SEO adresa');
            $container->addTextArea('shortDescription', 'Shrnutí');
            $container->addTextArea('description', 'Popis');

        }  

        /*$form->addCheckbox('remember', 'Keep me signed in');*/
        $form->addSubmit('send', 'Uložit Změny');

        // call method signInFormSucceeded() on success
        $form->onSuccess[] = $this->saveProduct;

        return $form;
    }

    public function handleUploadImage() {       
        
        require( dirname(__FILE__). "/../components/UploadHandler.php" );
        $upload_handler = new \UploadHandler();
        die;   
    }


    public function saveProduct($form)
    {
        $values = $form->getValues();

        $id = $this->getParameter("id");

        $manufacturer = null;
        if(count($values["manufacturers"])) {
            $manufacturer = $values["manufacturers"][0];
        }

        $supplier = null;
        if(count($values["suppliers"])) {
            $supplier = $values["suppliers"][0];
        }

        $product = \ProductManager::product(array(
            "id" => $id,
            "price" => $values["price"],
            "count" => $values["count"],
            "smallPrice" => $values["smallPrice"],
            "buyPrice" => $values["buyPrice"],
            "supplier" => $supplier,
            "manufacturer" => $manufacturer
            
        ))->save();


        if($values["upload_images"]) {
            $upload_images = trim($values["upload_images"], ";" );

            $upload_images = explode(";",$upload_images);

            foreach($upload_images as $image2) {
                $image = \ImageManager::image(array(
                    "source" => $image2,
                ))->save();

                \ProductImageManager::productImage(array(
                    "product" => $product->id,
                    "image" => $image->id,
                ))->save();
            }
        }

        foreach($values["categories"] as $cat) {
            $productCategory = \ProductCategoryManager::productCategoryForProduct( $product->id , $cat);

            if(!$productCategory) {
                \ProductCategoryManager::productCategory(array(
                        "product" => $product->id,
                        "category" => $cat
                    )
                )->save();
            } 
        }

        foreach($this->languages as $lang) {
            $langData = $values[$lang->symbol];

            $exist = false;

            foreach($langData as $item) {
                if($item){
                    $exist = true;
                }
            }

            if($exist) {
                try {
                    $description = \ProductDescriptionManager::productDescriptionForProduct($lang->symbol , $product->id);

                    if($description) {
                        $langData["id"] = $description->id;
                    }

                } catch(\Exception $e) {
                    $langData["product"] = $product->id;
                    $langData["language"] = $lang->symbol;
                }

                \ProductDescriptionManager::product($langData)->save();
            }


        }
        
        $this->flashMessage("Produkt byl uložen","success");
        $this->redirect("detail",array("id" => $product->id));
        
    }

}
