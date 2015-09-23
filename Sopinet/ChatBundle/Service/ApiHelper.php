<?php

namespace Sopinet\ChatBundle\Service;
use FOS\RestBundle\View\ViewHandler;
use Sopinet\ChatBundle\Entity\Chat;
use Symfony\Component\Form\Form;
use  Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: sopinet
 * Date: 11/11/14
 * Time: 8:41 AM
 */

class ApiHelper {


    // TODO: ESTA CLASE HABRÍA QUE ELIMINARLA, ¿¿¿¿¿¿ NO ??????



    const OK = "ok";
    const DENIED= "Data not valid";
    const USERNOTVALID = "User starter not valid";
    const USERNOTINCHAT= "User is not member of the chat";
    const USERSTARTERNOTVALID = "User starter not valid";
    const USERNOTADMIN = "User not admin";
    const CHATTYPEINCORRECT = "The type of chat must be event";
    const NOTMONEY = "User dont have enough money";
    const GENERALERROR = "General error";
    const NODEVICE= "User don't have device registered";

    const STATE_OK = 1;
    const STATE_ERROR = -1;

    const ERROR_UNKNOWN = "Unknown";

    public function __construct(EntityManager $entityManager, ViewHandler $viewHandler) {
        $this->em = $entityManager;
        $this->viewhandler=$viewHandler;
    }

    /**
     * responseOk
     *
     * Crea y devuelve una respuesta de aceptación de la API.
     * Recibe en $data el conjunto de datos que devolverá la API.
     * Opcionalmente, se pueden especificar grupos para el contexto de la serialización.
     * Si no se especificam, el estado HTTP y el mensaje de error tendrán valores por defecto.
     *
     * @param $data
     * @param string $groups
     * @param string $message
     * @param int $httpStatusCode
     * @return Response
     */
    public function responseOk ($data, $groups = "", $message = "", $httpStatusCode = Response::HTTP_ACCEPTED)
    {
        $response['state'] = $this::STATE_OK;
        $response['msg'] = strlen($message) ? $message : $this::OK;
        $response['data'] = $data;

        $view = View::create()
            ->setStatusCode($httpStatusCode)
            ->setData($response);

        if ((is_string($groups) and strlen($groups)) or is_array($groups)) {
            $groupsArray = (array) $groups;
            $view->setSerializationContext(SerializationContext::create()->setGroups($groupsArray));
        }

        return $this->viewhandler->handle($view);
    }

    /**
     * responseDenied
     *
     * Crea y devuelve una respuesta de denegación de la API.
     * Si no se proporcionan parámetros, el estado HTTP y el mensaje de error tendrán valores por defecto.
     *
     * @param string $message
     * @param int $httpStatusCode
     * @return Response
     */
    public function responseDenied ($message = "", $httpStatusCode = Response::HTTP_NOT_FOUND)
    {
        $response['state'] = $this::STATE_ERROR;
        $response['msg'] = $message;
        // $response['data'] = array();

        $view = View::create()
            ->setStatusCode($httpStatusCode)
            ->setData($response);

        return $this->viewhandler->handle($view);
    }

    /**
     * Se hace un submit de los campos de un request que esten definidos en un formulario
     * @param Request $request
     * @param Form $form
     * @return Form
     */
    public function handleForm(Request $request,Form $form){
        // The JSON PUT data will include all attributes in the entity, even
        // those that are not updateable by the user and are not in the form.
        // We need to remove these extra fields or we will get a
        // "This form should not contain extra fields" Form Error
        $data = $request->request->all();
        $children = $form->all();
        //Eliminamos los datos del request que no pertenecen al formulario
        $data = array_intersect_key($data, $children);
        $form->submit($data);
        return $form;
    }

    /**
     * Dado un formulario se devuelven sus errores parseados
     * @param Form $form
     * @return array
     */
    public function getFormErrors(Form $form){
        // Se parsean los errores que existan en el formulario para devolverlos en el reponse
        $errors=array();
        //Se parsean los posibles errores generales del formulario(incluyendo los asserts a nivel de entidad)
        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['form'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }
        $childs=$form->getIterator();
        //Se parsean los posibles errores de cada campo del formulario
        /** @var Form $child */
        foreach($childs as $child ){
            $fieldErrors=$child->getErrors();
            while($fieldErrors->current()!=null){
                $errors[$child->getName()][]=$fieldErrors->current()->getMessage();
                $fieldErrors->next();
            }
        }
        return $errors;
    }

    /**
     * Maneja excepciones para devolverlas mediante la API
     * @param \Exception $e
     * @return mixed
     */
    public function handleException(\Exception $e)
    {
        return $this->msgDenied($e->getMessage());
    }
} 