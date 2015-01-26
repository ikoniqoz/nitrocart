<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Csv_library extends ViewObject
{

    /** columns names retrieved after parsing */
    var $fields;

    /** separator used to explode each line */
    var $separator = ',';


	public function __construct($params = [])
	{
        parent::__construct();
	}

    /**
     * Parse a text containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_text($p_Text)
    {
        $lines = explode("\n", $p_Text);
        return $this->parse_lines($lines);
    }

    /**
     * Parse a file containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @return    array
     */
    function parse_file($p_Filepath)
    {
        $lines = file($p_Filepath);
        return $this->parse_lines($lines);
    }

    /**
     * Parse an array of text lines containing CSV formatted data.
     *
     * @access    public
     * @param    array
     * @return    array
     */
    function parse_lines($p_CSVLines)
    {
        $content = false;
        foreach( $p_CSVLines as $line_num => $line ) {
            if( $line != '' ) { // skip empty lines
                $elements = explode($this->separator, $line);

                if( !is_array($content) ) { // the first line contains fields names
                    $this->fields = $elements;
                    $content = [];
                } else {
                    $item =[];
                    foreach( $this->fields as $id => $field ) {
                        if( isset($elements[$id]) ) {
                            $item[$field] = $elements[$id];
                        }
                    }
                    $content[] = $item;
                }
            }
        }
        return $content;
    }
}