<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File::CSV
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 1997-2008,
 * Vincent Blavet <vincent@phpconcept.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   File_Formats
 * @package    Marifa\Base
 * @subpackage Update\Compresion\Pear
 * @author     Vincent Blavet <vincent@phpconcept.net>
 * @copyright  1997-2010 The Authors
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Archive_Tar
 */
defined('APP_BASE') || die('No direct access allowed.');

/**
 * Constante para formato TAR.
 * @package    Marifa\Base
 * @subpackage Update\Compresion\Pear
 */
define('ARCHIVE_TAR_ATT_SEPARATOR', 90001);

/**
 * Constante para formato TAR.
 * @package    Marifa\Base
 * @subpackage Update\Compresion\Pear
 */
define('ARCHIVE_TAR_END_BLOCK', pack("a512", ''));

/**
 * Creates a (compressed) Tar archive
 *
 * @package    Marifa\Base
 * @subpackage Update\Compresion\Pear
 * @author     Vincent Blavet <vincent@phpconcept.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Revision$
 */
class Base_Update_Compresion_Pear_Tar {

	/**
	 * Name of the Tar
	 * @var string
	 */
	var $_tarname = '';

	/**
	 * if TRUE, the Tar file will be gzipped
	 * @var boolean
	 */
	var $_compress = FALSE;

	/**
	 * Type of compression : 'none', 'gz' or 'bz2'
	 * @var string Type of compression : 'none', 'gz' or 'bz2'
	 */
	var $_compress_type = 'none';

	/**
	 * Explode separator
	 * @var string Explode separator
	 */
	var $_separator = ' ';

	/**
	 * file descriptor
	 * @var
	 */
	var $_file = 0;

	/**
	 * string Local Tar name of a remote Tar (http:// or ftp://)
	 * @var
	 */
	var $_temp_tarname = '';

	/**
	 * regular expression for ignoring files or directories
	 * @var string
	 */
	var $_ignore_regexp = '';

	/**
	 * PEAR_Error object
	 * @var object
	 */
	var $error_object = NULL;

	// {{{ constructor
	/**
	 * Archive_Tar Class constructor. This flavour of the constructor only
	 * declare a new Archive_Tar object, identifying it by the name of the
	 * tar file.
	 * If the compress argument is set the tar will be read or created as a
	 * gzip or bz2 compressed TAR file.
	 *
	 * @param string $p_tarname  The name of the tar archive to create
	 * @param string $p_compress can be NULL, 'gz' or 'bz2'. This
	 *               parameter indicates if gzip or bz2 compression
	 *               is required.  For compatibility reason the
	 *               boolean value 'TRUE' means 'gz'.
	 *
	 * @access public
	 */
	function __construct($p_tarname, $p_compress = NULL)
	{
		$this->_compress = FALSE;
		$this->_compress_type = 'none';
		if (($p_compress === NULL) || ($p_compress == ''))
		{
			if (@file_exists($p_tarname))
			{
				if ($fp = @fopen($p_tarname, "rb"))
				{
					// look for gzip magic cookie
					$data = fread($fp, 2);
					fclose($fp);
					if ($data == "\37\213")
					{
						$this->_compress = TRUE;
						$this->_compress_type = 'gz';
						// No sure it's enought for a magic code ....
					}
					elseif ($data == "BZ")
					{
						$this->_compress = TRUE;
						$this->_compress_type = 'bz2';
					}
				}
			}
			else
			{
				// probably a remote file or some file accessible
				// through a stream interface
				if (substr($p_tarname, -2) == 'gz')
				{
					$this->_compress = TRUE;
					$this->_compress_type = 'gz';
				}
				elseif ((substr($p_tarname, -3) == 'bz2') ||
						(substr($p_tarname, -2) == 'bz'))
				{
					$this->_compress = TRUE;
					$this->_compress_type = 'bz2';
				}
			}
		}
		else
		{
			if (($p_compress === TRUE) || ($p_compress == 'gz'))
			{
				$this->_compress = TRUE;
				$this->_compress_type = 'gz';
			}
			elseif ($p_compress == 'bz2')
			{
				$this->_compress = TRUE;
				$this->_compress_type = 'bz2';
			}
			else
			{
				$this->_error("Unsupported compression type '$p_compress'\nSupported types are 'gz' and 'bz2'.\n");
				return FALSE;
			}
		}
		$this->_tarname = $p_tarname;
		if ($this->_compress)
		{ // assert zlib or bz2 extension support
			/**
			  if ($this->_compress_type == 'gz')
			  $extname = 'zlib';
			  else if ($this->_compress_type == 'bz2')
			  $extname = 'bz2';

			  //if (!extension_loaded($extname))
			  //{
			  //	PEAR::loadExtension($extname);
			  //}
			  if (!extension_loaded($extname))
			  {
			  $this->_error("The extension '$extname' couldn't be found.\n".
			  "Please make sure your version of PHP was built ".
			  "with '$extname' support.\n");
			  return FALSE;
			  } */
		}
	}

	// }}}
	// {{{ destructor
	/**
	 * @ignore
	 */
	function __destruct()
	{
		$this->_close();
		// ----- Look for a local copy to delete
		if ($this->_temp_tarname != '')
		{
			@unlink($this->_temp_tarname);
		}
	}

	// }}}
	// {{{ create()
	/**
	 * This method creates the archive file and add the files / directories
	 * that are listed in $p_filelist.
	 * If a file with the same name exist and is writable, it is replaced
	 * by the new tar.
	 * The method return FALSE and a PEAR error text.
	 * The $p_filelist parameter can be an array of string, each string
	 * representing a filename or a directory name with their path if
	 * needed. It can also be a single string with names separated by a
	 * single blank.
	 * For each directory added in the archive, the files and
	 * sub-directories are also added.
	 * See also create_modify() method for more details.
	 *
	 * @param array $p_filelist An array of filenames and directory names, or a
	 *              single string with names separated by a single
	 *              blank space.
	 *
	 * @return TRUE on success, FALSE on error.
	 * @see    create_modify()
	 * @access public
	 */
	function create($p_filelist)
	{
		return $this->create_modify($p_filelist, '', '');
	}

	// }}}
	// {{{ add()
	/**
	 * This method add the files / directories that are listed in $p_filelist in
	 * the archive. If the archive does not exist it is created.
	 * The method return FALSE and a PEAR error text.
	 * The files and directories listed are only added at the end of the archive,
	 * even if a file with the same name is already archived.
	 * See also create_modify() method for more details.
	 *
	 * @param array $p_filelist An array of filenames and directory names, or a
	 *              single string with names separated by a single
	 *              blank space.
	 *
	 * @return TRUE on success, FALSE on error.
	 * @see    create_modify()
	 * @access public
	 */
	function add($p_filelist)
	{
		return $this->add_modify($p_filelist, '', '');
	}

	// }}}
	// {{{ extract()
	/**
	 * @ignore
	 * @param type $p_path
	 * @param type $p_preserve
	 * @return type
	 */
	function extract($p_path = '', $p_preserve = FALSE)
	{
		return $this->extract_modify($p_path, '', $p_preserve);
	}

	// }}}
	// {{{ list_content()
	/**
	 * @ignore
	 * @return int
	 */
	function list_content()
	{
		$v_list_detail = array();

		if ($this->_open_read())
		{
			if ( ! $this->_extract_list('', $v_list_detail, "list", '', ''))
			{
				unset($v_list_detail);
				$v_list_detail = 0;
			}
			$this->_close();
		}

		return $v_list_detail;
	}

