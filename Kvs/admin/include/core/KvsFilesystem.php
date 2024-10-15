<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Common filesystem functions.
 */
final class KvsFilesystem
{
	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	public const SUPPORTED_FILE_EXTENSIONS = ['gif', 'jpg', 'png', 'txt', 'zip', 'pdf', 'mp4', 'tmp'];

	public const SCAN_DIR_ALL = 0;
	public const SCAN_DIR_FILES = 1;
	public const SCAN_DIR_DIRS = 2;

	private static $TEMP_FILES = [];
	private static $LOCAL_DATA_CACHE = [];

	/**
	 * Finds suitable temporary file path.
	 *
	 * @param string|null $file_ext
	 * @param bool $autodelete
	 *
	 * @return string
	 * @throws KvsException
	 */
	public static function create_new_temp_file_path(?string $file_ext = null, bool $autodelete = true): string
	{
		global $config;

		if (!is_dir($config['temporary_path']))
		{
			throw KvsException::logic_error('Temporary path does not exist', $config['temporary_path']);
		}
		if (!is_writable($config['temporary_path']))
		{
			throw KvsException::logic_error('Temporary path is not writable', $config['temporary_path']);
		}
		if (!$file_ext || !in_array($file_ext, self::SUPPORTED_FILE_EXTENSIONS))
		{
			$file_ext = 'tmp';
		}

		$rnd = mt_rand(1000000000, 999999999999);
		for ($i = 0; $i < 1000; $i++)
		{
			$result = "$config[temporary_path]/$rnd.$file_ext";
			if (!is_file($result))
			{
				if ($autodelete)
				{
					self::$TEMP_FILES[] = $result;
				}
				return $result;
			}
			$rnd = mt_rand(1000000000, 999999999999);
		}
		throw KvsException::logic_error('Failed to create temporary file path', "$config[temporary_path]/$rnd.$file_ext");
	}

	/**
	 * Scans directory and returns paths from it. Throws exception if directory exists, but failed to be scanned.
	 *
	 * @param string $dir
	 * @param int $filter
	 * @param bool $recursive
	 *
	 * @return array
	 * @throws KvsException
	 */
	public static function scan_dir(string $dir, int $filter = self::SCAN_DIR_ALL, bool $recursive = false): array
	{
		$result = [];
		if ($dir === '')
		{
			throw new InvalidArgumentException('Empty dir passed to be scanned');
		}
		if (!in_array($filter, KvsUtilities::get_class_constants_starting_with(self::class, 'SCAN_DIR_')))
		{
			throw new InvalidArgumentException("Unsupported dir scan filter passed ($filter)");
		}
		if (!self::check_path_security($dir))
		{
			throw new KvsSecurityException('Attempt to use insecure path in directory scan method', $dir);
		}
		if (is_dir($dir))
		{
			$temp = @scandir($dir);
			if (is_array($temp))
			{
				foreach ($temp as $name)
				{
					if ($name == '.' || $name == '..')
					{
						continue;
					}
					if ($filter == self::SCAN_DIR_ALL || ($filter == self::SCAN_DIR_FILES && !is_dir("$dir/$name")) ||
							($filter == self::SCAN_DIR_DIRS && is_dir("$dir/$name")))
					{
						$result[] = "$dir/$name";
					}
					if ($recursive && is_dir("$dir/$name"))
					{
						$result = array_merge($result, self::scan_dir("$dir/$name", $filter, true));
					}
				}
			} else
			{
				throw KvsException::error('Failed to scan directory', KvsException::ERROR_FILESYSTEM_GENERAL, $dir);
			}
		} else
		{
			throw KvsException::coding_error('Attempt to scan non-existing directory', $dir);
		}
		return $result;
	}

