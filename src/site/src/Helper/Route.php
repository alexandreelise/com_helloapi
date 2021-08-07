<?php
declare(strict_types=1);
/**
 * Helloapi
 *
 * @package    Helloapi
 *
 * @author     Alexandre ELISÉ <contact@alexandre-elise.fr>
 * @copyright  Copyright(c) 2009 - 2021 Alexandre ELISÉ. All rights reserved
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://alexandre-elise.fr
 */

namespace AE\Component\Helloapis\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Language\Multilanguage;

/**
 * Helloapis Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_helloapis
 * @since       1.5
 */
abstract class Route
{
	/**
	 * Get the URL route for a helloapis from a helloapi ID, helloapis category ID and language
	 *
	 * @param   integer  $id        The id of the helloapis
	 * @param   integer  $catid     The id of the helloapis's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the helloapis
	 *
	 * @since   1.5
	 */
	public static function getHelloapisRoute($id, $catid, $language = 0)
	{
		// Create the link
		$link = 'index.php?option=com_helloapis&view=helloapi&id=' . $id;

		if ($catid > 1)
		{
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && Multilanguage::isEnabled())
		{
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Get the URL route for a helloapis category from a helloapis category ID and language
	 *
	 * @param   mixed  $catid     The id of the helloapis's category either an integer id or an instance of CategoryNode
	 * @param   mixed  $language  The id of the language being used.
	 *
	 * @return  string  The link to the helloapis
	 *
	 * @since   1.5
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof CategoryNode)
		{
			$id = $catid->id;
		}
		else
		{
			$id = (int) $catid;
		}

		if ($id < 1)
		{
			$link = '';
		}
		else
		{
			// Create the link
			$link = 'index.php?option=com_helloapis&view=category&id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled())
			{
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}
