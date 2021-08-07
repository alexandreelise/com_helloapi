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

namespace AE\Component\Helloapis\Site\View\Helloapi;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Registry\Registry;

/**
 * HTML Helloapis View class for the Helloapi component
 *
 * @since  0.1.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The page parameters
	 *
	 * @var    \Joomla\Registry\Registry|null
	 * @since  0.1.0
	 */
	protected $params = null;

	/**
	 * The item model state
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  0.1.0
	 */
	protected $state;

	/**
	 * The item object details
	 *
	 * @var    \JObject
	 * @since  0.1.0
	 */
	protected $item;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$item = $this->item = $this->get('Item');
		$state = $this->State = $this->get('State');
		$params = $this->Params = $state->get('params');
		$itemparams = new Registry(json_decode($item->params));

		$temp = clone $params;
		$temp->merge($itemparams);
		$item->params = $temp;

		Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_helloapis.helloapi', &$item));

		// Store the events for later
		$item->event = new \stdClass;
		$results = Factory::getApplication()->triggerEvent('onContentAfterTitle', array('com_helloapis.helloapi', &$item, &$item->params));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = Factory::getApplication()->triggerEvent('onContentBeforeDisplay', array('com_helloapis.helloapi', &$item, &$item->params));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = Factory::getApplication()->triggerEvent('onContentAfterDisplay', array('com_helloapis.helloapi', &$item, &$item->params));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		return parent::display($tpl);
	}
}
