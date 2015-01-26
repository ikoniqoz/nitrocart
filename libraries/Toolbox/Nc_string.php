<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
final class StringSplitOptions {
	const DelimitComma 	= ',';
	const DelimitMinus 	= '-';	
	const DelimitLine 	= '/\n|\r\n?/';	
	const DelimitPipe 	= '|';
	const DelimitSpace 	= ' ';	
	const None 			= false;	
}
final class RoundingOptions {
	const HalfUp 		= PHP_ROUND_HALF_UP;
	const HalfDown 		= PHP_ROUND_HALF_DOWN;	
	const HalfEven 		= PHP_ROUND_HALF_EVEN;	
	const HalfOdd 		= PHP_ROUND_HALF_ODD;
	const FullUp 		= 'Ceil';	
	const FullDown 		= 'Floor';	
}
/*http://php.net/manual/en/language.oop5.magic.php*/
class Nc_object
{
	protected $_type ='ncobject';
	protected $_value;
	protected $_id;	

	public function __construct( $object='', $options='',$type='ncobject')
	{
		$this->_type = $type;
		$this->_value = $object;
		$this->_id = time().'-'.rand(10,99) . '-' . substr(md5($options),5,10);		
	} 
	public function __call($method, $args) {
		print "Method $method called:\n";
		var_dump($args);
		return $this->x;
	}	
	/*The __invoke() method is called when a script tries to call an object as a function. */
 	public function __invoke($x)
    {
        var_dump($x);
    }	
    public function __toString()
    {
        return (string) $this->_value;
    }	
    public function __debugInfo() 
    {
        return [
            '_type' => $this->_type,        
            '_value' => $this->_value,
            '_id' => $this->_id,
        ];
    } 
	public function serialize()
	{
		return new NCString( serialize($this->_value) );
	}	

	public function deserialize()
	{
		return new NCString(  unserialize( $this->_value  ) );
	}	    	 		
}

class Nc_bool extends Nc_object
{
	public function __construct($value,$options='')
	{
		$value = (is_bool($value))?$value:false;
		parent::__construct( $value, $options,'boolean');	
	} 
 	public function __invoke($x)
    {
        return $x->_value;
    }
    public function __toString()
    {
        return (string) '<pre>'. print_r($this->_value,true).'</pre>';
    }	    
}
class NCBool extends Nc_bool
{
	public function toString()
	{
		return ($this->_value)?'true':'false';
	}
	public function toNCString()
	{
		return new NCString($this->toString());
	}
}

class Nc_array extends Nc_object
{
	static $_popped;

	public function __construct($value=[],$options='')
	{
		$value = $this->myGetType($value);
		parent::__construct( (array) $value, $options,'array');	
	} 
    private function myGetType($var)
    {
        if (is_array($var)) return $var;
        return [$var];
    }
 	public function __invoke($x)
    {
        return (array)$x->_value;
    }
    public function __toString()
    {
        return (string) '<pre>'. print_r($this->_value,true).'</pre>';
    }	    
}

class NCArray extends Nc_array
{
	
    public function toString()
    {
        return $this->__toString();
    }	

    public function toInlineString($space=StringSplitOptions::DelimitSpace)
    {
    	$string_build = '';
    	foreach($this->_value as $v) $string_build .= $v.$space;
    	return new NCString($string_build);
    }


    public function push($item)
    {	
    	$copy = new NCArray( $this->copy() );   
    	$copy->_value[] = $item;
    	return new NCArray( $copy->_value );
    }

    public function pop()
    {	
   		$copy = new NCArray( $this->copy() );       	
    	self::$_popped = array_pop( $copy );
    	return new NCArray( $copy );
    } 

    public function getPopped()
    {
    	if(self::$_popped!=NULL)
    		return self::$_popped;
    	return '';
    }

