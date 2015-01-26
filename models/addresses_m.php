<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Addresses_m extends MY_Model 
{


    public $_table = 'nct_addresses';
    public $address_validation = [];
    public $shipping_address_validation = [];


    public function __construct()
    {

        parent::__construct();

        $this->address_validation = [
                [
                        'field' => 'first_name',
                        'label' => lang('nitrocart:address:first_name'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'last_name',
                        'label' => lang('nitrocart:address:last_name'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'company',
                        'label' => lang('nitrocart:address:company'),
                        'rules' => 'trim'
                ],
                [
                        'field' => 'phone',
                        'label' => lang('nitrocart:address:phone'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'email',
                        'label' => lang('nitrocart:address:email'),
                        'rules' => 'required|trim|valid_email'
                ],
                [
                        'field' => 'address1',
                        'label' => lang('nitrocart:address:address1'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'address2',
                        'label' => lang('nitrocart:address:address2'),
                        'rules' => 'trim'
                ],
                [
                        'field' => 'city',
                        'label' => lang('nitrocart:address:city'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'state',
                        'label' => lang('nitrocart:address:state'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'country',
                        'label' => lang('nitrocart:address:country'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'zip',
                        'label' => lang('nitrocart:address:zip'),
                        'rules' => 'required|trim'
                ],
        ];

        $this->shipping_address_validation = [
                [
                        'field' => 'shipping_first_name',
                        'label' => 'Shipping ' . lang('nitrocart:address:first_name'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_last_name',
                        'label' => 'Shipping ' . lang('nitrocart:address:last_name'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_company',
                        'label' => 'Shipping ' . lang('nitrocart:address:company'),
                        'rules' => 'trim'
                ],
                [
                        'field' => 'shipping_phone',
                        'label' => 'Shipping ' . lang('nitrocart:address:phone'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_email',
                        'label' => 'Shipping ' . lang('nitrocart:address:email'),
                        'rules' => 'required|trim|valid_email'
                ],
                [
                        'field' => 'shipping_address1',
                        'label' => 'Shipping ' . lang('nitrocart:address:address1'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_address2',
                        'label' => 'Shipping ' . lang('nitrocart:address:address2'),
                        'rules' => 'trim'
                ],
                [
                        'field' => 'shipping_city',
                        'label' => 'Shipping ' . lang('nitrocart:address:city'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_state',
                        'label' => 'Shipping ' . lang('nitrocart:address:state'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_country',
                        'label' => 'Shipping ' . lang('nitrocart:address:country'),
                        'rules' => 'required|trim'
                ],
                [
                        'field' => 'shipping_zip',
                        'label' => 'Shipping ' . lang('nitrocart:address:zip'),
                        'rules' => 'required|trim'
                ],
        ];        
    }

    /**
     * Type can only be billing or shipping
     *
     * @param  [type] $input [description]
     * @param  string $type  [description]
     * @return [type]        [description]
     */
    public function create($input, $billing=0, $shipping=0)  {
        $this->db->trans_start();
        $to_insert = [
                'user_id'       => $input['user_id'],
                'email'         => $input['email'],
                'first_name'    => $input['first_name'],
                'last_name'     => $input['last_name'],
                'company'       => isset($input['company'])?$input['company']:'',
                'address1'      => $input['address1'],
                'address2'      => $input['address2'],
                'state'         => $input['state'],
                'city'          => $input['city'],
                'country'       => $input['country'],
                'zip'           => $input['zip'],
                'phone'         => $input['phone'],
                'billing'       => 1, //$billing,
                'shipping'      => 1, //$shipping,
                'updated'       => date("Y-m-d H:i:s"),
                'created'       => date("Y-m-d H:i:s"),
                'instruction'   => isset($input['instruction'])?$input['instruction']:'',
        ];

        $input['id'] = $this->insert($to_insert);

        $this->db->trans_complete();

        return ($this->db->trans_status() === false) ? false : $input['id'];
    }


    public function doShippingAlso($address_id)  {
        $update_record = [
            'shipping' =>  1,
        ];
        return $this->update($address_id, $update_record); //returns id
    }



    public function remove($address_id, $user_id) {
        $update_record = [
            'deleted' => date("Y-m-d H:i:s"),
        ];
        return $this->where('user_id', $user_id)->update($address_id, $update_record); //returns id
    }


    public function get_active_by_user($user_id) {
        return $this->where('deleted',NULL)->get_many_by('user_id', $user_id);
    }
}