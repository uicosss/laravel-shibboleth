<?php

namespace StudentSystemServices\Shibboleth\Controllers;

use Illuminate\Auth\GenericUser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Console\AppNamespaceDetectorTrait;
//use JWTAuth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShibbolethController extends Controller
{
    /**
     * Service Provider
     * @var Shibalike\SP
     */
    private $sp;

    /**
     * Identity Provider
     * @var Shibalike\IdP
     */
    private $idp;

    /**
     * Configuration
     * @var Shibalike\Config
     */
    private $config;

    /**
     * Constructor
     */
    public function __construct(GenericUser $user = null)
    {
        $this->user = $user;
    }

    /**
     * Create the session, send the user away to the IDP
     * for authentication.
     */
    public function login()
    {
        return Redirect::to('https://' . Request::server('SERVER_NAME')
            . ':' . Request::server('SERVER_PORT') . config('shibboleth.idp_login')
            . '?target=' . action('\\' . __CLASS__ . '@idpAuthenticate'));
    }

    /**
     * Setup authentication based on returned server variables
     * from the IdP.
     */
    public function idpAuthenticate()
    {
        if (empty(config('shibboleth.user'))) {
            throw new \Exception('No user attribute mapping for server variables.');
        }

        foreach (config('shibboleth.user') as $local => $server) {
            $map[$local] = $this->getServerVariable($server);
        }

        if (empty($map['email'])) {
            return abort(403, 'Unauthorized');
        }

        $userClass = config('auth.providers.users.model', 'App\User');

        // Attempt to login with the email, if success, update the user model
        // with data from the Shibboleth headers (if present)
        if (Auth::attempt(array('email' => $map['email']), true)) {
            $user = $userClass::where('email', '=', $map['email'])->first();

            // Update the model as necessary
            $user->update($map);
        }

        // Add user and send through auth.
        elseif (config('shibboleth.add_new_users', true)) {
            $map['password'] = 'shibboleth';
            $user = $userClass::create($map);
            Auth::login($user);
        }

        else {
            return abort(403, 'Unauthorized');
        }

        Session::regenerate();

        // Check if there is a session variable 'shibboleth_redirect_to' set, otherwise use the default value in config('shibboleth.authenticated')
        $route = empty(session('shibboleth_redirect_to')) ? config('shibboleth.authenticated') : session('shibboleth_redirect_to');

        // Unset the shibboleth_redirect_to if it was set
        if(!empty(session('shibboleth_redirect_to'))){

            session()->remove('shibboleth_redirect_to');

        }

//        if (config('jwtauth') === true) {
//            $route .= $this->tokenizeRedirect($user, ['auth_type' => 'idp']);
//        }

        return redirect()->intended($route);
    }

    /**
     * Destroy the current session and log the user out, redirect them to the main route.
     */
    public function destroy()
    {
        Auth::logout();
        Session::flush();

//        if (config('jwtauth')) {
//            $token = JWTAuth::parseToken();
//            $token->invalidate();
//        }

        return Redirect::to('https://' . Request::server('SERVER_NAME') . config('shibboleth.idp_logout'));
    }

    /**
     * Wrapper function for getting server variables.
     */
    private function getServerVariable($variableName)
    {

        $variable = Request::server($variableName);

        if (strtolower($variableName) === 'itrustsuppress') {
            $variable = empty($variable) ? Request::server('REDIRECT_' . $variableName) : $variable;

            return strtolower($variable) === 'true';
        }

        return (!empty($variable)) ?
            $variable :
            Request::server('REDIRECT_' . $variableName);
    }

    /*
     * Simple function that allows configuration variables
     * to be either names of views, or redirect routes.
     */
    private function viewOrRedirect($view)
    {
        return (View::exists($view)) ? view($view) : Redirect::to($view);
    }

    /**
     * Uses JWTAuth to tokenize the user and returns a URL query string.
     *
     * @param  App\User $user
     * @param  array $customClaims
     * @return string
     */
//    private function tokenizeRedirect($user, $customClaims)
//    {
//        // This is where we used to setup a session. Now we will setup a token.
//        $token = JWTAuth::fromUser($user, $customClaims);
//
//        // We need to pass the token... how?
//        // Let's try this.
//        return "?token=$token";
//    }
}
