<?php
namespace App\Library\OneDrive;

	/*
     * A Client instance allows communication with the OneDrive API and perform
     * operations programmatically.
     *
     * For an overview of the OneDrive protocol flow, see here:
     * http://msdn.microsoft.com/en-us/library/live/hh243647.aspx
     *
     * To manage your Live Connect applications, see here:
     * https://account.live.com/developers/applications/index
     * Or here:
     * https://manage.dev.live.com/ (not working?)
     *
     * For an example implementation, see here:
     * https://github.com/drumaddict/skydrive-api-yii/blob/master/SkyDriveAPI.php
     */
// TODO: support refresh tokens: http://msdn.microsoft.com/en-us/library/live/hh243647.aspx
// TODO: pass parameters in POST request body when obtaining the access token
class Client {
	// The base URL for API requests.
	const API_URL   = 'https://api.onedrive.com/v1.0'  ;  // https://apis.live.net/v5.0/

	// The base URL for authorization requests.
	const AUTH_URL  = 'https://login.live.com/oauth20_authorize.srf';

	// The base URL for token requests.
	const TOKEN_URL = 'https://login.live.com/oauth20_token.srf';

	// Client information.
	private $_clientId;

	// OAuth state (token, etc...).
	private $_state;

	// The last HTTP status received.
	private $_httpStatus;

	// The last Content-Type received.
	private $_contentType;

	/**
	 * Creates a base cURL object which is compatible with the OneDrive API.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 * @param  (array) $options - Further curl options to set.
	 * @return (resource) A compatible cURL object.
	 */
	private static function _createCurl($path, $options = array()) {
		$curl = curl_init();

		$default_options = array(
			// General options.
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_AUTOREFERER    => true,

			// SSL options.
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false
		);

		// See http://php.net/manual/en/function.array-merge.php for a description of the + operator (and why array_merge() would be wrong)
		$final_options = $options + $default_options;

		curl_setopt_array($curl, $final_options);

		return $curl;
	}

	/**
	 * Processes a result returned by the OneDrive API call using a cURL object.
	 *
	 * @param  (resource) $curl - The cURL object used to perform the call.
	 * @return (object|string) The content returned, as an object instance if
	 *         served a JSON, or as a string if served as anything else.
	 */
	private function _processResult($curl) {
		$result = curl_exec($curl);
		if (false === $result) {
			throw new \Exception('curl_exec() failed: ' . curl_error($curl));
		}

		$info = curl_getinfo($curl);

		$this->_httpStatus = array_key_exists('http_code', $info) ?
			(int) $info['http_code'] : null;

		$this->_contentType = array_key_exists('content_type', $info) ?
			(string) $info['content_type'] : null;

		// Parse nothing but JSON.
		if (1 !== preg_match('|^application/json|', $this->_contentType)) {
			return $result;
		}

		// Empty JSON string is returned as an empty object.
		if ('' == $result) {
			return (object) array();
		}

		$decoded = json_decode($result);
		// $vars    = get_object_vars($decoded);

		// if (array_key_exists('error', $vars)) {
		// 	throw new \Exception($decoded->error->message, (int) $decoded->error->code);
		// }

		return $decoded;
	}

	/**
	 * Constructor.
	 *
	 * @param  (array) $options. The options to use while creating this object.
	 *         The only supported key is 'state'. When defined, it should contain
	 *         a valid OneDrive client state, as returned by getState(). Default:
	 *         array().
	 */
	public function __construct(array $options = array()) {
		$this->_clientId = array_key_exists('client_id', $options)
			? (string) $options['client_id'] : null;

		$this->_state = array_key_exists('state', $options)
			? $options['state'] : (object) array(
				'redirect_uri' => null,
				'token'        => null
			);
	}

	/**
	 * Gets the current state of this Client instance. Typically saved in the
	 * session and passed back to the Client constructor for further requests.
	 *
	 * @return (object) The state of this Client instance.
	 */
	public function getState() {
		return $this->_state;
	}