    /**
     * remove frst and last elem
     * @return [type] [description]
     */
    public function trim()
    {	
    	$copy = new NCArray( $this->copy() );   
    	$copy = $copy->trimLeft()->trimRight();
    	return new NCArray( $copy->_value );
    }  

    public function trimLeft()
    {	
    	$copy = new NCArray( $this->copy() );   
    	array_shift($copy->_value);   	
    	return new NCArray($copy->_value);
    }  

    public function trimRight()
    {	
    	$copy = new NCArray( $this->copy() );   
    	array_pop($copy->_value);   	    	
    	return new NCArray($copy->_value);
    }  

    public function toJson()
    {
    	return json_encode($this->_value);
    }  

    public function count()
    {
    	return count($this->_value);
    } 

    public function removeIndex($int)
    {
    	$copy = $this->copy();
    	unset($copy[$int]);
    	return new NCArray( $copy );
    }  

    public function removeValue($value)
    {
    	$copy = array_diff($this->_value, array($value));
    	return new NCArray( $copy );
    }   

    public function reverse()
    {
    	$copy = $this->copy();
    	$preserve_keys = false;
    	return new NCArray( array_reverse($copy, $preserve_keys) );
    } 

    public function keyExist($key)
    {
		return (in_array($key, $this->_value))?true:false;
    }  

    public function shuffle()
    {
    	$copy = $this->copy();
    	shuffle ( $copy );
    	return new NCArray($copy);
    } 

    public function copy()
    {
		$copy = (array) $this->_value;
		return $copy;
    }   

    public function value()
    {
    	return (array) $this->_value;
    } 
    public function iterator()
    {
        return (array)$this->_value;
    }  
}

class Nc_string extends Nc_object
{
	public function __construct($string='',$options='')
	{
		parent::__construct( (string) $string, $options,'string');	
	}  	 
}

class NCFloat extends Nc_object
{

	public function __construct($value=0.0,$options='')
	{
		parent::__construct( (float) $value, $options,'float');	
	} 

 	public function __invoke($x)
    {
        return (float)$x->_value;
    }

	public function add($value)
	{
		$a = $this->_value;
		$b = $this->tryGetObjectValue($value,0);
		$r = $a + $b;			
		return new NCFloat($r);
	}

	public function subtract($value)
	{
		$a = $this->_value;
		$b = $this->tryGetObjectValue($value,0);
		$r = $a - $b;
		return new NCFloat( $r );
	}

	private function tryGetObjectValue($value,$alt=0)
	{
		if( is_object($value) )
		{
			if(get_class($value)==='NCFloat') 
			{
				return $value->value();
			}
			else
			{
				return (float) $value;
			}
		}
		return $alt; 
	}

	public function pcent($percentage)
	{
		return new NCFloat((float)$this->_value * (float) $percentage / 100 );
	}
	/**
	 * taxinc is aninteresting function.
	 * Similar to pcent it yeilds the percent value request but as if the value was included prior
	 * ex:$100 for a car
	 * the car is 10% includes tax
	 * This is not $10 of tax, but 9.9 and 90.1 value of item 
	 * @param  [type] $percentage [description]
	 * @return [type]             [description]
	 */
	public function taxinc($percentage)
	{
		//get the amount due on tax
		$tax_due = $this->_value / (1 + $percentage );
		$tax_due = ($this->_value - $tax_due);
		return new NCFloat((float) $tax_due );
	}
	public function pcentIncrement($percentage)
	{
		$pv = (float)$this->_value * (float) $percentage / 100;
		return new NCFloat((float)$this->_value + $pv );
	}	

	public function pcentDecrement($percentage)
	{
		$pv = (float)$this->_value * (float) $percentage / 100;
		return new NCFloat((float)$this->_value - $pv );
	}

	public function toInt()
	{
		return (int) $this->_value;
	}

    public function __toString()
    {
        return (string) (float)$this->_value;
    }	

