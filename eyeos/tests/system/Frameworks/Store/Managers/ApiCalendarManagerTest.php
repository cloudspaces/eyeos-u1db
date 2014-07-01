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
    private $accessorProviderMock;
    private $u1dbCredsManagerMock;
    private $sut;
    private $credentials;

    public function setUp()
    {
        $this->calendarManagerMock = $this->getMock('ICalendarManager');
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->u1dbCredsManagerMock = $this->getMock('U1DBCredsManager');

        $this->sut = new ApiCalendarManager($this->accessorProviderMock,$this->calendarManagerMock,$this->u1dbCredsManagerMock);
        //$this->credentials = json_decode('{"credentials":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"},"request_token":{"key":"HIJK","secret":"ABCD"},"verifier":"verifier"}');
        $this->credentials = json_decode('{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}');

    }

    public function tearDown()
    {
        $this->calendarManagerMock = null;
        $this->accessorProviderMock = null;
        $this->u1dbCredsManagerMock = null;
        $this->sut = null;
    }

    /**
     * method: createEvent
     * when: called
     * with: event
     * should: calledU1dbUpdate
     */
    public function test_createEvent_called_event_calledU1dbUpdate()
    {
        $event = json_decode('[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"NEW", "isallday":"0", "timestart": "201419160000", "timeend":"201419170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Médico", "location": "Barcelona", "description": "Llevar justificante"},{"type":"event","user_eyeos": "eyeos","calendarid": "eyeID_Calendar_2b", "isallday": "1", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]}');

        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->createEvent($event);
    }

    /**
     * method: deleteEvent
     * when: called
     * with: event
     * should: calledU1dbDelete
     */
    public function test_deleteEvent_called_event_calledU1dbDelete()
    {
        $event = json_decode('[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"DELETED", "isallday": "0","timestart": "201419173000","timeend":"201419183000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Museo", "location": "Esplugues de llobregat", "description": "Llevar Ticket"},
                    {"type":"event","user_eyeos": "eyeos","calendar": "personal","status":"DELETED", "isallday": "0", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]');

        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));
        $this->sut->deleteEvent($event);
    }

    /**
     * method: updateEvent
     * when: called
     * with: event
     * should: calledU1dbUpdate
     */
    public function test_updateEvent_called_event_calledU1dbUpdate()
    {
        $event = json_decode('[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"CHANGED", "isallday": "0","timestart": "201419173000","timeend":"201419183000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Museo", "location": "Esplugues de llobregat", "description": "Llevar Ticket"},
                    {"type":"event","user_eyeos": "eyeos","calendar": "personal","status":"CHANGED", "isallday": "0", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]');

        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));
        $this->sut->updateEvent($event);
    }

    /**
     * method: selectEvent
     * when: called
     * with: event
     * should: calledU1db
     */
    public function test_selectEvent_called_event_calledU1db()
    {
        $u1db = '[{"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"NEW", "isallday": "0", "timestart": "201419173000","timeend":"201419183000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Visita Museo", "location": "Esplugues de llobregat", "description": "Llevar Ticket"},
                    {"type":"event","user_eyeos": "eyeos","calendar": "personal", "status":"CHANGED" ,"isallday": "0", "timestart": "201420160000", "timeend":"201420170000", "repetition": "None", "finaltype": "1", "finalvalue": "0", "subject": "Excursión", "location": "Girona", "description": "Mochila"}]';
        $event = json_decode('[{"type":"event","user_eyeos":"eyeos","calendar":"personal"}]');

        $this->u1dbCredsManagerMock->expects($this->once())
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->will($this->returnValue($u1db));
        $this->sut->selectEvent($event);
    }

    /**
     * method: synchronizeCalendar
     * when: called
     * with: userAndCalendarIdAndAndUser
     * should: calledU1dbEmpty
     */
    public function test_synchronizeCalendar_called_userAndCalendarIdAndAndUser_calledU1dbEmpty()
    {
        $calendarId = 'eyeID_Calendar_f';
        $user = 'eyeos';
        $event['type'] = 'selectEvent';
        $event['lista'] = json_decode('[{"type":"event","user_eyeos":"eyeos","calendar":"personal"}]');
        $event['credentials'] = json_decode('{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}');

        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue($this->getEvents()));

        $this->u1dbCredsManagerMock->expects($this->exactly(5))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($event))
            ->will($this->returnValue('[]'));

        $this->accessorProviderMock->expects($this->exactly(5))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->synchronizeCalendar($calendarId,$user);
    }

    /**
     * method: synchronizeCalendar
     * when: called
     * with: userAndCalendarIdAndUser
     * should: calledU1db
     */
    public function test_synchronizeCalendar_called_userAndCalendarIdAndUser_calledU1db()
    {
        $calendarId = 'eyeID_Calendar_f';
        $user = 'eyeos';
        $event['type'] = 'selectEvent';
        $event['lista'] = json_decode('[{"type":"event","user_eyeos":"eyeos","calendar":"personal"}]');
        $event['credentials'] = json_decode('{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}');
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');

        $eventsU1db = array();
        array_push($eventsU1db,$this->getEventsU1db('eyeos','personal',"DELETED",0,1395730800,1395738000,'None','n',1,0,"Examen","Barcelona","Examen de matemáticas"));
        array_push($eventsU1db,$this->getEventsU1db('eyeos','personal',"NEW",0,1395820800,1395828000,'None','n',1,0,"Médico","Girona","Radiografia"));
        array_push($eventsU1db,$this->getEventsU1db('eyeos','personal',"CHANGED",0,1394820800,1394820800,'None','n',1,0,"Salida","Barcelona","Parc Güell"));
        array_push($eventsU1db,$this->getEventsU1db('eyeos','personal',"NEW",0,1394720800,1394720800,'None','n',1,0,"Clase","Tarragona","Matemáticas"));

        $eventsCalendar = $this->getEvents();

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getCalendarById')
            ->with($calendarId)
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getAllEventsByPeriod')
            ->with($calendar,null,null)
            ->will($this->returnValue($eventsCalendar));

        $this->u1dbCredsManagerMock->expects($this->exactly(2))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($event))
            ->will($this->returnValue(json_encode($eventsU1db)));


        $this->calendarManagerMock->expects($this->at(2))
            ->method('deleteEvent')
            ->with($eventsCalendar[0]);

        $this->calendarManagerMock->expects($this->at(3))
            ->method('saveEvent')
            ->with(new CalendarEvent('eyeID_CalendarEvent_67','Salida','Barcelona','Parc Güell',false,1394820800,1394820800,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));

        $this->calendarManagerMock->expects($this->at(4))
            ->method('getNewEvent')
            ->will($this->returnValue(new CalendarEvent()));

        $this->calendarManagerMock->expects($this->at(5))
            ->method('saveEvent')
            ->with(new CalendarEvent(null,'Clase','Tarragona','Matemáticas',false,1394720800,1394720800,'eyeID_EyeosUser_63',null,'eyeID_Calendar_64','private','None','n',1,0,null,null));

        $event['type'] = 'insertEvent';
        $event['lista'] = array($this->getEventsU1db('eyeos','personal',"NEW",0,1494820800,1494820800,'None','n',1,0,"Clase","Barcelona","Ingles"));

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($event))
            ->will($this->returnValue('true'));


        $this->sut->synchronizeCalendar($calendarId,$user);
    }

    /**
     *method: synchronizeCalendars
     * when: called
     * with: user
     * should: calledU1dbEmpty
     */
    public function test_synchronizeCalendars_called_user_calledU1dbEmpty()
    {
        $userMock = $this->getMock("EyeosUserCalendarTest");

        $event['type'] = 'selectCalendar';
        $event['lista'] = json_decode('[{"type":"calendar","user_eyeos":"eyeos"}]');
        $event['credentials'] = json_decode('{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}');
        $calendars = array();
        array_push($calendars,$this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63'));
        array_push($calendars,$this->getCalendar('eyeID_Calendar_65', 'school', 'school calendar.', 'eyeID_EyeosUser_63'));

        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $this->u1dbCredsManagerMock->expects($this->exactly(3))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($event))
            ->will($this->returnValue('[]'));


        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->synchronizeCalendars($userMock);

    }

    /**
     * method: synchronizeCalendars
     * when: called
     * with: user
     * should: calledU1db
     */
    public function test_synchronizeCalendars_called_user_calledU1db()
    {
        $userMock = $this->getMock("EyeosUserCalendarTest");

        $event['type'] = 'selectCalendar';
        $event['lista'] = json_decode('[{"type":"calendar","user_eyeos":"eyeos"}]');
        $event['credentials'] = json_decode('{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}');

        $calendarInsert = array();
        $calendarInsert['type'] = 'insertCalendar';
        $calendarInsert['lista'] = json_decode('[{"name":"people","type":"calendar","status":"NEW","user_eyeos":"eyeos","timezone":0,"description":"people calendar."}]');
        $calendarInsert['credentials'] = json_decode('{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}');

        $calendars = array();
        array_push($calendars,$this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63'));
        array_push($calendars,$this->getCalendar('eyeID_Calendar_65', 'school', 'school calendar.', 'eyeID_EyeosUser_63'));
        array_push($calendars,$this->getCalendar('eyeID_Calendar_66', 'people', 'people calendar.', 'eyeID_EyeosUser_63'));
        array_push($calendars,$this->getCalendar('eyeID_Calendar_66', 'family', 'family calendar.', 'eyeID_EyeosUser_63'));

        $calendarsU1db = array();
        array_push($calendarsU1db,$this->getCalendarU1db("eyeos","personal","NEW","personal calendar",0));
        array_push($calendarsU1db,$this->getCalendarU1db("eyeos","school","NEW","school calendar",0));
        array_push($calendarsU1db,$this->getCalendarU1db("eyeos","work","NEW","work calendar",0));
        array_push($calendarsU1db,$this->getCalendarU1db("eyeos","family","DELETED","family calendar",0));
        array_push($calendarsU1db,$this->getCalendarU1db("eyeos","class","DELETED","class calendar",0));
        $calendar = new Calendar();

        $userMock->expects($this->any())
            ->method("getName")
            ->will($this->returnValue('eyeos'));

        $userMock->expects($this->any())
            ->method("getId")
            ->will($this->returnValue('eyeID_EyeosUser_63'));

        $this->u1dbCredsManagerMock->expects($this->exactly(2))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with(json_encode($event))
            ->will($this->returnValue(json_encode($calendarsU1db)));

        $this->calendarManagerMock->expects($this->at(0))
            ->method('getAllCalendarsFromOwner')
            ->with($userMock)
            ->will($this->returnValue($calendars));

        $this->calendarManagerMock->expects($this->at(1))
            ->method('getNewCalendar')
            ->will($this->returnValue($calendar));

        $this->calendarManagerMock->expects($this->at(2))
            ->method('saveCalendar');

        $this->calendarManagerMock->expects($this->at(3))
            ->method('deleteCalendar');

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->with(json_encode($calendarInsert))
            ->will($this->returnValue("true"));

        $this->sut->synchronizeCalendars($userMock);
    }


    /**
     * method: insertCalendar
     * when: called
     * with: userAndCalendar
     * should: calledU1db
     */
    public function test_insertCalendar_called_userAndCalendar_calledU1db()
    {
        $user = 'eyeos';
        $calendar = $this->getCalendar('eyeID_Calendar_64', 'personal', 'personal\'s personal calendar.', 'eyeID_EyeosUser_63');
        $calendarU1db = '{"type":"insertCalendar","lista":[{"type":"calendar","user_eyeos":"eyeos","name":"personal","description":"personal\'s personal calendar.","timezone":0,"status":"NEW"}],"credentials":{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}}';

        $this->u1dbCredsManagerMock->expects($this->exactly(1))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with($calendarU1db)
            ->will($this->returnValue("true"));

        $this->sut->insertCalendar($user,$calendar);
    }

    /**
     * method: deleteCalendar
     * when: called
     * with: userAndCalendarName
     * should: calledU1db
     */
    public function test_deleteCalendar_called_userAndCalendarName_calledU1db()
    {
        $user = 'eyeos';
        $calendar = 'personal';
        $calendarU1db = '{"type":"deleteCalendar","lista":[{"type":"calendar","user_eyeos":"eyeos","name":"personal"}],"credentials":{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}}';

        $this->u1dbCredsManagerMock->expects($this->exactly(1))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with($calendarU1db)
            ->will($this->returnValue("true"));

        $this->sut->deleteCalendar($user,$calendar);
    }

    /**
     *method: deleteCalendarAndEventsByUser
     * when: called
     * with: user
     * should: calledU1db
     */
    public function test_deleteCalendarAndEventsByUser_called_user_calledU1db()
    {
        $user = 'eyeos';
        $calendarU1db = '{"type":"deleteCalendarUser","lista":[{"user_eyeos":"eyeos"}],"credentials":{"oauth":{"token_key":"1234","token_secret":"ABCD","consumer_key":"keySebas","consumer_secret":"secretSebas"}}}';

        $this->u1dbCredsManagerMock->expects($this->exactly(1))
            ->method('callProcessCredentials')
            ->will($this->returnValue($this->credentials));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->with($calendarU1db)
            ->will($this->returnValue("true"));

        $this->sut->deleteCalendarAndEventsByUser($user);
    }

    private function getEvents()
    {
        $events = array();
        array_push($events,new CalendarEvent('eyeID_CalendarEvent_65','Examen','Barcelona','Examen de matemáticas',false,1395730800,1395738000,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        array_push($events,new CalendarEvent('eyeID_CalendarEvent_66','Médico','Girona','Radiografia',false,1395820800,1395828000,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        array_push($events,new CalendarEvent('eyeID_CalendarEvent_67','Salida','Lleida','Justificante',false,1394820800,1394820800,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
        array_push($events,new CalendarEvent('eyeID_CalendarEvent_67','Clase','Barcelona','Ingles',false,1494820800,1494820800,'eyeID_EyeosUser_63','other','eyeID_Calendar_64','private','None','n',1,0,null,null));
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

    private function getEventsU1db($user,$calendar,$status,$isallday,$timestart,$timeend,$repetition,$repeattype,$finaltype,$finalvalue,$subject,$location,$description)
    {
        $eventU1db = array();
        $eventU1db['type'] = 'event';
        $eventU1db['user_eyeos'] = $user;
        $eventU1db['calendar'] = $calendar;
        $eventU1db['status'] = $status;
        $eventU1db['isallday'] = $isallday;
        $eventU1db['timestart'] = $timestart;
        $eventU1db['timeend'] = $timeend;
        $eventU1db['repetition'] = $repetition;
        $eventU1db['finaltype'] = $finaltype;
        $eventU1db['finalvalue'] = $finalvalue;
        $eventU1db['subject'] = $subject;
        $eventU1db['location'] = $location;
        $eventU1db['repeattype'] = $repeattype;
        $eventU1db['description'] = $description;

        return $eventU1db;
    }

    private function getCalendarU1db($user,$name,$status,$description,$timezone)
    {
        $calendarU1db = array();
        $calendarU1db['type'] = 'calendar';
        $calendarU1db['user_eyeos'] = $user;
        $calendarU1db['name'] = $name;
        $calendarU1db['status'] = $status;
        $calendarU1db['description'] = $description;
        $calendarU1db['timezone'] = $timezone;

        return $calendarU1db;
    }
}

?>