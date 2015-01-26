<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Currency_library extends ViewObject
{

    protected $__symbol;
    protected $__decimal_seperators;
    protected $__thousands_seperators;
    protected $__iso_country_code;
    protected $_setting;
    protected $__precision;

	public function __construct($params = [])
	{
        $this->_setting = Settings::get('shop_currency_mode');
        $this->__symbol = ['&#36;','&#128;','&#36;'];
        $this->__decimal_seperators = ['.',',','.'];
        $this->__thousands_seperators = [',','.',','];
        $this->__iso_country_code = ['AUD', 'EUR', 'USD'];
        $this->__precision = [2, 2, 2];        
	}

    public function sandbox()
    {
        return $this->__decimal_seperators[$this->_setting];
    }

    /**
     * [getCurrencySymbol description]
     * @return [type] [description]
     */
    public function getCurrencySymbol()
    {
        return $this->__symbol[$this->_setting];
    }

    /**
     * [getCurrencyDecimalSeperator description]
     * @return [type] [description]
     */
    public function getCurrencyDecimalSeperator()
    {
        return $this->__decimal_seperators[$this->_setting];
    }

    /**
     *
     * @return [type] [description]
     */
    public function getCurrencyThousandsSeperator()
    {
        return $this->__thousands_seperators[$this->_setting];
    }


    /**
     * Format can be 2 or 3 letter codes
     *
     * @param  string $format [description]
     * @return [type]         [description]
     */
    public function getCountryISOCode($param='')
    {
        return $this->__iso_country_code[$this->_setting];
    }


    public function getCurrencyPrecision($param='')
    {
        return $this->__precision[$this->_setting];
    }    


    public function format($price_value)
    {
        $symbol = $this->getCurrencySymbol();
        
        $price_value = number_format(
            $price_value,  
            $this->getCurrencyPrecision(), 
            $this->getCurrencyDecimalSeperator(),  
            $this->getCurrencyThousandsSeperator()
        );

        switch($this->__iso_country_code[$this->_setting])
        {
            case 'EUR':
                $final = $price_value.' '.$symbol;
                break;            
            case 'AUD':
            case 'USD':
            default:
                $final = $symbol.' '.$price_value;
                break;
        }
        return $final;
    }

}