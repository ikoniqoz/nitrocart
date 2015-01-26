<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/core/Icart.php');
class Mycart extends ICart {

	/**
	 * Contents of the coupons
	 *
	 * @var array
	 */
	protected $_cart_coupon	= [];


	public function __construct($params = [])
	{
		parent::__construct($params);		
		
		$this->_cart_coupon = $this->CI->session->userdata('cart_coupon');

		log_message('debug', 'Cart Coupon Class Initialized');
	}


	/**
	 * BY is either ID or ROWID
	 * VAL = the ROWID, or variance ID
	 * @param  [type] $val [description]
	 * @param  string $by  [description]
	 * @return [type]      [description]
	 */
	public function productv_qty($val, $by = 'rowid') {
		$count = 0;
		if( $this->contents() != NULL )
		{
			foreach( $this->contents() as $item )
			{
				if(isset($item[$by]))
				{
					if ($item[$by] == $val)
					{
						$count += $item['qty'];
					}
				}
			}
		}
		return $count;
	}

	/**
	 * Update the cart
	 *
	 * This function permits the quantity of a given item to be changed.
	 * Typically it is called from the "view cart" page if a user makes
	 * changes to the quantity before checkout. That array must contain the
	 * product ID and quantity for each item.
	 *
	 * @param	array
	 * @return	bool
	 */
	public function update($items=[]) 
	{
		return parent::update($items);
	}

	/**
	 * Override of _update.
	 * The main change here is the storing of the extra values
	 * 
	 * @param  array  $items [description]
	 * @return [type]        [description]
	 */
	protected function _update($items = []) {

		// Without these array indexes there is nothing we can do
		if ( ! isset($items['qty'], $items['rowid'], $this->_cart_contents[$items['rowid']])) {
			return false;
		}

		// Prep the quantity
		$items['qty'] = (float) $items['qty'];

		// Is the quantity a number?
		if ( ! is_numeric($items['qty']))
		{
			return false;
		}

		// Is the quantity zero?  If so we will remove the item from the cart.
		// If the quantity is greater than zero we are updating
		if ($items['qty'] == 0)
		{
			unset($this->_cart_contents[$items['rowid']]);
		}
		else
		{
			//all we are doing is storing the new qty during an update
			//when we add, discounts work becuase the qty is pre-collected
			$this->_cart_contents[$items['rowid']]['qty'] = $items['qty'];


			if (isset( $items['base'] )) $this->_cart_contents[$items['rowid']]['base'] = $items['base'];


			//if (isset( $items['name'] )) $this->_cart_contents[$items['rowid']]['name'] = $items['name'];
	
			if (isset( $items['price'] )) $this->_cart_contents[$items['rowid']]['price'] = $items['price'];

			if (isset( $items['discount_message'] )) $this->_cart_contents[$items['rowid']]['discount_message'] = $items['discount_message'];

			if (isset( $items['discountable'] )) $this->_cart_contents[$items['rowid']]['discountable'] = $items['discountable'];

			if (isset( $items['discount'] )) $this->_cart_contents[$items['rowid']]['discount'] = $items['discount'];


		}

		return true;
	}



	public function insert($items = array())
	{
	
		if (isset($items['id']))
		{
			$items['price'] =  $this->clean_price(  $items['price'] );
			$items['base']  =  $this->clean_price(  $items['base'] );
		}
		else
		{
			foreach ( $items as $key => $val )
			{
				if (is_array($val) && isset($val['id']))
				{
					$items[$key]['price'] =  $this->clean_price( $val['price'] );
					$items[$key]['base']  =  $this->clean_price( $val['base'] );
				}
			}
		}

		return parent::insert($items);		
	}



