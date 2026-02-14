<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3Fluid\Fluid\View\TemplateView;

class NodeTemplateService
{
    /**
     * @param array<string, mixed> $data
     */
    public function renderView(string $template, array $data = []): string
    {
        $renderingContext = GeneralUtility::makeInstance(RenderingContextFactory::class)->create();
        $templatePaths = $renderingContext->getTemplatePaths();
        $templatePaths->setPartialRootPaths(
            [GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Partials/TCA')]
        );
        $templatePaths->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName('EXT:yoast_seo/Resources/Private/Templates/TCA/' . $template . '.html')
        );

        $templateView = GeneralUtility::makeInstance(TemplateView::class, $renderingContext);
        $templateView->assignMultiple($data);
        return $templateView->render();
    }
}