	/**
	 * Gets the URL of the log in form. After login, the browser is redirected to
	 * the redirect URL, and a code is passed as a GET parameter to this URL.
	 *
	 * The browser is also redirected to this URL if the user is already logged
	 * in.
	 *
	 * @param  (array) $scopes - The OneDrive scopes requested by the application.
	 *         Supported values: 'wl.signin', 'wl.basic', 'wl.contacts_skydrive',
	 *         'wl.skydrive_update'.
	 * @param  (string) $redirectUri - The URI to which to redirect to upon
	 *         successful log in.
	 * @param  (array) $options. Reserved for future use. Default: array(). TODO:
	 *         support it.
	 * @return (string) The login URL.
	 */
	public function getLogInUrl(array $scopes, $redirectUri, array $options = array()) {
		if (null === $this->_clientId) {
			throw new \Exception('The client ID must be set to call getLoginUrl()');
		}

		$imploded    = implode(',', $scopes);
		$redirectUri = (string) $redirectUri;
		$this->_state->redirect_uri = $redirectUri;

		// When using this URL, the browser will eventually be redirected to the
		// callback URL with a code passed in the URL query string (the name of the
		// variable is "code"). This is suitable for PHP.
		$url = self::AUTH_URL
			. '?client_id=' . urlencode($this->_clientId)
			. '&scope=' . urlencode($imploded)
			. '&response_type=code'
			. '&redirect_uri=' . urlencode($redirectUri)
			. '&display=popup'
			. '&locale=en';

		return $url;
	}

	/**
	 * Gets the access token expiration delay.
	 *
	 * @return (int) The token expiration delay, in seconds.
	 */
	public function getTokenExpire() {
		return $this->_state->token->obtained
		+ $this->_state->token->data->expires_in - time();
	}

	/**
	 * Gets the status of the current access token.
	 *
	 * @return (int) The status of the current access token:
	 *          0 => no access token
	 *         -1 => access token will expire soon (1 minute or less)
	 *         -2 => access token is expired
	 *          1 => access token is valid
	 */
	public function getAccessTokenStatus() {
		if (null === $this->_state->token) {
			return 0;
		}

		$remaining = $this->getTokenExpire();

		if (0 >= $remaining) {
			return -2;
		}

		if (60 >= $remaining) {
			return -1;
		}

		return 1;
	}

	public function getAccessToken() {
		if (null === $this->_state->token->data->access_token) {
			return 0;
		}

		return $this->_state->token->data->access_token;
	}

	/**
	 * Obtains a new access token from OAuth. This token is valid for one hour.
	 *
	 * @param  (string) $clientSecret - The OneDrive client secret.
	 * @param  (string) $code - The code returned by OneDrive after successful log
	 *         in.
	 * @param  (string) $redirectUri. Must be the same as the redirect URI passed
	 *         to getLoginUrl().
	 */
	public function obtainAccessToken($clientSecret, $code) {
		if (null === $this->_clientId) {
			throw new \Exception('The client ID must be set to call obtainAccessToken()');
		}

		if (null === $this->_state->redirect_uri) {
			throw new \Exception('The state\'s redirect URI must be set to call obtainAccessToken()');
		}

		$url = self::TOKEN_URL
			. '?client_id=' . urlencode($this->_clientId)
			. '&redirect_uri=' . urlencode($this->_state->redirect_uri)
			. '&client_secret=' . urlencode($clientSecret)
			. '&grant_type=authorization_code'
			. '&code=' . urlencode($code);

		$curl = curl_init();

		curl_setopt_array($curl, array(
			// General options.
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_AUTOREFERER    => true,

			// SSL options.
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,

			CURLOPT_URL            => $url
		));

		$result = curl_exec($curl);
		$decoded = json_decode($result);

		if (null === $decoded) {
			throw new \Exception('json_decode() failed');
		}

		$this->_state->redirect_uri = null;

		$this->_state->token = (object) array(
			'obtained' => time(),
			'data'     => $decoded
		);
	}

	/**
	 * Renews the access token from OAuth. This token is valid for one hour.
	 */
	/*public function renewAccessToken($clientSecret, $redirectUri) {
		$url = self::TOKEN_URL
			. '?client_id=' . $this->_clientId
			. '&redirect_uri=' . (string) $redirectUri
			. '&client_secret=' . (string) $clientSecret
			. '&grant_type=' . 'refresh_token'
			. '&code=' . (string) $code;
	}*/