	// }}}
	// {{{ create_modify()
	/**
	 * This method creates the archive file and add the files / directories
	 * that are listed in $p_filelist.
	 * If the file already exists and is writable, it is replaced by the
	 * new tar. It is a create and not an add. If the file exists and is
	 * read-only or is a directory it is not replaced. The method return
	 * FALSE and a PEAR error text.
	 * The $p_filelist parameter can be an array of string, each string
	 * representing a filename or a directory name with their path if
	 * needed. It can also be a single string with names separated by a
	 * single blank.
	 * The path indicated in $p_remove_dir will be removed from the
	 * memorized path of each file / directory listed when this path
	 * exists. By default nothing is removed (empty path '')
	 * The path indicated in $p_add_dir will be added at the beginning of
	 * the memorized path of each file / directory listed. However it can
	 * be set to empty ''. The adding of a path is done after the removing
	 * of path.
	 * The path add/remove ability enables the user to prepare an archive
	 * for extraction in a different path than the origin files are.
	 * See also add_modify() method for file adding properties.
	 *
	 * @param array  $p_filelist   An array of filenames and directory names,
	 *                             or a single string with names separated by
	 *                             a single blank space.
	 * @param string $p_add_dir    A string which contains a path to be added
	 *                             to the memorized path of each element in
	 *                             the list.
	 * @param string $p_remove_dir A string which contains a path to be
	 *                             removed from the memorized path of each
	 *                             element in the list, when relevant.
	 *
	 * @return boolean TRUE on success, FALSE on error.
	 * @access public
	 * @see add_modify()
	 */
	function create_modify($p_filelist, $p_add_dir, $p_remove_dir = '')
	{
		$v_result = TRUE;

		if ( ! $this->_open_write())
		{
			return FALSE;
		}

		if ($p_filelist != '')
		{
			if (is_array($p_filelist))
			{
				$v_list = $p_filelist;
			}
			elseif (is_string($p_filelist))
			{
				$v_list = explode($this->_separator, $p_filelist);
			}
			else
			{
				$this->_clean_file();
				$this->_error('Invalid file list');
				return FALSE;
			}

			$v_result = $this->_add_list($v_list, $p_add_dir, $p_remove_dir);
		}

		if ($v_result)
		{
			$this->_write_footer();
			$this->_close();
		}
		else
		{
			$this->_clean_file();
		}

		return $v_result;
	}

	// }}}
	// {{{ add_modify()
	/**
	 * This method add the files / directories listed in $p_filelist at the
	 * end of the existing archive. If the archive does not yet exists it
	 * is created.
	 * The $p_filelist parameter can be an array of string, each string
	 * representing a filename or a directory name with their path if
	 * needed. It can also be a single string with names separated by a
	 * single blank.
	 * The path indicated in $p_remove_dir will be removed from the
	 * memorized path of each file / directory listed when this path
	 * exists. By default nothing is removed (empty path '')
	 * The path indicated in $p_add_dir will be added at the beginning of
	 * the memorized path of each file / directory listed. However it can
	 * be set to empty ''. The adding of a path is done after the removing
	 * of path.
	 * The path add/remove ability enables the user to prepare an archive
	 * for extraction in a different path than the origin files are.
	 * If a file/dir is already in the archive it will only be added at the
	 * end of the archive. There is no update of the existing archived
	 * file/dir. However while extracting the archive, the last file will
	 * replace the first one. This results in a none optimization of the
	 * archive size.
	 * If a file/dir does not exist the file/dir is ignored. However an
	 * error text is send to PEAR error.
	 * If a file/dir is not readable the file/dir is ignored. However an
	 * error text is send to PEAR error.
	 *
	 * @param array  $p_filelist   An array of filenames and directory
	 *                             names, or a single string with names
	 *                             separated by a single blank space.
	 * @param string $p_add_dir    A string which contains a path to be
	 *                             added to the memorized path of each
	 *                             element in the list.
	 * @param string $p_remove_dir A string which contains a path to be
	 *                             removed from the memorized path of
	 *                             each element in the list, when
	 *                             relevant.
	 *
	 * @return TRUE on success, FALSE on error.
	 * @access public
	 */
	function add_modify($p_filelist, $p_add_dir, $p_remove_dir = '')
	{
		$v_result = TRUE;

		if ( ! $this->_is_archive())
		{
			$v_result = $this->create_modify($p_filelist, $p_add_dir, $p_remove_dir);
		}
		else
		{
			if (is_array($p_filelist))
			{
				$v_list = $p_filelist;
			}
			elseif (is_string($p_filelist))
			{
				$v_list = explode($this->_separator, $p_filelist);
			}
			else
			{
				$this->_error('Invalid file list');
				return FALSE;
			}

			$v_result = $this->_append($v_list, $p_add_dir, $p_remove_dir);
		}

		return $v_result;
	}

	// }}}
	// {{{ add_string()
	/**
	 * This method add a single string as a file at the
	 * end of the existing archive. If the archive does not yet exists it
	 * is created.
	 *
	 * @param string $p_filename A string which contains the full
	 *                           filename path that will be associated
	 *                           with the string.
	 * @param string $p_string   The content of the file added in
	 *                           the archive.
	 *
	 * @return TRUE on success, FALSE on error.
	 * @access public
	 */
	function add_string($p_filename, $p_string)
	{
		$v_result = TRUE;

		if ( ! $this->_is_archive())
		{
			if ( ! $this->_open_write())
			{
				return FALSE;
			}
			$this->_close();
		}

		if ( ! $this->_open_append())
		{
			return FALSE;
		}

		// Need to check the get back to the temporary file ? ....
		$v_result = $this->_add_string($p_filename, $p_string);

		$this->_write_footer();

		$this->_close();

		return $v_result;
	}

	// }}}
	// {{{ extract_modify()
	/**
	 * This method extract all the content of the archive in the directory
	 * indicated by $p_path. When relevant the memorized path of the
	 * files/dir can be modified by removing the $p_remove_path path at the
	 * beginning of the file/dir path.
	 * While extracting a file, if the directory path does not exists it is
	 * created.
	 * While extracting a file, if the file already exists it is replaced
	 * without looking for last modification date.
	 * While extracting a file, if the file already exists and is write
	 * protected, the extraction is aborted.
	 * While extracting a file, if a directory with the same name already
	 * exists, the extraction is aborted.
	 * While extracting a directory, if a file with the same name already
	 * exists, the extraction is aborted.
	 * While extracting a file/directory if the destination directory exist
	 * and is write protected, or does not exist but can not be created,
	 * the extraction is aborted.
	 * If after extraction an extracted file does not show the correct
	 * stored file size, the extraction is aborted.
	 * When the extraction is aborted, a PEAR error text is set and FALSE
	 * is returned. However the result can be a partial extraction that may
	 * need to be manually cleaned.
	 *
	 * @param string  $p_path        The path of the directory where the
	 *                               files/dir need to by extracted.
	 * @param string  $p_remove_path Part of the memorized path that can be
	 *                               removed if present at the beginning of
	 *                               the file/dir path.
	 * @param boolean $p_preserve    Preserve user/group ownership of files
	 *
	 * @return boolean TRUE on success, FALSE on error.
	 * @access public
	 * @see    extract_list()
	 */
	function extract_modify($p_path, $p_remove_path, $p_preserve = FALSE)
	{
		$v_result = TRUE;
		$v_list_detail = array();

		if ($v_result = $this->_open_read())
		{
			$v_result = $this->_extract_list($p_path, $v_list_detail, "complete", 0, $p_remove_path, $p_preserve);
			$this->_close();
		}

		return $v_result;
	}

	// }}}
	// {{{ extract_in_string()
	/**
	 * This method extract from the archive one file identified by $p_filename.
	 * The return value is a string with the file content, or NULL on error.
	 *
	 * @param string $p_filename The path of the file to extract in a string.
	 *
	 * @return a string with the file content or NULL.
	 * @access public
	 */
	function extract_in_string($p_filename)
	{
		if ($this->_open_read())
		{
			$v_result = $this->_extract_in_string($p_filename);
			$this->_close();
		}
		else
		{
			$v_result = NULL;
		}

		return $v_result;
	}

