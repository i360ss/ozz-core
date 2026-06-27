<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class UserAgent {
  /**
   * User agent info (Separated)
   * @param boolean Return only the user-agent string if (true)
   */
  public function parse($string=false){

    if($string === true){
      return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (!$user_agent || strlen($user_agent) > 500) {
      return null;
    }

    $user_agent_info = [
      'all'             => $user_agent,
      'device'          => null,
      'os'              => null,
      'device_name'     => null,
      'os_version'      => null,
      'browser'         => null,
      'browser_version' => null,
    ];

    $device_list = [
      'Computer' => 'Windows|Mac|Linux|UNIX|BeOS|FreeBSD|OpenBSD|NetBSD|SunOS|Solaris|IRIX|HP-UX|AIX|OSF1|IOS',
      'Mobile' => 'Android|iPhone|iPad|iPod|BlackBerry|Windows Phone|SymbianOS|S60|Series60|Series40|Opera Mini|Opera Mobi|Nokia|SonyEricsson|Samsung|LG|HTC|Sony|Asus|Micromax|Palm|Vertu|Pantech|Fly|iMobile|SimValley|Ubuntu Touch|Windows CE|WindowsCE|Smartphone|Armv|Spice|Bird|ZTE|Alcatel|Lenovo|SonyEricsson|Ericsson|Bada|Meizu|Xolo|Lava|iOne|Celkon|Gionee|Vivo|Nexus|OnePlus|Yu|Acer|Xiaomi|OPPO|vivo|Coolpad|Wiko|Generic Smartphone',
      'Tablet' => 'iPad|Android|Windows Tablet|Kindle|PlayBook|Samsung Tablet|Galaxy Tab|Nexus 7|Nexus 10|Asus Tablet|Transformer|Lenovo|Acer|HP|Toshiba|Sony|Sony Tablet|Galaxy|Galaxy Tab|Xoom|Dell|Motorola|LG|Asus|Nook|Fonepad|Ainol|Nabi|Nexus|Sony Xperia Tablet|Iconia|IdeaTab|ThinkPad Tablet|Yoga Tablet|Zenpad|Xiaomi|Surface Pro|NuVision|Venue|Nexus 9|Nexus 7|Surface|Pixel C|Lenovo Yoga Tablet 2|Lenovo Yoga Tablet|Lenovo IdeaPad|Lenovo Miix|Lenovo ThinkPad',
    ];

    $os_list = [
      'Windows' => 'Windows NT|Windows NT 10.0|Windows NT 6.2|Windows NT 6.1|Windows NT 6.0|Windows NT 5.1|Windows NT 5.0|Windows 2000|Windows NT 4.0|Windows 98|Windows 95|Windows CE|Windows Phone|Windows',
      'Mac' => 'Mac OS X|Macintosh|Mac OS X 10.10|Mac OS X 10.9|Mac OS X 10.8|Mac OS X 10.7|Mac OS X 10.6|Mac OS X 10.5|Mac OS X 10.4|Mac OS X 10.3|Mac OS X 10.2|Mac OS X 10.1|Mac OS X 10.0',
      'Linux' => 'Linux|Red Hat|Fedora|Debian|Ubuntu|FreeBSD|OpenBSD|NetBSD|SunOS|Solaris|IRIX|HP-UX|AIX|OSF1|IOS',
      'UNIX' => 'UNIX',
      'BeOS' => 'BeOS',
      'iOS' => 'iPhone|iPad|iPod',
      'Android' => 'Android',
      'BlackBerry' => 'BlackBerry|BB10|RIM Tablet OS',
      'Symbian' => 'SymbianOS|S60|Series60|Series40',
      'Palm' => 'PalmOS',
      'Chrome OS' => 'CrOS'
    ];

    $browser_list = [
      'Chrome' => 'Chrome',
      'Firefox' => 'Firefox',
      'Safari' => 'Safari',
      'Opera' => 'Opera',
      'Edge' => 'Edge',
      'MSIE' => 'MSIE|IEMobile|MSIEMobile',
      'BlackBerry' => 'BlackBerry|BB10|RIM Tablet OS',
      'UC Browser' => 'UCBrowser',
      'Opera Mini' => 'Opera Mini',
      'Opera Mobi' => 'Opera Mobi',
      'Nokia' => 'Nokia',
      'SonyEricsson' => 'SonyEricsson',
      'Samsung' => 'Samsung',
      'LG' => 'LG',
      'HTC' => 'HTC',
      'Sony' => 'Sony',
      'Asus' => 'Asus',
      'Micromax' => 'Micromax',
      'Palm' => 'Palm',
      'Vertu' => 'Vertu',
      'Pantech' => 'Pantech',
      'Fly' => 'Fly',
      'iMobile' => 'iMobile',
      'SimValley' => 'SimValley',
      'Ubuntu Touch' => 'Ubuntu Touch',
      'Windows CE' => 'Windows CE',
      'WindowsCE' => 'WindowsCE',
      'Smartphone' => 'Smartphone',
      'Armv' => 'Armv',
      'Spice' => 'Spice',
      'Bird' => 'Bird',
      'ZTE' => 'ZTE',
      'Alcatel' => 'Alcatel',
      'Lenovo' => 'Lenovo',
      'SonyEricsson' => 'SonyEricsson',
      'Ericsson' => 'Ericsson',
      'Bada' => 'Bada',
      'Meizu' => 'Meizu',
      'Xolo' => 'Xolo',
      'Lava' => 'Lava',
      'iOne' => 'iOne',
      'Celkon' => 'Celkon',
      'Gionee' => 'Gionee',
      'Vivo' => 'Vivo',
      'Nexus' => 'Nexus',
      'OnePlus' => 'OnePlus',
      'Yu' => 'Yu',
      'Acer' => 'Acer',
      'Xiaomi' => 'Xiaomi',
      'OPPO' => 'OPPO',
      'Coolpad' => 'Coolpad',
      'Wiko' => 'Wiko',
      'Generic Smartphone' => 'Generic Smartphone'
    ];

    // check device
    foreach ($device_list as $device => $regex) {
      if(preg_match("/$regex/i", $user_agent, $match)){
        $user_agent_info['device'] = $device;
        $user_agent_info['device_name'] = $match[0];
        break;
      }
    }

    // check os
    foreach ($os_list as $os => $regex) {
      if(preg_match("/$regex/i", $user_agent, $base_matches)){
        $user_agent_info['os'] = $os;
        $user_agent_info['os_version'] = $base_matches[0];
        if(is_null($user_agent_info['os_version'])){
          if(preg_match("/$os (.*);/i", $user_agent, $matches)){
            $user_agent_info['os_version'] = $matches[1];
          }
        }
        break;
      }
    }

    // check browser
    foreach ($browser_list as $browser => $regex) {
      if(preg_match("/$regex/i", $user_agent)){
        $user_agent_info['browser'] = $browser;
        if(preg_match("/$browser\/(.*);/i", $user_agent, $matches)){
          $user_agent_info['browser_version'] = $matches[1];
        }
        break;
      }
    }

    return $user_agent_info;
  }
}