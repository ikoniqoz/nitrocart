<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Sandbox extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'sandbox';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		role_or_die('nitrocart', 'admin_develop');
	}

	/**
	 * List all items:load the dashboard
	 */
	public function index()
	{
		$output ='View shipping or currect test';
		//$this->data->items = & $items;
		$this->template->title($this->module_details['name'])
			->set('output',$output)
			->build('admin/sandbox/main');
	}

	/**
	 * The currency sandbox should display 1500.50 for AU and US.
	 * For Eu it should display 1.500,50
	 * @return [type] [description]
	 */
	public function currency()
	{

		$this->load->library('nitrocart/Currency_library');

		$output = '';

		$output .= $this->currency_library->getCurrencySymbol();
		$output .= '1';
		$output .= $this->currency_library->getCurrencyThousandsSeperator();
		$output .= '500';
		$output .= $this->currency_library->getCurrencyDecimalSeperator();
		$output .= '50';

		$output .= $this->currency_library->getCountryISOCode();


		$this->template->title($this->module_details['name'])
			->set('output',$output)
			->build('admin/sandbox/main');
	}

	public function shipping()
	{

		$this->load->library('nitrocart/packages_library');

		if($this->mycart->contents() != NULL)
			$output = $this->packages_library->pack( $this->mycart->contents() );

		$output = "<br><h1>Cart Items</h1><br/>".$this->packages_library->getTrace();

		$this->template->title($this->module_details['name'])
			->set('output',$output)
			->build('admin/sandbox/main');
	}

	public function cart()
	{

		$items = $this->mycart->contents();
		$output = '<pre>'.print_r($items,true).'</pre>';
		$this->template->title($this->module_details['name'])
			->set('output', $output)
			->build('admin/sandbox/cart',$items);
	}	


	public function canvas()
	{
		$this->load->library('nitrocart/Toolbox/Nc_string');
		$this->load->library('nitrocart/Toolbox/Nc_status');

        $status = new NCMessageObject(true,'Error Message');
        if(!$status->setMessage('New Error'))
        {
        	echo "failed";
        }
        echo $status->set('jubbie','are kool').br();
        echo $status->asJson().br();
        echo $status->asJsonReturn();
        //var_dump( $status);




		/*
		$bool = new NCBool(false);
		echo $bool->toNCString()->toLower();die;


		$array = new NCArray(['smack','apples','oranges']);

		echo $array
				->push('fruit')
				->push('greens')
				->push('carrots')->trimLeft()
				->toInlineString(StringSplitOptions::DelimitComma)
				->toNCWebString()
				->slugify();

		die;

 
		/*
		$a = new NCString("Hey John this is my string class..");

		echo $a->reverse()->toNCArray()->shuffle();
		die;
		*/

		/*
		$string = new NCString('hahah');
		echo 'the string is:'. NCString::$empty;
		echo 'the string is:'. $string::$empty;
		*/
		/*
		$array = new NCArray(['smack','apples','oranges']);
		echo ''.$array
				->push('fruit')
				->push('greens')
				->pop()
				->push('carrots')
				->pop()
				->removeIndex(1)
				->removeValue('fruit')
				->removeIndex(1)
				->reverse()
				//->keyExist('oranges');
				->toString();
				//->removeIndex($int);
		die;
		*/
		/*

		$array = new NCArray(['smack','apples','oranges']);
		echo ''.$array
				->push('fruit')
				->push('greens')
				->pop()
				->push('carrots')
				->pop()
				->toString();
		die;
		*/
		/*
		$string = new NCWebString("Panda");
		echo ''.$string->toLower()->subString(0,1).br();
		die;
		*/
		/*
		$price 		= new NCFloat(12.30455);
		echo $price.br();
		echo  	$price->ceil().br();
		echo  	$price->floor().br();
	
		
	*/

		/*

		echo  	$tax.br();
		echo  	$item_bt;

/*
		//echo $tax.br();
		//echo $item_bt;

		$string = new NCString("wonderful");
		echo $string->toNCArray()->trim().br();
		echo $string->toNCArray()->trimLeft().br();
		echo $string->toNCArray()->trimRight().br();
		echo $string->toNCArray().br();
	
		//echo $new_price->add($tax)->add($item_bt);
	
			die;
		*/
		//$tax 		= $price->subtract($discount)->pcent(0.10);
		//$subtotal 	= $price->add($tax)->subtract($discount);

			/*
		$price = new NCFloat(156.454384);
	
		echo  	$price->round(3,RoundingOptions::HalfUp).br();
		echo  	$price->round(3,RoundingOptions::HalfDown).br();		
		echo  	$price->round(3,RoundingOptions::HalfEven).br();
		echo  	$price->round(3,RoundingOptions::HalfOdd).br();
		echo  	$price->round(3,RoundingOptions::FullUp).br();
		echo  	$price->round(3,RoundingOptions::FullDown).br();		
	
*/

		//revwerse a string and then toarray
		//$string = new NCString("Hello World");
		//echo $string->toLower()->toArrayDebug();

	
		/*
		$some_string = "What, a w

			ffonder,ful 

			w|orld";

		$string = new NCString( $some_string );
		
		var_dump($string->toArray(StringSplitOptions::DelimitComma)).br();
		var_dump($string->toArray(StringSplitOptions::DelimitSpace)).br();
		var_dump($string->toArray(StringSplitOptions::None)).br();		
		var_dump($string->toArray(StringSplitOptions::DelimitLine));die;
		*/
	
		//echo NCString::Levenshtein('carreot',array('carrot','james'));

		//echo $theclone->toString().br();
		//
		//
		//echo $theclone->equals($string);
		//echo $string->debug();
		//echo $theclone->debug();


		/*
		$string_a = "Hello World";
		$string_b = new NCString("Hello World");
		echo $string_b.'<br/>';
		echo $string_b->serialize()->deSerialize()->toLower()->toUpper().'<br/>';
		echo $string_b->serialize()->subString(4,1)->serialize()->md5().'<br>';
		echo NCString::Wrap("coolio magnifico",5,true,'\n');
		var_dump($string_a);
		var_dump($string_b);
		echo die;
		*/
	}
}