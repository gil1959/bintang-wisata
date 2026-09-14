<?php
$bpMeta = json_decode(file_get_contents('test_addons.log'), true);
$addonsResp = $bpMeta['addons_response']['addOns'] ?? [];
$getBaggageDesc = function($code) use ($addonsResp) {
    if (empty($code)) return $code;
    foreach ($addonsResp as $ao) {
        foreach ($ao['baggageInfos'] ?? [] as $bag) {
            if (($bag['code'] ?? '') === $code) return $bag['desc'] ?? $code;
        }
    }
    return $code;
};
echo $getBaggageDesc('PBAB');