	// }}}
	// {{{ extract_list()
	/**
	 * This method extract from the archive only the files indicated in the
	 * $p_filelist. These files are extracted in the current directory or
	 * in the directory indicated by the optional $p_path parameter.
	 * If indicated the $p_remove_path can be used in the same way as it is
	 * used in extract_modify() method.
	 *
	 * @param array   $p_filelist    An array of filenames and directory names,
	 *                               or a single string with names separated
	 *                               by a single blank space.
	 * @param string  $p_path        The path of the directory where the
	 *                               files/dir need to by extracted.
	 * @param string  $p_remove_path Part of the memorized path that can be
	 *                               removed if present at the beginning of
	 *                               the file/dir path.
	 * @param boolean $p_preserve    Preserve user/group ownership of files
	 *
	 * @return TRUE on success, FALSE on error.
	 * @access public
	 * @see    extract_modify()
	 */
	function extract_list($p_filelist, $p_path = '', $p_remove_path = '', $p_preserve = FALSE)
	{
		$v_result = TRUE;
		$v_list_detail = array();

		if (is_array($p_filelist))
		{
			$v_list = $p_filelist;
		}
		elseif (is_string($p_filelist))
		{
			$v_list = explode($this->_separator, $p_filelist);
		}
		else
		{
			$this->_error('Invalid string list');
			return FALSE;
		}

		if ($v_result = $this->_open_read())
		{
			$v_result = $this->_extract_list($p_path, $v_list_detail, "partial", $v_list, $p_remove_path, $p_preserve);
			$this->_close();
		}

		return $v_result;
	}

	// }}}
	// {{{ set_attribute()
	/**
	 * This method set specific attributes of the archive. It uses a variable
	 * list of parameters, in the format attribute code + attribute values :
	 * $arch->set_attribute(ARCHIVE_TAR_ATT_SEPARATOR, ',');
	 *
	 * @ignore
	 * @param mixed $argv variable list of attributes and values
	 *
	 * @return TRUE on success, FALSE on error.
	 * @access public
	 */
	function set_attribute()
	{
		$v_result = TRUE;

		// ----- Get the number of variable list of arguments
		if (($v_size = func_num_args()) == 0)
		{
			return TRUE;
		}

		// ----- Get the arguments
		$v_att_list = & func_get_args();

		// ----- Read the attributes
		$i = 0;
		while ($i < $v_size)
		{

			// ----- Look for next option
			switch ($v_att_list[$i])
			{
				// ----- Look for options that request a string value
				case ARCHIVE_TAR_ATT_SEPARATOR :
					// ----- Check the number of parameters
					if (($i + 1) >= $v_size)
					{
						$this->_error('Invalid number of parameters for '
								.'attribute ARCHIVE_TAR_ATT_SEPARATOR');
						return FALSE;
					}

					// ----- Get the value
					$this->_separator = $v_att_list[$i + 1];
					$i++;
					break;

				default :
					$this->_error('Unknow attribute code '.$v_att_list[$i].'');
					return FALSE;
			}

			// ----- Next attribute
			$i++;
		}

		return $v_result;
	}

	// }}}
	// {{{ set_ignore_regexp()
	/**
	 * This method sets the regular expression for ignoring files and directories
	 * at import, for example:
	 * $arch->set_ignore_regexp("#CVS|\.svn#");
	 *
	 * @param string $regexp regular expression defining which files or directories to ignore
	 *
	 * @access public
	 */
	function set_ignore_regexp($regexp)
	{
		$this->_ignore_regexp = $regexp;
	}

	// }}}
	// {{{ set_ignore_list()
	/**
	 * This method sets the regular expression for ignoring all files and directories
	 * matching the filenames in the array list at import, for example:
	 * $arch->set_ignore_list(array('CVS', '.svn', 'bin/tool'));
	 *
	 * @param array $list a list of file or directory names to ignore
	 *
	 * @access public
	 */
	function set_ignore_list($list)
	{
		$regexp = str_replace(array('#', '.', '^', '$'), array('\#', '\.', '\^', '\$'), $list);
		$regexp = '#/'.join('$|/', $list).'#';
		$this->set_ignore_regexp($regexp);
	}

	// }}}
	// {{{ _error()
	/**
	 * @ignore
	 * @param type $p_message
	 * @throws Exception
	 */
	function _error($p_message)
	{
		throw new Exception($p_message);
		// $this->error_object = &$this->raiseError($p_message);
	}

	// }}}
	// {{{ _warning()
	/**
	 * @ignore
	 * @param type $p_message
	 * @throws Exception
	 */
	function _warning($p_message)
	{
		throw new Exception($p_message);
		// $this->error_object = &$this->raiseError($p_message);
	}

	// }}}
	// {{{ _is_archive()
	/**
	 * @ignore
	 * @param type $p_filename
	 * @return type
	 */
	function _is_archive($p_filename = NULL)
	{
		if ($p_filename == NULL)
		{
			$p_filename = $this->_tarname;
		}
		clearstatcache();
		return @is_file($p_filename) && ! @is_link($p_filename);
	}

	// }}}
	// {{{ _open_write()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _open_write()
	{
		if ($this->_compress_type == 'gz' && function_exists('gzopen'))
		{
			$this->_file = @gzopen($this->_tarname, "wb9");
		}
		elseif ($this->_compress_type == 'bz2' && function_exists('bzopen'))
		{
			$this->_file = @bzopen($this->_tarname, "w");
		}
		elseif ($this->_compress_type == 'none')
		{
			$this->_file = @fopen($this->_tarname, "wb");
		}
		else
		{
			$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
			return FALSE;
		}

		if ($this->_file == 0)
		{
			$this->_error('Unable to open in write mode \''.$this->_tarname.'\'');
			return FALSE;
		}

		return TRUE;
	}

	// }}}
	// {{{ _open_read()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _open_read()
	{
		if (strtolower(substr($this->_tarname, 0, 7)) == 'http://')
		{

			// ----- Look if a local copy need to be done
			if ($this->_temp_tarname == '')
			{
				$this->_temp_tarname = uniqid('tar').'.tmp';
				if ( ! $v_file_from = @fopen($this->_tarname, 'rb'))
				{
					$this->_error('Unable to open in read mode \''
							.$this->_tarname.'\'');
					$this->_temp_tarname = '';
					return FALSE;
				}
				if ( ! $v_file_to = @fopen($this->_temp_tarname, 'wb'))
				{
					$this->_error('Unable to open in write mode \''
							.$this->_temp_tarname.'\'');
					$this->_temp_tarname = '';
					return FALSE;
				}
				while ($v_data = @fread($v_file_from, 1024))
				{
					@fwrite($v_file_to, $v_data);
				}
				@fclose($v_file_from);
				@fclose($v_file_to);
			}

			// ----- File to open if the local copy
			$v_filename = $this->_temp_tarname;
		}
		else
		{
			// ----- File to open if the normal Tar file
			$v_filename = $this->_tarname;
		}

		if ($this->_compress_type == 'gz' && function_exists('gzopen'))
		{
			$this->_file = @gzopen($v_filename, "rb");
		}
		elseif ($this->_compress_type == 'bz2' && function_exists('bzopen'))
		{
			$this->_file = @bzopen($v_filename, "r");
		}
		elseif ($this->_compress_type == 'none')
		{
			$this->_file = @fopen($v_filename, "rb");
		}
		else
		{
			$this->_error('Unknown or missing compression type ('
					.$this->_compress_type.')');
			return FALSE;
		}

		if ($this->_file == 0)
		{
			$this->_error('Unable to open in read mode \''.$v_filename.'\'');
			return FALSE;
		}

		return TRUE;
	}

	// }}}
	// {{{ _open_read_write()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _open_read_write()
	{
		if ($this->_compress_type == 'gz')
		{
			$this->_file = @gzopen($this->_tarname, "r+b");
		}
		elseif ($this->_compress_type == 'bz2')
		{
			$this->_error('Unable to open bz2 in read/write mode \''.$this->_tarname.'\' (limitation of bz2 extension)');
			return FALSE;
		}
		elseif ($this->_compress_type == 'none')
		{
			$this->_file = @fopen($this->_tarname, "r+b");
		}
		else
		{
			$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
			return FALSE;
		}

		if ($this->_file == 0)
		{
			$this->_error('Unable to open in read/write mode \''.$this->_tarname.'\'');
			return FALSE;
		}

		return TRUE;
	}

