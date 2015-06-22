<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/06/14
 * Time: 14:19
 */

class ApiCalendarManager
{
    private $calendarManager;
    private $apiManager;

    public function __construct(ICalendarManager $calendarManager = NULL,ApiManager $apiManager = NULL)
    {
        if(!$calendarManager) $calendarManager = CalendarManager::getInstance();
        $this->calendarManager = $calendarManager;

        if(!$apiManager) $apiManager = new ApiManager();
        $this->apiManager = $apiManager;
    }

    public function createEvent($cloud,$token,$event,$resourceUrl)
    {
        return  $this->apiManager->insertEvent($cloud,$token,$event->user,$event->calendar,$event->isallday,$event->timestart,
            $event->timeend,$event->repetition,$event->finaltype,$event->finalvalue,$event->subject,$event->location,
            $event->description,$event->repeattype,$resourceUrl);
    }


    public function deleteEvent($cloud,$token,$event,$resourceUrl)
    {
        return $this->apiManager->deleteEvent($cloud,$token,$event->user,$event->calendar,$event->timestart,$event->timeend,
            $event->isallday,$resourceUrl);
    }

    public function updateEvent($cloud,$token,$event,$resourceUrl)
    {
        return $this->apiManager->updateEvent($cloud,$token,$event->user,$event->calendar,$event->isallday,$event->timestart,
            $event->timeend,$event->repetition,$event->finaltype,$event->finalvalue,$event->subject,$event->location,
            $event->description,$event->repeattype,$resourceUrl);
    }

    public function selectEvent($cloud,$token,$event,$resourceUrl)
    {
        return $this->apiManager->getEvents($cloud,$token,$event->user,$event->calendar,$resourceUrl);
    }

    public function synchronizeCalendar($cloud,$token,$user,$calendarId,$resourceUrl)
    {
        $cal = null;
        $result = array();
        $arrayDelete = array();
        $arrayInsert = array();
        $arrayUpdate = array();
        try {
            $cal = $this->calendarManager->getCalendarById($calendarId);
        } catch(Exception $e){}

        if($cal) {
            $eventsCalendar = $this->calendarManager->getAllEventsByPeriod($cal,null,null);
            $event = new stdClass();
            $event->user = $user;
            $event->calendar = $cal->getName();
            $eventsServer = $this->selectEvent($cloud,$token,$event,$resourceUrl);
            if(count($eventsServer) > 0) {
                if(count($eventsCalendar) == 0) {
                    $arrayInsert = $eventsServer;
                } else {
                    foreach($eventsServer as $eventServer)
                    {
                        $encontrado = false;
                        foreach($eventsCalendar as $eventCalendar)
                        {
                            if($this->sameEvent($eventServer,$eventCalendar))
                            {
                                $encontrado = true;
                                if($this->changeEvent($eventServer,$eventCalendar)) {
                                    array_push($arrayUpdate,$eventCalendar);
                                }
                                array_push($result,$eventCalendar);
                                break;
                            }

                        }

                        if(!$encontrado) {
                            array_push($arrayInsert,$eventServer);
                        }
                    }

                    foreach($eventsCalendar as $eventCalendar) {
                        $encontrado = false;
                        foreach($eventsServer as $eventServer) {
                            if($this->sameEvent($eventServer,$eventCalendar)) {
                                $encontrado = true;
                                break;
                            }
                        }

                        if(!$encontrado) {
                            array_push($arrayDelete,$eventCalendar);
                        }
                    }
                }
            } else {
                if(count($eventsCalendar) > 0) {
                    $arrayDelete = $eventsCalendar;
                }
            }

            if(count($arrayInsert) > 0) {
               foreach($arrayInsert as $event) {
                   array_push($result,$this->insertEvent($event,$cal));
               }
            }

            if(count($arrayUpdate) > 0) {
                foreach($arrayUpdate as $event) {
                    $this->calendarManager->saveEvent($event);
                }
            }

            if(count($arrayDelete) > 0) {
                foreach($arrayDelete as $event) {
                    $this->calendarManager->deleteEvent($event);
                }
            }
        }
        return $result;
    }

