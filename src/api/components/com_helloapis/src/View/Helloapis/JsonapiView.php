<?php
declare(strict_types=1);
/**
 * Helloapis
 *
 * @package    Helloapis
 *
 * @author     Alexandre ELISÉ <contact@alexandre-elise.fr>
 * @copyright  Copyright(c) 2009 - 2021 Alexandre ELISÉ. All rights reserved
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://alexandre-elise.fr
 */

namespace AE\Component\Helloapis\Api\View\Helloapis;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\JsonApiView as BaseApiView;

/**
 * Helloapis Api Endpoint JsonApi View
 */
class JsonapiView extends BaseApiView
{
	/**
	 * @var string[] $fieldsToRenderItem
	 */
	protected $fieldsToRenderItem = [
		'id',
		'name',
		'alias',
		'category_title',
		'language_title',
		'published',
		'publish_up',
		'publish_down',
		'access',
		'ordering',
	];
	
	/**
	 * @var string[] $fieldsToRenderList
	 */
	protected $fieldsToRenderList = [
		'id',
		'name',
		'alias',
		'category_title',
		'checked_out',
		'checked_out_time',
		'published',
		'access',
		'ordering',
		'language_title',
		'publish_up',
		'publish_down',
	];
}
