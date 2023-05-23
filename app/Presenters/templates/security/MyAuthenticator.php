<?php

namespace App\Presenters\templates\security;

use Nette\Security\Authenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\SimpleIdentity;

class MyAuthenticator implements Authenticator
{

    public function authenticate(string $username, string $password): SimpleIdentity
    {
        $request = \Httpful\Request::post("http://localhost:9000/api/v1/login");
        $request->addHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        $data = [
            'username' => $username,
            'password' => $password,
        ];

        $request->body(http_build_query($data));
        $response = $request->send();

        if ($response->hasErrors()) {
            throw new AuthenticationException('User not found.');
        }






        $tokenParts = explode('.', $response->body->access_token);

        $payload = base64_decode($tokenParts[1]);

        $decodedPayload = json_decode($payload, true);

        $roles = ($decodedPayload["roles"]);



        return new SimpleIdentity($response->body->access_token,$roles,['name' => $username,'token'=>$response->body->access_token]);
    }

}
