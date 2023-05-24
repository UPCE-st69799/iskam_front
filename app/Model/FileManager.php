<?php

class FileManager extends BaseManager
{

    public function saveFile()
    {
        return  $alergen = $this->foodRequest("get","ingredients");
    }


}