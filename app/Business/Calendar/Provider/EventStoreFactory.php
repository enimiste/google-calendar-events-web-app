<?php

namespace App\Business\Calendar\Provider;

use App\Business\Calendar\Provider\Google\GoogleEventStore;

class EventStoreFactory {
    const GOOGLE = 1;

    /**
     * 
     * @param int $provider see EventStoreFactory::GOOGLE
     * @return EventStoreInterface
     */
    public static function get(int $provider): EventStoreInterface {
        switch($provider) {
            case self::GOOGLE:
                return app(GoogleEventStore::class);
            default:
                throw new \Exception('No provider found');
        }
    }
}