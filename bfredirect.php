<?php
/**
 * @package   System plugin for redirection with message
 * @version   0.0.1
 * @author    https://www.brainforge.co.uk
 * @copyright Copyright (C) 2020 Jonathan Brain. All rights reserved.
 * @license   GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

class plgSystemBfredirect extends CMSPlugin {
	/**
	 * Constructor
	 *
	 * @param object  &$subject The object to observe
	 * @param array $config An optional associative array of configuration settings.
	 *                      Recognized key values include 'name', 'group', 'params', 'language'
	 *                      (this list is not meant to be comprehensive).
	 *
	 * @since   11.1
	 */
	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
	}

	/**
	 */
	public function onAfterInitialise()
	{
		$app = Factory::getApplication();
		if ($app->isClient('administrator'))
		{
			return;
		}

		$uri = Uri::getInstance();
		$base = $uri->base(true);
		$path = explode('/', trim(substr($uri->getPath(), strlen($base)), '/'));
		if (count($path) == 2 && $path[0] == $this->params->get('name', 'plg_system_bfredirect'))
		{
			$redirect = $this->params->get('redirect');
			if (!empty($redirect))
			{
				foreach((array) $redirect as $redirectWithMessage)
				{
					if (empty($redirectWithMessage->state) || $redirectWithMessage->label != $path[1])
					{
						continue;
					}

					if (!empty($redirectWithMessage->messagetype))
					{
						$app->enqueueMessage(Text::_($redirectWithMessage->message), $redirectWithMessage->messagetype);
					}

					$app->redirect($base . '/' . ltrim($redirectWithMessage->target, '/'));
				}
			}
		}

	}
}
