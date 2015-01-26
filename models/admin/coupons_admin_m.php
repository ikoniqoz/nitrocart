<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/../coupons_m.php');
class Coupons_admin_m extends Coupons_m
{


    public function __construct()
    {
        parent::__construct();
    }



    /**
     * Create a new product, only some fields are required, the rest uses the default fields,
     * when creating a new product, you must first enter the first few req values, save-> then edit the newly created product.
     *
     * The TYPE_ID is set once and can not be altered.
     *
     * @param Array $input Input fields from user, they should be prepped before coming here.
     */
    public function create($input)
    {

        $to_insert = array(
                'code'          => strtoupper(strip_tags(trim($input['code']))),
                'max_use'       => (int) ($input['max_use']),
                'pcent'         => (int) ($input['pcent']),
                'product_id'    => (int) ($input['product_id']),
                'enabled'       => $input['enabled'],
                'created'       => date("Y-m-d H:i:s"),
        );

        $new_id =  $this->insert($to_insert);

        if($new_id)
        {
            Events::trigger('SHOPEVT_AdminCouponCreate', $new_id);
        }

        return $new_id;
    }

    public function edit($id, $input)
    {
        $to_update = array(
                'max_use'       => (int) ($input['max_use']),
                'pcent'         => (int) ($input['pcent']),
                'enabled'       => $input['enabled'],
                'updated'       => date("Y-m-d H:i:s"),
        );

        return $this->update($id,$to_update);
    }

    /**
     * Delete if there are no uses
     * 
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
        $to_update = array( 'enabled'=>0, 'deleted' => date("Y-m-d H:i:s") );
        return $this->update($id,$to_update);        
    }


  
}