<?php
/* 
the project created by @wizardloop                                                                                                                                                                                                                                                     
*/
namespace JewishPulse\Storage;

use Amp\File;
use function Amp\File\read;
use function Amp\File\write;
use function Amp\File\exists;

use Amp\ByteStream\Payload;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;

class UserLocation
{
    private static string $dataDir = __DIR__ . '/../data';

	public static function getGeonameId(int $senderId): string {
        $file = self::$dataDir . "/$senderId/location.json";
        
        if (!exists($file)) {
            return 281184;
        }

        $data = json_decode(read($file), true);

        return $data['geonameid'] ?? 281184;
    }

    public static function setGeonameId(int $userId, int $geonameId): void {
    $file = self::$dataDir . "/$userId/location.json";
    
    $data = [
        'geonameid' => $geonameId,
        'updated_at' => date('c')
    ];

    $userDir = dirname($file);
    if (!is_dir($userDir)) {
        mkdir($userDir, 0777, true);
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

    public static function searchGeoNames(string $query): array {
    try {
        $env = parse_ini_file(__DIR__ . '/../.env');
        $client = HttpClientBuilder::buildDefault();
        $username = $env['GEONAMES_USERNAME']; 
        $url = "http://api.geonames.org/searchJSON?q=" . urlencode($query) . "&maxRows=10&username=$username";
        $response = $client->request(new Request($url));
        $body = json_decode($response->getBody()->buffer(), true);

        return $body['geonames'] ?? [];
    } catch (\Throwable $e) {
        return [];
    }
}

    public static function getCandleSetting(int $senderId): int {
    $file = self::$dataDir . "/$senderId/candles.json";
    
    if (!file_exists($file)) {
        return 40; 
    }

    $data = json_decode(read($file), true);

    return $data['candles'] ?? 40; 
}

    public static function setCandleSetting(int $senderId, int $candlesMinutes): void {
    $file = self::$dataDir . "/$senderId/candles.json";
    
    $data = [
        'candles' => $candlesMinutes,
        'updated_at' => date('c')
    ];

    $userDir = dirname($file);
    if (!is_dir($userDir)) {
        mkdir($userDir, 0777, true);
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

    public static function getHavdalahSetting(int $senderId): float {
    $file = self::$dataDir . "/$senderId/havdalah.json";
    
    if (!file_exists($file)) {
        return 8.5; 
    }

    $data = json_decode(read($file), true);

    return $data['havdalah'] ?? 8.5; 
}

    public static function setHavdalahSetting(int $senderId, float $havdalahHours): void {
    $file = self::$dataDir . "/$senderId/havdalah.json";
    
    $data = [
        'havdalah' => $havdalahHours,
        'updated_at' => date('c')
    ];

    $userDir = dirname($file);
    if (!is_dir($userDir)) {
        mkdir($userDir, 0777, true);
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

}
