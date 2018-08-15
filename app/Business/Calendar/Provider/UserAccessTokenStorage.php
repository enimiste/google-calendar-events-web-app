<?php

namespace App\Business\Calendar\Provider;

use App\User;

class UserAccessTokenStorage implements AccessTokenStorageInterface {

    /**
     * @param User $user
     * @param int $provider see EventStoreFactory::GOOGLE
     */
    function get(User $user, int $provider): array {
        $provider = $this->getProviderKey($provider);
        return array_get($user->event_providers_access, $provider, []);
    }

    /**
     * @param User $user
     * @param int $provider
     * @param array $access
     */
    function save(User $user, int $provider, array $access) {
        $provider = $this->getProviderKey($provider);
        $oldAccess = $user->event_providers_access;

        array_set($oldAccess, $provider, $access);
        $this->saveAccessOnUser($user, $oldAccess);
    }

    /**
     * @param User $user
     * @param int $provider see EventStoreFactory::GOOGLE
     */
    function forget(User $user, int $provider) {
        $provider = $this->getProviderKey($provider);
        $oldAccess = $user->event_providers_access;

        if(array_has($oldAccess, $provider)){
            $newAccess = array_forget($oldAccess, $provider);
            $this->saveAccessOnUser($user, $newAccess);
        }
    }

    /**
     * @param int $provider
     * @return string
     */
    protected function getProviderKey(int $provider): string {
        switch($provider) {
            case EventStoreFactory::GOOGLE:
                $provider = 'google';
                break;
            default:
                throw new \Exception('No provider found');
        }

        return $provider;
    }

     /**
     * @param User $user
     * @param array $access
     */
    protected function saveAccessOnUser(User $user, array $access) {
        $user->event_providers_access = $access;
        $user->save();
    }
}