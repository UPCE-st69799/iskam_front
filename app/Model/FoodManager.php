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




}