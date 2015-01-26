<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/packer/Box.php');
require_once(dirname(__FILE__) . '/packer/BoxList.php');
require_once(dirname(__FILE__) . '/packer/Item.php');
require_once(dirname(__FILE__) . '/packer/ItemList.php');
require_once(dirname(__FILE__) . '/packer/PackedBox.php');
require_once(dirname(__FILE__) . '/packer/PackedBoxList.php');
require_once(dirname(__FILE__) . '/packer/Packer.php');
require_once(dirname(__FILE__) . '/packer/Package.php');

class Packages_library extends ViewObject
{

    protected $__trace;
    protected $__packers;
    protected $__packed_boxes;
    protected $__redir_options;


    public function __construct($params = array())
    {
        // initialize any fields
        $this->_packages = [];
        $this->__packers = [];
        $this->__packed_boxes = [];
        $this->__trace = '<br><h1>Trace Output</h1><br />';

        //set the redir options
        $this->__redir_options = [
            'redir'=> NC_ROUTE.'/cart',
            'mode'=>'front'
        ];

    }

    public function getRedirOptions()
    {
        return $this->__redir_options;  
    }

    private function _trace($title='TraceLine',$output=NULL,$line=0,$indent=0)
    {
        if(is_object($output) or is_array($output))
        {
            ob_start();
            var_dump($output);
            $output = ob_get_clean();
        }
        $margin = "{$indent}px;";
        $this->__trace .= "   <div style='margin-left:{$margin}'>";
        $this->__trace .= "      <h2>{$title}</h2>";
        $this->__trace .= "      <br> {$output}";
        $this->__trace .= "      <br><br>";
        $this->__trace .= "   </div>";
        $this->__trace .= "<hr />";
    }



    /**
     * 1. Get a Distict list of all package Groups
     * 2. Create a new Packer for each Group
     * 3.
     *
     *
     * @param  array  $cart_items [description]
     * @return [type]             [description]
     */
   public function pack( $_in_cart_items = [], $redir_options=['redir'=> NULL,'mode'=>'front'] )
   {
        //copy and do not use the reference items
        $cart_items = $_in_cart_items;

        if($redir_options['redir']==NULL)
        {
            $redir_options['redir'] = NC_ROUTE.'/cart';
        }


        $this->__packed_boxes = [];

        $this->load->model('nitrocart/packages_m');
        $this->load->model('nitrocart/products_front_m');
        $this->load->model('nitrocart/products_variances_m');

        $__pkg_groups = [];

        //set the redir options
        $this->__redir_options = $redir_options;


        //
        // 1.1 Only keep shipable items, and remove others
        //
        foreach ($cart_items as $key => $value)
        {
            if(isset($value['is_shippable']))
            {
                //its not shippable, lets remove it
                if( (int)($value['is_shippable'])==0)
                {
                    unset($cart_items[$key]);
                }
            }
        }
  

        //
        // 1.2 Define all the unique package Groups
        //
        foreach ($cart_items as $key => $value)
        {
            $__pkg_groups[ $value['pkg_group_id']  ] = []; 
        }


        // Trace
        // Status: Working
        // $this->_trace('Unique Package Groups',$__pkg_groups, __LINE__);



        //
        // 2. Create a Packer for each Group
        //
        foreach($__pkg_groups as $key => $value)
        {
            $this->__packers[$key]['packer'] = new DVDoug\BoxPacker\Packer($redir_options);
        }








        //
        // 3. Get all the available packages for the group
        //
        foreach($this->__packers as $key => $value)
        {
            $this->__packers[$key]['packages'] = $this->packages_m->selectPackagesByGroup($key);
        }






        //
        // 4. Assign all the packages to the packer
        //
        foreach($this->__packers as $key => $value)
        {


            foreach($value['packages'] as $package)
            {
                // Create a new definition of a box to the packer
                $this->__packers[$key]['packer']->addBox( new Package($package) );
            }


            // Remove the package
            //
            // No longer needed and a good
            unset($this->__packers[$key]['packages']);
        }





        //
        // 5. Now assign the items in the cart to the packers
        //
        foreach ($cart_items as $key => $cartItem)
        {
            $_packer = $this->__packers[$cartItem['pkg_group_id']]['packer'];

            //Get variance, we should do this earlier, but for concept ok.
            $variance = $this->products_variances_m->get($cartItem['id']);
            $product_name = $cartItem['name'] ;

            // Repeat the step multiple times until we have done the qty
            for($i = 0; $i < $cartItem['qty'];$i++)
            {
                $admin_link = "<a href='".NC_ADMIN_ROUTE."/product/variant/{$variance->id}'>{$product_name} - {$variance->name}</a>";
                $front_link = "<a href='".NC_ROUTE."/products/product/{$variance->product_id}'>{$product_name} - {$variance->name}</a>";

                $_packer->addItem( new Product( $admin_link, $front_link , $variance  ) );
            }


        }





        //
        // 6. Get all the packers to do their work. No slacking off.
        //
        $tcount = 0;
        foreach($this->__packers as $key => $packer)
        {
            $packer =$packer['packer'];
            //$packedBoxes =  $this->__packers[$key]['packer']->pack();
            //the false will force to use lower smaller packages as possible without redistributing weight
            $packedBoxes =  $packer->pack(true);


            $count = 0;


            $this->_trace('NEW PACKER: ', 'This packer has ' .count($packedBoxes) . ' boxes<br />' , __LINE__, 0);
            //echo ' and there are ' .$tcount . ' boxes in total';;


            foreach($packedBoxes as $packedBox)
            {

                $boxtype            = $packedBox->getBox();
                $rw                 = $packedBox->getRemainingWeight();
                $itemsInTheBox      = $packedBox->getItems();

                //var_dump($boxtype->maxWeight);die;
                //var_dump($packedBox);die;
                $curr_weight = $boxtype->maxWeight - $rw;



                // Found Box Title
                $this->_trace('BOX: ', $boxtype->reference , __LINE__,50);

                $str = $this->buildBoxTrace( $boxtype , $curr_weight, $rw, $itemsInTheBox  );

                //Details
                $this->_trace('Details: ', $str  , __LINE__, 100);


                foreach ($itemsInTheBox as $item)
                {
                    $this->_trace('Item &rarr;:'.$item->getDescription(), '' , __LINE__, 200);
                }

                $shippable_item = [];

                
                $shippable_item['package_name'] = $boxtype->getBoxID();
                $shippable_item['height'] = $boxtype->getOuterDepth();
                $shippable_item['width'] = $boxtype->getOuterWidth();
                $shippable_item['length'] = $boxtype->getOuterLength();
                $shippable_item['weight'] = $curr_weight;
                $shippable_item['qty'] = 1;
                $shippable_item['qty_in_box'] = count($itemsInTheBox);

                $this->__packed_boxes[] = $shippable_item;
            }

        }


        //report complete
   }