    public function synchronizeCalendars($cloud,$token,$user,$resourceUrl)
    {
        $calendars = $this->calendarManager->getAllCalendarsFromOwner($user);
        $calendarsServer = $this->apiManager->getCalendars($cloud,$token,$user->getName(),$resourceUrl);
        $result = array();
        $arrayInsert = array();
        $arrayDelete = array();
        $arrayUpdate = array();

        if(count($calendarsServer) > 0) {
            if(count($calendars) == 0) {
                $arrayInsert = $calendarsServer;
            } else {
                foreach($calendarsServer as $calendarServer) {
                    $encontrado = false;
                    foreach($calendars as $calendar) {
                        $name = $calendar->getName();
                        if(strrpos($name,$cloud . '_') !== false) {
                            $name = substr($name, strrpos($name, '_') + 1);
                            if ($calendarServer->name == $name) {
                                $encontrado = true;
                                if (strtolower($calendarServer->description) != strtolower($calendar->getDescription())) {
                                    $calendar->setDescription($calendarServer->description);
                                    array_push($arrayUpdate, $calendar);
                                }
                                array_push($result, $calendar);
                                break;
                            }
                        }
                    }

                    if(!$encontrado) {
                        array_push($arrayInsert,$calendarServer);
                    }
                }

                foreach($calendars as $calendar) {
                    $name = $calendar->getName();
                    if(strrpos($name,$cloud . '_') !== false) {
                        $encontrado = false;
                        foreach ($calendarsServer as $calendarServer) {
                            $name = $calendar->getName();
                            $name = substr($name, strrpos($name, '_') + 1);
                            if ($name == $calendarServer->name) {
                                $encontrado = true;
                                break;
                            }
                        }
                        if (!$encontrado) {
                            array_push($arrayDelete, $calendar);
                        }
                    }
                }
            }
        } else {
            if(count($calendars) > 0) {
                $arrayDelete = $calendars;
            }
        }

        if(count($arrayInsert) > 0) {
            foreach($arrayInsert as $calendar) {
                array_push($result,$this->createNewCalendar($cloud . '_' . $calendar->name,$calendar->description,$calendar->timezone,$user->getId()));
            }
        }

        if(count($arrayDelete) > 0) {
            foreach($arrayDelete as $calendar) {
                $this->calendarManager->deleteCalendar($calendar);
            }
        }

        if(count($arrayUpdate) > 0) {
            foreach($arrayUpdate as $calendar) {
                $this->calendarManager->saveCalendar($calendar);
            }
        }

        return $result;
    }

    public function deleteCalendar($cloud,$token,$calendar,$resourceUrl)
    {
        return $this->apiManager->deleteCalendar($cloud,$token,$calendar->user,$calendar->name,$resourceUrl);
    }

    public function updateCalendar($cloud,$token,$calendar,$resourceUrl)
    {
        return $this->apiManager->updateCalendar($cloud,$token,$calendar->user,$calendar->name,$calendar->description,$calendar->timezone,$resourceUrl);
    }

    public function deleteCalendarAndEventsByUser($cloud,$token,$user,$resourceUrl)
    {
        return $this->apiManager->deleteCalendarsUser($cloud,$token,$user,$resourceUrl);
    }

    public function insertCalendar($cloud,$token,$calendar,$resourceUrl)
    {
        return $this->apiManager->insertCalendar($cloud,$token,$calendar->user,$calendar->name,$calendar->description,$calendar->timezone,$resourceUrl);
    }

    public function sameEvent($eventServer,$eventCalendar)
    {
        $isallday = $eventCalendar->getIsAllDay()?1:0;
        if($isallday == $eventServer->isallday && $eventCalendar->getTimeStart() == $eventServer->timestart && $eventCalendar->getTimeEnd() == $eventServer->timeend) {
            return true;
        }
        return false;
    }

    public function changeEvent($eventServer,&$eventCalendar)
    {
        if(strtolower($eventCalendar->getSubject()) !== strtolower($eventServer->subject) || strtolower($eventCalendar->getLocation()) !== strtolower($eventServer->location) ||
            $eventCalendar->getRepetition() !== $eventServer->repetition || $eventCalendar->getRepeatType() !== $eventServer->repeattype || $eventCalendar->getFinalType() !== $eventServer->finaltype ||
            $eventCalendar->getFinalValue() !== $eventServer->finalvalue || strtolower($eventCalendar->getDescription()) !== strtolower($eventServer->description)) {
            $eventCalendar->setSubject($eventServer->subject);
            $eventCalendar->setLocation($eventServer->location);
            $eventCalendar->setRepetition($eventServer->repetition);
            $eventCalendar->setRepeatType($eventServer->repeattype);
            $eventCalendar->setFinalType($eventServer->finaltype);
            $eventCalendar->setFinalValue($eventServer->finalvalue);
            $eventCalendar->setDescription($eventServer->description);
            return true;
        }

        return false;
    }

    public function insertEvent($eventServer,$cal)
    {
        $newEvent = $this->calendarManager->getNewEvent();
        $newEvent->setSubject($eventServer->subject);
        $newEvent->setTimeStart($eventServer->timestart);
        $newEvent->setTimeEnd($eventServer->timeend);
        $newEvent->setCalendarId($cal->getId());
        $newEvent->setIsAllDay($eventServer->isallday == 1?true:false);
        $newEvent->setRepetition($eventServer->repetition);
        $newEvent->setRepeatType($eventServer->repeattype);
        $newEvent->setLocation($eventServer->location);
        $newEvent->setDescription($eventServer->description);
        $newEvent->setFinalType($eventServer->finaltype);
        $newEvent->setFinalValue($eventServer->finalvalue);
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