	/**
	 * Performs a call to the OneDrive API using the GET method.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 * @param  (array) $options - Further curl options to set.
	 */
	public function apiGet($path, $options = array()) {
		$url = self::API_URL . $path
			. '?access_token=' . urlencode($this->_state->token->data->access_token);
		$curl = self::_createCurl($path, $options);

		curl_setopt($curl, CURLOPT_URL, $url);

		return $this->_processResult($curl);
	}

	public function apiSearch($query, $options = array()){
		$url = self::API_URL . "/drive/items/root/view.search".
			'?q=' . $query
			. '&access_token=' . urlencode($this->_state->token->data->access_token);
		$curl = self::_createCurl($query, $options);
		curl_setopt($curl, CURLOPT_URL, $url);

		return $this->_processResult($curl);
	}

	public function apiDownload($path) {
		$url = self::API_URL . "/drive/root:" . $path . ':/content';
		$curl = curl_init();
		curl_setopt_array($curl, array(
			// SSL options.
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER => true,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_URL        => $url,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),
		));

		$content = curl_exec( $curl );
		curl_close ( $curl );
		return $this->get_headers_from_curl_response($content);
	}

	public function apiDownloadIn($path, $des = null) {
		$url = self::API_URL . "/drive/root:" . $path . ':/content';
		$curl = curl_init();

		$fh = @fopen($des, 'wb'); // write binary
		if($fh === false) {
			@fclose($fh);
			throw new DropboxException("Could not create file $des !");
		}
		curl_setopt_array($curl, array(
			// SSL options.
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_BINARYTRANSFER => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FILE => $fh,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_URL        => $url,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),
		));
		curl_exec($curl);
		curl_close ( $curl );
		fclose($fh);
	}

	function get_headers_from_curl_response($response)
	{
		$headers = array();

		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

		foreach (explode("\r\n", $header_text) as $i => $line)
			if ($i === 0)
				$headers['http_code'] = $line;
			else
			{
				list ($key, $value) = explode(': ', $line);

				$headers[$key] = $value;
			}

		return $headers;
	}

	/**
	 * Performs a call to the OneDrive API using the POST method.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 * @param  (array|object) $data - The data to pass in the body of the request.
	 */
	public function apiPost($path, $data) {
		$url  = self::API_URL . $path;
		$data = (object) $data;
		$curl = self::_createCurl($path);

		curl_setopt_array($curl, array(
			CURLOPT_URL        => $url,
			CURLOPT_POST       => true,

			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json', // The data is sent as JSON as per OneDrive documentation
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),

			CURLOPT_POSTFIELDS => json_encode($data)
		));

		return $this->_processResult($curl);
	}

	/**
	 * Performs a call to the OneDrive API using the PUT method.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 * @param  (resource) $stream - The data stream to upload.
	 * @param  (string) $contentType - The MIME type of the data stream, or null
	 *         if unknown. Default: null.
	 */
	public function apiPut($path, $stream, $contentType = null) {
		$url   = self::API_URL . $path;
		$curl  = self::_createCurl($path);
		$stats = fstat($stream);

		$headers = array(
			'Authorization: Bearer ' . $this->_state->token->data->access_token
		);

		if (null !== $contentType) {
			$headers[] = 'Content-Type: ' . $contentType;
		}

		$options = array(
			CURLOPT_URL        => $url,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_PUT        => true,
			CURLOPT_INFILE     => $stream,
			CURLOPT_INFILESIZE => $stats[7] // Size
		);

		curl_setopt_array($curl, $options);
		return $this->_processResult($curl);
	}

	/**
	 * Performs a call to the OneDrive API using the DELETE method.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 */
	public function apiDelete($path) {
		$url = self::API_URL . $path
			. '?access_token=' . urlencode($this->_state->token->data->access_token);
		$curl = self::_createCurl($path);

		curl_setopt_array($curl, array(
			CURLOPT_URL           => $url,
			CURLOPT_CUSTOMREQUEST => 'DELETE'
		));

		return $this->_processResult($curl);
	}

	/**
	 * Performs a call to the OneDrive API using the MOVE method.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 * @param  (array|object) $data - The data to pass in the body of the request.
	 */
	public function apiMove($path, $data) {
		$url  = self::API_URL . $path;
		$data = (object) $data;
		$curl = self::_createCurl($path);

		curl_setopt_array($curl, array(
			CURLOPT_URL           => $url,
			CURLOPT_CUSTOMREQUEST => 'MOVE',

			CURLOPT_HTTPHEADER    => array(
				'Content-Type: application/json', // The data is sent as JSON as per OneDrive documentation
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),

			CURLOPT_POSTFIELDS    => json_encode($data)
		));

		return $this->_processResult($curl);
	}

	/**
	 * Performs a call to the OneDrive API using the COPY method.
	 *
	 * @param  (string) $path - The path of the API call (eg. me/skydrive).
	 * @param  (array|object) $data - The data to pass in the body of the request.
	 */
	public function apiCopy($path, $data) {
		$url  = self::API_URL . $path;
		$data = (object) $data;
		$curl = self::_createCurl($path);

		curl_setopt_array($curl, array(
			CURLOPT_URL           => $url,
			CURLOPT_CUSTOMREQUEST => 'COPY',

			CURLOPT_HTTPHEADER    => array(
				'Content-Type: application/json', // The data is sent as JSON as per OneDrive documentation
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),

			CURLOPT_POSTFIELDS    => json_encode($data)
		));

		return $this->_processResult($curl);
	}

	public function apiShare($path, $data) {
		$url  = self::API_URL . $path . '/action.createLink';
		$data = (object) $data;
		$curl = self::_createCurl($path);

		curl_setopt_array($curl, array(
			CURLOPT_URL        => $url,
			CURLOPT_POST       => true,

			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json', // The data is sent as JSON as per OneDrive documentation
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),

			CURLOPT_POSTFIELDS => json_encode($data)
		));

		return $this->_processResult($curl);
	}

	/**
	 * Creates a folder in the current OneDrive account.
	 *
	 * @param  (string) $name - The name of the OneDrive folder to be created.
	 * @param  (null|string) $parentId - The ID of the OneDrive folder into which
	 *         to create the OneDrive folder, or null to create it in the OneDrive
	 *         root folder. Default: null.
	 * @param  (null|string) $description - The description of the OneDrive folder to be
	 *         created, or null to create it without a description. Default: null.
	 * @return (Folder) The folder created, as a Folder instance referencing to
	 *         the OneDrive folder created.
	 */
	public function createFolder($name, $parentId = null) {

		if (null === $parentId) {
			$parentId = '/drive/root/children';
		}else{
			$parentId = $parentId . '/children';
		}

		$properties = array(
			'name' => (string) $name,
			'folder' => (object)(array(
			)),
			'@name.conflictBehavior' => 'rename'
		);
		$folder = $this->apiPost($parentId, (object) $properties);
		return $folder;
	}

	public function createSharing($type, $objectId = null) {
		$properties = array(
			'type' => (string) $type
		);

		$link = $this->apiShare($objectId, (object) $properties);
		return $link;
	}

	public function searchFile($query){
		$result =  $this->apiSearch($query);
//		$objects  = array();
//		foreach ($result->value as $d) {
//			$d = (object)$d;
//			$data = array(
//				'name' => $d->name,
//				'parent_id' => 'folder.'.strtolower(substr($d->parentReference->id,0,strpos($d->parentReference->id,"!"))).".".$d->parentReference->id,
//				'description' => "",
//				'size' => $d->size,
//				'created_time' => $d->createdDateTime,
//				'updated_time' => $d->lastModifiedDateTime,
//			);
//			$object = property_exists($d, 'folder') ?
//				new Folder($this, 'folder.'.strtolower(substr($d->id,0,strpos($d->id,"!"))).".".$d->id, $data)
//				: new File($this, 'file.'.strtolower(substr($d->id,0,strpos($d->id,"!"))).".".$d->id, $data);
//
//			$objects[] = $object;
//		}

		return $result->value;
	}

	/**
	 * Creates a file in the current OneDrive account.
	 *
	 * @param  (string) $name - The name of the OneDrive file to be created.
	 * @param  (null|string) $parentId - The ID of the OneDrive folder into which
	 *         to create the OneDrive file, or null to create it in the OneDrive
	 *         root folder. Default: null.
	 * @param  (string) $content - The content of the OneDrive file to be created.
	 * @return (File) The file created, as File instance referencing to the
	 *         OneDrive file created.
	 * @throw  (\Exception) Thrown on I/O errors.
	 */
	public function createFile($name, $content = '',$parentId = '') {
		$parentId = '/drive/root:' . $parentId . '/' . urlencode($name) . ':/content';

		$stream = fopen('php://temp', 'w+b');

		if (false === $stream) {
			throw new \Exception('fopen() failed');
		}

		if (false === fwrite($stream, $content)) {
			fclose($stream);
			throw new \Exception('fwrite() failed');
		}

		if (!rewind($stream)) {
			fclose($stream);
			throw new \Exception('rewind() failed');
		}

		// TODO: some versions of cURL cannot PUT memory streams? See here for a
		// workaround: https://bugs.php.net/bug.php?id=43468
		$file = $this->apiPut($parentId, $stream);
		fclose($stream);
		dd($file);
	}


	/**
	 * Fetches the objects in a folder in the current OneDrive account.
	 *
	 * @return (array) The objects in the folder fetched, as Object instances
	 *         referencing OneDrive objects.
	 */
	public function fetchObjects($objectId = '/') {
		$objectId = '/drive/root:' . $objectId . ':/children';
		$result   = $this->apiGet($objectId);
//		$objects  = array();
//
//		foreach ($result->data as $data) {
//			$object = in_array($data->type, array('folder', 'album')) ?
//				new Folder($this, $data->id, $data)
//				: new File($this, $data->id, $data);
//
//			$objects[] = $object;
//		}

		return $result->value;
	}

	/**
	 * Updates the properties of an object in the current OneDrive account.
	 *
	 * @param  (string) $objectId - The unique ID of the object to update.
	 * @param  (array|object) $properties - The properties to update. Default:
	 *         array().
	 * @throw  (\Exception) Thrown on I/O errors.
	 */
	public function updateObject($objectId, $properties = array()) {
		$path = '/drive/root:' . $objectId;
		$data = (object) $properties;
		$url = self::API_URL . $path;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			// SSL options.
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER => true,
			CURLOPT_CUSTOMREQUEST => "PATCH",
			CURLOPT_URL        => $url,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->_state->token->data->access_token
			),
			CURLOPT_POSTFIELDS => json_encode($data)
		));

		$content = curl_exec( $curl );
		curl_close ( $curl );
		return $this->get_headers_from_curl_response($content);



	}

	/**
	 * Moves an object into another folder.
	 *
	 * @param  (string) The unique ID of the object to move.
	 * @param  (null|string) The unique ID of the folder into which to move the
	 *         object, or null to move it to the OneDrive root folder. Default:
	 *         null.
	 */
	public function moveObject($objectId, $destinationId = null) {
		if (null === $destinationId) {
			$destinationId = 'me/skydrive';
		}

		$this->apiMove($objectId, array(
			'destination' => $destinationId
		));
	}

	/**
	 * Copies a file into another folder. OneDrive does not support copying
	 * folders.
	 *
	 * @param  (string) The unique ID of the file to copy.
	 * @param  (null|string) The unique ID of the folder into which to copy the
	 *         file, or null to copy it to the OneDrive root folder. Default:
	 *         null.
	 */
	public function copyFile($objectId, $destinationId = null) {
		if (null === $destinationId) {
			$destinationId = 'me/skydrive';
		}

		$this->apiCopy($objectId, array(
			'destination' => $destinationId
		));
	}


	public function deleteObject($objectId) {
		$objectId = '/drive/root:' . $objectId;
		$res = $this->apiDelete($objectId);
		return true;
	}


	public function fetchAccountInfo() {
		return $this->apiGet('/drive');
	}

}
