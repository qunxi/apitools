<?php
class BaseHttp{
    
    private $_timeout = 30;
    private $_connecttimeout = 30;
    private $_ssl_verifypeer = FALSE;
    private $_http_info;
    private $_useragent = '';
    private $_http_code;
    private $_request_url;
    private $_http_header;
    private $_postfields;
    private $_boundary;
    private $_debug = FALSE;
    
    public function __construct(){
    }
        
    public function http($url, $method, $parameters = NULL, $multi = FALSE, $headers = array()) {
		$this->_http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->_useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->_connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->_ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		$postfields = '';
        if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
				$postfields = http_build_query($parameters);
		} else {
				$postfields = $this->build_http_query_multi($parameters);
				$headers[] = "Content-Type: multipart/form-data; boundary=" . $this->_boundary;
		}
			
		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->_postfields = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
			case 'GET':
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$this->_http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->_http_info = array_merge($this->_http_info, curl_getinfo($ci));
		$this->_request_url = $url;
		//debug inforamtion
		if ($this->_debug) {
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo '=====info====='."\r\n";
			print_r( curl_getinfo($ci) );

			echo '=====$response====='."\r\n";
			print_r( $response );
		}
		curl_close ($ci);
		return $response;
	}
	
    protected function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->_http_header[$key] = $value;
		}
		return strlen($header);
	}
	
	protected function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		$this->_boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
}