	// }}}
	// {{{ _close()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _close()
	{
		// if (isset($this->_file)) {
		if (is_resource($this->_file))
		{
			if ($this->_compress_type == 'gz')
			{
				@gzclose($this->_file);
			}
			elseif ($this->_compress_type == 'bz2')
			{
				@bzclose($this->_file);
			}
			elseif ($this->_compress_type == 'none')
			{
				@fclose($this->_file);
			}
			else
			{
				$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
			}

			$this->_file = 0;
		}

		// ----- Look if a local copy need to be erase
		// Note that it might be interesting to keep the url for a time : ToDo
		if ($this->_temp_tarname != '')
		{
			@unlink($this->_temp_tarname);
			$this->_temp_tarname = '';
		}

		return TRUE;
	}

	// }}}
	// {{{ _clean_file()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _clean_file()
	{
		$this->_close();

		// ----- Look for a local copy
		if ($this->_temp_tarname != '')
		{
			// ----- Remove the local copy but not the remote tarname
			@unlink($this->_temp_tarname);
			$this->_temp_tarname = '';
		}
		else
		{
			// ----- Remove the local tarname file
			@unlink($this->_tarname);
		}
		$this->_tarname = '';

		return TRUE;
	}

	// }}}
	// {{{ _write_block()
	/**
	 * @ignore
	 * @param type $p_binary_data
	 * @param type $p_len
	 * @return boolean
	 */
	function _write_block($p_binary_data, $p_len = NULL)
	{
		if (is_resource($this->_file))
		{
			if ($p_len === NULL)
			{
				if ($this->_compress_type == 'gz')
				{
					@gzputs($this->_file, $p_binary_data);
				}
				elseif ($this->_compress_type == 'bz2')
				{
					@bzwrite($this->_file, $p_binary_data);
				}
				elseif ($this->_compress_type == 'none')
				{
					@fputs($this->_file, $p_binary_data);
				}
				else
				{
					$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
				}
			} else
			{
				if ($this->_compress_type == 'gz')
				{
					@gzputs($this->_file, $p_binary_data, $p_len);
				}
				elseif ($this->_compress_type == 'bz2')
				{
					@bzwrite($this->_file, $p_binary_data, $p_len);
				}
				elseif ($this->_compress_type == 'none')
				{
					@fputs($this->_file, $p_binary_data, $p_len);
				}
				else
				{
					$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
				}
			}
		}
		return TRUE;
	}

	// }}}
	// {{{ _read_block()
	/**
	 * @ignore
	 * @return type
	 */
	function _read_block()
	{
		$v_block = NULL;
		if (is_resource($this->_file))
		{
			if ($this->_compress_type == 'gz')
			{
				$v_block = @gzread($this->_file, 512);
			}
			elseif ($this->_compress_type == 'bz2')
			{
				$v_block = @bzread($this->_file, 512);
			}
			elseif ($this->_compress_type == 'none')
			{
				$v_block = @fread($this->_file, 512);
			}
			else
			{
				$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
			}
		}
		return $v_block;
	}

	// }}}
	// {{{ _jump_block()
	/**
	 * @ignore
	 * @param int $p_len
	 * @return boolean
	 */
	function _jump_block($p_len = NULL)
	{
		if (is_resource($this->_file))
		{
			if ($p_len === NULL)
			{
				$p_len = 1;
			}

			if ($this->_compress_type == 'gz')
			{
				@gzseek($this->_file, gztell($this->_file) + ($p_len * 512));
			}
			elseif ($this->_compress_type == 'bz2')
			{
				// ----- Replace missing bztell() and bzseek()
				for ($i = 0; $i < $p_len; $i++)
				{
					$this->_read_block();
				}
			}
			elseif ($this->_compress_type == 'none')
			{
				@fseek($this->_file, $p_len * 512, SEEK_CUR);
			}
			else
			{
				$this->_error('Unknown or missing compression type ('.$this->_compress_type.')');
			}
		}
		return TRUE;
	}

	// }}}
	// {{{ _write_footer()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _write_footer()
	{
		if (is_resource($this->_file))
		{
			// ----- Write the last 0 filled block for end of archive
			$v_binary_data = pack('a1024', '');
			$this->_write_block($v_binary_data);
		}
		return TRUE;
	}

	// }}}
	// {{{ _add_list()
	/**
	 * @ignore
	 * @param type $p_list
	 * @param type $p_add_dir
	 * @param type $p_remove_dir
	 * @return boolean
	 */
	function _add_list($p_list, $p_add_dir, $p_remove_dir)
	{
		$v_result = TRUE;
		$v_header = array();

		// ----- Remove potential windows directory separator
		$p_add_dir = $this->_translate_win_path($p_add_dir);
		$p_remove_dir = $this->_translate_win_path($p_remove_dir, FALSE);

		if ( ! $this->_file)
		{
			$this->_error('Invalid file descriptor');
			return FALSE;
		}

		if (sizeof($p_list) == 0)
		{
			return TRUE;
		}

		foreach ($p_list as $v_filename)
		{
			if ( ! $v_result)
			{
				break;
			}

			// ----- Skip the current tar name
			if ($v_filename == $this->_tarname)
			{
				continue;
			}

			if ($v_filename == '')
			{
				continue;
			}

			// ----- ignore files and directories matching the ignore regular expression
			if ($this->_ignore_regexp && preg_match($this->_ignore_regexp, '/'.$v_filename))
			{
				$this->_warning("File '$v_filename' ignored");
				continue;
			}

			if ( ! file_exists($v_filename) && ! is_link($v_filename))
			{
				$this->_warning("File '$v_filename' does not exist");
				continue;
			}

			// ----- Add the file or directory header
			if ( ! $this->_add_file($v_filename, $v_header, $p_add_dir, $p_remove_dir))
			{
				return FALSE;
			}

			if (@is_dir($v_filename) && ! @is_link($v_filename))
			{
				if ( ! ($p_hdir = opendir($v_filename)))
				{
					$this->_warning("Directory '$v_filename' can not be read");
					continue;
				}
				while (FALSE !== ($p_hitem = readdir($p_hdir)))
				{
					if (($p_hitem != '.') && ($p_hitem != '..'))
					{
						if ($v_filename != ".")
						{
							$p_temp_list[0] = $v_filename.'/'.$p_hitem;
						}
						else
						{
							$p_temp_list[0] = $p_hitem;
						}

						$v_result = $this->_add_list($p_temp_list, $p_add_dir, $p_remove_dir);
					}
				}

				unset($p_temp_list);
				unset($p_hdir);
				unset($p_hitem);
			}
		}

		return $v_result;
	}

