<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/03/14
 * Time: 9:55
 */

class ApiManager
{
    private $accessorProvider;
    private $apiProvider;
    private $filesProvider;
    private $calendarManager;

    public function __construct(AccessorProvider $accessorProvider = NULL, ApiProvider $apiProvider = NULL, FilesProvider $filesProvider = NULL,ICalendarManager $calendarManager = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;

        if(!$apiProvider) $apiProvider = new ApiProvider();
        $this->apiProvider = $apiProvider;

        if(!$filesProvider) $filesProvider = new FilesProvider();
        $this->filesProvider = $filesProvider;

        if(!$calendarManager) $calendarManager = CalendarManager::getInstance();
        $this->calendarManager = $calendarManager;
    }

    public function getProcessDataU1db($json)
    {
        return $this->accessorProvider->getProcessDataU1db($json);
    }

    public function getMetadata($path,$fileId = NULL)
    {
        $metadata = $this->apiProvider->getMetadata($this->getUrl(),$this->getToken(),$fileId);
        $respuesta = '';
        if($metadata) {
            $respuesta = json_encode($metadata);
            $files = array();
            if(isset($metadata->contents) && count($metadata->contents) > 0) {
                $files = $metadata->contents;
                if ($fileId === NULL) {
                    unset($metadata->contents);
                    array_push($files,$metadata);
                }
            }
            $file = array();
            $file['file_id'] = $fileId===NULL?'null':$fileId;
            $file['user_eyeos'] = $_SESSION['user'];
            $query = $this->callProcessU1db('select',$file);
            if($query == '[]') {
                foreach($files as $file) {
                    $insert = true;
                    if($file->file_id !== 'null') {
                        $insert = $this->filesProvider->createFile($path . "/" . $file->filename,$file->is_folder);
                    }
                    if($insert) {
                        $this->callProcessU1db('insert',$this->setUserEyeos($file));
                    }
                }
            } else {
                $dataU1db = json_decode($query);
                if ($dataU1db){
                    for($i = 0;$i < count($files);$i++) {
                        if($this->search($dataU1db,"file_id",$files[$i]->file_id) === false){
                            if($this->filesProvider->createFile($path . "/" . $files[$i]->filename,$files[$i]->is_folder)) {
                                $this->callProcessU1db('insert',$this->setUserEyeos($files[$i]));
                            }
                        } else {
                            $filenameDb = $this->getFilename($dataU1db,"file_id",$files[$i]->file_id,"filename");
                            if ($filenameDb !== $files[$i]->filename){
                                if($this->filesProvider->renameFile($path . "/" . $filenameDb, $files[$i]->filename)) {
                                    $this->callProcessU1db('update',$this->setUserEyeos($files[$i]));
                                }
                            }
                        }
                    }
                    for($i = 0;$i < count($dataU1db);$i++) {
                        if($this->search($files,"file_id",$dataU1db[$i]->file_id) === false && $metadata->file_id !== $dataU1db[$i]->file_id){
                            if($this->filesProvider->deleteFile($path . "/" . $dataU1db[$i]->filename, $dataU1db[$i]->is_folder)) {
                                $this->callProcessU1db('delete',$this->setUserEyeos($dataU1db[$i]));
                            }
                        }
                    }
                }
            }
        }
        return $respuesta;
    }

    public function createFile($filename,$file,$filesize,$pathParent,$folderParent = NULL)
    {
        $respuesta = '';
        $parentId = -1;
        if($folderParent !== NULL) {
            $lista = array();
            $lista['path'] = $pathParent;
            $lista['folder'] = $folderParent;
            $lista['user_eyeos'] = $_SESSION['user'];
            $u1db = json_decode($this->callProcessU1db('parent',$lista));
            if($u1db !== NULL) {
                $parentId = $u1db[0]->file_id === "null"?NULL:$u1db[0]->file_id;
            }
        } else {
            $parentId = NULL;
        }
        if($parentId !== -1) {
            $metadata = $this->apiProvider->createFile($this->getUrl(),$this->getToken(),$filename,$file,$filesize,$parentId);

            if(array_key_exists("file_id",$metadata)) {
                $file = array();
                $file['file_id'] = $metadata->file_id;
                $file['user_eyeos'] = $_SESSION['user'];
                $query = $this->callProcessU1db('select',$file);

                if($query == '[]') {
                    $this->callProcessU1db('insert',$this->setUserEyeos($metadata));
                } else {
                    $dataU1db = json_decode($query);
                    if($dataU1db) {
                        $this->callProcessU1db("update",$this->setUserEyeos($metadata));
                    }
                }
                $respuesta = json_encode($metadata);
            }

        }

        return $respuesta;
    }

