<?php
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

class plgCommunityDocman extends CApplications {
	var $name = "docman";
	var $_name = 'docman';

	function plgCommunityDocman(&$subject, $config) {
		parent::__construct($subject, $config);
		FB::log('plgCommunityDocman');

	}

/**
  *  This is fired every time someone joins a group
  *
  * @todo 
  * @example $this->OnGroupJoin($group_id, $memberid)   ;
  * @param 
  * @since 0.1
  * @return Object
  */
	function OnGroupJoin($group, $memberid) {
		FB::log('OnGroupJoin');
		$this -> execute($group -> id, $memberid);

	}
/**
  *  On the frontend of Jom Social, users can request joining a group, if they do so they are not added to the docman group until  they are approved by a jom_social admin
  *
  * @todo 
  * @example $this->onGroupJoinApproved($group_id, $memberid)   ;
  * @param 
  * @since 0.1
  * @return Object
  */

	function onGroupJoinApproved($group, $memberid) {
		$this -> execute($group -> id, $memberid);

	}


/**
  *  this is function that calls the other functions for sync.  the plugin events  luanch this function.
  *
  * @todo 
  * @example $this->execute($group_id, $memberid)   ;
  * @param 
  * @since 0.1
  * @return Object
  */

	function execute($group_id, $memberid) {
		//TODO make this some kinda table, or XML  or something but for now lets just hardcode it
		//$key == Jomsocial group ID  //$value == DocManGroup ID
		$groupsXref = array();
		$groupsXref['1'] = '2'; 
		$groupsXref['2'] = '3'; 
		$groupsXref['3'] = '4';
		$groupsXref['4'] = '5'; 
		$groupsXref['5'] = '6'; 
		$groupsXref['6'] = '7'; 
		$groupsXref['7'] = '8'; 
		$groupsXref['8'] = '9'; 
		$groupsXref['9'] = '10'; 
		$groupsXref['10'] = '11'; 

		$members = $this -> getMembersOfGroup($group_id);
		$this -> updateDocmanGroupMembers($groupsXref[$group_id], $members);
	}

/**
  *  gets the groups from jomsocials
  *
  * @todo 
  * @example $this->getMembersOfGroup($group_id)  ;
  * @param 
  * @since 0.1
  * @return Object
  */	

	function getMembersOfGroup($group_id) {

		$db = JFactory::getDbo();

		$query = $db -> getQuery(true);
		$query -> select(array('memberid'));
		$query -> from('#__community_groups_members');
		$query -> where('groupid = ' . $db -> quote($group_id));
		$query -> where("approved = '1'");

		$db -> setQuery($query);

		// Load the results as a list of stdClass objects.
		$members = $db -> loadResultArray();

		return $members;
	}

/**
  *  Selects the docman groups fields, that is a CSV string, and explodes it into an array and returns it
  *
  * @todo 
  * @example $this->mergeArrays($current, $new) ;
  * @param 
  * @since 0.1
  * @return Object
  */	

	function getDocmanGroupMembers($group_id) {

		$db = JFactory::getDbo();

		$query = $db -> getQuery(true);

		$query -> select('groups_members');
		$query -> from('#__docman_groups');
		$query -> where('groups_id = ' . $db -> quote($group_id));

		$db -> setQuery($query);

		$result = $db -> result();
	
		$result = explode(',', $result);
		return $result;

	}

/**
  *  We combine the the arrays by they values into the ids, that way we  combine our duplicates,  than we remove any null, or blank keys
  *
  * @todo 
  * @example $this->mergeArrays($current, $new) ;
  * @param 
  * @since 0.1
  * @return Object
  */

	function mergeArrays($current, $new) {
		//array_merge()  doesn't work for some reason

		$array = array();
		foreach ($new as $id) {
			$array[$id] = $id;
		}
		foreach ($current as $id) {
			$array[$id] = $id;
		}

		$array = array_filter($array, 'strlen');
		$array = array_filter($array);
		return $array;

	}


/**
  *  Updates the docman groups, from the current values plus the new values 
  *
  * @todo 
  * @example $this->updateDocmanGroupMembers($group_id, $members) ;
  * @param 
  * @since 0.1
  * @return Object
  */

	function updateDocmanGroupMembers($group_id, $members) {

		$currentMembers = $this -> getDocmanGroupMembers($group_id);

		$members = $this -> mergeArrays($currentMembers, $members);

		$comma_separated_members = implode(",", $members);

		$db = JFactory::getDbo();

		$query = $db -> getQuery(true);

		$query -> update('#__docman_groups');
		$query -> set('groups_members=' . $db -> quote($comma_separated_members));
		$query -> where('groups_id = ' . $db -> quote($group_id));

		$db -> setQuery($query);

		$result = $db -> query();

		return $result;

	}

}
