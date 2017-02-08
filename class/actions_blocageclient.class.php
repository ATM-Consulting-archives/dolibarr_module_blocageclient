<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_blocageclient.class.php
 * \ingroup blocageclient
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class Actionsblocageclient
 */
class Actionsblocageclient
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function doActions($parameters, &$object, &$action, $hookmanager)
	{
		$error = 0; // Error counter
		$myvalue = 'test'; // A result value

		if (in_array('somecontext', explode(':', $parameters['context'])))
		{
		  // do something only for the context 'somecontext'
		}

		if (! $error)
		{
			$this->results = array('myreturn' => $myvalue);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		}
		else
		{
			$this->errors[] = 'Error message';
			return -1;
		}
	}

	function formObjectOptions($parameters, &$object, &$action, $hookmanager) {
		
		global $db, $conf, $langs;
		
		$langs->load('blocageclient@blocageclient');
		
		define('INC_FROM_DOLIBARR', true);
		
		dol_include_once('/blocageclient/lib/blocageclient.lib.php');
		
		if(get_class($object) === 'Societe' || get_class($object) === 'Client') $soc = &$object;
		elseif(!empty($object->thirdparty)) $soc = &$object->thirdparty;
		else return 0;
		
		$client_bloque = !empty($soc->array_options['options_blocage_client']);
		
		if($client_bloque) {
		
			if($parameters['currentcontext'] === 'commcard') {
				
				if(!empty($conf->global->BLOCAGE_CLIENT_ON_CUSTOMER_ORDER)) hideElement('/htdocs/commande/card.php');
				
			} elseif($parameters['currentcontext'] === 'ordercard') {
				
				if(!empty($conf->global->BLOCAGE_CLIENT_ON_SHIPPING)) hideElement('/htdocs/expedition/shipment.php?id=');
				
				if(!empty($conf->global->BLOCAGE_CLIENT_ON_CUSTOMER_ORDER)) hideElement('&action=validate');
				
			} elseif($parameters['currentcontext'] === 'expeditioncard' && !empty($conf->global->BLOCAGE_CLIENT_ON_SHIPPING)) {
				
				hideElement('&action=valid');
				
			}
			
			setEventMessage($langs->trans('blocageclientBlockedCustomer'), 'warnings');
			
		}
		
	}

}