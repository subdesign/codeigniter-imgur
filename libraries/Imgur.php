<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Imgur API wrapper for anonymous resources
 *
 * @package		Codeigniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Barna Szalai (sz.b@devartpro.com)
 * @copyright   Copyright (c) 2012, Barna Szalai
 * @link		https://github.com/subdesign/codeigniter-imgur
 * @license     MIT http://opensource.org/licenses/MIT
 */

class Imgur 
{
	protected $_ci;
	protected $_imgur_apikey   = '';
	protected $_imgur_baseurl  = 'http://api.imgur.com/2/';
	protected $_imgur_format   = '';
	protected $_imgur_xml_type = '';
	protected $_response;

	public function __construct()
	{
		$this->_ci =& get_instance();

		$this->_ci->load->spark('curl/1.2.1');
			
		$this->_ci->load->config('imgur');
		$this->_initialize();		
		
		log_message('debug', 'Imgur library started.');
	}

	private function _initialize()
	{
		$this->_response       = '';
		$this->_imgur_apikey   = $this->_ci->config->item('imgur_apikey');
		$this->_imgur_format   = ( ! $this->_ci->config->item('imgur_format')) ? 'json' : $this->_ci->config->item('imgur_format');
		$this->_imgur_xml_type = ( ! $this->_ci->config->item('imgur_xml_type')) ? 'object' : $this->_ci->config->item('imgur_xml_type');
	}

	public function move_image($params)
	{
		if($this->_run(__FUNCTION__, $params))
		{
			return $this->_response;
		}

		return FALSE;
	}	

	public function upload($params = array())
	{
		if(count($params))
		{
			// image required
			if( ! array_key_exists('image', $params))
			{
				return FALSE;
			}

			$params = array_merge(array('key' => $this->_imgur_apikey), $params);

			if($this->_run(__FUNCTION__, $params))
			{
				return $this->_response;
			}
		}
		
		return FALSE;		
	}

	public function stats($param = '')
	{
		$view_params = array('today', 'week', 'month');
		
		$param = ( ! in_array($param, $view_params) OR ! strlen($param)) ? array() : array('view' => $param);

		if($this->_run(__FUNCTION__, $param))
		{
			return $this->_response;
		}

		return FALSE;
	}

	public function album($id)
	{
		if($this->_run(__FUNCTION__, $id))
		{
			return $this->_response;
		}

		return FALSE;
	}
	
	public function image($hash)
	{
		if($this->_run(__FUNCTION__, $hash))
		{
			return $this->_response;
		}

		return FALSE;
	}

	public function delete($delete_hash)
	{
		if($this->_run(__FUNCTION__, $delete_hash))
		{
			return $this->_response;
		}

		return FALSE;
	}

	public function oembed($params = array())
	{
		if(count($params))
		{
			if( ! array_key_exists('url', $params))
			{
				return FALSE;
			}

			if($this->_run(__FUNCTION__, $params))
			{
				return $this->_response;
			}
		}	

		return FALSE;
	}	

	private function _run($method, $params)
	{
		$url = '';

		if($method === 'move_image')
		{
			if($params['edit'] === TRUE)
			{
				$url .= $this->_imgur_baseurl.'upload?edit&';
				unset($params['edit']);
			}
			else
			{
				$url .= $this->_imgur_baseurl.'upload?';	
				unset($params['edit']);
			}

			$url .= http_build_query($params, NULL, '&');

			$this->_ci->curl->create($url);
			$this->_ci->curl->options(array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_CONNECTTIMEOUT => 30));
		}		
		elseif($method === 'upload')
		{
			$url .= $this->_imgur_baseurl.$method. '.' .$this->_imgur_format;
			
			$this->_ci->curl->create('');						
			$this->_ci->curl->options(array(CURLOPT_URL => $url, CURLOPT_TIMEOUT => 30, CURLOPT_RETURNTRANSFER => 1));			
			$this->_ci->curl->post($params);			
		}
		else
		{
			if($method === 'oembed')
			{
				$this->_imgur_baseurl = substr($this->_imgur_baseurl, 0, -2);
			}
			// if $params is an array
			if(is_array($params))
			{
				// if there are params
				if(count($params))
				{
					// if format parameter is set, we use it instead of pre-set value
					if(array_key_exists('format', $params))
					{
						$url .= $this->_imgur_baseurl.$method.'?';		
					}
					else
					{
						$url .= $this->_imgur_baseurl.$method. '.' .$this->_imgur_format.'?';		
					}				
				}
				else
				{
					$url .= $this->_imgur_baseurl.$method. '.' .$this->_imgur_format;	
				}

				$url .= http_build_query($params, NULL, '&');
			}
			// else $params is a string param
			else
			{
				$url .= $this->_imgur_baseurl.$method.'/'.$params. '.' .$this->_imgur_format;			
			}

			$this->_ci->curl->create($url);
			$this->_ci->curl->options(array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_CONNECTTIMEOUT => 30));
		}
		
		if( $_response = $this->_ci->curl->execute())
		{
			switch($this->_imgur_format)
			{
				case 'json' :
					$this->_response = $_response;
				break;

				case 'xml' :
					// convert string response to xml object or array
					$simplexml = simplexml_load_string($_response, 'SimpleXMLElement', LIBXML_NOCDATA);

					if($this->_imgur_xml_type === 'array')
					{
						$this->_response = $this->_xml_to_array($simplexml);
					}
					else
					{
						$this->_response = $simplexml;
					}
				break;
			}			

			return TRUE;
		}

		log_message('error', $this->_ci->curl->error_string.' - cURL error code:'.$this->_ci->curl->error_code);
		return FALSE;
	}
	
	private function _xml_to_array($xmlstring)
	{		
		$json = json_encode($xmlstring);
		$array = json_decode($json,TRUE);
	
		return $array;
	}
}