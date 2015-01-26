<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Install_library extends Component
{

	public function __construct()
	{
		$this->ci = get_instance();
        $this->ci->load->library('nitrocart/Store_module');
	}

    public function subsystem( $driver, $install=true )
    {
        $method = ($install)?'install':'uninstall';
        return $this->_system_action('nitrocart','systems',$driver,$method,1);
    }

    public function feature( $driver, $install=true )
    {
        $method = ($install)?'install':'uninstall';
        return $this->_system_action('nitrocart','features',$driver,$method,1);
    }
    
    public function extension( $namespace, $path, $driver, $install=true )
    {
        $method = ($install)?'install':'uninstall';
        $is_core = ($namespace=='nitrocart')?1:0;
        return $this->_system_action($namespace,$path,$driver,$method,$is_core);
    }    

    private function _system_action($name_space='nitrocart',$path='feature',$driver='feature_customer',$method='install',$core=1)
    {
        $value= ($method=='install')?1:0;       

        $this->ci->load->library( $name_space.'/'.$path.'/'.$driver );
        if( $this->ci->$driver->$method($this) )
        {
            if( $this->db->table_exists('nct_systems')  )
            {
                //update only if core, otherwise ignore this line
                if($core)  
                {
                    if($row =  $this->ci->db->where( 'driver', $driver )->get('nct_systems')->row())
                    {
                        $this->ci->db->where( 'driver', $driver )->update('nct_systems', ['installed'=> $value ] ); 
                    }
                    else
                    {
                        $to_insert =    [
                                            'installed'=> $value, 
                                            'driver'=> $driver, 
                                            'description'=>$this->ci->$driver->description,
                                            'require'=>$this->ci->$driver->require,
                                            'system_type'=>$this->ci->$driver->system_type,
                                            'title' =>$this->ci->$driver->title,
                                        ]; 
                        $this->ci->db->insert('nct_systems', $to_insert); 
                    }
                    
                }
            }
            return true;               
        }
        return false;
    }


    /**
     * Upgrade the Module
     * @param  [type] $old_version [description]
     * @return [type]              [description]
     */
    public function upgrade(  $old_version = NULL )
    {

        /**
         * Load relevant libs
         */
        $this->load->driver('Streams');  
        $this->load->model('nitrocart/systems_m');
        $this->load->library('nitrocart/nitrocore_library'); 

        /**
         * Create the default status object
         * @var NCMessageObject
         */
        $status = new NCMessageObject();


        //get all installed
        $systems = $this->systems_m->where('installed',1)->get_all();


        foreach($systems as $system)
        {

            $path = ($system->system_type == 'feature') ? 'nitrocart/features/' : 'nitrocart/systems/';

            $path = $path . $system->driver;

            $this->load->library($path);


            /**
             * gets a new NCMessageObject()
             * @var [type]
             */
            $status = $this->{$system->driver}->upgrade( $this,  $old_version );

            if($status->getStatus() == false)
            {   
                //stop operation
                $this->session->set_flashdata(JSONStatus::Error,$status->getMessage());
                break;
            }            

        }

        /**
         * Returns true or false
         */
        return $status->getStatus();
    }    



    public function is_installed($driver)
    {
        if($row = $this->ci->db->where('driver',strtolower($driver))->get('nct_systems')->row())
        {
            if($row->installed == 1)
            {
                return true;
            }
        }
        return false;
    }


    public function clean($redir=true)
    {
        $this->uninstall_all_subsystems();  

        if( $this->db->table_exists('nct_systems')  )
        {
            $rows = $row = $this->db->where('system_type','feature')->where('installed',1)->get('nct_systems')->result();
            foreach($rows as $row) 
            {
                $this->feature($row->driver,false);
            }
        }

        if( $this->db->table_exists('nct_systems')  )
        {
            $this->dbforge->drop_table('nct_systems'); 
        }
        if( $this->db->table_exists('nct_admin_menu')  )
        {        
            $this->dbforge->drop_table('nct_admin_menu');    
        }

        if($redir)
        {
            $this->session->set_flashdata('success','System cleaned, please try to re-install');  
            redirect('admin/addons'); 
        }
        return;
    }


    public function checkFieldtypes()
    {

        $this->load->library('streams/type');


        $types = $this->type->field_types_array() ;

        if( ! ( array_key_exists('decimal', $types) ) )
        {
            $this->uninstallFail(JSONStatus::Notice,'Ensure you have the deimal field type installed prior to installing NitroCart. https://www.pyrocms.com/store/details/decimal');    
        }

        if( ! ( array_key_exists('boolean', $types) ) )
        {
            $this->uninstallFail(JSONStatus::Notice,'Ensure you have the boolean field type :: https://github.com/nitrocart/field_type_boolean');    
        }

        if( ! ( array_key_exists('iso31661', $types) ) )
        {
            $this->uninstallFail(JSONStatus::Notice,'Ensure you have the boolean field type :: https://github.com/nitrocart/field_type_iso31661');    
        }
        
        if( ! ( array_key_exists('global_regions', $types) ) )
        {
            $this->uninstallFail(JSONStatus::Notice,'Ensure you have the boolean field type :: https://github.com/nitrocart/field_type_global_regions');    
        }  

        return true;  
    }


    /**
     * Restrict access to users of PCMS 2.2.2-2.2.5
     * Clear/Drop logger tables if they exist - as this is a fresh install
     * Ensure we have the right field types installed
     * Install core tables (note:Core tables are not subsystems)
     * Install subsystems 1-10
     * At any point if the install fails we need to redirect to addons, and warn message. Do not return false as this is useless and not informative
     */
    public function install_nitrocart($segment_6=false)
    {

        /**
         * Check we have the right cms version
         */
        if ( CMS_VERSION != '0.0.2' )
        {
            $this->uninstallFail( JSONStatus::Error ,'Invalid CMS Version : Ensure you have the NitroCMS 0.0.2' );    
        }

        /**
         * Check we have the right field types setup in the cms
         */
        $this->checkFieldtypes();



        /**
         * Start prepping the system by removing tables that are going to harm install
         */
        if($this->db->table_exists('shop_logger'))
        {
            $this->dbforge->drop_table('shop_logger');
        }
        



        /**
         * Only use clean for stubborn installs
         * @var [type]
         */
        if( ( $segment_6 == 'clean' ) )
        {
            $this->clean();
        }



        /**
         * Only use new for stubborn installs, however this will 
         * prep the system if the core tables do not exist
         * @var [type]
         */
        if( ($segment_6 == 'new' ) OR  
            ( ! $this->db->table_exists('nct_systems') )  OR  
            ( ! $this->db->table_exists('nct_admin_menu') ))
        {
            $this->clean(false); 
            $this->install_tables( $this->core_tables );   
            $this->db->insert_batch('nct_systems', $this->_moduleData ); 
        }

       

       // return true;
        

        /*
         * Installs all subsystems in seq order, if status code 
         * returned does NOT= 20 then we have a failure
         * @var [type]
         */
        $pos = $this->_install_all_subsystems();

        if($pos == 20)
        {
            return true;
        }

        return false;    
    }


    /**
     * Uninstalls nitrocart from the db system
     * @return [type] [description]
     */
    public function uninstall_nitrocart()
    {
        /**
         * Load config file
         */
        $this->config->load('nitrocart/install/' . NC_CONFIG);



        /**
         * Collect settings required for uninstall pre-checks
         * @var [type]
         */
        $extensions_fast_uninstall = $this->config->item('uninstall/extensions_fast_uninstall');
        $features_fast_uninstall = $this->config->item('uninstall/features_fast_uninstall');

  
        /**
         * Default data and messages
         * @var boolean
         */
        $_message = 'Please <a href="admin/'.NC_ADMIN_ROUTE.'/subsystems">uninstall</a> ALL subsystems first. Or try to <a class="confirm" href="admin/addons/modules/uninstall/shop/clean">Force clean</a> PyroCMS of NitroCart';
        $_message2 = "You can not uninstall the NitroCart module until you remove all features and 3rd party modules for NitroCart first.";
        $_message3 = 'Please remove all Features before uninstalling.';

        if( $this->db->table_exists('nct_modules'))
        {
            if( ! $extensions_fast_uninstall)
            {
                //fail if an extern mod is installed
                if(($row = $this->db->where('namespace !=','nitrocart')->get('nct_modules')->row() ) )
                {
                    $this->uninstallFail(JSONStatus::Notice, $_message2 );            
                    return false; 
                }   
            }
            //else - we need to find a way to uninstall
        }

        if( $this->db->table_exists('nct_systems')  )
        {
            if( ! $features_fast_uninstall )
            {
                //what would be good is to uninstall each feature.
                if($row = $this->db->where('system_type','feature')->where('installed',1)->get('nct_systems')->row() )
                {
                    $this->uninstallFail('notice', $_message3);            
                    return false; 
                }
            }

            $rows = $row = $this->db->where('system_type','feature')->where('installed',1)->get('nct_systems')->result();
            foreach($rows as $row) 
            {
                $this->feature($row->driver,false);
            }
        }



        /**
         * Remove all subsystems in reverse order
         */
        $this->uninstall_all_subsystems();    

      

        /**
         * [$uninstalled description]
         * @var [type]
         */
        return $this->uninstall_core_structures();
    }

  


    /**
     * Un-Installs all the subsystems from 10-1
     * @return [type] [description]
     */
    public function uninstall_all_subsystems()
    {
        $this->subsystem('system_z_admin_layer',false);
        $this->subsystem('system_d_products',false);
        $this->subsystem('system_b_zones',false);
        $this->subsystem('system_a_core',false); 
        return true;            
    }

    public function uninstall_all_features()
    {
        if( $this->db->table_exists('nct_systems')  )
        {
            $rows = $row = $this->db->where('system_type','feature')->where('installed',1)->get('nct_systems')->result();
            foreach($rows as $row) 
            {
                $this->feature($row->driver,false);
            }
        }  
        return true;
    }

    /**
     * Installs all subsystems for nitrocart
     * @return [type] [description]
     */
    public function _install_all_subsystems()
    {
        set_time_limit ( 220 );

        if($this->subsystem('system_a_core')) 
        {

           if($this->subsystem('system_b_zones')) 
           {

                if($this->subsystem('system_d_products')) 
                {  

                    if($this->subsystem('system_z_admin_layer')) 
                    {         
                        set_time_limit ( 60 );
                        return 20;               
                    }

                    return 6;
                }
                    
                return 4;
           }

           return 2;
        }

        return false;    
    }



    private function uninstallFail( $status, $message, $redir='admin/addons/' )
    {
        $this->ci->session->set_flashdata($status,$message);            
        redirect($redir);    
    }





    /**
     * Removes tables, email templates and settings from the database
     * This should only be called once all other data has been removef
     * @return [type] [description]
     */
    private function uninstall_core_structures()
    {
        /**
         * Remove email templates
         */
        $this->ci->db->delete('email_templates', ['module' => 'nitrocart']);


        /**
         * Remove settings from db
         */
        $this->ci->db->delete('settings', ['module' => 'nitrocart']);

        /**
         * Remove core tables
         * @var [type]
         */
        foreach ($this->core_tables as $table_name => $table) 
        {
            if($this->ci->db->table_exists( $table_name ))
            {        
                $this->dbforge->drop_table($table_name);
            }
        }

        return true;
    }
          


    /**
     * [$core_tables description]
     * @var array
     */
    private $core_tables = 
    [
        'nct_systems'     => 
        [
            'driver'        => ['type' => 'VARCHAR', 'constraint' => 100, 'primary' => true, 'unique' => true, 'key' => 'index_driver'],
            'require'       => ['type' => 'VARCHAR', 'constraint' => 30, 'default'=>''],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 100,],
            'description'   => ['type' => 'TEXT',],
            'installed'     => ['type' => 'INT', 'constraint' => 1, 'default' => 0,],
            'system_type'   => ['type' => 'VARCHAR', 'constraint' => 20,], 
        ],
        'nct_admin_menu'   => 
        [
            'id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true],
            'label'         => ['type' => 'VARCHAR', 'constraint' => 80, 'default'=>''],
            'uri'           => ['type' => 'VARCHAR', 'constraint' => 100, 'default'=>''],
            'menu'          => ['type' => 'VARCHAR', 'constraint' => 100,],
            'module'        => ['type' => 'VARCHAR', 'constraint' => 100,],
            'order'         => ['type' => 'INT', 'constraint' => 4,],
            'visible'       => ['type' => 'INT', 'constraint' => 1, 'default'=>1],
        ],
    ];    



    /**
     * Infor for subsystems and features
     * @var [type]
     */
    public $_moduleData = 
    [
            [
                'title'         => '01) Core-Routing and Module Subsystem',
                'driver'        => 'system_a_core',
                'require'       => '', 
                'description'   => 'Modules, Routes DB, and other core table structures',
                'system_type'   => 'subsystem',
            ],
            [
                'title'         => '02) User profile management',
                'driver'        => 'system_b_zones',                
                'require'       => 'system_a_core',
                'description'   => 'Countries and Addresses DB Tables, and additional meta data for user account management',
                'system_type'   => 'subsystem',
            ],
            [
                'title'         => '04) Products and Variation Subsystem',
                'driver'        => 'system_d_products',                
                'require'       => 'system_b_packages', 
                'description'   => 'Product and variations core structures and streams',
                'system_type'   => 'subsystem',
            ],
            [
                'title'         => '06) Core Admin Menus + Routes',
                'driver'        => 'system_z_admin_layer',                
                'require'       => 'system_d_content', 
                'description'   => 'Admin Menu',
                'system_type'   => 'subsystem', 
            ],
    ];

                          
                                
}