<?php
if (!defined('KAIZEKU')) { die(42); }
/**
 * $Id$
 * Wp-iStalker dynamic sidebar widgets
 * @package WordPress
 */

class wpiSidebar
{
	
	public $tpl;
	
	public $wp_filter_id;
	
	
	public function __construct(){
		$this->_template('widget');
		
	}

	public function setSidebar()
	{
	
		if (IS_WIDGET && WP_VERSION_MAJ >= 2)
		{
			$options = array(
							'home-1','home-2','home-3', /* 1,3 */
							'single-1','single-2','single-3', /* 4,6 */
							'page-1','page-2','page-3', /* 7,9 */
							'category','tag','archive', /* 10,12 */
							'author-1','author-2','author-3', /* 13,15 */
							'others','comment' /* 16, 17 */
						);
						
			$this->register($options,'register_sidebar');
		}
		
	}
	
	private function _template($key)
	{
        $key = (string) $key ;
		$tpl = array();

        $tpl['widget'] = array(
			'name' => 'widget-',
			'before_widget' => '<li id="%1$s" class="cb widgets %2$s cf">' .
							   '<div class="append-1 prepend-1">',
			'after_widget' 	=> '</div></div></li>',
			'before_title' 	=> '<h3 class="widget-title tr ox cf"><cite class="rtx">&nbsp;</cite><dfn class="rn">',
			'after_title'	=> '</dfn></h3><div class="widget-content cb">' ) ;

        $this->tpl[$key] = $tpl[$key];	
        return $this;
	}
	
	
	public function register($sidebar, $callback)
	{

		$options 	= $this->tpl['widget'];
		$arg_type 	= gettype($sidebar);

			switch ($arg_type):
				case 'array':
					foreach($sidebar as $key):
						$options['name'] =	$this->tpl['widget']['name'].$key;
						call_user_func_array($callback,array($options));
					endforeach;
				break;
				case 'string':
					$options['name'] =	$this->tpl['widget']['name'].$key;
					call_user_func_array($callback,array($options));
				break;
			endswitch;
			
			unset($options,$sidebar,$arg_type);

	}
	
		
	public static function hasWidget($sidebar_id=1){
		
		$widgets	= wp_get_sidebars_widgets();
		$key		= (string) 'sidebar-'. $sidebar_id;
	
		return (isset($widgets[$key]));	
		
	}
	
	
}
?>