	protected function _save_cart()
	{
		// Lets add up the individual prices and set the cart sub-total
		$this->_cart_contents['total_items'] = $this->_cart_contents['cart_total'] = 0;
		foreach ($this->_cart_contents as $key => $val)
		{		
			// We make sure the array contains the proper indexes
			if ( ! is_array($val) OR ! isset($val['price'], $val['qty']))
			{
				continue;
			}


			$discounting = 0; $coupon_applied = '';
			$product_value = ( ((float) $val['price']) * ((int) $val['qty'])) + ((float) $val['base']);

			//if coupon discount is applied, reset it for re-calc
			$this->_reset_cart_item_coupon($key);

			// Chek to see if a coupon can be applied
			if($this->_check_couponable( $val ) )
			{
				$discounting = ( $this->_cart_coupon['rate'] * (float) $product_value );
				$product_value = (float) $product_value - $discounting;
				$this->_cart_contents[$key]['discount'] = (float) $discounting;
				$this->_cart_contents[$key]['discount_message'] = "Coupon applied: ".$this->_cart_coupon['code']." : Saving of ".$discounting;
				$this->_cart_contents[$key]['is_coupon'] = 'yes';
				$this->_cart_contents[$key]['coupon_code'] = $this->_cart_coupon['code'];
			}

			$this->_cart_contents[$key]['subtotal'] = $product_value;
			$this->_cart_contents['total_items'] += $val['qty'];
			$this->_cart_contents['cart_total'] += $product_value;			

		}

		// Is our cart empty? If so we delete it from the session
		if (count($this->_cart_contents) <= 2)
		{
			$this->CI->session->unset_userdata('cart_contents');

			// Nothing more to do... coffee time!
			return false;
		}

		// If we made it this far it means that our cart has data.
		// Let's pass it to the Session class so it can be stored
		$this->CI->session->set_userdata(['cart_contents' => $this->_cart_contents]);

		// Woot!
		return true;
	}



	private function clean_price($value)
	{
		return (float) str_replace(',','', $value );
	}
	/**
	 * [_reset_cart_item_coupon description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	private function _reset_cart_item_coupon($key)
	{
		//reset the discount if coupon
		if(isset($this->_cart_contents[$key]['is_coupon']))
		{
			if($this->_cart_contents[$key]['is_coupon'] == 'yes')
			{
				$this->_cart_contents[$key]['discount'] = (float) 0;
				$this->_cart_contents[$key]['discount_message'] = '';
			}
		}
		$this->_cart_contents[$key]['is_coupon'] = 'no';
		$this->_cart_contents[$key]['coupon_code'] = '';		
	}


	/**
	 * Check to see if a product in te cart
	 * can have a/the coupon applied
	 * @param  [type] $cart_item [description]
	 * @return [type]            [description]
	 */
	private function _check_couponable( $cart_item )
	{
		if($this->_cart_coupon !== false)
		{
			if( ((int)$this->_cart_coupon['pid'] == (int)$cart_item['productid'])  )
			{	
				if($cart_item['discount'] == 0)
				{
					return true;
				}
			}
		}
		return false;		
	}


 	/**
 	 * Check if the cart has coupons applied
 	 * @return boolean [description]
 	 */
	public function coupon() {
		$coupon = $this->CI->session->userdata('cart_coupon');
		return ($coupon)?$coupon['code']:false;
	}



	/**
	 * Apply a coupon
	 * @return [type] [description]
	 */
	public function apply_coupon( $coupon ) {

		// We make sure the array contains the proper indexes
		if ( ! is_array($coupon) OR ! isset($coupon['code'],$coupon['pid'],  $coupon['rate']) )
		{
			$this->CI->session->unset_userdata( 'cart_coupon' );
			$this->_cart_coupon = false;
			$this->_save_cart();
			return false;
		}

		$coupon['code']=strtoupper(trim($coupon['code']));
		$coupon['pid']=(int)$coupon['pid'];
		$coupon['vid']=(int)$coupon['vid'];
		$coupon['rate']=(float)$coupon['rate'];

		//re-apply to the session
		$this->CI->session->set_userdata( 'cart_coupon' , $coupon );
		$this->_cart_coupon = $this->CI->session->userdata('cart_coupon');
		$this->_save_cart();		
		return true;
	}

	public function clear_coupon() {
		$this->CI->session->unset_userdata( 'cart_coupon' );
		$this->_cart_coupon = false;
		$this->_save_cart();
	}

	public function destroy()
	{
		$this->CI->session->unset_userdata( 'cart_coupon' );
		$this->_cart_coupon = false;
		parent::destroy();
	}

	/**
	 * Cart Contents
	 *
	 * Returns the entire cart array
	 *
	 * @param	bool
	 * @return	array
	 */
	public function contents($newest_first = false)
	{
		$cart = ($newest_first) ? array_reverse($this->_cart_contents) : $this->_cart_contents;
		unset($cart['total_items']);
		unset($cart['cart_total']);
		return ($cart)?$cart:array();
	}

}
// End of class