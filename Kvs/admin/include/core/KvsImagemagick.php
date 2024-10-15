<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

// =====================================================================================================================
// WARNING !!! This is not an API or any final KVS code, this code could be temporary for now.
// We will change / replace this code in future without any notification or concern.
// =====================================================================================================================

/**
 * Common image processing functions.
 */
final class KvsImagemagick
{
	public const RESIZE_TYPE_FIXED_SIZE = 'fixed_size';
	public const RESIZE_TYPE_MAX_SIZE = 'max_size';
	public const RESIZE_TYPE_MAX_WIDTH = 'max_width';
	public const RESIZE_TYPE_MAX_HEIGHT = 'max_height';

	// =================================================================================================================
	// Static properties and methods
	// =================================================================================================================

	/**
	 * Checks if the given image is a valid GIF.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_gif(string $image_path): bool
	{
		if ($image_path === '')
		{
			throw new InvalidArgumentException('Empty path passed to GIF detection logic');
		}

		if (!is_file($image_path))
		{
			throw KvsException::coding_error('Non-existing image file passed to GIF detection logic', $image_path);
		}

		if (!function_exists('imagecreatefromgif') )
		{
			throw KvsException::logic_error('GD2 module function is not available', 'imagecreatefromgif');
		}

		if (@imagecreatefromgif($image_path))
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if the given image is a valid JPEG.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_jpeg(string $image_path): bool
	{
		if ($image_path === '')
		{
			throw new InvalidArgumentException('Empty path passed to JPEG detection logic');
		}

		if (!is_file($image_path))
		{
			throw KvsException::coding_error('Non-existing image file passed to JPEG detection logic', $image_path);
		}

		if (!function_exists('imagecreatefromjpeg') )
		{
			throw KvsException::logic_error('GD2 module function is not available', 'imagecreatefromjpeg');
		}

		if (@imagecreatefromjpeg($image_path))
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if the given image is a valid PNG.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_png(string $image_path): bool
	{
		if ($image_path === '')
		{
			throw new InvalidArgumentException('Empty path passed to PNG detection logic');
		}

		if (!is_file($image_path))
		{
			throw KvsException::coding_error('Non-existing image file passed to PNG detection logic', $image_path);
		}

		if (!function_exists('imagecreatefrompng') )
		{
			throw KvsException::logic_error('GD2 module function is not available', 'imagecreatefrompng');
		}

		if (@imagecreatefrompng($image_path))
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if the given image is a valid WEBP.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_webp(string $image_path): bool
	{
		if ($image_path === '')
		{
			throw new InvalidArgumentException('Empty path passed to WEBP detection logic');
		}

		if (!is_file($image_path))
		{
			throw KvsException::coding_error('Non-existing image file passed to WEBP detection logic', $image_path);
		}

		$size = @getimagesize($image_path);
		if (!is_array($size) || $size[0] == 0 || $size[1] == 0 || $size['mime'] != 'image/webp')
		{
			// imagecreatefromwebp causes FATAL error on wrong images
			return false;
		}
		return true;
	}

	/**
	 * Checks if the given file is a valid image of a known formats.
	 *
	 * @param string $file_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_image(string $file_path): bool
	{
		return self::is_gif($file_path) || self::is_jpeg($file_path) || self::is_png($file_path) || self::is_webp($file_path);
	}

	/**
	 * Checks if the given image is an animated GIF.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_animated_gif(string $image_path): bool
	{
		global $config;

		if (!self::is_gif($image_path))
		{
			return false;
		}

		$response = KvsUtilities::exec_command(str_replace('/convert', '/identify', $config['image_magick_path']), ['1' => $image_path]);
		if (strpos($response, $image_path . '[0] GIF') !== false)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if the given image is an animated WEBP.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_animated_webp(string $image_path): bool
	{
		global $config;

		if (!self::is_webp($image_path))
		{
			return false;
		}

		$response = KvsUtilities::exec_command(str_replace('/convert', '/identify', $config['image_magick_path']), ['1' => $image_path]);
		if (strpos($response, $image_path . '[0] WEBP') !== false)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if the given image has transparency channel.
	 *
	 * @param string $image_path
	 *
	 * @return bool
	 * @throws KvsException
	 */
	public static function is_transparent(string $image_path): bool
	{
		if ($image_path === '')
		{
			throw new InvalidArgumentException('Empty path passed to transparent image detection logic');
		}

		if (!is_file($image_path))
		{
			throw KvsException::coding_error('Non-existing image file passed to transparent image detection logic', $image_path);
		}

		$size = @getimagesize($image_path);
		if (!is_array($size) || $size[0] == 0 || $size[1] == 0)
		{
			throw KvsException::coding_error('Invalid image passed to transparent image detection logic', $image_path);
		}

		if ($size['mime'] != 'image/webp' && $size['mime'] != 'image/gif' && $size['mime'] != 'image/png')
		{
			return false;
		}

		$image = null;
		if ($size['mime'] == 'image/png')
		{
			if (!function_exists('imagecreatefrompng') )
			{
				throw KvsException::logic_error('GD2 module function is not available', 'imagecreatefrompng');
			}
			$image = @imagecreatefrompng($image_path);
		} elseif ($size['mime'] == 'image/gif')
		{
			if (!function_exists('imagecreatefromgif') )
			{
				throw KvsException::logic_error('GD2 module function is not available', 'imagecreatefromgif');
			}
			$image = @imagecreatefromgif($image_path);
		} elseif ($size['mime'] == 'image/webp')
		{
			$image = null;
			// imagecreatefromwebp causes FATAL error
		}
		if ($image)
		{
			$rgba = @imagecolorat($image, 1, 1);
			if ($rgba !== false)
			{
				$colors = imagecolorsforindex($image, $rgba);
				if (is_array($colors) && $colors['alpha'] == 127)
				{
					return true;
				}
			}

			$rgba = @imagecolorat($image, 1, imagesy($image));
			if ($rgba !== false)
			{
				$colors = imagecolorsforindex($image, $rgba);
				if (is_array($colors) && $colors['alpha'] == 127)
				{
					return true;
				}
			}

			$rgba = @imagecolorat($image, imagesx($image), 1);
			if ($rgba !== false)
			{
				$colors = imagecolorsforindex($image, $rgba);
				if (is_array($colors) && $colors['alpha'] == 127)
				{
					return true;
				}
			}

			$rgba = @imagecolorat($image, imagesx($image), imagesy($image));
			if ($rgba !== false)
			{
				$colors = imagecolorsforindex($image, $rgba);
				if (is_array($colors) && $colors['alpha'] == 127)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Resizes image with the given resize logic. Throws error on failure.
	 *
	 * @param string $resize_type
	 * @param string $source_path
	 * @param string $target_path
	 * @param string $size_string
	 * @param bool $try_webp
	 *
	 * @throws KvsException
	 */
	public static function resize_image(string $resize_type, string $source_path, string $target_path, string $size_string, bool $try_webp = false): void
	{
		if ($source_path === '' || $target_path === '')
		{
			throw new InvalidArgumentException('Empty path passed to image resize logic');
		}

		if (!is_file($source_path))
		{
			throw KvsException::coding_error('Attempt to resize non-existing image file', $source_path);
		}

		if (is_file($target_path))
		{
			if ($source_path != $target_path)
			{
				KvsFilesystem::unlink($target_path);
			}
		} elseif (is_dir($target_path))
		{
			throw KvsException::coding_error('Attempt to resize image into directory', $target_path);
		}

		if (!is_writable(dirname($target_path)))
		{
			throw KvsException::logic_error('Attempt to resize image into non-writable path', $target_path);
		}

		$size = KvsUtilities::parse_size($size_string);
		if ($size[0] == 0 || $size[1] == 0)
		{
			throw KvsException::coding_error('Unsupported size value passed to image resize logic', $size_string);
		}

		$source_size = getimagesize($source_path);
		if (!is_array($source_size) || $source_size[0] == 0 || $source_size[1] == 0)
		{
			throw KvsException::coding_error('Invalid image passed to resize logic', $source_path);
		}

		switch ($resize_type)
		{
			case self::RESIZE_TYPE_FIXED_SIZE:
				self::resize_image_fixed_size($source_path, $target_path, $size, $source_size, $try_webp);
				break;
			case self::RESIZE_TYPE_MAX_SIZE:
			case self::RESIZE_TYPE_MAX_WIDTH:
			case self::RESIZE_TYPE_MAX_HEIGHT:
				self::resize_image_max_size($resize_type, $source_path, $target_path, $size, $source_size, $try_webp);
				break;
			default:
				throw new InvalidArgumentException("Unsupported resize type: $resize_type");
		}
	}

	/**
	 * Merges multiple images into a single image.
	 *
	 * @param array $image_paths
	 * @param string $target_path
	 *
	 * @throws KvsException
	 */
	public static function montage_horizontally(array $image_paths, string $target_path): void
	{
		global $config;

		if (count($image_paths) == 0)
		{
			throw KvsException::coding_error('Attempt to montage empty list of images');
		}
		if (is_dir($target_path))
		{
			throw KvsException::coding_error('Attempt to montage image into directory', $target_path);
		}
		if (!is_writable(dirname($target_path)))
		{
			throw KvsException::logic_error('Attempt to montage image into non-writable path', $target_path);
		}

		$image_sizes = [];
		$min_image_height = PHP_INT_MAX;
		for ($i = 0; $i < count($image_paths); $i++)
		{
			if (!is_file($image_paths[$i]))
			{
				throw KvsException::coding_error('Attempt to montage non-existing image file', $image_paths[$i]);
			}
			$image_size = @getimagesize($image_paths[$i]);
			if (!is_array($image_size) || $image_size[0] == 0 || $image_size[1] == 0)
			{
				throw KvsException::coding_error('Attempt to montage invalid image file', $image_paths[$i]);
			}
			$image_sizes[$i] = $image_size;
			if ($image_size[1] < $min_image_height)
			{
				$min_image_height = $image_size[1];
			}
		}

		$params = [];
		for ($i = 0; $i < count($image_paths); $i++)
		{
			if ($image_sizes[$i][1] > $min_image_height)
			{
				$temp_image = KvsFilesystem::create_new_temp_file_path('.jpg');
				self::resize_image(self::RESIZE_TYPE_MAX_HEIGHT, $image_paths[$i], $temp_image, "10000x$min_image_height");
				$params[$i + 1] = $temp_image;
			} else
			{
				$params[$i + 1] = $image_paths[$i];
			}
		}

		$params['-tile'] = count($image_paths) . 'x1';
		$params['-geometry'] = '+0+0';
		$params[$i + 1] = $target_path;

		$command_output = KvsUtilities::exec_command(str_replace('/convert', '/montage', $config['image_magick_path']), $params);
		if (is_file($target_path) && filesize($target_path) > 0)
		{
			return;
		}

		$last_shell_command = KvsUtilities::get_last_shell_command();
		throw KvsException::error("Failed to execute ImageMagick", KvsException::ERROR_IMAGE_PROCESSING_GENERAL, "$last_shell_command\n$command_output");
	}

	/**
	 * Resizes image with fixed size, when both width and height are fixed regardless of the provided image size and
	 * proportions. Throws error on failure.
	 *
	 * @param string $source_path
	 * @param string $target_path
	 * @param array $size
	 * @param array $source_size
	 * @param bool $try_webp
	 *
	 * @throws KvsException
	 */
	private static function resize_image_fixed_size(string $source_path, string $target_path, array $size, array $source_size, bool $try_webp = false): void
	{
		global $config;

		$target_extension = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
		$use_only_first_frame = false;
		if (self::is_animated_gif($source_path))
		{
			if ($target_extension === 'gif' || $target_extension === 'webp')
			{
				$temp_path = KvsFilesystem::create_new_temp_file_path('gif');
				KvsUtilities::exec_command($config['image_magick_path'], ['1' => $source_path, '-coalesce' => '', '-repage' => '0x0', '2' => $temp_path]);
				$source_path = $temp_path;
			} else
			{
				$use_only_first_frame = true;
			}
		} elseif (self::is_animated_webp($source_path))
		{
			if ($target_extension !== 'gif' && $target_extension !== 'webp')
			{
				$use_only_first_frame = true;
			}
		}

		$command_options = [];
		$command_options['1'] = $source_path . ($use_only_first_frame ? '[0]' : '');
		$command_options['-quality'] = $config['imagemagick_default_jpeg_quality'];
		if ($source_size[0] > $size[0] || $source_size[1] > $size[1])
		{
			$k1 = $source_size[0] / $size[0];
			$k2 = $source_size[1] / $size[1];

			if ($k1 >= 1 || $k2 >= 1)
			{
				if ($k1 >= $k2)
				{
					if ($k2 >= 1)
					{
						$new_width = ceil($source_size[0] / $k2);
						$new_height = ceil($source_size[1] / $k2);
					} else
					{
						[$new_width, $new_height] = $source_size;
					}
				} else
				{
					if ($k1 >= 1)
					{
						$new_width = ceil($source_size[0] / $k1);
						$new_height = ceil($source_size[1] / $k1);
					} else
					{
						[$new_width, $new_height] = $source_size;
					}
				}
			} else
			{
				[$new_width, $new_height] = $source_size;
			}

			$new_width++;
			$new_height++;
			$command_options['-resize'] = "{$new_width}x{$new_height}";
		}

		$command_options['-background'] = 'black';
		if ($source_size['mime'] == 'image/png' || $source_size['mime'] == 'image/gif')
		{
			if (self::is_transparent($source_path))
			{
				$command_options['-background'] = 'none';
			}
		}
		$command_options['-gravity'] = 'center';
		$command_options['-extent'] = "$size[0]x$size[1]";
		$command_options['2'] = $target_path;
		if ($target_extension === 'jpg' && $try_webp)
		{
			$command_options['2'] = "webp:$target_path";
		}

		$command_output = KvsUtilities::exec_command($config['image_magick_path'], $command_options);
		if (is_file($target_path) && filesize($target_path) > 0)
		{
			return;
		}

		if ($target_extension === 'jpg' && $try_webp)
		{
			// failed to create webp, create jpg then
			$command_options['2'] = "$target_path";

			$command_output = KvsUtilities::exec_command($config['image_magick_path'], $command_options);
			if (is_file($target_path) && filesize($target_path) > 0)
			{
				return;
			}
		}

		$last_shell_command = KvsUtilities::get_last_shell_command();
		throw KvsException::error("Failed to execute ImageMagick", KvsException::ERROR_IMAGE_PROCESSING_GENERAL, "$last_shell_command\n$command_output");
	}

	/**
	 * Resizes image with dynamic size, either fixing width or height, or any of them. Returns operation success or
	 * failure. Throws error on failure.
	 *
	 * @param string $resize_type
	 * @param string $source_path
	 * @param string $target_path
	 * @param array $size
	 * @param array $source_size
	 * @param bool $try_webp
	 *
	 * @throws KvsException
	 */
	private static function resize_image_max_size(string $resize_type, string $source_path, string $target_path, array $size, array $source_size, bool $try_webp = false): void
	{
		global $config;

		$target_extension = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
		if (self::is_animated_gif($source_path))
		{
			if ($source_size[0] > $size[0] || $source_size[1] > $size[1])
			{
				if ($target_extension === 'gif')
				{
					$temp_path = KvsFilesystem::create_new_temp_file_path('gif');
					KvsUtilities::exec_command($config['image_magick_path'], ['1' => $source_path, '-coalesce' => '', '-repage' => '0x0', '2' => $temp_path]);
					$source_path = $temp_path;
				} else
				{
					$source_path .= '[0]';
				}
			} elseif ($target_extension !== 'gif')
			{
				$source_path .= '[0]';
			}
		}

		$command_options = [];
		$command_options['1'] = $source_path;
		$command_options['-quality'] = $config['imagemagick_default_jpeg_quality'];
		if ($source_size[0] > $size[0] || $source_size[1] > $size[1])
		{
			if ($resize_type == self::RESIZE_TYPE_MAX_SIZE)
			{
				$command_options['-resize'] = "$size[0]x$size[1]";
			} elseif ($resize_type == self::RESIZE_TYPE_MAX_WIDTH)
			{
				$new_width = $size[0];
				if ($new_width > $source_size[0])
				{
					$new_width = $source_size[0];
				}
				$new_height = ceil($new_width * $source_size[1] / $source_size[0]);
				if ($new_height > $size[1])
				{
					$new_height = $size[1];
				}
				$command_options['-resize'] = "{$new_width}x{$new_height}^";
				$command_options['-gravity'] = 'center';
				$command_options['-crop'] = "{$new_width}x{$new_height}+0+0";
				$command_options['+repage'] = '';
			} elseif ($resize_type == self::RESIZE_TYPE_MAX_HEIGHT)
			{
				$new_height = $size[1];
				if ($new_height > $source_size[1])
				{
					$new_height = $source_size[1];
				}
				$new_width = ceil($new_height * $source_size[0] / $source_size[1]);
				if ($new_width > $size[0])
				{
					$new_width = $size[0];
				}
				$command_options['-resize'] = "{$new_width}x{$new_height}^";
				$command_options['-gravity'] = 'center';
				$command_options['-crop'] = "{$new_width}x{$new_height}+0+0";
				$command_options['+repage'] = '';
			}
		}
		$command_options['2'] = $target_path;
		if ($target_extension === 'jpg' && $try_webp)
		{
			$command_options['2'] = "webp:$target_path";
		}

		$command_output = KvsUtilities::exec_command($config['image_magick_path'], $command_options);
		if (is_file($target_path) && filesize($target_path) > 0)
		{
			return;
		}

		if ($target_extension === 'jpg' && $try_webp)
		{
			// failed to create webp, create jpg then
			$command_options['2'] = "$target_path";

			$command_output = KvsUtilities::exec_command($config['image_magick_path'], $command_options);
			if (is_file($target_path) && filesize($target_path) > 0)
			{
				return;
			}
		}

		$last_shell_command = KvsUtilities::get_last_shell_command();
		throw KvsException::error("Failed to execute ImageMagick", KvsException::ERROR_IMAGE_PROCESSING_GENERAL, "$last_shell_command\n$command_output");
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