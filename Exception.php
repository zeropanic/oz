<?php

namespace Oz;

class Exception extends \Exception
{
	protected $reportWay  = self::REPORT_TEXT;

	protected $dateFormat = 'd-m-Y à H:i s';

	const REPORT_NOREPORT = 'noreport';

	const REPORT_TEXT     = 'textreport';
	
    const REPORT_MAIL     = 'mailreport';
	
    const REPORT_DB       = 'dbreport';

    public function __construct($message, $code = 0)
    {
        if (OZ_APP_ENV == 'dev') {
            parent::__construct($message, $code);
        }
    }

    final public function printOrReport()
    {
        if (OZ_APP_ENV == 'dev') {
            echo $this;
        } else {
            $this->report();
        }
    }

    public function __toString()
    {
    	$dateTime = new \DateTime();

    	$toString  = $dateTime->format($this->getDateFormat()).'s'.PHP_EOL;
    	$toString .= 'Exception class: '.get_class($this).PHP_EOL;
    	$toString .= 'Message: '.$this->getMessage().PHP_EOL;
    	$toString .= 'File: '.$this->getFile().PHP_EOL;
    	$toString .= 'Line: '.$this->getLine().PHP_EOL;
    	$toString .= 'Stack:'.PHP_EOL.$this->getTraceAsString().PHP_EOL;

    	return $toString;
    }

    public function report()
    {
    	//Si on est en dev et qu'il y a au moins une exception dans la liste
    	if(OZ_APP_ENV == 'dev')
        {
			//On s'assure que le block d'exception est initialisé
    		Fc::loadBlock('exception', Fc::DONT_EXECUTE_BLOCK);
    		//On ajoute l'erreur au bloc pour qu'elle s'affiche à l'écran
    		\blocks\exception::add($this->__toString());
    	}

        //selon la façon configurée on report l'exception afin d'en garder trace
    	switch ($this->getReportWay())
    	{
    		case static::REPORT_NOREPORT:
    			break;
    		
    		case static::REPORT_TEXT:
    			new Exception\Logger\Text($this);
    			break;

            case static::REPORT_DB:
                new Exception\Logger\Db($this);
                break;

    		default:
    			break;
    	}
    }

    public function setReportWay($reportWay)
    {
    	switch($reportWay)
    	{
    		case static::REPORT_NOREPORT:
    			$this->_reportWay = self::REPORT_NOREPORT;
    			break;

    		case static::REPORT_TEXT:
    			$this->_reportWay = self::REPORT_TEXT;
    			break;

    		case static::REPORT_MAIL:
    			$this->_reportWay = self::REPORT_MAIL;
    			break;

    		case static::REPORT_DB:
    			$this->_reportWay = self::REPORT_DB;
    			break;

    		default:
    			throw new Exception\UnexpectedValue('Unexpected value specified for $reportWay, REPORT_* constants expected.');
    			break;
    	}
    }

    public function getReportWay()
    {
    	return $this->reportWay;
    }

    public function setDateFormat($dateFormat)
    {
    	$this->dateFormat = $dateFormat;
    }

    public function getDateFormat()
    {
    	return $this->dateFormat;
    }
}

?>