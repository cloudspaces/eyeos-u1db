<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/05/14
 * Time: 16:05
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
}


?>