    public function createFolder($foldername,$idParent = NULL)
    {
        $metadata = $this->apiProvider->createFolder($this->getUrl(),$this->getToken(),$foldername,$idParent);
        $this->callProcessU1db('insert',$this->setUserEyeos($metadata));
        return json_encode($metadata);
    }

    public function deleteComponent($idComponent,$folder = false)
    {
        $result = false;
        if($this->apiProvider->deleteComponent($this->getUrl(),$this->getToken(),$idComponent)) {
            $file = array();
            $file['file_id'] = $idComponent;
            $file['user_eyeos'] = $_SESSION['user'];
            $type = $folder?'deleteFolder':'delete';
            $result = $this->callProcessU1db($type,$file) === 'true'?true:false;
        }
        return $result;
    }

    public function renameFile($idFile,$fileName,$file,$filesize,$idParent=NULL)
    {
        $result = '';
        if($this->deleteComponent($idFile)) {
            $metadata = $this->apiProvider->createFile($this->getUrl(),$this->getToken(),$fileName,$file,$filesize,$idParent);
            $this->callProcessU1db('insert',$this->setUserEyeos($metadata));
            $result = json_encode($metadata);
        }
        return $result;
    }

    public function downloadFile($idFile)
    {
        return $this->apiProvider->downloadFile($this->getUrl(),$this->getToken(),$idFile);
    }

    public function renameFolder($idFolder,$folderName,$idParent = NULL)
    {
        $result = '';
        if($this->deleteComponent($idFolder,true)) {
            $metadata = $this->apiProvider->createFolder($this->getUrl(),$this->getToken(),$folderName,$idParent);
            $this->callProcessU1db('insert',$this->setUserEyeos($metadata));
            $result = json_encode($metadata);
        }
        return $result;
    }

    public function createEvent($event)
    {
        return json_decode($this->callProcessU1db("insertEvent",$event));
    }

    public function deleteEvent($event)
    {
        return json_decode($this->callProcessU1db("deleteEvent",$event));
    }

    public function updateEvent($event)
    {
        return json_decode($this->callProcessU1db("updateEvent",$event));
    }

    public function selectEvent($event)
    {
        return json_decode($this->callProcessU1db("selectEvent",$event));
    }

    public function synchronizeCalendar($calendarId,$user)
    {
        $cal = $this->calendarManager->getCalendarById($calendarId);
        if($cal) {
            $eventsCalendar = $this->calendarManager->getAllEventsByPeriod($cal,null,null);
            $event['type'] = 'event';
            $event['user_eyeos'] = $user;
            $event['calendar'] = $cal->getName();
            $u1dbCalendar = $this->selectEvent($event);

            if (is_array($u1dbCalendar)) {
                if(count($u1dbCalendar) === 0 && count($eventsCalendar) > 0) {
                    foreach($eventsCalendar as $eventCalendar) {
                        $eventInsert = $this->getEventInsert($eventCalendar,$user,$cal->getName());
                        $this->createEvent($eventInsert);
                    }
                } else {
                    $arrayInsert = array();

                    for($i = 0;$i < count($u1dbCalendar);$i++) {
                        $encontrado = false;
                        for($j = 0;$j < count($eventsCalendar);$j++) {
                            if($this->sameEvent($u1dbCalendar[$i],$eventsCalendar[$j])) {
                                $encontrado = true;
                                if($u1dbCalendar[$i]->status === "DELETED") {
                                    $this->calendarManager->deleteEvent($eventsCalendar[$j]);
                                    unset($eventsCalendar[$j]);
                                    $eventsCalendar = array_values($eventsCalendar);
                                } else {
                                    if($this->changeEvent($u1dbCalendar[$i],$eventsCalendar[$j])) {
                                        $this->calendarManager->saveEvent($eventsCalendar[$j]);
                                    }
                                }
                                break;
                            }
                        }

                        if(!$encontrado) {
                            if($u1dbCalendar[$i]->status !== "DELETED") {
                                array_push($arrayInsert,$this->insertEvent($u1dbCalendar[$i],$cal));
                            }
                        }
                    }

                    if(count($arrayInsert) > 0) {
                        $eventsCalendar = array_merge($eventsCalendar,$arrayInsert);
                    }

                    for($i = 0;$i < count($eventsCalendar);$i++) {
                        $encontrado = false;
                        for($j = 0;$j < count($u1dbCalendar);$j++) {
                            if($this->sameEvent($u1dbCalendar[$j],$eventsCalendar[$i])) {
                                $encontrado = true;
                                break;
                            }
                        }
                        if(!$encontrado) {
                            $eventInsert = $this->getEventInsert($eventsCalendar[$i],$user,$cal->getName());
                            $this->createEvent($eventInsert);
                        }
                    }
                }
            }
            return $eventsCalendar;
        }

        return array();
    }

