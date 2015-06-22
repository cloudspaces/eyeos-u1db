<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/06/14
 * Time: 14:12
 */

class EyeosUserCalendarTest  implements IPrincipal,EyeObject
{
    public function setId($id){}
    public function getId($forceGeneration=true){}
    public function getName(){}
    public function __toString(){return "";}
}


class ApiCalendarManagerTest extends PHPUnit_Framework_TestCase
{
    private $calendarManagerMock;
    private $apiManagerMock;
    private $sut;
    private $token;
    private $cloud;
    private $resourceUrl;
    private $user;

    public function setUp()
    {
        $this->calendarManagerMock = $this->getMock('ICalendarManager');
        $this->apiManagerMock = $this->getMock('ApiManager');
        $this->sut = new ApiCalendarManager($this->calendarManagerMock,$this->apiManagerMock);
        $this->token = new stdClass();
        $this->token->key = '1234';
        $this->token->secret = 'ABCD';
        $this->cloud = 'Stacksync';
        $this->resourceUrl = "http://192.68.56.101/";
        $this->user = 'eyeos';
    }

    public function tearDown()
    {
        $this->calendarManagerMock = null;
        $this->apiManagerMock = null;
        $this->sut = null;
    }

    /**
     * method: createEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnInsertCorrect
     */
    public function test_createEvent_called_cloudAndTokenAndEventAndResourceUrl_returnInsertCorrect()
    {
        $event = json_decode('{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}');
        $check = array("status" => "OK");
        $this->exerciseCreateEvent($event,$check);
    }

    /**
     * method: createEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnException
     */
    public function test_createEvent_called_cloudAndTokenAndEventAndResourceUrl_returnException()
    {
        $event = json_decode('{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}');
        $check = array("status" => "KO","error" => -1);
        $this->exerciseCreateEvent($event,$check);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnDeleteCorrect
     */
    public function test_deleteEvent_called_cloudAndTokenAndEventAndResourceUrl_returnDeleteCorrect()
    {
        $event = json_decode('{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}');
        $check = array("status" => "OK");
        $this->exerciseDeleteEvent($event,$check);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnException
     */
    public function test_deleteEvent_called_cloudAndTokenAndEventAndResourceUrl_returnException()
    {
        $event = json_decode('{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}');
        $check = array("status" => "KO","error" => -1);
        $this->exerciseDeleteEvent($event,$check);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnUpdateCorrect
     */
    public function test_updateEvent_called_cloudAndTokenAndEventAndResourceUrl_returnUpdateCorrect()
    {
        $event = json_decode('{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}');
        $check = array("status" => "OK");
        $this->exerciseUpdateEvent($event,$check);
    }

    /**
     * method: updateEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnException
     */
    public function test_updateEvent_called_cloudAndTokenAndEventAndResourceUrl_returnException()
    {
        $event = json_decode('{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}');
        $check = array("status" => "KO","error" => -1);
        $this->exerciseUpdateEvent($event,$check);
    }

    /**
     * method: selectEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnEvents
     */
    public function test_selectEvent_called_cloudAndTokenAndEventAndResourceUrl_returnEvents()
    {
        $events = json_decode('[{"user": "eyeos","calendar": "personal","isallday":0, "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita", "location": "Barcelona", "description": "Dentista","repeattype":"n"}]');
        $this->exerciseSelectEvent($events);
    }

    /**
     * method: selectEvent
     * when: called
     * with: cloudAndTokenAndEventAndResourceUrl
     * should: returnException
     */
    public function test_selectEvent_called_cloudAndTokenAndEventAndResourceUrl_returnException()
    {
        $check = array("status" => "KO","error" => -1);
        $this->exerciseSelectEvent($check);
    }


    /**
     * method: synchronizeCalendar
     * when: called
     * with: cloudAndTokenAndUserAndCalendarIdAndResourceUrl
     * should: calledCalendarEventsEmptyAndServerEmpty
     */
    public function test_synchronizeCalendar_called_cloudAndTokenAndUserAndCalendarIdAndResourceUrl_calledCalendarEventsEmptyAndServerEmpty()
    {
        $calendarId = 'eyeID_Calendar_f';
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');
        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue(array()));

        $this->apiManagerMock->expects($this->once())
            ->method('getEvents')
            ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
            ->will($this->returnValue(array()));

        $this->calendarManagerMock->expects($this->never())
            ->method('deleteEvent');

        $this->calendarManagerMock->expects($this->never())
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->never())
            ->method('saveEvent');


        $result = $this->sut->synchronizeCalendar($this->cloud,$this->token,$this->user,$calendarId,$this->resourceUrl);
        $this->assertEquals(array(),$result);
    }


    /**
     * method: synchronizeCalendar
     * when: called
     * with: cloudAndTokenAndUserAndCalendarIdAndResourceUrl
     * should: calledCalendarEventsAndServerEmpty
     */
    public function test_synchronizeCalendar_called_cloudAndTokenAndUserAndCalendarIdAndResourceUrl_calledCalendarEventsAndServerEmpty()
    {
        $calendarId = 'eyeID_Calendar_f';
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');
        $eventsCalendar = $this->getEvents();
        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue($eventsCalendar));

        $this->apiManagerMock->expects($this->at(0))
        ->method('getEvents')
        ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
        ->will($this->returnValue(array()));

        $this->calendarManagerMock->expects($this->at(2))
            ->method('deleteEvent')
            ->with($eventsCalendar[0]);


        $this->calendarManagerMock->expects($this->at(3))
            ->method('deleteEvent')
            ->with($eventsCalendar[1]);

        $this->calendarManagerMock->expects($this->at(4))
            ->method('deleteEvent')
            ->with($eventsCalendar[2]);

        $this->calendarManagerMock->expects($this->at(5))
            ->method('deleteEvent')
            ->with($eventsCalendar[3]);

        $this->calendarManagerMock->expects($this->never())
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->never())
            ->method('saveEvent');


