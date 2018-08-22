<?php

namespace AppBundle\Service;

class MessageGenerator
{
    public function getHappyMessage()
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_InsertionDB_OK(){
        $messages = [
            'Sucessfull insertion in database',
            'Correct insertion in database',
            'Insertion query OK',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_UpdateDB_OK(){
        $messages = [
            'Sucessfull update in database',
            'Correct update in database',
            'Update query OK',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_DeleteDB_OK(){
        $messages = [
            'Sucessfull delete in database',
            'Correct delete in database',
            'Delete query OK',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_DeleteDB_NONE(){
        $messages = [
            'Nothing to delete.',
            'No data to delete in the database',
            'Zero data for deleting',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_ActionAdmin_FAIL(){
        $messages = [
            'Admin action denied for this action',
            'You have not the right to this admin action',
            'You are not allowed to this admin action',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_Action_FAIL(){
        $messages = [
            'Action denied for this action',
            'You have not the right to this run action',
            'You are not allowed to this action',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function Msg_DeleteDB_Profile_OK(){
        $messages = [
            'Your profile is completly deleted in this application.',
            'Profile deleting sucessfully.',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }

    public function getTokenStorage(){
		return $this->tokenStorage;
	}

	public function setTokenStorage($tokenStorage){
		$this->tokenStorage = $tokenStorage;
	}

	public function getAuthorizationChecker(){
		return $this->authorizationChecker;
	}

	public function setAuthorizationChecker($authorizationChecker){
		$this->authorizationChecker = $authorizationChecker;
	}
}