	// }}}
	// {{{ _add_file()
	/**
	 * @ignore
	 * @param type $p_filename
	 * @param type $p_header
	 * @param type $p_add_dir
	 * @param string $p_remove_dir
	 * @return boolean
	 */
	function _add_file($p_filename, & $p_header, $p_add_dir, $p_remove_dir)
	{
		if ( ! $this->_file)
		{
			$this->_error('Invalid file descriptor');
			return FALSE;
		}

		if ($p_filename == '')
		{
			$this->_error('Invalid file name');
			return FALSE;
		}

		// ----- Calculate the stored filename
		$p_filename = $this->_translate_win_path($p_filename, FALSE);

		$v_stored_filename = $p_filename;
		if (strcmp($p_filename, $p_remove_dir) == 0)
		{
			return TRUE;
		}
		if ($p_remove_dir != '')
		{
			if (substr($p_remove_dir, -1) != '/')
			{
				$p_remove_dir .= '/';
			}

			if (substr($p_filename, 0, strlen($p_remove_dir)) == $p_remove_dir)
			{
				$v_stored_filename = substr($p_filename, strlen($p_remove_dir));
			}
		}
		$v_stored_filename = $this->_translate_win_path($v_stored_filename);
		if ($p_add_dir != '')
		{
			if (substr($p_add_dir, -1) == '/')
			{
				$v_stored_filename = $p_add_dir.$v_stored_filename;
			}
			else
			{
				$v_stored_filename = $p_add_dir.'/'.$v_stored_filename;
			}
		}

		$v_stored_filename = $this->_path_reduction($v_stored_filename);

		if ($this->_is_archive($p_filename))
		{
			if (($v_file = @fopen($p_filename, "rb")) == 0)
			{
				$this->_warning("Unable to open file '".$p_filename."' in binary read mode");
				return TRUE;
			}

			if ( ! $this->_write_header($p_filename, $v_stored_filename))
			{
				return FALSE;
			}

			while (($v_buffer = fread($v_file, 512)) != '')
			{
				$v_binary_data = pack("a512", "$v_buffer");
				$this->_write_block($v_binary_data);
			}

			fclose($v_file);
		}
		else
		{
			// ----- Only header for dir
			if ( ! $this->_write_header($p_filename, $v_stored_filename))
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// }}}
	// {{{ _add_string()
	/**
	 * @ignore
	 * @param type $p_filename
	 * @param type $p_string
	 * @return boolean
	 */
	function _add_string($p_filename, $p_string)
	{
		if ( ! $this->_file)
		{
			$this->_error('Invalid file descriptor');
			return FALSE;
		}

		if ($p_filename == '')
		{
			$this->_error('Invalid file name');
			return FALSE;
		}

		// ----- Calculate the stored filename
		$p_filename = $this->_translate_win_path($p_filename, FALSE);

		if ( ! $this->_write_header_block($p_filename, strlen($p_string), time(), 384, "", 0, 0))
		{
			return FALSE;
		}

		$i = 0;
		while (($v_buffer = substr($p_string, (($i++) * 512), 512)) != '')
		{
			$v_binary_data = pack("a512", $v_buffer);
			$this->_write_block($v_binary_data);
		}

		return TRUE;
	}

	// }}}
	// {{{ _write_header()
	/**
	 * @ignore
	 * @param type $p_filename
	 * @param type $p_stored_filename
	 * @return boolean
	 */
	function _write_header($p_filename, $p_stored_filename)
	{
		if ($p_stored_filename == '')
		{
			$p_stored_filename = $p_filename;
		}
		$v_reduce_filename = $this->_path_reduction($p_stored_filename);

		if (strlen($v_reduce_filename) > 99)
		{
			if ( ! $this->_write_long_header($v_reduce_filename))
			{
				return FALSE;
			}
		}

		$v_info = lstat($p_filename);
		$v_uid = sprintf("%07s", DecOct($v_info[4]));
		$v_gid = sprintf("%07s", DecOct($v_info[5]));
		$v_perms = sprintf("%07s", DecOct($v_info['mode'] & 000777));

		$v_mtime = sprintf("%011s", DecOct($v_info['mtime']));

		$v_linkname = '';

		if (@is_link($p_filename))
		{
			$v_typeflag = '2';
			$v_linkname = readlink($p_filename);
			$v_size = sprintf("%011s", DecOct(0));
		}
		elseif (@is_dir($p_filename))
		{
			$v_typeflag = "5";
			$v_size = sprintf("%011s", DecOct(0));
		}
		else
		{
			$v_typeflag = '0';
			clearstatcache();
			$v_size = sprintf("%011s", DecOct($v_info['size']));
		}

		$v_magic = 'ustar ';

		$v_version = ' ';

		if (function_exists('posix_getpwuid'))
		{
			$userinfo = posix_getpwuid($v_info[4]);
			$groupinfo = posix_getgrgid($v_info[5]);

			$v_uname = $userinfo['name'];
			$v_gname = $groupinfo['name'];
		}
		else
		{
			$v_uname = '';
			$v_gname = '';
		}

		$v_devmajor = '';

		$v_devminor = '';

		$v_prefix = '';

		$v_binary_data_first = pack("a100a8a8a8a12a12", $v_reduce_filename, $v_perms, $v_uid, $v_gid, $v_size, $v_mtime);
		$v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12", $v_typeflag, $v_linkname, $v_magic, $v_version, $v_uname, $v_gname, $v_devmajor, $v_devminor, $v_prefix, '');

		// ----- Calculate the checksum
		$v_checksum = 0;
		// ..... First part of the header
		for ($i = 0; $i < 148; $i++)
		{
			$v_checksum += ord(substr($v_binary_data_first, $i, 1));
		}
		// ..... Ignore the checksum value and replace it by ' ' (space)
		for ($i = 148; $i < 156; $i++)
		{
			$v_checksum += ord(' ');
		}
		// ..... Last part of the header
		for ($i = 156, $j = 0; $i < 512; $i++, $j++)
		{
			$v_checksum += ord(substr($v_binary_data_last, $j, 1));
		}

		// ----- Write the first 148 bytes of the header in the archive
		$this->_write_block($v_binary_data_first, 148);

		// ----- Write the calculated checksum
		$v_checksum = sprintf("%06s ", DecOct($v_checksum));
		$v_binary_data = pack("a8", $v_checksum);
		$this->_write_block($v_binary_data, 8);

		// ----- Write the last 356 bytes of the header in the archive
		$this->_write_block($v_binary_data_last, 356);

		return TRUE;
	}

	// }}}
	// {{{ _write_header_block()
	/**
	 * @ignore
	 * @param type $p_filename
	 * @param type $p_size
	 * @param type $p_mtime
	 * @param type $p_perms
	 * @param type $p_type
	 * @param type $p_uid
	 * @param type $p_gid
	 * @return boolean
	 */
	function _write_header_block($p_filename, $p_size, $p_mtime = 0, $p_perms = 0, $p_type = '', $p_uid = 0, $p_gid = 0)
	{
		$p_filename = $this->_path_reduction($p_filename);

		if (strlen($p_filename) > 99)
		{
			if ( ! $this->_write_long_header($p_filename))
			{
				return FALSE;
			}
		}

		if ($p_type == "5")
		{
			$v_size = sprintf("%011s", DecOct(0));
		}
		else
		{
			$v_size = sprintf("%011s", DecOct($p_size));
		}

		$v_uid = sprintf("%07s", DecOct($p_uid));
		$v_gid = sprintf("%07s", DecOct($p_gid));
		$v_perms = sprintf("%07s", DecOct($p_perms & 000777));

		$v_mtime = sprintf("%11s", DecOct($p_mtime));

		$v_linkname = '';

		$v_magic = 'ustar ';

		$v_version = ' ';

		if (function_exists('posix_getpwuid'))
		{
			$userinfo = posix_getpwuid($p_uid);
			$groupinfo = posix_getgrgid($p_gid);

			$v_uname = $userinfo['name'];
			$v_gname = $groupinfo['name'];
		}
		else
		{
			$v_uname = '';
			$v_gname = '';
		}

		$v_devmajor = '';

		$v_devminor = '';

		$v_prefix = '';

		$v_binary_data_first = pack("a100a8a8a8a12A12", $p_filename, $v_perms, $v_uid, $v_gid, $v_size, $v_mtime);
		$v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12", $p_type, $v_linkname, $v_magic, $v_version, $v_uname, $v_gname, $v_devmajor, $v_devminor, $v_prefix, '');

		// ----- Calculate the checksum
		$v_checksum = 0;
		// ..... First part of the header
		for ($i = 0; $i < 148; $i++)
		{
			$v_checksum += ord(substr($v_binary_data_first, $i, 1));
		}
		// ..... Ignore the checksum value and replace it by ' ' (space)
		for ($i = 148; $i < 156; $i++)
		{
			$v_checksum += ord(' ');
		}
		// ..... Last part of the header
		for ($i = 156, $j = 0; $i < 512; $i++, $j++)
		{
			$v_checksum += ord(substr($v_binary_data_last, $j, 1));
		}

		// ----- Write the first 148 bytes of the header in the archive
		$this->_write_block($v_binary_data_first, 148);

		// ----- Write the calculated checksum
		$v_checksum = sprintf("%06s ", DecOct($v_checksum));
		$v_binary_data = pack("a8", $v_checksum);
		$this->_write_block($v_binary_data, 8);

		// ----- Write the last 356 bytes of the header in the archive
		$this->_write_block($v_binary_data_last, 356);

		return TRUE;
	}

	// }}}
	// {{{ _write_long_header()
	/**
	 * @ignore
	 * @param type $p_filename
	 * @return boolean
	 */
	function _write_long_header($p_filename)
	{
		$v_size = sprintf("%11s ", DecOct(strlen($p_filename)));

		$v_typeflag = 'L';

		$v_linkname = '';

		$v_magic = '';

		$v_version = '';

		$v_uname = '';

		$v_gname = '';

		$v_devmajor = '';

		$v_devminor = '';

		$v_prefix = '';

		$v_binary_data_first = pack("a100a8a8a8a12a12", '././@LongLink', 0, 0, 0, $v_size, 0);
		$v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12", $v_typeflag, $v_linkname, $v_magic, $v_version, $v_uname, $v_gname, $v_devmajor, $v_devminor, $v_prefix, '');

		// ----- Calculate the checksum
		$v_checksum = 0;
		// ..... First part of the header
		for ($i = 0; $i < 148; $i++)
		{
			$v_checksum += ord(substr($v_binary_data_first, $i, 1));
		}
		// ..... Ignore the checksum value and replace it by ' ' (space)
		for ($i = 148; $i < 156; $i++)
		{
			$v_checksum += ord(' ');
		}
		// ..... Last part of the header
		for ($i = 156, $j = 0; $i < 512; $i++, $j++)
		{
			$v_checksum += ord(substr($v_binary_data_last, $j, 1));
		}

		// ----- Write the first 148 bytes of the header in the archive
		$this->_write_block($v_binary_data_first, 148);

		// ----- Write the calculated checksum
		$v_checksum = sprintf("%06s ", DecOct($v_checksum));
		$v_binary_data = pack("a8", $v_checksum);
		$this->_write_block($v_binary_data, 8);

		// ----- Write the last 356 bytes of the header in the archive
		$this->_write_block($v_binary_data_last, 356);

		// ----- Write the filename as content of the block
		$i = 0;
		while (($v_buffer = substr($p_filename, (($i++) * 512), 512)) != '')
		{
			$v_binary_data = pack("a512", "$v_buffer");
			$this->_write_block($v_binary_data);
		}

		return TRUE;
	}

	// }}}
	// {{{ _read_header()
	/**
	 * @ignore
	 * @param type $v_binary_data
	 * @param type $v_header
	 * @return boolean
	 */
	function _read_header($v_binary_data, & $v_header)
	{
		if (strlen($v_binary_data) == 0)
		{
			$v_header['filename'] = '';
			return TRUE;
		}

		if (strlen($v_binary_data) != 512)
		{
			$v_header['filename'] = '';
			$this->_error('Invalid block size : '.strlen($v_binary_data));
			return FALSE;
		}

		if ( ! is_array($v_header))
		{
			$v_header = array();
		}
		// ----- Calculate the checksum
		$v_checksum = 0;
		// ..... First part of the header
		for ($i = 0; $i < 148; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));
		// ..... Ignore the checksum value and replace it by ' ' (space)
		for ($i = 148; $i < 156; $i++)
			$v_checksum += ord(' ');
		// ..... Last part of the header
		for ($i = 156; $i < 512; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));

		$v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/"."a8checksum/a1typeflag/a100link/a6magic/a2version/"."a32uname/a32gname/a8devmajor/a8devminor/a131prefix", $v_binary_data);

		if (strlen($v_data["prefix"]) > 0)
		{
			$v_data["filename"] = "$v_data[prefix]/$v_data[filename]";
		}

		// ----- Extract the checksum
		$v_header['checksum'] = OctDec(trim($v_data['checksum']));
		if ($v_header['checksum'] != $v_checksum)
		{
			$v_header['filename'] = '';

			// ----- Look for last block (empty block)
			if (($v_checksum == 256) && ($v_header['checksum'] == 0))
				return TRUE;

			$this->_error('Invalid checksum for file "'.$v_data['filename']
					.'" : '.$v_checksum.' calculated, '
					.$v_header['checksum'].' expected');
			return FALSE;
		}

		// ----- Extract the properties
		$v_header['filename'] = $v_data['filename'];
		if ($this->_malicious_filename($v_header['filename']))
		{
			$this->_error('Malicious .tar detected, file "'.$v_header['filename'].'" will not install in desired directory tree');
			return FALSE;
		}
		$v_header['mode'] = OctDec(trim($v_data['mode']));
		$v_header['uid'] = OctDec(trim($v_data['uid']));
		$v_header['gid'] = OctDec(trim($v_data['gid']));
		$v_header['size'] = OctDec(trim($v_data['size']));
		$v_header['mtime'] = OctDec(trim($v_data['mtime']));
		if (($v_header['typeflag'] = $v_data['typeflag']) == "5")
		{
			$v_header['size'] = 0;
		}
		$v_header['link'] = trim($v_data['link']);
		/* ----- All these fields are removed form the header because
		  they do not carry interesting info
		  $v_header[magic] = trim($v_data[magic]);
		  $v_header[version] = trim($v_data[version]);
		  $v_header[uname] = trim($v_data[uname]);
		  $v_header[gname] = trim($v_data[gname]);
		  $v_header[devmajor] = trim($v_data[devmajor]);
		  $v_header[devminor] = trim($v_data[devminor]);
		 */

		return TRUE;
	}

