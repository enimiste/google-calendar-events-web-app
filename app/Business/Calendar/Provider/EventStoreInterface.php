<?php

namespace App\Business\Calendar\Provider;

use App\User;

interface EventStoreInterface {

    /**
     * @param User $user
     * @param array $options to send extras options to the provider
     * @return array
     * @throws \App\Business\Calendar\Provider\ShouldAuthException
     */
    function loadEvents(User $user, array $options = []):array;

    /**
     * @param User $user
     * @param array $response
     */
    function onOAuthCallback(User $user, array $response);

}