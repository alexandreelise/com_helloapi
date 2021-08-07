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

namespace AE\Component\Helloapis\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Helloapis master display controller.
 *
 * @since  0.1.0
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  0.1.0
	 */
	protected $default_view = 'helloapis';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  BaseController|bool  This object to support chaining.
	 *
	 * @since   0.1.0
	 *
	 * @throws  \Exception
	 */
	public function display($cachable = false, $urlparams = array())
	{
		return parent::display();
	}
}
