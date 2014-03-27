<?php
/*
*                 eyeos - The Open Source Cloud's Web Desktop
*                               Version 2.0
*                   Copyright (C) 2007 - 2010 eyeos Team 
* 
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU Affero General Public License version 3 as published by the
* Free Software Foundation.
* 
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
* details.
* 
* You should have received a copy of the GNU Affero General Public License
* version 3 along with this program in the file "LICENSE".  If not, see 
* <http://www.gnu.org/licenses/agpl-3.0.txt>.
* 
* See www.eyeos.org for more details. All requests should be sent to licensing@eyeos.org
* 
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required under
* Section 5 of the GNU Affero General Public License version 3.
* 
* In accordance with Section 7(b) of the GNU Affero General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* eyeos" logo and retain the original copyright notice. If the display of the 
* logo is not reasonably feasible for technical reasons, the Appropriate Legal Notices
* must display the words "Powered by eyeos" and retain the original copyright notice. 
*/

class CalendarEvent extends AbstractCalendarEvent {	
	public function __construct($id = NULL, $subject = NULL,$location = NULL,$description = NULL, $isAllDay = false,$timeStart = NULL, $timeEnd = NULL, $creatorId = NULL,
            $type = NULL,$calendarId = NULL, $privacy = NULL,$repetition = NULL, $repeatType = NULL,$finalType = NULL,$finalValue = NULL, $eventGroup = NULL, $gmtTimeDiffrence = NULL) {
		//set default values

        if($id) $this->setId($id);
        if($subject) $this->setSubject($subject);
        if($location) $this->setLocation($location);
        if($description) $this->setDescription($description);
		$this->setIsAllDay($isAllDay);
        if($timeStart !== NULL) $this->setTimeStart($timeStart);
        if($timeEnd != NULL) $this->setTimeEnd($timeEnd);
        if($creatorId) $this->setCreatorId($creatorId);
        if($type) {
            $this->setType($type);
        } else {
            $this->setType(self::TYPE_OTHER);
        }

        if($calendarId) $this->setCalendarId($calendarId);
        if($privacy) {
            $this->setPrivacy($privacy);
        } else {
            $this->setPrivacy(self::PRIVACY_PRIVATE);
        }

        if($repetition) {
            $this->setRepetition($repetition);
        } else {
            $this->setRepetition('');
        }
        if($repeatType) $this->setRepeatType($repeatType);
        if($finalType !== NULL) $this->setFinalType($finalType);
        if($finalValue !== NULL) $this->setFinalValue($finalValue);
        if($eventGroup) $this->setEventGroup($eventGroup);
        if($gmtTimeDiffrence) $this->setGmtTimeDiffrence($gmtTimeDiffrence);
	}
	
	public function addCollaborator(AbstractEyeosPrincipal $collaborator, SharePermission $permissions) {
		SharingManager::getInstance()->addCollaborator($this, $collaborator, $permissions);
	}
	
	public function getAllCollaborators() {
		$shareInfos = SharingManager::getInstance()->getAllShareInfo($this);
		$collaborators = array();
		foreach($shareInfos as $shareInfo) {
			$collaborators[$shareInfo->getCollaborator()->getId()] = $shareInfo->getCollaborator();
		}
		return $collaborators;
	}
	
	public function getAllShareInfo() {
		return SharingManager::getInstance()->getAllShareInfo($this);
	}
	
	public function getShareOwner() {
		$calendar = CalendarManager::getInstance()->getCalendarById($this->getCalendarId());
		return UMManager::getInstance()->getUserById($calendar->getOwnerId());
	}
	
	public function removeCollaborator(AbstractEyeosPrincipal $collaborator) {
		SharingManager::getInstance()->removeCollaborator($this, $collaborator);
	}
	
	public function updateCollaboratorPermission(AbstractEyeosPrincipal $collaborator, SharePermission $permission) {
		SharingManager::getInstance()->updateCollaboratorPermission($this, $collaborator, $permission);
	}
}
?>
