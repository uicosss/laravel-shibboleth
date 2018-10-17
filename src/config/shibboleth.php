<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Views / Endpoints
    |--------------------------------------------------------------------------
    |
    | Set your login page, or login routes, here. If you provide a view,
    | that will be rendered. Otherwise, it will redirect to a route.
    |
     */

    'idp_login'     => '/Shibboleth.sso/Login',
    'idp_logout'    => '/Shibboleth.sso/Logout',
    'authenticated' => '/home',
    'protected' => [ // Array of routes that should be protected with Shibboleth, invoked by custom middleware
        'dashboard'
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Variable Mapping
    |--------------------------------------------------------------------------
    |
    | Change these to the proper values for your IdP.
    |
     */

    'entitlement' => 'entitlement',

    'user' => [
        // fillable user model attribute => server variable
        'email'       => 'mail',
        'name'        => 'displayName',
        'first_name'  => 'givenName',
        'last_name'   => 'sn',
        'netid'       => 'eppn',
        'username'    => 'uid',
        'uin'         => 'iTrustUIN',
        'org'         => 'ou',
        'primary_affiliation' => 'primary-affiliation',
        'affiliation' => 'affiliation',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if / how new users are added
    |
     */

    'add_new_users' => true, // Should new users be added automatically if they do not exist?

    /*
    |--------------------------------------------------------------------------
    | JWT Auth
    |--------------------------------------------------------------------------
    |
    | JWTs are for the front end to know it's logged in
    |
    | https://github.com/tymondesigns/jwt-auth
    | https://github.com/StudentSystemServices/Laravel-Shibboleth-Service-Provider/issues/24
    |
     */

    'jwtauth' => env('JWTAUTH', false),
);
