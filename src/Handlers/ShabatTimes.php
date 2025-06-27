<?php
/* 
the project created by @wizardloop                                                                                                                                                                                                                                                     
*/
namespace JewishPulse\Handlers;

use JewishPulse\Locales\Lang;

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use DateTime;
use DateInterval;

use danog\MadelineProto\Wrappers\HTML;

class ShabatTimes
{

    public static function parseShabatData(int $geonameId, int $candles, int $havdalah, int $senderId, object $context): string {
    try {

$lang = (new Lang($context))->getUserLang($senderId);
$translate = (new Lang($context))->loadTranslations($lang); 
$settings_shabbat_times_1 = $translate['settings_shabbat_times_1'] ?? ' מעלות';
$settings_shabbat_times_2 = $translate['settings_shabbat_times_2'] ?? ' דקות';
$settings_shabbat_times_3 = $translate['settings_shabbat_times_3'] ?? ' דקות לפני השקיעה';
$settings_shabbat_times_4 = $translate['settings_shabbat_times_4'] ?? 'אזור:';
$settings_shabbat_times_5 = $translate['settings_shabbat_times_5'] ?? 'שבת';
$settings_shabbat_times_6 = $translate['settings_shabbat_times_6'] ?? 'זמני שבת הקרובה:';
$settings_shabbat_times_7 = $translate['settings_shabbat_times_7'] ?? 'כניסת שבת:';
$settings_shabbat_times_8 = $translate['settings_shabbat_times_8'] ?? 'יציאת שבת:';
$settings_shabbat_times_9 = $translate['settings_shabbat_times_9'] ?? 'פרשת השבוע:';
$settings_shabbat_times_10 = $translate['settings_shabbat_times_10'] ?? 'מברכים:';
$settings_shabbat_times_11 = $translate['settings_shabbat_times_11'] ?? 'חג:';
$settings_shabbat_times_12 = $translate['settings_shabbat_times_12'] ?? 'ראש חודש:';

$client = HttpClientBuilder::buildDefault();	
if($havdalah === 8){
$url = "https://www.hebcal.com/shabbat?cfg=json&geonameid=$geonameId&ue=off&b=$candles&M=on&lg=he-x-NoNikud&tgt=_top";
}else{
$url = "https://www.hebcal.com/shabbat?cfg=json&geonameid=$geonameId&ue=off&b=$candles&M=off&m=$havdalah&lg=he-x-NoNikud&tgt=_top";
}

if($havdalah === 8){
$havdalahtxt = "8.5".$settings_shabbat_times_1;
}else{
$havdalah = $havdalah;
$havdalahtxt = $havdalah.$settings_shabbat_times_2;
}
$candles = $candles-1;
$candlestxt = $candles.$settings_shabbat_times_3;
	
        $response = $client->request(new Request($url));
        $body = $response->getBody()->buffer();
        $json = json_decode($body, true);

        if (!$json || !isset($json['items'])) {
            return "⚠️ Unable to retrieve the Sabbath times for the location.";
        }

        $candles = null;
        $havdalah = null;
        $parasha = null;
        $holiday = null;
        $mevarchim = null;
        $roshChodesh = null; 

        foreach ($json['items'] as $item) {
            switch ($item['category']) {
                case 'candles':
                    $candles = $item;
                    break;
                case 'havdalah':
                    $havdalah = $item;
                    break;
                case 'parashat':
                    $parasha = $item;
                    break;
                case 'mevarchim':
                    $mevarchim = $item;
                    break;
                case 'holiday':
                    $holiday = $item;
                    break;
                case 'roshchodesh': 
                    $roshChodesh = $item;
                    break;
            }
        }

        $candleDate = isset($candles['date']) ? new \DateTime($candles['date']) : null;
        $candleTime = $candleDate?->format('H:i') ?? 'לא ידוע';
        $candleDay = $candleDate?->format('d/m/Y') ?? '---';

        $havdalahDate = isset($havdalah['date']) ? new \DateTime($havdalah['date']) : null;
        $havdalahTime = $havdalahDate?->format('H:i') ?? 'לא ידוע';
        $havdalahDay = $havdalahDate?->format('d/m/Y') ?? '---';

        $locationTitle = $json['location']['title'] ?? 'מיקום לא ידוע';

        $msg = "🌍 *$settings_shabbat_times_4* $locationTitle\n";
		$msg .= "🗓 $havdalahDay, $settings_shabbat_times_5\n\n";
        $msg .= "🕯️ *$settings_shabbat_times_6*\n";
        $msg .= "▪️ $settings_shabbat_times_7 *$candleTime* ($candlestxt)\n";
        $msg .= "▪️ $settings_shabbat_times_8 *$havdalahTime* ($havdalahtxt)\n";

        if ($parasha) {
            $msg .= "\n📖 *$settings_shabbat_times_9* " . $parasha['hebrew'];
        }

        if ($mevarchim) {
            $memo = isset($mevarchim['memo']) ? " ($mevarchim[memo])" : '';
            $msg .= "\n🌒 *$settings_shabbat_times_10* " . $mevarchim['hebrew'] . $memo;
        }

        if ($holiday) {
            $msg .= "\n🎉 *$settings_shabbat_times_11* " . $holiday['hebrew'];
        }

        if ($roshChodesh) {
            $msg .= "\n🌙 *$settings_shabbat_times_12* " . $roshChodesh['hebrew'];
        }

        return $msg;
    } catch (\Throwable $e) {
		echo $e;
        return "⚠️ Unable to retrieve the Sabbath times for the location.";
    }
}