    public function synchronizeCalendars($user)
    {
        $calendar = array();
        $calendar['type'] = 'calendar';
        $calendar['user_eyeos'] = $user->getName();
        $u1dbCalendar = json_decode($this->callProcessU1db("selectCalendar",$calendar));

        if(is_array($u1dbCalendar)) {
            $calendars = $this->calendarManager->getAllCalendarsFromOwner($user);
            if(count($u1dbCalendar) === 0 && count($calendars) > 0) {
                foreach($calendars as $calendar) {
                    $calendarU1db = $this->getCalendarInsert($calendar->getName(),$user->getName(),$calendar->getTimezone(),$calendar->getDescription());
                    $this->callProcessU1db("insertCalendar",$calendarU1db);
                }
            } else {
                $arrayInsert = array();

                for($i = 0;$i < count($u1dbCalendar);$i++) {
                    $encontrado = false;
                    for($j = 0;$j < count($calendars);$j++) {
                        if($u1dbCalendar[$i]->name == $calendars[$j]->getName()) {
                            $encontrado = true;

                            if($u1dbCalendar[$i]->status == "DELETED") {
                                $this->calendarManager->deleteCalendar($calendars[$j]);
                                unset($calendars[$j]);
                                $calendars = array_values($calendars);
                            }

                            break;
                        }
                    }

                    if(!$encontrado) {
                        if($u1dbCalendar[$i]->status != "DELETED") {
                            array_push($arrayInsert,$this->createNewCalendar($u1dbCalendar[$i]->name,$u1dbCalendar[$i]->description,$u1dbCalendar[$i]->timezone,$user->getId()));
                        }
                    }
                }

                if(count($arrayInsert) > 0) {
                    $calendars = array_merge($calendars,$arrayInsert);
                }

                for($i = 0;$i < count($calendars);$i++) {
                    $encontrado = false;
                    for($j = 0;$j < count($u1dbCalendar);$j++) {
                        if($calendars[$i]->getName() == $u1dbCalendar[$j]->name) {
                            $encontrado = true;
                            break;
                        }
                    }

                    if(!$encontrado) {
                        $calendar = $this->getCalendarInsert($calendars[$i]->getName(),$user->getName(),$calendars[$i]->getTimezone(),$calendars[$i]->getDescription());
                        $this->callProcessU1db("insertCalendar",$calendar);
                    }
                }

            }

            return $calendars;
        }

        return array();

    }

