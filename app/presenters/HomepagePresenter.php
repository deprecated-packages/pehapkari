<?php

namespace App\Presenters;

use App\Forms\CertificateGeneratorFormFactory;
use Nette\Application\UI\Form;


final class HomepagePresenter extends BasePresenter
{

	/**
	 * @var CertificateGeneratorFormFactory
	 */
	private $certificateGeneratorFormFactory;


	public function __construct(CertificateGeneratorFormFactory $certificateGeneratorFormFactory)
	{
		$this->certificateGeneratorFormFactory = $certificateGeneratorFormFactory;
	}


	protected function createComponentCertificateGeneratorForm(): Form
	{
		return $this->certificateGeneratorFormFactory->create();
	}

}
