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

        // An admin has access to every request. Users only theirs
        if (!AuthenticationController::isAdmin()) {
            $filteredResult = [];
            foreach ($result as $request) {
                if (intval($request->userId) === $_SERVER['loggedUser']->getId())
                    $filteredResult[] = $request;
            }
            $result = $filteredResult;
        }

        return $this->generateResponse(200, ['requests' => $result]);
    }

    // Get a specific Request (Not in use acutally)
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

    // Create a Request
    public function post() {
        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "POST") {
            return $this->generateResponse(400, 'Bad Request');
        }

        try {
            /**
             * @TODO make real validation with constraints
             */
            $userId = $_SERVER['loggedUser']->getId();
            $startDate = \DateTime::createFromFormat('D M d Y H:i:s e+', filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_STRING));
            $endDate = \DateTime::createFromFormat('D M d Y H:i:s e+', filter_input(INPUT_POST, 'endDate', FILTER_SANITIZE_STRING));

            if ($startDate->getTimestamp() > $endDate->getTimestamp()) {
                return $this->generateResponse(400, 'Invalid Dates. Start after End');
            }

            $now = new \Datetime();

            // deadline = startDate -1 days, 8pm
            $deadline = new \DateTime($startDate->format(\DateTime::ISO8601));
            $deadline->sub(new \DateInterval('P1D'));
            $deadline->setTime(20, 0, 0);

            // Same as front end
            // Check deadline. startDate must be at least 4 hours before the end of the previous day

            if ($now->getTimestamp() > $deadline->getTimestamp()) {
                return $this->generateResponse(400, 'Your starting date is too early. It can not be approuved so quick');
            }

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
        } catch (\TypeError $error) {
            return $this->generateResponse(400, $error->getMessage());
        } catch (\Exception $ex) {
            return $this->generateResponse(400, $ex->getMessage());
        }

        return $this->generateResponse(200, []);
    }

    // Update a request status
    public function put($params) {
        $id = $params[0] ?? null;

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "POST" || !is_numeric($id)) {
            return $this->generateResponse(400, 'Bad Request');
        }

        try {

            $requestDAO = new RequestDAO();
            $request = $requestDAO->getById(intval($id));

            if ($request === null) {
                return $this->generateResponse(400, 'Request not found');
            }

            $userId = $_SERVER['loggedUser']->getId();

            // Check if user can edit this request
            if (!AuthenticationController::isAdmin() && $userId !== $request->getUserId()) {
                return $this->generateResponse(403, 'Forbidden');
            }

            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            // Validate $status value.
            // A request is pending on creation. Do not update if status did not change
            if (!Request::isStatusValid($status) || $request->getStatus() !== Request::STATUS_PENDING) {
                return $this->generateResponse(400, 'Bad Request');
            }

            $request->setStatus($status);
            $result = $requestDAO->update($request);

            if ($result->errorCode() !== \PDO::ERR_NONE) {
                return $this->generateResponse(400, $result->errorInfo());
            }
        } catch (\TypeError $error) {
            return $this->generateResponse(400, $error->getMessage());
        } catch (\Exception $ex) {
            return $this->generateResponse(400, $ex->getMessage());
        }

        return $this->generateResponse(200, []);
    }

    // Delete / Cancel Request
    public function delete($params) {
        $id = $params[0] ?? null;

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING) !== "DELETE" || !is_numeric($id)) {
            return $this->generateResponse(400, 'Bad Request');
        }

        try {

            $requestDAO = new RequestDAO();
            $request = $requestDAO->getById(intval($id));
            if ($request === null) {
                return $this->generateResponse(400, 'Request not found');
            }

            $userId = $_SERVER['loggedUser']->getId();

            // Check if user can edit this request. Admin can not delete request (only accept or reject)
            if (!AuthenticationController::isAdmin() && $userId !== $request->getUserId()) {
                return $this->generateResponse(403, 'Forbidden');
            }

            $result = $requestDAO->delete($request);
            if ($result->errorCode() !== \PDO::ERR_NONE) {
                return $this->generateResponse(400, $result->errorInfo());
            }
        } catch (\Exception $ex) {
            return $this->generateResponse(400, $ex->getMessage());
        }

        return $this->generateResponse(200, []);
    }

}
