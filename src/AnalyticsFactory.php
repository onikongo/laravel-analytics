<?php

namespace Spatie\Analytics;

use Google_Client;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository;

class AnalyticsFactory
{
    public static function createForConfig(array $analyticsConfig)
    {
        $authenticatedClient = self::createAuthenticatedGoogleClient($analyticsConfig);

        $googleService = new Google_Service_Analytics($authenticatedClient);

        $analyticsClient = self::createAnalyticsClient($analyticsConfig, $googleService);

        return new Analytics($analyticsClient, $analyticsConfig['view_id']);
    }

    public static function createAuthenticatedGoogleClient($config): Google_Client
    {
        $client = new Google_Client();

        $credentials = $client->loadServiceAccountJson($config['client_secret_json'], 'https://www.googleapis.com/auth/analytics.readonly');

        $client->setAssertionCredentials($credentials);

        return $client;
    }

    protected static function createAnalyticsClient(array $analyticsConfig, Google_Service_Analytics $googleService): AnalyticsClient
    {
        $client = new AnalyticsClient($googleService, app(Repository::class));

        $client->setCacheLifeTimeInMinutes($analyticsConfig['cache_lifetime_in_minutes']);

        return $client;
    }
}