    /**
     * Get the raw float/numeric value 
     * @return [type] [description]
     */
    public function value()
    {
        return (float)$this->_value;
    }  
    public function toStringNumber($decimals = 0,$dec_point = ".", $thousands_sep = "," )
    {
    	return number_format( $this->_value , $decimals , $dec_point  ,  $thousands_sep  );
    }  	

    public function abs()
    {
    	return new NCFloat(abs( $this->_value ));
    }
    public function squareRoot()
    {
    	return new NCFloat(sqrt( $this->_value ));
    }

    public function round($precision = 2, $mode = RoundingOptions::HalfUp)
    {
    	switch($mode)
    	{
    		case RoundingOptions::FullUp:
    			return $this->ceil();
    			break;
    		case RoundingOptions::FullDown:
    			return $this->floor();
    			break;
    	}

    	return new NCFloat( round( $this->_value , $precision ,  $mode  ) );
    }

    public function ceil()
    {
    	return new NCFloat( ceil($this->_value) );
    }
    public function floor()
    {
    	return new NCFloat( floor($this->_value) );
    }
}

class NCString extends Nc_object
{

	public static $empty = '';

	/*
	 *
	 *  -- A -- 
	 * 
	 */
	


	/*
	 *
	 *  -- B -- 
	 * 
	 */
	

	/*
	 *
	 *  -- C -- 
	 * 
	 */	
	
	/**
	 * Produces a copy/clone of the string
	 * @return [type] [description]
	 */
	public function copy()
	{
		return new NCString($this->_value,'CLONE');
	}

	/**
	 * Calls the PHP crypt function
	 * 
	 * @param  string $salt [description]
	 * @return [type]       [description]
	 */
	public function crypt($salt='')
	{
		return new NCString(crypt($this->_value , $salt));
	}

	/**
	 * Determin if the string contains a char or string passed in.
	 * 
	 * @param  [type] $string_chars [description]
	 * @return [type]               [description]
	 */
	public function contains($string_chars)
	{
		return (substr_count( $this->_value, $string_chars ))?true:false;
	}


	/**
	 * Gets the count of substring
	 * @param  [type] $string_chars [description]
	 * @return [type]               [description]
	 */
	public function count($string_chars, $offset = 0)
	{
		return substr_count( $this->_value, $string_chars );
	}


	public function equals($ncstring2,$sensitive = false)
	{
		if($sensitive)
		{
			return ( $ncstring2->toString()	=== $this->_value) ? 'true':'false';
		}
		else
		{
			return ( $ncstring2->toLower()->toString() == strtolower($this->_value) ) ? 'true':'false';
		}

	}



	/*
	 *
	 *  -- D -- 
	 * 
	 */	
	public function debug()
	{
		return var_dump($this->__debugInfo());
	}


	/*
	 *
	 *  -- F -- 
	 * 
	 */	
	
	public function indexOf($needle)
	{
		return strpos( $this->_value , $needle ) ;
	}
	public function indexOfFirst($needle, $start_offset = 0)
	{
		return strpos( $this->_value , $needle, $start_offset = 0 ) ;
	}

	public function isNumeric()
	{
		return is_numeric( $this->_value ) ;
	}	

	/*
	 *
	 *  -- L -- 
	 * 
	 */	
		
	/**
	 * @return INT  The length of the NCString
	 */
  	public function length()
	{
		return strlen( $this->_value );
	}

	public function lastIndexOf($needle, $start_offset = 0)
	{
		return strrpos( $this->_value , $needle ,  $start_offset ) ;
	}	
	/*
	 *
	 *  -- M -- 
	 * 
	 */	
	public function md5($raw_output=false)
	{
		return new NCString( md5( $this->_value ,  $raw_output ) );
	}



	/*
	 *
	 *  -- N -- 
	 * 
	 */
	
	/*
	 *
	 *  -- O -- 
	 * 
	 */	
	
	/*
	 *
	 *  -- P -- 
	 * 
	 */				


