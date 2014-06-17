<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/06/14
 * Time: 14:19
 */

class ApiCalendarManager
{
    private $accessorProvider;
    private $calendarManager;

    public function __construct(AccessorProvider $accessorProvider = NULL,ICalendarManager $calendarManager = NULL)
    {
        if(!$accessorProvider) $accessorProvider = new AccessorProvider();
        $this->accessorProvider = $accessorProvider;

        if(!$calendarManager) $calendarManager = CalendarManager::getInstance();
        $this->calendarManager = $calendarManager;
    }

    public function createEvent($event)
    {
        $credentials = $this->callProcessCredentials();
        return json_decode($this->callProcessU1db("insertEvent",$event,$credentials));
    }

    public function callProcessCredentials()
    {
        $credentials = NULL;
        $token = isset($_SESSION['request_token'])?$_SESSION['request_token']:null;
        $verifier = isset($_SESSION['verifier'])?$_SESSION['verifier']:null;
        $creds = json_decode($this->accessorProvider->getProcessCredentials($token,$verifier));
        if ($creds) {
            $_SESSION['request_token'] = json_encode($creds->request_token);
            $_SESSION['verifier'] = $creds->verifier;
            $json['oauth'] = $creds->credentials;
            $credentials = $json;
            //Logger::getLogger('sebas')->error('Credenciales:' . json_encode($credentials));
        }
        return $credentials;
    }

    public function callProcessU1db($type,$lista,$credentials=NULL)
    {
        $json['type'] = $type;
        $json['lista'] = array();
        array_push($json['lista'],$lista);
        if ($credentials) {
            $json['credentials'] = $credentials;
        }
        return $this->accessorProvider->getProcessDataU1db(json_encode($json));
    }

    public function deleteEvent($event)
    {
        $credentials = $this->callProcessCredentials();
        return json_decode($this->callProcessU1db("deleteEvent",$event,$credentials));
    }

    public function updateEvent($event)
    {
        $credentials = $this->callProcessCredentials();
        return json_decode($this->callProcessU1db("updateEvent",$event,$credentials));
    }

    public function selectEvent($event)
    {
        $credentials = $this->callProcessCredentials();
        return json_decode($this->callProcessU1db("selectEvent",$event,$credentials));
    }

    public function synchronizeCalendar($calendarId,$user)
    {
        $cal = null;
        try {
            $cal = $this->calendarManager->getCalendarById($calendarId);
        } catch(Exception $e){}
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
        $credentials = $this->callProcessCredentials();
        $calendar = array();
        $calendar['type'] = 'calendar';
        $calendar['user_eyeos'] = $user->getName();
        $u1dbCalendar = json_decode($this->callProcessU1db("selectCalendar",$calendar,$credentials));

        if(is_array($u1dbCalendar)) {
            $calendars = $this->calendarManager->getAllCalendarsFromOwner($user);
            if(count($u1dbCalendar) === 0 && count($calendars) > 0) {
                foreach($calendars as $calendar) {
                    $credentials = $this->callProcessCredentials();
                    $calendarU1db = $this->getCalendarInsert($calendar->getName(),$user->getName(),$calendar->getTimezone(),$calendar->getDescription());
                    $this->callProcessU1db("insertCalendar",$calendarU1db,$credentials);
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
                        $credentials = $this->callProcessCredentials();
                        $calendar = $this->getCalendarInsert($calendars[$i]->getName(),$user->getName(),$calendars[$i]->getTimezone(),$calendars[$i]->getDescription());
                        $this->callProcessU1db("insertCalendar",$calendar,$credentials);
                    }
                }

            }

            return $calendars;
        }

        return array();

    }

    public function deleteCalendar($user,$calendar)
    {
        $credentials = $this->callProcessCredentials();
        $calendarU1db = array();
        $calendarU1db['type'] = 'calendar';
        $calendarU1db['user_eyeos'] = $user;
        $calendarU1db['name'] = $calendar;
        return json_decode($this->callProcessU1db("deleteCalendar",$calendarU1db,$credentials));
    }

    public function deleteCalendarAndEventsByUser($user)
    {
        $credentials = $this->callProcessCredentials();
        $calendarU1db = array();
        $calendarU1db['user_eyeos'] = $user;
        return json_decode($this->callProcessU1db("deleteCalendarUser",$calendarU1db,$credentials));
    }

    public function insertCalendar($user,Calendar $calendar)
    {
        $credentials = $this->callProcessCredentials();
        $calendarU1db = array();
        $calendarU1db['type'] = 'calendar';
        $calendarU1db['user_eyeos'] = $user;
        $calendarU1db['name'] = $calendar->getName();
        $calendarU1db['description'] = $calendar->getDescription();
        $calendarU1db['timezone'] = $calendar->getTimezone();
        $calendarU1db['status'] = 'NEW';
        return json_decode($this->callProcessU1db("insertCalendar",$calendarU1db,$credentials));
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