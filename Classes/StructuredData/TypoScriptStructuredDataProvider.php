<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class TypoScriptStructuredDataProvider implements StructuredDataProviderInterface
{
    public function __construct(
        protected SiteFinder $siteFinder,
        protected PageRepository $pageRepository,
        protected TypoScriptService $typoScriptService,
    ) {}

    /**
     * @return array<int, array<int|string, mixed>>
     */
    public function getData(): array
    {
        $data = [];
        $structuredDataConfig = $this->getStructuredDataConfig();
        $contentObjectRenderer = $this->getContentObjectRenderer();

        foreach ($structuredDataConfig as $dataConfig) {
            if (!isset($dataConfig['type'], $dataConfig['context'])) {
                continue;
            }

            $item = [];
            $config = $this->typoScriptService->convertTypoScriptArrayToPlainArray($dataConfig);

            foreach ($config as $key => $value) {
                $cObject = $key . '.';
                if (isset($dataConfig[$cObject]) && $contentObjectRenderer !== null) {
                    $value = $contentObjectRenderer->stdWrap((string)$key, $dataConfig[$cObject]);
                }
                $key = in_array($key, ['type', 'context']) ? '@' . $key : $key;

                $item[$key] = $value;
            }
            $data[] = $item;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function getStructuredDataConfig(): array
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request !== null) {
            $typoScript = $request->getAttribute('frontend.typoscript');
            if ($typoScript !== null) {
                $configArray = $typoScript->getConfigArray();
                return $configArray['structuredData.']['data.'] ?? [];
            }
        }
        return ($GLOBALS['TSFE'] ?? null)?->config['config']['structuredData.']['data.'] ?? [];
    }

    private function getContentObjectRenderer(): ?ContentObjectRenderer
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request !== null) {
            $cObj = $request->getAttribute('currentContentObject');
            if ($cObj instanceof ContentObjectRenderer) {
                return $cObj;
            }
        }
        return ($GLOBALS['TSFE'] ?? null)?->cObj;
    }
}
