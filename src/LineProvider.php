<?php

namespace Socialite\Provider;

use Socialite\Two\AbstractProvider;
use Socialite\Two\User;

class LineProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['profile','openid','email'];
    
    /**
     * {@inheritdoc}
     */
    protected $id_token = '';


    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(string $state)
    {
        return $this->buildAuthUrlFromBase(
            'https://access.line.me/oauth2/v2.1/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.line.me/oauth2/v2.1/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(string $token)
    {
    
        $response = $this->getHttpClient()->post(
            'https://api.line.me/oauth2/v2.1/verify', [
            'headers' => ['Accept' => 'application/json'],
            'form_params' => ['id_token'=>$this->id_token, 'client_id' => $this->clientId],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['sub'],
            'nickname' => null,
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['picture'] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields(string $code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAccessTokenResponse(string $code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json','Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => $this->getTokenFields($code),
        ]);
        $array=json_decode($response->getBody(), true);
        $this->id_token = $array['id_token'] ?? null;
        return $array;
    }
}
