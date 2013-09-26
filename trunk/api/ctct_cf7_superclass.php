<?php

class CTCT_SuperClass extends CTCTUtility {

	function __construct($user = null, $password=null) {
		self::updateSettings($this);
	}

	static public function updateSettings($object = false) {
		if(is_a($object,'CTCTUtility')) {
			$settings = get_option("ctct_cf7");
			$object->setLogin(trim($settings['username']));
	        $object->setPassword(trim($settings['password']));
			$object->setApiPath(str_replace('USERNAME', '', (string)$object->getApiPath()).trim($settings['username']));
			$object->setActionBy('ACTION_BY_CONTACT');
			$object->setRequestLogin($object->getApiKey().'%'.$object->getLogin().':'.$object->getPassword());
		}
	}

	public function CC_List() {
		$ccListOBJ = new CC_List();
		self::updateSettings($ccListOBJ);
		return $ccListOBJ;
	}

	public function CC_Campaign() {
		$CC_Campaign = new CTCTCampaign();
		self::updateSettings($CC_Campaign);
		return $CC_Campaign;
	}

	public function CC_ContactsCollection() {
		$CC_ContactsCollection = new CTCTContactsCollection();
		self::updateSettings($CC_ContactsCollection);
		return $CC_ContactsCollection;
	}

	public function CC_Utility() {
		$CC_Utility = new CTCT_SuperClass();
		self::updateSettings($CC_Utility);
		return $CC_Utility;
	}

	public function CC_Contact($params = array()) {
		$CC_Contact = new CTCTContact($params);
		self::updateSettings($CC_Contact);
		return $CC_Contact;
	}

	static public function CC_ListsCollection() {
		$CC_ListsCollection = new CTCTListsCollection();
		self::updateSettings($CC_ListsCollection);
		return $CC_ListsCollection;
	}

	static public function getAvailableLists() {
		$lists = self::getAllLists();
		foreach ($lists as $key => $list) {
			if(!is_numeric($list['id'])) {
				unset($lists[$key]);
			}
		}
		return $lists;
	}
	public function getContactId(&$Contact) {
		$id = preg_replace('/.*?\/contacts\/(.+)/ism', '$1', $Contact->getId());
		return $id;
	}

	static private function getLists($page = null, $outputLists = array()) {

		$Lists = CTCT_SuperClass::CC_ListsCollection()->getLists($page);

		if(!$Lists || empty($Lists)) { return array(); }

		foreach($Lists[0] as $List) {
			$listid = preg_replace('/.*?\/lists\/(.+)/ism', '$1', $List->getLink());
			$vars = array(
				'link' => $List->getLink(),
				'id' => $listid,
				'name' => $List->getName()
			);

			$outputLists[$listid] = $vars;
		}

		if(isset($Lists[1]['next'])) {
			$page = self::findNextLink($Lists[1]['next']);
		}

		if($page) {
			$outputLists = self::getLists($page, $outputLists);
		}

		return $outputLists;
	}

	static public function getAllLists() {

		$ctct_cf7_alllists = get_transient('ctct_cf7_alllists');

		if($ctct_cf7_alllists && is_array($ctct_cf7_alllists) && !is_wp_error($ctct_cf7_alllists) && (!isset($_GET['cache']) && !isset($_GET['refresh']))) {
			return $ctct_cf7_alllists;
		}


		$outputLists = self::getLists();

		if(!empty($outputLists)) {
			set_transient('ctct_cf7_alllists', $outputLists, 60*60*96);
		}

		return $outputLists;
	}

	public static function findNextLink(&$item){
        $nextLink = $item->xpath("//*[@rel='next']");
        return ($nextLink) ? (string) $nextLink[0]->Attributes()->href : false;
    }

	public function listMergeVars() {
		return array(
			array('tag'=>'email_address', 'req' => true, 'name' => "Email Address", 'placeholder' => '[your-email]'),
			array('tag'=>'full_name', 	  'req' => false, 'name' => "Full Name"),
			array('tag'=>'first_name', 	  'req' => false, 'name' => "First Name"),
			array('tag'=>'middle_name',   'req' => false, 'name' => "Middle Name"),
			array('tag'=>'last_name',	  'req' => false, 'name' => "Last Name"),
			array('tag'=>'job_title', 	  'req' => false, 'name' => "Job Title"),
			array('tag'=>'company_name',  'req' => false, 'name' => "Company Name"),
			array('tag'=>'home_number',   'req' => false, 'name' => "Home Phone"),
			array('tag'=>'work_number',	  'req' => false, 'name' => "Work Phone"),
			array('tag'=>'address_line_1','req' => false, 'name' => "Address 1"),
			array('tag'=>'address_line_2','req' => false, 'name' => "Address 2"),
			array('tag'=>'address_line_3','req' => false, 'name' => "Address 3"),
			array('tag'=>'city_name',	  'req' => false, 'name' => "City"),
			array('tag'=>'state_code',	  'req' => false, 'name' => "State Code"),
			array('tag'=>'state_name',	  'req' => false, 'name' => "State Name"),
			array('tag'=>'country_code',  'req' => false, 'name' => "Country Code"),
			array('tag'=>'country_name',  'req' => false, 'name' => "Country Name"),
			array('tag'=>'zip_code',	  'req' => false, 'name' => "Postal Code"),
			array('tag'=>'sub_zip_code',  'req' => false, 'name' => "Sub Postal Code"),
			array('tag'=>'notes',		  'req' => false, 'name' => "Note"),
			array('tag'=>'mail_type', 	  'req' => false, 'name' => "Email Type (Text or HTML)"),
			array('tag'=>'custom_field_1','req' => false, 'name' => "Custom Field 1 (Up to 50 characters)"),
			array('tag'=>'custom_field_2', 'req' => false, 'name' => "Custom Field 2 (Up to 50 characters)"),
			array('tag'=>'custom_field_3', 'req' => false, 'name' => "Custom Field 3 (Up to 50 characters)"),
			array('tag'=>'custom_field_4', 'req' => false, 'name' => "Custom Field 4 (Up to 50 characters)"),
			array('tag'=>'custom_field_5', 'req' => false, 'name' => "Custom Field 5 (Up to 50 characters)"),
			array('tag'=>'custom_field_6', 'req' => false, 'name' => "Custom Field 6 (Up to 50 characters)"),
			array('tag'=>'custom_field_7', 'req' => false, 'name' => "Custom Field 7 (Up to 50 characters)"),
			array('tag'=>'custom_field_8', 'req' => false, 'name' => "Custom Field 8 (Up to 50 characters)"),
			array('tag'=>'custom_field_9', 'req' => false, 'name' => "Custom Field 9 (Up to 50 characters)"),
			array('tag'=>'custom_field_10','req' => false, 'name' => "Custom Field 10 (Up to 50 characters)"),
			array('tag'=>'custom_field_11','req' => false, 'name' => "Custom Field 11 (Up to 50 characters)"),
			array('tag'=>'custom_field_12','req' => false, 'name' => "Custom Field 12 (Up to 50 characters)"),
			array('tag'=>'custom_field_13','req' => false, 'name' => "Custom Field 13 (Up to 50 characters)"),
			array('tag'=>'custom_field_14','req' => false, 'name' => "Custom Field 14 (Up to 50 characters)"),
			array('tag'=>'custom_field_15','req' => false, 'name' => "Custom Field 15 (Up to 50 characters)"),
		);
	}

}