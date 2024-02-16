<?php

declare(strict_types=1);

namespace App;

use Aws\S3\S3Client;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

final readonly class Converter
{
    public function __construct(
        private string $priceDir,
        private string $tempDir,
        private string $objectId,
        private S3Client $s3 = new S3Client([
            'version' => 'latest',
            'endpoint' => 'https://storage.yandexcloud.net',
            'region' => 'ru-central1',
        ]),
    ) {
    }

    function run(): void
    {
        $csvObject = str_replace('xlsx', 'csv', $this->objectId);
        $csvObjectName = basename($csvObject);

        $this->convert($csvObjectName);

        $this->s3->putObject([
            'Bucket' => 'parts',
            'Key' => $csvObject,
            'SourceFile' => $this->tempDir . '/' . $csvObjectName,
        ]);
    }

    private function convert(string $csvObjectName): void
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $objPHPExcel = $reader->load($this->priceDir . '/' . $this->objectId);
        $worksheet = $objPHPExcel->getActiveSheet();

        $csvObjectResource = fopen($this->tempDir . '/' . $csvObjectName, 'w');
        foreach ($worksheet->toArray() as $i => $row) {
            if ($i === 0) {
                continue;
            }

            fputcsv($csvObjectResource, [$row[1], $row[3], $row[4], $row[0]], ';');
        }
        fclose($csvObjectResource);
    }
}
