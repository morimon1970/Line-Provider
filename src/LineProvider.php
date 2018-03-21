<?php

namespace Socialite\Provider;

use Socialite\Two\AbstractProvider;
use Socialite\Two\User;

class LineProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(string $state)
    {
        return $this->buildAuthUrlFromBase(
            'https://access.line.me/dialog/oauth/weblogin', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://api.line.me/v2/oauth/accessToken';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken(string $token)
    {
        $response = $this->getHttpClient()->get(
            'https://api.line.me/v2/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['userId'],
            'nickname' => null,
            'name' => $user['displayName'],
            'email' => null,
            'avatar' => $user['pictureUrl'] ?? null,
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
}
