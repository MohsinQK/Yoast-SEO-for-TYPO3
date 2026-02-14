<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Crypto\HashService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class YoastRequestService
{
    public function __construct(
        protected HashService $hashService
    ) {}
    /**
     * @param array<string, mixed> $serverParams
     */
    public function isValidRequest(array $serverParams): bool
    {
        return isset($serverParams['HTTP_X_YOAST_PAGE_REQUEST'])
            && $serverParams['HTTP_X_YOAST_PAGE_REQUEST'] === $this->hashService->hmac(
                GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'),
                'yoast-seo-page-request'
            );
    }
}