    public static function getNextShabbatDates(int $geonameId, int $candles, int $havdalah, int $senderId, object $context): array {
		try {

$lang = (new Lang($context))->getUserLang($senderId);
$translate = (new Lang($context))->loadTranslations($lang); 
$settings_shabbat_times_1 = $translate['settings_shabbat_times_1'] ?? ' מעלות';
$settings_shabbat_times_2 = $translate['settings_shabbat_times_2'] ?? ' דקות';
$settings_shabbat_times_3 = $translate['settings_shabbat_times_3'] ?? ' דקות לפני השקיעה';
$settings_shabbat_times_4 = $translate['settings_shabbat_times_4'] ?? 'אזור:';
$settings_shabbat_times_5 = $translate['settings_shabbat_times_5'] ?? 'שבת';
$settings_shabbat_times_6 = $translate['settings_shabbat_times_6'] ?? 'זמני שבת הקרובה:';
$settings_shabbat_times_7 = $translate['settings_shabbat_times_7'] ?? 'כניסת שבת:';
$settings_shabbat_times_8 = $translate['settings_shabbat_times_8'] ?? 'יציאת שבת:';
$settings_shabbat_times_9 = $translate['settings_shabbat_times_9'] ?? 'פרשת השבוע:';
$settings_shabbat_times_10 = $translate['settings_shabbat_times_10'] ?? 'מברכים:';
$settings_shabbat_times_11 = $translate['settings_shabbat_times_11'] ?? 'חג:';
$settings_shabbat_times_12 = $translate['settings_shabbat_times_12'] ?? 'ראש חודש:';

$client = HttpClientBuilder::buildDefault();	
if($havdalah === 8){
$url = "https://www.hebcal.com/shabbat?cfg=json&geonameid=$geonameId&ue=off&b=$candles&M=on&lg=he-x-NoNikud&tgt=_top";
}else{
$url = "https://www.hebcal.com/shabbat?cfg=json&geonameid=$geonameId&ue=off&b=$candles&M=off&m=$havdalah&lg=he-x-NoNikud&tgt=_top";
}

if($havdalah === 8){
$havdalahtxt = "8.5".$settings_shabbat_times_1;
}else{
$havdalah = $havdalah-1;
$havdalahtxt = $havdalah.$settings_shabbat_times_2;
}
$candles = $candles-1;
$candlestxt = $candles.$settings_shabbat_times_3;
	
        $response = $client->request(new Request($url));
        $body = $response->getBody()->buffer();
        $json = json_decode($body, true);

        if (!$json || !isset($json['items'])) {
            return "⚠️ Unable to retrieve the Sabbath times for the location.";
        }

        $candles = null;
        $havdalah = null;
        $parasha = null;
        $holiday = null;
        $mevarchim = null;
        $roshChodesh = null; 

        foreach ($json['items'] as $item) {
            switch ($item['category']) {
                case 'candles':
                    $candles = $item;
                    break;
                case 'havdalah':
                    $havdalah = $item;
                    break;
                case 'parashat':
                    $parasha = $item;
                    break;
                case 'mevarchim':
                    $mevarchim = $item;
                    break;
                case 'holiday':
                    $holiday = $item;
                    break;
                case 'roshchodesh': 
                    $roshChodesh = $item;
                    break;
            }
        }

        $candleDate = isset($candles['date']) ? new \DateTime($candles['date']) : null;
        $candleTime = $candleDate?->format('H:i') ?? 'לא ידוע';
        $candleDay = $candleDate?->format('d/m/Y') ?? '---';

        $havdalahDate = isset($havdalah['date']) ? new \DateTime($havdalah['date']) : null;
        $havdalahTime = $havdalahDate?->format('H:i') ?? 'לא ידוע';
        $havdalahDay = $havdalahDate?->format('d/m/Y') ?? '---';

        $locationTitle = $json['location']['title'] ?? 'מיקום לא ידוע';

        $msg = "🌍 *$settings_shabbat_times_4* $locationTitle\n";
		$msg .= "🗓 $havdalahDay, $settings_shabbat_times_5\n\n";
        $msg .= "🕯️ *$settings_shabbat_times_6*\n";
        $msg .= "▪️ $settings_shabbat_times_7 *$candleTime* ($candlestxt)\n";
        $msg .= "▪️ $settings_shabbat_times_8 *$havdalahTime* ($havdalahtxt)\n";

        if ($parasha) {
            $msg .= "\n📖 *$settings_shabbat_times_9* " . $parasha['hebrew'];
        }

        if ($mevarchim) {
            $memo = isset($mevarchim['memo']) ? " ($mevarchim[memo])" : '';
            $msg .= "\n🌒 *$settings_shabbat_times_10* " . $mevarchim['hebrew'] . $memo;
        }

        if ($holiday) {
            $msg .= "\n🎉 *$settings_shabbat_times_11* " . $holiday['hebrew'];
        }

        if ($roshChodesh) {
            $msg .= "\n🌙 *$settings_shabbat_times_12* " . $roshChodesh['hebrew'];
        }
				
$candleDateTime = $candleDate ? $candleDate->format('d/m/Y H:i') : 'לא ידוע';
$havdalahDateTime = $havdalahDate ? $havdalahDate->format('d/m/Y H:i') : 'לא ידוע';			

$dateTime = DateTime::createFromFormat('d/m/Y H:i', $candleDateTime);
$dateTime->sub(new DateInterval('PT10M'));
$formattedTime = $dateTime->format('d/m/Y H:i');

$dateTime2 = DateTime::createFromFormat('d/m/Y H:i', $havdalahDateTime);
$dateTime2->sub(new DateInterval('PT0M'));
$formattedTime2 = $dateTime2->format('d/m/Y H:i');

        return [
'candleDateTime' => $formattedTime,
'havdalahDateTime' => $formattedTime2,
'candleDay' => $candleDay,
'message' => $msg	
        ];
		
        } catch (\Throwable $e) {
    return "⚠️ Unable to retrieve the Sabbath times for the location.";
    }
}

