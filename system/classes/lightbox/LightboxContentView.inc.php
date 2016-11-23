<?php
/* --------------------------------------------------------------
   LightboxContentView.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LightboxContentView extends ContentView
{
	protected $v_lightbox_mode = false;
	protected $v_left_buttons = array();
	protected $v_right_buttons = array();
	protected $v_parameters = array();
	protected $v_javascript_section = '';
	
	public function set_lightbox_mode( $p_lightbox_mode )
	{
		if( !is_bool( $p_lightbox_mode ) ) trigger_error ( 'set_lightbox_mode: typeof($p_lightbox_mode) != boolean', E_USER_ERROR );	
		$this->v_lightbox_mode = $p_lightbox_mode;
	}
	
	public function set_javascript_section( $p_javascript_section )
	{
		if( preg_match( '/[\W]+/', $p_javascript_section ) ) trigger_error ( 'set_javascript_section: typeof($p_javascript_section) contains unexpected characters', E_USER_ERROR );
		$this->v_javascript_section = $p_javascript_section;
	}
	
	public function set_lightbox_button( $p_position, $p_text_variable, $p_class_array )
	{
		if( $p_position == 'left' )
		{
			$this->v_left_buttons[$p_text_variable] = $p_class_array;
		}
		else if( $p_position == 'right' )
		{
			$this->v_right_buttons[$p_text_variable] = $p_class_array;
		}
		
	}
	
	public function set_lightbox_parameters( $p_parameters )
	{
		if( is_array( $p_parameters ) )
		{
			$this->v_parameters = $p_parameters;
		}
	}
	
	public function build_html( $p_content_data_array = false, $p_template_file = false )
	{
		$this->prepare_data();
		if( $this->v_lightbox_mode )
		{
			$this->set_content_data( 'lightbox_parameters', $this->v_parameters );
			if( array_key_exists( 'section', $this->v_parameters) && array_key_exists( 'message', $this->v_parameters) )
			{
				$coo_text_mgr = MainFactory::create_object( 'LanguageTextManager', array( $this->v_parameters['section'], $_SESSION['languages_id'] ) );
				$t_lightbox_message = $coo_text_mgr->get_text( $this->v_parameters['message'] );
				if( trim( $t_lightbox_message ) != '' )
				{
					$this->set_content_data( 'lightbox_message', $t_lightbox_message );
				}
			}
			
			$this->init_smarty();
			$this->set_flat_assigns(false);
 			
			$t_lightbox_content = parent::build_html();
			
			if( is_array( $this->v_parameters ) && count( $this->v_parameters ) > 0 )
			{
				if( array_key_exists( 'buttons', $this->v_parameters) )
				{
					$t_buttons = explode( '-', $this->v_parameters['buttons'] );
					if( in_array( 'no', $t_buttons) )
					{
						$this->set_lightbox_button('left', 'no', array('lightbox_close', 'no'));
					}
					if( in_array( 'cancel', $t_buttons) )
					{
						$this->set_lightbox_button('left', 'cancel', array('lightbox_close', 'cancel'));
					}
					if( in_array( 'yes', $t_buttons) )
					{
						$this->set_lightbox_button('right', 'yes', array('green', 'yes'));
					}
					if( in_array( 'save', $t_buttons) )
					{
						$this->set_lightbox_button('right', 'save', array('save', 'green'));
					}
					if( in_array( 'save_close', $t_buttons) )
					{
						$this->set_lightbox_button('right', 'save_close', array('save_close', 'green'));
					}
					if( in_array( 'delete', $t_buttons) )
					{
						$this->set_lightbox_button('right', 'delete', array('delete', 'red'));
					}
					if( in_array( 'confirm', $t_buttons) )
					{
						$this->set_lightbox_button('right', 'confirm', array('confirm', 'green'));
					}
					if( in_array( 'discard', $t_buttons) )
					{
						$this->set_lightbox_button('right', 'discard', array('discard', 'red'));
					}
				}
			}
			
			$t_show_button = false;
			if( count( $this->v_left_buttons ) > 0 || count( $this->v_right_buttons ) > 0 ) $t_show_button = true;
			
			$this->content_array = array();
			$this->set_template_dir( DIR_FS_CATALOG.'admin/html/content/' );
			$this->set_content_template( 'lightbox_container.html' );
			$this->set_content_data( 'buttons_show', $t_show_button );
			$this->set_content_data( 'buttons_left', $this->v_left_buttons );
			$this->set_content_data( 'buttons_right', $this->v_right_buttons );
			$this->set_content_data( 'parameters', $this->v_parameters );
			$this->set_content_data( 'javascript_section', $this->v_javascript_section );
			$this->set_content_data( 'lightbox_content', $t_lightbox_content );
			
			$this->init_smarty();
			$this->set_flat_assigns(false);
			
			$t_return_html = parent::build_html();
		}
		else
		{
			$this->init_smarty();
			$this->set_flat_assigns(false);
			
			$t_return_html = parent::build_html();
		}
		
		return $t_return_html;
	}
	
	public function get_html( $p_get_array = array(), $p_post_array = array() )	
	{
		$t_html_output = $this->get_html_array( $p_get_array, $p_post_array );
		return $t_html_output[ 'html' ];
	}
	
	public function get_html_array( $p_get_array = array(), $p_post_array = array() )	
	{
		$t_html_output[ 'html' ] = $this->build_html();
		return $t_html_output;
	}
}