    public function search($array, $key, $value)
    {
        if (is_array($array)) {
            foreach($array as $data) {
                if($data->$key == $value){
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function getFilename($array, $key, $value, $keyFind)
    {
        $filename = '';
        if (is_array($array)) {
            foreach($array as $data) {
                if($data->$key == $value){
                    $filename = $data->$keyFind;
                    break;
                }
            }
        }
        return $filename;
    }

    public function callProcessU1db($type,$lista)
    {
        $json['type'] = $type;
        $json['lista'] = array();
        array_push($json['lista'],$lista);
        return $this->accessorProvider->getProcessDataU1db(json_encode($json));
    }

    public function  getDecryption($data)
    {
        $codeManager = new CodeManager();
        return $codeManager->getDecryption($data);
    }

    public function getUrl()
    {
        return $this->getDecryption($_SESSION['url']);
    }

    public function getToken()
    {
        return $this->getDecryption($_SESSION['token']);
    }

    public function setUserEyeos($metadata)
    {
        $aux = new stdClass();
        $aux->user_eyeos = $_SESSION['user'];
        $metadata = (object)array_merge((array)$aux,(array)$metadata);
        return $metadata;
    }

    public function getEventInsert($event,$user,$calendarName)
    {
        $eventU1db = array();
        $eventU1db['type'] = 'event';
        $eventU1db['user_eyeos'] = $user;
        $eventU1db['calendar'] = $calendarName;
        $eventU1db['status'] = 'NEW';
        $eventU1db['isallday'] = $event->getIsAllDay()?1:0;
        $eventU1db['timestart'] = (int)$event->getTimeStart();
        $eventU1db['timeend'] = (int)$event->getTimeEnd();
        $eventU1db['repetition'] = $event->getRepetition();
        $eventU1db['finaltype'] = (int)$event->getFinalType();
        $eventU1db['finalvalue'] = (int)$event->getFinalValue();
        $eventU1db['subject'] = $event->getSubject();
        $eventU1db['location'] = $event->getLocation();
        $eventU1db['repeattype'] = $event->getRepeatType();
        $eventU1db['description'] = $event->getDescription();
        return $eventU1db;
    }

    public function sameEvent($eventU1db,$eventCalendar)
    {
        $isallday = $eventCalendar->getIsAllDay()?1:0;
        if($isallday == $eventU1db->isallday && $eventCalendar->getTimeStart() == $eventU1db->timestart && $eventCalendar->getTimeEnd() == $eventU1db->timeend) {
            return true;
        }
        return false;
    }

    public function changeEvent($eventU1db,&$eventCalendar)
    {
        if(strtolower($eventCalendar->getSubject()) !== strtolower($eventU1db->subject) || strtolower($eventCalendar->getLocation()) !== strtolower($eventU1db->location) ||
            $eventCalendar->getRepetition() !== $eventU1db->repetition || $eventCalendar->getRepeatType() !== $eventU1db->repeattype || $eventCalendar->getFinalType() !== $eventU1db->finaltype ||
            $eventCalendar->getFinalValue() !== $eventU1db->finalvalue || strtolower($eventCalendar->getDescription()) !== strtolower($eventU1db->description)) {
            $eventCalendar->setSubject($eventU1db->subject);
            $eventCalendar->setLocation($eventU1db->location);
            $eventCalendar->setRepetition($eventU1db->repetition);
            $eventCalendar->setRepeatType($eventU1db->repeattype);
            $eventCalendar->setFinalType($eventU1db->finaltype);
            $eventCalendar->setFinalValue($eventU1db->finalvalue);
            $eventCalendar->setDescription($eventU1db->description);
            return true;
        }

        return false;
    }

    public function insertEvent($eventU1db,$cal)
    {
        $newEvent = $this->calendarManager->getNewEvent();
        $newEvent->setSubject($eventU1db->subject);
        $newEvent->setTimeStart($eventU1db->timestart);
        $newEvent->setTimeEnd($eventU1db->timeend);
        $newEvent->setCalendarId($cal->getId());
        $newEvent->setIsAllDay($eventU1db->isallday);
        $newEvent->setRepetition($eventU1db->repetition);
        $newEvent->setRepeatType($eventU1db->repeattype);
        $newEvent->setLocation($eventU1db->location);
        $newEvent->setDescription($eventU1db->description);
        $newEvent->setFinalType($eventU1db->finaltype);
        $newEvent->setFinalValue($eventU1db->finalvalue);
        $newEvent->setCreatorId($cal->getOwnerId());
        $this->calendarManager->saveEvent($newEvent);
        return $newEvent;
    }

    public function getCalendarInsert($name,$user,$timezone,$description)
    {
        $calendar = array();
        $calendar['name'] = $name;
        $calendar['type'] = 'calendar';
        $calendar['status'] = 'NEW';
        $calendar['user_eyeos'] = $user;
        $calendar['timezone'] = $timezone;
        $calendar['description'] = $description;
        return $calendar;
    }

    public function createNewCalendar($name,$description,$timezone, $ownerId)
    {
        $calendar = $this->calendarManager->getNewCalendar();
        $calendar->setName($name);
        $calendar->setDescription($description);
        $calendar->setTimezone($timezone);
        $calendar->setOwnerId($ownerId);
        $this->calendarManager->saveCalendar($calendar);
        return $calendar;
    }
}

?>