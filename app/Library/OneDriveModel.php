<?php
/**
 * Created by PhpStorm.
 * User: Arraylist
 * Date: 03-Dec-15
 * Time: 3:17 PM
 */

namespace App\Library;

require_once __DIR__ . '../../../vendor/autoload.php';

use AdammBalogh\Box\Command\Content;
use App\Library\OneDrive\OAuthClient;
use AdammBalogh\KeyValueStore\KeyValueStore;
use AdammBalogh\KeyValueStore\Adapter\MemoryAdapter;
use AdammBalogh\Box\Exception\ExitException;
use AdammBalogh\Box\Exception\OAuthException;
use GuzzleHttp\Exception\ClientException;


class OneDriveModel
{
    private $access_token;
    private $refresh_token;
    private $clientId = '000000004016694A';
    private $clientSecret = 'U30Ozap0Su7I8aDMpOmbC4M3oehKH1eN';
    private $redirectUri = 'http://localhost/gathercloud/public/add/onedrive';
    private $state;

    function __construct($access_token = null){
        if($access_token != null){
            $this->access_token = $access_token;
        } else{
            $keyValueStore = new KeyValueStore(new MemoryAdapter());
            $oAuthClient = new OAuthClient($keyValueStore, $this->clientId, $this->clientSecret, $this->redirectUri);
            try {
                $oAuthClient->authorize();
                $keyValueStore = $oAuthClient->getKvs();
                $this->access_token = $keyValueStore->get('access_token');
//                $this->refresh_token = $keyValueStore->get('refresh_token');
            } catch (ExitException $e) {
                # Location header has set (box's authorize page)
                # Instead of an exit call it throws an ExitException
                exit;
            } catch (OAuthException $e) {
                # e.g. Invalid user credentials
                # e.g. The user denied access to your application
            } catch (ClientException $e) {
                # e.g. if $_GET['code'] is older than 30 sec
            }

        }
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

}