<?php

class FileManager extends BaseManager
{

    public function saveFile()
    {
        return  $alergen = $this->foodManager->foodRequest("get","ingredients");
    }


}