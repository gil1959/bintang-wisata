<?php
$client = app(\App\Services\Darmawisata\DarmawisataClient::class);
$req = [
    "tripType" => "OneWay",
    "origin" => "SUB",
    "destination" => "CGK",
    "departDate" => "2026-05-24",
    "paxAdult" => 1,
    "paxChild" => 0,
    "paxInfant" => 0,
    "cacheType" => 2,
    "isShowEachAirline" => false,
];
$resp = $client->scheduleAllAirline($req);
$jtFlights = [];
foreach ($resp["journeyDepart"] ?? [] as $journey) {
    if ($journey["airlineID"] === "JT" && count($journey["segment"] ?? []) === 1 && count($journey["segment"][0]["flightDetail"] ?? []) === 1) {
        $detail = $journey["segment"][0]["flightDetail"][0];
        $jtFlights[] = $detail["airlineCode"] . " " . $detail["flightNumber"];
    }
}
echo "JT Non-Transit Flights (Operating Carrier):\n";
echo implode("\n", $jtFlights) . "\n";

