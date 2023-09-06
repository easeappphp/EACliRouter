<?php

declare(strict_types=1);

namespace EACliRouter;

use EACliRouter\CliRouterInterface;
use function glob;
use function explode;
use function basename;
use function stripos;
use function is_array;
use function count;
use function mb_strlen;
use function filter_var;
use function implode;
//use const FILTER_SANITIZE_STRING;

/**
 * EACliRouter Class
 *
 */
 
class EACliRouter implements CliRouterInterface
{
	//private $routerInterface;
	private $routes = array();
	private $singleFileNameExploded = array();
	private $singleRouteFileContent;
	private $routingRuleLength = "";
	private $cliPathParamsCollected = array();
	private $cliPathParams = array();
	private $cliPathParamsCount = 0;
	//private $specificRouteParams = array();
	private $specificRouteParamsCount = 0;
	//private $specificRouteValueConstructed = array();
	private $specificRouteValueImploded = "";
	
	/**
	 * Accepts Extracted Routes Array
	 *
	 * @param array $routeArray
	 * @return array
	 */
	public function getAsArray($routeArray = array())
	{
		$this->routes = $routeArray;
		
		return $this->routes;
	}
	
	/**
	 * Gets the Routes Array from a Single Route File
	 *
	 * @param string $filePath
	 * @return array
	 */
	public function getFromSingleFile($filePath)
	{
		//$routes = require __DIR__.'/routing-engine-rules.php';
		$this->routes = require $filePath;
		return $this->routes;
	}

	/**
	 * Gets the Routes Array from Multiple Route Files, that resides in a Single Route Folder. This method will read only PHP Files with Route info in an array, and specifically does not read those Route files, that have spaces in the filename.
	 *
	 * @param string $folderPath
	 * @return array
	 */
	public function getFromSingleFolder($folderPath)
	{
		foreach (glob($folderPath . "/*.php") as $singleFilePath) {
			
			$this->singleRouteFileContent = "";
			
			$this->singleFileNameExploded = explode(".", basename($singleFilePath));
			
			if (stripos($this->singleFileNameExploded[0], " ") === false) {
				
				$this->singleRouteFileContent = require $singleFilePath;
				
				if (is_array($this->singleRouteFileContent) && count($this->singleRouteFileContent) > 0) {
					
					foreach($this->singleRouteFileContent as $key => $content){
						$this->routes[$key] = $content;
					}
				}
				
			}
			
		}
		
		return $this->routes;
	}

	/**
	 * Gets the Routes Array from Multiple Route Files, which list is provided as a numeric index array. This method will read only PHP Files with Route info in an array, from given paths.
	 *
	 * @param  array  $filepathsArray
	 * @return array
	 */
	public function getFromFilepathsArray($filepathsArray)
	{
		foreach ($filepathsArray as $singleFilePath) {
			
			$this->singleRouteFileContent = "";
			
			$this->singleFileNameExploded = explode(".", basename($singleFilePath));
			
			if (stripos($this->singleFileNameExploded[0], " ") === false) {
				
				$this->singleRouteFileContent = require $singleFilePath;
				
				if (is_array($this->singleRouteFileContent) && count($this->singleRouteFileContent) > 0) {
					
					foreach($this->singleRouteFileContent as $key => $content){
						$this->routes[$key] = $content;
					}
				}
				
			}
			
		}
		
		
		
		return $this->routes;
	}
	
	/**
	 * Match a Route, from Routes Array and based on provided URL Parameters.
	 *
	 * @param  array   $routesArray
	 * @param  string  $cliPath
	 * @param  string  $configuredMaxRouteLength
	 * @return array
	 */
	public function matchRoute($routesArray, $cliPath, $configuredMaxRouteLength)
	{
		//if (mb_strlen(filter_var($cliPath, FILTER_SANITIZE_STRING))<$configuredMaxRouteLength) {
		if (mb_strlen($cliPath)<$configuredMaxRouteLength) {	
			
			$this->cliPathParams = $this->getCliPathParams($cliPath);
			 
			
			$this->cliPathParamsCount = count($this->cliPathParams);
			
			foreach($routesArray as $key => $routeArray) {
				
				if (isset($routeArray['route_value'])) {
					
					$specificRouteParams = array();
					
					//Get Route Value example: /resume-name/:routing_eng_var_2
					$specificRouteParams = $this->getCliPathParams($routeArray['route_value']);
						
					$this->specificRouteParamsCount = count($specificRouteParams);
					
					if ($this->specificRouteParamsCount == $this->cliPathParamsCount) {
						
						if (stripos($routeArray['route_value'], ":routing_eng_var_") === FALSE) {
							
							$this->specificRouteValueImploded = $routeArray['route_value'];
							
						} else {
							
							$specificRouteValueConstructed = array();
							
							foreach($specificRouteParams as $k => $v) {
								
								if ($v != ":routing_eng_var_" . $k) {
									
									$specificRouteValueConstructed[] = $v;
									
								}  else {
									
									if (isset($this->cliPathParams[$k])) {
										
										$specificRouteValueConstructed[] = $this->cliPathParams[$k];
										
									} else {
										
										break;
										
									}
									
									
								} 
								
							}
							
							$this->specificRouteValueImploded = implode("/", $specificRouteValueConstructed);
						}
						
							
						if ($this->specificRouteValueImploded === $cliPath) {
							
							return [

								'matched_route_key' => $key,
								'matched_page_filename' => $routeArray['page_filename'],
								//'cli_process_exit_code' => 0
								
							];
							
						}
						
					}
					
				}
				
			}
			
		}
		
		return [
			
			'matched_route_key' => "not-found",
			'matched_page_filename' => "not-found.php",
			//'cli_process_exit_code' => 1
			
		];
	}
	
	/**
	 * Gets the URI Path Params (path of the url, before query string, in $_SERVER["REQUEST_URI"]), from URI Path input.
	 *
	 * @param  string  $cliPath
	 * @return array
	 */
	public function getCliPathParams($cliPath)
	{
		$this->cliPathParamsCollected = explode('/', $cliPath);
		
		return $this->cliPathParamsCollected;
	}
			
}
?>