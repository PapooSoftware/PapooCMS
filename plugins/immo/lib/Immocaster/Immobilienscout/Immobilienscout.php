<?php

/**
 * Immocaster SDK
 * Verbindung zur API von ImmobilienScout24.
 *
 * @package    Immocaster SDK
 * @author     Norman Braun (medienopfer98.de)
 * @link       http://www.immocaster.com
 */

class Immocaster_Immobilienscout
{

	/**
     * REST-URL die für ImmobilienScout24 genutzt werden soll.
     *
     * @var string
     */
	protected $_sUri = 'http://sandbox.immobilienscout24.de';
	
    /**
     * REST-Pfad der für ImmobilienScout24 genutzt werden soll.
     *
     * @var string
     */
	private $_sUriPath = 'restapi/api/';
	
    /**
     * REST-Pfad der für ImmobilienScout24 genutzt werden soll,
	 * bei 3-legged-oauth.
     *
     * @var string
     */
	private $_sUriPathSecurity = 'restapi/security/';
	
	/**
     * Typ der Authentifizierung die genutzt werden soll (z.B. oauth).
	 *
	 * @var string
     */
	private $_sAuthType = null;
	
	/**
     * Signaturmethode für die Nutzung des Service.
	 *
	 * @var object
     */
	protected $_oSignatureMethod = null;
	
	/**
     * Consumerobjekt für die Ausführung der Requests.
	 *
	 * @var object
     */
	protected $_oConsumer = null;
	
	/**
     * Key des Consumer.
	 *
	 * @var string
     */
	protected $_sConsumerKey = null;
	
	/**
     * Secret des Consumer.
	 *
	 * @var string
     */
	protected $_sConsumerSecret = null;
	
	/**
     * Verbindung zum Service aufbauen.
     *
	 * @param string $sKey Key für diesen Service
	 * @param string $sSecret Secret für diesen Service
	 * @param string $sAuth Typ der Authentifizierung für den Service
     * @return void
     */
    protected function connectService($sKey,$sSecret,$sAuth)
    {
		$this->_sConsumerKey = $sKey;
		$this->_sConsumerSecret = $sSecret;
		$this->_sAuthType = strtolower($sAuth);
		if($this->_sAuthType=='oauth')
		{
			$this->_oSignatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
			$this->_oConsumer = new OAuthConsumer($sKey,$sSecret,NULL);
		}
    }
	
	/**
     * Prüfen ob alle Pflichvariablen für eine Methode gesetzt sind.
	 *
	 * @param array Variablen die gesetzt sind
	 * @param array Variablen die benötigt werden
	 * @param string Name der Methode die geprüft wird
	 * @return boolean
     */
	protected function requiredArgs($aArgs,$aRequired,$sMethod)
	{
		foreach($aRequired as $sRequired)
		{
			if(!isset($aArgs[$sRequired]))
			{
				$sMethod = substr($sMethod,1,strlen($sMethod)-1);
				throw new Exception(sprintf(IMMOCASTER_SDK_LANG_PARAMETER_NOT_SET,$sMethod,$sRequired));
				return false;
			}
		}
		return true;
	}
	
	/**
     * Request per REST ausführen.
     *
	 * @param string $sPath Pfad zum Request
	 * @param array $aArgs Variablen für den Request
	 * @param boolean $bSecurity Wert, ob der Securitypfad genutzt werden soll (für 3-legged-oauth)
	 * @param object Requesttoken für 3-Legged-Oauth
	 * @param string $requestMethod HTTP Request Method ('GET','POST')
     * @return mixed
     */
	protected function restRequest($sPath='',$aArgs=array(),$bSecurity=false,$oToken=null,$requestMethod='GET')
	{
		if(!in_array($requestMethod,array('GET','POST','PUT','DELETE')))
		{
			$requestMethod = 'GET';
		}
		if($this->_sAuthType=='oauth')
		{
			$sSubPath = $this->_sUriPath;
			if($bSecurity==true)
			{
				$sSubPath = $this->_sUriPathSecurity;
			}
			//debug::print_d($this->_sUri.'/'.$sSubPath.$sPath);
			if($oToken!=null)
			{
				return OAuthRequest::from_consumer_and_token($this->_oConsumer,$oToken,$requestMethod,$this->_sUri.'/'.$sSubPath.$sPath,$aArgs);
			}
			return OAuthRequest::from_consumer_and_token($this->_oConsumer,NULL,$requestMethod,$this->_sUri.'/'.$sSubPath.$sPath,$aArgs);
		}
		return false;
	}
	
