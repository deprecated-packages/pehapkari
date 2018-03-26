<?php declare(strict_types=1);

namespace Pehapkari;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use setasign\Fpdi\Fpdi;

final class CertificateGenerator
{
    /**
     * @var string
     */
    private $outputDirectory;

    public function __construct(string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
        FileSystem::createDir($this->outputDirectory);

        // required for Fpdi?
        define('FPDF_FONTPATH', __DIR__ . '/../files/fonts');
    }

    public function generateForTrainingNameDateAndName(string $lectureName, string $date, string $name): void
    {
        $pdf = new Fpdi('l', 'pt');
        $pdf->AddPage('l');

        $pdf->AddFont('DejaVuSans','','DejaVuSans.php');
        $pdf->AddFont('Georgia','','Georgia.php');

        $pdf->setSourceFile(__DIR__ . '/../files/certificate.pdf');
        $tppl = $pdf->importPage(1);
        $pdf->useTemplate($tppl, 25, 0);

        $width = (int) $pdf->GetPageWidth();

        $this->addUserName($name, $pdf, $width);
        $this->addDate($date, $pdf, $width);
        $this->addTrainingName($lectureName, $pdf, $width);

        $pdf->Output(
            'F', // F = "file"
            $this->createDestinationForLecturNameAndUserName($lectureName, $name)
        );
    }

    private function addTrainingName(string $trainingName, Fpdi $pdf, int $width): void
    {
        $trainingName = iconv('UTF-8', 'windows-1250', $trainingName);
        $pdf->SetFont('DejaVuSans', '', 23);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(($width / 2) - ($pdf->GetStringWidth($trainingName) / 2), 350);
        $pdf->Write(0, $trainingName);
    }

    private function addDate(string $date, Fpdi $pdf, int $width): void
    {
        $date = iconv('UTF-8', 'windows-1250', $date);
        $pdf->SetFont('Georgia', '', 13);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(($width / 2) - ($pdf->GetStringWidth($date) / 2), 300);
        $pdf->Write(0, $date);
    }

    private function addUserName(string $name, Fpdi $pdf, int $width): void
    {
        $name = iconv('UTF-8', 'windows-1250', $name);
        $pdf->SetFont('Georgia', '', 32);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(($width / 2) - ($pdf->GetStringWidth($name) / 2), 260);
        $pdf->Write(0, $name);
    }

    /**
     * @param string $trainingName
     * @param string $name
     * @return string
     */
    private function createDestinationForLecturNameAndUserName(string $trainingName, string $name): string
    {
        return $this->outputDirectory . DIRECTORY_SEPARATOR . sprintf('%s-%s.pdf', Strings::webalize($trainingName), Strings::webalize($name));
    }
}
