<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;

class Main extends CI_Controller {
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $serverUrl = 'http://localhost:4444/wd/hub';

        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($serverUrl, $capabilities);

        $driver->get('https://www.google.com/');
        $searchBox = $driver->findElement(WebDriverBy::name('q'));
        $searchBox->click();
        $searchBox->sendKeys('타이드플로');
        $searchBox->sendKeys(WebDriverKeys::ENTER);
        $driver->manage()->timeouts()->implicitlyWait(10);
        $driver->switchTo()->frame($driver->findElement(WebDriverBy::xpath('//iframe[contains(@src, "recaptcha")]')));
        $checkbox = $driver->findElement(WebDriverBy::className('recaptcha-checkbox-border'));
        $checkbox->click();
        
        $driver->switchTo()->defaultContent();
        echo $driver->getTitle();

        //$driver->quit();
    }
}