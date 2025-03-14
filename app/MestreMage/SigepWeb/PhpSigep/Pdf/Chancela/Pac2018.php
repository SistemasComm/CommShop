<?php
namespace MestreMage\SigepWeb\PhpSigep\Pdf\Chancela;
use MestreMage\SigepWeb\PhpSigep\Pdf\ImprovedFPDF;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\AbstractChancela;

class Pac2018 extends AbstractChancela
{
    public function draw(ImprovedFPDF $pdf)
    {
        $pdf->saveState();
        // Desenha o retangulo
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $x = $this->x;
        $y = $this->y;
        //$pdf->RoundedRect($x, $y, $wRect, $h, 5);
        $pdf->Circle($x, $y, 10, 'F');

        $pdf->restoreLastState();
    }
}