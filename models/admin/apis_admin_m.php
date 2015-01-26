<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Apis_admin_m extends MY_Model
{
    public $_table = 'nct_api_keys';
    public $_validation_fields =
    [
            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|trim'
            ],    
            [
                'field' => 'max_allowed',
                'label' => 'Max Allowed',
                'rules' => 'required|trim|numeric'
            ],      
            [
                'field' => 'enabled',
                'label' => 'Max Allowed',
                'rules' => 'required|trim|numeric'
            ],                         
    ];
    public $_edit_validation_fields =
    [
            [
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|trim'
            ],    
            [
                'field' => 'max_allowed',
                'label' => 'Max Allowed',
                'rules' => 'required|trim|numeric'
            ],      
            [
                'field' => 'enabled',
                'label' => 'Max Allowed',
                'rules' => 'required|trim|numeric'
            ],                         
    ];
    public function __construct()
    {
        parent::__construct();
    }
    

    /**
     * Creates an api record and generates a key
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function create($input)
    {
        $to_insert=
        [
            'name' => $input['name'],
            'key' => make_UUID( $input['name'] + time() + $this->current_user->id , ['braces'=>false]),
            'enabled' => $input['enabled'],
            'max_allowed' => $input['max_allowed'],
            'tot_requests' => 0,     
            'tot_curr_requests' => 0,    
            'ax_extensions' => 1, 
            'ax_products' => 1,            
                                            
        ];

        return $this->insert($to_insert);
    }

    /**
     * Editsa the curret api key
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public function edit($id, $input)
    {
        $to_edit=
        [
            'name' => $input['name'],
            'enabled' => $input['enabled'],
            'max_allowed' => $input['max_allowed'],
            'tot_requests' => 0,     
            'tot_curr_requests' => 0,    
            'ax_extensions' => 1, 
            'ax_products' => 1,            
                                            
        ];
        return $this->update($id, $to_edit);
    }

    /**
     * [get_requests description]
     * @param  [type] $key_id [description]
     * @return [type]         [description]
     */
    public function get_requests($key_id)
    {
        $query = $this->db->where('key_id',$key_id)->get('nct_api_requests')->result();
        return $query;
    }

    /**
     * [delete description]
     * @param  [type] $key_id [description]
     * @return [type]         [description]
     */
    public function delete($key_id)
    {
        $this->db->where('key_id',$key_id)->delete('nct_api_requests');        
    	parent::delete($key_id);
    	return true;
    }
}