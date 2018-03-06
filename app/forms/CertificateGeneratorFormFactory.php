<?php

namespace App\Forms;

use Nette\Application\UI\Form;
use setasign\Fpdi\Fpdi;


final class CertificateGeneratorFormFactory
{

	/**
	 * @var FormFactory
	 */
	private $factory;


	public function __construct(FormFactory $factory)
	{
		$this->factory = $factory;
	}


	/**
	 * @return Form
	 */
	public function create()
	{
		$form = $this->factory->create();
		$form->addText('name', 'Jméno a příjmení:')
			->setRequired('Zadejte jméno a příjmení účastníka školení.')
			->setAttribute('placeholder', 'Prokop Buben');
		
		$form->addText('date', 'Datum')
			->setRequired('Zadejte datum školení')
			->setAttribute('placeholder', 'v lednu 2018');
		
		$form->addText('trainingName', 'Název školení')
			->setRequired('Zadejte název školení')
			->setAttribute('placeholder', 'Doctrine 2 - Začínáme používat ORM');

		$form->addSubmit('generate', 'Vygenerovat');

		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}


	public function processForm(Form $form, $values) {
		define('FPDF_FONTPATH', __DIR__ . '/files/fonts');

		$pdf = new Fpdi('l', 'pt');
		$pdf->AddPage('l');

		$pdf->AddFont('DejaVuSans','','DejaVuSans.php');
		$pdf->AddFont('Georgia','','Georgia.php');

		$pdf->setSourceFile(__DIR__ . '/files/certificate.pdf');
		$tppl = $pdf->importPage(1);
		$pdf->useTemplate($tppl, 25, 0);

		$width = $pdf->GetPageWidth();

		// name
		$name = iconv('UTF-8', 'windows-1250', $values['name']);
		$pdf->SetFont('Georgia', '', 32);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY(($width / 2) - ($pdf->GetStringWidth($name) / 2), 260);
		$pdf->Write(0, $name);

		// date
		$date = iconv('UTF-8', 'windows-1250', $values['date']);
		$pdf->SetFont('Georgia', '', 13);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY(($width / 2) - ($pdf->GetStringWidth($date) / 2), 300);
		$pdf->Write(0, $date);

		// training name
		$trainingName = iconv('UTF-8', 'windows-1250', $values['trainingName']);
		$pdf->SetFont('DejaVuSans', '', 23);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetXY(($width / 2) - ($pdf->GetStringWidth($trainingName) / 2), 350);
		$pdf->Write(0, $trainingName);

		$pdf->Output();
	}

}
