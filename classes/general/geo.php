<?php
IncludeModuleLangFile(__FILE__);

class WSMMapOfficeGeo
{
    /**
     * @param null $options
     */
    public function __construct($options = null) {
		
		$this->dirname = dirname(__file__);
		
		// ip
		if(!isset($options['ip']) OR !$this->is_valid_ip($options['ip']))
			$this->ip = $this->get_ip(); 
		elseif($this->is_valid_ip($options['ip']))          
			$this->ip = $options['ip'];

		if(isset($options['charset']) && $options['charset'] && $options['charset']!='windows-1251')
			$this->charset = $options['charset'];
	}

    /**
     * @param bool $key
     * @param bool $cookie
     * @return array|string
     */
    function get_value($key = false, $cookie = true)
	{
		$key_array = array('inetnum', 'country', 'city', 'region', 'district', 'lat', 'lng');

		if(!in_array($key, $key_array))
			$key = false;            
		
		if($cookie && isset($_COOKIE['geobase']))
		{
			$data = unserialize($_COOKIE['geobase']);
		} 
		else
		{
			$data = $this->get_geobase_data();
			if(!$data || !is_array($data))
				$data = $this->get_geobase_data();

			if($data && is_array($data))
				setcookie('geobase', serialize($data), time()+3600*24*7);
		}  
		
		if($key)
			return (string)$data[$key];
		else
			return (array)$data;
	}

    /**
     * @return array|bool|string
     */
    function get_geobase_data()
	{
        if(!defined('CURLOPT_URL'))
            return false;

		$link = 'ipgeobase.ru:7020/geo?ip='.$this->ip;

        try
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
			$string = curl_exec($ch);    
		
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			if($http_code != 200)
				return false;
			
			if(curl_exec($ch) === false)
			{
				return curl_error($ch);
			}
			else
			{
				if($this->charset)       
					$string = iconv('windows-1251', $this->charset, $string);
				
				$data = $this->parse_string($string);
				return $data;
			}
		}
		catch(Exception $e)
		{
			echo 'exception: ',  $e->getMessage(), "\n";
			echo GetMessage("GEOIP_ERROR");
			return false;
		}
	}

    /**
     * @param $string
     * @return array
     */
    function parse_string($string)
	{
		$pa['inetnum'] = '#<inetnum>(.*)</inetnum>#is';
		$pa['country'] = '#<country>(.*)</country>#is';
		$pa['city'] = '#<city>(.*)</city>#is';
		$pa['region'] = '#<region>(.*)</region>#is';
		$pa['district'] = '#<district>(.*)</district>#is';
		$pa['lat'] = '#<lat>(.*)</lat>#is';
		$pa['lng'] = '#<lng>(.*)</lng>#is';
		$data = array();
		foreach($pa as $key => $pattern)
		{
			if(preg_match($pattern, $string, $out))
			{
				$data[$key] = trim($out[1]);
			}
		}
		return $data;
	}
	
	function get_ip()
	{
		$ip = false;
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipa[] = trim(strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ','));
		
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipa[] = $_SERVER['HTTP_CLIENT_IP'];       
		
		if (isset($_SERVER['REMOTE_ADDR']))
			$ipa[] = $_SERVER['REMOTE_ADDR'];
		
		if (isset($_SERVER['HTTP_X_REAL_IP']))
			$ipa[] = $_SERVER['HTTP_X_REAL_IP'];
		
		foreach($ipa as $ips)
		{
			if($this->is_valid_ip($ips))
			{                    
				$ip = $ips;
				break;
			}
		}
		return $ip;
		
	}
   
	function is_valid_ip($ip=null)
	{
		if(preg_match("#^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$#", $ip))
			return true;
		
		return false;
	}
	
}
