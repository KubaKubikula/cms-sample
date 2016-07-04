<?php
namespace Components;

class CustomerFilter extends \Components\Filter implements \obo\Interfaces\IFilter
{

    

    public function getJoin() {
       
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
            $where = "AND [surname] LIKE '%".$this->data["name"]."%'";
        }

        if(isset($this->data["email"])) {
            $where = "AND [email] LIKE '%".$this->data["email"]."%'";
        }

        if(isset($this->data["phone"])) {
            $where = "AND [phone] LIKE '%".$this->data["phone"]."%'";
        }

        if($where) {
            return $where;
        }
    }

}