	/**
	 * Copies file or directory. Throws exception if the file cannot be copied for whatever reason.
	 *
	 * @param string $source
	 * @param string $target
	 *
	 * @throws KvsException
	 */
	public static function copy(string $source, string $target): void
	{
		$source = trim($source);
		$target = trim($target);
		if ($source === '' || $target === '')
		{
			throw new InvalidArgumentException('Empty path passed to be copied');
		}
		if (!self::check_path_security($source))
		{
			throw new KvsSecurityException('Attempt to use insecure path in copy method', $source);
		}
		if (!self::check_path_security($target))
		{
			throw new KvsSecurityException('Attempt to use insecure path in copy method', $target);
		}

		if (!is_file($source) && !is_dir($source))
		{
			throw KvsException::coding_error('Attempt to copy non existing file or directory', $source);
		}

		if (is_file($source))
		{
			if (is_dir($target))
			{
				throw KvsException::coding_error('Attempt to copy file into directory path', $target);
			}

			$parent = dirname($target);
			self::mkdir($parent);

			if (!@copy($source, $target))
			{
				if (is_file($target))
				{
					@unlink($target);
				}
				if (!@copy($source, $target))
				{
					throw KvsException::error('Failed to copy file', KvsException::ERROR_FILESYSTEM_COPY_FILE, "$source -> $target");
				}
			}

			KvsContext::log_debug('Copied file', "$source -> $target");
		} else
		{
			if (is_file($target))
			{
				throw KvsException::coding_error('Attempt to copy directory into file path', $target);
			}

			self::mkdir($target);

			$names = self::scan_dir($source);
			foreach ($names as $name)
			{
				$name = basename($name);
				self::copy("$source/$name", "$target/$name");
				KvsContext::log_debug('Copied child path inside directory', "$source/$name -> $target/$name");
			}
			KvsContext::log_debug('Copied directory', "$source -> $target");
		}
	}

	/**
	 * Renames file or directory. Throws exception if the file cannot be renamed for whatever reason.
	 *
	 * @param string $source
	 * @param string $target
	 *
	 * @throws KvsException
	 */
	public static function rename(string $source, string $target): void
	{
		$source = trim($source);
		$target = trim($target);
		if ($source === '' || $target === '')
		{
			throw new InvalidArgumentException('Empty path passed to be renamed');
		}
		if (!self::check_path_security($source))
		{
			throw new KvsSecurityException('Attempt to use insecure path in renamed method', $source);
		}
		if (!self::check_path_security($target))
		{
			throw new KvsSecurityException('Attempt to use insecure path in renamed method', $target);
		}

		if (!is_file($source) && !is_dir($source))
		{
			throw KvsException::coding_error('Attempt to rename non existing file or directory', $source);
		}

		if (is_file($source))
		{
			if (is_dir($target))
			{
				throw KvsException::coding_error('Attempt to rename file into directory path', $target);
			}

			$parent = dirname($target);
			self::mkdir($parent);

			if (!@rename($source, $target))
			{
				if (is_file($target))
				{
					@unlink($target);
				}
				if (!@rename($source, $target))
				{
					throw KvsException::error('Failed to rename file', KvsException::ERROR_FILESYSTEM_RENAME_FILE, "$source -> $target");
				}
			}

			KvsContext::log_debug('Renamed file', "$source -> $target");
		} else
		{
			if (is_file($target))
			{
				throw KvsException::coding_error('Attempt to rename directory into file path', $target);
			}
			if (is_dir($target))
			{
				throw KvsException::coding_error('Attempt to rename directory into existing directory path', $target);
			}

			if (!@rename($source, $target))
			{
				throw KvsException::error('Failed to rename dir', KvsException::ERROR_FILESYSTEM_RENAME_FILE, "$source -> $target");
			}

			KvsContext::log_debug('Renamed directory', "$source -> $target");
		}
	}

	/**
	 * Creates directory. Throws exception if the directory cannot be created for whatever reason.
	 *
	 * @param string $dir
	 * @param int $permissions
	 *
	 * @throws KvsException
	 */
	public static function mkdir(string $dir, int $permissions = 0777): void
	{
		if ($dir === '')
		{
			throw new InvalidArgumentException('Empty directory passed to be created');
		}
		if (!self::check_path_security($dir))
		{
			throw new KvsSecurityException('Attempt to use insecure path in directory creation method', $dir);
		}

		if (is_dir($dir))
		{
			@chmod($dir, $permissions);
			if (!is_writable($dir))
			{
				throw KvsException::error('Failed to chmod existing directory', KvsException::ERROR_FILESYSTEM_CHMOD, $dir);
			}
			return;
		}

		$parent_dir = dirname($dir);
		if (!is_dir($parent_dir) && $parent_dir !== $dir)
		{
			self::mkdir($parent_dir, $permissions);
		}
		if (@mkdir($dir, $permissions))
		{
			KvsContext::log_debug('Created directory', $dir);
		} else
		{
			throw KvsException::error('Failed to create directory', KvsException::ERROR_FILESYSTEM_CREATE_DIRECTORY, $dir);
		}
	}

