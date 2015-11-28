<?php

// Autoload composer installed libraries
// require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set("display_errors", "on");

include("guzzle/ClientInterface.php");
include("client/Model.php");
include("guzzle/RedirectMiddleware.php");
include("guzzle/RequestOptions.php");
include("guzzle/psr7/UriInterface.php");
include("guzzle/psr7/Uri.php");
include("guzzle/psr7/MessageInterface.php");
include("guzzle/psr7/RequestInterface.php");
include("guzzle/psr7/MessageTrait.php");
include("guzzle/psr7/Request.php");
include("guzzle/psr7/StreamInterface.php");
include("guzzle/psr7/Stream.php");
include("guzzle/psr7/functions.php");
include("guzzle/promises/TaskQueue.php");
include("guzzle/promises/functions.php");
include("guzzle/Client.php");
include("client/Connection.php");
include("guzzle/PrepareBodyMiddleware.php");
include("guzzle/Exception/GuzzleException.php");
include("guzzle/Exception/TransferException.php");
include("guzzle/Exception/RequestException.php");
include("guzzle/Exception/BadResponseException.php");
include("guzzle/Exception/ClientException.php");
include("guzzle/Middleware.php");
include("guzzle/HandlerStack.php");
include("guzzle/Handler/CurlHandler.php");
include("guzzle/Handler/StreamHandler.php");
include("guzzle/functions.php");
include("guzzle/Handler/Proxy.php");
include("guzzle/Handler/CurlMultiHandler.php");
include("guzzle/Handler/CurlFactoryInterface.php");
include("guzzle/psr7/ResponseInterface.php");
include("guzzle/psr7/Response.php");
include("guzzle/Handler/EasyHandle.php");
include("guzzle/promises/PromiseInterface.php");
include("guzzle/promises/Promise.php");
include("guzzle/promises/FulfilledPromise.php");
include("guzzle/Handler/CurlFactory.php");
include("client/Query/Findable.php");
include("client/Persistance/Storable.php");


/**
 * Function to retrieve persisted data for the example
 * @param string $key
 * @return null|string
 */
function getValue($key)
{
    $storage = json_decode(file_get_contents('storage.json'), true);
    if (array_key_exists($key, $storage)) {
        return $storage[$key];
    }
    return null;
}

/**
 * Function to persist some data for the example
 * @param string $key
 * @param string $value
 */
function setValue($key, $value)
{
    $storage       = json_decode(file_get_contents('storage.json'), true);
    $storage[$key] = $value;
    file_put_contents('storage.json', json_encode($storage));
}

/**
 * Function to authorize with Exact, this redirects to Exact login promt and retrieves authorization code
 * to set up requests for oAuth tokens
 */
function authorize()
{
    $connection = new \Picqer\Financials\Exact\Connection();
    $connection->setRedirectUrl('http://crmdevelop.cbx-nederland.nl/modules/ExactOnline/test.php');
    $connection->setExactClientId('27e8b04d-b60d-489e-b493-e70c90f4fc0b');
    $connection->setExactClientSecret('KWXYAJFxizty');
    $connection->redirectForAuthorization();
}

/**
 * Function to connect to Exact, this creates the client and automatically retrieves oAuth tokens if needed
 *
 * @return \Picqer\Financials\Exact\Connection
 * @throws Exception
 */
function connect()
{
    $connection = new \Picqer\Financials\Exact\Connection();
    $connection->setRedirectUrl('http://crmdevelop.cbx-nederland.nl/modules/ExactOnline/test.php');
    $connection->setExactClientId('27e8b04d-b60d-489e-b493-e70c90f4fc0b');
    $connection->setExactClientSecret('KWXYAJFxizty');

    if (getValue('authorizationcode')) // Retrieves authorizationcode from database
    {
        $connection->setAuthorizationCode(getValue('authorizationcode'));
    }

    if (getValue('accesstoken')) // Retrieves accesstoken from database
    {
        $connection->setAccessToken(getValue('accesstoken'));
    }

    if (getValue('refreshtoken')) // Retrieves refreshtoken from database
    {
        $connection->setRefreshToken(getValue('refreshtoken'));
    }

    if (getValue('expires_in')) // Retrieves expires timestamp from database
    {
        $connection->setTokenExpires(getValue('expires_in'));
    }

    // Make the client connect and exchange tokens
    try {
        $connection->connect();
    } catch (\Exception $e) {
        throw new Exception('Could not connect to Exact: ' . $e->getMessage());
    }

    // Save the new tokens for next connections
    setValue('accesstoken', $connection->getAccessToken());
    setValue('refreshtoken', $connection->getRefreshToken());

    // Save expires time for next connections
    setValue('expires_in', $connection->getTokenExpires());

    return $connection;
}

// If authorization code is returned from Exact, save this to use for token request
if (isset($_GET['code']) && is_null(getValue('authorizationcode'))) {
    setValue('authorizationcode', $_GET['code']);
}

// If we do not have a authorization code, authorize first to setup tokens
if (getValue('authorizationcode') === null) {
    authorize();
}

// Create the Exact client
$connection = connect();

// Get the journals from our administration
try {
    $journals = new \Picqer\Financials\Exact\Journal($connection);
    $result   = $journals->get();
    foreach ($result as $journal) {
        echo 'Journal: ' . $journal->Description . '<br>';
    }
} catch (\Exception $e) {
    echo get_class($e) . ' : ' . $e->getMessage();
}


?>