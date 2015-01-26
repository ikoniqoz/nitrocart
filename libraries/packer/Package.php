<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
  class Package implements DVDoug\BoxPacker\Box
  {

    public function __construct($package)
    {
        //set weight to zero.
        //$package->weight = 0;

        //$aReference, $aOuterWidth,$aOuterLength,$aOuterDepth,$aEmptyWeight,$aInnerWidth,$aInnerLength,$aInnerDepth,$aMaxWeight
        $this->reference = $package->name;
        $this->box_id = $package->code;        
        $this->outerWidth = $package->outer_width;
        $this->outerLength = $package->outer_length;
        $this->outerDepth = $package->outer_height;
        $this->emptyWeight = $package->cur_weight;
        $this->innerWidth = $package->width;
        $this->innerLength = $package->length;
        $this->innerDepth = $package->height;
        $this->maxWeight = $package->max_weight;
        $this->innerVolume = $this->innerWidth * $this->innerLength * $this->innerDepth;

    }


    public function getBoxID()
    {
      return $this->box_id;
    }

    public function getReference()
    {
      return $this->reference;
    }

    public function getOuterWidth()
    {
      return $this->outerWidth;
    }

    public function getOuterLength()
    {
      return $this->outerLength;
    }

    public function getOuterDepth()
    {
      return $this->outerDepth;
    }

    public function getEmptyWeight()
    {
      return $this->emptyWeight;
    }

    public function getInnerWidth()
    {
      return $this->innerWidth;
    }

    public function getInnerLength()
    {
      return $this->innerLength;
    }

    public function getInnerDepth()
    {
      return $this->innerDepth;
    }

    public function getInnerVolume()
    {
      return $this->innerVolume;
    }

    public function getMaxWeight()
    {
      return $this->maxWeight;
    }

  }

  class Product implements DVDoug\BoxPacker\Item {

    public $variance_id;

    public function __construct($admin_link='Product',$front_link='Product', $variance = NULL)
    {
        //make sure we have an variance object
        if($variance==NULL)
        {
            $variance = (object)array();
            $variance->id = 0;
            $variance->name = 'Standard';
        }

        $this->variance_id = $variance->id;
        $this->description = $front_link; 
        $this->admin_description = $admin_link; 
        $this->width = $variance->width;
        $this->length = $variance->length;
        $this->depth = $variance->height;
        $this->weight = $variance->weight;
        $this->volume = $this->width * $this->length * $this->depth;
    }

    public function getDescription() {
      return $this->description;
    }

    public function getAdminDescription() {
      return $this->admin_description;
    }

    public function getWidth() {
      return $this->width;
    }

    public function getLength() {
      return $this->length;
    }

    public function getDepth() {
      return $this->depth;
    }

    public function getWeight() {
      return $this->weight;
    }

    public function getVolume() {
      return $this->volume;
    }
 }
