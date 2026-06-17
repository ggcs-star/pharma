<?php

return [

    'service' => env('GEOIP_DRIVER', 'maxmind_database'),

    'services' => [

        'ipapi' => [
            'class' => \Torann\GeoIP\Services\IPApi::class,
            'secure' => true,
            'key' => env('GEOIP_IPAPI_KEY'),
            'continent_path' => 'continent_code',
            'lang' => env('GEOIP_LANG', 'en'),
        ],

        'ipgeolocation' => [
            'class' => \Torann\GeoIP\Services\IPGeoLocation::class,
            'secure' => true,
            'key' => env('GEOIP_IPGEOLOCATION_KEY'),
            'lang' => env('GEOIP_LANG', 'en'),
        ],

        'maxmind_database' => [
            'class' => \Torann\GeoIP\Services\MaxMindDatabase::class,
            'database_path' => storage_path('app/geoip.mmdb'),
            'update_url' => sprintf(
                'https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=%s&suffix=tar.gz',
                env('MAXMIND_LICENSE_KEY')
            ),
            'locales' => ['en'],
        ],

        'maxmind_api' => [
            'class' => \Torann\GeoIP\Services\MaxMindWebService::class,
            'user_id' => env('MAXMIND_USER_ID'),
            'license_key' => env('MAXMIND_LICENSE_KEY'),
            'lang' => env('GEOIP_LANG', 'en'),
        ],

    ],

    'cache' => env('GEOIP_CACHE', 'all'),

    'cache_tags' => ['geoip'],

    'cache_expires' => 30,

    'default_location' => [

        'ip' => '127.0.0.0',
        'iso_code' => 'US',
        'country' => 'United States',
        'city' => 'New Haven',
        'state' => 'CT',
        'state_name' => 'Connecticut',
        'postal_code' => '06510',
        'lat' => 41.31,
        'lon' => -72.92,
        'timezone' => 'America/New_York',
        'continent' => 'NA',
        'default' => true,
        'currency' => 'USD',

    ],

    'log_failures' => true,

];