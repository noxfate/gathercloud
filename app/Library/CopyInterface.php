<?php
/**
 * Created by PhpStorm.
 * User: jarvis
 * Date: 28/11/2558
 * Time: 17:31 น.
 */
namespace App\Library;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Mockery\Exception;

require_once base_path('vendor/autoload.php');


Class CopyInterface implements ModelInterface
{
    private $cpyobj;

    private $consumerKey = "bGCvu6JV6fVdIrVzmkA0A8NfbtDy3cG2";
    private $consumerSecret = "3DSK8DV0pcJGT2IMeN3W5FOE7woK2nVDcSMupuCZtfOdWgVc";

    private $server = "api.copy.com";
    private $secure = true;
    private $self_signed = true;
    private $www = "www.copy.com";


    public function __construct($access = null)
    {

        if (empty($access))
            $this->accessToken = $this->authenticate();
        else
            $this->accessToken = $access;

        $this->cpyobj = new \Barracuda\Copy\API($this->consumerKey,
            $this->consumerSecret,
            $this->accessToken['token'],
            $this->accessToken['secret']);
    }

    /**
     * @return array
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }


    private function authenticate()
    {
        // From authorize page
        if (empty($_GET['oauth_token'])) {
            $url = $this->getRequestToken();
            header("Location: $url");

            exit();
        } else {
            $ac = $this->requestAccessToken($_GET['oauth_token']);
            return $ac;
        }

    }

    private function getRequestToken()
    {
        // URL
        $requestURL = "https://$this->server/oauth/request";
        $callbackURL = 'http://' . $_SERVER['SERVER_NAME'] . '/gathercloud/public/add/copy';
        $authorizeURL = "https://$this->www/applications/authorize";


        // Get Request Token
        if (isset($_GET['scope'])) {
            $scope = (array(
                'profile' => array(
                    'read' => true,
                    'write' => true,
                    'email' => array(
                        'read' => true,
                    ),
                ),
                'inbox' => array(
                    'read' => true,
                ),
                'company' => array(
                    'multi' => true,
                    'filesystem' => array(
                        'read' => true,
                        'write' => true,
                    ),
                    'inbox' => array(
                        'read' => true,
                    ),
                    'email' => array(
                        'read' => true,
                    ),
                ),
                'links' => array(
                    'read' => true,
                    'write' => true,
                ),
                'filesystem' => array(
                    'read' => true,
                    'write' => true,
                ),
            ));

            if ($_GET['scope'] == 'profile-only') {
                unset($scope['inbox']);
                unset($scope['company']);
                unset($scope['links']);
                unset($scope['filesystem']);
            } else if ($_GET['scope'] == 'filesystem-read') {
                unset($scope['profile']);
                unset($scope['inbox']);
                unset($scope['company']);
                unset($scope['links']);
                $scope['filesystem']['write'] = false;
            } else if ($_GET['scope'] == 'none') {
                $scope = array();
            }

            $scope = json_encode($scope);
            $requestURL .= '?scope=' . urlencode($scope);
        }
        session_start();
        $tokenInfo = null;
        try {
            $OAuth = new \OAuth($this->consumerKey, $this->consumerSecret);
            $OAuth->enableDebug();
            // SSL CA Signed
            if ($this->self_signed) $OAuth->disableSSLChecks();
            $tokenInfo = $OAuth->getRequestToken($requestURL, $callbackURL);
        } catch (Exception $E) {
            echo '<h1>There was an error getting the Request Token</h1>';
            echo '<pre>';
            echo "Message:\n";
            print_r($E->getMessage());
            echo "\n\nLast Response:\n";
            print_r($OAuth->getLastResponse());
            echo "\n\nLast Response Info:\n";
            print_r($OAuth->getLastResponseInfo());
            echo "\n\nDebug Info:\n";
            print_r($OAuth->debugInfo); // get info about headers
            echo '</pre>';
        }
        // Check whether Return is empty or not
        if (empty($tokenInfo['oauth_token_secret']) || empty($tokenInfo['oauth_token'])) {
            echo "<pre>Token Info:\n";
            print_r($tokenInfo);
            echo '</pre>';
            exit;
        }

        $_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];
        $location = $authorizeURL . '?oauth_token=' . $tokenInfo['oauth_token'];

        $response_body = $OAuth->getLastResponse();
        $response = $OAuth->getLastResponseInfo();
        $debug = $OAuth->debugInfo;


        return $location;

    }


    private function requestAccessToken($oauth_token)
    {
        // URL
        $accessURL = "https://$this->server/oauth/access";

        // Get Access Token Flow
        session_start();
        try {
            $OAuth = new \OAuth($this->consumerKey, $this->consumerSecret);
            if ($this->self_signed) $OAuth->disableSSLChecks();
            $OAuth->setToken($oauth_token, $_SESSION['oauth_token_secret']);
            $OAuth->enableDebug();
            $tokenInfo = $OAuth->getAccessToken($accessURL);

            $response_body = $OAuth->getLastResponse();
            $response = $OAuth->getLastResponseInfo();
            $debug = $OAuth->debugInfo;

            // Open and decode the file
//            $data = json_decode(file_get_contents('keys.json'));
//
//            if (!isset($data->consumer) || !isset($data->access)) {
//                die("Someone has deleted the consumer field during the OAuth handshake.");
//            }
//
//            // Setting new data
//            $data->access->token = $tokenInfo['oauth_token'];
//            $data->access->secret = $tokenInfo['oauth_token_secret'];
//
//            // encode
//            $new_data = json_encode($data);
//
//            // write contents to the file
//            $handle = fopen('keys.json', 'w');
//            fwrite($handle, $new_data);
//            fclose($handle);

            $access = Array("token" => $tokenInfo['oauth_token']
            , "secret" => $tokenInfo['oauth_token_secret']);

        } catch (Exception $E) {
            echo "<pre>OAuth ERROR MESSAGE:\n";
            echo $E->getMessage();
            echo "\nRESPONSE:\n";
            var_dump($OAuth->getLastResponse());
            echo "\nRESPONSE INFO:\n";
            var_dump($OAuth->getLastResponseInfo());
            echo '</pre>';
        }
        return $access;

    }

    public function getAccountInfo()
    {
        // TODO: Implement getAccountInfo() method.
        return $this->cpyobj->getUserInfo();

    }

    public function downloadFile($file, $destination = null)
    {
        // TODO: Implement downloadFile() method.
    }

    public function uploadFile($file, $destination = null)
    {
        // TODO: Implement uploadFile() method.
    }

    public function getFiles($file = '/')
    {
        // TODO: Implement getFiles() method.
        if ($file == null){
            $file = '/';
        }
        return $this->cpyobj->listPath($file);
    }

    public function deleteFile($file)
    {
        // TODO: Implement deleteFile() method.
    }

    public function getLink($file)
    {
        // TODO: Implement getLink() method.
    }

    private function json_prettify($json)
    {
        if (strnatcmp(phpversion(), '5.4.0') >= 0) {
            return json_encode(json_decode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {

            $result = '';
            $pos = 0;
            $strLen = strlen($json);
            $indentStr = '  ';
            $newLine = "\n";
            $prevChar = '';
            $outOfQuotes = true;

            for ($i = 0; $i <= $strLen; $i++) {

                // Grab the next character in the string.
                $char = substr($json, $i, 1);

                // Are we inside a quoted string?
                if ($char == '"' && $prevChar != '\\') {
                    $outOfQuotes = !$outOfQuotes;

                    // If this character is the end of an element,
                    // output a new line and indent the next line.
                } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                    $result .= $newLine;
                    $pos--;
                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }

                // Add the character to the result string.
                $result .= $char;

                // If the last character was the beginning of an element,
                // output a new line and indent the next line.
                if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                    $result .= $newLine;
                    if ($char == '{' || $char == '[') {
                        $pos++;
                    }

                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }

                $prevChar = $char;
            }

            return $result;
        }

    }


    public function rename($file, $new_name)
    {
        // TODO: Implement rename() method.
    }

    public function getPath($file)
    {
        // TODO: Implement getPath() method.
    }
}