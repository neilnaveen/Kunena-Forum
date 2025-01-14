<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Class KunenaVersion
 * @since Kunena
 */
class KunenaAdminVersion
{
	/**
	 * Get warning for unstable releases
	 *
	 * @param   string $msg Message to be shown containing two %s parameters for version (2.0.0RC) and version type
	 *                      (GIT, RC, BETA etc)
	 *
	 * @return    string    Warning message
	 * @since    1.6
	 */
	public function getVersionWarning($msg = 'COM_KUNENA_VERSION_WARNING')
	{
		if (strpos(KunenaForum::version(), 'GIT') !== false)
		{
			$kn_version_type    = Text::_('COM_KUNENA_VERSION_GIT');
			$kn_version_warning = Text::_('COM_KUNENA_VERSION_GIT_WARNING');
		}
		else
		{
			if (strpos(KunenaForum::version(), 'DEV') !== false)
			{
				$kn_version_type    = Text::_('COM_KUNENA_VERSION_DEV');
				$kn_version_warning = Text::_('COM_KUNENA_VERSION_DEV_WARNING');
			}
			else
			{
				if (strpos(KunenaForum::version(), 'RC') !== false)
				{
					$kn_version_type    = Text::_('COM_KUNENA_VERSION_RC');
					$kn_version_warning = Text::_('COM_KUNENA_VERSION_RC_WARNING');
				}
				else
				{
					if (strpos(KunenaForum::version(), 'BETA') !== false)
					{
						$kn_version_type    = Text::_('COM_KUNENA_VERSION_BETA');
						$kn_version_warning = Text::_('COM_KUNENA_VERSION_BETA_WARNING');
					}
					else
					{
						if (strpos(KunenaForum::version(), 'ALPHA') !== false)
						{
							$kn_version_type    = Text::_('COM_KUNENA_VERSION_ALPHA');
							$kn_version_warning = Text::_('COM_KUNENA_VERSION_ALPHA_WARNING');
						}
					}
				}
			}
		}

		if (!empty($kn_version_warning) && !empty($kn_version_type))
		{
			return Text::sprintf($msg, '<strong>' . strtoupper(KunenaForum::version()), $kn_version_type . '</strong>') . ' ' . $kn_version_warning;
		}

		return '';
	}

	/**
	 * @return boolean
	 * @since Kunena
	 */
	public function checkVersion()
	{
		$version = $this->getDBVersion();

		if (!isset($version->version))
		{
			return false;
		}

		if ($version->state)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get version information from database
	 *
	 * @param    string    Kunena table prefix
	 *
	 * @return    object    Version table
	 * @since    1.6
	 */
	public function getDBVersion($prefix = 'kunena_')
	{
		$db    = Factory::getDBO();
		$query = "SHOW TABLES LIKE {$db->quote($db->getPrefix() . $prefix . 'version')}";
		$db->setQuery($query);

		if ($db->loadResult())
		{
			$db->setQuery("SELECT * FROM " . $db->quoteName($db->getPrefix() . $prefix . 'version') . " ORDER BY `id` DESC", 0, 1);
			$version = $db->loadObject();
		}

		if (!isset($version) || !is_object($version) || !isset($version->state))
		{
			$version        = new stdClass;
			$version->state = '';
		}
		elseif (!empty($version->state))
		{
			if ($version->version != KunenaForum::version())
			{
				$version->state = '';
			}
		}

		return $version;
	}

	/**
	 * Retrieve installed Kunena version as string.
	 *
	 * @return string "Kunena X.Y.Z | YYYY-MM-DD [versionname]"
	 * @since Kunena
	 */
	public static function getVersionHTML()
	{
		return 'Kunena ' . strtoupper(KunenaForum::version()) . ' | ' . KunenaForum::versionDate() . ' [ ' . KunenaForum::versionName() . ' ]';
	}

	/**
	 * Retrieve copyright information as string.
	 *
	 * @return string "© 2008 - 2020 Copyright: Kunena Team. All rights reserved. | License: GNU General Public License"
	 * @since Kunena
	 */
	public static function getCopyrightHTML()
	{
		return ': &copy; 2008 - 2020 ' . Text::_('COM_KUNENA_VERSION_COPYRIGHT') . ': <a href = "https://www.kunena.org/team" target = "_blank">'
			. Text::_('COM_KUNENA_VERSION_TEAM') . '</a>  | ' . Text::_('COM_KUNENA_VERSION_LICENSE')
			. ': <a href = "https://www.gnu.org/copyleft/gpl.html" target = "_blank">'
			. Text::_('COM_KUNENA_VERSION_GPL') . '</a>';
	}

	/**
	 * Retrieve installed Kunena version, copyright and license as string.
	 *
	 * @return string "Kunena X.Y.Z | YYYY-MM-DD | © 2008 - 2020 Copyright: Kunena Team. All rights reserved. |
	 *                License: GNU General Public License"
	 * @since Kunena
	 */
	public static function getLongVersionHTML()
	{
		return self::getVersionHTML() . ' | ' . self::getCopyrightHTML();
	}
}

/**
 * Class KunenaVersionException
 * @since Kunena
 */
class KunenaVersionException extends Exception
{
}
