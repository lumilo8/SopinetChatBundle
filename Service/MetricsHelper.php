<?php

namespace Sopinet\ChatBundle\Service;

use AppBundle\Services\GGStatsHelper;
use Sopinet\ChatBundle\Entity\Message;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class MetricsHelper
 * @package Sopinet\ChatBundle\Service
 */
class MetricsHelper
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Devuelve un array de objetos que contienen Fecha y Intervalo (en el formato especificado) del tiempo de actividad
     * que tiene un Chat.
     *
     * Agrupa los mensajes de Chat que se le pasan en Chat, descartando aquellos que no son de Chat.
     * TODO: Podríamos añadir un filtro por tipo a los mensajes
     *
     * Separa en conversaciones los grupos de mensajes que llevan más de X horas sin actividad.
     *
     * Devuelve el intervalo formateado de diferencia entre el primer mensaje de Chat y el último.
     *
     * @param $messages
     * @param int $hoursDisconnect
     * @param string $formatInterval
     * @return array (Array de datos, cada dato un objeto con atributo date para la fecha y diffFormatted para el intervalo formateado)
     */
    public function getChatTimeActivity($messages, $hoursDisconnect = 24, $formatInterval = "%h") {
        // Group by Chat
        $messagesGroupedByChat = array();
        /** @var Message $message */
        foreach($messages as $message) {
            // If Chat is not null
            if ($message->getChat() != null) {
                $chatID = $message->getChat()->getId();
                if (!isset($messagesGroupedByChat[$chatID])) {
                    $messagesGroupedByChat[$chatID] = array();
                }
                $messagesGroupedByChat[$chatID][] = $message;
            }
        }

        // Order by createdAt DESC
        // TODO: DO!

        // Split disconnect hours
        $chatConversation = array();
        $contSplit = -1;
        $timeHours = $hoursDisconnect * 60 * 60;
        foreach($messagesGroupedByChat as $chatID => $chatMessages) {
            $prev = 0;
            foreach($chatMessages as $message) {
                $next = $message->getCreatedAt()->getTimestamp();
                // New Split
                if (($next - $timeHours) > $prev) {
                    $contSplit++;
                    $chatConversation[$contSplit] = array();
                }
                $chatConversation[$contSplit][] = $message;
                $prev = $next;
            }
        }

        $mediaChats = array();
        $i = 0;
        foreach($chatConversation as $splitMessages) {
            $sizeSplit = count($splitMessages);
            if ($sizeSplit > 1) {
                /** @var \DateTime $iniTime */
                $iniTime = $splitMessages[0]->getCreatedAt();
                $endTime = $splitMessages[$sizeSplit - 1]->getCreatedAt();
                $mediaChats[$i] = new \stdClass();
                $mediaChats[$i]->date = $splitMessages[0]->getCreatedAt();
                $mediaChats[$i]->diffFormatted = $iniTime->diff($endTime)->format($formatInterval);

                $i++;
            }
        }

        return $mediaChats;
    }

    public function getNumberMessages($messages, $modeDate = GGStatsHelper::MODE_DATETIME_MONTH) {
        /** @var GGStatsHelper $ggHelper */
        $ggHelper = $this->container->get('youchat_ggstats');

        $formatCode = $ggHelper->getFormatDate($modeDate, GGStatsHelper::FORMAT_DATETIME_CODE);

        // Group by Chat and Date
        $messagesGroupedByChat = array();
        /** @var Message $message */
        foreach($messages as $message) {
            // If Chat is not null
            if ($message->getChat() != null) {
                $chatID = $message->getChat()->getId();
                $dateCode = $message->getCreatedAt()->format($formatCode);
                if (!isset($messagesGroupedByChat[$dateCode])) {
                    $messagesGroupedByChat[$dateCode] = array();
                }
                if (!isset($messagesGroupedByChat[$dateCode][$chatID])) {
                    $messagesGroupedByChat[$dateCode][$chatID] = new \stdClass();
                    $messagesGroupedByChat[$dateCode][$chatID]->date = $message->getCreatedAt();
                    $messagesGroupedByChat[$dateCode][$chatID]->numberMessages = 0;
                }

                $messagesGroupedByChat[$dateCode][$chatID]->numberMessages++;
            }
        }

        // Good Format
        $numberMessages = array();
        $i = 0;
        foreach($messagesGroupedByChat as $dateCode => $chats) {
            foreach($chats as $chatID => $chatGroupData) {
                $numberMessages[$i] = new \stdClass();
                $numberMessages[$i]->date = $chatGroupData->date;
                $numberMessages[$i]->numberMessages = $chatGroupData->numberMessages;
                $i++;
            }
        }

        return $numberMessages;
    }
}