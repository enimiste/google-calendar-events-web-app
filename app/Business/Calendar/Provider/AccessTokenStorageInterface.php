<?php

namespace App\Business\Calendar\Provider;

use App\Business\Calendar\Provider\EventStoreFactory;
use App\User;

interface AccessTokenStorageInterface {

    /**
     * @param User $user
     * @param int $provider see EventStoreFactory::GOOGLE
     */
    function get(User $user, int $provider): array;

    /**
     * @param User $user
     * @param int $provider
     * @param array $access
     */
    function save(User $user, int $provider, array $access);

    /**
     * @param User $user
     * @param int $provider see EventStoreFactory::GOOGLE
     */
    function forget(User $user, int $provider);
}