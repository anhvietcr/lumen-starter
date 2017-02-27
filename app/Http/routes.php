<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Http\Controllers\Api\QuoteController;

$app->get('/', function () use ($app) {
    return $app->make('db')->table('quotes')->orderByRaw('random()')->take(1)->get();
});
$app->group(['prefix' => 'api/v1'], function () use ($app) {
    $app->get('quotes', ['middleware' => 'jsonApi.enforceMediaType', 'uses' => 'Api\QuoteController' . '@index']);
    $app->post('quotes', ['middleware' => 'jsonApi.enforceMediaType', 'uses' => 'Api\QuoteController' . '@post']);
    $app->get('quotes/{id}', ['middleware' => 'jsonApi.enforceMediaType', 'uses' => 'Api\QuoteController' . '@show']);
});

/**
 * Healthcheck to ensure the application is healthy.  When deployed this endpoint will determine healthy nodes
 * where the application can live.  In the event of network connectivity failure with any external dependencies,
 * this healthcheck should fail.
 */
$app->get('/healthcheck/{token}', function ($token) {
    if ($token == env('HEALTHCHECK_TOKEN')) {
        $connection = DB::connection();
        $connection->disconnect();

        return response('');
    }

    throw new \Exception('Invalid healthcheck token');
});