        $result = $this->sut->synchronizeCalendar($this->cloud,$this->token,$this->user,$calendarId,$this->resourceUrl);
        $this->assertEquals(array(),$result);
    }

    /**
     * method: synchronizeCalendar
     * when: called
     * with: cloudAndTokenAndUserAndCalendarIdAndResourceUrl
     * should: calledCalendarEventsEmptyAndServerData
     */
    public function test_synchronizeCalendar_called_cloudAndTokenAndUserAndCalendarIdAndResourceUrl_calledCalendarEventsEmptyAndServerData()
    {
        $calendarId = 'eyeID_Calendar_f';
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');
        $eventsCalendar = $this->getEvents();
        $eventsServer = $this->createEventsServer($this->user,"personal",$eventsCalendar);
        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue(array()));


        $this->apiManagerMock->expects($this->at(0))
            ->method('getEvents')
            ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
            ->will($this->returnValue($eventsServer));

        $this->calendarManagerMock->expects($this->at(2))
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->at(3))
            ->method('saveEvent')
            ->with($eventsCalendar[0]);

        $this->calendarManagerMock->expects($this->at(4))
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->at(5))
            ->method('saveEvent')
            ->with($eventsCalendar[1]);

        $this->calendarManagerMock->expects($this->at(6))
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->at(7))
            ->method('saveEvent')
            ->with($eventsCalendar[2]);

        $this->calendarManagerMock->expects($this->at(8))
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->at(9))
            ->method('saveEvent')
            ->with($eventsCalendar[3]);


        $result = $this->sut->synchronizeCalendar($this->cloud,$this->token,$this->user,$calendarId,$this->resourceUrl);
        $this->assertEquals($eventsCalendar,$result);

    }

    /**
     * method: synchronizeCalendar
     * when: called
     * with: cloudAndTokenAndUserAndCalendarIdAndResourceUrl
     * should: calledSameData
     */
    public function test_synchronizeCalendar_called_cloudAndTokenAndUserAndCalendarIdAndResourceUrl_calledSameData()
    {
        $calendarId = 'eyeID_Calendar_f';
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');
        $eventsCalendar = $this->getEvents();
        $eventsServer = $this->createEventsServer($this->user,"personal",$eventsCalendar);
        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue($eventsCalendar));


        $this->apiManagerMock->expects($this->at(0))
            ->method('getEvents')
            ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
            ->will($this->returnValue($eventsServer));

        $result = $this->sut->synchronizeCalendar($this->cloud,$this->token,$this->user,$calendarId,$this->resourceUrl);
        $this->assertEquals($this->getEvents(),$result);
    }

    /**
     * method: synchronizeCalendar
     * when: called
     * with: cloudAndTokenAndUserAndCalendarIdAndResourceUrl
     * should: calledDistinctData
     */
    public function test_synchronizeCalendar_called_cloudAndTokenAndUserAndCalendarIdAndResourceUrl_calledDistinctData()
    {
        $calendarId = 'eyeID_Calendar_f';
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');
        $eventsCalendar = $this->getEvents();
        $eventsServer = $this->createEventsServer($this->user,"personal",$eventsCalendar);
        unset($eventsCalendar[0]);
        $eventsServer[1]->subject = "Examen trimestre";
        unset($eventsServer[2]);
        $expected = array();

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue($eventsCalendar));


        $this->apiManagerMock->expects($this->at(0))
            ->method('getEvents')
            ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
            ->will($this->returnValue($eventsServer));

        $this->calendarManagerMock->expects($this->at(2))
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $event1 = new CalendarEvent(null,'Examen','Barcelona','Examen de matemáticas',false,1395730800,1395738000,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null);

        $this->calendarManagerMock->expects($this->at(3))
            ->method('saveEvent')
            ->with($event1);

        $event2 = new CalendarEvent(null,'Examen trimestre','Girona','Radiografia',false,1395820800,1395828000,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null);

        $this->calendarManagerMock->expects($this->at(4))
            ->method('saveEvent')
            ->with($event2);

        $this->calendarManagerMock->expects($this->at(5))
            ->method('deleteEvent')
            ->with($eventsCalendar[2]);


        array_push($expected,$event2);
        array_push($expected,$eventsCalendar[3]);
        array_push($expected,$event1);

        $result = $this->sut->synchronizeCalendar($this->cloud,$this->token,$this->user,$calendarId,$this->resourceUrl);
        $this->assertEquals($expected,$result);

    }

    /**
     *method: synchronizeCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: calledCalendarsServerEmptyAndCalendarsEmpty
     */
    public function test_synchronizeCalendars_called_cloudAndTokenAndUserAndResourceUrl_calledCalendarsServerEmptyAndCalendarsEmpty()
    {
        $calendars = array();
        $userMock = $this->getMock("EyeosUserCalendarTest");
        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->apiManagerMock->expects($this->at(0))
            ->method('getCalendars')
            ->with($this->cloud,$this->token,"eyeos","http://192.68.56.101/")
            ->will($this->returnValue(array()));

        $this->calendarManagerMock->expects($this->never())
            ->method('getNewCalendar')
            ->will($this->returnValue(new Calendar()));

        $this->calendarManagerMock->expects($this->never())
            ->method('saveCalendar');

        $this->calendarManagerMock->expects($this->never())
            ->method('deleteCalendar');

        $result = $this->sut->synchronizeCalendars($this->cloud,$this->token,$userMock,$this->resourceUrl);
        $this->assertEquals(array(),$result);

    }

    /**
     *method: synchronizeCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: calledCalendarsServerDataAndCalendarsEmpty
     */
    public function test_synchronizeCalendars_called_cloudAndTokenAndUserAndResourceUrl_calledCalendarsServerDataAndCalendarsEmpty()
    {
        $calendars = array();
        $calendarsServer = json_decode('[{"type": "calendar","user": "eyeos","cloud": "Stacksync","name": "personal","description": "Calendario personal","timezone": 0,"status": "NEW"}]');
        $userMock = $this->getMock("EyeosUserCalendarTest");
        $expected = array();
        array_push($expected,$this->getCalendar(null, 'Stacksync_personal', 'Calendario personal', 'eyeID_EyeosUser_63'));

        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $userMock->expects($this->any())
            ->method("getId")
            ->will($this->returnValue('eyeID_EyeosUser_63'));

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->apiManagerMock->expects($this->at(0))
            ->method('getCalendars')
            ->with($this->cloud,$this->token,"eyeos","http://192.68.56.101/")
            ->will($this->returnValue($calendarsServer));


        $this->calendarManagerMock->expects($this->at(1))
            ->method('getNewCalendar')
            ->will($this->returnValue(new Calendar()));

        $this->calendarManagerMock->expects($this->at(2))
            ->method('saveCalendar')
            ->with($expected[0]);

        $this->calendarManagerMock->expects($this->never())
            ->method('deleteCalendar');


        $result = $this->sut->synchronizeCalendars($this->cloud,$this->token,$userMock,$this->resourceUrl);
        $this->assertEquals($expected,$result);
    }

    /**
     *method: synchronizeCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: calledCalendarsServerEmptyAndCalendarsData
     */
    public function test_synchronizeCalendars_called_cloudAndTokenAndUserAndResourceUrl_calledCalendarsServerEmptyAndCalendarsData()
    {
        $calendarsServer = array();
        $userMock = $this->getMock("EyeosUserCalendarTest");
        $calendars = array();
        array_push($calendars,$this->getCalendar(null, 'Stacksync_personal', 'Calendario personal', 'eyeID_EyeosUser_63'));

        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $userMock->expects($this->any())
            ->method("getId")
            ->will($this->returnValue('eyeID_EyeosUser_63'));

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->apiManagerMock->expects($this->at(0))
            ->method('getCalendars')
            ->with($this->cloud,$this->token,"eyeos","http://192.68.56.101/")
            ->will($this->returnValue($calendarsServer));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('deleteCalendar')
            ->with($calendars[0]);

        $result = $this->sut->synchronizeCalendars($this->cloud,$this->token,$userMock,$this->resourceUrl);
        $this->assertEquals(array(),$result);
    }

    /**
     *method: synchronizeCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: calledSameData
     */
    public function test_synchronizeCalendars_called_cloudAndTokenAndUserAndResourceUrl_calledSameData()
    {
        $calendarsServer = json_decode('[{"type": "calendar","user": "eyeos","cloud": "Stacksync","name": "personal","description": "Calendario personal","timezone": 0,"status": "NEW"}]');
        $userMock = $this->getMock("EyeosUserCalendarTest");
        $calendars = array();
        array_push($calendars,$this->getCalendar(null, 'Stacksync_personal', 'Calendario personal', 'eyeID_EyeosUser_63'));

        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $userMock->expects($this->any())
            ->method("getId")
            ->will($this->returnValue('eyeID_EyeosUser_63'));

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->apiManagerMock->expects($this->at(0))
            ->method('getCalendars')
            ->with($this->cloud,$this->token,"eyeos","http://192.68.56.101/")
            ->will($this->returnValue($calendarsServer));

        $this->calendarManagerMock->expects($this->never())
            ->method('getNewCalendar')
            ->will($this->returnValue(new Calendar()));

        $this->calendarManagerMock->expects($this->never())
            ->method('saveCalendar');

        $this->calendarManagerMock->expects($this->never())
            ->method('deleteCalendar');

        $result = $this->sut->synchronizeCalendars($this->cloud,$this->token,$userMock,$this->resourceUrl);
        $this->assertEquals($calendars,$result);

    }

    /**
     *method: synchronizeCalendars
     * when: called
     * with: cloudAndTokenAndUserAndResourceUrl
     * should: calledDistinctData
     */
    public function test_synchronizeCalendars_called_cloudAndTokenAndUserAndResourceUrl_calledDistinctData()
    {
        $userMock = $this->getMock("EyeosUserCalendarTest");
        $calendarsServer = json_decode('[{"type": "calendar","user": "eyeos","cloud": "Stacksync","name": "personal","description": "Calendario personal","timezone": 0,"status": "NEW"},
                                        {"type": "calendar","user": "eyeos","cloud": "Stacksync","name": "laboral","description": "Calendario Laboral","timezone": 0,"status": "NEW"},
                                        {"type": "calendar","user": "eyeos","cloud": "Stacksync","name": "academico","description": "Calendario academico","timezone": 0,"status": "NEW"}]');
        $calendars = array();
        array_push($calendars,$this->getCalendar('eyeID_Calendar_64','Stacksync_laboral', 'Calendario Laboral', 'eyeID_EyeosUser_63'));
        array_push($calendars,$this->getCalendar('eyeID_Calendar_65','Stacksync_escolar', 'Calendario Escolar', 'eyeID_EyeosUser_63'));
        array_push($calendars,$this->getCalendar('eyeID_Calendar_66','Stacksync_academico', 'Calendario Ingles', 'eyeID_EyeosUser_63'));

        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $userMock->expects($this->any())
            ->method("getId")
            ->will($this->returnValue('eyeID_EyeosUser_63'));

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->apiManagerMock->expects($this->at(0))
            ->method('getCalendars')
            ->with($this->cloud,$this->token,"eyeos","http://192.68.56.101/")
            ->will($this->returnValue($calendarsServer));


        $this->calendarManagerMock->expects($this->at(1))
            ->method('getNewCalendar')
            ->will($this->returnValue(new Calendar()));

        $this->calendarManagerMock->expects($this->at(2))
            ->method('saveCalendar')
            ->with($this->getCalendar(null,'Stacksync_personal', 'Calendario personal', 'eyeID_EyeosUser_63'));

        $this->calendarManagerMock->expects($this->at(3))
            ->method('deleteCalendar')
            ->with($calendars[1]);

        $this->calendarManagerMock->expects($this->at(4))
            ->method('saveCalendar')
            ->with($this->getCalendar('eyeID_Calendar_66','Stacksync_academico', 'Calendario academico', 'eyeID_EyeosUser_63'));

        $expected = array();
        array_push($expected,$calendars[0]);
        array_push($expected,$this->getCalendar('eyeID_Calendar_66','Stacksync_academico', 'Calendario academico', 'eyeID_EyeosUser_63'));
        array_push($expected,$this->getCalendar(null,'Stacksync_personal', 'Calendario personal', 'eyeID_EyeosUser_63'));

        $result = $this->sut->synchronizeCalendars($this->cloud,$this->token,$userMock,$this->resourceUrl);
        $this->assertEquals($expected,$result);
    }


    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnInsertCorrect
     */
    public function test_insertCalendar_called__cloudAndTokenAndCalendarAndResourceUrl_returnInsertCorrect()
    {
        $check = array("status" => "OK");
        $this->exerciseInsertCalendar($check);
    }


    /**
     * method: insertCalendar
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnException
     */
    public function test_insertCalendar_called__cloudAndTokenAndCalendarAndResourceUrl_returnException()
    {
        $check = array("status" => "KO","error" => -1);
        $this->exerciseInsertCalendar($check);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnDeleteCorrect
     */
    public function test_deleteCalendar_called_cloudAndTokenAndCalendarAndResourceUrl_returnDeleteCorrect()
    {
        $check = array("status" => "OK");
        $this->exerciseDeleteCalendar($check);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnException
     */
    public function test_deleteCalendar_called_cloudAndTokenAndCalendarAndResourceUrl_returnException()
    {
        $check = array("status" => "KO","error" => -1);
        $this->exerciseDeleteCalendar($check);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnUpdateCorrect
     */
    public function test_updateCalendar_called_cloudAndTokenAndCalendarAndResourceUrl_returnUpdateCorrect()
    {
        $check = array("status" => "OK");
        $this->exerciseUpdateCalendar($check);
    }

    /**
     * method: updateCalendar
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnException
     */
    public function test_updateCalendar_called_cloudAndTokenAndCalendarAndResourceUrl_returnException()
    {
        $check = array("status" => "KO","error" => -1);
        $this->exerciseUpdateCalendar($check);
    }

    /**
     * method: deleteCalendarAndEventsByUser
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnDeleteCorrect
     */
    public function test_deleteCalendarAndEventsByUser_called_cloudAndTokenAndCalendarAndResourceUrl_returnDeleteCorrect()
    {
        $check = array("status" => "OK");
        $this->exerciseDeleteCalendarByUser($check);
    }

    /**
     * method: deleteCalendarAndEventsByUser
     * when: called
     * with: cloudAndTokenAndCalendarAndResourceUrl
     * should: returnException
     */
    public function test_deleteCalendarAndEventsByUser_called_cloudAndTokenAndCalendarAndResourceUrl_returnException()
    {
        $check = array("status" => "KO","error" => -1);
        $this->exerciseDeleteCalendarByUser($check);
    }

    private function getEvents()
    {
        $events = array();
        array_push($events,new CalendarEvent(null,'Examen','Barcelona','Examen de matemáticas',false,1395730800,1395738000,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        array_push($events,new CalendarEvent(null,'Médico','Girona','Radiografia',false,1395820800,1395828000,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        array_push($events,new CalendarEvent(null,'Salida','Lleida','Justificante',false,1394820800,1394820800,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        array_push($events,new CalendarEvent(null,'Clase','Barcelona','Ingles',false,1494820800,1494820800,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        return $events;
    }

    private function getCalendar($id, $name, $description, $ownerId)
    {
        $calendar = new Calendar();
        $calendar->setId($id);
        $calendar->setName($name);
        $calendar->setDescription($description);
        $calendar->setTimezone(0);
        $calendar->setOwnerId($ownerId);
        return $calendar;
    }

    private function exerciseCreateEvent($event,$check)
    {
        $this->apiManagerMock->expects($this->once())
            ->method('insertEvent')
            ->with($this->cloud,$this->token,"eyeos","personal",0,"201419160000","201419170000","None","1","0","Visita","Barcelona","Dentista","n","http://192.68.56.101/")
            ->will($this->returnValue($check));

        $result = $this->sut->createEvent($this->cloud,$this->token,$event,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function exerciseDeleteEvent($event,$check)
    {
        $this->apiManagerMock->expects($this->once())
            ->method('deleteEvent')
            ->with($this->cloud,$this->token,"eyeos","personal","201419160000","201419170000",0,"http://192.68.56.101/")
            ->will($this->returnValue($check));

        $result = $this->sut->deleteEvent($this->cloud,$this->token,$event,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function exerciseUpdateEvent($event,$check)
    {
        $this->apiManagerMock->expects($this->once())
            ->method('updateEvent')
            ->with($this->cloud,$this->token,"eyeos","personal",0,"201419160000","201419170000","None","1","0","Visita","Barcelona","Dentista","n","http://192.68.56.101/")
            ->will($this->returnValue($check));

        $result = $this->sut->updateEvent($this->cloud,$this->token,$event,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function exerciseSelectEvent($check)
    {
        $event = json_decode('{"user":"eyeos","calendar":"personal"}');
        $this->apiManagerMock->expects($this->once())
            ->method('getEvents')
            ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
            ->will($this->returnValue($check));

        $result = $this->sut->selectEvent($this->cloud,$this->token,$event,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function createEventsServer($user,$calendar,$eventsCalendar)
    {
        $eventsServer = array();

        foreach($eventsCalendar as $eventCalendar)
        {
            $event = new stdClass();
            $event->type = 'event';
            $event->user_eyeos = $user;
            $event->calendar = $calendar;
            $event->status = 'NEW';
            $event->isallday = $eventCalendar->getIsAllDay()?1:0;
            $event->timestart = (int)$eventCalendar->getTimeStart();
            $event->timeend = (int)$eventCalendar->getTimeEnd();
            $event->repetition = $eventCalendar->getRepetition();
            $event->finaltype = (int)$eventCalendar->getFinalType();
            $event->finalvalue = (int)$eventCalendar->getFinalValue();
            $event->subject = $eventCalendar->getSubject();
            $event->location = $eventCalendar->getLocation();
            $event->repeattype = $eventCalendar->getRepeatType();
            $event->description = $eventCalendar->getDescription();
            array_push($eventsServer,$event);
        }

        return $eventsServer;
    }

    private function getCalendarStruct($user,$name,$description,$timezone)
    {
        $calendar = new stdClass();
        $calendar->user = $user;
        $calendar->name = $name;
        $calendar->description = $description;
        $calendar->timezone = $timezone;
        return $calendar;
    }

    private function exerciseInsertCalendar($check)
    {
        $calendar = $this->getCalendarStruct('eyeos','personal','personal\'s personal calendar.',0);
        $this->apiManagerMock->expects($this->once())
            ->method('insertCalendar')
            ->with($this->cloud,$this->token,"eyeos","personal",'personal\'s personal calendar.',0,"http://192.68.56.101/")
            ->will($this->returnValue($check));
        $result = $this->sut->insertCalendar($this->cloud,$this->token,$calendar,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function exerciseDeleteCalendar($check)
    {
        $calendar = $this->getCalendarStruct('eyeos','personal','personal\'s personal calendar.',0);
        $this->apiManagerMock->expects($this->once())
            ->method('deleteCalendar')
            ->with($this->cloud,$this->token,"eyeos","personal","http://192.68.56.101/")
            ->will($this->returnValue($check));
        $result = $this->sut->deleteCalendar($this->cloud,$this->token,$calendar,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function exerciseUpdateCalendar($check)
    {
        $calendar = $this->getCalendarStruct('eyeos','personal','personal\'s personal calendar.',0);
        $this->apiManagerMock->expects($this->once())
            ->method('updateCalendar')
            ->with($this->cloud,$this->token,"eyeos","personal",'personal\'s personal calendar.',0,"http://192.68.56.101/")
            ->will($this->returnValue($check));
        $result = $this->sut->updateCalendar($this->cloud,$this->token,$calendar,$this->resourceUrl);
        $this->assertEquals($check,$result);
    }

    private function exerciseDeleteCalendarByUser($check)
    {
        $this->apiManagerMock->expects($this->once())
            ->method('deleteCalendarsUser')
            ->with($this->cloud,$this->token,"eyeos","http://192.68.56.101/")
            ->will($this->returnValue($check));
        $result = $this->sut->deleteCalendarAndEventsByUser($this->cloud,$this->token,"eyeos",$this->resourceUrl);
        $this->assertEquals($check,$result);
    }
}

?>