	/**
	 * Removes directory. Throws exception if the directory exists and cannot be deleted for whatever reason.
	 *
	 * @param string $dir
	 * @param bool $recursive
	 * @param bool $only_clear
	 *
	 * @throws KvsException
	 */
	public static function rmdir(string $dir, bool $recursive = false, bool $only_clear = false): void
	{
		$dir = trim($dir);
		if ($dir === '')
		{
			throw new InvalidArgumentException('Empty directory passed to be deleted');
		}
		if (!self::check_path_security($dir))
		{
			throw new KvsSecurityException('Attempt to use insecure path in directory delete method', $dir);
		}
		if (is_file($dir))
		{
			throw KvsException::coding_error('Attempt to delete file as of directory', $dir);
		}
		if (!is_dir($dir))
		{
			return;
		}

		$files = @scandir($dir);
		foreach ($files as $file)
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}
			$file = $dir . '/' . $file;
			if (is_file($file))
			{
				self::unlink($file);
			} elseif (is_dir($file) && $recursive)
			{
				self::rmdir($file, $recursive);
			}
		}

		if ($only_clear)
		{
			return;
		}

		$result = @rmdir($dir);
		if ($result)
		{
			KvsContext::log_debug('Deleted directory', $dir);
		} else
		{
			throw KvsException::error('Failed to delete directory', KvsException::ERROR_FILESYSTEM_DELETE_DIRECTORY, $dir);
		}
	}

	/**
	 * Removes file. Throws exception if the file exists and cannot be deleted for whatever reason.
	 *
	 * @param string $file
	 *
	 * @throws KvsException
	 */
	public static function unlink(string $file): void
	{
		$file = trim($file);
		if ($file === '')
		{
			throw new InvalidArgumentException('Empty file passed to be deleted');
		}
		if (!self::check_path_security($file))
		{
			throw new KvsSecurityException('Attempt to use insecure path in file delete method', $file);
		}

		if (is_dir($file))
		{
			throw KvsException::coding_error('Attempt to delete directory as of file', $file);
		}
		if (!is_file($file))
		{
			return;
		}

		if (@unlink($file))
		{
			KvsContext::log_debug('Deleted file', $file);
		} else
		{
			throw KvsException::error('Failed to delete file', KvsException::ERROR_FILESYSTEM_DELETE_FILE, $file);
		}
	}

	/**
	 * Reads file from disk. Throws exception if file doesn't exists or cannot be read for whatever reason.
	 *
	 * @param string $file
	 * @param bool $uncached
	 *
	 * @return string
	 * @throws KvsException
	 */
	public static function read_file(string $file, bool $uncached = false): string
	{
		$file = trim($file);
		if ($file === '')
		{
			throw new InvalidArgumentException('Empty file passed to be read');
		}
		if (!self::check_path_security($file))
		{
			throw new KvsSecurityException('Attempt to use insecure path in file read method', $file);
		}
		if (is_dir($file))
		{
			throw KvsException::coding_error('Attempt to read directory as of file', $file);
		}

		if (!$uncached && isset(self::$LOCAL_DATA_CACHE[$file]))
		{
			return self::$LOCAL_DATA_CACHE[$file];
		}

		if (!is_file($file))
		{
			throw KvsException::coding_error('Attempt to read non-existing file', $file);
		}

		$result = @file_get_contents($file);
		if ($result === false)
		{
			throw KvsException::error('Failed to read file', KvsException::ERROR_FILESYSTEM_READ_FILE, $file);
		}

		KvsContext::log_debug('Read file', $file);
		return self::$LOCAL_DATA_CACHE[$file] = trim($result);
	}

	/**
	 * Tries to read file without throwing any errors. Returns the readed contents or empty string on any error.
	 *
	 * @param string $file
	 * @param bool $uncached
	 *
	 * @return string
	 */
	public static function maybe_read_file(string $file, bool $uncached = false): string
	{
		try
		{
			if ($file === '' || !is_file($file))
			{
				return '';
			}
			return self::read_file($file, $uncached);
		} catch (Throwable $e)
		{
			KvsContext::log_exception($e);
			return '';
		}
	}

	/**
	 * Writes file to disk. Throws exception if file cannot be written for whatever reason.
	 *
	 * @param string $file
	 * @param string $contents
	 * @param bool $append
	 *
	 * @return void
	 * @throws KvsException
	 */
	public static function write_file(string $file, string $contents, bool $append = false): void
	{
		$file = trim($file);
		if ($file === '')
		{
			throw new InvalidArgumentException('Empty file passed to be written');
		}
		if (!self::check_path_security($file))
		{
			throw new KvsSecurityException('Attempt to use insecure path in file write method', $file);
		}
		if (is_dir($file))
		{
			throw KvsException::coding_error('Attempt to write directory as of file', $file);
		}

		if (!is_dir(dirname($file)))
		{
			self::mkdir(dirname($file));
		}
		if ($append)
		{
			$result = @file_put_contents($file, $contents, LOCK_EX | FILE_APPEND);
		} else
		{
			$result = @file_put_contents($file, $contents, LOCK_EX);
		}
		if ($result === false)
		{
			throw KvsException::error('Failed to write file', KvsException::ERROR_FILESYSTEM_WRITE_FILE, $file);
		}

		if (!$append)
		{
			self::$LOCAL_DATA_CACHE[$file] = $contents;
			KvsContext::log_debug('Wrote file', $file);
		} else
		{
			unset(self::$LOCAL_DATA_CACHE[$file]);
			KvsContext::log_debug('Appended file', $file);
		}
	}

	/**
	 * Tries to write file to disk without throwing any errors. Returns the result of operation.
	 *
	 * @param string $file
	 * @param string $contents
	 * @param bool $append
	 *
	 * @return bool
	 */
	public static function maybe_write_file(string $file, string $contents, bool $append = false): bool
	{
		try
		{
			self::write_file($file, $contents, $append);
			return true;
		} catch (Throwable $e)
		{
			KvsContext::log_exception($e);
			return false;
		}
	}

	/**
	 * Tries to append log file with the new line. Returns the result of operation.
	 *
	 * @param string $file
	 * @param string $contents
	 * @param string $line_separator
	 *
	 * @return bool
	 */
	public static function maybe_append_log(string $file, string $contents, string $line_separator = "\n"): bool
	{
		try
		{
			self::write_file($file, "$contents{$line_separator}", true);
			return true;
		} catch (Throwable $e)
		{
			KvsContext::log_exception($e);
			return false;
		}
	}

	/**
	 * Calculates file hash with some performance optimizations.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @throws KvsException
	 */
	public static function file_hash(string $file): string
	{
		global $config;

		$file = trim($file);
		if ($file === '')
		{
			throw new InvalidArgumentException('Empty file passed to be hashed');
		}
		if (!self::check_path_security($file))
		{
			throw new KvsSecurityException('Attempt to use insecure path in file hash method', $file);
		}

		if (!is_file($file))
		{
			throw KvsException::coding_error('Attempt to hash non-existing file', $file);
		}

		if (isset($config['optimize_file_hashing']))
		{
			$fh = @fopen($file, 'r');
			if (!$fh)
			{
				throw KvsException::error('Failed to read file', KvsException::ERROR_FILESYSTEM_READ_FILE, $file);
			}
			$file_contents = fread($fh, 100000);
			if (!$file_contents)
			{
				throw KvsException::error('Failed to read file', KvsException::ERROR_FILESYSTEM_READ_FILE, $file);
			}
			return md5($file_contents);
		}
		$result = md5_file($file);
		if (!$result)
		{
			throw KvsException::error('Failed to read file', KvsException::ERROR_FILESYSTEM_READ_FILE, $file);
		}
		return $result;
	}

	/**
	 * Reads JSON file and returns parsed array from it, or throws exception if failed to parse or file doesn't exist.
	 *
	 * @param string $path
	 *
	 * @return array
	 * @throws KvsException
	 */
	public static function parse_json(string $path): array
	{
		if (!function_exists('json_decode'))
		{
			throw new RuntimeException('JSON module is not installed within PHP');
		}

		$data = self::read_file($path);
		if ($data !== '')
		{
			$json = @json_decode($data, true);
			if (is_array($json))
			{
				return $json;
			} else
			{
				throw KvsException::logic_error('Failed to parse JSON file', $path);
			}
		} else
		{
			throw KvsException::logic_error('Required JSON file is missing or empty', $path);
		}
	}

	/**
	 * Reads serialized file and returns parsed array from it, or throws exception if failed to parse or file doesn't
	 * exist.
	 *
	 * @param string $path
	 *
	 * @return array
	 * @throws KvsException
	 */
	public static function parse_serialized(string $path): array
	{
		$data = self::read_file($path);
		if ($data !== '')
		{
			$serialized = @unserialize($data, ['allowed_classes' => false]);
			if (is_array($serialized))
			{
				return $serialized;
			} else
			{
				throw KvsException::logic_error('Failed to parse serialized file', $path);
			}
		} else
		{
			throw KvsException::logic_error('Required serialized file is missing or empty', $path);
		}
	}

	/**
	 * Reads XML file and returns parsed XML from it or throws exception if failed to parse or file doesn't exist.
	 *
	 * @param string $path
	 *
	 * @return SimpleXMLElement
	 * @throws KvsException
	 */
	public static function parse_xml(string $path): ?SimpleXMLElement
	{
		if (!function_exists('simplexml_load_string'))
		{
			throw new RuntimeException('XML module is not installed within PHP');
		}

		$data = self::read_file($path);
		if ($data !== '')
		{
			$result = @simplexml_load_string($data);
			if (!$result)
			{
				throw KvsException::logic_error('Failed to parse XML file', $path);
			}
			return $result;
		} else
		{
			throw KvsException::logic_error('Required XML file is missing or empty', $path);
		}
	}

	/**
	 * Reads properties file and returns name-value pairs from it, or throws exception if failed to parse or file
	 * doesn't exist.
	 *
	 * @param string $path
	 *
	 * @return array
	 * @throws KvsException
	 */
	public static function parse_properties(string $path): array
	{
		$data = self::read_file($path);
		if ($data !== '')
		{
			$result = [];
			$had_error = false;
			$contents = explode("\n", str_replace("\r", "\n", $data));
			foreach ($contents as $line)
			{
				$line = trim($line);
				if ($line == '' || strpos($line, '#') === 0)
				{
					continue;
				}

				$pair = explode('=', $line, 2);
				if (count($pair) == 2)
				{
					$result[trim($pair[0])] = trim($pair[1]);
				} else
				{
					$had_error = true;
					KvsException::logic_error('Failed to parse properties line', $line);
				}
			}
			if (count($result) == 0 && $had_error)
			{
				throw KvsException::logic_error('Failed to parse properties file', $path);
			}
			return $result;
		} else
		{
			throw KvsException::logic_error('Required properties file is missing or empty', $path);
		}
	}

	/**
	 * Updates the given properties in the properties file, or throws exception on failure.
	 *
	 * @param string $path
	 * @param array $properties
	 *
	 * @throws KvsException
	 */
	public static function update_properties(string $path, array $properties): void
	{
		if (count($properties) == 0)
		{
			return;
		}

		$contents = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", self::read_file($path))));
		$result = '';
		foreach ($contents as $line)
		{
			$line = trim($line);
			if ($line == '' || strpos($line, '#') === 0)
			{
				$result .= "$line\n";
				continue;
			}

			$pair = array_map('trim', explode('=', $line, 2));
			if (count($pair) == 2)
			{
				if (isset($properties[$pair[0]]))
				{
					$result .= "$pair[0] = {$properties[$pair[0]]}\n";
					unset($properties[$pair[0]]);
				} elseif (isset($properties["-$pair[0]"]))
				{
					unset($properties[$pair[0]]);
				} else
				{
					$result .= "$line\n";
				}
			} else
			{
				$result .= "$line\n";
			}
		}
		foreach ($properties as $new_key => $new_value)
		{
			if ($new_key[0] != '-')
			{
				$result .= "$new_key = $new_value\n";
			}
		}

		self::write_file($path, $result);
	}

	/**
	 * Cleans up all created temporary files that are allowed to be auto-deleted.
	 */
	public static function delete_temp_files(): void
	{
		foreach (self::$TEMP_FILES as $temp_file)
		{
			try
			{
				self::unlink($temp_file);
			} catch (Throwable $e)
			{
				KvsContext::log_exception($e);
			}
		}
	}

	/**
	 * Checks if OS type is windows.
	 *
	 * @return bool
	 */
	public static function is_os_windows(): bool
	{
		return strtolower(substr(PHP_OS, 0, 3)) == 'win';
	}

	/**
	 * Checks if path is allowed.
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	private static function check_path_security(string $path): bool
	{
		if (self::is_os_windows())
		{
			return strpos($path, '..\\') === false && strpos($path, '../') === false;
		}
		return substr($path, 0, 1) === '/' && strpos($path, '../') === false;
	}

	// =================================================================================================================
	// Properties
	// =================================================================================================================

	// =================================================================================================================
	// Public methods
	// =================================================================================================================

	// =================================================================================================================
	// Protected methods
	// =================================================================================================================

	// =================================================================================================================
	// Private methods
	// =================================================================================================================
}