	/**
	 * Find the position of the string, then add the length to fnd the end of it
	 */
	public function endOf($char='', $sensitive=true)
	{
		$len = strlen($char);
		$method = ($sensitive)?'strpos':'strripos';
		$offset = 0 ;

		$position = $method( $this->_value, $char, 0 );

		return $position + $len;
	}

	public function positionOf($char='', $sensitive=true)
	{
		$method = ($sensitive)?'strpos':'strripos';
		$offset = 0 ;
		return $method( $this->_value, $char, 0 );
	}	

	/*
	 *
	 *  -- R -- 
	 * 
	 */

	/**
	 * Return random string/substr from the given string
	 * @param  integer $count [description]
	 * @return [type]         [description]
	 */
	public function randomChar()
	{
		return str_split ( $this->_value )[rand(0,strlen($this->_value)-1)];
	}
	public function randomize()
	{
		return new NCString( str_shuffle($this->_value) );
	}
	public function replace($search_for,$replace_with)
	{
		return new NCString( str_replace ( $search_for , $replace_with , $this->_value ) );
	}

	public function removeTags()
	{
		return new NCString( strip_tags($this->_value) );
	}	

	public function reverse()
	{
		return new NCString( strrev($this->_value) );
	}	


	/**
	 * get the right portion of text as a NCString
	 */
	public function rightOf($char=' ')
	{
		$length = strlen($char);
		
		$n = new NCString($this->_value);

		$start_from = $n->endOf($char);

		$v = substr($this->_value,$start_from);

		return new NCString($v);
	}	


	/*
	 *
	 *  -- S -- 
	 * 
	 */
	

	public function subString($start,$length=NULL)
	{
		if($length==NULL)
		{
			return new NCString( substr( $this->_value , $start  ) );
		}
		else
		{
			return new NCString( substr( $this->_value , $start , $length  ) );
		}
		
	}

	public function startsWithChar($char=' ')
	{
		/**
		 * Must clean and prepare the passed in char
		 */
		if(strlen(trim($char."")) < 1)
		{
			$char = ' ';
		}
		else
		{
			$char = substr($char,0,1);
		}
		
		return ( (substr($this->_value,0,1))===$char)?true:false;
	}	

	public function startsWith($char=' ',$case_sensitive=false)
	{
		$length = strlen($char);

		if(strlen(trim($char."")) < 1)
			return false;	
		
		return ( (substr($this->_value,0,$length))===$char)?true:false;
	}	


	/*
	 *
	 *  -- T -- 
	 * 
	 */	
	
	/**
	 *
	 * Ex: $ncstring->toUpper()
	 */
	public function toUpper()
	{
		return new NCString( strtoupper($this->_value) );
	}

	public function toLower()
	{
		return new NCString( strtolower($this->_value) );
	}	

	/**
	 * $string->toArray()
	 * or
	 * $string->toArray(',');
	 * @param  [type] $split_options [description]
	 * @return [type]                [description]
	 */
	public function toArray($split_options=StringSplitOptions::None)
	{
		switch($split_options)
		{
			case StringSplitOptions::DelimitLine:
				return preg_split('/\n|\r\n?/',  $this->_value);
				break;
			case StringSplitOptions::None:
				return str_split ( $this->_value );
				break;
			default:
				break;
		}

		return explode( $split_options , $this->_value );
	}

	public function toArrayDebug($split_options=StringSplitOptions::None)
	{
		var_dump($this->toArray($split_options)); return $this;
	}
	public function toNCArray()
	{
		return new NCArray( str_split ( $this->_value ));
	}	
	public function trim()
	{
		return new NCString( trim( $this->_value ) );
	}

	public function trimLeft()
	{
		return new NCString( ltrim( $this->_value ) );
	}

	public function trimRight()
	{
		return new NCString( ltrim( $this->_value ) );
	}

  	public function toUpperCaseFirst()
	{
		return new NCString( ucfirst ( $this->_value ) );
	}

	public function toLowerCaseFirst()
	{
		return new NCString( lcfirst( $this->_value ) );
	}

