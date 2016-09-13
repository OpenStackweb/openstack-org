<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class DeletedDupeMemberFactory
 */
final class DeletedDupeMemberFactory
    implements IDeletedDupeMemberFactory {

    /**
     * @param ICommunityMember $member
     * @return IDeletedDupeMember
     */
    public function build(ICommunityMember $member)
    {
        $deleted = new DeletedDupeMember;
        $deleted->MemberID = $member->ID;
        $deleted->FirstName = $member->FirstName;
        $deleted->Surname = $member->Surname;
        $deleted->Email = $member->Email;
        $deleted->Password = $member->Password;
        $deleted->PasswordEncryption = $member->PasswordEncryption;
        $deleted->Salt = $member->Salt;
        $deleted->PasswordExpiry = $member->PasswordExpiry;
        $deleted->LockedOutUntil = $member->LockedOutUntil;
        $deleted->Locale = $member->Locale;
        $deleted->DateFormat = $member->DateFormat;
        $deleted->TimeFormat = $member->TimeFormat;
        $deleted->SecondEmail = $member->SecondEmail;
        $deleted->ThirdEmail = $member->ThirdEmail;
        $deleted->HasBeenEmailed = $member->HasBeenEmailed;
        $deleted->ShirtSize = $member->ShirtSize;
        $deleted->StatementOfInterest = $member->StatementOfInterest;
        $deleted->Bio = $member->Bio;
        $deleted->FoodPreference = $member->FoodPreference;
        $deleted->OtherFood = $member->OtherFood;
        $deleted->IRCHandle = $member->IRCHandle;
        $deleted->TwitterName = $member->TwitterName;
        $deleted->Projects = $member->Projects;
        $deleted->OtherProject = $member->OtherProject;
        $deleted->SubscribedToNewsletter = $member->SubscribedToNewsletter;
        $deleted->JobTitle = $member->JobTitle;
        $deleted->DisplayOnSite = $member->DisplayOnSite;
        $deleted->Role = $member->Role;
        $deleted->LinkedInProfile = $member->LinkedInProfile;
        $deleted->Address = $member->Address;
        $deleted->Suburb = $member->Suburb;
        $deleted->State = $member->State;
        $deleted->Postcode = $member->Postcode;
        $deleted->Country = $member->Country;
        $deleted->City = $member->City;
        $deleted->Gender = $member->Gender;
        $deleted->TypeOfDirector = $member->TypeOfDirector;
        return $deleted;
    }
}