	// }}}
	// {{{ _malicious_filename()
	/**
	 * Detect and report a malicious file name
	 *
	 * @param string $file
	 *
	 * @return bool
	 * @access private
	 */
	function _malicious_filename($file)
	{
		if (strpos($file, '/../') !== FALSE)
		{
			return TRUE;
		}
		if (strpos($file, '../') === 0)
		{
			return TRUE;
		}
		return FALSE;
	}

	// }}}
	// {{{ _read_long_header()
	/**
	 * @ignore
	 * @param array $v_header
	 * @return boolean
	 */
	function _read_long_header( & $v_header)
	{
		$v_filename = '';
		$n = floor($v_header['size'] / 512);
		for ($i = 0; $i < $n; $i++)
		{
			$v_content = $this->_read_block();
			$v_filename .= $v_content;
		}
		if (($v_header['size'] % 512) != 0)
		{
			$v_content = $this->_read_block();
			$v_filename .= trim($v_content);
		}

		// ----- Read the next header
		$v_binary_data = $this->_read_block();

		if ( ! $this->_read_header($v_binary_data, $v_header))
		{
			return FALSE;
		}

		$v_filename = trim($v_filename);
		$v_header['filename'] = $v_filename;
		if ($this->_malicious_filename($v_filename))
		{
			$this->_error('Malicious .tar detected, file "'.$v_filename.'" will not install in desired directory tree');
			return FALSE;
		}

		return TRUE;
	}

	// }}}
	// {{{ _extract_in_string()
	/**
	 * This method extract from the archive one file identified by $p_filename.
	 * The return value is a string with the file content, or NULL on error.
	 *
	 * @param string $p_filename The path of the file to extract in a string.
	 *
	 * @return a string with the file content or NULL.
	 * @access private
	 */
	function _extract_in_string($p_filename)
	{
		$v_result_str = "";

		While (strlen($v_binary_data = $this->_read_block()) != 0)
		{
			if ( ! $this->_read_header($v_binary_data, $v_header))
			{
				return NULL;
			}

			if ($v_header['filename'] == '')
			{
				continue;
			}

			// ----- Look for long filename
			if ($v_header['typeflag'] == 'L')
			{
				if ( ! $this->_read_long_header($v_header))
				{
					return NULL;
				}
			}

			if ($v_header['filename'] == $p_filename)
			{
				if ($v_header['typeflag'] == "5")
				{
					$this->_error('Unable to extract in string a directory '.'entry {'.$v_header['filename'].'}');
					return NULL;
				}
				else
				{
					$n = floor($v_header['size'] / 512);
					for ($i = 0; $i < $n; $i++)
					{
						$v_result_str .= $this->_read_block();
					}
					if (($v_header['size'] % 512) != 0)
					{
						$v_content = $this->_read_block();
						$v_result_str .= substr($v_content, 0, ($v_header['size'] % 512));
					}
					return $v_result_str;
				}
			}
			else
			{
				$this->_jump_block(ceil(($v_header['size'] / 512)));
			}
		}

		return NULL;
	}

