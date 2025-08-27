<?php
declare(strict_types=1);

namespace Amasty\Oaction\Model\Command\Pdf\PdfCollector;

interface PdfCollectorInterface
{
    /**
     * @param \Zend_Pdf[] $pdfs
     * @return PdfCollectorInterface
     */
    public function collect(array $pdfs): PdfCollectorInterface;

    public function getExtension(): string;

    public function render(): string;

    public function hasResponse(): bool;

    public function useSeparateFiles(): bool;

    public function isAvailable(): bool;
}
