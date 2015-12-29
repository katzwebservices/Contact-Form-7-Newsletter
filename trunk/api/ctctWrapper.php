<?php
/**
 * ctctWrapper.php
 *
 * Constant Contact PHP Wrapper, includes access to:
 * 1. Contacts Collection - add, delete, update, and get contacts
 * 2. Campaign Collection - add, delete, update, and get campaigns
 * 3. List Collection - add, delete, update, and get lists
 * 4. Event Collection - get events and registrants
 * 5. Activities Collection - add, and get activities
 * 6. Library Collection - add and get images and folders
 *
 * @name ctctWrapper.php
 * @author Constant Contact API Support Team <webservices@constantcontact.com>
 * @version 1.0
 * @link http://developer.constantcontact.com
 * @package ctctWrapper
 *
 */
	/**
	 * Utility Class for all HTTP calls
	 *
	 * This class is used for all the HTTP calls made: GET, PUT, POST, and DELETE
	 * Also used to make Bulk URL encoded calls and Multi Part Form calls
	 *
	 */
	class CTCTUtility
	{
		/**
		 * Public Function __construct of Utility Object
		 *
		 * Sets variables for username, password, and API key, as well as
		 * constructs the login request string: APIKey%Username:Password
		 *
		 * Also sets ActionBy settings:
		 * - ACTION_BY_CUSTOMER will add contacts as if they were being added by Site Owner.
		 * - ACTION_BY_CONTACT will add contacts as if they were added by themselves
		 *
		 */
		public function __construct()
		{
			$this->setActionBy('ACTION_BY_CONTACT');

			/* DO NOT CHANGE! It will break the plugin if changed. */
			$this->setApiKey('fux9by9kmr9h4t8sd6hnmefc');
			$this->setApiPath('https://api.constantcontact.com');
			$this->setLogin(CTCTCF7::get_username());
			$this->setPassword(CTCTCF7::get_password());
			$this->setRequestLogin($this->getApiKey() . "%" . $this->getLogin() . ":" . $this->getPassword());
        }



		private $actionBy;
		private $apiKey;
		private $apiPath;
		private $login;
		private $password;
		private $requestLogin;

		//Getters and Setters for the Utility class
		public function setActionBy($value) { $this->actionBy = $value; }
		public function getActionBy() { return $this->actionBy; }

		public function setApiKey($value) { $this->apiKey = $value; }
		public function getApiKey() { return $this->apiKey; }

		public function setApiPath($value) { $this->apiPath = $value; }
		public function getApiPath() { return $this->apiPath; }

		public function setLogin($value) { $this->login = $value; }
		public function getLogin() { return $this->login; }

		public function setPassword($value) { $this->password = $value; }
		public function getPassword() { return $this->password; }

		public function setRequestLogin($value) { $this->requestLogin = $value; }
		public function getRequestLogin() { return $this->requestLogin; }

		/**
		* Public function httpGet sends a GET request to the API server
		*
		* @param string $request - valid constant contact URI
		* @return string $getRequest - returns an array from httpRequest with the API error and success messaging and codes
		*/
		public function httpGet($request)
		{
			$getRequest = $this->httpRequest('receive', 'GET', $request, '');
			return $getRequest;
		}
		/**
		* Public function httpPut sends a PUT request to the API server
		*
		* @param string $request - valid constant contact URI
		* @param string $paramter - data passed to the URI
		* @return string $getRequest - returns an array from httpRequest with the API error and success messaging and codes
		*/
		public function httpPut($request, $parameter)
		{
			$putRequest = $this->httpRequest('send', 'PUT', $request, $parameter);
			return $putRequest;
		}
		/**
		* Public function httpPost sends a POST request to the API server
		*
		* @param string $request - valid constant contact URI
		* @param string $paramter - data passed to the URI
		* @return string $getRequest - returns an array from httpRequest with the API error and success messaging and codes
		*/
		public function httpPost($request, $parameter)
		{
			$postRequest = $this->httpRequest('send', 'POST', $request, $parameter);
			return $postRequest;
		}
		/**
		* Public function httpDelete sends a DELETE request to the API server
		*
		* @param string $request - valid constant contact URI
		* @return string $getRequest - returns an array from httpRequest with the API error and success messaging and codes
		*/
		public function httpDelete($request)
		{
			$deleteRequest = $this->httpRequest('receive', 'DELETE', $request, '');
			return $deleteRequest;
		}
		/**
		* Public function urlEncodedPost sends a POST request to the API server for URL encoded requests
		*
		* @param string $request - valid constant contact URI
		* @param string $paramter - data passed to the URI
		* @return string $getRequest - returns an array from httpRequest with the API error and success messaging and codes
		*/
		public function urlEncodedPost($request, $parameter)
		{
			$encodeRequest = $this->httpRequest('urlEncode', 'POST', $request, $parameter);
			return $encodeRequest;
		}
		/**
		* Public function multiPartPost sends a POST request to the API server for multipart/form data requests
		*
		* @param string $request - valid constant contact URI
		* @param string $paramter - data passed to the URI
		* @return string $getRequest - returns an array from httpRequest with the API error and success messaging and codes
		*/
		public function multiPartPost($request, $parameter)
		{
			$multiPartRequest = $this->httpRequest('multiPart', 'POST', $request, $parameter);
			return $multiPartRequest;
		}
		/**
		* Private function httpRequest sends requests to the API server
		*
		* @param string $info - Type of request, multipart, urlEncode, receive, or send
		* @param string $type - Server call, POST, GET, PUT, DELETE
		* @param string $request - valid constant contact URI
		* @param string $parameter - data passed to the URII
		* @return string $return - returns an array from httpRequest with the API error and success messaging and codes
		*/
		private function httpRequest($info, $type, $request, $parameter)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $this->getRequestLogin());
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			if ($info == 'urlEncode')
			{
				curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/x-www-form-urlencoded", 'Content-Length: ' . strlen($parameter)));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
			}
			else if ($info == 'multiPart')
			{
				curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:multipart/form-data"));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
			}
			else
			{
				curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/atom+xml", "accept:application/atom+xml", 'Content-Length: ' . strlen($parameter)));
			}

			curl_setopt($ch, CURLOPT_FAILONERROR, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

			if ($info == 'send')
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
			}

			$return['xml'] = curl_exec($ch);
			$return['info'] = curl_getinfo($ch);
			$return['error'] = curl_error($ch);

			return $return;
		}
	}
	/**
	 * Activities Collection Class for Activitiy API calls
	 *
	 * Includes functions for listing all activities within the account, specific activity details,
	 * creating a bulk import with Multipart/form data and bulk url encoded calls, also bulk export
	 *
	 */
	class CTCTActivitiesCollection
	{
		/**
		* Public function that Gets a list of first 50 Activities in the account
		*
		* @return array $allActivities - array of two arrays, array 1 is activity objects, array 2 is link for next 50 activities
		*/
		public function listActivities()
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/activities');
			$allActivities = array();

			$activityList = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml']);

			foreach ($parsedReturn->entry as $item)
			{
				$activity = new CTCTActivity();
				$activity->setLink($item->link['href']);
				$activity->setId($item->id);
				$activity->setActivityTitle($item->content->title);
				$activity->setType($item->content->Activity->Type);
				$activity->setStatus($item->content->Activity->Status);
				$activity->setTransactionCount($item->content->Activity->TransactionCount);
				$activity->setErrorCount($item->content->Activity->Errors);
				$activity->setRunStartTime($item->content->Activity->RunStartTime);
				$activity->setRunFinishTime($item->content->Activity->RunFinishTime);
				$activity->setInsertTime($item->content->Activity->InsertTime);

				$activityList[] = $activity;
			}

			if ($parsedReturn->link[2])
			{
				$pages[] = $parsedReturn->link[2]->Attributes()->href;
			}

			$allActivities = array($activityList, $pages);

			return $allActivities;
		}
		/**
		* Public function that gets full details of a specific activity, from an activity object.
		*
		* @param object $activity - a valid activity object with valid activity link
		* @return object $activity with full details of the passed activity object
		*/
		public function listActivityDetails($activity)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $activity->getLink();
			$return = $utility->httpGet($call);
			$activity = $this->createActivityStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $activity;
			}
		}

		/**
		* Public function POSTs a URL encoded string to the activities collection, for bulk importing and exporting.
		*
		* @param string $urlEncodedPost - URL encoded string that follows this format:
		*   - activityType=SV_ADD&data=Email+Address%2CFirst+Name%2CLast+Name%0Awstest3%40example.com%2C+Fred%2C+Test%0Awstest4%40example.com%2C+Joan%2C+Test%0Awstest5%40example.com%2C+Ann%2C+Test&lists=http%3A%2F%2Fapi.constantcontact.com%2Fws%2Fcustomers%2Fjoesflowers%2Flists%2F2&lists=http%3A%2F%2Fapi.constantcontact.com%2Fws%2Fcustomers%2Fjoesflowers%2Flists%2F5
		* @return string - Success or Fail code from the API server
		*/
		public function bulkUrlEncoded($urlEncodedPost)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/' . $utility->getLogin() .'/activities';
			$return = $utility->urlEncodedPost($call, $urlEncodedPost);
			$code = $return['info']['http_code'];
			return $code;
		}
		//
		/**
		* Public function that sends a POST request that creates an Export Contact Activity, returns exported activity
		*
		* @param string $filetype - either CSV or TXT file to upload
		* @param object $list - valid list object
		* @param string $exportOptDate - true to include the Add/Remove Date in the export file, false to not include it
		* @param string $exportOptSource - true to include the Added/Removed By (source of add or remove) in the export file, false to not include it
		* @param string exportListName - true to include the List Name in the export file, false to not include it
		* @param string sortBy - EMAIL_ADDRESS to sort the list by email address in ascending order. DATE_DESC will sort the contacts by the Date in descending order
		* @return object $activity with full details of the created activity object
		*/
		public function exportContacts($filetype, $list, $exportOptDate, $exportOptSource, $exportListName, $sortBy, $columns = array())
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/' . $utility->getLogin() .'/activities';

			foreach ($columns as $item)
			{
				$allColumns .= '&columns=' . urlencode($item);
			}

			$urlEncodedPost = "activityType=EXPORT_CONTACTS&fileType=" . $filetype . "&exportOptDate=" . $exportOptDate . "&exportOptSource=" . $exportOptSource . "&exportListName=" . $exportListName . "&sortBy=" . $sortBy . $allColumns . "&listId=" . urlencode($list->getId());
			$activityXml = $utility->urlEncodedPost($call, $urlEncodedPost);
			$activity = $this->createActivityStruct($activityXml);

			return $activity;
		}
		/**
		* Public function that passes @fileArray to the activities collection
		* as a mutlipart/form data request
		*
		* @param array $fileArray - array of dataFile, activityType and lists
		* @return string - Success or Fail code from the API server
		*/
		public function multiPart($fileArray)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/' . $utility->getLogin() .'/activities';
			$return = $utility->multiPartPost($call, $fileArray);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Private function that creates the structure for an Activity Object
		*
		* @param string $activityXml - the returned XML from an activity call as a string
		* @return object $activityObj - valid activity object
		*/
		private function createActivityStruct($activityXml)
		{
			$activity = array();
			$parsedReturn = simplexml_load_string($activityXml);
			$activity['link'] = ($parsedReturn->link->Attributes()->href);
			$activity['id'] = ($parsedReturn->id);
			$activity['activity_title'] = ($parsedReturn->title);
			$activity['updated'] = ($parsedReturn->updated);
			$activity['type'] = ($parsedReturn->content->Activity->Type);
			$activity['status'] = ($parsedReturn->content->Activity->Status);
			$activity['transaction_count'] = ($parsedReturn->content->Activity->TransactionCount);
			$activity['run_start_time'] = ($parsedReturn->content->Activity->RunStartTime);
			$activity['run_finish_time'] = ($parsedReturn->content->Activity->RunFinishTime);
			$activity['file_name'] = ($parsedReturn->content->Activity->FileName);

			if ($parsedReturn->content->Contact->ContactLists->ContactList)
			{
				foreach ($parsedReturn->content->Activity->Errors->Error as $item)
				{
					$activity['errors'][$item]['LineNumber'] = (trim((string) $item->LineNumber));
					$activity['errors'][$item]['EmailAddress'] = (trim((string) $item->EmailAddress));
					$activity['errors'][$item]['Message'] = (trim((string) $item->Message));
				}
			}

			$activityObj = new CTCTActivity($activity);
			return $activityObj;
		}
	}
	/**
	 * Activity class defines an activity object
	 *
	 * Defines an activity object, includes all activity variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTActivity
	{
		/**
		* Construct function for the Activity Class
		*
		* @param array $params - an array of variables that set up an activity object
		* @return object - activity object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setLink(@$params['link']);
			$this->setId(@$params['id']);
			$this->setUpdated(@$params['updated']);
			$this->setType(@$params['type']);
			$this->setActivityTitle(@$params['activity_title']);
			$this->setStatus(@$params['status']);
			$this->setTransactionCount(@$params['transaction_count']);
			$this->setRunStartTime(@$params['run_start_time']);
			$this->setRunFinishTime(@$params['run_finish_time']);
			$this->setFileName(@$params['file_name']);
			$this->setErrorCount(@$params['error_count']);
			if (@$params['errors'])
			{
				foreach ($params['errors'] as $tmp)
				{
					$this->setErrors($tmp);
				}
			}

			return $this;
		}

		private $link;
		private $id;
		private $updated;
		private $type;
		private $activityTitle;
		private $status;
		private $transactionCount;
		private $runStartTime;
		private $runFinishTime;
		private $insertTime;
		private $fileName;
		private $errorCount;
		private $errors = array();

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setId( $value ) { $this->id = $value; }
		public function getId() { return $this->id; }

		public function setUpdated( $value ) { $this->updated = $value; }
		public function getUpdated() { return $this->updated; }

		public function setType( $value ) { $this->type = $value; }
		public function getType() { return $this->type; }

		public function setActivityTitle( $value ) { $this->activityTitle = $value; }
		public function getActivityTitle() { return $this->activityTitle; }

		public function setStatus( $value ) { $this->status = $value; }
		public function getStatus() { return $this->status; }

		public function setTransactionCount( $value ) { $this->transactionCount = $value; }
		public function getTransactionCount() { return $this->transactionCount; }

		public function setRunStartTime( $value ) { $this->runStartTime = $value; }
		public function getRunStartTime() { return $this->runStartTime; }

		public function setRunFinishTime( $value ) { $this->runFinishTime = $value; }
		public function getRunFinishTime() { return $this->runFinishTime; }

		public function setInsertTime( $value ) { $this->insertTime = $value; }
		public function getInsertTime() { return $this->insertTime; }

		public function setFileName( $value ) { $this->fileName = $value; }
		public function getFileName() { return $this->fileName; }

		public function setErrorCount( $value ) { $this->errorCount = $value; }
		public function getErrorCount() { return $this->errorCount; }

		public function setErrors( $value ) { $this->errors[] = $value; }
		public function getErrors() { return $this->errors; }
	}
	/**
	 * Library Collection Class for calls to the library collection API
	 *
	 * Includes functions to create folders, list folders, list images and delete images and folders
	 *
	 */
	class CTCTLibraryCollection
	{
		/**
		* Public function that gets a list of first 50 folders in the account
		*
		* @return array $allFolders - an array of two arrays, array 1 is folder objects, array 2 is link for next 50 folders
		*/
		public function listFolders()
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/library/folders');
			$allFolders = array();

			$folderList = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml'], null, null, "http://www.w3.org/2005/Atom");

			foreach ($parsedReturn->entry as $item)
			{
				$folder = new CTCTFolder();
				$folder->setFolderLink($item->link->Attributes()->href);
				$folder->setFolderId($item->id);
				$folder->setFolderName($item->title);
				$folderList[] = $folder;
			}

			if ($parsedReturn->link[2])
			{
				$pages[] = $parsedReturn->link[2]->Attributes()->href;
			}

			$allFolders = array($folderList, $pages);

			return $allFolders;
		}
		/**
		* Public function that does a POST to the Library collection, passing a folder object
		*
		* @param object $folder - a valid folder object with all required fields
		* @return string $code - returns success or fail code from API server
		*/
		public function createFolder($folder)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/library/folders';
			$folderStruct = $this->createFolderXml($folder);
			$return = $utility->httpPost($call, $folderStruct);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Public function that gets a list of first 50 images in the account
		*
		* @return array $allImages - an array of two arrays, array 1 is image objects, array 2 is link for next 50 images
		*/
		public function listImages($folder)
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . $folder->getFolderLink() . '/images');
			$allImages = array();
			$imageArray = array();
			$imageList = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml'], null, null, "http://www.w3.org/2005/Atom");

			foreach ($parsedReturn->entry as $item)
			{
				$imageArray['image_link'] = $item->link->Attributes()->href;
				$imageArray['file_name'] = $item->title;
				$imageArray['last_updated']  = $item->updated;
				$imageArray['file_type'] = $item->content->Image->FileType;
				$image = new CTCTImage($imageArray);
				$imageList[] = $image;
			}

			if ($parsedReturn->link[2])
			{
				$pages[] = $parsedReturn->link[2]->Attributes()->href;
			}

			$allImages = array($imageList, $pages);

			return $allImages;
		}
		/**
		* Public function that gets image details of a single image object passed to it.
		*
		* @param object $image - a valid image object with a valid image link
		* @return object $image - returns image object with full details
		*/
		public function listImageDetails($image)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $image->getLink();
			$return = $utility->httpGet($call);
			$imageStruct = $this->createImageStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $imageStruct;
			}
		}
		/**
		* Public function that deletes an image from the account
		*
		* @param object $image - a valid image object with a valid image link
		* @return string $code - returns success or fail code from API server
		*/
		public function deleteImage($image)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $image->getLink();
			$return = $utility->httpDelete($call);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Public function that removes every image from a specific folder
		*
		* @param object $folder - a valid folder object with a valid folder link
		* @return string $code - returns success or fail code from API server
		*/
		public function clearFolder($folder)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $folder->getFolderLink() . '/images';
			$return = $utility->httpDelete($call);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Private function that creates folder XML
		*
		* @param object $folder - a valid folder object with all required fields
		* @return string $xmlReturn - valid XML of a folder
		*/
		private function createFolderXml($folder)
		{
			$utility = new CTCTUtility();
			$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><atom:entry xmlns:atom='http://www.w3.org/2005/Atom'/>");
			$content = $xml->addChild("content");
			$folderNode = $content->addChild("Folder", "", "");
			$folderNode->addChild("Name", $folder->getFolderName());
			$xmlReturn = $xml->asXML();
			return $xmlReturn;
		}
		/**
		* Private function that creates a folder object from XML
		*
		* @param string $folderXml - Valid folder XML
		* @return object $folderStruct - returns a valid folder object
		*/
		private function createFolderStruct($folderXml)
		{
			$folder = array();
			$parsedReturn = simplexml_load_string($folderXml, null, null, "http://www.w3.org/2005/Atom");
			$folder['folderLink'] = ($parsedReturn->link);
			$folder['folderId'] = ($parsedReturn->id);
			$folder['folderName'] = ($parsedReturn->content->Folder->Name);
			$folderStruct =  ($folder);
			return $folderStruct;
		}
		/**
		* Private function that creates an image object from XML
		*
		* @param string $imageXml - Valid image XML
		* @return object $imageStruct - returns a valid image object
		*/
		private function createImageStruct($imageXml)
		{
			$image = array();
			$parsedReturn = simplexml_load_string($imageXml);
			$parsedArray = $parsedReturn->xpath('atom:content/Image');
			$image['image_link'] = ("");
			$image['file_name'] = ($parsedArray[0]->FileName);
			$image['image_url'] = ($parsedArray[0]->ImageURL);
			$image['image_height'] = ($parsedArray[0]->Height);
			$image['image_width'] = ($parsedArray[0]->Width);
			$image['description'] = ($parsedArray[0]->Description);
			$image['MD5Hash'] = ($parsedArray[0]->MD5Hash);
			$image['file_size'] = ($parsedArray[0]->FileSize);
			$image['last_updated'] = ($parsedArray[0]->LastUpdated);
			$image['file_type'] = ($parsedArray[0]->FileType);

			if ($parsedArray[0]->ImageUsages)
			{
				foreach ($parsedArray[0]->ImageUsages->ImageUsage as $item)
				{
					$image['image_usage'][] = (trim((string) $item->Link->href));
				}
			}

			$imageStruct = new CTCTImage($image);
			return $imageStruct;

		}

	}
	/**
	 * Folder class defines a folder object
	 *
	 * Defines a folder object, includes all campaign variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTFolder
	{
		/**
		* Construct function for the Folder Class
		*
		* @param array $params - an array of variables that set up a folder object
		* @return object folder object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setFolderLink(@$params['folderLink']);
			$this->setFolderId(@$params['folderId']);
			$this->setFolderName(@$params['folderName']);

			return $this;
		}

		private $folderName;
		private $folderId;
		private $folderLink;

		public function setFolderName( $value ) { $this->folderName = $value; }
		public function getFolderName() { return $this->folderName; }

		public function setFolderId( $value ) { $this->folderId = $value; }
		public function getFolderId() { return $this->folderId; }

		public function setFolderLink( $value ) { $this->folderLink = $value; }
		public function getFolderLink() { return $this->folderLink; }
	}
	/**
	 * Image class defines an Image object
	 *
	 * Defines an Image object, includes all campaign variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTImage
	{
		/**
		* Construct function for the Image Class
		*
		* @param array $params - an array of variables that set up an image object
		* @return object image object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setLink(@$params['image_link']);
			$this->setFileName(@$params['file_name']);
			$this->setImageUrl(@$params['image_url']);
			$this->setHeight(@$params['image_height']);
			$this->setWidth(@$params['image_width']);
			$this->setDescription(@$params['description']);
			$this->setMd5Hash(@$params['MD5Hash']);
			$this->setFileSize(@$params['file_size']);
			$this->setUpdated(@$params['last_updated']);
			$this->setFileType(@$params['file_type']);

			if (@$params['image_usage'])
			{
				foreach ($params['image_usage'] as $tmp)
				{
					$this->setImageUsage($tmp);
				}
			}

			return $this;
		}

		private $link;
		private $fileName;
		private $fileType;
		private $imageUrl;
		private $height;
		private $width;
		private $description;
		private $fileSize;
		private $updated;
		private $md5Hash;
		private $imageUsage = array();

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setFileName( $value ) { $this->fileName = $value; }
		public function getFileName() { return $this->fileName; }

		public function setFileType( $value ) { $this->fileType = $value; }
		public function getFileType() { return $this->fileType; }

		public function setImageUrl( $value ) { $this->imageUrl = $value; }
		public function getImageUrl() { return $this->imageUrl; }

		public function setHeight( $value ) { $this->height = $value; }
		public function getHeight() { return $this->height; }

		public function setWidth( $value ) { $this->width = $value; }
		public function getWidth() { return $this->width; }

		public function setDescription( $value ) { $this->description = $value; }
		public function getDescription() { return $this->description; }

		public function setFileSize( $value ) { $this->fileSize = $value; }
		public function getFileSize() { return $this->fileSize; }

		public function setUpdated( $value ) { $this->updated = $value; }
		public function getUpdated() { return $this->updated; }

		public function setMd5Hash( $value ) { $this->md5Hash = $value; }
		public function getMd5Hash() { return $this->md5Hash; }

		public function setImageUsage( $value ) { $this->imageUsage[] = $value; }
		public function getImageUsage() { return $this->imageUsage; }
	}
	/**
	 * Contacts Collection Class for calls to the contact collection API
	 *
	 * Includes functions for listing all contacts within the account, specific contact details,
	 * also creating, removing, and sending contacts to do not mail.
	 *
	 */
	class CTCTContactsCollection
	{
		/**
		* Public function that does a POST to the Contacts collection, passing a contact object
		*
		* @param object $contact - a valid contact object with all required fields
		* @return string $code - returns success or fail code from API server
		*/
		public function createContact($contact, $return_code = true)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/contacts';
			$contactStruct = $this->createContactXml(null, $contact);
			$return = $utility->httpPost($call, $contactStruct);
			if($return_code) {
				$return = $return['info']['http_code'];
			}
			return $return;
		}
		/**
		* Public function that deletes a contact, sending the contact to the Do Not Mail List
		*
		* @param object $contact - a valid contact object with a valid contact link
		* @return string $code - returns success or fail code from API server
		*/
		public function deleteContact($contact)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $contact->getLink();
			$return = $utility->httpDelete($call);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Public function that gets contact details of a single contact object passed to it.
		*
		* @param object $contact - a valid contact object with a valid contact link
		* @return object $contact - returns contact object with full details
		*/
		public function listContactDetails($contact)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $contact->getLink();
			$return = $utility->httpGet($call);
			$contact = $this->createContactStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $contact;
			}
		}
		/**
		* Public function that gets a list of first 50 contacts in the account
		*
		* @return array $allContacts - an array of two arrays, array 1 is contact objects, array 2 is link for next 50 contacts
		*/
		public function listContacts()
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/contacts');
			$allContacts = array();

			$contactList = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml']);

			foreach ($parsedReturn->entry as $item)
			{
				$contact = new CTCTContact();
				$contact->setLink($item->link['href']);
				$contact->setId($item->id);
				$contact->setEmailAddress($item->content->Contact->EmailAddress);
				$contact->setFullName($item->content->Contact->Name);
				$contact->setStatus($item->content->Contact->Status);
				$contact->setEmailType($item->Contact->EmailType);
				$contactList[] = $contact;
			}

			if ($parsedReturn->link[2])
			{
				$pages[] = $parsedReturn->link[2]->Attributes()->href;
			}

			$allContacts = array($contactList, $pages);

			return $allContacts;
		}
		/**
		* Public function that gets a list of first 50 contact events in the account of a specific contact event type
		*
		* @param object $contact - valid contact object with valid contact link
		* @param string $type - type must be opens, clicks, sends, optOuts, bounces, or forwards.
		* @return object $contact - returns contact object with fields set with array of the events that were requested
		*/
		public function listContactEvents($contact, $type)
		{
			$utility = new CTCTUtility();
			switch ($type)
			{
				case 'opens':
					$call = $utility->httpGet($utility->getApiPath() . $contact->getLink() . "/events/opens");
					$parsedReturn = simplexml_load_string($call['xml']);

					foreach ($parsedReturn->entry as $item)
					{
						$contact->setOpens(array("CampaignLink" => $item->content->OpenEvent->Campaign->link->Attributes()->href, "EventTime" => $item->content->OpenEvent->EventTime));
					}

					break;
				case 'clicks':
					$call = $utility->httpGet($utility->getApiPath() . $contact->getLink() . "/events/clicks");
					$parsedReturn = simplexml_load_string($call['xml']);

					foreach ($parsedReturn->entry as $item)
					{
						$contact->setClicks(array("CampaignLink" => $item->content->ClickEvent->Campaign->link->Attributes()->href, "EventTime" => $item->content->ClickEvent->EventTime, "LinkUrl" => $item->content->ClickEvent->LinkUrl));
					}

					break;
				case 'bounces':
					$call = $utility->httpGet($utility->getApiPath() . $contact->getLink() . "/events/bounces");
					$parsedReturn = simplexml_load_string($call['xml']);

					foreach ($parsedReturn->entry as $item)
					{
						$contact->setBounces(array("CampaignLink" => $item->content->BounceEvent->Campaign->link->Attributes()->href, "EventTime" => $item->content->BounceEvent->EventTime, "Code" => $item->content->BounceEvent->Code, "Description" => $item->content->BounceEvent->Description));
					}

					break;
				case 'optOuts':
					$call = $utility->httpGet($utility->getApiPath() . $contact->getLink() . "/events/optouts");
					$parsedReturn = simplexml_load_string($call['xml']);

					foreach ($parsedReturn->entry as $item)
					{
						$contact->setOptOuts(array("CampaignLink" => $item->content->OptoutEvent->Campaign->link->Attributes()->href, "EventTime" => $item->content->OptoutEvent->EventTime, "OptOutSource" => $item->content->OptoutEvent->OptOutSource, "OptOutReason" => $item->content->OptoutEvent->OptOutReason));
					}
					break;

				case 'forwards':
					$call = $utility->httpGet($utility->getApiPath() . $contact->getLink() . "/events/forwards");
					$parsedReturn = simplexml_load_string($call['xml']);

					foreach ($parsedReturn->entry as $item)
					{
						$contact->setForwards(array("CampaignLink" => $item->content->ForwardEvent->Campaign->link->Attributes()->href, "EventTime" => $item->content->ForwardEvent->EventTime));
					}

					break;
				case 'sends':
					$call = $utility->httpGet($utility->getApiPath() . $contact->getLink() . "/events/sends");
					$parsedReturn = simplexml_load_string($call['xml']);

					foreach ($parsedReturn->entry as $item)
					{
						$contact->setSends(array("CampaignLink" => $item->content->SentEvent->Campaign->link->Attributes()->href, "EventTime" => $item->content->SentEvent->EventTime));
					}

					break;
			}
			return $contact;
		}
		/**
		* Public function that removes a contact from all lists in the account
		*
		* @param object $contact - a valid contact object with a valid contact link
		* @return string $code - returns success or fail code from API server
		*/
		public function removeContact($contact)
		{
			$utility = new CTCTUtility();
			$existingContact = $this->listContactDetails($contact);
			$existingContact->removeLists();

			$contactXml = $this->createContactXml($existingContact->getId(), $existingContact);
			$return = $utility->httpPut($utility->getApiPath() . $existingContact->getLink(), $contactXml);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Public function that searches for a contact based on their email address
		*
		* @param string $emailAddress - valid email address
		* @return array $searchContacts - an array of two arrays, array 1 is search results, array 2 is link for next 50 contacts
		*/
		public function searchByEmail($emailAddress)
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/contacts?email=' . urlencode($emailAddress));
			$parsedReturn = simplexml_load_string($return['xml']);

			if (!$parsedReturn->entry)
			{
				return false;
			}

			$email = $parsedReturn->entry->content->Contact->EmailAddress;
			$id = $parsedReturn->entry->link->Attributes();
			$searchResults = array();
			$searcgContacts = array();
			$pages = array();

			foreach ($parsedReturn->entry as $item)
			{
				$contact = new CTCTContact();
				$contact->setLink($item->link->Attributes()->href);
				$contact->setId($item->id);
				$contact->setEmailAddress($item->content->Contact->EmailAddress);
				$contact->setFullName($item->content->Contact->Name);
				$contact->setStatus($item->content->Contact->Status);
				$contact->setEmailType($item->Contact->EmailType);
				$searchResults[] = $contact;
			}

			if ($parsedReturn->link[4])
			{
				$pages[] = $parsedReturn->link[4]->Attributes()->href;
			}

			$searchContacts = array($searchResults, $pages);

			return $searchContacts;
		}
		/**
		* Public function that shows a list of contacts updated by date in a list of list type
		*
		* @param string $date - valid date
		* @param string $syncType - valid sync type is listtype or listid
		* @param string $list - either the list ID if listid is chosen, or the list type, if list type is chosen
		* @return array $syncContacts - an array of two arrays, array 1 is sync results, array 2 is link for next 50 contacts
		*/
		public function syncContacts($date, $syncType, $list)
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/contacts?updatedsince=' . $date . '&' . $syncType . '=' . $list);
			$parsedReturn = simplexml_load_string($return['xml']);

			$email = $parsedReturn->entry->content->Contact->EmailAddress;
			$id = $parsedReturn->entry->link->Attributes();

			$searchResults = array();
			$syncContacts = array();
			$pages = array();

			foreach ($parsedReturn->entry as $item)
			{
				$contact = new CTCTContact();
				$contact->setLink($item->link->Attributes()->href);
				$contact->setId($item->id);
				$contact->setEmailAddress($item->content->Contact->EmailAddress);
				$contact->setFullName($item->content->Contact->Name);
				$contact->setStatus($item->content->Contact->Status);
				$contact->setEmailType($item->Contact->EmailType);
				$searchResults[] = $contact;
			}

			if ($parsedReturn->link[4])
			{
				$pages[] = $parsedReturn->link[4]->Attributes()->href;
			}

			$syncContacts = array($searchResults, $pages);
			return $syncContacts;
		}
		/**
		* Public function that updates a contact
		*
		* @param string $contactId - valid contact ID of the contact that needs to be updated
		* @param object $contact - valid contact object of the new updates to the contact
		* @return string $code - success or fail message from the API server
		*/
		public function updateContact($contactId, $contact, $return_code = true)
		{
			$utility = new CTCTUtility();
			$existingContact = $this->listContactDetails($contact);
			$contactXml = $this->createContactXml($existingContact->getId(), $contact);
			$return = $utility->httpPut($utility->getApiPath() . $existingContact->getLink(), $contactXml);
			if($return_code) {
				$return = $return['info']['http_code'];
			}
			return $return;
		}
		/**
		* Private function that creates a contact object from XML
		*
		* @param string $contactXml - Valid contact XML
		* @return object $contactStruct - returns a valid contact object
		*/
		private function createContactStruct($contactXml)
		{
			$fullContact = array();
			$parsedReturn = simplexml_load_string($contactXml);
			$fullContact['link'] = ($parsedReturn->link->Attributes()->href);
			$fullContact['id'] = ($parsedReturn->id);
			$fullContact['email_address'] = ($parsedReturn->content->Contact->EmailAddress);
			$fullContact['first_name'] = ($parsedReturn->content->Contact->FirstName);
			$fullContact['last_name'] = ($parsedReturn->content->Contact->LastName);
			$fullContact['middle_name'] = ($parsedReturn->content->Contact->MiddleName);
			$fullContact['company_name'] = ($parsedReturn->content->Contact->CompanyName);
			$fullContact['job_title'] = ($parsedReturn->content->Contact->JobTitle);
			$fullContact['home_number'] = ($parsedReturn->content->Contact->HomePhone);
			$fullContact['work_number'] = ($parsedReturn->content->Contact->WorkPhone);
			$fullContact['address_line_1'] = ($parsedReturn->content->Contact->Addr1);
			$fullContact['address_line_2'] = ($parsedReturn->content->Contact->Addr2);
			$fullContact['address_line_3'] = ($parsedReturn->content->Contact->Addr3);
			$fullContact['city_name'] = ((string) $parsedReturn->content->Contact->City);
			$fullContact['state_code'] = ((string) $parsedReturn->content->Contact->StateCode);
			$fullContact['state_name'] = ((string) $parsedReturn->content->Contact->StateName);
			$fullContact['country_code'] = ($parsedReturn->content->Contact->CountryCode);
			$fullContact['zip_code'] = ($parsedReturn->content->Contact->PostalCode);
			$fullContact['sub_zip_code'] = ($parsedReturn->content->Contact->SubPostalCode);
			$fullContact['custom_field_1'] = ($parsedReturn->content->Contact->customField1);
			$fullContact['custom_field_2'] = ($parsedReturn->content->Contact->customField2);
			$fullContact['custom_field_3'] = ($parsedReturn->content->Contact->customField3);
			$fullContact['custom_field_4'] = ($parsedReturn->content->Contact->customField4);
			$fullContact['custom_field_5'] = ($parsedReturn->content->Contact->customField5);
			$fullContact['custom_field_6'] = ($parsedReturn->content->Contact->customField6);
			$fullContact['custom_field_7'] = ($parsedReturn->content->Contact->customField7);
			$fullContact['custom_field_8'] = ($parsedReturn->content->Contact->customField8);
			$fullContact['custom_field_9'] = ($parsedReturn->content->Contact->customField9);
			$fullContact['custom_field_10'] = ($parsedReturn->content->Contact->customField10);
			$fullContact['custom_field_11'] = ($parsedReturn->content->Contact->customField11);
			$fullContact['custom_field_12'] = ($parsedReturn->content->Contact->customField12);
			$fullContact['custom_field_13'] = ($parsedReturn->content->Contact->customField13);
			$fullContact['custom_field_14'] = ($parsedReturn->content->Contact->customField14);
			$fullContact['custom_field_15'] = ($parsedReturn->content->Contact->customField15);
			$fullContact['notes'] = ($parsedReturn->content->Contact->Note);
			$fullContact['mail_type'] = ($parsedReturn->content->Contact->EmailType);
			$fullContact['status'] = ($parsedReturn->content->Contact->Status);

			if ($parsedReturn->content->Contact->ContactLists->ContactList)
			{
				foreach ($parsedReturn->content->Contact->ContactLists->ContactList as $item)
				{
					$fullContact['lists'][] = (trim((string) $item->Attributes()));
				}
			}

			$contact = new CTCTContact($fullContact);
			return $contact;
		}
		/**
		* Private function that creates contact XML
		*
		* @param string $id - optional valid contact ID, used for updating a contact
		* @param object $contact - valid contact object
		* @return string $entry - valid XML of a contact
		*/
		private	function createContactXml($id, $contact)
		{
			$utility = new CTCTUtility();

			if ( empty($id)) {
				$id = "urn:uuid:E8553C09F4xcvxCCC53F481214230867087";
			}

			$update_date = date("Y-m-d").'T'.date("H:i:s").'+01:00';
			$xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><entry xmlns='http://www.w3.org/2005/Atom'></entry>";
			$xml_object = simplexml_load_string($xml_string);
			$title_node = $xml_object->addChild("title", htmlspecialchars(("TitleNode"), ENT_QUOTES, 'UTF-8'));
			$updated_node = $xml_object->addChild("updated", htmlspecialchars(($update_date), ENT_QUOTES, 'UTF-8'));
			$author_node = $xml_object->addChild("author");
			$author_name = $author_node->addChild("name", ("CTCT Samples"));
			$id_node = $xml_object->addChild("id", htmlspecialchars(((string) $id),ENT_QUOTES, 'UTF-8'));
			$summary_node = $xml_object->addChild("summary", htmlspecialchars(("Customer document"),ENT_QUOTES, 'UTF-8'));
			$summary_node->addAttribute("type", "text");
			$content_node = $xml_object->addChild("content");
			$content_node->addAttribute("type", "application/vnd.ctct+xml");
			$contact_node = $content_node->addChild("Contact", htmlspecialchars(("Customer document"), ENT_QUOTES, 'UTF-8'));
			$contact_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
			$email_node = $contact_node->addChild("EmailAddress", htmlspecialchars(($contact->getEmailAddress()), ENT_QUOTES, 'UTF-8'));
			$fname_node = $contact_node->addChild("FirstName", urldecode(htmlspecialchars(($contact->getFirstName()), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("LastName", urldecode(htmlspecialchars(($contact->getLastName()), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("MiddleName", urldecode(htmlspecialchars(($contact->getMiddleName()), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("CompanyName", urldecode(htmlspecialchars(($contact->getCompanyName()), ENT_QUOTES, 'UTF-8')));
			$lname_node = $contact_node->addChild("JobTitle", urldecode(htmlspecialchars(($contact->getJobTitle()), ENT_QUOTES, 'UTF-8')));
			#$optin_node = $contact_node->addChild("OptInSource", htmlspecialchars($utility->getActionBy()));
			$optin_node = $contact_node->addChild("OptInSource", htmlspecialchars($contact->getOptInSource()));  // ZK mod
			$hn_node = $contact_node->addChild("HomePhone", htmlspecialchars($contact->getHomeNumber(), ENT_QUOTES, 'UTF-8'));
			$wn_node = $contact_node->addChild("WorkPhone", htmlspecialchars($contact->getWorkNumber(), ENT_QUOTES, 'UTF-8'));
			$ad1_node = $contact_node->addChild("Addr1", htmlspecialchars($contact->getAddr1(), ENT_QUOTES, 'UTF-8'));
			$ad2_node = $contact_node->addChild("Addr2", htmlspecialchars($contact->getAddr2(), ENT_QUOTES, 'UTF-8'));
			$ad3_node = $contact_node->addChild("Addr3", htmlspecialchars($contact->getAddr3(), ENT_QUOTES, 'UTF-8'));
			$city_node = $contact_node->addChild("City", htmlspecialchars($contact->getCity(), ENT_QUOTES, 'UTF-8'));
			$state_node = $contact_node->addChild("StateCode", htmlspecialchars($contact->getStateCode(), ENT_QUOTES, 'UTF-8'));
			$state_name = $contact_node->addChild("StateName", htmlspecialchars($contact->getStateName(), ENT_QUOTES, 'UTF-8'));
			$ctry_node = $contact_node->addChild("CountryCode", htmlspecialchars($contact->getCountryCode(), ENT_QUOTES, 'UTF-8'));
			$zip_node = $contact_node->addChild("PostalCode", htmlspecialchars($contact->getPostalCode(), ENT_QUOTES, 'UTF-8'));
			$subzip_node = $contact_node->addChild("SubPostalCode", htmlspecialchars($contact->getSubPostalCode(), ENT_QUOTES, 'UTF-8'));
			$note_node = $contact_node->addChild("Note", htmlspecialchars($contact->getNotes(), ENT_QUOTES, 'UTF-8'));
			$emailtype_node = $contact_node->addChild("EmailType", htmlspecialchars($contact->getEmailType(), ENT_QUOTES, 'UTF-8'));
			$customfield1_node = $contact_node->addChild("CustomField1", htmlspecialchars(($contact->getCustomField1()), ENT_QUOTES, 'UTF-8'));
			$customfield2_node = $contact_node->addChild("CustomField2", htmlspecialchars(($contact->getCustomField2()), ENT_QUOTES, 'UTF-8'));
			$customfield3_node = $contact_node->addChild("CustomField3", htmlspecialchars(($contact->getCustomField3()), ENT_QUOTES, 'UTF-8'));
			$customfield4_node = $contact_node->addChild("CustomField4", htmlspecialchars(($contact->getCustomField4()), ENT_QUOTES, 'UTF-8'));
			$customfield5_node = $contact_node->addChild("CustomField5", htmlspecialchars(($contact->getCustomField5()), ENT_QUOTES, 'UTF-8'));
			$customfield6_node = $contact_node->addChild("CustomField6", htmlspecialchars(($contact->getCustomField6()), ENT_QUOTES, 'UTF-8'));
			$customfield7_node = $contact_node->addChild("CustomField7", htmlspecialchars(($contact->getCustomField7()), ENT_QUOTES, 'UTF-8'));
			$customfield8_node = $contact_node->addChild("CustomField8", htmlspecialchars(($contact->getCustomField8()), ENT_QUOTES, 'UTF-8'));
			$customfield9_node = $contact_node->addChild("CustomField9", htmlspecialchars(($contact->getCustomField9()), ENT_QUOTES, 'UTF-8'));
			$customfield10_node = $contact_node->addChild("CustomField10", htmlspecialchars(($contact->getCustomField10()), ENT_QUOTES, 'UTF-8'));
			$customfield11_node = $contact_node->addChild("CustomField11", htmlspecialchars(($contact->getCustomField11()), ENT_QUOTES, 'UTF-8'));
			$customfield12_node = $contact_node->addChild("CustomField12", htmlspecialchars(($contact->getCustomField12()), ENT_QUOTES, 'UTF-8'));
			$customfield13_node = $contact_node->addChild("CustomField13", htmlspecialchars(($contact->getCustomField13()), ENT_QUOTES, 'UTF-8'));
			$customfield14_node = $contact_node->addChild("CustomField14", htmlspecialchars(($contact->getCustomField14()), ENT_QUOTES, 'UTF-8'));
			$customfield15_node = $contact_node->addChild("CustomField15", htmlspecialchars(($contact->getCustomField15()), ENT_QUOTES, 'UTF-8'));

			$contactlists_node = $contact_node->addChild("ContactLists");

			foreach ($contact->getLists() as $tmp)
			{
				$contactlist_node = $contactlists_node->addChild("ContactList");
				$contactlist_node->addAttribute("id", $tmp);
			}

			$entry = $xml_object->asXML();
			return $entry;
		}
	}
	/**
	 * Contact class defines a contact object
	 *
	 * Defines a contact object, includes all contact variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTContact
	{
		/**
		* Construct function for the Contact Class
		*
		* @param array $params - an array of variables that set up a contact object
		* @return object contact object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$utility = new CTCTUtility();
			if (@$params['status'] == 'Do Not Mail')
			{
				$utility->setActionBy('ACTION_BY_CONTACT');
			}
			$this->setLink(@$params['link']);
			$this->setId(@$params['id']);
			$this->setEmailAddress(@$params['email_address']);
			$this->setFirstName(@$params['first_name']);
			$this->setMiddleName(@$params['middle_name']);
			$this->setLastName(@$params['last_name']);
			$this->setCompanyName(@$params['company_name']);
			$this->setJobTitle(@$params['job_title']);
			$this->setHomeNumber(@$params['home_number']);
			$this->setWorkNumber(@$params['work_number']);
			$this->setAddr1(@$params['address_line_1']);
			$this->setAddr2(@$params['address_line_2']);
			$this->setAddr3(@$params['address_line_3']);
			$this->setCity(@$params['city_name']);
			$this->setStateCode(@$params['state_code']);
			$this->setStateName(@$params['state_name']);
			$this->setCountryCode(@$params['country_code']);
			$this->setPostalCode(@$params['zip_code']);
			$this->setSubPostalCode(@$params['sub_zip_code']);
			$this->setNotes(@$params['notes']);
			$this->setCustomField1(@$params['custom_field_1']);
			$this->setCustomField2(@$params['custom_field_2']);
			$this->setCustomField3(@$params['custom_field_3']);
			$this->setCustomField4(@$params['custom_field_4']);
			$this->setCustomField5(@$params['custom_field_5']);
			$this->setCustomField6(@$params['custom_field_6']);
			$this->setCustomField7(@$params['custom_field_7']);
			$this->setCustomField8(@$params['custom_field_8']);
			$this->setCustomField9(@$params['custom_field_9']);
			$this->setCustomField10(@$params['custom_field_10']);
			$this->setCustomField11(@$params['custom_field_11']);
			$this->setCustomField12(@$params['custom_field_12']);
			$this->setCustomField13(@$params['custom_field_13']);
			$this->setCustomField14(@$params['custom_field_14']);
			$this->setCustomField15(@$params['custom_field_15']);
			$this->setEmailType(@$params['mail_type']);
			$this->setOptInSource((isset($params['opt_in_source']) ? $params['opt_in_source'] : $utility->getActionBy())); // ZK mod

			if (@$params['lists'])
			{
				foreach (@$params['lists'] as $tmp)
				{
					$this->setLists($tmp);
				}
			}

			return $this;
		}

		private $link;
		private $id;
		private $status;
		private $emailAddress;
		private $emailType;
		private $firstName;
		private $middleName;
		private $lastName;
		private $fullName;
		private $jobTitle;
		private $companyName;
		private $homeNumber;
		private $workNumber;
		private $addr1;
		private $addr2;
		private $addr3;
		private $city;
		private $stateCode;
		private $stateName;
		private $countryCode;
		private $countryName;
		private $postalCode;
		private $subPostalCode;
		private $notes;
		private $customField1;
		private $customField2;
		private $customField3;
		private $customField4;
		private $customField5;
		private $customField6;
		private $customField7;
		private $customField8;
		private $customField9;
		private $customField10;
		private $customField11;
		private $customField12;
		private $customField13;
		private $customField14;
		private $customField15;
		private $contactLists;
		private $confirmed;
		private $optInSource;
		private $lists = array();
		private $bounces = array();
		private $clicks = array();
		private $forwards = array();
		private $opens = array();
		private $optOuts = array();
		private $sends = array();

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setId( $value ) { $this->id = $value; }
		public function getId() { return $this->id; }

		public function setStatus( $value ) { $this->status = $value; }
		public function getStatus() { return $this->status; }

		public function setEmailAddress( $value ) { $this->emailAddress = $value; }
		public function getEmailAddress() { return $this->emailAddress; }

		public function setEmailType( $value ) { $this->emailType = $value; }
		public function getEmailType() { return $this->emailType; }

		public function setFullName( $value ) { $this->fullName = $value; }
		public function getFullName() { return $this->fullName; }

		public function setFirstName( $value ) { $this->firstName = $value; }
		public function getFirstName() { return $this->firstName; }

		public function setMiddleName( $value ) { $this->middleName = $value; }
		public function getMiddleName() { return $this->middleName; }

		public function setLastName( $value ) { $this->lastName = $value; }
		public function getLastName() { return $this->lastName; }

		public function setJobTitle( $value ) { $this->jobTitle = $value; }
		public function getJobTitle() { return $this->jobTitle; }

		public function setCompanyName( $value ) { $this->companyName = $value; }
		public function getCompanyName() { return $this->companyName; }

		public function setHomeNumber( $value ) { $this->homeNumber = $value; }
		public function getHomeNumber() { return $this->homeNumber; }

		public function setWorkNumber( $value ) { $this->workNumber = $value; }
		public function getWorkNumber() { return $this->workNumber; }

		public function setAddr1( $value ) { $this->addr1 = $value; }
		public function getAddr1() { return $this->addr1; }

		public function setAddr2( $value ) { $this->addr2 = $value; }
		public function getAddr2() { return $this->addr2; }

		public function setAddr3( $value ) { $this->addr3 = $value; }
		public function getAddr3() { return $this->addr3; }

		public function setCity( $value ) { $this->city = $value; }
		public function getCity() { return $this->city; }

		public function setStateCode( $value ) { $this->stateCode = $value; }
		public function getStateCode() { return $this->stateCode; }

		public function setStateName( $value ) { $this->stateName = $value; }
		public function getStateName() { return $this->stateName; }

		public function setCountryCode( $value ) { $this->countryCode = $value; }
		public function getCountryCode() { return $this->countryCode; }

		public function setCountryName( $value ) { $this->countryName = $value; }
		public function getCountryName() { return $this->countryName; }

		public function setPostalCode( $value ) { $this->postalCode = $value; }
		public function getPostalCode() { return $this->postalCode; }

		public function setSubPostalCode( $value ) { $this->subPostalCode = $value; }
		public function getSubPostalCode() { return $this->subPostalCode; }

		public function setNotes( $value ) { $this->notes = $value; }
		public function getNotes() { return $this->notes; }

		public function setCustomField1( $value ) { $this->customField1 = $value; }
		public function getCustomField1() { return $this->customField1; }

		public function setCustomField2( $value ) { $this->customField2 = $value; }
		public function getCustomField2() { return $this->customField2; }

		public function setCustomField3( $value ) { $this->customField3 = $value; }
		public function getCustomField3() { return $this->customField3; }

		public function setCustomField4( $value ) { $this->customField4 = $value; }
		public function getCustomField4() { return $this->customField4; }

		public function setCustomField5( $value ) { $this->customField5 = $value; }
		public function getCustomField5() { return $this->customField5; }

		public function setCustomField6( $value ) { $this->customField6 = $value; }
		public function getCustomField6() { return $this->customField6; }

		public function setCustomField7( $value ) { $this->customField7 = $value; }
		public function getCustomField7() { return $this->customField7; }

		public function setCustomField8( $value ) { $this->customField8 = $value; }
		public function getCustomField8() { return $this->customField8; }

		public function setCustomField9( $value ) { $this->customField9 = $value; }
		public function getCustomField9() { return $this->customField9; }

		public function setCustomField10( $value ) { $this->customField10 = $value; }
		public function getCustomField10() { return $this->customField10; }

		public function setCustomField11( $value ) { $this->customField11 = $value; }
		public function getCustomField11() { return $this->customField11; }

		public function setCustomField12( $value ) { $this->customField12 = $value; }
		public function getCustomField12() { return $this->customField12; }

		public function setCustomField13( $value ) { $this->customField13 = $value; }
		public function getCustomField13() { return $this->customField13; }

		public function setCustomField14( $value ) { $this->customField14 = $value; }
		public function getCustomField14() { return $this->customField14; }

		public function setCustomField15( $value ) { $this->customField15 = $value; }
		public function getCustomField15() { return $this->customField15; }

		public function setConfirmed( $value ) { $this->confirmed = $value; }
		public function getConfirmed() { return $this->confirmed; }

		public function setLists( $value ) { $this->lists[] = $value; }
		public function getLists() { return $this->lists; }
		public function removeLists() { $this->lists= array(); }

		public function setBounces( $value ) { $this->bounces[] = $value; }
		public function getBounces() { return $this->bounces; }

		public function setClicks( $value ) { $this->clicks[] = $value; }
		public function getClicks() { return $this->clicks; }

		public function setForwards( $value ) { $this->forwards[] = $value; }
		public function getForwards() { return $this->forwards; }

		public function setOpens( $value ) { $this->opens[] = $value; }
		public function getOpens() { return $this->opens; }

		public function setOptOuts( $value ) { $this->optOuts[] = $value; }
		public function getOptOuts() { return $this->optOuts; }

		public function setSends( $value ) { $this->sends[] = $value; }
		public function getSends() { return $this->sends; }

		public function setOptInSource( $value ) { $this->optInSource = $value; }
		public function getOptInSource() { return $this->optInSource; }

	}
	/**
	 * Campaign class defines a campaign object
	 *
	 * Defines a campaign object, includes all campaign variables as well as the
	 * getters and setters for variables
	 *
	 */
		class CTCTCampaign
	{
		/**
		* Construct function for the Campaign Class
		*
		* @param array $params - an array of variables that set up a campaign object
		* @return object campaign object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setId(@$params['id']);
			$this->setLink(@$params['link']);
			$this->setCampaignName(@$params['campaign_name']);
			$this->setStatus(@$params['status']);
			$this->setCampaignDate(@$params['campaign_date']);
			$this->setLastEditDate(@$params['last_edit_date']);
			$this->setCampaignSent(@$params['campaign_sent']);
			$this->setCampaignOpens(@$params['campaign_opens']);
			$this->setCampaignClicks(@$params['campaign_clicks']);
			$this->setCampaignBounces(@$params['campaign_bounces']);
			$this->setCampaignForwards(@$params['campaign_forwards']);
			$this->setCampaignOptOuts(@$params['campaign_optouts']);
			$this->setCampaignSpamReports(@$params['campaign_spamreports']);
			$this->setSubject(@$params['subject']);
			$this->setFromName(@$params['from_name']);
			$this->setCampaignType(@$params['campaign_type']);
			$this->setVawp(@$params['view_as_web_page']);
			$this->setVawpLinkText(@$params['vawp_link_text']);
			$this->setVawpText(@$params['vawp_text']);
			$this->setPermissionReminder(@$params['permission_reminder']);
			$this->setPermissionReminderText(@$params['permission_reminder_txt']);
			$this->setGreetingSalutation(@$params['greeting_salutation']);
			$this->setGreetingName(@$params['greeting_name']);
			$this->setGreetingString(@$params['greeting_string']);
			$this->setOrgName(@$params['org_name']);
			$this->setOrgAddr1(@$params['org_address_1']);
			$this->setOrgAddr2(@$params['org_address_2']);
			$this->setOrgAddr3(@$params['org_address_3']);
			$this->setOrgCity(@$params['org_city']);
			$this->setOrgState(@$params['org_state']);
			$this->setOrgInternationalState(@$params['org_international_state']);
			$this->setOrgCountry(@$params['org_country']);
			$this->setOrgPostalCode(@$params['org_postal_code']);
			$this->setIncForwardEmail(@$params['include_forward_email']);
			$this->setForwardEmailLinkText(@$params['forward_email_link_text']);
			$this->setIncSubscribeLink(@$params['include_subscribe_link']);
			$this->setSubscribeLinkText(@$params['subscribe_link_text']);
			$this->setEmailContentFormat(@$params['email_content_format']);
			$this->setEmailContent(@$params['email_content']);
			$this->setTextVersionContent(@$params['text_version_content']);
			$this->setStyleSheet(@$params['style_sheet']);

			if (!empty($params['lists']))
			{
				foreach ($params['lists'] as $tmp)
				{
					$this->setLists($tmp);
				}
			}
			//From and Reply Addresses must be Verified addresses
			//These can be used from getVerifiedAddresses() in the settingsCollection class
			$this->setFromEmailAddress(@$params['frm_addr']);
			$this->setFromEmailAddressLink(@$params['frm_addr_link']);
			$this->setReplyEmailAddress(@$params['rep_addr']);
			$this->setReplyEmailAddressLink(@$params['rep_addr_link']);

			$this->setArchiveStatus(@$params['archive_status']);
			$this->setArchiveUrl(@$params['archive_url']);

			return $this;
		}

		private $campaignName;
		private $id;
		private $link;
		private $status;
		private $campaignDate;
		private $lastEditDate;
		private $campaignSent;
		private $campaignOpens;
		private $campaignClicks;
		private $campaignBounces;
		private $campaignForwards;
		private $campaignOptOuts;
		private $campaignSpamReports;
		private $subject;
		private $fromName;
		private $campaignType;
		private $vawp;
		private $vawpLinkText;
		private $vawpText;
		private $permissionReminder;
		private $permissionReminderText;
		private $greetingSalutation;
		private $greetingName;
		private $greetingString;
		private $orgName;
		private $orgAddr1;
		private $orgAddr2;
		private $orgAddr3;
		private $orgCity;
		private $orgState;
		private $orgInternationalState;
		private $orgCountry;
		private $orgPostalCode;
		private $incForwardEmail;
		private $forwardEmailLinkText;
		private $incSubscribeLink;
		private $subscribeLinkText;
		private $emailContentFormat;
		private $emailContent;
		private $textVersionContent;
		private $styleSheet;
		private $lists = array();
		private $fromEmailAddress;
		private $fromEmailAddressLink;
		private $replyEmailAddress;
		private $replyEmailAddressLink;
		private $archiveStatus;
		private $archiveUrl;

		public function setCampaignName( $value ) { $this->campaignName = $value; }
		public function getCampaignName() { return $this->campaignName; }

		public function setStatus( $value ) { $this->status = $value; }
		public function getStatus() { return $this->status; }

		public function setCampaignDate( $value ) { $this->campaignDate = $value; }
		public function getCampaignDate() { return $this->campaignDate; }


		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setId( $value ) { $this->id = $value; }
		public function getId() { return $this->id; }

		public function setLastEditDate( $value ) { $this->lastEditDate = $value; }
		public function getLastEditDate() { return $this->lastEditDate; }

		public function setCampaignSent( $value ) { $this->campaignSent = $value; }
		public function getCampaignSent() { return $this->campaignSent; }

		public function setCampaignOpens( $value ) { $this->campaignOpens = $value; }
		public function getCampaignOpens() { return $this->campaignOpens; }

		public function setCampaignClicks( $value ) { $this->campaignClicks = $value; }
		public function getCampaignClicks() { return $this->campaignClicks; }

		public function setCampaignBounces( $value ) { $this->campaignBounces = $value; }
		public function getCampaignBounces() { return $this->campaignBounces; }

		public function setCampaignForwards( $value ) { $this->campaignForwards = $value; }
		public function getCampaignForwards() { return $this->campaignForwards; }

		public function setCampaignOptOuts( $value ) { $this->campaignOptOuts = $value; }
		public function getCampaignOptOuts() { return $this->campaignOptOuts; }

		public function setCampaignSpamReports( $value ) { $this->campaignSpamReports = $value; }
		public function getCampaignSpamReports() { return $this->campaignSpamReports; }

		public function setCampaignType( $value ) { $this->campaignType = $value; }
		public function getCampaignType() { return $this->campaignType; }

		public function setSubject( $value ) { $this->subject = $value; }
		public function getSubject() { return $this->subject; }

		public function setFromName( $value ) { $this->fromName = $value; }
		public function getFromName() { return $this->fromName; }

		public function setVawp( $value ) { $this->vawp = $value; }
		public function getVawp() { return $this->vawp; }

		public function setVawpLinkText( $value ) { $this->vawpLinkText = $value; }
		public function getVawpLinkText() { return $this->vawpLinkText; }

		public function setVawpText( $value ) { $this->vawpText = $value; }
		public function getVawpText() { return $this->vawpText; }

		public function setPermissionReminder( $value ) { $this->permissionReminder = $value; }
		public function getPermissionReminder() { return $this->permissionReminder; }

		public function setPermissionReminderText( $value ) { $this->permissionReminderText = $value; }
		public function getPermissionReminderText() { return $this->permissionReminderText; }

		public function setGreetingSalutation( $value ) { $this->greetingSalutation = $value; }
		public function getGreetingSalutation() { return $this->greetingSalutation; }

		public function setGreetingName( $value ) { $this->greetingName = $value; }
		public function getGreetingName() { return $this->greetingName; }

		public function setGreetingString( $value ) { $this->greetingString = $value; }
		public function getGreetingString() { return $this->greetingString; }

		public function setOrgName( $value ) { $this->orgName = $value; }
		public function getOrgName() { return $this->orgName; }

		public function setOrgAddr1( $value ) { $this->orgAddr1 = $value; }
		public function getOrgAddr1() { return $this->orgAddr1; }

		public function setOrgAddr2( $value ) { $this->orgAddr2 = $value; }
		public function getOrgAddr2() { return $this->orgAddr2; }

		public function setOrgAddr3( $value ) { $this->orgAddr3 = $value; }
		public function getOrgAddr3() { return $this->orgAddr3; }

		public function setOrgCity( $value ) { $this->orgCity = $value; }
		public function getOrgCity() { return $this->orgCity; }

		public function setOrgState( $value ) { $this->orgState = $value; }
		public function getOrgState() { return $this->orgState; }

		public function setOrgInternationalState( $value ) { $this->orgInternationalState = $value; }
		public function getOrgInternationalState() { return $this->orgInternationalState; }

		public function setOrgCountry( $value ) { $this->orgCountry = $value; }
		public function getOrgCountry() { return $this->orgCountry; }

		public function setOrgPostalCode( $value ) { $this->orgPostalCode = $value; }
		public function getOrgPostalCode() { return $this->orgPostalCode; }

		public function setIncForwardEmail( $value ) { $this->incForwardEmail = $value; }
		public function getIncForwardEmail() { return $this->incForwardEmail; }

		public function setForwardEmailLinkText( $value ) { $this->forwardEmailLinkText = $value; }
		public function getForwardEmailLinkText() { return $this->forwardEmailLinkText; }

		public function setIncSubscribeLink( $value ) { $this->incSubscribeLink = $value; }
		public function getIncSubscribeLink() { return $this->incSubscribeLink; }

		public function setSubscribeLinkText( $value ) { $this->subscribeLinkText = $value; }
		public function getSubscribeLinkText() { return $this->subscribeLinkText; }

		public function setEmailContentFormat( $value ) { $this->emailContentFormat = $value; }
		public function getEmailContentFormat() { return $this->emailContentFormat; }

		public function setEmailContent( $value ) { $this->emailContent = $value; }
		public function getEmailContent() { return $this->emailContent; }

		public function setTextVersionContent( $value ) { $this->textVersionContent = $value; }
		public function getTextVersionContent() { return $this->textVersionContent; }

		public function setStyleSheet( $value ) { $this->styleSheet = $value; }
		public function getStyleSheet() { return $this->styleSheet; }

		public function setLists( $value ) { $this->lists[] = $value; }
		public function getLists() { return $this->lists; }
		public function removeLists() { $this->lists=""; }

		public function setFromEmailAddress( $value ) { $this->fromEmailAddress = $value; }
		public function getFromEmailAddress() { return $this->fromEmailAddress; }

		public function setFromEmailAddressLink( $value ) { $this->fromEmailAddressLink = $value; }
		public function getFromEmailAddressLink() { return $this->fromEmailAddressLink; }

		public function setReplyEmailAddress( $value ) { $this->replyEmailAddress = $value; }
		public function getReplyEmailAddress() { return $this->replyEmailAddress; }

		public function setReplyEmailAddressLink( $value ) { $this->replyEmailAddressLink = $value; }
		public function getReplyEmailAddressLink() { return $this->replyEmailAddressLink; }

		public function setArchiveStatus( $value ) { $this->archiveStatus = $value; }
		public function getArchiveStatus() { return $this->archiveStatus; }

		public function setArchiveUrl( $value ) { $this->archiveUrl = $value; }
		public function getArchiveUrl() { return $this->archiveUrl; }
	}
	/**
	 * Campaigns Collection Class for calls to the campaign collection API
	 *
	 * Includes functions for listing all campaigns within the account, specific campaign details,
	 * also creating and deleting campaigns
	 *
	 */
	class CTCTCampaignsCollection
	{
		/**
		* Public function that does a POST to the Campaigns collection, passing a campaign object
		*
		* @param object $campaign - a valid campaign object with all required fields
		* @return string $code - returns success or fail code from API server
		*/
		public function createCampaign($campaign)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/campaigns';
			$campaignXml = $this->createCampaignXml(null, $campaign);
			$return = $utility->httpPost($call, $campaignXml);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Public function that deletes a campaign, using a campaign object
		*
		* @param object $campaign - a valid campaign object with a valid campaign link
		* @return string $code - returns success or fail code from API server
		*/
		public function deleteCampaign($campaign)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $campaign->getLink();
			$return = $utility->httpDelete($call);
			$code = $return['info']['http_code'];
			return $code;
		}
		/**
		* Public function that gets campaign details of a single campaign object passed to it.
		*
		* @param object $campaign - a valid campaign object with a valid campaign link
		* @return object $campaign - returns campaign object with full details
		*/
		public function listCampaignDetails($campaignObj)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $campaignObj->getLink();
			$return = $utility->httpGet($call);
			$campaign = $this->createCampaignStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $campaign;
			}
		}
		/**
		* Public function that gets a list of first 50 campaigns in the account
		*
		* @return array $allCampaigns - an array of two arrays, array 1 is campaign objects, array 2 is link for next 50 campaigns
		*/
		public function listCampaigns()
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/campaigns');

			$allCampaigns = array();
			$campaignList = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml']);

			foreach ($parsedReturn->entry as $item)
			{
				$campaign = new CTCTCampaign();
				$campaign->setLink($item->link['href']);
				$campaign->setId($item->id);
				$campaign->setCampaignName($item->content->Campaign->Name);
				$campaign->setStatus($item->content->Campaign->Status);
				$campaign->setCampaignDate($item->content->Campaign->Date);
				$campaignList[] = $campaign;
			}

			if ($parsedReturn->link[4])
			{
				$pages[] = $parsedReturn->link[4]->Attributes()->href;
			}

			$allCampaigns = array($campaignList, $pages);

			return $allCampaigns;
		}
		/**
		* Public function that searches Campaigns by Status
		*
		* @param string $status - valid status is DRAFT, SENT, RUNNING, SCHEDULED
		* @return array $searchCampaigns - an array of two arrays, array 1 is search results, array 2 is link for next 50 search results
		*/
		public function searchCampaigns($status)
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/campaigns?status=' . $status);
			$parsedReturn = simplexml_load_string($return['xml']);

			$email = $parsedReturn->entry->content->Campaign;

			if (!$email)
			{
				return false;
			}
			else
			{
				$searchResults = array();
				$pages = array();
				$searchCampaigns = array();

				foreach ($parsedReturn->entry as $item)
				{
					$campaign = new CTCTCampaign();
					$campaign->setLink($item->link['href']);
					$campaign->setId($item->id);
					$campaign->setCampaignName($item->content->Campaign->Name);
					$campaign->setStatus($item->content->Campaign->Status);
					$campaign->setCampaignDate($item->content->Campaign->Date);
					$searchResults[] = $campaign;
				}

				if ($parsedReturn->link[4])
				{
					$pages[] = $parsedReturn->link[4]->Attributes()->href;
				}

				$searchCampaigns = array($searchResults, $pages);

				return $searchCampaigns;
			}
		}
		/**
		* Public function that updates a draft campaign
		*
		* @param string $campaignId - valid campaign ID of the campaign that needs to be updated
		* @param object $campaign - valid campaign object of the new updates to the campaign
		* @return string $code - success or fail message from the API server
		*/
		public function updateCampaign($campaign)
		{
			$utility = new CTCTUtility();
			$existingCampaign = $this->listCampaignDetails($campaign);
			$campaignXml = $this->createCampaignXml($existingCampaign->getId(), $campaign);
			$return = $utility->httpPut($utility->getApiPath() . $existingCampaign->getLink(), $campaignXml);
			$code = $return['info'];
			return $code;
		}
		/**
		* Private function that creates a campaign object from XML
		*
		* @param string $campaignXml - Valid campaign XML
		* @return object $campaignStruct - returns a valid campaign object
		*/
		private function createCampaignStruct($campaignXml)
		{
			$campaign = array();
			$parsedReturn = simplexml_load_string($campaignXml);
			$campaign['link'] = ($parsedReturn->link->Attributes()->href);
			$campaign['id'] = ($parsedReturn->id);
			$campaign['campaign_name'] = ($parsedReturn->content->Campaign->Name);
			$campaign['status'] = ($parsedReturn->content->Campaign->Status);
			$campaign['campaign_date'] = ($parsedReturn->content->Campaign->Date);
			$campaign['last_edit_date'] = ($parsedReturn->content->Campaign->LastEditDate);
			$campaign['campaign_sent'] = ($parsedReturn->content->Campaign->Sent);
			$campaign['campaign_opens'] = ($parsedReturn->content->Campaign->Opens);
			$campaign['campaign_clicks'] = ($parsedReturn->content->Campaign->Clicks);
			$campaign['campaign_bounces'] = ($parsedReturn->content->Campaign->Bounces);
			$campaign['campaign_forwards'] = ($parsedReturn->content->Campaign->Forwards);
			$campaign['campaign_optouts'] = ($parsedReturn->content->Campaign->OptOuts);
			$campaign['campaign_spamreports'] = ($parsedReturn->content->Campaign->SpamReports);
			$campaign['subject'] = ($parsedReturn->content->Campaign->Subject);
			$campaign['from_name'] = ($parsedReturn->content->Campaign->FromName);
			$campaign['campaign_type'] = ($parsedReturn->content->Campaign->CampaignType);
			$campaign['view_as_web_page'] = ($parsedReturn->content->Campaign->ViewAsWebpage);
			$campaign['vawp_link_text'] = ($parsedReturn->content->Campaign->ViewAsWebpageLinkText);
			$campaign['vawp_text'] = ($parsedReturn->content->Campaign->ViewAsWebpageText);
			$campaign['permission_reminder'] = ($parsedReturn->content->Campaign->PermissionReminder);
			$campaign['permission_reminder_txt'] = ($parsedReturn->content->Campaign->PermissionReminderText);
			$campaign['greeting_salutation'] = ($parsedReturn->content->Campaign->GreetingSalutation);
			$campaign['greeting_name'] = ($parsedReturn->content->Campaign->GreetingName);
			$campaign['greeting_string'] = ($parsedReturn->content->Campaign->GreetingString);
			$campaign['org_name'] = ($parsedReturn->content->Campaign->OrganizationName);
			$campaign['org_address_1'] = ($parsedReturn->content->Campaign->OrganizationAddress1);
			$campaign['org_address_2'] = ($parsedReturn->content->Campaign->OrganizationAddress2);
			$campaign['org_address_3'] = ($parsedReturn->content->Campaign->OrganizationAddress3);
			$campaign['org_city'] = ($parsedReturn->content->Campaign->OrganizationCity);
			$campaign['org_state'] = ($parsedReturn->content->Campaign->OrganizationState);
			$campaign['org_international_state'] = ($parsedReturn->content->Campaign->OrganizationInternationalState);
			$campaign['org_country'] = ($parsedReturn->content->Campaign->OrganizationCountry);
			$campaign['org_postal_code'] = ($parsedReturn->content->Campaign->OrganizationPostalCode);
			$campaign['include_forward_email'] = ($parsedReturn->content->Campaign->IncludeForwardEmail);
			$campaign['forward_email_link_text'] = ($parsedReturn->content->Campaign->ForwardEmailLinkText);
			$campaign['include_subscribe_link'] = ($parsedReturn->content->Campaign->IncludeSubscribeLink);
			$campaign['subscribe_link_text'] = ($parsedReturn->content->Campaign->SubscribeLinkText);
			$campaign['email_content_format'] = ($parsedReturn->content->Campaign->EmailContentFormat);
			$campaign['email_content'] = ($parsedReturn->content->Campaign->EmailContent);
			$campaign['text_version_content'] = ($parsedReturn->content->Campaign->EmailTextContent);
			$campaign['style_sheet'] = ($parsedReturn->content->Campaign->StyleSheet);
			$campaign['archive_status'] = ($parsedReturn->content->Campaign->ArchiveStatus);
			$campaign['archive_url'] = ($parsedReturn->content->Campaign->ArchiveURL);
			$campaign['frm_addr'] = ($parsedReturn->content->Campaign->FromEmail->EmailAddress);
			$campaign['frm_addr_link'] = ($parsedReturn->content->Campaign->FromEmail->Email->link->Attributes()->href);
			$campaign['rep_addr'] = ($parsedReturn->content->Campaign->ReplyToEmail->EmailAddress);
			$campaign['rep_addr_link'] = ($parsedReturn->content->Campaign->ReplyToEmail->Email->link->Attributes()->href);

			if ($parsedReturn->content->Contact->ContactLists->ContactList)
			{
				foreach ($parsedReturn->content->ContactLists->ContactList as $item)
				{
					$campaign['lists'][] = (trim((string) $item->Attributes()->href));
				}
			}

			$campaignStruct = new CTCTCampaign($campaign);
			return $campaignStruct;
		}
		/**
		* Private function that creates campaign XML
		*
		* @param string $id - optional valid campaign ID, used for updating a campaign
		* @param object $campaign - a valid campaign object with all required fields
		* @return string $XmlReturn - valid XML of a campaign
		*/
		private function createCampaignXml($id, $campaign)
		{
			$utility = new CTCTUtility();

			if (empty($id))
			{
				$id = ('http://api.constantcontact.com/ws/customers/' . $utility->getLogin() . '/campaigns/1100546096289');
				$standard_id = ('http://api.constantcontact.com/ws/customers/' . $utility->getLogin().'/campaigns');
			}
			else
			{
                $standard_id = $id;
            }

			$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8'?><entry xmlns='http://www.w3.org/2005/Atom' />");
			$link = $xml->addChild("link");
			$link_href = $link->addAttribute('href', '/ws/customers/' . $utility->getLogin() . '/campaigns');
			$link_rel = $link->addAttribute('rel', 'edit');
			$xml->addChild("id", $standard_id);
			$title = $xml->addChild("title", $campaign->getCampaignName());
			$title->addAttribute("type", "text");
			$xml->addChild("updated", date("Y-m-d").'T'.date("H:i:s").'+01:00');
			$author = $xml->addChild("author");
			$author->addChild("name", "Constant Contact");
			$content = $xml->addChild("content");
			$content->addAttribute("type", "application/vnd.ctct+xml");
			$campaign_node = $content->addChild("Campaign");
			$campaign_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
			$campaign_node->addAttribute("id", $id);
			$campaign_node->addChild("Name", $campaign->getCampaignName());
			$campaign_node->addChild("Status", "draft");
			$campaign_node->addChild("Date", date("Y-m-d").'T'.date("H:i:s").'+01:00');
			$campaign_node->addChild("Subject", $campaign->getSubject());
			$campaign_node->addChild("FromName", $campaign->getFromName());
			$campaign_node->addChild("ViewAsWebpage", $campaign->getVawp());
			$campaign_node->addChild("ViewAsWebpageLinkText", $campaign->getVawpLinkText());
			$campaign_node->addChild("ViewAsWebpageText", $campaign->getVawpText());
			$campaign_node->addChild("PermissionReminder", $campaign->getPermissionReminder());
			$campaign_node->addChild("PermissionReminderText", $campaign->getPermissionReminderText());
			$campaign_node->addChild("GreetingSalutation", $campaign->getGreetingSalutation());
			$campaign_node->addChild("GreetingName", $campaign->getGreetingName());
			$campaign_node->addChild("GreetingString", $campaign->getGreetingString());
			$campaign_node->addChild("OrganizationName", $campaign->getOrgName());
			$campaign_node->addChild("OrganizationAddress1", $campaign->getOrgAddr1());
			$campaign_node->addChild("OrganizationAddress2", $campaign->getOrgAddr2());
			$campaign_node->addChild("OrganizationAddress3", $campaign->getOrgAddr3());
			$campaign_node->addChild("OrganizationCity", $campaign->getOrgCity());
			$campaign_node->addChild("OrganizationState", $campaign->getOrgState());
			$campaign_node->addChild("OrganizationInternationalState", $campaign->getOrgInternationalState());
			$campaign_node->addChild("OrganizationCountry", $campaign->getOrgCountry());
			$campaign_node->addChild("OrganizationPostalCode", $campaign->getOrgPostalCode());
			$campaign_node->addChild("IncludeForwardEmail", $campaign->getIncForwardEmail());
			$campaign_node->addChild("ForwardEmailLinkText", $campaign->getForwardEmailLinkText());
			$campaign_node->addChild("IncludeSubscribeLink", $campaign->getIncSubscribeLink());
			$campaign_node->addChild("SubscribeLinkText", $campaign->getSubscribeLinkText());
			$campaign_node->addChild("EmailContentFormat", $campaign->getEmailContentFormat());
			$campaign_node->addChild("EmailContent", $campaign->getEmailContent());
			$campaign_node->addChild("EmailTextContent", $campaign->getTextVersionContent());
			$campaign_node->addChild("StyleSheet", $campaign->getStyleSheet());
			$contactLists = $campaign_node->addChild("ContactLists");
			$campaignLists = array();
			$campaignLists = $campaign->getLists();
			if ($campaignLists)
			{
				foreach ($campaignLists as $list)
				{
					$contactList = $contactLists->addChild("ContactList");
					$contactList->addAttribute("id", $list);
					$contactLink = $contactList->addChild("link");
					$contactLink->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
					$contactLink->addAttribute("href", str_replace("http://api.constantcontact.com", "", $list));
					$contactLink->addAttribute("rel", "self");
				}
			}
			$fromEmail = $campaign_node->addChild("FromEmail");
			$email_node = $fromEmail->addChild("Email");
			$email_node->addAttribute("id", "http://api.constantcontact.com" . $campaign->getFromEmailAddressLink());
			$email_link = $email_node->addChild("link");
			$email_link->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
			$email_link->addAttribute("href", $campaign->getFromEmailAddressLink());
			$email_link->addAttribute("rel", "self");
			$fromEmail->addChild("EmailAddress", $campaign->getFromEmailAddress());
			$replyEmail = $campaign_node->addChild("ReplyToEmail");
			$replyEmailNode = $replyEmail->addChild("Email");
			$replyEmailNode->addAttribute("id", "http://api.constantcontact.com" . $campaign->getReplyEmailAddressLink());
			$replyEmailLink = $replyEmailNode->addChild("link");
			$replyEmailLink->addAttribute("xmlns", "http://www.w3.org/2005/Atom");
			$replyEmailLink->addAttribute("href", $campaign->getReplyEmailAddressLink());
			$replyEmailLink->addAttribute("rel", "self");
			$replyEmail->addChild("EmailAddress", $campaign->getReplyEmailAddress());
			$sourceNode = $xml->addChild("source");
			$sourceNode->addChild("id", $standard_id);
			$sourceTitle = $sourceNode->addChild("title", "Campaigns for customer: " . $utility->getLogin());
			$sourceTitle->addAttribute("type", "text");
			$sourceLink1 = $sourceNode->addChild("link");
			$sourceLink1->addAttribute("href", "campaigns");
			$sourceLink2 = $sourceNode->addChild("link");
			$sourceLink2->addAttribute("href", "campaigns");
			$sourceLink2->addAttribute("rel", "self");
			$sourceAuthor = $sourceNode->addChild("author");
			$sourceAuthor->addChild("name", $utility->getLogin());
			$sourceNode->addChild("updated", date("Y-m-d").'T'.date("H:i:s").'+01:00');
			$xmlReturn = $xml->asXML();

			return $xmlReturn;
		}
	}
	/**
	 * ListObj class defines a list object
	 *
	 * Defines a list object, includes all campaign variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTListObj
	{
		/**
		* Construct function for the List Class
		*
		* @param array $params - an array of variables that set up a list object
		* @return object list object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setContactCount(@$params['contact_count']);
			$this->setDisplayOnSignup(@$params['display_on_signup']);
			$this->setId(@$params['id']);
			$this->setLink(@$params['link']);
			$this->setName(@$params['list_name']);
			$this->setOptInDefault(@$params['opt_in_default']);
			$this->setSortOrder(@$params['sort_order']);
			$this->setUpdated(@$params['updated']);
		}

		private $contactCount;
		private $displayOnSignup;
		private $id;
		private $link;
		private $name;
		private $optInDefault;
		private $sortOrder;
		private $updated;

		public function setContactCount( $value ) { $this->contactCount = $value; }
		public function getContactCount() { return $this->contactCount; }

		public function setDisplayOnSignup( $value ) { $this->displayOnSignup = $value; }
		public function getDisplayOnSignup() { return $this->displayOnSignup; }

		public function setId( $value ) { $this->id = $value; }
		public function getId() { return $this->id; }

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setName( $value ) { $this->name = $value; }
		public function getName() { return $this->name; }

		public function setOptInDefault( $value ) { $this->optInDefault = $value; }
		public function getOptInDefault() { return $this->optInDefault; }

		public function setSortOrder( $value ) { $this->sortOrder = $value; }
		public function getSortOrder() { return $this->sortOrder; }

		public function setUpdated( $value ) { $this->updated = $value; }
		public function getUpdated() { return $this->updated; }
	}
	/**
	 * List Collection Class for calls to the list collection API
	 *
	 * Includes functions for listing lists, creating lists, and listing members in a list
	 *
	 */
	class CTCTListsCollection
	{
		/**
		* Public function that does a POST to the Lists collection, passing a list object
		*
		* @param object $list - a valid list object with all required fields
		* @return string $code - returns success or fail code from API server
		*/
		public function createList($list)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/lists';
			$listStruct = $this->createListXml(null, $list);
			$return = $utility->httpPost($call, $listStruct);
			$code = $return['info']['http_code'];
			return $code;
		}

		/**
		* Public function that deletes a list from the account
		*
		* @param object $list - a valid list object with a valid lists link
		* @return string $code - returns success or fail code from API server
		*/
		public function deleteList($list)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $list->getLink();
			$return = $utility->httpDelete($call);
			$code = $return['info']['http_code'];
			return code;
		}

		/**
		* Public function that gets list details of a single list object passed to it.
		*
		* @param object $list - a valid list object with a valid list link
		* @return object $list - returns list object with full details
		*/
		public function getListDetails($list)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $list->getLink();
			$return = $utility->httpGet($call);
			$list = $this->createListStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $list;
			}
		}
		/**
		* Public function that gets list members of a single list object passed to it.
		*
		* @param object $list - a valid list object with a valid list link
		* @return array $List - returns first 50 contact objects that are part of that list, and a link to next 50
		*/
		public function getListMembers($list)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $list->getLink() . '/members';
			$return = $utility->httpGet($call);
			$parsedReturn = simplexml_load_string($return['xml']);


			$List = array();

			$listMembers = array();
			$pages = array();

			foreach ($parsedReturn->entry as $item)
			{
				$contact = new CTCTContact();
				$contact->setLink($item->link->Attributes()->href);
				$contact->setId($item->id);
				$contact->setFullName($item->content->ContactListMember->Name);
				$contact->setEmailAddress($item->content->ContactListMember->EmailAddress);
				$listMembers[] = $contact;
			}

			$pages[] = $parsedReturn->link[2]->Attributes();
			$pages[] = $parsedReturn->link[3]->Attributes();
			//$pages[] = $parsedReturn->link[4]->Attributes();

			$List = array($listMembers, $pages);

			if (!$return)
			{
				return false;
			}
			else
			{
				return $List;
			}
		}
		/**
		* Public function that gets a list of first 50 lists in the account
		*
		* @return array $allLists - an array of two arrays, array 1 is list objects, array 2 is link for next 50 lists
		*/
		public function getLists($page = null)
		{
			$utility = new CTCTUtility();

			// $page should look like `/ws/customers/example/lists?next=55`
			$page = ($page) ? $page : '/ws/customers/'. $utility->getLogin() .'/lists';
			$return = $utility->httpGet($utility->getApiPath() . $page);
			$allLists = array();

			$Lists = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml']);
			$listArray = array();

			foreach ($parsedReturn->entry as $item)
			{
				$listArray['link'] = ((string)$item->link->Attributes()->href);
				$listArray['id'] = ((string)$item->id);
				$listArray['updated'] = ((string)$item->updated);
				$listArray['opt_in_default'] = ((string)$item->content->ContactList->OptInDefault);
				$listArray['list_name'] = ((string)$item->content->ContactList->Name);
				$listArray['display_on_signup'] = ((string)$item->content->ContactList->DisplayOnSignup);
				$listArray['sort_order'] = ((string)$item->content->ContactList->SortOrder);
				$listArray['contact_count'] = ((string)$item->content->ContactList->ContactCount);
				$list = new CTCTListObj($listArray);
				$Lists[] = $list;
			}

			$pages['next'] = $parsedReturn->link[2];
			$pages['first'] = $parsedReturn->link[3];
			$pages['current'] = $parsedReturn->link[4];

			$allLists = array($Lists, $pages);

			return $allLists;
		}
		/**
		* Public function that updates a list
		*
		* @param string $listId - valid list ID of the list that needs to be updated
		* @param object $list - valid list object of the new updates to the list
		* @return string $code - success or fail message from the API server
		*/
		public function updateList($listId, $list)
		{
			$utility = new CTCTUtility();
			$existingList = $this->getListDetails($listId);
			$listXml = $this->createListXml($existingList->getId(), $list);
			$return = $utility->httpPut($utility->getApiPath() . $existingList->getLink(), $listXml);
			$code = $return['info']['http_code'];
			return $code;
		}

		/**
		* Private function that creates a list object from XML
		*
		* @param string $listXml - Valid list XML
		* @return object $listStruct - returns a valid list object
		*/
		private function createListStruct($listXml)
		{
			$parsedReturn = simplexml_load_string($listXml);

			$listArray['link'] = ($parsedReturn->link->Attributes()->href);
			$listArray['id'] = ($parsedReturn->id);
			$listArray['updated'] = ($parsedReturn->content->updated);
			$listArray['opt_in_default'] = ($parsedReturn->content->ContactList->OptInDefault);
			$listArray['list_name'] = ($parsedReturn->content->ContactList->Name);
			$listArray['display_on_signup'] = ($parsedReturn->content->ContactList->DisplayOnSignup);
			$listArray['sort_order'] = ($parsedReturn->content->ContactList->SortOrder);
			$listArray['contact_count'] = ($parsedReturn->content->ContactList->ContactCount);
			$list = new CTCTListObj($listArray);
			return $list;
		}
		/**
		* Private function that creates list XML
		*
		* @param string $id - optional valid list ID, used for updating a list
		* @param object $list - valid list object
		* @return string $entry - valid XML of a list
		*/
		private function createListXml($id, $list)
		{
			$utility = new CTCTUtility();

			if ( empty($id)) {
				$id = "urn:uuid:E8553C09F4xcvxCCC53F481214230867087";
			}

			$update_date = date("Y-m-d").'T'.date("H:i:s").'+01:00';
			$xml_string = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><entry xmlns='http://www.w3.org/2005/Atom'></entry>";
			$xml_object = simplexml_load_string($xml_string);
			$title_node = $xml_object->addChild("title", htmlspecialchars(("TitleNode"), ENT_QUOTES, 'UTF-8'));
			$updated_node = $xml_object->addChild("updated", htmlspecialchars(($update_date), ENT_QUOTES, 'UTF-8'));
			$author_node = $xml_object->addChild("author");
			$author_name = $author_node->addChild("name", ("CTCT Samples"));
			$id_node = $xml_object->addChild("id", htmlspecialchars(((string) $id),ENT_QUOTES, 'UTF-8'));
			$summary_node = $xml_object->addChild("summary", htmlspecialchars(("Customer document"),ENT_QUOTES, 'UTF-8'));
			$summary_node->addAttribute("type", "text");
			$content_node = $xml_object->addChild("content");
			$content_node->addAttribute("type", "application/vnd.ctct+xml");
			$contact_node = $content_node->addChild("ContactList", htmlspecialchars(("Customer document"), ENT_QUOTES, 'UTF-8'));
			$contact_node->addAttribute("xmlns", "http://ws.constantcontact.com/ns/1.0/");
			$email_node = $contact_node->addChild("Name", htmlspecialchars(($list->getName()), ENT_QUOTES, 'UTF-8'));
			$email_node = $contact_node->addChild("SortOrder", htmlspecialchars(($list->getSortOrder()), ENT_QUOTES, 'UTF-8'));
			$email_node = $contact_node->addChild("OptInDefault", htmlspecialchars(($list->getOptInDefault()), ENT_QUOTES, 'UTF-8'));

			$entry = $xml_object->asXML();
			return $entry;
		}
	}
	/**
	 * VerifiedAddress class
	 *
	 * Defines a verified address object, includes all verified address variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTVerifiedAddress
	{
		/**
		* Construct function for the VerifiedAddress Class
		*
		* @param array $params - an array of variables that set up a verifiedaddress object
		* @return object verifiedaddress object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setLink(@$params['verified_email_link']);
			$this->setId(@$params['verified_email_id']);
			$this->setEmailAddress(@$params['verified_email_address']);
			$this->setStatus(@$params['verified_email_status']);
			$this->setVerifiedTime(@$params['verified_time']);

			return $this;
		}

		private $link;
		private $id;
		private $emailAddress;
		private $status;
		private $verifiedTime;

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { $this->link; }

		public function setId( $value ) { $this->id = $value; }
		public function getId() { $this->id; }

		public function setEmailAddress( $value ) { $this->emailAddress = $value; }
		public function getEmailAddress() { $this->emailAddress; }

		public function setStatus( $value ) { $this->status = $value; }
		public function getStatus() { $this->status; }

		public function setVerifiedTime( $value ) { $this->verifiedTime = $value; }
		public function getVerifiedTime() { $this->verifiedTime; }
	}
	/**
	 * Settings Collection Class for calls to the settings collection API
	 *
	 * Includes functions for listing all verified addresses in an account
	 *
	 */
	class CTCTSettingsCollection
	{
		/**
		* Public function that gets a list of first 50 verified addresses in the account
		*
		* @return array $verifiedAddress - an array of two arrays, array 1 is verifiedaddress objects, array 2 is link for next 50 verified addresses
		*/
		public function listVerifiedAddresses()
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/settings/emailaddresses');
			$allAddresses = array();

			$addressList = array();
			$pages = array();

			$parsedReturn = simplexml_load_string($return['xml']);

			foreach ($parsedReturn->entry as $item)
			{
				$newAddress = $this->createVerifiedStruct($item);
				$addressList[] = $newAddress;
			}

			if ($parsedReturn->link[2])
			{
				$pages[] = $parsedReturn->link[2]->Attributes()->href;
			}

			$allAddresses = array($addressList, $pages);

			return $allAddresses;
		}
		/**
		* Private function that creates a verifiedaddress object from XML
		*
		* @param string $parsedXml - Valid verifiedaddress XML
		* @return object $verifiedAddress - returns a valid verifiedAddress object
		*/
		private function createVerifiedStruct($parsedXml)
		{
			$verifiedArray = array();
			$verifiedArray['verified_email_link'] = ($parsedXml->link->Attributes()->href);
			$verifiedArray['verified_email_id'] = ($parsedXml->id);
			$verifiedArray['verified_email_address'] = ($parsedXml->content->Email->EmailAddress);
			$verifiedArray['verified_email_status'] = ($parsedXml->content->Email->Status);
			$verifiedArray['verified_time'] = ($parsedXml->content->Email->VerifiedTime);

			$verifiedAddress = new CTCTVerifiedAddress($verifiedArray);
			return $verifiedAddress;
		}
	}
	/**
	 * Event class defines an event object
	 *
	 * Defines an event object, includes all campaign variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTEvent
	{
		/**
		* Construct function for the Event Class
		*
		* @param array $params - an array of variables that set up a event object
		* @return object event object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setLink(@$params['event_link']);
			$this->setName(@$params['event_name']);
			$this->setDescription(@$params['event_description']);
			$this->setTitle(@$params['event_title']);
			$this->setRegistered(@$params['registered']);
			$this->setCreatedDate(@$params['created_date']);
			$this->setStatus(@$params['event_status']);
			$this->setEventType(@$params['event_type']);
			$this->setLocation(@$params['location']);
			$this->setAddr1(@$params['event_addr1']);
			$this->setAddr2(@$params['event_addr2']);
			$this->setAddr3(@$params['event_addr3']);
			$this->setCity(@$params['event_city']);
			$this->setState(@$params['event_state']);
			$this->setCountry(@$params['event_country']);
			$this->setPostalCode(@$params['event_postalcode']);
			$this->setRegistrationUrl(@$params['registration_url']);
			$this->setStartDate(@$params['event_start']);
			$this->setEndDate(@$params['event_end']);
			$this->setPublishDate(@$params['event_publishdate']);
			$this->setWebPage(@$params['event_webpage']);
			$this->setAttendedCount(@$params['attended_count']);
			$this->setCancelledCount(@$params['cancelled_count']);
			$this->setEventFeeRequired(@$params['event_fee_required']);
			$this->setCurrencyType(@$params['currency_type']);
			$this->setRegistrationLimitDate(@$params['reg_limit_date']);
			$this->setRegistrationLimitCount(@$params['reg_limit_count']);
			$this->setRegistrationClosedManually(@$params['reg_closed_manually']);
			$this->setEarlyFeeDate(@$params['early_fee_date']);
			$this->setLateFeeDate(@$params['late_fee_date']);
			$this->setGuestLimit(@$params['guest_limit']);
			$this->setTicketing(@$params['ticketing']);

			if (@$params['payment_options'])
			{
				foreach ($params['payment_options'] as $tmp)
				{
					$this->setPaymentOptions($tmp);
				}
			}

			if (@$params['event_fees'])
			{
				foreach ($params['event_fees'] as $tmp)
				{
					$this->setEventFee($tmp);
				}
			}

			return $this;
		}

		private $link;
		private $name;
		private $description;
		private $title;
		private $registered;
		private $createdDate;
		private $status;
		private $eventType;
		private $location;
		private $addr1;
		private $addr2;
		private $addr3;
		private $city;
		private $state;
		private $country;
		private $postalCode;
		private $regstrationUrl;
		private $startDate;
		private $EndDate;
		private $publishDate;
		private $webPage;
		private $attendedCount;
		private $cancelledCount;
		private $eventFeeRequired;
		private $currencyType;
		private $paymentOptions = array();
		private $registrationLimitDate;
		private $registrationLimitCount;
		private $registrationClosedManually;
		private $earlyFeeDate;
		private $lateFeeDate;
		private $guestLimit;
		private $ticketing;
		private $eventFee = array();

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setName( $value ) { $this->name = $value; }
		public function getName() { return $this->name; }

		public function setDescription( $value ) { $this->description = $value; }
		public function getDescription() { return $this->description; }

		public function setTitle( $value ) { $this->title = $value; }
		public function getTitle() { return $this->title; }

		public function setRegistered( $value ) { $this->registered = $value; }
		public function getRegistered() { return $this->registered; }

		public function setCreatedDate( $value ) { $this->createdDate = $value; }
		public function getCreatedDate() { return $this->createdDate; }

		public function setStatus( $value ) { $this->status = $value; }
		public function getStatus() { return $this->status; }

		public function setEventType( $value ) { $this->eventType = $value; }
		public function getEventType() { return $this->eventType; }

		public function setLocation( $value ) { $this->location = $value; }
		public function getLocation() { return $this->location; }

		public function setAddr1( $value ) { $this->addr1 = $value; }
		public function getAddr1() { return $this->addr1; }

		public function setAddr2( $value ) { $this->addr2 = $value; }
		public function getAddr2() { return $this->addr2; }

		public function setAddr3( $value ) { $this->addr3 = $value; }
		public function getAddr3() { return $this->addr3; }

		public function setCity( $value ) { $this->city = $value; }
		public function getCity() { return $this->city; }

		public function setState( $value ) { $this->state = $value; }
		public function getState() { return $this->state; }

		public function setCountry( $value ) { $this->country = $value; }
		public function getCountry() { return $this->country; }

		public function setPostalCode( $value ) { $this->postalCode = $value; }
		public function getPostalCode() { return $this->postalCode; }

		public function setRegistrationUrl( $value ) { $this->regstrationUrl = $value; }
		public function getRegistrationUrl() { return $this->regstrationUrl; }

		public function setStartDate( $value ) { $this->startDate = $value; }
		public function getStartDate() { return $this->startDate; }

		public function setEndDate( $value ) { $this->EndDate = $value; }
		public function getEndDate() { return $this->EndDate; }

		public function setPublishDate( $value ) { $this->publishDate = $value; }
		public function getPublishDate() { return $this->publishDate; }

		public function setWebPage( $value ) { $this->webPage = $value; }
		public function getWebPage() { return $this->webPage; }

		public function setAttendedCount( $value ) { $this->attendedCount = $value; }
		public function getAttendedCount() { return $this->attendedCount; }

		public function setCancelledCount( $value ) { $this->cancelledCount = $value; }
		public function getCancelledCount() { return $this->cancelledCount; }

		public function setEventFeeRequired( $value ) { $this->eventFeeRequired = $value; }
		public function getEventFeeRequired() { return $this->eventFeeRequired; }

		public function setCurrencyType( $value ) { $this->currencyType = $value; }
		public function getCurrencyType() { return $this->currencyType; }

		public function setPaymentOptions( $value ) { $this->paymentOptions[] = $value; }
		public function getPaymentOptions() { return $this->paymentOptions; }

		public function setRegistrationLimitDate( $value ) { $this->registrationLimitDate = $value; }
		public function getRegistrationLimitDate() { return $this->registrationLimitDate; }

		public function setRegistrationLimitCount( $value ) { $this->registrationLimitCount = $value; }
		public function getRegistrationLimitCount() { return $this->registrationLimitCount; }

		public function setRegistrationClosedManually( $value ) { $this->registrationClosedManually = $value; }
		public function getRegistrationClosedManually() { return $this->registrationClosedManually; }

		public function setEarlyFeeDate( $value ) { $this->earlyFeeDate = $value; }
		public function getEarlyFeeDate() { return $this->earlyFeeDate; }

		public function setLateFeeDate( $value ) { $this->lateFeeDate = $value; }
		public function getLateFeeDate() { return $this->lateFeeDate; }

		public function setGuestLimit( $value ) { $this->guestLimit = $value; }
		public function getGuestLimit() { return $this->guestLimit; }

		public function setTicketing( $value ) { $this->ticketing = $value; }
		public function getTicketing() { return $this->ticketing; }

		public function setEventFee( $value ) { $this->eventFee[] = $value; }
		public function getEventFee() { return $this->eventFee; }
	}
	/**
	 * Registrant class defines a registrant object
	 *
	 * Defines a registrant object, includes all campaign variables as well as the
	 * getters and setters for variables
	 *
	 */
	class CTCTRegistrant
	{
		/**
		* Construct function for the Registrant Class
		*
		* @param array $params - an array of variables that set up a registrant object
		* @return object registrant object with passed variables set to the object
		*/
		public function __construct($params = array())
		{
			$this->setLink(@$params['registrant_link']);
			$this->setLastName(@$params['last_name']);
			$this->setFirstName(@$params['first_name']);
			$this->setEmailAddress(@$params['email_address']);
			$this->setPersonalLabel(@$params['personal_label']);
			$this->setPersonalAddr1(@$params['personal_addr1']);
			$this->setPersonalAddr2(@$params['personal_addr2']);
			$this->setPersonalAddr3(@$params['personal_addr3']);
			$this->setPersonalCity(@$params['personal_city']);
			$this->setPersonalState(@$params['personal_state']);
			$this->setPersonalPostalCode(@$params['personal_postalcode']);
			$this->setPersonalProvince(@$params['personal_province']);
			$this->setPersonalCountry(@$params['personal_country']);
			$this->setPersonalPhone(@$params['personal_phone']);
			$this->setPersonalCellPhone(@$params['personal_cellphone']);
			$this->setBusinessLabel(@$params['business_label']);
			$this->setBusinessCompany(@$params['business_company']);
			$this->setBusinessJobTitle(@$params['business_jobtitle']);
			$this->setBusinessDepartment(@$params['business_department']);
			$this->setBusinessAddr1(@$params['business_addr1']);
			$this->setBusinessAddr2(@$params['business_addr2']);
			$this->setBusinessAddr3(@$params['business_addr3']);
			$this->setBusinessCity(@$params['business_city']);
			$this->setBusinessState(@$params['business_state']);
			$this->setBusinessPostalCode(@$params['business_postalcode']);
			$this->setBusinessProvince(@$params['business_province']);
			$this->setBusinessCountry(@$params['business_country']);
			$this->setBusinessPhone(@$params['business_phone']);
			$this->setBusinessFax(@$params['business_fax']);
			$this->setBusinessWebSite(@$params['business_website']);
			$this->setBusinessBlog(@$params['business_blog']);
			$this->setRegistrationStatus(@$params['reg_status']);
			$this->setRegistrationDate(@$params['reg_date']);
			$this->setGuestCount(@$params['guest_count']);
			$this->setPaymentStatus(@$params['payment_status']);
			$this->setOrderAmount(@$params['order_amount']);
			$this->setCurrencyType(@$params['currency_type']);
			$this->setPaymentType(@$params['payment_type']);

			if (@$params['custom_field1'])
			{
				foreach ($params['custom_field1'] as $tmp)
				{
					$this->setCustomField1($tmp);
				}
			}

			if (@$params['custom_field2'])
			{
				foreach ($params['custom_field2'] as $tmp)
				{
					$this->setCustomField2($tmp);
				}
			}

			if (@$params['costs'])
			{
				foreach ($params['costs'] as $tmp)
				{
					$this->setCost($tmp);
				}
			}

			return $this;
		}

		private $link;
		private $lastName;
		private $firstName;
		private $emailAddress;
		private $personalLabel;
		private $personalAddr1;
		private $personalAddr2;
		private $personalAddr3;
		private $personalCity;
		private $personalState;
		private $personalPostalCode;
		private $personalProvince;
		private $personalCountry;
		private $personalPhone;
		private $personalCellPhone;
		private $businessLabel;
		private $businessCompany;
		private $businessJobTitle;
		private $businessDepartment;
		private $businessAddr1;
		private $businessAddr2;
		private $businessAddr3;
		private $businessCity;
		private $businessState;
		private $businessPostalCode;
		private $businessProvince;
		private $businessCountry;
		private $businessPhone;
		private $businessFax;
		private $businessWebSite;
		private $businessBlog;
		private $customField1 = array();
		private $customField2 = array();
		private $registrationStatus;
		private $registrationDate;
		private $guestCount;
		private $paymentStatus;
		private $orderAmount;
		private $currencyType;
		private $paymentType;
		private $costs = array();

		public function setLink( $value ) { $this->link = $value; }
		public function getLink() { return $this->link; }

		public function setLastName( $value ) { $this->lastName = $value; }
		public function getLastName() { return $this->lastName; }

		public function setFirstName( $value ) { $this->firstName = $value; }
		public function getFirstName() { return $this->firstName; }

		public function setEmailAddress( $value ) { $this->emailAddress = $value; }
		public function getEmailAddress() { return $this->emailAddress; }

		public function setPersonalLabel( $value ) { $this->personalLabel = $value; }
		public function getPersonalLabel() { return $this->personalLabel; }

		public function setPersonalAddr1( $value ) { $this->personalAddr1 = $value; }
		public function getPersonalAddr1() { return $this->personalAddr1; }

		public function setPersonalAddr2( $value ) { $this->personalAddr2 = $value; }
		public function getPersonalAddr2() { return $this->personalAddr2; }

		public function setPersonalAddr3( $value ) { $this->personalAddr3 = $value; }
		public function getPersonalAddr3() { return $this->personalAddr3; }

		public function setPersonalCity( $value ) { $this->personalCity = $value; }
		public function getPersonalCity() { return $this->personalCity; }

		public function setPersonalState( $value ) { $this->personalState = $value; }
		public function getPersonalState() { return $this->personalState; }

		public function setPersonalPostalCode( $value ) { $this->personalPostalCode = $value; }
		public function getPersonalPostalCode() { return $this->personalPostalCode; }

		public function setPersonalProvince( $value ) { $this->personalProvince = $value; }
		public function getPersonalProvince() { return $this->personalProvince; }

		public function setPersonalCountry( $value ) { $this->personalCountry = $value; }
		public function getPersonalCountry() { return $this->personalCountry; }

		public function setPersonalPhone( $value ) { $this->personalPhone = $value; }
		public function getPersonalPhone() { return $this->personalPhone; }

		public function setPersonalCellPhone( $value ) { $this->personalCellPhone = $value; }
		public function getPersonalCellPhone() { return $this->personalCellPhone; }

		public function setBusinessLabel( $value ) { $this->businessLabel = $value; }
		public function getBusinessLabel() { return $this->businessLabel; }

		public function setBusinessCompany( $value ) { $this->businessCompany = $value; }
		public function getBusinessCompany() { return $this->businessCompany; }

		public function setBusinessJobTitle( $value ) { $this->businessJobTitle = $value; }
		public function getBusinessJobTitle() { return $this->businessJobTitle; }

		public function setBusinessDepartment( $value ) { $this->businessDepartment = $value; }
		public function getBusinessDepartment() { return $this->businessDepartment; }

		public function setBusinessAddr1( $value ) { $this->businessAddr1 = $value; }
		public function getBusinessAddr1() { return $this->businessAddr1; }

		public function setBusinessAddr2( $value ) { $this->businessAddr2 = $value; }
		public function getBusinessAddr2() { return $this->businessAddr2; }

		public function setBusinessAddr3( $value ) { $this->businessAddr3 = $value; }
		public function getBusinessAddr3() { return $this->businessAddr3; }

		public function setBusinessCity( $value ) { $this->businessCity = $value; }
		public function getBusinessCity() { return $this->businessCity; }

		public function setBusinessState( $value ) { $this->businessState = $value; }
		public function getBusinessState() { return $this->businessState; }

		public function setBusinessPostalCode( $value ) { $this->businessPostalCode = $value; }
		public function getBusinessPostalCode() { return $this->businessPostalCode; }

		public function setBusinessProvince( $value ) { $this->businessProvince = $value; }
		public function getBusinessProvince() { return $this->businessProvince; }

		public function setBusinessCountry( $value ) { $this->businessCountry = $value; }
		public function getBusinessCountry() { return $this->businessCountry; }

		public function setBusinessPhone( $value ) { $this->businessPhone = $value; }
		public function getBusinessPhone() { return $this->businessPhone; }

		public function setBusinessFax( $value ) { $this->businessFax = $value; }
		public function getBusinessFax() { return $this->businessFax; }

		public function setBusinessWebSite( $value ) { $this->businessWebSite = $value; }
		public function getBusinessWebSite() { return $this->businessWebSite; }

		public function setBusinessBlog( $value ) { $this->businessBlog = $value; }
		public function getBusinessBlog() { return $this->businessBlog; }

		public function setCustomField( $value ) { $this->customField[] = $value; }
		public function getCustomField() { return $this->customField; }

		public function setRegistrationStatus( $value ) { $this->registrationStatus = $value; }
		public function getRegistrationStatus() { return $this->registrationStatus; }

		public function setRegistrationDate( $value ) { $this->registrationDate = $value; }
		public function getRegistrationDate() { return $this->registrationDate; }

		public function setGuestCount( $value ) { $this->guestCount = $value; }
		public function getGuestCount() { return $this->guestCount; }

		public function setPaymentStatus( $value ) { $this->paymentStatus = $value; }
		public function getPaymentStatus() { return $this->paymentStatus; }

		public function setOrderAmount( $value ) { $this->orderAmount = $value; }
		public function getOrderAmount() { return $this->orderAmount; }

		public function setCurrencyType( $value ) { $this->currencyType = $value; }
		public function getCurrencyType() { return $this->currencyType; }

		public function setPaymentType( $value ) { $this->paymentType = $value; }
		public function getPaymentType() { return $this->paymentType; }

		public function setCost( $value ) { $this->costs[] = $value; }
		public function getCost() { return $this->costs; }

		public function setCustomField1( $value ) { $this->customField1[] = $value; }
		public function getCustomField1() { return $this->customField1; }

		public function setCustomField2( $value ) { $this->customField2[] = $value; }
		public function getCustomField2() { return $this->customField2; }
	}
	/**
	 * Event Collection Class for calls to the event collection API
	 *
	 * Includes functions for listing all events within the account, specific event details,
	 * also listing registrants and registrant details.
	 *
	 */
	class CTCTEventCollection
	{
		/**
		* Public function that gets a list of first 50 events in the account
		*
		* @return array $allEvents - an array of two arrays, array 1 is event objects, array 2 is link for next 50 events
		*/
		public function listEvents()
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . '/ws/customers/'. $utility->getLogin() .'/events');
			$allEvents = array();
			$eventArray = array();
			$returnedXml = str_replace('atom:', '', $return['xml']);
			$eventList = array();
			$pages = array();
			$xml = simplexml_load_string($returnedXml);

			foreach ($xml->entry as $item)
			{
				$eventArray['event_link'] = ($item->link['href']);
				$eventArray['event_name'] = ($item->content->Event->Name);
				$eventArray['event_description'] = ($item->content->Event->Description);
				$eventArray['event_title'] = ($item->Event->content->Title);
				$eventArray['registered'] = ($item->content->Event->Registered);
				$eventArray['created_date'] = ($item->content->Event->CreatedDate);
				$eventArray['event_status'] = ($item->content->Event->Status);
				$eventArray['event_type'] = ($item->content->Event->EventType);
				$eventArray['event_start'] = ($item->content->Event->StartDate);
				$eventArray['event_end'] = ($item->content->Event->EndDate);
				$eventArray['event_publishdate'] = ($item->content->Event->PublishDate);
				$eventArray['event_webpage'] = ($item->content->Event->WebPage);
				$eventArray['attended_count'] = ($item->content->Event->AttendedCount);
				$eventArray['cancelled_count'] = ($item->content->Event->CancelledCount);
				$eventArray['location'] = ($item->content->Event->EventLocation->Location);
				$eventArray['event_addr1'] = ($item->content->Event->EventLocation->Address1);
				$eventArray['event_addr2'] = ($item->content->Event->EventLocation->Address2);
				$eventArray['event_addr3'] = ($item->content->Event->EventLocation->Address3);
				$eventArray['event_city'] = ($item->content->Event->EventLocation->City);
				$eventArray['event_state'] = ($item->content->Event->EventLocation->State);
				$eventArray['event_country'] = ($item->content->Event->EventLocation->Country);
				$eventArray['event_postalcode'] = ($item->content->Event->EventLocation->PostalCode);
				$event = new CTCTEvent($eventArray);
				$eventList[] = $event;
			}

			if ($xml->link[2])
			{
				$pages[] = $xml->link[2]->Attributes()->href;
			}

			$allEvents= array($eventList, $pages);

			return $allEvents;
		}
		/**
		* Public function that gets event details of a single event object passed to it.
		*
		* @param object $event - a valid event object with a valid event link
		* @return object $event - returns event object with full details
		*/
		public function listEventDetails($event)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $event->getLink();
			$return = $utility->httpGet($call);
			$event = $this->createEventStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $event;
			}
		}
		/**
		* Public function that gets a list of first 50 registrants in an event
		*
		* @param object $event - a valid event object with a valid event link
		* @return array $allRegistrants - an array of two arrays, array 1 is registrant objects, array 2 is link for next 50 registrant
		*/
		public function listEventRegistrants($event)
		{
			$utility = new CTCTUtility();
			$return = $utility->httpGet($utility->getApiPath() . $event->getLink() . '/registrants');
			$allRegistrants = array();
			$registrantArray = array();
			$returnedXml = str_replace('atom:', '', $return['xml']);
			$registrantList = array();
			$pages = array();
			$xml = simplexml_load_string($returnedXml);

			foreach ($xml->entry as $item)
			{
				$registrantArray['registrant_link'] = ($item->link['href']);
				$registrantArray['last_name'] = ($item->content->Registrant->LastName);
				$registrantArray['first_name'] = ($item->content->Registrant->FirstName);
				$registrantArray['email_address'] = ($item->content->Registrant->EmailAddress);
				$registrantArray['reg_status'] = ($item->content->Registrant->RegistrationStatus);
				$registrantArray['reg_date'] = ($item->content->Registrant->RegistrationDate);
				$registrantArray['guest_count'] = ($item->content->Registrant->GuestCount);
				$registrantArray['payment_status'] = ($item->content->Registrant->PaymentStatus);
				$registrant = new CTCTRegistrant($registrantArray);
				$registrantList[] = $registrant;
			}

			if ($xml->link[2])
			{
				$pages[] = $xml->link[2]->Attributes()->href;
			}

			$allRegistrants = array($registrantList, $pages);

			return $allRegistrants;
		}
		/**
		* Public function that gets event details of a single event object passed to it.
		*
		* @param object $registrant - a valid registrant object with a valid registrant link
		* @return object $registrant - returns registrant object with full details
		*/
		public function listRegistrantDetails($registrant)
		{
			$utility = new CTCTUtility();
			$call = $utility->getApiPath() . $registrant->getLink();
			$return = $utility->httpGet($call);
			$registrant = $this->createRegistrantStruct($return['xml']);
			if (!$return)
			{
				return false;
			}
			else
			{
				return $registrant;
			}
		}
		/**
		* Private function that creates an event object from XML
		*
		* @param string $eventXml - Valid event XML
		* @return object $eventStruct - returns a valid event object
		*/
		private function createEventStruct($eventXml)
		{
			$eventArray = array();
			$eventXml = str_replace('atom:', '', $eventXml);
			$parsedReturn = simplexml_load_string($eventXml);
			$eventArray['event_link'] = ($parsedReturn->link->Attributes()->href);
			$eventArray['event_name'] = ($parsedReturn->content->Event->Name);
			$eventArray['event_description'] = ($parsedReturn->content->Event->Description);
			$eventArray['event_title'] = ($parsedReturn->content->Event->Title);
			$eventArray['registered'] = ($parsedReturn->content->Event->Registered);
			$eventArray['created_date'] = ($parsedReturn->content->Event->CreatedDate);
			$eventArray['event_status'] = ($parsedReturn->content->Event->Status);
			$eventArray['event_type'] = ($parsedReturn->content->Event->EventType);
			$eventArray['location'] = ($parsedReturn->content->Event->EventLocation->Location);
			$eventArray['event_addr1'] = ($parsedReturn->content->Event->EventLocation->Address1);
			$eventArray['event_addr2'] = ($parsedReturn->content->Event->EventLocation->Address2);
			$eventArray['event_addr3'] = ($parsedReturn->content->Event->EventLocation->Address3);
			$eventArray['event_city'] = ($parsedReturn->content->Event->EventLocation->City);
			$eventArray['event_state'] = ($parsedReturn->content->Event->EventLocation->State);
			$eventArray['event_country'] = ($parsedReturn->content->Event->EventLocation->Country);
			$eventArray['event_postalcode'] = ($parsedReturn->content->Event->EventLocation->PostalCode);
			$eventArray['registration_url'] = ($parsedReturn->content->Event->RegistrationURL);
			$eventArray['event_start'] = ($parsedReturn->content->Event->StartDate);
			$eventArray['event_end'] = ($parsedReturn->content->Event->EndDate);
			$eventArray['event_publishdate'] = ($parsedReturn->content->Event->PublishDate);
			$eventArray['event_webpage'] = ($parsedReturn->content->Event->WebPage);
			$eventArray['attended_count'] = ($parsedReturn->content->Event->AttendedCount);
			$eventArray['cancelled_count'] = ($parsedReturn->content->Event->CancelledCount);
			$eventArray['event_fee_required'] = ($parsedReturn->content->Event->EventFeeRequired);
			$eventArray['currency_type'] = ($parsedReturn->content->Event->CurrencyType);
			$eventArray['reg_limit_date'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->RegistrationLimitDate);
			$eventArray['reg_limit_count'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->RegistrationLimitCount);
			$eventArray['reg_closed_manually'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->RegistrationClosedManually);
			$eventArray['early_fee_date'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->EarlyFeeDate);
			$eventArray['late_fee_date'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->LateFeeDate);
			$eventArray['guest_limit'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->GuestLimit);
			$eventArray['ticketing'] = ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->Ticketing);

			if ($parsedReturn->content->Event->PaymentOptions)
			{
				foreach ($parsedReturn->content->Event->PaymentOptions->PaymentOption as $item)
				{
					$type = $item->Type;
					if ($type == 'PAYPAL')
					{
						$eventArray['payment_options']['paypal']['email_address'] = (trim((string) $item->PayPalAccountEmail));
					}
					if ($type == 'CHECK')
					{
						$eventArray['payment_options']['check']['address1'] = (trim((string) $item->PaymentAddress->Address1));
						$eventArray['payment_options']['check']['address2'] = (trim((string) $item->PaymentAddress->Address2));
						$eventArray['payment_options']['check']['address3'] = (trim((string) $item->PaymentAddress->Address3));
						$eventArray['payment_options']['check']['city'] = (trim((string) $item->PaymentAddress->City));
						$eventArray['payment_options']['check']['state'] = (trim((string) $item->PaymentAddress->State));
						$eventArray['payment_options']['check']['country'] = (trim((string) $item->PaymentAddress->Country));
						$eventArray['payment_options']['check']['postal_code'] = (trim((string) $item->PaymentAddress->PostalCode));
					}
					if ($type == 'DOOR')
					{
						$eventArray['payment_options']['door']['DOOR'] = 'Pay at the Door';
					}
				}
			}

			if ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->EventFees)
			{
				$int = 1;
				foreach ($parsedReturn->content->Event->RegistrationTypes->RegistrationType->EventFees->EventFee as $item)
				{
					$int++;
					$eventArray['event_fees']['event_fee' . $int]['Label'] = (trim((string) $item->Label));
					$eventArray['event_fees']['event_fee' . $int]['Fee'] = (trim((string) $item->Fee));
					$eventArray['event_fees']['event_fee' . $int]['EarlyFee'] = (trim((string) $item->EarlyFee));
					$eventArray['event_fees']['event_fee' . $int]['LateFee'] = (trim((string) $item->LateFee));
					$eventArray['event_fees']['event_fee' . $int]['FeeScope'] = (trim((string) $item->FeeScope));
				}
			}
			$eventStruct = new CTCTEvent($eventArray);
			return $eventStruct;

		}
		/**
		* Private function that creates an registrant object from XML
		*
		* @param string $registrantXml - Valid registrant XML
		* @return object $registrantStruct - returns a valid registrant object
		*/
		private function createRegistrantStruct($registrantXml)
		{
			$registrantArray = array();
			$registrantXml = str_replace('atom:', '', $registrantXml);
			$parsedReturn = simplexml_load_string($registrantXml);
			$registrantArray['registrant_link'] = ($parsedReturn->link->Attributes()->href);
			$registrantArray['last_name'] = ($parsedReturn->content->Registrant->LastName);
			$registrantArray['first_name'] = ($parsedReturn->content->Registrant->FirstName);
			$registrantArray['email_address'] = ($parsedReturn->content->Registrant->EmailAddress);
			$registrantArray['personal_label'] = ($parsedReturn->content->Registrant->PersonalInformation->Label);
			$registrantArray['personal_addr1'] = ($parsedReturn->content->Registrant->PersonalInformation->Address1);
			$registrantArray['personal_addr2'] = ($parsedReturn->content->Registrant->PersonalInformation->Address2);
			$registrantArray['personal_addr3'] = ($parsedReturn->content->Registrant->PersonalInformation->Address3);
			$registrantArray['personal_city'] = ($parsedReturn->content->Registrant->PersonalInformation->City);
			$registrantArray['personal_state'] = ($parsedReturn->content->Registrant->PersonalInformation->State);
			$registrantArray['personal_postalcode'] = ($parsedReturn->content->Registrant->PersonalInformation->PostalCode);
			$registrantArray['personal_province'] = ($parsedReturn->content->Registrant->PersonalInformation->Province);
			$registrantArray['personal_country'] = ($parsedReturn->content->Registrant->PersonalInformation->Country);
			$registrantArray['personal_phone'] = ($parsedReturn->content->Registrant->PersonalInformation->Phone);
			$registrantArray['personal_cellphone'] = ($parsedReturn->content->Registrant->PersonalInformation->CellPhone);
			$registrantArray['business_label'] = ($parsedReturn->content->Registrant->BusinessInformation->Label);
			$registrantArray['business_company'] = ($parsedReturn->content->Registrant->BusinessInformation->Company);
			$registrantArray['business_jobtitle'] = ($parsedReturn->content->Registrant->BusinessInformation->JobTitle);
			$registrantArray['business_department'] = ($parsedReturn->content->Registrant->BusinessInformation->Department);
			$registrantArray['business_addr1'] = ($parsedReturn->content->Registrant->BusinessInformation->Address1);
			$registrantArray['business_addr2'] = ($parsedReturn->content->Registrant->BusinessInformation->Address2);
			$registrantArray['business_addr3'] = ($parsedReturn->content->Registrant->BusinessInformation->Address3);
			$registrantArray['business_city'] = ($parsedReturn->content->Registrant->BusinessInformation->City);
			$registrantArray['business_state'] = ($parsedReturn->content->Registrant->BusinessInformation->State);
			$registrantArray['business_postalcode'] = ($parsedReturn->content->Registrant->BusinessInformation->PostalCode);
			$registrantArray['business_province'] = ($parsedReturn->content->Registrant->BusinessInformation->Province);
			$registrantArray['business_country'] = ($parsedReturn->content->Registrant->BusinessInformation->Country);
			$registrantArray['business_phone'] = ($parsedReturn->content->Registrant->BusinessInformation->Phone);
			$registrantArray['business_fax'] = ($parsedReturn->content->Registrant->BusinessInformation->Fax);
			$registrantArray['business_website'] = ($parsedReturn->content->Registrant->BusinessInformation->Website);
			$registrantArray['business_blog'] = ($parsedReturn->content->Registrant->BusinessInformation->Blog);
			$registrantArray['reg_status'] = ($parsedReturn->content->Registrant->RegistrationStatus);
			$registrantArray['reg_date'] = ($parsedReturn->content->Registrant->RegistrationDate);
			$registrantArray['guest_count'] = ($parsedReturn->content->Registrant->GuestCount);
			$registrantArray['payment_status'] = ($parsedReturn->content->Registrant->PaymentStatus);
			$registrantArray['order_amount'] = ($parsedReturn->content->Registrant->OrderAmount);
			$registrantArray['currency_type'] = ($parsedReturn->content->Registrant->CurrencyType);
			$registrantArray['payment_type'] = ($parsedReturn->content->Registrant->PaymentType);

			if ($parsedReturn->content->Registrant->CustomInformation1)
			{
				$int = 1;
				foreach ($parsedReturn->content->Registrant->CustomInformation1->CustomField as $item)
				{
					$int++;
					$registrantArray['custom_field1']['custom_field' . $int]['Question'] = (trim((string) $item->Question));
					$registrantArray['custom_field1']['custom_field' . $int]['Answer'] = (trim((string) $item->Answers->Answer));
				}
			}
			if ($parsedReturn->content->Registrant->CustomInformation2)
			{
				$int = 1;
				foreach ($parsedReturn->content->Registrant->CustomInformation2->CustomField as $item)
				{
					$int++;
					$registrantArray['custom_field2']['custom_field' . $int]['Question'] = (trim((string) $item->Question));
					$registrantArray['custom_field2']['custom_field' . $int]['Answer'] = (trim((string) $item->Answers->Answer));
				}
			}
			if ($parsedReturn->content->Registrant->Costs)
			{
				$int = 1;
				foreach ($parsedReturn->content->Registrant->Costs->Cost as $item)
				{
					$int++;
					$registrantArray['costs']['cost' . $int]['Count'] = (trim((string) $item->Count));
					$registrantArray['costs']['cost' . $int]['FeeType'] = (trim((string) $item->FeeType));
					$registrantArray['costs']['cost' . $int]['Rate'] = (trim((string) $item->Rate));
					$registrantArray['costs']['cost' . $int]['Total'] = (trim((string) $item->Total));

				}
			}
			$registrantStruct = new CTCTRegistrant($registrantArray);
			return $registrantStruct;
		}
	}
