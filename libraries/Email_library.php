<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Email_library extends ViewObject
{

    /**
     * Sole constructor
     * @param array $params [description]
     */
	public function __construct($params = array())
	{
	}


    /**
     * {{first_name}}
     * {{last_name}}
     * {{order_date}}
     * {{email}}
     * {{phone}}
     * {{sender_ip}}
     * {{cost_total}}
     * {{shipping_address}}
     * {{billing_address}}
     *
     * {{order_contents}}
     */
    public function prepareOrderLodgedEmail( $email_variables = []  )
    {
        // Load Libraries
        $this->load->model('nitrocart/addresses_m');

        // Collect the data
        $shipping_details = $this->addresses_m->get( $email_variables['shipping_address_id'] );
        $billing_details  = $this->addresses_m->get( $email_variables['billing_address_id'] );

        // prep the email
        $email_variables['first_name']              = $billing_details->first_name;
        $email_variables['last_name']               = $billing_details->last_name;
        $email_variables['order_date']              = date('d-M-Y', time() ); //fix order date format
        $email_variables['email']                   = $billing_details->email;
        $email_variables['phone']                   = $billing_details->phone;
        $email_variables['sender_ip']               = $email_variables['ip_address'];
        $email_variables['customer_ip']             = $email_variables['ip_address'];
        $email_variables['cost_total']              = $email_variables['order_total'];
        $email_variables['shipping_address']        = $this->format_address($shipping_details);
        $email_variables['billing_address']         = $this->format_address($billing_details);

        // Build the content list
        $order_items = '';
        foreach ( $email_variables['cart_items'] as $item)
        {
            $order_items .= "<li>{$item['name']}</li>";
        }

        $email_variables['order_contents'] = "<ul>{$order_items}</ul>";

        return $email_variables;
    }





    /**
     * we also want to get the tax and invoice items
     *
     * $ {{amt_shipping_total}} - shipping amount
     * $ {{amt_items_total}} - total amount for items purchased
     * $ {{amt_tax}} - total amount for tax
     * $ {{amt_item_total_ic_tax}} - ORDER total Inc tax
     * $ {{amt_item_total_ex_tax}} - ORDER total EXCL tax
     *
     * {{item_list}}
     *      {{qty}} - {{title}} -  $ {{amt}}
     * {{/item_list}}
     *
     * {{order_id}}
     * {{order_date}}
     * {{email}} - customer email
     * {{customer_ip}} - customer IP address
     *
     *
     */
    public function prepareOrderInvoiceEmail( $order_id = NULL )
    {
        // Load Libraries
        $this->load->model('nitrocart/orders_m');
        $this->load->model('nitrocart/addresses_m');
        $this->load->library('nitrocart/gateway_library');

        // Collect the data
        $order = $this->orders_m->get($order_id);

        $billing_details  = $this->addresses_m->get( $order->billing_address_id );


        $items  = $this->orders_m->get_order_items( $order->id );


        // prep the email
        $email_variables['first_name']              = $billing_details->first_name;
        $email_variables['last_name']               = $billing_details->last_name;      



        //order totals + tax values
        $email_variables['amt_total_shipping'] =  number_format( $order->total_shipping, 2)  ; //shipping not factored into tax
        $email_variables['amt_total_subtotal'] = number_format( $order->total_subtotal, 2) ; //total of items
        $email_variables['amt_total_tax'] = number_format(  $order->total_tax, 2) ; 
        $email_variables['amt_total_totals'] = number_format( $order->total_totals, 2); //otal of items without tax
        $email_variables['amt_total_discount'] = number_format( $order->total_discount, 2); //otal of items without tax
   

        $email_variables['item_list'] = [];

        foreach ( $items as $item)
        {
            $_item = [];
            $_item['qty'] = $item->qty;
            $_item['title'] = $item->title;
            $email_variables['item_list'][] = $_item;
        }

        // prep the email
        $email_variables['order_id'] = $order_id;
        $email_variables['order_date'] = date('d-M-Y', $order->order_date);
        $email_variables['email'] = $billing_details->email;
        $email_variables['customer_ip'] = $email_variables['sender_ip'] = $this->input->ip_address(); 

        return $email_variables;
    }


    /**
     * we also want to get the tax and invoice items
     *
     * $ {{amt_shipping_total}} - shipping amount
     * $ {{amt_items_total}} - total amount for items purchased
     * $ {{amt_tax}} - total amount for tax
     * $ {{amt_item_total_ic_tax}} - ORDER total Inc tax
     * $ {{amt_item_total_ex_tax}} - ORDER total EXCL tax
     *
     * {{item_list}}
     *      {{qty}} - {{title}} -  $ {{amt}}
     * {{/item_list}}
     *
     * {{order_id}}
     * {{order_date}}
     * {{email}} - customer email
     * {{customer_ip}} - customer IP address
     *
     *
     */
    public function prepareOrderPaidEmail( $order_id  )
    {

        $email_variables = $this->prepareOrderInvoiceEmail( $order_id );

        $email_variables['piad'] = '';

        //return variables
        return $email_variables;
    }

    public function sendEmail( $email_variables, $template_slug, $to = 'admin@localhost' )
    {

        $email_variables['slug'] = $template_slug;
        $email_variables['to'] = $to;

        Events::trigger('email', $email_variables, 'array');

        return true;
    }


    private function format_address($address_row)
    {
        if($address_row)
            return $address_row->address1.', '.$address_row->address2. ', '.$address_row->city . ' '.$address_row->zip;
        return '';
    }


}