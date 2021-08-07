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

namespace AE\Component\Helloapis\Administrator\Table;

defined('_JEXEC') or die;

use AE\Component\Helloapis\Administrator\Extension\HelloapisComponent;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use function explode;
use function implode;
use function is_array;
use function is_null;
use function str_replace;
use function trim;

/**
 * Helloapis Table class.
 *
 * @since  0.1.0
 */
class HelloapiTable extends Table implements VersionableTableInterface, TaggableTableInterface
{
	use TaggableTableTrait;
	
	/**
	 * Indicates that columns fully support the NULL value in the database
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $_supportNullValue = false;
	
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 *
	 * @since  0.1.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_helloapis.helloapi';
		
		// Set the alias since the column is called title
		$this->setColumnAlias('title', 'name');
		
		// Set the alias since the column is called state
		$this->setColumnAlias('published', 'state');
		
		parent::__construct('#__helloapis_details', 'id', $db);
	}
	
	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		
		return 'com_helloapis.helloapi.' . (int) $this->$k;
	}
	
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}
	
	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   Table    $table  A Table object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   1.6
	 */
	protected function _getAssetParentId(Table $table = null, $id = null)
	{
		$assetId = null;
		
		// This is an article under a category.
		if ($this->catid)
		{
			$catId = (int) $this->catid;
			
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true)
				->select($this->_db->quoteName('asset_id'))
				->from($this->_db->quoteName('#__categories'))
				->where($this->_db->quoteName('id') . ' = :catid')
				->bind(':catid', $catId, ParameterType::INTEGER);
			
			// Get the asset id from the database.
			$this->_db->setQuery($query);
			
			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}
		
		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
	
	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 *                          to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
	 *
	 * @see     Table::bind()
	 * @since   1.6
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && \is_array($array['params']))
		{
			$registry        = new Registry($array['params']);
			$array['params'] = (string) $registry;
		}
		
		if (isset($array['metadata']) && \is_array($array['metadata']))
		{
			$registry          = new Registry($array['metadata']);
			$array['metadata'] = (string) $registry;
		}
		
		// Bind the rules.
		if (isset($array['rules']) && \is_array($array['rules']))
		{
			$rules = new Rules($array['rules']);
			$this->setRules($rules);
		}
		
		return parent::bind($array, $ignore);
	}
	
	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     Table::check()
	 * @since   1.5
	 */
	public function check()
	{
		try
		{
			parent::check();
		}
		catch (\Exception $e)
		{
			$this->setError($e->getMessage());
			
			return false;
		}
		
		if (trim($this->getColumnAlias('title') ?? '') == '')
		{
			$this->setError(Text::_('COM_HELLOAPIS_WARNING_PROVIDE_VALID_NAME'));
			
			return false;
		}
		
		if (trim($this->alias) == '')
		{
			$this->alias = $this->title;
		}
		
		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);
		
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}
		
		// Check for a valid category.
		if (!$this->catid = (int) $this->catid)
		{
			$this->setError(Text::_('JLIB_DATABASE_ERROR_CATEGORY_REQUIRED'));
			
			return false;
		}
		
		/**
		 * Ensure any new items have compulsory fields set. This is needed for things like
		 * frontend editing where we don't show all the fields or using some kind of API
		 */
		if (!$this->id)
		{
			// Images can be an empty json string
			if (!isset($this->images))
			{
				$this->images = '{}';
			}
			
			// URLs can be an empty json string
			if (!isset($this->urls))
			{
				$this->urls = '{}';
			}
			
			// Attributes (article params) can be an empty json string
			if (!isset($this->attribs))
			{
				$this->attribs = '{}';
			}
			
			// Attributes (article params) can be an empty json string
			if (!isset($this->params))
			{
				$this->params = '{}';
			}
			
			// Metadata can be an empty json string
			if (!isset($this->metadata))
			{
				$this->metadata = '{}';
			}
			
			// Hits must be zero on a new item
			$this->hits = 0;
		}
		
		// Set publish_up to null if not set
		if (!$this->publish_up)
		{
			$this->publish_up = null;
		}
		
		// Set publish_down to null if not set
		if (!$this->publish_down)
		{
			$this->publish_down = null;
		}
		
		// Check the publish down date is not earlier than publish up.
		if (!is_null($this->publish_up) && !is_null($this->publish_down) && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp               = $this->publish_up;
			$this->publish_up   = $this->publish_down;
			$this->publish_down = $temp;
		}
		
		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey))
		{
			// Only process if not empty
			
			// Array of characters to remove
			$badCharacters = ["\n", "\r", "\"", '<', '>'];
			
			// Remove bad characters
			$afterClean = StringHelper::str_ireplace($badCharacters, '', $this->metakey);
			
			// Create array using commas as delimiter
			$keys = explode(',', $afterClean);
			
			$cleanKeys = [];
			
			foreach ($keys as $key)
			{
				if (trim($key))
				{
					// Ignore blank keywords
					$cleanKeys[] = trim($key);
				}
			}
			
			// Put array back together delimited by ", "
			$this->metakey = implode(', ', $cleanKeys);
		}
		else
		{
			$this->metakey = '';
		}
		
		if ($this->metadesc === null)
		{
			$this->metadesc = '';
		}
		
		return true;
	}
	
	/**
	 * Overrides Table::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function store($updateNulls = true)
	{
		$date = Factory::getDate()->toSql();
		$user = Factory::getUser();
		
		// Transform the params field
		if (is_array($this->params))
		{
			$registry     = new Registry($this->params);
			$this->params = (string) $registry;
		}
		
		
		// Set created date if not set.
		if (!(int) $this->created)
		{
			$this->created = $date;
		}
		
		if ($this->id)
		{
			// Existing item
			$this->modified_by = $user->get('id');
			$this->modified    = $date;
		}
		else
		{
			// Field created_by can be set by the user, so we don't touch it if it's set.
			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
			
			// Set modified to created date if not set
			if (!(int) $this->modified)
			{
				$this->modified = $this->created;
			}
			
			// Set modified_by to created_by user if not set
			if (empty($this->modified_by))
			{
				$this->modified_by = $this->created_by;
			}
		}
		
		// Verify that the alias is unique
		$app = Factory::getApplication();
		
		/**
		 * @var HelloapisComponent $component
		 */
		$component = $app->bootComponent('com_helloapis');
		$table     = $component->getMVCFactory()->createTable('Helloapi', 'Table', ['dbo' => $this->getDbo()]);
		
		if ($table->load(['alias' => $this->alias, 'catid' => $this->catid]) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(Text::_('JLIB_DATABASE_ERROR_ARTICLE_UNIQUE_ALIAS'));
			
			return false;
		}
		
		return parent::store($updateNulls);
	}
	
	/**
	 * Get the type alias for UCM features
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}
	
}
