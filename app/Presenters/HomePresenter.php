<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Presenters\templates\security\MyAuthenticator;
use Nette;
use Nette\Application\UI\Form;


final class HomePresenter extends Nette\Application\UI\Presenter
{

    private $authenticator;

    public function __construct(MyAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function renderDefault():void{


        $request = \Httpful\Request::get('http://localhost:9000/api/v1/appFood')
            ->expectsJson()
            ->send();



        $this->template->food = $request->body;
}
    protected function createComponentLoginForm(): Form
    {
        $form = new Form();

        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Zadejte prosím uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Zadejte prosím heslo.');

        $form->addSubmit('login', 'Přihlásit se');

        $form->onSuccess[] = function (Form $form, array $values) {
            $this->actionLogin($values['username'], $values['password']);
        };

        return $form;
    }

    public function actionLogin(string $username, string $password): void
    {
        try {
            $identity = $this->authenticator->authenticate($username, $password);
            $this->getUser()->login($identity);
            $this->redirect('this'); // nebo jiná stránka po úspěšném přihlášení
        } catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage('Neplatné přihlašovací údaje.', 'error');
            $this->redirect('this');
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->redirect('home:default');
    }


}
