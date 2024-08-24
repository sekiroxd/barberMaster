<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Detection\MobileDetect;

class DeviceRepository
{
    private $detect;
    private $user_agent;

    public function __construct($user_agent)
    {
        $this->user_agent = $user_agent;
        $this->detect = new MobileDetect();
    }

    public function get_device()
    {
        if ($this->detect->isTablet())
            return "Tablet";
        else if ($this->detect->isMobile())
            return "Mobile";
        else
            return "Desktop";
    }

    public function get_ip()
    {
        $ipServiceUrl = 'https://api.ipify.org?format=json';
        $response = file_get_contents($ipServiceUrl);
        $data = json_decode($response, true);
        return $data['ip'];
    }

    function get_os()
    {
        $osArray = [
            'Windows NT 10.0' => 'Windows 10',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows NT 6.0' => 'Windows Vista',
            'Windows NT 5.1' => 'Windows XP',
            'Mac OS X' => 'Mac OS X',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iOS',
            'Linux' => 'Linux',
            'Ubuntu' => 'Ubuntu'
        ];

        foreach ($osArray as $regex => $value) {
            if (strpos($this->user_agent, $regex) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }

    function get_browser()
    {
        $browsers = [
            'Opera' => 'Opera',
            'OPR/' => 'Opera',
            'Edge' => 'Edge',
            'Chrome' => 'Chrome',
            'Safari' => 'Safari',
            'Firefox' => 'Firefox',
            'MSIE' => 'Internet Explorer',
            'Trident/7' => 'Internet Explorer'
        ];

        foreach ($browsers as $browser => $name) {
            if (strpos($this->user_agent, $browser) !== false) {
                return $name;
            }
        }

        return 'Unknown';
    }
}
