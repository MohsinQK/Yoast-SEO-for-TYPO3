<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\Event\ModifyUrlForCanonicalTagEvent;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

class RecordCanonicalListener
{
    public function __construct(
        protected RecordService $recordService
    ) {}

    public function setCanonical(ModifyUrlForCanonicalTagEvent $event): void
    {
        $activeRecord = $this->recordService->getActiveRecord();
        if (!$activeRecord instanceof Record) {
            return;
        }

        $canonicalLink = $activeRecord->getRecordData()['canonical_link'] ?? '';
        if (empty($canonicalLink)) {
            return;
        }

        $event->setUrl(
            $this->getContentObjectRenderer()->typoLink_URL([
                'parameter' => $canonicalLink,
                'forceAbsoluteUrl' => true,
            ])
        );
    }

    protected function getContentObjectRenderer(): ContentObjectRenderer
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request instanceof ServerRequestInterface) {
            $cObj = $request->getAttribute('currentContentObject');
            if ($cObj instanceof ContentObjectRenderer) {
                return $cObj;
            }
        }
        return ($GLOBALS['TSFE'] ?? null)?->cObj ?? new ContentObjectRenderer();
    }
}
