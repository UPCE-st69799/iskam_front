<?php

class FoodManager extends BaseManager
{

    public function getAlergens()
    {
        return  $this->foodRequest("get","ingredients");
    }

    public function createFood($body)
    {
        return  $this->foodRequest("post","appFood",json_encode($body));
    }

    public function editFood($body, $id)
    {
        return  $this->foodRequest("put","appFood/".$id,json_encode($body));
    }


    public function getFoodById($id)
    {
        return  $this->foodRequest("get","appFood/".$id);
    }


    public function deleteFoodById($id)
    {
        return  $this->foodRequest("delete","appFood/".$id);
    }

    public function getDataWithFilter($body, $page)
    {
        return $this->foodRequest("post",'appFood/query?size=6&page=' . $page - 1,empty($body) ? "{}" : json_encode($body));
    }

    public function getDataWithoutFilter()
    {
        return $this->foodRequest("post",'appFood/query',"{}");
    }

    public function getAllCategory(){
        return $this->foodRequest("get","appCategory");
    }

}