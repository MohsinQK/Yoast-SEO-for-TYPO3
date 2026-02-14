<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\View\TemplateView;

class PageLayoutHeaderRenderer
{
    public function render(): string
    {
        $renderingContext = GeneralUtility::makeInstance(RenderingContextFactory::class)->create();
        $renderingContext->getTemplatePaths()->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/PageLayout/Header.html')
        );

        $templateView = GeneralUtility::makeInstance(TemplateView::class, $renderingContext);
        $templateView->assignMultiple([
            'targetElementId' => uniqid('_YoastSEO_panel_'),
        ]);
        return $templateView->render();
    }
}
