<?php
/**
 * 
 * Open Search Auto Discovery for Wordpress
 * PHP version 5
 *   
 * ---------------------------------------------------------------------------
 *  
 * Open Search Auto Discovery for Wordpress
 * Author: Avice Devereux <ck+wpi@animepaper.net>
 * Author: Noah Ark <noah@kakkoi.net> 
 * Copyright (c): 2006-2008 Kaizeku Ban, all rights reserved
 * Version: 1.57
 *   
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 * ---------------------------------------------------------------------------
 *      
 * @category	OpenSearch
 * @package		WP-iStalker  
 * @author		Avice Devereux <ck+wpi@animepaper.net>
 * @author		NH. Noah <noah@kakkoi.net> 
 * @copyright 	2006 - 2008 Kaizeku Ban, all rights reserved 
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser GPL  
 * @version		$Id$ 
 */


class wpiOSD
{		
	const OSD_SEARCHTERM_PARAMS = '{searchTerms}';
	public $osd;
	
	public function __construct()
	{	
		$this->_build();
	}
	
	
	private function _build()
	{
		// setup metadata
		$osd = new stdClass();
		
		$osd->blog_url 		= get_option('home');
		$osd->search_param  = '?s='.self::OSD_SEARCHTERM_PARAMS;
		$osd->blog_name		= get_option('blogname');
		$osd->blog_desc		= get_option('blogdescription');
		$osd->blog_favicon	= wpi_get_favicon_url();
		$osd->blog_html_type= get_option('html_type');
		$osd->language		= get_hreflang();
		
		
		$this->osd = $osd;
		$this->_setContent();
		unset($osd);
	}
	
	public function startTag()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">'."\n";
		return $xml;
	}
	
	public function getContent()
	{
		if (is_object($this->osd) && isset($this->content))
		{
			return $this->startTag().$this->content.$this->endTag();
		}
	}
	
	private function _setContent()
	{
		$content = false;
		
		if (is_object($this->osd))
		{
			$blog_name = htmlentities2($this->osd->blog_name);
				
			$title = sprintf(__('%s Search',WPI_META),$blog_name);
			$desc  = $blog_name.', '.$this->osd->blog_desc;
			$email = 'postmaster+abuse@'.parse_url($this->osd->blog_url,PHP_URL_HOST);
			
			$search_uri = $this->osd->blog_url.'/'.$this->osd->search_param;
			
			$xml = "\t".'<ShortName>'.$title.'</ShortName>'."\n";
			$xml .= "\t".'<Description>'.$desc.'</Description>'."\n";
  			$xml .= "\t".'<Contact>'.$email.'</Contact>'."\n";
			$xml .= "\t".'<Url type="'.	$this->osd->blog_html_type.'" method="get" template="'.$search_uri.'"></Url>'."\n";
			$xml .= "\t".'<LongName>'.$title.'</LongName>'."\n";
			
			if ($this->osd->blog_favicon){
				$xml .= "\t".'<Image height="16" width="16" type="image/vnd.microsoft.icon">'.$this->osd->blog_favicon.'</Image>'."\n";
			}
			
			$xml .= "\t".'<Query role="example" searchTerms="blogging" />';
			$xml .= "\n\t".'<Developer>ChaosKaizer</Developer>';
			$xml .= "\n\t".'<Attribution>Search data &amp;copy; '.date('Y',$_SERVER['REQUEST_TIME']).', '.$blog_name.', Some Rights Reserved. CC by-nc 2.5.</Attribution>';
			$xml .= "\n\t".'<SyndicationRight>open</SyndicationRight>';
			$xml .= "\n\t".'<AdultContent>false</AdultContent>';
			$xml .= "\n\t".'<Language>'.$this->osd->language.'</Language>';
			$xml .= "\n\t".'<OutputEncoding>UTF-8</OutputEncoding>';
			$xml .= "\n\t".'<InputEncoding>UTF-8</InputEncoding>'."\n";
			  			
		}
		
		$this->content = $xml;
		unset($xml);
	}
	
	public function endTag()
	{
		return '</OpenSearchDescription>';
	}
	

	
	public function __destruct()
	{
		unset($this);
	}	
}
?>