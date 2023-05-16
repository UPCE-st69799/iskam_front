<?php

namespace App\Presenters;

use \Nette\Forms\Controls;
use \Nette\Caching\Cache;
use \Defuse\Crypto\Crypto;
use Latte\Runtime\FilterInfo;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{


    protected $api;

    function setApi($api)
    {
        $this->api = $api;
    }


    public function startup()
    {

    }


    public function beforeRender(): void
    {


    }

}