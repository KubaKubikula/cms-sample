<?php
namespace Components;

use Nette\Application\UI\Control;
use Nette\Utils\Paginator;

class VisualPaginator extends \Nette\Application\UI\Control implements \obo\Interfaces\IPaginator
{

    public $userId = null;

    public $itemCount = null;

    public $itemsPerPage = 15;

    /** @var Paginator */
    private $paginator;

    /** @persistent */
    public $page = 1;

    /** @var array */
    public $onShowPage;

    /**
    * @return Nette\Paginator
    */
    public function getPaginator() {
        if (!$this->paginator) {
            $this->paginator = new Paginator;
        }
        return $this->paginator;
    }

    public function handleShowPage($page) {
        // vyvolat události
        $this->onShowPage($this, $page);
    }

    /**
    * Renders paginator.
    * @return void
    */
    public function render() {
        $paginator = $this->getPaginator();
        $paginator->setItemCount($this->itemCount);
        $paginator->setItemsPerPage($this->itemsPerPage);

        $page = $paginator->page;
        if ($paginator->pageCount < 2) {
            $steps = array($page);
        } else {
            $arr = range(max($paginator->firstPage, $page - 3), min($paginator->lastPage, $page + 3));
            $count = 4;
            $quotient = ($paginator->pageCount - 1) / $count;
            for ($i = 0; $i <= $count; $i++) {
                $arr[] = round($quotient * $i) + $paginator->firstPage;
            }
            sort($arr);
            $steps = array_values(array_unique($arr));
        }

        $this->template->itemCount = $this->itemCount;
        $this->template->from = $this->getOffset();

        $this->template->to = $this->getOffset() + $paginator->getLength() -1;
        $this->template->steps = $steps;
        $this->template->paginator = $paginator;
        $this->template->setFile(dirname(__FILE__) . '/Paginator.latte');
        $this->template->ajax = !empty($this->onShowPage);
        $this->template->render();
    }

    /**
    * Loads state informations.
    * @param array
    * @return void
    */
    public function loadState(array $params) {
        parent::loadState($params);
        $this->getPaginator()->page = $this->page;
    }

    /**
     * @param int $itemCount
     * @return void
     */
    public function setItemCount($itemCount) {
        $this->itemCount = $itemCount;
    }
    
    /**
     * @return int
     */
    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }
    
    /**
     * @return int
     */
    public function getOffset() {
        return $this->page * $this->itemsPerPage  - $this->itemsPerPage ;
    }  

}