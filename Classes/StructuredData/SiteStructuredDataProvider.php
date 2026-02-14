<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\StructuredData;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Site\SiteFinder;

class SiteStructuredDataProvider implements StructuredDataProviderInterface
{
    /** @var array<string, mixed> */
    protected array $configuration = [];

    public function __construct(
        protected SiteFinder $siteFinder,
        protected PageRepository $pageRepository,
    ) {}

    /**
     * @return array<array<string, mixed>>
     */
    public function getData(): array
    {
        $page = $this->getCurrentPage();
        if ($page === null || (int)($page['is_siteroot'] ?? 0) !== 1) {
            return [];
        }
        return [
            [
                '@context' => 'https://www.schema.org',
                '@type' => 'WebSite',
                'url' => $this->getUrl((int)$page['uid']),
                'name' => $this->getName((int)$page['uid']),
            ],
        ];
    }

    protected function getUrl(int $pageId): string
    {
        $site = $this->siteFinder->getSiteByPageId($pageId);
        return (string)$site->getBase();
    }

    protected function getName(int $pageId): string
    {
        $rootPageRecord = $this->pageRepository->getPage($pageId);
        return $rootPageRecord['seo_title'] ?: $rootPageRecord['title'] ?: $this->getUrl($pageId);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getCurrentPage(): ?array
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if ($request !== null) {
            $pageInformation = $request->getAttribute('frontend.page.information');
            if ($pageInformation !== null) {
                return $pageInformation->getPageRecord();
            }
        }
        return ($GLOBALS['TSFE'] ?? null)?->page;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