   private function  buildBoxTrace( $boxtype , $curr_weight, $rw, $itemsInTheBox )
   {

        $str = '<br/>';
        $str .= "<label>Width:(Inner/Outer)</label>";
        $str .= "<span><pre>{$boxtype->getInnerWidth()}/{$boxtype->getOuterWidth()}</pre></span>";

        $str .= "<label>Height:(Inner/Outer)</label>";
        $str .= "<span><pre>{$boxtype->getInnerDepth()}/{$boxtype->getOuterDepth()}</pre></span>";

        $str .= "<label>Length:(Inner/Outer)</label>";
        $str .= "<span><pre>{$boxtype->getInnerLength()}/{$boxtype->getOuterLength()}</pre></span>";

        $str .= "<label>Weight:(Empty/Current/Remaining/Max)</label>";
        $str .= "<span><pre>{$boxtype->getEmptyWeight()}/{$curr_weight}/{$rw}/{$boxtype->getMaxWeight()}</pre></span>";

        $str .= "<label>Inner Volume mm^3:</label>";
        $str .= "<span><pre>{$boxtype->getInnerVolume()}</pre></span>";

        $str .= "<label>Items in Box:</label>";
        $_count = count( $itemsInTheBox );
        $str .= "<span><pre>{$_count}</pre></span>";

        return $str;
   }

   public function getShippableContainers()
   {
       return $this->__packed_boxes;
   }

   /**
    * Use this method if you do not want to use packages. This will get the standard array from the cart items
    * @param  [type] $cart_items [description]
    * @return [type]             [description]
    */
   public function getShippableContainersCartItems($cart_items)
   {
        $return_array = array();


        foreach($cart_items as $item)
        {

            $varianceid = $item['variance'];
            $qty = $item['qty'];

            $ra = [];
            $ra['package_name'] = $item['code'];  //slug of the item
            $ra['height'] = $item['height']; 
            $ra['width'] = $item['width']; 
            $ra['length'] = $item['length']; 
            $ra['weight'] = $item['weight']; 
            $ra['qty'] = $item['qty']; 
            $ra['qty_in_box'] = 1; 
            $return_array[]  = $ra; 
        }

        return $return_array;
   }


    public function get_shippable_boxes( $use_packages = true , $items = [] )
    {
        $_shippable_boxes = [];
        if($use_packages)
        {
            $this->packages_library->pack( $items );
            return $this->packages_library->getShippableContainers();
        }
        return $this->packages_library->getShippableContainersCartItems($items); 
    }   


   private function getProductByVariance($variance)
   {
       $variance = $this->products_variances_m->get($variance);

       return new Product($variance);
   }

   public function getTrace()
   {
        return $this->__trace;
   }

   public function getPackages()
   {
        return $this->_packages();
   }

}