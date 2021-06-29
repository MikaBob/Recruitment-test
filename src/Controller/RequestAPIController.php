<?php

namespace Blexr\Controller;

use Blexr\Model\Entity\Request;
use Blexr\Model\RequestDAO;

class RequestAPIController extends DefaultAPIController {

    /**
     * Return list of requests
     * @TODO sort, offset, limit
     */
    public function list() {
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "GET") {
            return $this->generateResponse(400, 'Bad Request');
        }

        $requestDAO = new RequestDAO();
        $result = $requestDAO->getAll();

        if ($result === false) {
            return $this->generateResponse(400, $result->errorInfo());
        }

        return $this->generateResponse(200, ['requests' => $result]);
    }

    public function get($params) {
        $id = $params[0] ?? null;

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "GET" || !is_numeric($id)) {
            return $this->generateResponse(400, 'Bad Request');
        }

        $requestDAO = new RequestDAO();
        $request = $requestDAO->getById(intval($id));

        if ($request === null) {
            return $this->generateResponse(400, 'Request not found');
        }

        return $this->generateResponse(200, ['request' => $request]);
    }

    public function post() {
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "POST") {
            return $this->generateResponse(400, 'Bad Request');
        }

        try {

            /**
             * @TODO make real validation with constraints
             */
            $userId = $_SERVER['loggedUser']->getId();
            $startDate = \DateTime::createFromFormat('U', filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_STRING));
            $endDate = \DateTime::createFromFormat('U', filter_input(INPUT_POST, 'endDate', FILTER_SANITIZE_STRING));

            $request = new Request();
            $request->setUserId($userId);
            $request->setStartDate($startDate);
            $request->setEndDate($endDate);
            $request->setStatus(Request::STATUS_PENDING);

            $requestDAO = new RequestDAO();
            $result = $requestDAO->insert($request);
            if ($result->errorCode() !== \PDO::ERR_NONE) {
                return $this->generateResponse(400, $result->errorInfo());
            }
        } catch (\PDOException $ex) {
            return $this->generateResponse(400, $ex->getMessage());
        } catch (\TypeError $error) {
            return $this->generateResponse(400, $error->getMessage());
        } catch (\Exception $ex) {
            return $this->generateResponse(400, $ex->getMessage());
        }

        return $this->generateResponse(200, []);
    }

}