  	public function toUpperCaseFirstWord()
	{
		return new NCString( ucwords( $this->_value ) );
	}

	public function toString()
	{
		return new NCString($this->_value);
	}

	public function toNCWebString()
	{
		return new NCWebString($this->_value);
	}


	public function toNCFloat()
	{
		if($this->isNumeric())
		{
			return new NCFloat( floatval($this->_value) );
		}
		return false;
	}



	/*
	 * STATIC Functions
	 *
	 *
	 *
	 *
	 */


	public static function ToMD5($string,$raw_output=false)
	{
		return (new NCString($string))->md5($raw_output);
	}

	public static function Concat($string_1,$string_2)
	{
		return new NCString( $string_1 . $string_2 . "" );
	}

	public static function Levenshtein($input='carrot',$database_words=[],$success_link='https://www.google.com.au/#q=')
	{
		$shortest = -1;
		foreach ($database_words as $word) {
		    $lev = levenshtein($input, $word);
		    if ($lev == 0) {
		        $closest = $word;
		        $shortest = 0;
		        break;
		    }
		    if ($lev <= $shortest || $shortest < 0) {
		        $closest  = $word;
		        $shortest = $lev;
		    }
		}
		if ($shortest == 0) {
		    return new NCString("Exact match found: <a href='{$success_link}{$closest}'>$closest?</a>");		     
		} else {
		    return new NCString("Did you mean: <a href='{$success_link}{$closest}'>$closest?</a>");
		}
		return new NCString('No matches');
	}

	public static function Format($string,$arg1)
	{
		return new NCString( sprintf($string, $arg1 ) );
	}

	public static function CountWords($string)
	{
		return str_word_count( $string , $format = 0  );
	}	

	public static function Wrap( $string, $width=100, $cut = false, $break = '\n' )
	{
		return new NCString(wordwrap( $string , $width , $break,  $cut ));
	}
}


class NCWebString extends NCString
{
	public function slugify()
	{
		$slug=$this->_value;
		$delimiter='-';
		setlocale(LC_ALL, 'en_US.UTF8');
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $this->_value);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		return new NCWebString($clean);
	}
	public function toString()
	{
		return new NCString($this->_value);
	}

}

class Nc_eav extends Nc_object
{
	protected $_id;
	protected $_product_id;
	protected $_variance_id;
	protected $_label;
	protected $_type;
	public function __construct($row,$options='')
	{
		$this->_id = $row->id;
		$this->_product_id = $row->e_product;
		$this->_variance_id = $row->e_variance;
		$this->_label = $row->e_label;
		$this->_value =  $row->e_value;	
		parent::__construct( $row->e_value , $options,'override_eav_dbrow');	
	} 
 	public function __invoke($x)
    {
        return $x->_value;
    }
    public function __toString()
    {
        return (string) '<pre>'. print_r($this->_value,true).'</pre>';
    }	 
}
class NCEav extends Nc_eav
{
	public function getLabel()
	{
		return $this->_label;
	}
	public function getValue()
	{
		return $this->_value;
	}	
	public function getProductID()
	{
		return $this->_product_id;
	}	
	public function getVarianceID()
	{
		return $this->_variance_id;
	}	
	public function hasValue($x)
	{
		return ($this->_value === $x);
	}
	public function hasLabel($label)
	{
		return ($this->_label === $label);
	}	
	public function hasMatchLabelValue($label, $value)
	{
		return (($this->_value === $value) AND ($thsi->_label === $label));
	}	
}
class NCEAVArray extends NCArray
{

	public function __construct($value=[],$options='')
	{
		parent::__construct( $value, $options,'nceavarray');	
	} 

    public function keyExist($key)
    {
		return (in_array($key, $this->_value))?true:false;
    }  

    public function valueExist($value)
    {
	   foreach ($this->_value as $item)
	        if (isset($item[$key]) && $item[$key] == $value)
	            return true;
	    return false;
    } 

