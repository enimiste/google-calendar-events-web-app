<?php

namespace App\Business\Calendar\Provider\Google;

use App\Business\Calendar\Provider\EventStoreInterface;
use App\Business\Calendar\Provider\ShouldAuthException;
use App\Business\Calendar\Provider\AccessTokenStorageInterface;
use App\Business\Calendar\Provider\EventStoreFactory;
use App\User;
use Google_Client;
use Google_Service_Calendar;

class GoogleEventStore implements EventStoreInterface {

    /** @var array */
    protected $credentials;

    /** @var AccessTokenStorageInterface */
    protected $accessTokenStorage;

    /** @var string */
    protected $oAuthRedirectUri;

    public function __construct(array $credentials, string $oAuthRedirectUri, AccessTokenStorageInterface $accessTokenStorage){
        $this->credentials = $credentials;
        $this->accessTokenStorage = $accessTokenStorage;
        $this->oAuthRedirectUri=$oAuthRedirectUri;
    }

    /**
     * @param User $user
     * @param array $options to send extras options to the provider
     * @param string $calendarId
     * 
     * @return array
     * @throws \App\Business\Calendar\Provider\ShouldAuthException
     */
    function loadEvents(User $user, array $options = [], $calendarId = 'primary'):array {
        $client = $this->getClient($user);
        $service = new Google_Service_Calendar($client);
        $options = array_merge([
            'maxResults'=>10,
            'singleEvents'=>true,
            'timeMin'=>date('c'),
        ], $options);

        $events = $service->events->listEvents($calendarId, $options)->getItems();
        return array_map(function($event){
            return [
                'title'=>$event->getSummary(),
                'description'=>$event->getDescription(),
                'location'=>$event->getLocation(),
            ];
        }, $events);
    }

    /**
     * @param User $user
     * @param array $response
     */
    function onOAuthCallback(User $user, array $response) {
        $client = $this->getClient($user, true);
        if(!array_has($response, 'error')) {
            if(array_has($response, 'code')) {
                $x  = $client->fetchAccessTokenWithAuthCode(array_get($response, 'code'));
                if(!array_has($x, 'error')){
                    $this->accessTokenStorage->save($user, EventStoreFactory::GOOGLE, $client->getAccessToken());
                    return;
                }
            }
        }
        
        throw new ShouldAuthException($client->createAuthUrl());
    }

    /**
     * @param User $user
     * @param boolean $withoutAccessToken
     */
    protected function getClient(User $user, $withoutAccessToken = false): Google_Client {
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar Tutorial');
        $client->setAuthConfig($this->credentials);
        $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setRedirectUri($this->oAuthRedirectUri);

        if($withoutAccessToken) {
            return $client;
        }

        $accessToken = $this->accessTokenStorage->get($user, EventStoreFactory::GOOGLE);
        if(empty($accessToken)){
            throw new ShouldAuthException($client->createAuthUrl());
        }

        $client->setAccessToken($accessToken);

        if($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $this->accessTokenStorage->save($user, EventStoreFactory::GOOGLE, $client->getAccessToken());
        }

        return $client;
    }
}