    public static function getDailyPage(int $geonameId, int $senderId): string {
    try {
        $date = new DateTime();
        $startDate = $date->format('Y-m-d'); 
        $endDate = $startDate; 

        $client = HttpClientBuilder::buildDefault();
        $url = "https://www.hebcal.com/hebcal?cfg=json&v=1&F=on&start=$startDate&end=$endDate&geonameid=$geonameId";

        $response = $client->request(new Request($url));
        $body = $response->getBody()->buffer();
        $json = json_decode($body, true);

        if (!$json || !isset($json['items'])) {
            return "⚠️ לא ניתן היה לשלוף את הדף היומי עבור המיקום.";
        }

        $msg = "📅 *דף יומי:* \n";
        $link = '';  
        foreach ($json['items'] as $item) {
            if (isset($item['category']) && $item['category'] == 'dafyomi') {
                // הוסף את תיאור היום
                $msg .= "▪️ " . $item['hebrew'] . " - " . $item['title'] . "\n";

                // הוסף את הקישור
                $link = $item['link'] ?? ''; // קישור לספריה
            }
        }

        if ($link) {
            $msg .= "\n🔗 קישור לדף יומי: [לחץ כאן]($link)";
        }

        return $msg;
    } catch (\Throwable $e) {
        return "⚠️ לא ניתן היה לשלוף את הדף היומי עבור המיקום.";
    }
}

}