    public function pushall($rows)
    {	
    	$v = $this->copy();
    	foreach($rows as $row)
    	{
    		$v[] = new NCEav($row);
    	}
    	
    	return new NCEAVArray( $v );
    }
    public function push($item)
    {	
    	$x = parent::push($item);
    	return new NCEAVArray( $x );
    }
  
    public function pop()
    {
    	$x = parent::pop();
    	return new NCEAVArray( $x );
    } 

    public function trim()
    {	
    	$x = parent::trim();
    	return new NCEAVArray( $x );
    }  

    public function trimLeft()
    {	
    	$x = parent::trimLeft();
    	return new NCEAVArray( $x );
    }  

    public function trimRight()
    {	
    	$x = parent::trimRight();
    	return new NCEAVArray( $x );
    }   

    public function removeIndex($int)
    {
    	$x = parent::removeIndex($int);
    	return new NCEAVArray( $x );
    }   

    public function removeValue($value)
    {
    	$x = parent::removeValue($int);
    	return new NCEAVArray( $x );
    } 
    public function reverse()
    {
    	$x = parent::reverse();
    	return new NCEAVArray( $x );
    }

    public function shuffle()
    {
    	$x = parent::shuffle();
    	return new NCEAVArray( $x );
    }                   
 
    public function first()
    {
	   foreach ($this->_value as $item)
	   {
	       return $item;
	   }
	   return false;
    }  


    public function noNullValues()
    {
       $array = $this->copy();
	   foreach ($array as $key => $item)
	   {
	        if($item->_value == NULL)
	        {
				unset($array[$key]);
	        }
	   }
       return new NCEAVArray( $array );
    }  

    
    public function removeByVariance($variance)
    {
       $array = $this->copy();
	   foreach ($array as $key => $item)
	   {
	        if($item->getVarianceID() == $variance)
	        {
				unset($array[$key]);
	        }
	   }
       return new NCEAVArray( $array );
    } 


    public function whereLabelValue($label,$value)
    {
       return $this->_whereLabelValue($label,$value,'where');
    } 
    public function whereLabelValueNot($label,$value)
    {
       return $this->_whereLabelValue($label,$value,'not');
    }  

    private function _whereLabelValue($label,$value,$operator='where')
    {
       $array = $this->copy();
	   foreach ($array as $key => $item)
	   {
	   		if($operator == 'where')
	   		{
		        if( ! (($item->getLabel() === $label) AND ( $item->getValue() === $value) ))
		        {
					unset($array[$key]);
		        }
		    }
		    else
		    {
		        if(($item->getLabel() === $label) AND ( $item->getValue() === $value) )
		        {
					unset($array[$key]);
		        }
		    }
	   }
       return new NCEAVArray( $array );
    } 

    public function hasLabelValue($label,$value)
    {
	   foreach ($this->_value as $key => $item)
	   {
	        if( ($item->getLabel() === $label) AND ( $item->getValue() === $value) )
	        {
				return true;
	        }
	   }
       return false;
    }

    public function variancesWith($label,$value)
    {
    	//create the list to items to remove later on
    	$variances_to_remove = [];

    	//create a copy array to play with
		$array = $this->copy();

		//now the hunt begins.
		foreach ($array as $key => $item)
		{
			//only if the label matches
			if($item->getLabel() === $label)
			{
				//if the value matches, we are ok.
				if($item->getValue() === $value) 
				{
					
				}
				else
				{
					// mark it to remove later
					//get the variance id, remove all with that variance
					$variances_to_remove[$item->getVarianceID()] = $item->getVarianceID();
				}

			}
		}

		//remove by variance id's
		$workable = new NCEAVArray( $array );
		foreach ($variances_to_remove as $key => $value)
		{
			$workable = $workable->removeByVariance($key);
		}
		//var_dump($workable);die;

		return $workable;

		//return new NCEAVArray( $workable );
    }  
}