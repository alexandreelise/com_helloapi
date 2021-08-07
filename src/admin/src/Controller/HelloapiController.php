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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * Controller for a single helloapi
 *
 * @since  0.1.0
 */
class HelloapiController extends FormController
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since  0.1.0
	 */
	public function batch($model = null)
	{
		$this->checkToken();

		$myModel = $model ?? $this->getModel('Helloapi', 'Administrator', array());

		// Preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_helloapis&view=helloapis' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
