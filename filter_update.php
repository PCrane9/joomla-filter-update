<?php
/**
 * Copyright info goes here... not good at legal stuff. Will update
 *
 * 
 *
 */
// We are a valid entry point.
const _JEXEC = 1;
// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
	
}
if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}
// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';
// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';
// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Load Library language
$lang = JFactory::getLanguage();
// Try the files_joomla file in the current language (without allowing the loading of the file in the default language)
$lang->load('files_joomla.sys', JPATH_SITE, null, false, false)
// Fallback to the files_joomla file in the default language
|| $lang->load('files_joomla.sys', JPATH_SITE, null, true);
class SetNewParams extends JApplicationCli
{
	/**
	 * Entry point for CLI script
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function doExecute()
	{
		// Get a db connection.
        $db = JFactory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);
        
        $query->select($db->quoteName('params'));
        
        $query->from($db->quoteName('#__extensions'));
        
        $query->where($db->quoteName('name') . ' = '. $db->quote('com_config'));
        
        $db->setQuery($query);
        
        //Initial json
        $params = $db->loadResult();
        
        //Array data
        $paramsArray = json_decode($params, true);
        
        
        $filter_tags = &$paramsArray['filters'][7]['filter_tags'];
        
        $new_filter_tags = 'iframe,html,head,body,style,title,meta';
        
        $filter_tags = $new_filter_tags;
        
        //New data
        $new_params_json = json_encode($paramsArray);
        
        //Updates table with new data
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        // Fields to update.
        $fields = array(
            
            $db->quoteName('params') . ' = ' . $db->quote($new_params_json)
        );
        
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('name') . ' LIKE ' . $db->quote('com_config')
            
        );
        
        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        
        $db->setQuery($query);
        
        $result = $db->execute();
        
        //CLI Message not mandatory this can be removed.
        $site_info = new JConfig;
        echo "\033[32m Success! Update for $site_info->sitename is complete. You will need to clear Joomla cache, log out and back in, and reload your browser without cache to view these changes. \033 \n";
	}
}
// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
JApplicationCli::getInstance('SetNewParams')->execute();
