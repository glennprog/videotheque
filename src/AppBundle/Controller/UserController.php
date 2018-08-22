<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/profile/delete", name="profile_delete")
     */
    public function deleteProfileAction(Request $request)
    {
        $this->securityGuardianAccess();
        $userManager = $this->get('fos_user.user_manager');
        $userManager->deleteUser($this->getUser());
        $msgGen = $this->get('message_generator')->Msg_DeleteDB_Profile_OK();
        $request->getSession()->getFlashBag()->add("success", $msgGen);
        return $this->redirectToRoute('fos_user_security_login');
    }

    public function securityGuardianAccess($role = 'ROLE_USER'){
        $this->denyAccessUnlessGranted($role, null, 'Unable to access this page!');
    }
}