<?php
namespace Sopinet\ChatBundle\Command;

use Sopinet\ChatBundle\Entity\Device;
use Sopinet\ChatBundle\Entity\MessageUserState;
use Sopinet\ChatBundle\Entity\UserState;
use Sopinet\ChatBundle\Service\MessageHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class checkUserStateCommand extends ContainerAwareCommand
{
    # php app/console sopinet:chatBundle:checkUserState 60
    protected function configure()
    {
        $this
            ->setName('sopinet:chatBundle:checkUserState')
            ->setDescription('Check user state about your online or offline')
            ->addArgument(
                'timeSec',
                InputArgument::REQUIRED,
                'Seconds about user is timeout'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timeSec = $input->getArgument('timeSec');
        $con = $this->getContainer();
        $em  = $con->get('doctrine')->getEntityManager();

        $userManager = $con->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        foreach($users as $user) {
            // Check if user is active by doPing
            if ($user->getUserState() != null) {
                // Check WebClient
                $hasWebClient = false;
                /** @var Device $device */
                foreach($user->getDevices() as $device) {
                    if ($device->getDeviceType() == Device::TYPE_WEB) {
                        $hasWebClient = true;
                    }
                }

                // Check TimeOut from Apps
                /** @var UserState $userState */
                $userState = $user->getUserState();
                if ($userState->getState() == UserState::STATE_CONNECTED && !$hasWebClient) {
                    $dateNow = new \DateTime();
                    $diff = $dateNow->getTimestamp() - $userState->getUpdatedAt()->getTimestamp();
                    // User timeout!
                    if ($diff > $timeSec) {
                        $userState->setState(UserState::STATE_DISCONNECTED);
                        $em->persist($userState);
                        $em->flush();

                        $message = new MessageUserState();
                        $message->setFromUser($user);
                        $message->setText(UserState::STATE_DISCONNECTED);
                        $message->setFromTime(new \DateTime());
                        /** @var MessageHelper $messageHelper */
                        $messageHelper = $con->get('sopinet_chatbundle_messagehelper');
                        $messageHelper->sendMessage($message);
                    }
                }
            }
        }
    }
}