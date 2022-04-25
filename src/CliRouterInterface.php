<?php

declare(strict_types=1);

namespace EACliRouter;

/**
 * CliRouterInterface
 *
 */

interface CliRouterInterface
{
    public function getAsArray(array $routeArray);
	public function getFromSingleFile(string $filePath);
	public function getFromSingleFolder(string $folderPath);
	public function getFromFilepathsArray(array $filepathsArray);
	public function matchRoute(array $routesArray, string $cliPath, string $configuredMaxRouteLength);
	public function getCliPathParams(string $cliPath);
}

?>