	// }}}
	// {{{ _extract_list()
	/**
	 * @ignore
	 * @param type $p_path
	 * @param type $p_list_detail
	 * @param type $p_mode
	 * @param type $p_file_list
	 * @param type $p_remove_path
	 * @param type $p_preserve
	 * @return boolean
	 */
	function _extract_list($p_path, & $p_list_detail, $p_mode, $p_file_list, $p_remove_path, $p_preserve = FALSE)
	{
		$v_result = TRUE;
		$v_nb = 0;
		$v_extract_all = TRUE;
		$v_listing = FALSE;

		$p_path = $this->_translate_win_path($p_path, FALSE);
		if ($p_path == '' || (substr($p_path, 0, 1) != '/' && substr($p_path, 0, 3) != "../" && ! strpos($p_path, ':')))
		{
			$p_path = "./".$p_path;
		}
		$p_remove_path = $this->_translate_win_path($p_remove_path);

		// ----- Look for path to remove format (should end by /)
		if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/'))
		{
			$p_remove_path .= '/';
		}
		$p_remove_path_size = strlen($p_remove_path);

		switch ($p_mode)
		{
			case "complete" :
				$v_extract_all = TRUE;
				$v_listing = FALSE;
				break;
			case "partial" :
				$v_extract_all = FALSE;
				$v_listing = FALSE;
				break;
			case "list" :
				$v_extract_all = FALSE;
				$v_listing = TRUE;
				break;
			default :
				$this->_error('Invalid extract mode ('.$p_mode.')');
				return FALSE;
		}

		clearstatcache();

		while (strlen($v_binary_data = $this->_read_block()) != 0)
		{
			$v_extract_file = FALSE;
			$v_extraction_stopped = 0;

			if ( ! $this->_read_header($v_binary_data, $v_header))
			{
				return FALSE;
			}

			if ($v_header['filename'] == '')
			{
				continue;
			}

			// ----- Look for long filename
			if ($v_header['typeflag'] == 'L')
			{
				if ( ! $this->_read_long_header($v_header))
				{
					return FALSE;
				}
			}

			if (( ! $v_extract_all) && (is_array($p_file_list)))
			{
				// ----- By default no unzip if the file is not found
				$v_extract_file = FALSE;

				for ($i = 0; $i < sizeof($p_file_list); $i++)
				{
					// ----- Look if it is a directory
					if (substr($p_file_list[$i], -1) == '/')
					{
						// ----- Look if the directory is in the filename path
						if ((strlen($v_header['filename']) > strlen($p_file_list[$i])) && (substr($v_header['filename'], 0, strlen($p_file_list[$i])) == $p_file_list[$i]))
						{
							$v_extract_file = TRUE;
							break;
						}
					}

					// ----- It is a file, so compare the file names
					elseif ($p_file_list[$i] == $v_header['filename'])
					{
						$v_extract_file = TRUE;
						break;
					}
				}
			}
			else
			{
				$v_extract_file = TRUE;
			}

			// ----- Look if this file need to be extracted
			if (($v_extract_file) && ( ! $v_listing))
			{
				if (($p_remove_path != '') && (substr($v_header['filename'], 0, $p_remove_path_size) == $p_remove_path))
				{
					$v_header['filename'] = substr($v_header['filename'], $p_remove_path_size);
				}
				if (($p_path != './') && ($p_path != '/'))
				{
					while (substr($p_path, -1) == '/')
						$p_path = substr($p_path, 0, strlen($p_path) - 1);

					if (substr($v_header['filename'], 0, 1) == '/')
					{
						$v_header['filename'] = $p_path.$v_header['filename'];
					}
					else
					{
						$v_header['filename'] = $p_path.'/'.$v_header['filename'];
					}
				}
				if (file_exists($v_header['filename']))
				{
					if ((@is_dir($v_header['filename'])) && ($v_header['typeflag'] == ''))
					{
						$this->_error('File '.$v_header['filename'].' already exists as a directory');
						return FALSE;
					}
					if (($this->_is_archive($v_header['filename']))
							&& ($v_header['typeflag'] == "5"))
					{
						$this->_error('Directory '.$v_header['filename'].' already exists as a file');
						return FALSE;
					}
					if ( ! is_writeable($v_header['filename']))
					{
						$this->_error('File '.$v_header['filename'].' already exists and is write protected');
						return FALSE;
					}
					if (filemtime($v_header['filename']) > $v_header['mtime'])
					{
						// To be completed : An error or silent no replace ?
					}
				}

				// ----- Check the directory availability and create it if necessary
				elseif (($v_result = $this->_dir_check((($v_header['typeflag'] == "5") ? ($v_header['filename']) : (dirname($v_header['filename']))))) != 1)
				{
					$this->_error('Unable to create path for '.$v_header['filename']);
					return FALSE;
				}

				if ($v_extract_file)
				{
					if ($v_header['typeflag'] == "5")
					{
						if ( ! @file_exists($v_header['filename']))
						{
							if ( ! @mkdir($v_header['filename'], 0777))
							{
								$this->_error('Unable to create directory {'.$v_header['filename'].'}');
								return FALSE;
							}
						}
					}
					elseif ($v_header['typeflag'] == "2")
					{
						if (@file_exists($v_header['filename']))
						{
							@unlink($v_header['filename']);
						}
						if ( ! @symlink($v_header['link'], $v_header['filename']))
						{
							$this->_error('Unable to extract symbolic link {'.$v_header['filename'].'}');
							return FALSE;
						}
					}
					else
					{
						if (($v_dest_file = @fopen($v_header['filename'], "wb")) == 0)
						{
							$this->_error('Error while opening {'.$v_header['filename'].'} in write binary mode');
							return FALSE;
						}
						else
						{
							$n = floor($v_header['size'] / 512);
							for ($i = 0; $i < $n; $i++)
							{
								$v_content = $this->_read_block();
								fwrite($v_dest_file, $v_content, 512);
							}
							if (($v_header['size'] % 512) != 0)
							{
								$v_content = $this->_read_block();
								fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
							}

							@fclose($v_dest_file);

							if ($p_preserve)
							{
								@chown($v_header['filename'], $v_header['uid']);
								@chgrp($v_header['filename'], $v_header['gid']);
							}

							// ----- Change the file mode, mtime
							@touch($v_header['filename'], $v_header['mtime']);
							if ($v_header['mode'] & 0111)
							{
								// make file executable, obey umask
								$mode = fileperms($v_header['filename']) | (~umask() & 0111);
								@chmod($v_header['filename'], $mode);
							}
						}

						// ----- Check the file size
						clearstatcache();
						if ( ! is_file($v_header['filename']))
						{
							$this->_error('Extracted file '.$v_header['filename'].'does not exist. Archive may be corrupted.');
							return FALSE;
						}

						$filesize = filesize($v_header['filename']);
						if ($filesize != $v_header['size'])
						{
							$this->_error('Extracted file '.$v_header['filename']
									.' does not have the correct file size \''
									.$filesize
									.'\' ('.$v_header['size']
									.' expected). Archive may be corrupted.');
							return FALSE;
						}
					}
				}
				else
				{
					$this->_jump_block(ceil(($v_header['size'] / 512)));
				}
			}
			else
			{
				$this->_jump_block(ceil(($v_header['size'] / 512)));
			}

			/* TBC : Seems to be unused ...
			  if ($this->_compress)
			  $v_end_of_file = @gzeof($this->_file);
			  else
			  $v_end_of_file = @feof($this->_file);
			 */

			if ($v_listing || $v_extract_file || $v_extraction_stopped)
			{
				// ----- Log extracted files
				if (($v_file_dir = dirname($v_header['filename'])) == $v_header['filename'])
				{
					$v_file_dir = '';
				}
				if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == ''))
				{
					$v_file_dir = '/';
				}

				$p_list_detail[$v_nb++] = $v_header;
				if (is_array($p_file_list) && (count($p_list_detail) == count($p_file_list)))
				{
					return TRUE;
				}
			}
		}

		return TRUE;
	}

	// }}}
	// {{{ _open_append()
	/**
	 * @ignore
	 * @return boolean
	 */
	function _open_append()
	{
		if (filesize($this->_tarname) == 0)
			return $this->_open_write();

		if ($this->_compress)
		{
			$this->_close();

			if ( ! @ rename($this->_tarname, $this->_tarname.".tmp"))
			{
				$this->_error('Error while renaming \''.$this->_tarname
						.'\' to temporary file \''.$this->_tarname
						.'.tmp\'');
				return FALSE;
			}

			if ($this->_compress_type == 'gz')
			{
				$v_temp_tar = @gzopen($this->_tarname.".tmp", "rb");
			}
			elseif ($this->_compress_type == 'bz2')
			{
				$v_temp_tar = @bzopen($this->_tarname.".tmp", "r");
			}

			if ($v_temp_tar == 0)
			{
				$this->_error('Unable to open file \''.$this->_tarname
						.'.tmp\' in binary read mode');
				@rename($this->_tarname.".tmp", $this->_tarname);
				return FALSE;
			}

			if ( ! $this->_open_write())
			{
				@rename($this->_tarname.".tmp", $this->_tarname);
				return FALSE;
			}

			if ($this->_compress_type == 'gz')
			{
				$end_blocks = 0;

				while ( ! @ gzeof($v_temp_tar))
				{
					$v_buffer = @gzread($v_temp_tar, 512);
					if ($v_buffer == ARCHIVE_TAR_END_BLOCK || strlen($v_buffer) == 0)
					{
						$end_blocks++;
						// do not copy end blocks, we will re-make them
						// after appending
						continue;
					}
					elseif ($end_blocks > 0)
					{
						for ($i = 0; $i < $end_blocks; $i++)
						{
							$this->_write_block(ARCHIVE_TAR_END_BLOCK);
						}
						$end_blocks = 0;
					}
					$v_binary_data = pack("a512", $v_buffer);
					$this->_write_block($v_binary_data);
				}

				@gzclose($v_temp_tar);
			}
			elseif ($this->_compress_type == 'bz2')
			{
				$end_blocks = 0;

				while (strlen($v_buffer = @bzread($v_temp_tar, 512)) > 0)
				{
					if ($v_buffer == ARCHIVE_TAR_END_BLOCK || strlen($v_buffer) == 0)
					{
						$end_blocks++;
						// do not copy end blocks, we will re-make them
						// after appending
						continue;
					}
					elseif ($end_blocks > 0)
					{
						for ($i = 0; $i < $end_blocks; $i++)
						{
							$this->_write_block(ARCHIVE_TAR_END_BLOCK);
						}
						$end_blocks = 0;
					}
					$v_binary_data = pack("a512", $v_buffer);
					$this->_write_block($v_binary_data);
				}

				@bzclose($v_temp_tar);
			}

			if ( ! @ unlink($this->_tarname.".tmp"))
			{
				$this->_error('Error while deleting temporary file \''.$this->_tarname.'.tmp\'');
			}
		}
		else
		{
			// ----- For not compressed tar, just add files before the last
			//       one or two 512 bytes block
			if ( ! $this->_open_read_write())
			{
				return FALSE;
			}

			clearstatcache();
			$v_size = filesize($this->_tarname);

			// We might have zero, one or two end blocks.
			// The standard is two, but we should try to handle
			// other cases.
			fseek($this->_file, $v_size - 1024);
			if (fread($this->_file, 512) == ARCHIVE_TAR_END_BLOCK)
			{
				fseek($this->_file, $v_size - 1024);
			}
			elseif (fread($this->_file, 512) == ARCHIVE_TAR_END_BLOCK)
			{
				fseek($this->_file, $v_size - 512);
			}
		}

		return TRUE;
	}

	// }}}
	// {{{ _append()
	/**
	 * @ignore
	 * @param type $p_filelist
	 * @param type $p_add_dir
	 * @param type $p_remove_dir
	 * @return boolean
	 */
	function _append($p_filelist, $p_add_dir = '', $p_remove_dir = '')
	{
		if ( ! $this->_open_append())
		{
			return FALSE;
		}

		if ($this->_add_list($p_filelist, $p_add_dir, $p_remove_dir))
		{
			$this->_write_footer();
		}

		$this->_close();

		return TRUE;
	}

	// }}}
	// {{{ _dir_check()

	/**
	 * Check if a directory exists and create it (including parent
	 * dirs) if not.
	 *
	 * @param string $p_dir directory to check
	 *
	 * @return bool TRUE if the directory exists or was created
	 */
	function _dir_check($p_dir)
	{
		clearstatcache();
		if ((@is_dir($p_dir)) || ($p_dir == ''))
		{
			return TRUE;
		}

		$p_parent_dir = dirname($p_dir);

		if (($p_parent_dir != $p_dir) && ($p_parent_dir != '') && ( ! $this->_dir_check($p_parent_dir)))
		{
			return FALSE;
		}

		if ( ! @ mkdir($p_dir, 0777))
		{
			$this->_error("Unable to create directory '$p_dir'");
			return FALSE;
		}

		return TRUE;
	}

	// }}}
	// {{{ _path_reduction()

	/**
	 * Compress path by changing for example "/dir/foo/../bar" to "/dir/bar",
	 * rand emove double slashes.
	 *
	 * @param string $p_dir path to reduce
	 *
	 * @return string reduced path
	 *
	 * @access private
	 *
	 */
	function _path_reduction($p_dir)
	{
		$v_result = '';

		// ----- Look for not empty path
		if ($p_dir != '')
		{
			// ----- Explode path by directory names
			$v_list = explode('/', $p_dir);

			// ----- Study directories from last to first
			for ($i = sizeof($v_list) - 1; $i >= 0; $i--)
			{
				// ----- Look for current path
				if ($v_list[$i] == ".")
				{
					// ----- Ignore this directory
					// Should be the first $i=0, but no check is done
				}
				elseif ($v_list[$i] == "..")
				{
					// ----- Ignore it and ignore the $i-1
					$i--;
				}
				elseif (($v_list[$i] == '')	&& ($i != (sizeof($v_list) - 1)) && ($i != 0))
				{
					// ----- Ignore only the double '//' in path,
					// but not the first and last /
				}
				else
				{
					$v_result = $v_list[$i].(($i != (sizeof($v_list) - 1)) ? ('/'.$v_result) : '');
				}
			}
		}

		if (defined('OS_WINDOWS') && OS_WINDOWS)
		{
			$v_result = strtr($v_result, '\\', '/');
		}

		return $v_result;
	}

	// }}}
	// {{{ _translate_win_path()
	/**
	 * @ignore
	 * @param type $p_path
	 * @param type $p_remove_disk_letter
	 * @return type
	 */
	function _translate_win_path($p_path, $p_remove_disk_letter = TRUE)
	{
		if (defined('OS_WINDOWS') && OS_WINDOWS)
		{
			// ----- Look for potential disk letter
			if (($p_remove_disk_letter)	&& (($v_position = strpos($p_path, ':')) != FALSE))
			{
				$p_path = substr($p_path, $v_position + 1);
			}
			// ----- Change potential windows directory separator
			if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\'))
			{
				$p_path = strtr($p_path, '\\', '/');
			}
		}
		return $p_path;
	}

	// }}}
}