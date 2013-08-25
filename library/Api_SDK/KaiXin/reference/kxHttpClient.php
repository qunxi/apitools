<?php

/*
 * 实现网络连接
 */
class KXHttpClient{
	
	/* Contains the last HTTP status code returned. */
	public $http_code;

	/* Contains the last API call. */
	public $url;

	/* Set timeout default. */
	public $timeout = 30;

	/* Set connect timeout. */
	public $connecttimeout = 30; 

  /* Verify SSL Cert. */
  public $ssl_verifypeer = FALSE;
  
  /* Respons format. */
  public $format = 'json';
  
  /* Decode returned json data. */
  public $decode_json = TRUE;
  
  /* Contains the last HTTP headers returned. */
  public $http_info;
  
  /* Set the useragnet. */
  public $useragent = 'KX PHPSDK API v2.0.1';
  
 
	public function get($url, $params = array())
	{
		$url .= "?".OAuthUtil::build_http_query($params);
		$response = $this->http($url,'GET');	
    	if ($this->format === 'json' && $this->decode_json) {
      		return json_decode($response);
    	}
	}

	function post($url, $params = array(), $multi = false) {
		$query = "";
		if($multi)
			$query = OAuthUtil::build_http_query_multi($params);
		else 
			$query = OAuthUtil::build_http_query($params);
	    $response = $this->http($url,'POST',$query,$multi);
	    if ($this->format === 'json' && $this->decode_json) {
	      return json_decode($response);
	    }
	    return $response;
	}
  
	
 /**
   * Make an HTTP request
   *
   * @return API results
   */
  function http($url, $method, $postfields = NULL, $multi = false) {
        $this->http_info = array(); 
        $ci = curl_init(); 
        /* Curl settings */ 
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent); 
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout); 
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 
    	curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer); 
        curl_setopt($ci, CURLOPT_HEADER, FALSE); 

        switch ($method) { 
        case 'POST': 
            curl_setopt($ci, CURLOPT_POST, TRUE); 
            if (!empty($postfields)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields); 
            } 
            break; 
        case 'DELETE': 
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
            if (!empty($postfields)) { 
                $url = "{$url}?{$postfields}"; 
            } 
        } 

        if( $multi ) 
        {
        	$header_array = array("Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary , "Expect: ");
        	curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array ); 
        	curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE ); 
        }

        curl_setopt($ci, CURLOPT_URL, $url); 
        $response = curl_exec($ci); 
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE); 
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci)); 
        $this->url = $url; 
        curl_close ($ci); 
        return $response; 
  }
  
}

class OAuthUtil {
	public static $boundary = '';
	
  public static function urlencode_rfc3986($input) {
  if (is_array($input)) {
    return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input);
  } else if (is_scalar($input)) {
    return str_replace(
      '+',
      ' ',
      str_replace('%7E', '~', rawurlencode($input))
    );
  } else {
    return '';
  }
}


  // This decode function isn't taking into consideration the above
  // modifications to the encoding process. However, this method doesn't
  // seem to be used anywhere so leaving it as is.
  public static function urldecode_rfc3986($string) {
    return urldecode($string);
  }


 public static function build_http_query($params) {
    if (!$params) return '';

    // Urlencode both keys and values
    $keys = OAuthUtil::urlencode_rfc3986(array_keys($params));
    $values = OAuthUtil::urlencode_rfc3986(array_values($params));
    $params = array_combine($keys, $values);

    // Parameters are sorted by name, using lexicographical byte value ordering.
    // Ref: Spec: 9.1.1 (1)
    uksort($params, 'strcmp');

    $pairs = array();
    foreach ($params as $parameter => $value) {
      if (is_array($value)) {
        // If two or more parameters share the same name, they are sorted by their value
        // Ref: Spec: 9.1.1 (1)
        natsort($value);
        foreach ($value as $duplicate_value) {
          $pairs[] = $parameter . '=' . $duplicate_value;
        }
      } else {
        $pairs[] = $parameter . '=' . $value;
      }
    }
    // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
    // Each name-value pair is separated by an '&' character (ASCII code 38)
    return implode('&', $pairs);
  }
  

  public static function build_http_query_multi($params) { 
        if (!$params) return ''; 

        // Urlencode both keys and values 
        $keys = array_keys($params);
        $values = array_values($params); 
        $params = array_combine($keys, $values); 

        // Parameters are sorted by name, using lexicographical byte value ordering. 
        // Ref: Spec: 9.1.1 (1) 
        uksort($params, 'strcmp'); 
        $pairs = array(); 
        self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

        foreach ($params as $parameter => $value) 
        { 
	        if( in_array($parameter,array("pic","image")) && $value{0} == '@' )
	        {
	        	$url = ltrim( $value , '@' );
	        	$content = file_get_contents( $url );
	        	$filename = reset( explode( '?' , basename( $url ) ));
	        	$mime = self::get_image_mime($url); 
	        	
	        	$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= 'Content-Type: '. $mime . "\r\n\r\n";
				$multipartbody .= $content. "\r\n";
	        }
	        else
	        {
	        	$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="'.$parameter."\"\r\n\r\n";
				$multipartbody .= $value."\r\n";			
	        }      
        } 
        $multipartbody .=  $endMPboundary;
        return $multipartbody; 
  
    } 
 public static function get_image_mime( $file )
    {
    	$ext = strtolower(pathinfo( $file , PATHINFO_EXTENSION ));
    	switch( $ext )
    	{
    		case 'jpg':
    		case 'jpeg':
    			$mime = 'image/jpg';
    			break;
    		 	
    		case 'png';
    			$mime = 'image/png';
    			break;
    			
    		case 'gif';
    		default:
    			$mime = 'image/gif';
    			break;    		
    	}
    	return $mime;
    }  
}