<?php

namespace App\Service;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class Minio
{
    private $s3Client;
    private $bucketName;

    public function __construct(string $endpoint, string $accessKey, string $secretKey, string $bucketName)
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1', 
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
        ]);

        $this->bucketName = $bucketName;
    }

    public function uploadFile($file)
    {
        $imageId = uniqid().'.'.$file->guessExtension();
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $imageId,
                'Body'   => fopen($file->getPathname(), 'r'),
                'ACL'    => 'public-read', 
            ]);

            return $result['ObjectURL']; 
        } catch (AwsException $e) {
            return null;
        }
    }
}