	/**
     * Content für aktuellen Request abfragen,
	 * ermitteln und zurückliefern.
	 *
	 * @param object oAuth Objekt
	 * @param string Secret des Accesstoken
	 * @return mixed
     */
	protected function getContent($req,$sSecret=null,$aHeader=array())
	{
		if($this->_sAuthType=='oauth')
		{
			if($req->get_normalized_http_method() == 'POST' || $req->get_normalized_http_method() == 'PUT')
			{
				$requestBody = $req->get_parameter('request_body');
				if($requestBody !== NULL)
				{
					$req->unset_parameter('request_body');
				}
			}
			$req->sign_request($this->_oSignatureMethod,$this->_oConsumer,NULL);
			$sNewHeader = $this->getContentRequestHeaderArray($req,$sSecret,$aHeader);
			if($this->_sUrlReadingType=='none')
			{
				$opts = array('http'=>array('ignore_errors'=>true,'header'=>implode("\r\n", $sNewHeader)));
				if($req->get_normalized_http_method() == 'POST')
				{
					$opts['http']['method'] = 'POST';
					$opts['http']['content'] = $requestBody;
				}
				$result = file_get_contents($req->to_url(),false,stream_context_create($opts));
			}
			if($this->_sUrlReadingType=='curl')
			{
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_HTTPHEADER,$sNewHeader);
				curl_setopt($ch,CURLOPT_URL,$req->to_url());
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
				if($req->get_normalized_http_method() == 'POST')
				{
					curl_setopt($ch,CURLOPT_POST,TRUE);
					curl_setopt($ch,CURLOPT_POSTFIELDS,$requestBody);
				}
				if($req->get_normalized_http_method() == 'PUT')
				{
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				    curl_setopt($ch,CURLOPT_POSTFIELDS,$requestBody);
				}
				if($req->get_normalized_http_method() == 'DELETE')
				{
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				}
				$result = curl_exec($ch);
				curl_close($ch);
			}
			return $result;
		}
		return false;
	}
	
	/**
	 * Header Array für aktuellen Request erstellen
	 *
	 * @param object oAuth Objekt
	 * @param string Secret des Accesstoken
	 * @return array
	 */
	protected function getContentRequestHeaderArray($req,$sSecret=null,$aHeader)
	{
		$sAccessTokenSignature = '';
		if($sSecret)
		{
			$sConsKey = rawurlencode($this->_sConsumerSecret).'&'.$sSecret;
			$sSignature = urlencode(base64_encode(hash_hmac('sha1',$req->get_signature_base_string(),$sConsKey,true)));
			$sAccessTokenSignature = ',oauth_signature_method="HMAC-SHA1",oauth_signature="'.$sSignature.'"';
		}
		$sNewHeader = array(
			$req->to_header().$sAccessTokenSignature,
			'User-Agent: '.IMMOCASTER_USER_AGENT
		);
		if($this->_sContentResultType=='json')
		{
			$sNewHeader[] = 'Accept: application/json';
		}
		if($req->get_normalized_http_method()=='POST' || $req->get_normalized_http_method()=='PUT')
		{
			// Request-Header (Content-Type)
			if(isset($aHeader['Content-Type']))
			{
				$sNewHeader[] = 'Content-Type: ' . $aHeader['Content-Type'];
				unset($aHeader['Content-Type']);
			}
			else
			{
				if ($this->_sContentRequestType=='json'){ $sRequestType = 'json'; }else{ $sRequestType = 'xml'; }
				$sNewHeader[] = 'Content-Type: application/'.$sRequestType.';charset=utf-8';
			}
			// Request-Header (Other)
			foreach($aHeader as $sKey=>$sVal)
			{
				$sNewHeader[] = $sKey.': '.$sVal;
			}
		}
		return $sNewHeader;
	}
	
	/**
	 * Body für den Export von Anhängen erstellen (MIME)
	 *
	 * @param string Boundary für den Body
	 * @param array Argumente mit Filename bzw. Filepath
	 * @return string
	 */
	protected function createAttachmentBody($sMimeBoundary,$aArgs)
	{
		$fp = fopen($aArgs['file'],'rb');
		$sFileContent = fread($fp,filesize($aArgs['file']));
		fclose ($fp);
		$aFileInfos = getimagesize($aArgs['file']);
		$sBreak = "\r\n";
		$sBody  = '--' . $sMimeBoundary . $sBreak;
		$sBody .= 'Content-Type: application/xml; name=body.xml' . $sBreak;
		$sBody .= 'Content-Transfer-Encoding: binary' . $sBreak;
		$sBody .= 'Content-Disposition: form-data; name="metadata"; filename="' . $aArgs['file'] . '"' . $sBreak . $sBreak;
		$sBody .= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $sBreak;
		$sBody .= '<common:attachment xsi:type="common:Picture" xmlns:common="http://rest.immobilienscout24.de/schema/common/1.0" ';
		$sBody .= 'xmlns:ns3="http://rest.immobilienscout24.de/schema/platform/gis/1.0" xmlns:xlink="http://www.w3.org/1999/xlink" ';
		$sBody .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $sBreak;
		$sBody .= '<title>'.$aArgs['title'].'</title>' . $sBreak;
		$sBody .= '<floorplan>'.$aArgs['floorplan'].'</floorplan>' . $sBreak;
		$sBody .= '<titlePicture>'.$aArgs['titlePicture'].'</titlePicture>' . $sBreak;
		$sBody .= '</common:attachment>' . $sBreak;
		$sBody .= '--' . $sMimeBoundary . $sBreak;
		$sBody .= 'Content-Type: '.$aFileInfos['mime'].'; name=' . $aArgs['file'] . $sBreak;
		$sBody .= 'Content-Transfer-Encoding: binary' . $sBreak;
		$sBody .= 'Content-Disposition: form-data; name="attachment"; filename="' . $aArgs['file'] . '"' . $sBreak . $sBreak;
		$sBody .= $sFileContent . $sBreak;
		$sBody .= "--" . $sMimeBoundary . "--" . $sBreak . $sBreak;
		return $sBody;
	}

}