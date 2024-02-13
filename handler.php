<?php

declare(strict_types=1);

ini_set('memory_limit', '2024M');

require './vendor/autoload.php';

use App\Converter;

/**
 * @throws Exception
 */
function handler($event, $context): array
{
    $objectId = $event['messages'][0]['details']['object_id'] ?? null;
    if ($objectId === null) {
        throw new Exception('Invalid event data, not found object id');
    }

    $priceDir = getenv('PRICES_FOLDER');
    $tempDir = getenv('TEMP_FOLDER');

    try {
        $loader = new Converter($priceDir, $tempDir, $objectId);
        $loader->run();
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

    return [
        'statusCode' => 200,
        'body' => '',
    ];
}
