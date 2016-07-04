<?php
namespace Components;

class Filter extends \Nette\Application\UI\Control implements \obo\Interfaces\IFilter
{

    /** @persistent */
    public $data = array();

    private $header = array();

    /** @persistent */
    public $sort = "";

    /** @persistent */
    public $way;
    /**
    * Renders filter.
    * @return void
    */
    public function render() {
        
        $this->template->setFile(dirname(__FILE__) . '/Filter.latte');
        $this->template->header = $this->header;
        $this->template->hasFilter = false;
        $this->template->sort = $this->sort;
        $this->template->way = $this->way;
        $this->template->data = $this->data;


        foreach($this->header as $headerItem) {
            if(is_array($headerItem) AND isset($headerItem["filter"])) {
                $this->template->hasFilter = true;
            }
        }

        $this->template->render();
    }

    public function setHeader($header){
        $this->header = $header;
    }

    public function handleSort($name , $way) {
        
        $this->sort = $name;
        $this->way = $way;
    }

    public function handleFilter() {

    }

    public function getJoin() {
        if(count($this->data)) {
            return "INNER JOIN [ProductDescription] [PD] ON [Product].[id] = [PD].[product] ";
        }
    }
    /**
     * @return array
     */
    public function getWhere() {
        if(count($this->presenter->request->post)){
            $this->data = $this->presenter->request->post;
        }

        $where = "";
        
        if(isset($this->data["name"])) {
            $where = "AND [PD].[name] LIKE '%".$this->data["name"]."%'";
        }

        if(isset($this->data["count"])) {
            $where .= "AND [Product].[count] = '".$this->data["count"]."'";
        }

        if($where) {
            return $where;
        }
    }
    
    /**
     * @return array
     */
    public function getOrderBy() {
        
        if(isset($this->presenter->request->post["sort"])) {
            $this->sort = $this->presenter->request->post["sort"];
        }

        if(isset($this->presenter->request->post["sort"]) AND isset($this->sort) AND $this->presenter->request->post["sort"] == $this->sort) {
            if(isset($this->presenter->request->post["way"]) AND $this->presenter->request->post["way"] == "ASC") {
                $this->way = "DESC";
            } else {
                $this->way = "ASC";
            }
        }
        
        if(isset($this->sort) AND $this->sort) {
            return $this->sort ." ". $this->way;
        }
    }

}