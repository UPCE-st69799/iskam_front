<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Presenters\templates\security\MyAuthenticator;
use Nette;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;


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

        $alergen = \Httpful\Request::get('http://localhost:9000/api/v1/ingredients')
            ->addHeader('Authorization',"Bearer ".$this->user->id)
            ->send();
        $classis=[] ;
        $pocetAlergenu = count($alergen->body);
        for ($i = 0; $i < $pocetAlergenu / 6; $i++) {
            $classis[] = "bg-primary";
            $classis[] = "bg-secondary";
            $classis[] = "bg-success";
            $classis[] = "bg-danger";
            $classis[] = "bg-warning text-dark";
            $classis[] = "bg-info";
        }


        $this->template->foods = $request->body;
        $this->template->alergens = $alergen->body;
        $this->template->spanClass = $classis;
        $this->template->count = 0;
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

    protected function createComponentAddFoodForm(): Form
    {
        $request = \Httpful\Request::get('http://localhost:9000/api/v1/appCategory')
            ->addHeader('Authorization',"Bearer ".$this->user->id)
            ->send();

        $categories = [];

        if(!$request->hasErrors()){
            foreach ($request->body as $category){
                $categories[$category->id] = $category->name;
        }

}



        $form = new Form();
        $form->addText('itemName', 'Item Name')
            ->setRequired();
        $form->addTextArea('itemDescription', 'Item Description')
            ->setRequired();
        $form->addText('itemPrice', 'Price')
            ->setRequired()
            ->addRule(Form::FLOAT, 'Zadejte číslo')
            ->addRule(Form::MIN, 'Price must be greater than 0', 0);
        $form->addSelect('itemCategory', 'Category ID',$categories)
            ->setRequired();
        $form->addUpload('itemImage', 'Image File')
            ->setRequired()
            ->addRule(Form::MIME_TYPE, 'Please upload an image', ['image/jpeg', 'image/png']);
        $form->addText('itemIngredients', 'Ingredients (separated by commas)');

        $form->addHidden('itemIngredients_Id', '');

        $form->addSubmit('submit', 'Submit');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    public function processForm(Form $form, array $values): void
    {


        if ($form->isSuccess()) {

            /** @var FileUpload $fileUpload */
            $fileUpload = $values['itemImage'];

            if ($fileUpload->isOk() && $fileUpload->isImage()) {

                if($this->saveFile($fileUpload)){

                    $parser = !empty($values['itemIngredients_Id'])? explode(';', substr_replace($values['itemIngredients_Id'] ,"", -1)) : array();


                    $body = [];
                    $body['name'] = $values['itemName'];
                    $body['description'] = $values['itemDescription'];
                    $body['price'] = $values['itemPrice'];
                    $body['categoryId'] = $values["itemCategory"];
                    $body['image'] = $fileUpload->name;
                    $body['ingredients'] = $parser;



                    $request = \Httpful\Request::post('http://localhost:9000/api/v1/appFood')
                        ->addHeader('Authorization',"Bearer ".$this->user->id)
                        ->sendsJson()
                        ->body(json_encode($body))
                        ->send();

                    $this->flashMessage($request->body);
                }else{
                    $this->flashMessage("soubor se nepodařilo uložit");
                }

            }
        }

        $request = \Httpful\Request::get('http://localhost:9000/api/v1/appFood')
            ->expectsJson()
            ->send();

        $this->template->foods = $request->body;
        $this->redrawControl("foods");
    }



    public function saveFile(FileUpload $file): bool
    {
        $uploadPath = $this->getUploadPath();

        $fileName = $file->name;
        $file->move($uploadPath . '/' . $fileName);

        return file_exists($uploadPath."/".$fileName);
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
    private function getUploadPath(): string
    {
        return __DIR__ . '/../../www/uploads';
    }

    public function handleDelete($id)
    {
        $request = \Httpful\Request::delete('http://localhost:9000/api/v1/appFood/'.$id)
            ->expectsJson()
            ->send();
        $this->flashMessage($request->body);

        $this->redrawControl("foods");
        $this->payload->postGet = true;
        $this->payload->url = $this->link('this');
    }
}
