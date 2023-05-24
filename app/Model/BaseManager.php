<?php

declare(strict_types=1);

use \Nette\Caching\Storage;
use Nette\Database\Explorer;
use Nette\Security\User;

class BaseManager
{

    protected $api;
    protected $user;

    public function __construct(array $params,User $user)
    {
        $this->api = $params['api']['url'];
        $this->user = $user;
    }


    final function foodRequest($method, $url, $body = null)
    {
        $response = \Httpful\Request::$method($this->api . $url)
            ->sendsJson()
            ->authenticateWith('Authorization',"Bearer ".$this->user->id);

        switch ($method) {
            case 'post':
            case 'put':
                $response->body($body);
                break;

            default:
                break;
        }

        $response = $response->send();

        if ($response->hasErrors()) {
            $presenter = $this->getPresenter();
            $presenter->flashMessage('Chyba při zpracování požadavku.', 'error');

        }
        return $response;
    }


}