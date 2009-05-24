<?php
/**
 * WP-iStalker Chrome Min 
 * Back Compatible Classes and Functions
 * PHP 4 & 5
 * 
 * @package		WordPress
 * @subpackage	wp-istalker-chrome-min
 * 
 * @category	ToolsAndUtilities
 * @author		Avice (ChaosKaizer) De'vereux <ck+wp-istalker-min@istalker.net>
 * @author		NH. Noah <noah+wp-istalker-min@kakkoi.net>
 * @copyright 	2007 - 2009 Avice De'vereux, NH. Noah
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License v2 
 * @version 	CVS: $Id$
 * @since 		0.1
 */

/**
  * PHP4 DirectoryIterator Class
  * 
  * This class implements the SPL DirectoryIterator in PHP4.
  * Very usefull if wanting to traverse directories in an OO style 
  * 
  * @category 	PHP4 DirectoryIterator class 
  * @author		Adrian Rotea <adirotea@yahoo.com> 
  * @link		http://www.weberdev.com/get_example-4180.html
  * @copyright	Copyright © 2005, Adrian Rotea <adirotea@yahoo.com> 
  */
if (!class_exists('DirectoryIterator')) {
    class DirectoryIterator {       
        var $key;
        var $current;
        var $valid = true;
        var $entry;       
        var $path;
        var $handle;
   

        /** Construct a directory iterator from a path-string.
         *
         * \param $path directory to iterate.
         */
        function DirectoryIterator($path) {
            if (substr($path, strlen($path) - 1, 1) != '/') {
                $path = $path . '/';
            }
           
            $this->handle = opendir($path);
            $this->path = $path;
        }
   
        /** \return The opened path.
         */
        function getPath() {
            return $this->path;
        }   
   
        /** \return The current file name.
         */
        function getFileName() {
            return $this->entry;
        }   
   
        /** \return The current entries path and file name.
         */
        function getPathName() {
            return $this->getPath() . $this->getFileName();
        }   
   
        /** \return The current entry's permissions.
         */
        function getPerms() {
            return fileperms($this->getPathName());
        }
   
        /** \return The current entry's inode.
         */
        function getInode() {
            return fileinode($this->getPathName());
        }
   
        /** \return The current entry's size in bytes .
         */
        function getSize() {
            return filesize($this->getPathName());
        }
   
        /** \return The current entry's owner name.
         */
        function getOwner() {
            return fileowner($this->getPathName());
        }
   
        /** \return The current entry's group name.
         */
        function getGroup() {
            return filegroup($this->getPathName());
        }
   
        /** \return The current entry's last access time.
         */
        function getATime() {
            return fileatime($this->getPathName());
        }
   
        /** \return The current entry's last modification time.
         */
        function getMTime() {
            return filemtime($this->getPathName());
        }
   
        /** \return The current entry's last change time.
         */
        function getCTime() {
            return filectime($this->getPathName());
        }
   
        /** \return The current entry's size in bytes .
         */
        function getType() {
            return filetype($this->getPathName());
        }
   
        /** \return Whether the current entry is writeable.
         */
        function isWritable() {
            return is_writable($this->getPathName());
        }
   
        /** \return Whether the current entry is readable.
         */
        function isReadable() {
            return is_readable($this->getPathName());
        }
   
        /** \return Whether the current entry is executable.
         */
        function isExecutable() {
            if (function_exists('is_executable')) {
                return is_executable($this->getPathName());
            }
        }
   
        /** \return Whether the current entry is .
         */
        function isFile() {
            return is_file($this->getPathName());
        }
   
        /** \return Whether the current entry is a directory.
         */
        function isDir() {
            return is_dir($this->getPathName());
        }   
   
        /** \return Whether the current entry is either '.' or '..'.
         */
        function isDot() {
            return $this->isDir() && ($this->entry == '.' || $this->entry == '..');
        }   
   
        /** \return whether the current entry is a link.
         */
        function isLink() {
            return is_link($this->getPathName());
        }       

        /** \Move to next entry.
         */                 
        function next() {
            $this->valid = $this->getFile();
            $this->key++;
        }
   
        /** \Rewind dir back to the start.
         */           
        function rewind() {
            $this->key = 0;
            rewinddir($this->handle);
            $this->valid = $this->getFile();
        }
   
        /** \Check whether dir contains more entries.
         */           
        function valid() {
            if ($this->valid === false) {
                $this->close();
            }
                   
            return $this->valid;
        }
   
        /** \Return current dir entry.
         */           
        function key() {
            return $this->key;
        }
   
        /** \Return this.
         */       
        function current() {           
            return $this;
        }
   
        /** \Close dir.
         */           
        function close() {
            closedir($this->handle);
        }       
       
        function getFile() {
            if ( false !== ($file = readdir($this->handle)) ) {
                $this->entry = $file;
                return true;
            } else {
                return false;
            }
        }       
    }   
} 
?>