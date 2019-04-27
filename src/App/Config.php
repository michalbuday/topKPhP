<?php
namespace App;

use \App\ConfigKeys as CONFIG;

 //TODO: Load config from file or environment server

class Configuration {
  /**
   * Get configutation for slim app instance
   * @param string $env
   * @return array
   */
  public function getAppConfig(String $env) {
    switch ($env) {
      case CONFIG::ENV_TEST:
        return $this->getTestAppConfig();
        break;
      case CONFIG::ENV_PROD:
        return $this->getProdAppConfig();
        break;
      default:
        return $this->getTestAppConfig();
    }
  }

  /**
   * Return test configuration
   * @return array
   */
  private function getTestAppConfig() {
    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $config['db']['host']   = 'localhost';
    $config['db']['user']   = 'root';
    $config['db']['pass']   = '';
    $config['db']['dbname'] = 'exampleapp';

    return $config;
  }

  /**
   * Return production configuration
   * @return array
   */
  private function getProdAppConfig() {
    $config = [];
    return $config;
  }
}