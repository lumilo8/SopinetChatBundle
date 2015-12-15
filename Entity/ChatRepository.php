<?php
namespace Sopinet\ChatBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sopinet\ChatBundle\Service\ApiHelper;
use \Application\Sonata\UserBundle\Entity\User as User;

class ChatRepository extends EntityRepository
{
    /**
     * Función que comprueba si existe un chat y lo devuelve
     * para una serie de Usuarios pasados por parámetro
     * Si no existe devuelve null
     *
     * @param User[] $users
     *
     * @return Chat
     */
    public function getChatExist($users)
    {
        $em = $this->getEntityManager();
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        $chats=$repositoryChat->findAll();
        /** @var Chat $chat */
        foreach ($chats as $chat) {
            if ($this->usersInChat($users, $chat)) {
                return $chat;
            }
        }

        return null;
    }

    /**
     * Comprueba si un conjunto de Usuarios pertenecen a un Chat
     * Devuelve true si todos están en Chat
     * false en caso contrario
     *
     * @param $users
     * @param Chat $chat
     * @return bool
     */
    private function usersInChat($users, Chat $chat) {
        if (count($users) == 0) return false;
        foreach($users as $user) {
            if (!$this->userInChat($user, $chat)) return false;
        }
        return true;
    }

    /**
     * Comprueba si un usuario esta dentro de un chat
     * @param User $user
     * @param Chat $chat
     * @return bool
     */
    public function userInChat(User $user,Chat $chat)
    {
        return in_array($user, $chat->getChatMembers()->toArray());
    }






















    /** TODO LO SIGUIENTE PARECE DEPRECATED **/










    /**
     *
     *      DEPRECATED!!!
     *
     *
     * @param Integer $id
     *
     * @return bool
     */
    private function isNewDEPRECATED($id)
    {
        $chat = $this->findOneById($id);

        if ($chat != null) {
            if ($chat->getId() === $id) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     *
     *      DEPRECATED!!!
     *
     * Función que modifica el Administrador de un Chat por otro
     *
     * @param Chat $chat
     * @param User $newAdminUser
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function modifyAdminDEPRECATED(Chat $chat, User $newAdminUser)
    {
        $em = $this->getEntityManager();

        // Buscamos el antiguo administrador
        $oldAdminMember = $chat->getAdmin();
        // Si no encontramos administrador, devolvemos error
        if ($oldAdminMember == null) {
            throw new \Exception(ApiHelper::GENERALERROR);
        }
        // Si son el mismo, el antiguo administrador y el nuevo, devolvemos un true y no hacemos nada más
        if ($newAdminUser->getId() == $oldAdminMember->getId()) {
            return true;
        }

        // Se comprueba si el nuevo administrador es miembro del grupo
        if ($this->userInChat($newAdminUser, $chat)) {
            $chat->setAdmin($newAdminUser);
            $em->persist($chat);
            $em->flush();

            return true;
        } else {
            throw new \Exception(ApiHelper::USERNOTINCHAT);
        }
    }

    /**
     *
     *      DEPRECATED!!!
     *
     *
     * Elimina a un usuario de un chat
     * TODO: Qué hacer si se intenta eliminar un usuario de un chat donde sólo hay dos usuarios
     *
     * Devuelve true si se elimina con éxito
     * Devuelve false si no existe o el chat no es de tipo Event
     *
     * @param Chat $chat
     * @param Integer $userId - ID del usuario a eliminar del Chat
     *
     * @throws \Exception
     *
     * @return bool|Chat
     */
    public function removeMemberDEPRECATED(Chat $chat, $userId)
    {
        $em = $this->getEntityManager();

        /** @var UserExtendRepository $repositoryUser */
        $repositoryUser = $em->getRepository('ApplicationSonataUserBundle:User');

        /** @var UserExtend $user */
        $user = $repositoryUser->findOneById($userId);
        if ($user == null) {
            throw new \Exception(ApiHelper::USERNOTVALID);
        }
        if ($this->userInChat($user, $chat)) {
            $chat->removeChatMember($user);
            $user->removeChat($chat);
            $em->persist($chat);
            $em->persist($user);
            $em->flush();

            return $chat;
        }

        throw new \Exception(ApiHelper::USERNOTINCHAT);
    }

    /**
     *
     *
     *
     *      DEPRECATED!!!
     *
     *
     *
     * Añade un miembro a un chat
     *
     * @param Chat $chat - Entidad chat
     * @param Integer $userId - ID del usuario a introducir en el Chat
     *
     * @throws \Exception
     *
     * @return Chat|bool
     */
    private function addMemberDEPRECATED(Chat $chat, $userId)
    {
        $em = $this->getEntityManager();

        /** @var UserExtendRepository $repositoryUser */
        $repositoryUser = $em->getRepository('ApplicationSonataUserBundle:User');
        /** @var UserExtend $userToAdd */
        $userToAdd = $repositoryUser->findOneById($userId);

        // Comprobar que existe el usuario
        if (!$userToAdd) {
            throw new \Exception(ApiHelper::USERNOTVALID);
        }

        // Comprobar que no está ya en el chat
        if ($this->userInChat($userToAdd, $chat)) {
            throw new \Exception(ApiHelper::USERNOTINCHAT);
        }

        $chat->addChatMember($userToAdd);
        $userToAdd->addChat($chat);
        $em->persist($chat);
        $em->persist($userToAdd);
        $em->flush();

        return $chat;
    }

    /**
     *
     *
     *      DEPRECATED!!!
     *
     *
     * Marca un chat como borrado
     *
     * @param Chat $chat
     *
     * @return bool
     */
    public function deleteChatDEPRECATED(Chat $chat)
    {
        $em = $this->getEntityManager();
        $em->remove($chat);
        $em->flush();

        return true;
    }

    /**
     *
     *      DEPRECATED!!!
     *
     * Devuelve todos los usuarios de un chat menos el pasado por parámetro
     *
     * @param User $user
     * @param Chat $chat
     */
    private function getAnotherUsersDEPRECATED(User $user, Chat $chat) {
        $another = array();
        foreach($chat->getChatMembers() as $member) {
            if ($member->getUser()->getId() != $user->getId()) {
                $another[] = $member;
            }
        }
        return $another;
    }
}